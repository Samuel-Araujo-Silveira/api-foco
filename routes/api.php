<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\ImportController;
use App\Http\Controllers\Api\V1\RoomController;
use App\Http\Controllers\Api\V1\ReservationController;
use App\Http\Controllers\Api\V1\AuthController;

Route::prefix('v1')->name('v1.')->group(function(){
    Route::post('/import', [ImportController::class, 'store'])->middleware('auth:sanctum');
    Route::apiResource('rooms', RoomController::class);
    Route::apiResource('reservations', ReservationController::class);
    Route::post('/login', [AuthController::class, 'login']);
});