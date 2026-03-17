<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Hotel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class Rate extends Model
{
    public $incrementing = false;
    protected $fillable = [
        'name',
        'id',
        'active',
        'price',
        'hotel_id'
    ];

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class, 'hotel_id', 'id');
    }

    public function reservation_rooms(): BelongsToMany
    {
        return $this->belongsToMany(ReservationRoom::class)->using(RateReservationRoom::class)->withPivot(['date', 'amount']);;
    }
}
