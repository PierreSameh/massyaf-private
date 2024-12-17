<?php

namespace App\Http\Controllers\api\Auth;

use Ichtrojan\Otp\Otp;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Notifications\SendOtpNotify;

class PhoneVerifyController extends Controller
{
    public function __construct(public Otp $otp){}
    public function verify(Request $request)
    {
        $request->validate(['token' =>'required|max:6']);

        $user = $request->user();
        $sendOtp = $this->otp->validate($user->phone_number, $request->token);

        if ($sendOtp->status == false)
        {
            return responseApi(400, 'Codes Invalid');
        }

        $user->update(['phone_verified_at'=> true]);
        return responseApi(200, 'Your phone has been verified');
    }
    public function resend(Request $request)
    {
        $user = $request->user();
        $user->notify(new SendOtpNotify());
        return responseApi(200, 'Verification Code Sent', $user);
    }
}
