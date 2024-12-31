<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReservationController extends Controller
{
    public function getAll(){
        $user = auth()->user();
        $reservations = Reservation::whereRelation("unit","owner_id","=", $user->id)
            ->with('unit.images', 'unit.rooms')
            ->where('paid', 1)
            ->latest()
            ->get();

        return response()->json([
            "success" => true,
            "data"=> $reservations
        ], 200);
    }

    public function get($id){
        $user = auth()->user();
        $reservation = Reservation::where('id', $id)
            ->whereRelation("unit","owner_id","=", $user->id)
            ->with('unit.images', 'unit.rooms')
            ->first();
        if (!$reservation) {
            return response()->json([
                "success" => false,
                "message" => "الحجز غير موجود"
            ], 404);
        }

        
        return response()->json([
            "success" => true,
            "reservation" => $reservation
        ], 200);
    }

    public function cancel($id) {
        $user = auth()->user();
        $reservation = Reservation::where('id', $id)
            ->whereRelation("unit","owner_id","=", $user->id)
            ->whereNotIn('status', ['canceled_user', 'canceled_owner'])
            ->where('paid', 1)
            ->first();

        if (!$reservation) {
            return response()->json([
                "success" => false,
                "message" => "الحجز غير موجود"
            ], 404);
        }
        $reservation->status = "canceled_owner";
        $reservation->cancelled_at = now();
        $reservation->save();
        //Take the book advance from the owner
        $user->balance -= $reservation->book_advance;
        $user->save();
        //Return money to customer
        $customer = User::where('id', $reservation->user_id)->first();
        $customer->balance += $reservation->book_advance;
        $customer->save();

        /*
            Should create transactions and notification
        */
        return response()->json([
            "success" => true,
            "message" => "تم الغاء الحجز بنجاح"
        ], 200);
    }
    public function accept($id) {
        $user = auth()->user();
        $reservation = Reservation::where('id', $id)
            ->where('status', 'pending')
            ->whereRelation("unit","owner_id","=", $user->id)
            ->where('paid', 1)
            ->first();
        if (!$reservation) {
            return response()->json([
                "success" => false,
                "message" => "الحجز غير موجود"
            ], 404);
        }
        $reservation->status = "accepted";
        $reservation->save();
        return response()->json([
            "success" => true,
            "message" => "تم قبول الحجز بنجاح"
        ], 200);
    }
    public function approve($id) {
        $user = auth()->user();
        $reservation = Reservation::where('id', $id)
            ->where('status', 'accepted')
            ->whereRelation("unit","owner_id","=", $user->id)
            ->where('paid', 1)
            ->first();
        if (!$reservation) {
            return response()->json([
                "success" => false,
                "message" => "الحجز غير موجود"
            ], 404);
        }
        $reservation->status = "approved";
        $reservation->approved_at = now();
        $reservation->save();
        return response()->json([
            "success" => true,
            "message" => "تم تأكيد الحجز بنجاح"
        ], 200);
    }
}
