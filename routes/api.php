<?php

use Illuminate\Support\Facades\Route;

/*
 * |--------------------------------------------------
 * | API Version 1 Routes
 * |--------------------------------------------------
 */

Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        require __DIR__ . '/v1/Auth/auth.php';
    });

    // User Routes
    Route::middleware(['auth:sanctum',  'ability:user-access'])->group(function () {
        Route::prefix('shipment')->group(function () {
            require __DIR__.'/v1/user/shipment.php';
        });
    });

    //Admin Routes
    Route::prefix('admin')->group(function () {
        Route::middleware(['auth:sanctum',  'ability:admin-access'])->group(function () {
            require __DIR__.'/v1/admin/shipment.php';
            require __DIR__.'/v1/admin/system-logs.php';
        });
    });

    //Unguarded Routes
    require __DIR__.'/v1/utility.php';


});
