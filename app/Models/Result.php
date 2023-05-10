<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $type
 * @property float $execution_time
 * @property string $unit
 * @property int $unavailable_total_dates_counter
 * @property int $resource_range_counter
 * @property int $period_range_counter
 * @property int $total_range_counter
 * @property int $resource_counter
 * @property int $booking_counter
 *
 */
class Result extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'type',
        'execution_time',
        'unit',
        'unavailable_total_dates_counter',
        'resource_range_counter',
        'period_range_counter',
        'total_range_counter',
    ];

    public static function fromJsonString(string $jsonString, string $type): Result
    {
        $resultRaw = json_decode($jsonString, true);
        $unit = array_values(array_filter(preg_split("/[1-9 .]/", $resultRaw['ExecutionTime'])))[0];
        $result = new self();

        $result->type = $type;
        $result->execution_time = (float) $resultRaw['ExecutionTime'];
        $result->unit = $unit;
        $result->unavailable_total_dates_counter = $resultRaw['UnavailableTotalDaysCounter'];
        $result->resource_range_counter = $resultRaw['ResourceRangeCounter'];
        $result->period_range_counter = $resultRaw['PeriodRangeCounter'];
        $result->total_range_counter = $resultRaw['TotalRangeCounter'];

        $resourceCounter = Resource::query()->count();
        $bookingCounter = Booking::query()->count();

        $result->resource_counter = $resourceCounter;
        $result->booking_counter = $bookingCounter;

        $result->save();
        return $result;
    }

    public function toArray(): array
    {
        return [
            'executionTime' => number_format($this->execution_time, 2, '.', '') . ' ' . $this->unit,
            'unavailableTotalDaysCounter' => $this->unavailable_total_dates_counter,
            'resourceRangeCounter' => $this->resource_range_counter,
            'periodRangeCounter' => $this->period_range_counter,
            'totalRangeCounter' => $this->total_range_counter,
        ];
    }
}
