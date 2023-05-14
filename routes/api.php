<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/status', \App\Http\Controllers\Api\HealthCheck::class);
Route::post('/loopback', \App\Http\Controllers\Api\Loopback::class);
Route::apiResource('/chirps', \App\Http\Controllers\Api\ChirpController::class)->only('index', 'show');
