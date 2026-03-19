<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class GuestCount extends Model
{
    use HasFactory;
    protected $fillable = [
        'count',
        'type',
        'reservation_room_id'
    ];

    public function reservation_rooms(): BelongsTo
    {
        return $this->belongsTo(ReservationRoom::class, 'reservation_room_id', 'id');
    }
}
