<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DisconnectionController;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('disconnections', DisconnectionController::class);
    Route::post('disconnections/{id}/reconnect', [DisconnectionController::class, 'reconnect']);
    Route::get('disconnections/consumer/{consumerId}/history', [DisconnectionController::class, 'consumerHistory']);
    Route::get('disconnections/stats/overview', [DisconnectionController::class, 'getStats']);
});