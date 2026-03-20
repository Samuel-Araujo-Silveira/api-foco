<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Hotel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Room",
    title: "Room",
    required: ["id", "hotel_id", "hotel_name", "inventory_count", "name"],
    properties: [
        new OA\Property(property: "id", type: "integer", example: 101),
        new OA\Property(property: "hotel_id", type: "integer", example: 1),
        new OA\Property(property: "hotel_name", type: "string", example: "Hotel Central"),
        new OA\Property(property: "inventory_count", type: "integer", example: 10),
        new OA\Property(property: "name", type: "string", example: "Quarto Duplo"),
        new OA\Property(property: "created_at", type: "string", format: "date-time"),
        new OA\Property(property: "updated_at", type: "string", format: "date-time"),
    ]
)]

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
