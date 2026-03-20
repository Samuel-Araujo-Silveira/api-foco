<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Models\ReservationRoom;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Reservation",
    title: "Reservation",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1001),
        new OA\Property(property: "date", type: "string", format: "date", example: "2024-06-01"),
        new OA\Property(property: "time", type: "string", example: "14:00:00"),
        new OA\Property(property: "hotel_id", type: "integer", example: 1),
        new OA\Property(
            property: "customer",
            type: "object",
            properties: [
                new OA\Property(property: "first_name", type: "string", example: "João"),
                new OA\Property(property: "last_name", type: "string", example: "Silva"),
            ]
        ),
        new OA\Property(
            property: "room",
            type: "object",
            properties: [
                new OA\Property(property: "id", type: "integer", example: 101),
                new OA\Property(property: "arrival_date", type: "string", format: "date", example: "2024-06-10"),
                new OA\Property(property: "departure_date", type: "string", format: "date", example: "2024-06-15"),
                new OA\Property(property: "currencycode", type: "string", example: "BRL"),
                new OA\Property(property: "meal_plan", type: "string", example: "breakfast"),
                new OA\Property(property: "totalprice", type: "number", format: "float", example: 1500.00),
                new OA\Property(
                    property: "guest_counts",
                    type: "array",
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: "type", type: "string", example: "adult"),
                            new OA\Property(property: "count", type: "integer", example: 2),
                        ]
                    )
                ),
                new OA\Property(
                    property: "prices",
                    type: "array",
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: "rate_id", type: "integer", example: 5),
                            new OA\Property(property: "date", type: "string", format: "date", example: "2024-06-10"),
                            new OA\Property(property: "amount", type: "number", format: "float", example: 300.00),
                        ]
                    )
                ),
            ]
        ),
    ]
)]
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
