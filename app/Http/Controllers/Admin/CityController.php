<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\Request;

class CityController extends Controller
{
    // Display a listing of the cities
    public function index()
    {
        $cities = City::all();
        return response()->json([
            "success" => true,
            "message" => "Cities retrieved successfully",
            "data"=> $cities
             ], 200);
    }

    // Store a newly created city in storage
    public function store(Request $request)
    {
        try{
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'lat_top_right' => 'required|string',
            'lng_top_right' => 'required|string',
            'lat_top_left' => 'required|string',
            'lng_top_left' => 'required|string',
            'lat_bottom_right' => 'required|string',
            'lng_bottom_right' => 'required|string',
            'lat_bottom_left' => 'required|string',
            'lng_bottom_left' => 'required|string',
        ]);

        $city = City::create($validatedData);

        return response()->json([
            "success" => true,'message' => 'City created successfully', 'data' => $city], 201);
        }
        catch(\Exception $e){
            return response()->json([
                "success"=> false,
                "message" => "server error occured",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    // Display the specified city
    public function show($id)
    {
        $city = City::find($id);
        if(!$city){
            return response()->json([
                "success" => false,'message' => 'City not found'], 404);
        }
        return response()->json([
            "success" => true,'message' => 'City retrieved successfully', 'data' => $city], 200);
    }

    // Update the specified city in storage
    public function update(Request $request, $id)
    {
        $city = City::find($id);
        if(!$city){
            return response()->json([
                "success" => false,
                'message' => 'City not found'], 404);
        }
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'lat_top_right' => 'required|string',
            'lng_top_right' => 'required|string',
            'lat_top_left' => 'required|string',
            'lng_top_left' => 'required|string',
            'lat_bottom_right' => 'required|string',
            'lng_bottom_right' => 'required|string',
            'lat_bottom_left' => 'required|string',
            'lng_bottom_left' => 'required|string',
        ]);

        $city->update($validatedData);

        return response()->json([
            "success" => true,
            'message' => 'City updated successfully', 'data' => $city
        ], 200);
    }

    // Remove the specified city from storage
    public function destroy($id)
    {
        $city = City::find($id);
        if(!$city){
            return response()->json([
                "success" => false,
                'message' => 'City not found'], 404);
        }
        $city->delete();

        return response()->json([ 
            'success' => true,'message' => 'City deleted successfully'], 200);
    }
}
