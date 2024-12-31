<?php

use App\Http\Controllers\User\HomeController;
use App\Http\Controllers\User\ReservationController;
use App\Http\Controllers\User\UnPaidController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::prefix('user')->group(function () {
    Route::prefix('home')->group(function () {
        Route::get("/all", [HomeController::class,"index"]);
        Route::get("/sales", [HomeController::class,"sales"]);
        Route::get("/sales-type", [HomeController::class,"typeSales"]);
        Route::get("/top-rated", [HomeController::class,"topRated"]);
        Route::get("/filter", [HomeController::class,"filter"]);
    });
    Route::middleware('auth:sanctum')->group(function () {
        Route::prefix("reservations")->group(function () {
            Route::post('/create', [ReservationController::class, 'reserve']);
            Route::get('/all', [ReservationController::class, 'getAll']);
            Route::get('/{id}', [ReservationController::class,'get']);
            Route::put('/{id}', [ReservationController::class,'cancel']);
        });

        Route::prefix('unpaid')->group(function () {
            Route::get('/all', [UnPaidController::class, 'getAll']);
        });
    });
});