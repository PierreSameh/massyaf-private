<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUnitRequest;
use App\Models\AdditionalFee;
use App\Models\AvailableDate;
use App\Models\CancelPoliciy;
use App\Models\LongTermReservations;
use App\Models\Room;
use App\Models\Sale;
use App\Models\SpecialReservationTimes;
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
            $unitData = $validated;
    
            // Create the unit
            $unit = Unit::create($unitData);
    
            // Handle rooms and their amenities
            if (isset($unitData['rooms']) && is_array($unitData['rooms'])) {
                foreach ($unitData['rooms'] as $roomData) {
                    $room = Room::create([
                        'unit_id' => $unit->id,
                        'bed_count' => $roomData['bed_count'],
                        'bed_sizes' => json_encode($roomData['bed_sizes']),
                    ]);
                
                    // Attach amenities to the room using the pivot table
                    if (isset($roomData['amenities']) && is_array($roomData['amenities'])) {
                        $room->amenities()->attach($roomData['amenities']);
                    }
                }
            }
    
            // Handle available dates
            if (isset($unitData['available_dates']) && is_array($unitData['available_dates'])) {
                foreach ($unitData['available_dates'] as $date) {
                    AvailableDate::create([
                        'unit_id' => $unit->id,
                        'from' => $date['from'],
                        'to' => $date['to'],
                    ]);
                }
            }
    
            // Handle cancel policies
            if (isset($unitData['cancel_policies']) && is_array($unitData['cancel_policies'])) {
                foreach ($unitData['cancel_policies'] as $policy) {
                    CancelPoliciy::create([
                        'unit_id' => $unit->id,
                        'days' => $policy['days'],
                        'penalty' => $policy['penalty'],
                    ]);
                }
            }
    
            // Handle additional fees
            if (isset($unitData['additional_fees']) && is_array($unitData['additional_fees'])) {
                foreach ($unitData['additional_fees'] as $fee) {
                    AdditionalFee::create([
                        'unit_id' => $unit->id,
                        'fees' => $fee['fees'],
                        'amount' => $fee['amount'],
                    ]);
                }
            }
    
            // Handle long-term reservations
            if (isset($unitData['long_term_reservations']) && is_array($unitData['long_term_reservations'])) {
                foreach ($unitData['long_term_reservations'] as $reservation) {
                    LongTermReservations::create([
                        'unit_id' => $unit->id,
                        'more_than_days' => $reservation['more_than_days'],
                        'sale_percentage' => $reservation['sale_percentage'],
                    ]);
                }
            }
    
            // Handle sales
            if (isset($unitData['sales']) && is_array($unitData['sales'])) {
                foreach ($unitData['sales'] as $sale) {
                    Sale::create([
                        'unit_id' => $unit->id,
                        'from' => $sale['from'],
                        'to' => $sale['to'],
                        'sale_percentage' => $sale['sale_percentage'],
                    ]);
                }
            }
    
            // Handle special reservation times
            if (isset($unitData['special_reservation_times']) && is_array($unitData['special_reservation_times'])) {
                foreach ($unitData['special_reservation_times'] as $specialTime) {
                    SpecialReservationTimes::create([
                        'unit_id' => $unit->id,
                        'from' => $specialTime['from'],
                        'to' => $specialTime['to'],
                        'price' => $specialTime['price'],
                        'min_reservation_period' => $specialTime['min_reservation_period'],
                    ]);
                }
            }
    
            // Handle images if present
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('uploads/images', 'public');
                    UnitImage::create([
                        'unit_id' => $unit->id,
                        'image' => $path,
                    ]);
                }
            }
    
            // Handle videos if present
            if ($request->hasFile('videos')) {
                foreach ($request->file('videos') as $video) {
                    $path = $video->store('uploads/videos', 'public');
                    UnitVideo::create([
                        'unit_id' => $unit->id,
                        'video' => $path,
                    ]);
                }
            }
    
            // Return success response with the created unit
            return response()->json([
                'success' => true,
                'message' => 'Unit created successfully',
                'unit' => $unit,
            ], 201);
        } catch (\Exception $e) {
            // Clean up uploaded files if creation fails
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    Storage::disk('public')->delete($image->path());
                }
            }
            if ($request->hasFile('videos')) {
                foreach ($request->file('videos') as $video) {
                    Storage::disk('public')->delete($video->path());
                }
            }
    
            // Return error response
            return response()->json([
                'success' => false,
                'message' => 'Failed to create unit',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
    public function get($id){
        $unit = Unit::find($id);
        return response()->json($unit);
    }
}
