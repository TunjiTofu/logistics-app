<?php


/*
 * |--------------------------------------------------
 * | API ROUTES FOR USER AUTHENTICATION
 * |--------------------------------------------------
 */

use App\Http\Controllers\API\V1\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('user')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
});
