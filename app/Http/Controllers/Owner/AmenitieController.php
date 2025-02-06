<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Amenitie;
use Illuminate\Http\Request;

class AmenitieController extends Controller
{
    public function store(Request $request){
        $request->validate([
            "name" => "required|string|max:255",
            "type" => "required|in:unit,hotel,room,reception,kitchen"
        ]);
        $userId = auth()->user()->id;
        $amenitie = Amenitie::create([
            "name"=> $request->name,
            "type"=> $request->type,
            "user_id" => $userId
        ]);
        return response()->json([
            "success" => true,
            "message" => "Amenitie created successfully",
            "data" => $amenitie
        ], 201);
    }
}
