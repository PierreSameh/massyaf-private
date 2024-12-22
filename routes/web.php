<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/unauthorized', function () {
    return response()->json([
        'message'=> 'unauthorized',
        ], 401);
});