<?php

use App\Http\Controllers\PesertaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\KompetisiController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/midtrans-callback', [PesertaController::class, 'paymentCallback']);
