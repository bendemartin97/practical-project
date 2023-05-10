package main

import (
    "encoding/json"
    "github.com/tejasmanohar/timerange-go"
    "gorm.io/driver/mysql"
    "gorm.io/gorm"
    "sync"
    "time"
)

const DbUsername = "sail"
const DbPassword = "password"
const DbName = "laravel"
const DbHost = "mysql"
const DbPort = "3306"

func createDatabaseConnection() *gorm.DB {
    dsn := DbUsername + ":" + DbPassword + "@tcp" + "(" + DbHost + ":" + DbPort + ")/" + DbName + "?" + "parseTime=true&loc=Local"
    var db *gorm.DB
    for {
        var err error
        db, err = gorm.Open(mysql.Open(dsn), &gorm.Config{})
        if err == nil {
            break
        }

    }

    return db
}

type ReturnValues struct {
    ExecutionTime               string
    UnavailableTotalDaysCounter int
    ResourceRangeCounter        int
    PeriodRangeCounter          int
    TotalRangeCounter           int
}

func Calculate() []byte {
    // from shared/availability.go, it's going to be copied by the dockerfile
    returnValuesChannel := make(chan *ReturnValues)
    var returnValues *ReturnValues
    go calculateAvailability(returnValuesChannel)

    for {
        select {
        case returnValues = <-returnValuesChannel:
            returnValuesChannel = nil
        }
        if returnValues != nil {
            break
        }
    }
    b, _ := json.Marshal(returnValues)
    return b
}

func calculateAvailability(returnValuesChannel chan *ReturnValues) {
    values := ReturnValues{}

    // start of execution time
    start := time.Now()

    // get unavailable days by resource and debug stuff
    availabilityMap, resourceRangeCounter, periodRangeCounter := getUnavailableDaysByResource()
    // calculate execution time
    elapsed := time.Since(start)

    // set return values
    values.ExecutionTime = elapsed.String()
    //values.UnavailableDaysByResource = availabilityMap
    values.ResourceRangeCounter = resourceRangeCounter
    values.PeriodRangeCounter = periodRangeCounter

    // calculate total unavailable days
    counter := 0
    for _, v := range availabilityMap {
        counter += len(v)
    }
    values.UnavailableTotalDaysCounter = counter
    values.TotalRangeCounter = resourceRangeCounter * periodRangeCounter

    // send return values
    returnValuesChannel <- &values
}

type AvailabilityStruct struct {
    mutex                sync.Mutex
    availabilityMap      map[uint32][]string
    resourceRangeCounter int
    periodRangeCounter   int
}

func newAvailabilityStruct() AvailabilityStruct {
    return AvailabilityStruct{
        mutex:                sync.Mutex{},
        availabilityMap:      make(map[uint32][]string),
        resourceRangeCounter: 0,
        periodRangeCounter:   0,
    }
}

func getUnavailableDaysByResource() (map[uint32][]string, int, int) {
    // db stuff
    db := createDatabaseConnection()

    // get resources with their bookings
    var resources []Resource
    db.Preload("Bookings").Find(&resources)

    // set start and end time
    start := time.Now()
    year, month, day := start.Date()
    start = time.Date(year, month, day, 0, 0, 0, 0, start.Location())

    end := time.Now().AddDate(0, 0, 365)
    year, month, day = end.Date()
    end = time.Date(year, month, day, 23, 59, 59, 0, end.Location())

    // create struct to store unavailable days by resource id and to have a global mutex
    availabilityStruct := newAvailabilityStruct()

    var waitGroup sync.WaitGroup
    waitGroup.Add(len(resources))
    for _, resource := range resources {
        go func(resource Resource, waitGroup *sync.WaitGroup, availabilityStruct *AvailabilityStruct) {
            // added defer to ensure mutex is unlocked before goroutine is finished
            // multiple defers are executed in LIFO order
            defer waitGroup.Done()
            defer availabilityStruct.mutex.Unlock()

            // create time period
            period := timerange.New(start, end, time.Hour*24)

            // check availability
            unavailableDays, counter := resource.checkAvailability(period)

            // add unavailable days to struct by resource id
            availabilityMap := make(map[uint32][]string)
            for _, unavailableDay := range unavailableDays {
                availabilityMap[resource.ID] = append(availabilityMap[resource.ID], unavailableDay)
            }

            // concurrency handling
            availabilityStruct.mutex.Lock()
            availabilityStruct.availabilityMap[resource.ID] = availabilityMap[resource.ID]
            availabilityStruct.resourceRangeCounter++
            availabilityStruct.periodRangeCounter += counter
        }(resource, &waitGroup, &availabilityStruct)
    }
    waitGroup.Wait()

    return availabilityStruct.availabilityMap, availabilityStruct.resourceRangeCounter, availabilityStruct.periodRangeCounter
}

func (resource *Resource) checkAvailability(period *timerange.Iter) ([]string, int) {
    unavailableDays := make([]string, 0)
    counter := 0
    for period.Next() {
        counter++
        startDate := time.Date(period.Current().Year(), period.Current().Month(), period.Current().Day(), 0, 0, 0, 0, period.Current().Location())
        endDate := time.Date(startDate.Year(), startDate.Month(), startDate.Day(), 23, 59, 59, 0, startDate.Location())
        var count uint32 = 0
        for _, booking := range resource.Bookings {
            if startDate.Unix() <= booking.StartTime.Unix() && endDate.Unix() >= booking.EndTime.Unix() {
                count += booking.Quantity
            }
        }
        if count >= resource.Quantity {
            unavailableDays = append(unavailableDays, startDate.Format("2006-01-02"))
        }
    }

    return unavailableDays, counter
}
