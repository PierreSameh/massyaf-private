<?php

use App\Http\Controllers\Global\BankAccountController;
use App\Http\Controllers\Global\PayTabsController;
use App\Http\Controllers\Global\WalletController;
use App\Http\Controllers\Global\WithdrawController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\Auth\AuthController;
use App\Http\Controllers\Global\ProfileController;
use App\Http\Controllers\api\Auth\PhoneVerifyController;
use App\Http\Controllers\Api\Auth\password\ResetPasswordController;
use App\Http\Controllers\Api\Auth\password\ForgetPasswordController;
use App\Http\Controllers\Chat\Owner\LiveChatOwnerController;
use App\Http\Controllers\Chat\User\LiveChatUserController;


Route::post('/account/register', [AuthController::class, 'register']);
Route::post('/account/login', [AuthController::class, 'login']);
Route::get('/account/forget-password/send-code', [AuthController::class, 'sendForgetPassword']);
Route::post('/account/forget-password/check-code', [AuthController::class, 'forgetPasswordCheckCode']);
Route::post('/account/forget-password/reset', [AuthController::class, 'forgetPassword']);

Route::post('/deposit', [WalletController::class, 'deposit'])->middleware('auth:sanctum');
Route::get('/callback', [PayTabsController::class, 'handleCallBack']);
Route::middleware('auth:sanctum')->group(function(){
    //Ask Code for email validation
    Route::get('account/ask-code', [AuthController::class, 'askCode']);
    Route::post('account/verify', [AuthController::class, 'verify']);
    Route::delete('account/logout', [AuthController::class, 'logout']);
    Route::delete('account/destroy', [AuthController::class, 'deleteAccount']);

    //Profile
    Route::prefix('profile')->group(function(){
        Route::post('/change-password', [ProfileController::class, 'changePassword']);
        Route::get('/', [ProfileController::class, 'get']);
        Route::post('/update', [ProfileController::class,'update']);
    });
    //Wallet
    Route::prefix('wallet')->group(function(){
        //Bank Account
        Route::post('/bank-account', [BankAccountController::class, 'store']);
        Route::get('/bank-account', [BankAccountController::class, 'index']);
        Route::delete('/bank-account/{id}', [BankAccountController::class, 'destroy']);

        //Withdraws
        Route::get('/withdraws', [WithdrawController::class, 'index']);
        Route::post('/withdraws', [WithdrawController::class, 'store']);

        //Balance
        Route::get('/balance', [WalletController::class, 'balance']);
    });
});

