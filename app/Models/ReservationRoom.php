<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;


class ReservationRoom extends Model
{
    protected $primaryKey = 'id';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'meal_plan',
        'totalprice',
        'arrival_date',
        'departure_date',
        'currencycode',
        'room_id',
        'reservation_id',
    ];

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function guest_counts(): HasMany
    {
        return $this->hasMany(GuestCount::class);
    }

    public function rates(): BelongsToMany
    {
        return $this->belongsToMany(Rate::class)->using(RateReservationRoom::class)->withPivot(['date', 'amount']);;
    }
}