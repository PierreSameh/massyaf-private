<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Amenitie;
use App\Models\City;
use App\Models\Compound;
use App\Models\Hotel;
use App\Models\Type;
use Illuminate\Http\Request;

class DropDownController extends Controller
{
    public function typesUnit(){
        $types = Type::where('type_for', 'unit')->get();
        return response()->json([
            "success" => true,
            "data"=> $types
        ], 200);
    }
    public function typesHotel(){
        $types = Type::where('type_for', 'hotel')->get();
        return response()->json([
            "success" => true,
            "data"=> $types
        ], 200);
    }
    public function cities(){
        $cities = City::all();
        return response()->json([
            "success" => true,
            "data"=> $cities
        ], 200);
    }
    public function compounds(Request $request){
        $compounds = Compound::where('city_id', $request->city_id)->get();
        return response()->json([
            "success" => true,
            "data"=> $compounds
        ], 200);
    }
    public function hotels(Request $request){
        $hotels = Hotel::where('city_id', $request->city_id)->get();
        return response()->json([
            "success" => true,
            "data"=> $hotels
        ], 200);
    }

    public function getAmenitiesByType($type)
    {
        $validTypes = ['unit', 'hotel', 'room', 'reception', 'kitchen'];

        if (!in_array($type, $validTypes)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid amenity type.'
            ], 400);
        }

        $userId = auth()->user()->id;
        
        $amenities = Amenitie::where('type', $type)
        ->where(function ($query) use ($userId) {
            $query->where('is_global', 1)
                  ->orWhere('user_id', $userId);
        })->get();

        return response()->json([
            'success' => true,
            'data' => $amenities
        ], 200);
    }
}
