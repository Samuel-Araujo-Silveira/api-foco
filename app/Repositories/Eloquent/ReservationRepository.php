<?php
namespace App\Repositories\Eloquent;

use App\Models\Reservation;
use App\Repositories\Contracts\ReservationRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use App\Models\ReservationRoom;
use Illuminate\Support\Facades\DB;  


class ReservationRepository implements ReservationRepositoryInterface
{
    private array $relations = [
        'customer',
        'reservation_rooms.guest_counts',
        'reservation_rooms.rates',
    ];

    public function allWithRelations(): Collection
    {
        return Reservation::with($this->relations)->get();
    }

    public function findWithRelations(Reservation $reservation): Reservation
    {
        return $reservation->load($this->relations);
    }

    public function create(array $data): Reservation
    {
        return Reservation::create($data);
    }

    public function delete(string $id): bool
    {
        $reservation = Reservation::find($id);

        if (!$reservation) {
            return false;
        }

        DB::transaction(function () use ($reservation) {
            $reservationRoom = $reservation->reservation_rooms()->first();

            if ($reservationRoom) {
                $reservationRoom->guest_counts()->delete();
                $reservationRoom->rates()->detach();
                $reservationRoom->delete();
            }

            $reservation->delete();
        });

        return true;
    }
}