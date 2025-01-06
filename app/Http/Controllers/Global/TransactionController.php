<?php

namespace App\Http\Controllers\Global;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(){
        $user = auth()->user();
        $sentTransactions = Transaction::where("sender_id", $user->id)->get();
        $receivedTransactions = Transaction::where("receiver_id", $user->id)->get();

        return response()->json([
            "success" => true,
            'data'=> [
                'sent_transactions'=> $sentTransactions,
                'received_transactions'=> $receivedTransactions
            ]
        ], 200);
    }
}
