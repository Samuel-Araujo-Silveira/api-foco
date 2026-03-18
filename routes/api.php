<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\ImportController;
use App\Http\Controllers\Api\V1\RoomController;


Route::prefix('v1')->group(function(){
    Route::post('/import', [ImportController::class, 'store']);
    Route::apiResource('rooms', RoomController::class);
});