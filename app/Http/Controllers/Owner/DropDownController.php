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
    public function compounds(){
        $compounds = Compound::all();
        return response()->json([
            "success" => true,
            "data"=> $compounds
        ], 200);
    }
    public function hotels(){
        $hotels = Hotel::all();
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

        $amenities = Amenitie::where('type', $type)->get();

        return response()->json([
            'success' => true,
            'data' => $amenities
        ], 200);
    }
}
