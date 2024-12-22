<?php

use App\Http\Controllers\Admin\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix("admin")->group(function () {
    //Auth
    Route::post('/login', [AuthController::class, 'login']);

});
