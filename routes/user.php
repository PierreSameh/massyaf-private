<?php

use App\Http\Controllers\User\HomeController;
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
});