<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Traits\HttpResponses;
use App\Http\Requests\StoreRoomRequest;
use App\Http\Requests\UpdateRoomRequest;
use OpenApi\Attributes as OA;


class RoomController extends Controller
{
    use HttpResponses;
    #[OA\Get(
        path: "/rooms",
        summary: "Listar todos os quartos",
        tags: ["Rooms"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Lista de quartos retornada com sucesso",
                content: new OA\JsonContent(
                    type: "array",
                    items: new OA\Items(ref: "#/components/schemas/Room")
                )
            )
        ]
    )]
    public function index()
    {
        return response()->json(Room::all(), 200);
    }
    
     #[OA\Post(
        path: "/rooms",
        summary: "Criar um novo quarto",
        tags: ["Rooms"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["id", "hotel_id", "hotel_name", "inventory_count", "name"],
                properties: [
                    new OA\Property(property: "id", type: "integer", example: 101),
                    new OA\Property(property: "hotel_id", type: "integer", example: 1),
                    new OA\Property(property: "hotel_name", type: "string", example: "Hotel Central"),
                    new OA\Property(property: "inventory_count", type: "integer", example: 10),
                    new OA\Property(property: "name", type: "string", example: "Quarto Duplo"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Quarto criado com sucesso",
                content: new OA\JsonContent(ref: "#/components/schemas/Room")
            ),
            new OA\Response(response: 400, description: "Erro ao criar quarto"),
            new OA\Response(response: 422, description: "Erro de validação")
        ]
    )]
    public function store(StoreRoomRequest $request)
    {
        $created = Room::create($request->validated()); 

        if ($created) {
            return $this->response('Room Created', 200, $created);
        }

        return $this->error('Room not created', 400);
    }


    #[OA\Get(
        path: "/rooms/{room}",
        summary: "Exibir um quarto específico",
        tags: ["Rooms"],
        parameters: [
            new OA\Parameter(
                name: "room",
                description: "ID do quarto",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer", example: 101)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Quarto encontrado",
                content: new OA\JsonContent(ref: "#/components/schemas/Room")
            ),
            new OA\Response(response: 404, description: "Quarto não encontrado")
        ]
    )]
    public function show(Room $room)
    {
        return response()->json($room, 200); 
    }

    #[OA\Put(
        path: "/rooms/{room}",
        summary: "Atualizar um quarto",
        tags: ["Rooms"],
        parameters: [
            new OA\Parameter(
                name: "room",
                description: "ID do quarto",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer", example: 101)
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "hotel_id", type: "integer", example: 1),
                    new OA\Property(property: "hotel_name", type: "string", example: "Hotel Central"),
                    new OA\Property(property: "inventory_count", type: "integer", minimum: 1, example: 10),
                    new OA\Property(property: "name", type: "string", example: "Quarto Duplo"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Quarto atualizado com sucesso",
                content: new OA\JsonContent(ref: "#/components/schemas/Room")
            ),
            new OA\Response(response: 400, description: "Erro ao atualizar quarto"),
            new OA\Response(response: 404, description: "Quarto não encontrado"),
            new OA\Response(response: 422, description: "Erro de validação")
        ]
    )]
    public function update(UpdateRoomRequest $request, Room $room)
    {
        $updated = $room->update($request->validated());

        if ($updated) {
            return $this->response('Room updated', 200, $request->validated());
        }

        return $this->error('Room not updated', 400);
    }

    #[OA\Delete(
        path: "/rooms/{room}",
        summary: "Remover um quarto",
        tags: ["Rooms"],
        parameters: [
            new OA\Parameter(
                name: "room",
                description: "ID do quarto",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer", example: 101)
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Quarto removido com sucesso"),
            new OA\Response(response: 400, description: "Erro ao remover quarto"),
            new OA\Response(response: 404, description: "Quarto não encontrado")
        ]
    )]
    public function destroy(Room $room)
    {
        $deleted = $room->delete();

        if($deleted){
            return $this->response('Room deleted', 200);
        }else{
            return $this->error('Room not deleted', 400);
        }
    }
}
