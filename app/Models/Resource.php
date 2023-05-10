<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property string $start_time
 * @property string $end_time
 * @property int $quantity
 * @property-read Booking[] | Collection $bookings
 */
class Resource extends Model
{
    use HasFactory;

    protected $attributes = [
        'start_time' => '9:00',
        'end_time' => '17:00',
    ];

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}
