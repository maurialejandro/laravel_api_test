<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PlatoController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::prefix('api')->group(function () {
    Route::post('register' ,[UserController::class, 'register']);
    Route::post('login' ,[UserController::class, 'login']);
    Route::get('token', [UserController::class, 'token']);
    Route::get('plato', [PlatoController::class, 'index']);
});