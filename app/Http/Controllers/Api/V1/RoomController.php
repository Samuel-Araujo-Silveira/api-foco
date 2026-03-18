<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Traits\HttpResponses;
use App\Http\Requests\StoreRoomRequest;
use App\Http\Requests\UpdateRoomRequest;


class RoomController extends Controller
{
    use HttpResponses;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Room::all(), 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoomRequest $request)
    {
        $created = Room::create($request->validated()); 

        if ($created) {
            return $this->response('Room Created', 200, $created);
        }

        return $this->error('Room not created', 400);
    }

    /**
     * Display the specified resource.
     */
    public function show(Room $room)
    {
        return response()->json($room, 200); 
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoomRequest $request, Room $room)
    {
        $updated = $room->update($request->validated());

        if ($updated) {
            return $this->response('Room updated', 200, $request->validated());
        }

        return $this->error('Room not updated', 400);
    }

    /**
     * Remove the specified resource from storage.
     */
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
