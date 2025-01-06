<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Amenitie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AmenitieController extends Controller
{
    /**
     * Display a listing of the amenities.
     */
    public function index()
    {
        $amenities = Amenitie::all();
        return response()->json([
            'success' => true,
            'data' => $amenities
        ], 200);
    }

    /**
     * Store a newly created amenity in storage.
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type' => 'required|in:unit,hotel,room,reception,kitchen',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                "message" => $validator->errors()->first()
            ],422);
        }

        $amenity = Amenitie::create([
            "name" => $request->name,
            "type" => $request->type,
            "is_global" => true
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Amenity created successfully.',
            'data' => $amenity
        ], 201);
    }

    /**
     * Display the specified amenity.
     */
    public function show($id)
    {
        $amenitie = Amenitie::find($id);
        if(!$amenitie){
            return response()->json([
                'success' => false,
                'message' => 'Amenity not found.'
            ], 404);
        }
        return response()->json([
            'success' => true,
            'data' => $amenitie
        ], 200);
    }

    /**
     * Update the specified amenity in storage.
     */
    public function update(Request $request, $id)
    {
        $amenitie = Amenitie::find($id);
        if(!$amenitie){
            return response()->json([
                'success' => false,
                'message' => 'Amenity not found.'
            ], 404);
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type' => 'required|in:unit,hotel,room,reception,kitchen',
            'is_global' => 'required|in:0,1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                "message" => $validator->errors()->first()
            ],422);
        }

        $amenitie->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Amenity updated successfully.',
            'data' => $amenitie
        ], 200);
    }

    /**
     * Remove the specified amenity from storage.
     */
    public function destroy($id)
    {
        $amenitie = Amenitie::find($id);
        if(!$amenitie){
            return response()->json([
                'success' => false,
                'message' => 'Amenity not found.'
            ], 404);
        }
        $amenitie->delete();

        return response()->json([
            'success' => true,
            'message' => 'Amenity deleted successfully.'
        ], 200);
    }

}
