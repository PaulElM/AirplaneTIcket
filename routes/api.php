<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AirportController;
use App\Http\Controllers\TicketController;

Route::middleware('api')->group(function () {
    Route::get('/airports', [AirportController::class, 'index']);
    Route::post('/airports', [AirportController::class, 'store']);
});
