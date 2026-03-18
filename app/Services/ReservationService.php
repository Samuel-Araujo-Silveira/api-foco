<?php

namespace App\Services;
use App\Models\Room;
use App\Models\Reservation;
use App\Models\ReservationRoom;


class ReservationService
{
    public function isRoomAvailable(int $room_id, String $arrival_date, String $departure_date): bool
    {
        $reservation_room = ReservationRoom::where('room_id', $room_id)->get();
        dd($reservation_room);
        return true;
    }
}