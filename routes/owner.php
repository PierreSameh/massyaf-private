<?php

use App\Http\Controllers\Owner\ReservationController;
use App\Http\Controllers\Owner\ReservationFilterController;
use App\Http\Controllers\Owner\UnitController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Owner\DropDownController;

Route::prefix('owner')->group(function () {
    //Unit Creation Dropdown
    Route::prefix('dropdown')->group(function () {
        Route::get('/unit-types', [DropDownController::class, 'typesUnit']);
        Route::get('/hotel-types', [DropDownController::class, 'typesHotel']);
        Route::get('/cities', [DropDownController::class, 'cities']);
        Route::get('/compounds', [DropDownController::class, 'compounds']);
        Route::get('/hotels', [DropDownController::class, 'hotels']);
        Route::get('/amenities/unit', [DropDownController::class, 'getAmenitiesByType'])->defaults('type', 'unit');
        Route::get('/amenities/hotel', [DropDownController::class, 'getAmenitiesByType'])->defaults('type', 'hotel');
        Route::get('/amenities/room', [DropDownController::class, 'getAmenitiesByType'])->defaults('type', 'room');
        Route::get('/amenities/reception', [DropDownController::class, 'getAmenitiesByType'])->defaults('type', 'reception');
        Route::get('/amenities/kitchen', [DropDownController::class, 'getAmenitiesByType'])->defaults('type', 'kitchen');
    });
    Route::middleware('auth:sanctum')->group(function () {

        //Units
        Route::prefix('units')->group(function () {
            Route::get('/', [UnitController::class, 'getAll']);
            Route::get('/paginate', [UnitController::class, 'getPaginate']);
            Route::post('/store', [UnitController::class, 'create']);
            Route::post('/{id}', [UnitController::class, 'update']);
            Route::get('/{id}', [UnitController::class, 'get']);
            Route::delete('/{id}', [UnitController::class, 'destroy']);
            
        });

        Route::prefix('reservations')->group(function () {
            Route::get('/all', [ReservationController::class, 'getAll']);
            Route::get('/{id}', [ReservationController::class,'get']);
            Route::put('/{id}/cancel', [ReservationController::class,'cancel']);
            Route::put('/{id}/accept', [ReservationController::class,'accept']);
            Route::put('/{id}/approve', [ReservationController::class,'approve']);
        });
        Route::prefix('home')->group(function () {
            Route::get('/widgets', [ReservationFilterController::class, 'widgets']);
            Route::get('/pending', [ReservationFilterController::class, 'pending']);
            Route::get('/reserved', [ReservationFilterController::class, 'reserved']);
            Route::get('/approved', [ReservationFilterController::class, 'approved']);
            Route::get('/cancelled', [ReservationFilterController::class, 'cancelled']);
        });
    });
});