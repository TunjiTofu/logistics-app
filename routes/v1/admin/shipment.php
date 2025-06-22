<?php


/*
 * |--------------------------------------------------
 * | API ADMIN ROUTES FOR SHIPMENT
 * |--------------------------------------------------
 */

use App\Http\Controllers\API\V1\Admin\Shipment\ShipmentController;
use Illuminate\Support\Facades\Route;

Route::controller(ShipmentController::class)->group(function () {
//   Route::post('create', 'createShipment');
   Route::get('get', 'getShipments');
});
