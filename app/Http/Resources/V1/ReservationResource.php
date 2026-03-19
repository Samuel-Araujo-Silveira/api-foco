<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReservationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
         return [
            'id'       => $this->id,
            'date'     => $this->date,
            'time'     => $this->time,
            'hotel_id' => $this->hotel_id,
            'customer' => [
                'first_name' => $this->customer->first_name,
                'last_name'  => $this->customer->last_name,
            ],
            'room' => [
                'id'             => $this->reservation_rooms->first()->id,
                'arrival_date'   => $this->reservation_rooms->first()->arrival_date,
                'departure_date' => $this->reservation_rooms->first()->departure_date,
                'currencycode'   => $this->reservation_rooms->first()->currencycode,
                'meal_plan'      => $this->reservation_rooms->first()->meal_plan,
                'totalprice'     => $this->reservation_rooms->first()->totalprice,
                'guest_counts'   => $this->reservation_rooms->first()->guest_counts->map(fn($g) => [
                    'type'  => $g->type,
                    'count' => $g->count,
                ]),
                'prices' => $this->reservation_rooms->first()->rates->map(fn($r) => [
                    'rate_id' => $r->id,
                    'date'    => $r->pivot->date,
                    'amount'  => $r->pivot->amount,
                ]),
            ],
        ];
    }
}
