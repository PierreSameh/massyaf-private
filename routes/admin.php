<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Admin\CompoundController;
use App\Http\Controllers\Admin\HotelController;
use App\Http\Controllers\Admin\UnitTypeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix("admin")->group(function () {
    //Auth
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum,admin')->group(function () {
        //Logout
        Route::post('/logout', [AuthController::class, 'logout']);
        //Cities CRUD
        Route::apiResource('city', CityController::class);
        //Compounds CRUD
        Route::apiResource('compounds', CompoundController::class);
        //Unit Types CRUD
        Route::apiResource('types', UnitTypeController::class);
        //Hotels CRUD
        Route::apiResource('hotels', HotelController::class);

    });
});
