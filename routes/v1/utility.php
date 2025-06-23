<?php


/*
 * |--------------------------------------------------
 * | API ROUTES FOR USER AUTHENTICATION
 * |--------------------------------------------------
 */

use App\Http\Controllers\API\V1\Auth\AuthController;
use App\Http\Controllers\API\V1\Shipment\ShipmentController;
use Illuminate\Support\Facades\Route;

Route::prefix('track')->controller(ShipmentController::class)->group(function () {
    Route::get('{trackingNumber}', 'trackShipment');
});
