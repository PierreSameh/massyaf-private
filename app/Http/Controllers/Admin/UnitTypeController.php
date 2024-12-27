<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Type;
use Illuminate\Http\Request;

class UnitTypeController extends Controller
{
    // Get all types
    public function index()
    {
        $types = Type::all();
        return response()->json([
            'success' => true,
            'data' => $types,
        ], 200);
    }

    // Create a new type
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'type_for' => 'required|string|in:unit,hotel|max:255',
        ]);

        $type = Type::create($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Type created successfully.',
            'data' => $type,
        ], 201);
    }

    // Get a specific type by ID
    public function show($id)
    {
        $type = Type::find($id);

        if (!$type) {
            return response()->json([
                'success' => false,
                'message' => 'Type not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $type,
        ], 200);
    }

    // Update a specific type
    public function update(Request $request, $id)
    {
        $type = Type::find($id);

        if (!$type) {
            return response()->json([
                'success' => false,
                'message' => 'Type not found.',
            ], 404);
        }

        $validatedData = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'type_for' => 'sometimes|required|string|max:255',
        ]);

        $type->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Type updated successfully.',
            'data' => $type,
        ], 200);
    }

    // Delete a specific type
    public function destroy($id)
    {
        $type = Type::find($id);

        if (!$type) {
            return response()->json([
                'success' => false,
                'message' => 'Type not found.',
            ], 404);
        }

        $type->delete();

        return response()->json([
            'success' => true,
            'message' => 'Type deleted successfully.',
        ], 200);
    }
}
