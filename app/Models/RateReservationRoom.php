<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;


class RateReservationRoom extends Pivot
{
    protected $fillable = [
        'reservation_room_id',
        'rate_id',
        'date',
        'amount',
    ];
}
