<?php

namespace App\Services;
use App\Models\ReservationRoom;
use Illuminate\Support\Carbon;
use App\Models\Customer;
use App\Models\Reservation;
use App\Models\GuestCount;
use App\Models\RateReservationRoom;
use Illuminate\Support\Facades\DB;

class ReservationService
{
    public function isRoomAvailable(int $room_id, String $arrival_date, String $departure_date): bool
    {
        $roomReservations = ReservationRoom::where('room_id', $room_id)->get();

        $newCheckIn = Carbon::parse($arrival_date);
        $newCheckOut = Carbon::parse($departure_date);

        foreach($roomReservations as $reservation)
        {
            $currentCheckIn  = Carbon::parse($reservation->arrival_date); 
            $currentCheckOut = Carbon::parse($reservation->departure_date); 

            if($newCheckIn->lt($currentCheckOut) && $newCheckOut->gt($currentCheckIn))
            {
                return false;
            }
        }
        return true;
    }

    public function createReservation(array $data): Reservation
    {
        return DB::transaction(function () use ($data) {

            $customer = Customer::firstOrCreate([
                'first_name' => $data['first_name'],
                'last_name'  => $data['last_name'],
            ]);

            $reservation = Reservation::create([
                'id'          => $data['id'],
                'date'        => now()->toDateString(),
                'time'        => now()->toTimeString(),
                'hotel_id'    => $data['hotel_id'],
                'customer_id' => $customer->getKey(),
            ]);

            $reservationRoom = ReservationRoom::create([
                'id'             => $data['reservation_room_id'],
                'reservation_id' => $data['id'],
                'room_id'        => $data['room_id'],
                'arrival_date'   => $data['arrival_date'],
                'departure_date' => $data['departure_date'],
                'currencycode'   => $data['currencycode'],
                'meal_plan'      => $data['meal_plan'],
                'totalprice'     => $data['totalprice'],
            ]);

            foreach ($data['guest_counts'] as $guestCount) {
                GuestCount::create([
                    'reservation_room_id' => $data['reservation_room_id'],
                    'type'                => $guestCount['type'],
                    'count'               => $guestCount['count'],
                ]);
            }

            foreach ($data['prices'] as $price) {
                RateReservationRoom::create([
                    'reservation_room_id' => $data['reservation_room_id'],
                    'rate_id'             => $price['rate_id'],
                    'date'                => $price['date'],
                    'amount'              => $price['amount'],
                ]);
            }

            return $reservation;
        });
    }
}