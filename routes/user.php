<?php

use App\Http\Controllers\User\ChatController;
use App\Http\Controllers\User\HomeController;
use App\Http\Controllers\User\ReservationController;
use App\Http\Controllers\User\UnPaidController;
use App\Http\Controllers\User\WishlistController;
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
        //Wishlist
        Route::prefix('wishlist')->group(function () {
            Route::get('/', [WishlistController::class, 'index']);
            Route::post('/', [WishlistController::class, 'store']);
            Route::delete('/{id}', [WishlistController::class, 'destroy']);
        });

        //Chat
        Route::prefix('chat')->group(function () {
            Route::post('/message', [ChatController::class, 'sendMessage']);
            Route::get('/', [ChatController::class,'getChats']);
            Route::get('/{id}', [ChatController::class,'getMessages']);
            Route::put('/{id}', [ChatController::class,'seenMessages']);
            Route::delete('/{id}', [ChatController::class,'delete']);
        });
    });
});