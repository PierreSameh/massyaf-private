<?php

namespace App\Http\Controllers\Global;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use Illuminate\Http\Request;

class BankAccountController extends Controller
{
    public function index(){
        $user = auth()->user();
        $accounts = $user->bankAccounts()->get();
        return response()->json([
            "success" => true,
            "data"=> $accounts
        ], 200);
    }

    public function store(Request $request){
        try{
        $request->validate([
            "name" => "required|string|max:255",
            "bank" => "required|string|max:255",
            "account_number" => "required|numeric"
        ]);

        $user = auth()->user();

        $account = BankAccount::create([
            "account_name" => $request->name,
            "bank" => $request->bank,
            "account_number" => $request->account_number,
            "user_id" => $user->id
        ]);

        return response()->json([
            "success" => true,
            "data" => $account
        ], 200);
        }catch(\Exception $e){
            return response()->json([
                "success" => false,
                "message" => "حدث خطاء في الخادم",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id){
        $user = auth()->user();
        $account = BankAccount::find($id);
        if($account->user_id != $user->id){
            return response()->json([
                "success" => false,
                "message" => "لا يمكنك حذف هذا الحساب"
            ], 401);
        }
        if($account){
            $account->delete();
            return response()->json([
                "success" => true,
                "message" => "حذف الحساب بنجاح"
            ], 200);
        } else {
            return response()->json([
                "success" => false,
                "message" => "الحساب غير موجود"
            ], 404);
        }
    }
}
