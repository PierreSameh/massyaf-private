<?php

use App\Http\Controllers\Owner\UnitController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/owner/unit/store', [UnitController::class, 'create']);
Route::post('/owner/unit/{id}', [UnitController::class, 'update']);
Route::get('/owner/unit/{id}', [UnitController::class, 'get']);
Route::delete('/owner/unit/{id}', [UnitController::class, 'destroy']);