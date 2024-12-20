<?php

namespace App\Http\Controllers\Api\Auth\password;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Notifications\SendOtpNotify;

class ForgetPasswordController extends Controller
{
    public function forget(Request $request)
    {
        $request->validate(['phone_number' => 'required|exists:users,phone_number']);
        $user = User::wherePhone_number($request->phone_number)->first();
        if (!$user) {
            return responseApi(404, 'Not Found User');
        }
        $user->notify(new SendOtpNotify());
        return responseApi(200, 'Sent OTP Successfully');
    }
}
