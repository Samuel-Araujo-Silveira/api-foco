<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreReservationRequest;
use App\Services\ReservationService;
use App\Traits\HttpResponses;
use App\Http\Resources\V1\ReservationResource;
use App\Models\Reservation;
use Illuminate\Support\Facades\Log;
use OpenApi\Attributes as OA;


class ReservationController extends Controller
{
    use HttpResponses;

    public function __construct(private ReservationService $reservationService) {}

    #[OA\Get(
        path: "/reservations",
        summary: "Listar todas as reservas",
        tags: ["Reservations"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Lista de reservas retornada com sucesso",
                content: new OA\JsonContent(
                    type: "array",
                    items: new OA\Items(ref: "#/components/schemas/Reservation")
                )
            )
        ]
    )]
    public function index()
    {
        try {
            $reservations = Reservation::with([
                'customer',
                'reservation_rooms.guest_counts',
                'reservation_rooms.rates',
            ])->get();

            return ReservationResource::collection($reservations);
        } catch (\Exception $e) {
            Log::error('Error fetching reservations', ['error' => $e->getMessage()]);
            return $this->error('Error fetching reservations', 500);
        }
    }

    #[OA\Post(
        path: "/reservations",
        summary: "Criar uma nova reserva",
        tags: ["Reservations"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: [
                    "id", "reservation_room_id", "first_name", "last_name",
                    "hotel_id", "room_id", "arrival_date", "departure_date",
                    "currencycode", "meal_plan", "totalprice", "guest_counts", "prices"
                ],
                properties: [
                    new OA\Property(property: "id", type: "integer", example: 1001),
                    new OA\Property(property: "reservation_room_id", type: "integer", example: 2001),
                    new OA\Property(property: "first_name", type: "string", example: "João"),
                    new OA\Property(property: "last_name", type: "string", example: "Silva"),
                    new OA\Property(property: "hotel_id", type: "integer", example: 1),
                    new OA\Property(property: "room_id", type: "integer", example: 101),
                    new OA\Property(property: "arrival_date", type: "string", format: "date", example: "2024-06-10"),
                    new OA\Property(property: "departure_date", type: "string", format: "date", example: "2024-06-15"),
                    new OA\Property(property: "currencycode", type: "string", example: "BRL"),
                    new OA\Property(property: "meal_plan", type: "string", example: "breakfast"),
                    new OA\Property(property: "totalprice", type: "number", format: "float", example: 1500.00),
                    new OA\Property(
                        property: "guest_counts",
                        type: "array",
                        items: new OA\Items(
                            required: ["type", "count"],
                            properties: [
                                new OA\Property(property: "type", type: "string", example: "adult"),
                                new OA\Property(property: "count", type: "integer", minimum: 1, example: 2),
                            ]
                        )
                    ),
                    new OA\Property(
                        property: "prices",
                        type: "array",
                        items: new OA\Items(
                            required: ["rate_id", "date", "amount"],
                            properties: [
                                new OA\Property(property: "rate_id", type: "integer", example: 5),
                                new OA\Property(property: "date", type: "string", format: "date", example: "2024-06-10"),
                                new OA\Property(property: "amount", type: "number", format: "float", example: 300.00),
                            ]
                        )
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Reserva criada com sucesso",
                content: new OA\JsonContent(ref: "#/components/schemas/Reservation")
            ),
            new OA\Response(response: 409, description: "Quarto não disponível para o período"),
            new OA\Response(response: 422, description: "Erro de validação")
        ]
    )]
    public function store(StoreReservationRequest $request)
    {
        try {
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
        } catch (\Exception $e) {
            Log::error('Error creating reservation', ['error' => $e->getMessage()]);
            return $this->error('Error creating reservation', 500);
        }
    }

    #[OA\Get(
        path: "/reservations/{reservation}",
        summary: "Exibir uma reserva específica",
        tags: ["Reservations"],
        parameters: [
            new OA\Parameter(
                name: "reservation",
                description: "ID da reserva",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer", example: 1001)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Reserva encontrada",
                content: new OA\JsonContent(ref: "#/components/schemas/Reservation")
            ),
            new OA\Response(response: 404, description: "Reserva não encontrada")
        ]
    )]
    public function show(Reservation $reservation)
    {
        try {
            return new ReservationResource(
                $reservation->load([
                    'customer',
                    'reservation_rooms.guest_counts',
                    'reservation_rooms.rates',
                ])
            );
        } catch (\Exception $e) {
            Log::error('Error fetching reservation', ['error' => $e->getMessage()]);
            return $this->error('Error fetching reservation', 500);
        }
    }

   

    #[OA\Delete(
        path: "/reservations/{id}",
        summary: "Remover uma reserva",
        tags: ["Reservations"],
        parameters: [
            new OA\Parameter(
                name: "id",
                description: "ID da reserva",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer", example: 1001)
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Reserva removida com sucesso"),
            new OA\Response(response: 404, description: "Reserva não encontrada")
        ]
    )]
    public function destroy(string $id)
    {
        try {
            $deleted = $this->reservationService->deleteReservation($id);

            if (!$deleted) {
                return $this->error('Reservation not found', 404);
            }

            return $this->response('Reservation deleted', 200);
        } catch (\Exception $e) {
            Log::error('Error deleting reservation', ['error' => $e->getMessage()]);
            return $this->error('Error deleting reservation', 500);
        }
    }
}
