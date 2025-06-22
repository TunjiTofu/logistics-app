<?php


/*
 * |--------------------------------------------------
 * | API ADMIN ROUTES FOR SHIPMENT
 * |--------------------------------------------------
 */

use App\Http\Controllers\API\V1\Admin\Shipment\ShipmentController;
use Illuminate\Support\Facades\Route;

Route::prefix('shipment')->controller(ShipmentController::class)->group(function () {
   Route::get('get', 'getShipments');

    Route::prefix('{shipmentId}')->group(function () {
        Route::patch('update', 'updateStatus');
    });
});
