<?php

use App\Http\Controllers\PesertaController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\KompetisiController;
use App\Http\Controllers\Api\AtletController;
use App\Http\Controllers\Api\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/kompetisi', [KompetisiController::class, 'getAllKompetisi']);
Route::get('/kompetisi/{id}', [KompetisiController::class, 'getKompetisiById']);

Route::middleware('auth:sanctum')->get('/user', [UserController::class, 'show']);

Route::post('/midtrans-callback', [PesertaController::class, 'paymentCallback']);

Route::middleware('api.key')->group(function () {
    Route::get('/atlets', [AtletController::class, 'index']);
});
