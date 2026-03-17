<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\ImportController;



Route::get('/import', [ImportController::class, 'index']);
