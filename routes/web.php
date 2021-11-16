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
    // Routes profile
    Route::post('register' ,[UserController::class, 'register']);
    Route::post('login' ,[UserController::class, 'login']);
    Route::get('token', [UserController::class, 'token']);
    Route::post('info-user', [UserController::class, 'info']);
    Route::post('store-avatar', [UserController::class, 'storeAvatar']);
    Route::get('get-avatar/{id}/{img}', [UserController::class, 'getAvatar']);
    Route::post('update-profile-name', [UserController::class, 'updateProfileName']);
    Route::post('update-profile-email', [UserController::class, 'updateProfileEmail']);
    Route::post('update-profile-pass', [UserController::class, 'updateProfilePass']);
    Route::post('check-user', [UserController::class, 'checkUser']);
    
    // Routes platos
    Route::post('store-file-plato', [PlatoController::class, 'storeFilePlato']);
    Route::get('plato', [PlatoController::class, 'index']);
    Route::post('store-platos', [PlatoController::class, 'store']);
    Route::get('get-file/{id}/{img}', [PlatoController::class, 'getFile']);
    Route::post('get-plato', [PlatoController::class, 'getPlato']); 
    Route::post('get-total-platos', [PlatoController::class, 'getTotalPlatos']);
    Route::post('store-puntuation', [PlatoController::class, 'storePuntuation']);
    Route::post('add-favorite', [PlatoController::class, 'addFavorite']);
    Route::post('remove-favorite', [PlatoController::class, 'removeFavorite']);
    Route::post('get-favorites', [PlatoController::class, 'getFavorites']);
    Route::post('get-best-rating', [PlatoController::class, 'getBestRating']);
    Route::post('search', [PlatoController::class, 'search']);
});
