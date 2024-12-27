<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use Illuminate\Http\Request;

class HotelController extends Controller
{

    public function index()
    {
        // Fetch all hotels with units and their relationships
        $hotels = Hotel::with(['units', 'units.city', 'units.type', 'units.additionalFees', 
                                'units.availableDates', 'units.sales', 'units.cancelPolicies',
                                'units.longTermReservations', 'units.specialReservationTimes',
                                'units.images', 'units.videos', 'units.rooms', 'units.amenities'])
                        ->get();
                        
        return response()->json([
            "success" => true,
            "data"=> $hotels
        ], 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'details' => 'nullable|string',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);

        $hotel = Hotel::create($validated);
        return response()->json([
            "success" => true,
            "message" => "Hotel created successfully",
            "data"=> $hotel
        ], 201);
    }

    public function show($id)
    {
        // Fetch a single hotel with its units and relations
        $hotel = Hotel::with(['units', 'units.city', 'units.type', 'units.additionalFees', 
                               'units.availableDates', 'units.sales', 'units.cancelPolicies',
                               'units.longTermReservations', 'units.specialReservationTimes',
                               'units.images', 'units.videos', 'units.rooms', 'units.amenities'])
                       ->find($id);
        if(!$hotel){
            return response()->json([
                "success" => false,
                "message" => "Hotel not found"
            ],404);
        }
        // Format units' amenities
        foreach ($hotel->units as $unit) {
            $unitAmenities = [];
            $receptionAmenities = [];
            $kitchenAmenities = [];
        
            foreach ($unit->amenities as $amenity) {
                switch ($amenity->type) {
                    case 'unit':
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

            // Add categorized amenities to the unit
            $unit->unit_amenities = $unitAmenities;
            $unit->reception_amenities = $receptionAmenities;
            $unit->kitchen_amenities = $kitchenAmenities;
        }

        return response()->json([
            "success" => true,
            "message" => "Hotel retrieved successfully",
            "data" => $hotel
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $hotel = Hotel::find($id);
        if(!$hotel){
            return response()->json([
                "success" => false,
                "message" => "Hotel not found"
            ],404);
        }
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'address' => 'sometimes|string|max:255',
            'details' => 'nullable|string',
            'lat' => 'sometimes|numeric',
            'lng' => 'sometimes|numeric',
        ]);

        $hotel->update($validated);
        return response()->json([
            "success" => true,
            "message" => "Hotel updated successfully",
            "data" => $hotel
        ], 200); 
    }

    public function destroy($id)
    {
        $hotel = Hotel::findOrFail($id);
        if(!$hotel){
            return response()->json(['message' => 'Hotel not found'], 404);
        }
        $hotel->delete();

        return response()->json(['message' => 'Hotel deleted successfully'], 200);
    }
}
