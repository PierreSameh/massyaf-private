<?php

namespace App\Http\Controllers\Global;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function balance(){
        $user = auth()->user();
        $balance = $user->balance;
        return response()->json([
            "success" => true,
            "balance" => $balance,
        ], 200);
    }
}
