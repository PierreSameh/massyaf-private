<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(){
        $units = Unit::with([
            'city',
            'compound',
            'hotel',
            'unitType',
            'additionalFees',
            'availableDates',
            'sales',
            'cancelPolicies',
            'longTermReservations',
            'specialReservationTimes',
            'images',
            'videos',
            'rooms',
        ])->latest()->get();

        return response()->json([
            "success" => true,
            "units" => $units
        ], 200);
    }
    public function sales()
    {
        $units = Unit::with([
            'city',
            'compound',
            'hotel',
            'unitType',
            'additionalFees',
            'availableDates',
            'sales',
            'cancelPolicies',
            'longTermReservations',
            'specialReservationTimes',
            'images',
            'videos',
            'rooms',
        ])
        ->has('sales') // Filter units that have sales
        ->inRandomOrder() // Get the units in random order
        ->get();
    
        return response()->json([
            "success" => true,
            "units" => $units
        ], 200);
    }

    public function topRated(){
        $units = Unit::with([
            'city',
            'compound',
            'hotel',
            'unitType',
            'additionalFees',
            'availableDates',
            'sales',
            'cancelPolicies',
            'longTermReservations',
            'specialReservationTimes',
            'images',
            'videos',
            'rooms',
        ])->orderBy('rate', 'desc')->get();

        return response()->json([
            "success" => true,
            "units" => $units
        ], 200);
    }

    public function typeSales(Request $request)
    {
        $request->validate([
            "type" => "required|in:unit,hotel"
        ]);
        $units = Unit::with([
            'city',
            'compound',
            'hotel',
            'unitType',
            'additionalFees',
            'availableDates',
            'sales',
            'cancelPolicies',
            'longTermReservations',
            'specialReservationTimes',
            'images',
            'videos',
            'rooms',
        ])
        ->where('type', $request->type)
        ->has('sales') // Filter units that have sales
        ->inRandomOrder() // Get the units in random order
        ->get();
    
        return response()->json([
            "success" => true,
            "units" => $units
        ], 200);
    }
    
}
