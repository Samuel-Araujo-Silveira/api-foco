<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateReservationRequest extends FormRequest
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
            'first_name'          => 'sometimes|string',
            'last_name'           => 'sometimes|string',
            'hotel_id'            => 'sometimes|exists:hotels,id',
            'room_id'             => 'sometimes|exists:rooms,id',
            'arrival_date'        => 'sometimes|date|after_or_equal:today',
            'departure_date'      => 'sometimes|date|after:arrival_date',
            'currencycode'        => 'sometimes|string',
            'meal_plan'           => 'sometimes|string',
            'totalprice'          => 'sometimes|numeric',
            'guest_counts'        => 'sometimes|array',
            'guest_counts.*.type' => 'sometimes|string',
            'guest_counts.*.count'=> 'sometimes|integer|min:1',
            'prices'              => 'sometimes|array',
            'prices.*.rate_id'    => 'sometimes|exists:rates,id',
            'prices.*.date'       => 'sometimes|date',
            'prices.*.amount'     => 'sometimes|numeric',    
        ];
    }
}
