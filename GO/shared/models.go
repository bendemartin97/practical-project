package main

import (
	"time"
)

type Resource struct {
	ID        uint32
	StartTime string
	EndTime   string
	Quantity  uint32
	Bookings  []Booking `gorm:"foreignKey:ResourceId"`
}

type Booking struct {
	ID         uint32
	ResourceId uint32
	StartTime  time.Time
	EndTime    time.Time
	Quantity   uint32
	Resource   Resource `gorm:"foreignKey:ResourceId"`
}
