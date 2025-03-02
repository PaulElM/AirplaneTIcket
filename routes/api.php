<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AirportController;
use App\Http\Controllers\TicketController;

Route::middleware('api')->group(function () {
    Route::get('/airports', [AirportController::class, 'index']);
    Route::post('/airports', [AirportController::class, 'store']);

    Route::get('/tickets', [TicketController::class, 'index']);
    Route::post('/tickets', [TicketController::class, 'create']);
    Route::patch('/tickets/{id}/cancel', [TicketController::class, 'cancel']);
    Route::patch('/tickets/{id}/seat', [TicketController::class, 'changeSeat']);
});
