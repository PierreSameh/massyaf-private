<?php

namespace App\Http\Controllers\Global;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Traits\PayTabsPayment;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    use PayTabsPayment;

    public function deposit(Request $request)
    {
        try{
            $request->validate([
                "amount" => "required|numeric"
            ]);
            $user = auth()->user();
            $transaction = Transaction::create([
                "receiver_id" => $user->id,
                "amount" => $request->amount,
                "type" => "deposit",
                "created_at" => now()
            ]);
            // dd([ (int) env('PAYTABS_PROFILE_ID'), env('PAYTABS_SERVER_KEY')]);
            $result = $this->createPayTabsPayment($transaction->amount, $transaction->id);
            return response()->json([
                "success" => true,
                "message" => "تم ارسال طلب الايداع بنجاح",
                "payment_url" => $result['data']['redirect_url']
            ], 200);
        }catch(\Exception $e){
            return response()->json([
                "success" => false,
                "message" => "حدث خطاء في الخادم",
                "error"=> $e->getMessage()
            ], 500);
        }
    }

    public function balance(){
        $user = auth()->user();
        $balance = $user->balance;
        return response()->json([
            "success" => true,
            "balance" => $balance,
        ], 200);
    }
}
