<?php

namespace  App\Services;

use App\Models\Transaction;
use App\Models\User;

class TransactionCallback
{
    public function completePayment($id){
        $transaction = Transaction::find($id);
        
        if($transaction->type == "deposit"){
            $receiver = User::find($transaction->receiver_id);
            //Update Receiver wallet
            $receiver->balance += $transaction->amount;
            $receiver->save();

            //Update Transaction
            $transaction->status = "completed";
            $transaction->updated_at = now();
            $transaction->save();
        } else if($transaction->type == "booking"){
            $sender = User::find($transaction->sender_id);
            $receiver = User::find($transaction->receiver_id);
            //Update Sender wallet
            $sender->balance -= $transaction->amount;
            $sender->save();

            //Update Receiver wallet
            $receiver->balance += $transaction->amount;
            $receiver->save();

            //Update Transaction
            $transaction->status = "completed";
            $transaction->updated_at = now();
            $transaction->save();
        }
    }
}