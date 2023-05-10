<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Resource;
use App\Models\Result;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Log;

class PHPService implements ConnectionInterface
{
    const TYPE = "php";

    /**
     * @return Result
     */
    public function calculate(): Result
    {
        set_time_limit(0);
        $resources = Resource::query()->with('bookings')->get();

        $startDate = Carbon::now();
        $endDate = Carbon::now()->addDays(365);

        $period = CarbonPeriod::create($startDate, $endDate);
        $unavailableDates = collect();

        $resourceRangeCounter = 0;
        $periodRangeCounter = 0;
        $unavailableDatesCounter = 0;

        $time_start = microtime(true);

        $resources->each(function (Resource $resource) use ($period, $unavailableDates, &$resourceRangeCounter, &$unavailableDatesCounter, &$periodRangeCounter) {
            $resourceRangeCounter++;
            $period->forEach(function (Carbon $date) use ($resource, $unavailableDates, &$resourceRangeCounter, &$periodRangeCounter, &$unavailableDatesCounter) {
                $periodRangeCounter++;
                // we save only the date, where the resource is not available,
                // so we can easily check if the resource is available on a given date
                $startDate = $date->clone()->startOfDay();
                $endDate = $date->clone()->endOfDay();

                $bookingsQuantity = 0;
                $resource->bookings->each(function (Booking $booking) use ($startDate, $endDate, &$bookingsQuantity) {
                    if ($booking->start_time->gte($startDate) && $booking->end_time->lte($endDate)) {
                        $bookingsQuantity += $booking->quantity;
                    }
                });

                if ($bookingsQuantity >= $resource->quantity) {
                    $unavailableDatesCounter++;
                    $unavailableDates->put($resource->id, $date->format('Y-m-d'));
                }
            });
        });
        $time_end = microtime(true);
        $execution_time = floor(($time_end - $time_start) * 1000);

        $result = new Result();
        $result->execution_time = $execution_time;
        $result->unit = 'ms';
        $result->unavailable_total_dates_counter = $unavailableDatesCounter;
        $result->resource_range_counter = $resourceRangeCounter;
        $result->period_range_counter = $periodRangeCounter;
        $result->total_range_counter = $resourceRangeCounter * $periodRangeCounter;
        $result->type = self::TYPE;
        $result->save();
        return $result;
    }
}
