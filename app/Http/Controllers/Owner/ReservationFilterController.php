<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReservationFilterController extends Controller
{
    public function pending(){
        $user = auth()->user();
        $reservations = Reservation::whereRelation("unit","owner_id","=", $user->id)
            ->where('status', 'pending')
            ->with('unit.images', 'unit.rooms')
            ->latest()
            ->get();

        foreach ($reservations as $reservation) {        
            $dateFrom = Carbon::parse($reservation->date_from);
            $dateTo = Carbon::parse($reservation->date_to);
            $reservation->days_count = $dateFrom->diffInDays($dateTo) + 1; // Include the start day
        }

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
            ->latest()
            ->get();

        foreach ($reservations as $reservation) {        
            $dateFrom = Carbon::parse($reservation->date_from);
            $dateTo = Carbon::parse($reservation->date_to);
            $reservation->days_count = $dateFrom->diffInDays($dateTo) + 1; // Include the start day
        }

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
            ->latest()
            ->get();

        foreach ($reservations as $reservation) {        
            $dateFrom = Carbon::parse($reservation->date_from);
            $dateTo = Carbon::parse($reservation->date_to);
            $reservation->days_count = $dateFrom->diffInDays($dateTo) + 1; // Include the start day
        }

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
            ->latest()
            ->get();

        foreach ($reservations as $reservation) {        
            $dateFrom = Carbon::parse($reservation->date_from);
            $dateTo = Carbon::parse($reservation->date_to);
            $reservation->days_count = $dateFrom->diffInDays($dateTo) + 1; // Include the start day
        }

        return response()->json([
            "success" => true,
            "data"=> $reservations
        ], 200);
    }
}
