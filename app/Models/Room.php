<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Hotel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Room extends Model
{
    use HasFactory;
    public $incrementing = false;
    protected $fillable = [
        'name',
        'id',
        'inventory_count',
        'hotel_name',
        'hotel_id'

    ];

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class, 'hotel_id', 'id');
    }

    public function reservationRooms(): HasMany
    {
        return $this->hasMany(ReservationRoom::class);
    }
}
