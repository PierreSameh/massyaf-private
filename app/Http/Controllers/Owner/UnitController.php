<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUnitRequest;
use App\Models\Room;
use App\Models\Unit;
use App\Models\UnitImage;
use App\Models\UnitVideo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UnitController extends Controller
{
    public function create(StoreUnitRequest $request)
    {
        try {
            $validated = $request->validated();
            $unitData = $validated['data'];
            
            
            // Handle optional arrays if they exist
            $optionalArrays = [
                'rooms',
                'available_dates',
                'cancel_policies',
                'additional_fees',
                'long_term_reservations',
                'sales',
                'special_reservation_times'
            ];
            
            
            // Create the unit
            $unit = Unit::create($unitData);
            if($request->rooms){
            foreach ($request->rooms as $room) {
                Room::create([
                    "unit_id" => $unit->id,
                    
                ]); 
            }
            }
            
            // Handle images if present
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('uploads/images', 'public');
                    UnitImage::create([
                        "unit_id" => $unit->id,
                        "image" => $path
                    ]);
                }
            }
            
            // Handle videos if present
            if ($request->hasFile('videos')) {
                foreach ($request->file('videos') as $video) {
                    $path = $video->store('uploads/videos', 'public');
                    UnitVideo::create([
                        "unit_id" => $unit->id,
                        "video" => $path
                    ]);
                }
            }
            
            // Return success response with the created unit
            return response()->json([
                'success' => true,
                'message' => 'Unit created successfully',
                'unit' => $unit
            ], 201);
            
        } catch (\Exception $e) {
            // Delete uploaded files if unit creation fails
            if (!empty($images)) {
                foreach ($images as $image) {
                    Storage::disk('public')->delete($image);
                }
            }
            if (!empty($videos)) {
                foreach ($videos as $video) {
                    Storage::disk('public')->delete($video);
                }
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create unit',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function get($id){
        $unit = Unit::find($id);
        return response()->json($unit);
    }
}
