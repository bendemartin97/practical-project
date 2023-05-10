<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property Carbon $start_time
 * @property Carbon $end_time
 * @property int $quantity
 * @property-read $resource
 */
class Booking extends Model
{
    use HasFactory;

    public function resource()
    {
        return $this->belongsTo(Resource::class);
    }

    public function getStartTimeAttribute($date)
    {
        return Carbon::parse($date);
    }

    public function getEndTimeAttribute($date)
    {
        return Carbon::parse($date);
    }
}
