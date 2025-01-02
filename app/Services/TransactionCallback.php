<?php

namespace  App\Services;

use App\Models\Reservation;
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
            $receiver = User::find($transaction->receiver_id);

            //Update Receiver wallet
            $receiver->balance += $transaction->amount;
            $receiver->save();

            //Update Transaction
            $transaction->status = "completed";
            $transaction->updated_at = now();
            $transaction->save();

            $reservation = Reservation::where('transaction_id', $transaction->id)->first();
            $reservation->paid = 1;
            $reservation->save();
        }
    }
}