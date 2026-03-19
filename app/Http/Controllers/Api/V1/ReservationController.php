<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreReservationRequest;
use App\Services\ReservationService;
use App\Traits\HttpResponses;
use App\Http\Resources\V1\ReservationResource;
use App\Models\Reservation;


class ReservationController extends Controller
{
    use HttpResponses;

    public function __construct(private ReservationService $reservationService) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $reservations = Reservation::with([
            'customer',
            'reservation_rooms.guest_counts',
            'reservation_rooms.rates',
        ])->get();

        return ReservationResource::collection($reservations);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreReservationRequest $request)
    {
        $isAvailable = $this->reservationService->isRoomAvailable(
            $request->room_id,
            $request->arrival_date,
            $request->departure_date
         );

        if (!$isAvailable) {
            return $this->error('Room not available for this period', 409);
        }

        $reservation = $this->reservationService->createReservation($request->validated());

        return $this->response('Reservation created', 201, $reservation);
    }

    /**
     * Display the specified resource.
     */
    public function show(Reservation $reservation)
    {
        return new ReservationResource(
            $reservation->load([
                'customer',
                'reservation_rooms.guest_counts',
                'reservation_rooms.rates',
            ])
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $deleted = $this->reservationService->deleteReservation($id);

        if (!$deleted) {
            return $this->error('Reservation not found', 404);
        }

        return $this->response('Reservation deleted', 200);
    }
}
