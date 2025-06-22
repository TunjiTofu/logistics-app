<?php


/*
 * |--------------------------------------------------
 * | API ROUTES FOR USER AUTHENTICATION
 * |--------------------------------------------------
 */

use App\Http\Controllers\API\V1\Auth\AuthController;
use App\Http\Controllers\API\V1\Shipment\ShipmentController;
use Illuminate\Support\Facades\Route;

Route::controller(ShipmentController::class)->group(function () {
   Route::post('create', 'createShipment');
   Route::get('get', 'getShipments');
});
