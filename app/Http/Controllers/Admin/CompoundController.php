<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Compound;
use Illuminate\Http\Request;

class CompoundController extends Controller
{
    // Display a listing of the Compounds
    public function index()
    {
        $Compounds = Compound::all();
        return response()->json([
            "success" => true,
            "message" => "Compounds retrieved successfully",
            "data"=> $Compounds
             ], 200);
    }

    // Store a newly created Compound in storage
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

        $Compound = Compound::create($validatedData);

        return response()->json([
            "success" => true,'message' => 'Compound created successfully', 'data' => $Compound], 201);
        }
        catch(\Exception $e){
            return response()->json([
                "success"=> false,
                "message" => "server error occured",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    // Display the specified Compound
    public function show($id)
    {
        $Compound = Compound::find($id);
        if(!$Compound){
            return response()->json([
                "success" => false,'message' => 'Compound not found'], 404);
        }
        return response()->json([
            "success" => true,'message' => 'Compound retrieved successfully', 'data' => $Compound], 200);
    }

    // Update the specified Compound in storage
    public function update(Request $request, $id)
    {
        $Compound = Compound::find($id);
        if(!$Compound){
            return response()->json([
                "success" => false,
                'message' => 'Compound not found'], 404);
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

        $Compound->update($validatedData);

        return response()->json([
            "success" => true,
            'message' => 'Compound updated successfully', 'data' => $Compound
        ], 200);
    }

    // Remove the specified Compound from storage
    public function destroy($id)
    {
        $Compound = Compound::find($id);
        if(!$Compound){
            return response()->json([
                "success" => false,
                'message' => 'Compound not found'], 404);
        }
        $Compound->delete();

        return response()->json([ 
            'success' => true,'message' => 'Compound deleted successfully'], 200);
    }
}
