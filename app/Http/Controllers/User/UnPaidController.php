<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;

class UnPaidController extends Controller
{
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
}
