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
    public function isRoomAvailable(int $room_id, String $newArrival_date, String $newDeparture_date): bool
    {
        $roomReservations = ReservationRoom::where('room_id', $room_id)->get();

        foreach($roomReservations as $reservation)
        {
            if ($this->hasConflict($newArrival_date, $newDeparture_date, $reservation->arrival_date, $reservation->departure_date)) {
                return false; 
            }
        }
        return true;
    }

    public function hasConflict(string $newCheckIn, string $newCheckOut, string $existingCheckIn, string $existingCheckOut): bool
    {
        $newIn  = Carbon::parse($newCheckIn);
        $newOut = Carbon::parse($newCheckOut);
        $exIn   = Carbon::parse($existingCheckIn);
        $exOut  = Carbon::parse($existingCheckOut);

        return $newIn->lt($exOut) && $newOut->gt($exIn);
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

    public function deleteReservation(string|int $reservationId): bool
    {
        $reservation = Reservation::find($reservationId);

        if (!$reservation) {
            return false; 
        }

        DB::transaction(function () use ($reservation) {
            $reservationRooms = ReservationRoom::where('reservation_id', $reservation->id)->get();

            foreach ($reservationRooms as $room) {
                GuestCount::where('reservation_room_id', $room->id)->delete();
                RateReservationRoom::where('reservation_room_id', $room->id)->delete();
                
                $room->delete();
            }

            $reservation->delete();
        });

        return true;
    }

    public function updateReservation(Reservation $reservation, array $data): Reservation
    {
        return DB::transaction(function () use ($reservation, $data) {

            if (isset($data['first_name']) || isset($data['last_name'])) {
                $reservation->customer->update([
                    'first_name' => $data['first_name'] ?? $reservation->customer->first_name,
                    'last_name'  => $data['last_name']  ?? $reservation->customer->last_name,
                ]);
            }

            $reservation->update([
                'hotel_id' => $data['hotel_id'] ?? $reservation->hotel_id,
            ]);

            $reservationRoom = $reservation->reservation_rooms()->first();

            $reservationRoom->update([
                'room_id'        => $data['room_id']        ?? $reservationRoom->room_id,
                'arrival_date'   => $data['arrival_date']   ?? $reservationRoom->arrival_date,
                'departure_date' => $data['departure_date'] ?? $reservationRoom->departure_date,
                'currencycode'   => $data['currencycode']   ?? $reservationRoom->currencycode,
                'meal_plan'      => $data['meal_plan']      ?? $reservationRoom->meal_plan,
                'totalprice'     => $data['totalprice']     ?? $reservationRoom->totalprice,
            ]);

            if (isset($data['guest_counts'])) {
                $reservationRoom->guest_counts()->delete();
                foreach ($data['guest_counts'] as $guestCount) {
                    $reservationRoom->guest_counts()->create([
                        'type'  => $guestCount['type'],
                        'count' => $guestCount['count'],
                    ]);
                }
            }

            if (isset($data['prices'])) {
                $reservationRoom->rates()->detach();
                foreach ($data['prices'] as $price) {
                    RateReservationRoom::create([
                        'reservation_room_id' => $reservationRoom->getKey(),
                        'rate_id'             => $price['rate_id'],
                        'date'                => $price['date'],
                        'amount'              => $price['amount'],
                    ]);
                }
            }

            return $reservation->fresh();
        });
    }
}