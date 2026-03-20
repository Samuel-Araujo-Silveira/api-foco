<?php
// app/Repositories/Contracts/RoomRepositoryInterface.php
namespace App\Repositories\Contracts;

use App\Models\Room;
use Illuminate\Database\Eloquent\Collection;

interface RoomRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): Room;
    public function create(array $data): Room;
    public function update(Room $room, array $data): bool;
    public function delete(Room $room): bool;
}