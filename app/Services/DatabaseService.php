<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Resource;
use App\Models\Result;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class DatabaseService
{
    const DURATION = 8 * 60;

    /**
     * @return array
     */
    public function seedBookings(): array
    {
        $resources = Resource::query()->get();

        $startTime = Carbon::now()->setTime(9, 0);

        try {
            for ($i = 0; $i < $resources->count(); $i++) {
                $bookings = collect();
                /** @var Resource $resource */
                $resource = $resources->random(1)->first();
                for ($j = 0; $j < 10; $j++) {
                    if ($resource->quantity === 0) {
                        break;
                    }

                    $periodStartTime = $startTime->clone()->addDays(rand(0, 365));

                    $periodEndTime = $periodStartTime->clone()
                        ->addMinutes(self::DURATION);

                    $quantity = rand(1, $resource->quantity);
                    $booking = [
                        'resource_id' => $resource->id,
                        'start_time' => $periodStartTime->format('Y-m-d H:i:s'),
                        'end_time' => $periodEndTime->format('Y-m-d H:i:s'),
                        'quantity' => $quantity
                    ];
                    $bookings->push($booking);
                    $resource->quantity -= $quantity;

                    if (!Booking::query()->insert($bookings->toArray())) {
                        throw new \Exception("Error inserting bookings");
                    };
                }

            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        } finally {
            $counter = Booking::query()->count();
            return ["bookingsCounter" => $counter];
        }
    }

    /**
     * @return array
     */
    public function seedResources(): array
    {
        $resources = collect();

        for ($i = 0; $i < 10; $i++) {
            $resources->push([
                'quantity' => rand(1, 10),
            ]);
        }

        Resource::query()->insert($resources->toArray());

        $count = Resource::query()->count();

        return ["resourceCounter" => $count];
    }

    /**
     * @return void
     */
    public function flushDatabase(): void
    {
        Resource::query()->get()->each(function (Resource $resource) {
            $resource->bookings()->delete();
            $resource->delete();
        });

        Result::query()->delete();
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getChartData(): \Illuminate\Support\Collection
    {
        $results =  Result::query()->get();

        $collection = collect();
        $results->each(function (Result $result) use ($collection) {
            $current = $collection->get($result->type, []);
            $new = [...$current, [
                'type' => $result->type,
                'execution_time' => $result->execution_time,
                'resource_counter' => $result->resource_counter,
                'bookings_counter' => $result->booking_counter,
            ]];
            $collection->put($result->type, $new);
        });

        return $collection;
    }
}
