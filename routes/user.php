<?php

use App\Http\Controllers\User\HomeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get("/units", [HomeController::class,"index"]);