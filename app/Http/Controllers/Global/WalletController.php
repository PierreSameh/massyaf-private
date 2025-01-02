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
        $request->validate([
            "amount" => "required|numeric"
        ]);
        $user = auth()->user();
        $transaction = Transaction::create([
            "receiver_id" => $user->id,
            "amount" => $request->amount,
            "type" => "deposit"
        ]);
        // dd([ (int) env('PAYTABS_PROFILE_ID'), env('PAYTABS_SERVER_KEY')]);
        $result = $this->createPayTabsPayment(200, $transaction->id);

        return response()->json($result);
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
