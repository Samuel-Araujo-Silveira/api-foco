<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Models\ReservationRoom;

class Reservation extends Model
{
    use HasFactory;
    public $incrementing = false;
    protected $fillable = [
        'time',
        'date',
        'hotel_id',
        'id',
        'customer_id'
    ];

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class, 'hotel_id', 'id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function reservation_rooms(): HasMany
    {
        return $this->hasMany(ReservationRoom::class);
    }
}
