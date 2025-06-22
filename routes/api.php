<?php

use Illuminate\Support\Facades\Route;

/*
 * |--------------------------------------------------
 * | API Version 1 Routes
 * |--------------------------------------------------
 */

Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        require __DIR__.'/v1/user/auth.php';
    });

    Route::middleware(['auth:sanctum',  'ability:user-access'])->group(function () {
        Route::prefix('shipment')->group(function () {
            require __DIR__.'/v1/user/shipment.php';
        });
    });
});
