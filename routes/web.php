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

Route::get('/payment/success', function () {
    return view('payment-success');
});

Route::get('/payment/failure', function () {
    return view('payment-failure');
});