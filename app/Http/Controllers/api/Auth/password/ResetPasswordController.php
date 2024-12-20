<?php

namespace App\Http\Controllers\Api\Auth\password;

use App\Models\User;
use Ichtrojan\Otp\Otp;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ResetPasswordController extends Controller
{
    private $otp;
    public function __construct()
    {
        $this->otp = new Otp();
    }
    public function reset(Request $request)
    {
        $request->validate([
            'phone_number' =>'required|exists:users,phone_number',
            'token' =>'required|max:5',
            'password' =>'required|min:8|confirmed',
            'password_confirmation' =>'required'
        ]);

        $sendOtp = $this->otp->validate($request->phone_number, $request->token);
        if($sendOtp->status == false){
            return responseApi(401, 'Codes Invalid');
        }

        $user = User::wherePhone_number($request->phone_number)->first();
        if (!$user) {
            return responseApi(404, 'User not found');
        }

        $user->update(['password' => $request->password]);
        return responseApi(200, 'Password Reset Successfully');
    }
}
