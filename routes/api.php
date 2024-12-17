<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\Auth\AuthController;
use App\Http\Controllers\api\Auth\ProfileController;
use App\Http\Controllers\api\Auth\PhoneVerifyController;


Route::middleware('auth:sanctum')->group(function(){
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::put('/user/update', [ProfileController::class, 'updateProfile']);
    Route::prefix('verify/')->controller(PhoneVerifyController::class)->group(function(){
        Route::post('/', 'verify');
        Route::get('resend', 'resend');
    });
});

Route::prefix('account/')->controller(AuthController::class)->group(function(){
    Route::post('register', 'register');
    Route::delete('logout', 'logout')->middleware('auth:sanctum');
    Route::delete('destroy', 'deleteAccount')->middleware('auth:sanctum');
});
