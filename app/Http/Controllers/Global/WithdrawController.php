<?php

namespace App\Http\Controllers\Global;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\Transaction;
use App\Models\Withdraw;
use Illuminate\Http\Request;

class WithdrawController extends Controller
{
    public function index(){
        $user = auth()->user();
        $withdraws = Withdraw::where("user_id", $user->id)
            ->with('bankAccount')
            ->latest()
            ->get();

        return response()->json([
            "success" => true,
            'data'=> $withdraws
        ], 200);
    }

    public function store(Request $request){
        try{
        $request->validate([
            "amount" => "required|numeric",
            "bank_account_id" => "required|exists:bank_accounts,id"
        ]);

        $user = auth()->user();
        $authorized = BankAccount::where('user_id', $user->id)
            ->where('id', $request->bank_account_id)
            ->first();

        if (!$authorized) {
            return response()->json([
                "success" => false,
                "message" => "لا يمكنك السحب من هذا الحساب"
            ], 401);
        }
        //Check user balance
        if($user->balance < $request->amount){
            return response()->json([
            "success"=> false,
            "message" => "لا يوجد رصيد كافي في حسابك"
            ], 400);
        }

        $withdraw = Withdraw::create([
            "amount" => $request->amount,
            "bank_account_id" => $request->bank_account_id,
            "user_id" => $user->id
        ]);

        $transaction = Transaction::create([
            "sender_id" => $user->id,
            "amount" => $withdraw->amount,
            "type" => "withdraw",
            "created_at" => now()
        ]);
        return response()->json([
            "success" => true,
            "data" => $withdraw
        ], 200);
        }catch(\Exception $e){
            return response()->json([
                "success" => false,
                "message" => "حدث خطاء في الخادم",
                "error" => $e->getMessage()
            ], 500);
        }
    }

}
