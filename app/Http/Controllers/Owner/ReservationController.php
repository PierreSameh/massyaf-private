<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReservationController extends Controller
{
    public function getAll(){
        $user = auth()->user();
        $reservations = Reservation::whereRelation("unit","owner_id","=", $user->id)
            ->with('unit.images', 'unit.rooms')
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
