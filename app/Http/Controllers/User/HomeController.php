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
        ])
        ->where('status', 'active')
        ->latest()->get();

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
        ->where('status', 'active')
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
        ])
        ->where('status', 'active')
        ->orderBy('rate', 'desc')->get();

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
        ->where('status', 'active')
        ->where('type', $request->type)
        ->has('sales') // Filter units that have sales
        ->inRandomOrder() // Get the units in random order
        ->get();
    
        return response()->json([
            "success" => true,
            "units" => $units
        ], 200);
    }

    public function filter(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            "type" => "nullable|in:unit,hotel",
            "unit_type_id" => "nullable|exists:types,id",
            "city_id" => "nullable|exists:cities,id",
            "compound_id" => "nullable|exists:compounds,id",
            "area" => "nullable|numeric",
            "min_price" => "nullable|numeric",
            "max_price" => "nullable|numeric",
        ]);
        // Start building the query
        $query = Unit::query();
    
        // Apply filters based on the request parameters
        if ($request->input('type')) {
            $query->where('type', $request->type);
        }
    
        if ($request->input('unit_type_id')) {
            $query->where('unit_type_id', $request->unit_type_id);
        }
    
        if ($request->input('city_id')) {
            $query->where('city_id', $request->city_id);
        }
    
        if ($request->input('compound_id')) {
            $query->where('compound_id', $request->compound_id);
        }
    
        if ($request->input('area')) {
            $query->where('area', ">=",$request->area);
        }
    
        if ($request->input('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
    
        if ($request->input('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }
    
        // Execute the query and get the results
        $units = $query->where('status', 'active')->get();
    
        // Return the filtered results
        return response()->json([
            'success' => true,
            'units' => $units,
        ]);
    }
    
}
