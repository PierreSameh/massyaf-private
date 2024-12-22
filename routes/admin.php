<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Admin\CompoundController;
use App\Http\Controllers\Admin\UnitTypeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix("admin")->group(function () {
    //Auth
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum,admin')->group(function () {
        Route::apiResource('city', CityController::class);
        Route::apiResource('compounds', CompoundController::class);
        Route::apiResource('types', UnitTypeController::class);
    });
});
