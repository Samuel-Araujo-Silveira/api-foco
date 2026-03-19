<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreReservationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id'                  => 'required|integer|unique:reservations,id',
            'reservation_room_id' => 'required|integer|unique:reservation_rooms,id',
            'first_name'          => 'required|string',
            'last_name'           => 'required|string',
            'hotel_id'            => 'required|exists:hotels,id',
            'room_id'             => 'required|exists:rooms,id',
            'arrival_date'        => 'required|date|after_or_equal:today',
            'departure_date'      => 'required|date|after:arrival_date',
            'currencycode'        => 'required|string',
            'meal_plan'           => 'required|string',
            'totalprice'          => 'required|numeric',
            'guest_counts'        => 'required|array',
            'guest_counts.*.type' => 'required|string',
            'guest_counts.*.count'=> 'required|integer|min:1',
            'prices'              => 'required|array',
            'prices.*.rate_id'    => 'required|exists:rates,id',
            'prices.*.date'       => 'required|date',
            'prices.*.amount'     => 'required|numeric',
        ];
    }
}
