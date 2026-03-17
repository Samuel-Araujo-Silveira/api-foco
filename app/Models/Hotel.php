<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Rate;
use App\Models\Room;
use App\Models\Reservation;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Hotel extends Model
{
    public $incrementing = false;
    protected $fillable = [
        'name',
        'id',
    ];

    public function rates(): HasMany
    {
        return $this->hasMany(Rate::class);
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }
}
