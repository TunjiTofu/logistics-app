<?php


/*
 * |--------------------------------------------------
 * | API ROUTES FOR USER AUTHENTICATION
 * |--------------------------------------------------
 */

use App\Http\Controllers\API\V1\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('user')->controller(AuthController::class)
    ->group(function () {
    Route::post('register','register');
    Route::post('login', 'login');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', 'logout');
    });
});
