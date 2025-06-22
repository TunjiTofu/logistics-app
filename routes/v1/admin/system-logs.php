<?php


/*
 * |--------------------------------------------------
 * | API ADMIN ROUTES FOR SYSTEM LOGS
 * |--------------------------------------------------
 */

use App\Http\Controllers\API\V1\Admin\SystemLogs\SystemLogController;
use Illuminate\Support\Facades\Route;

Route::prefix('logs')->controller(SystemLogController::class)->group(function () {
   Route::get('get', 'getLogs');
});
