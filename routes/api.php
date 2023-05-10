<?php

use App\Http\Controllers\MainController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// calculating routes
Route::post('rpc', [MainController::class, 'rpc']);
Route::post('ffi', [MainController::class, 'ffi']);
Route::post('websocket', [MainController::class, 'socket']);
Route::post('rabbitmq', [MainController::class, 'rabbitmq']);
Route::post('php', [MainController::class, 'php']);

// db routes
Route::post('seedResources', [MainController::class, 'seedResources']);
Route::post('seedBookings', [MainController::class, 'seedBookings']);
Route::post('flushDatabase', [MainController::class, 'flushDatabase']);
Route::post('getChartData', [MainController::class, 'getChartData']);

