<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Unit;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReservationFilterController extends Controller
{
    public function pending(){
        $user = auth()->user();
        $reservations = Reservation::whereRelation("unit","owner_id","=", $user->id)
            ->where('status', 'pending')
            ->with('unit.images', 'unit.rooms')
            ->where('paid', 1)
            ->latest()
            ->get();

        return response()->json([
            "success" => true,
            "data"=> $reservations
        ], 200);
    }
    public function reserved(){
        $user = auth()->user();
        $reservations = Reservation::whereRelation("unit","owner_id","=", $user->id)
            ->where('status', 'accepted')
            ->with('unit.images', 'unit.rooms')
            ->where('paid', 1)
            ->latest()
            ->get();


        return response()->json([
            "success" => true,
            "data"=> $reservations
        ], 200);
    }
    public function approved(){
        $user = auth()->user();
        $reservations = Reservation::whereRelation("unit","owner_id","=", $user->id)
            ->where('status', 'approved')
            ->with('unit.images', 'unit.rooms')
            ->where('paid', 1)
            ->latest()
            ->get();

        return response()->json([
            "success" => true,
            "data"=> $reservations
        ], 200);
    }
    public function cancelled(){
        $user = auth()->user();
        $reservations = Reservation::whereRelation("unit","owner_id","=", $user->id)
            ->whereIn('status', ['canceled_user', 'canceled_owner'])
            ->with('unit.images', 'unit.rooms')
            ->where('paid', 1)
            ->latest()
            ->get();

        return response()->json([
            "success" => true,
            "data"=> $reservations
        ], 200);
    }

    public function widgets(){
        $user = auth()->user();
        $newRequests = Reservation::whereRelation("unit","owner_id","=", $user->id)
            ->where('status', 'pending')
            ->where('paid', 1)
            ->count();
        $units = Unit::where('owner_id', $user->id)->count();
        $totalProfits = Reservation::whereRelation("unit","owner_id","=", $user->id)
            ->where('status', 'approved')
            ->sum('owner_profit');
        return response()->json([
            "success"=> true,
            "new_requests" => $newRequests,
            "units_count" => $units,
            "total_profits" => $totalProfits
        ]);
    }
}
