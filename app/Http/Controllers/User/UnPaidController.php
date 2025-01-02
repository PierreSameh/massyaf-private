<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Transaction;
use App\Traits\PayTabsPayment;
use Illuminate\Http\Request;

class UnPaidController extends Controller
{
    use PayTabsPayment;
    public function getAll() {
        $user = auth()->user();
        $reservations = Reservation::where('user_id', $user->id)
            ->with('unit.images')
            ->where('paid', 0)
            ->get();

        return response()->json([
            "success" => true,
            "reservations" => $reservations
        ], 200);
    }

    public function pay($id){
        try{
        $user = auth()->user();
        $reservation = Reservation::where('user_id', $user->id)
            ->where('id', $id)
            ->with('unit')
            ->first();
        if (!$reservation) {
            return response()->json([
                "success" => false,
                "message" => "الحجز غير موجود"
            ], 404);
        }

        if ($reservation->paid == 1){
            return response()->json([
                "success" => false,
                "message" => "الحجز مدفوع"
            ], 400);
        }
        $owner = $reservation->unit->owner;
        $transaction = Transaction::create([
            "sender_id" => $user->id,
            "receiver_id" => $owner->id,
            "amount" => $reservation->book_advance,
            "type" => "booking",
            "created_at" => now()
        ]);

        //Update Reservation transaction id
        $reservation->transaction_id = $transaction->id;
        $reservation->save();
        //Payment Process
        $paymentUrl = $this->createPayTabsPayment($transaction->amount, $transaction->id);
        return response()->json([
            "success" => true,
            "message" => "من فضلك اكمل عملية الدفع",
            "payment_url" => $paymentUrl['data']['redirect_url'],
            "reservation" => $reservation,
        ]);
    }catch (\Exception $e){
        return response()->json([
            "success" => false,
            "message" => "حدث خطاء في الخادم",
            "error"=> $e->getMessage()
        ], 500);
    }
    }
}
