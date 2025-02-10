<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Compound;
use App\Models\Hotel;
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

    public function get($id)
    {
        $unit = Unit::with([
            'owner',
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
            'rooms.amenities',
            'amenities',
            'reservations'
        ])->find($id);
        if (!$unit) {
            return response()->json(['message' => 'Unit not found'], 404);
        }

        // Divide amenities into categories based on type
        $unitAmenities = [];
        $receptionAmenities = [];
        $kitchenAmenities = [];

        foreach ($unit->amenities as $amenity) {
            switch ($amenity->type) {
                case 'unit':
                case 'hotel':
                    $unitAmenities[] = $amenity;
                    break;
                case 'reception':
                    $receptionAmenities[] = $amenity;
                    break;
                case 'kitchen':
                    $kitchenAmenities[] = $amenity;
                    break;
            }
        }

        // Add categorized amenities to the response
        $unitData = $unit->toArray();
        $unitData['unit_amenities'] = $unitAmenities;
        $unitData['reception_amenities'] = $receptionAmenities;
        $unitData['kitchen_amenities'] = $kitchenAmenities;

        // Remove the original amenities array
        unset($unitData['amenities']);

        return response()->json($unitData);
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
    public function bestSeller(){
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
        ->withCount(['reservations' => function($query) {
            $query->where('status', 'approved')
                ->where('created_at', '>=', now()->subMonths(3)); // Last 3 months
        }])
        ->orderByDesc('reservations_count')
        ->take(10)  // Limit results
        ->get();
    

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
            "search" => "nullable|string|max:255"
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
        if ($request->search) {
            $searchTerm = "%{$request->input('search')}%";
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', $searchTerm)
                  ->orWhere('description', 'like', $searchTerm);
            });
        }
    
        // Execute the query and get the results
        $units = $query->where('status', 'active')->get();
    
        // Return the filtered results
        return response()->json([
            'success' => true,
            'units' => $units,
        ]);
    }
    
    public function getCity($id){
        $city = City::find($id);
        if (!$city) {
            return response()->json([
                "success" => false,
                "message" => "City not found"
            ], 404);
        }
        return response()->json([
            "success" => true,
            "city" => $city
        ], 200);
    }
    public function getCompound($id){
        $compound = Compound::find($id);
        if (!$compound) {
            return response()->json([
                "success" => false,
                "message" => "compound not found"
            ], 404);
        }
        return response()->json([
            "success" => true,
            "compound" => $compound
        ], 200);
    }
    public function getHotel($id){
        $hotel = Hotel::find($id);
        if (!$hotel) {
            return response()->json([
                "success" => false,
                "message" => "hotel not found"
            ], 404);
        }
        return response()->json([
            "success" => true,
            "hotel" => $hotel
        ], 200);
    }
}
