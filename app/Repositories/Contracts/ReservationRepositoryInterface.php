<?php
// app/Repositories/Contracts/ReservationRepositoryInterface.php
namespace App\Repositories\Contracts;

use App\Models\Reservation;
use Illuminate\Database\Eloquent\Collection;

interface ReservationRepositoryInterface
{
    public function allWithRelations(): Collection;
    public function findWithRelations(Reservation $reservation): Reservation;
    public function create(array $data): Reservation;
    public function delete(string $id): bool;
}