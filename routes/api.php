<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\ImportController;
use App\Http\Controllers\Api\V1\RoomController;
use App\Http\Controllers\Api\V1\ReservationController;

Route::prefix('v1')->group(function(){
    Route::post('/import', [ImportController::class, 'store']);
    Route::apiResource('rooms', RoomController::class);
    Route::apiResource('reservations', ReservationController::class);
});