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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UnitController extends Controller
{
    // public function create(StoreUnitRequest $request){
    //     try {
    //         $validated = $request->validated();
    //         $unitData = $validated;
    
    //         // Create the unit
    //         $unit = Unit::create($unitData);
    
    //         // Handle rooms and their amenities
    //         if (isset($unitData['rooms']) && is_array($unitData['rooms'])) {
    //             foreach ($unitData['rooms'] as $roomData) {
    //                 $room = Room::create([
    //                     'unit_id' => $unit->id,
    //                     'bed_count' => $roomData['bed_count'],
    //                     'bed_sizes' => json_encode($roomData['bed_sizes']),
    //                 ]);
                
    //                 // Attach amenities to the room using the pivot table
    //                 if (isset($roomData['amenities']) && is_array($roomData['amenities'])) {
    //                     $room->amenities()->attach($roomData['amenities']);
    //                 }
    //             }
    //         }
    
    //         // Handle available dates
    //         if (isset($unitData['available_dates']) && is_array($unitData['available_dates'])) {
    //             foreach ($unitData['available_dates'] as $date) {
    //                 AvailableDate::create([
    //                     'unit_id' => $unit->id,
    //                     'from' => $date['from'],
    //                     'to' => $date['to'],
    //                 ]);
    //             }
    //         }
    
    //         // Handle cancel policies
    //         if (isset($unitData['cancel_policies']) && is_array($unitData['cancel_policies'])) {
    //             foreach ($unitData['cancel_policies'] as $policy) {
    //                 CancelPoliciy::create([
    //                     'unit_id' => $unit->id,
    //                     'days' => $policy['days'],
    //                     'penalty' => $policy['penalty'],
    //                 ]);
    //             }
    //         }
    
    //         // Handle additional fees
    //         if (isset($unitData['additional_fees']) && is_array($unitData['additional_fees'])) {
    //             foreach ($unitData['additional_fees'] as $fee) {
    //                 AdditionalFee::create([
    //                     'unit_id' => $unit->id,
    //                     'fees' => $fee['fees'],
    //                     'amount' => $fee['amount'],
    //                 ]);
    //             }
    //         }
    
    //         // Handle long-term reservations
    //         if (isset($unitData['long_term_reservations']) && is_array($unitData['long_term_reservations'])) {
    //             foreach ($unitData['long_term_reservations'] as $reservation) {
    //                 LongTermReservations::create([
    //                     'unit_id' => $unit->id,
    //                     'more_than_days' => $reservation['more_than_days'],
    //                     'sale_percentage' => $reservation['sale_percentage'],
    //                 ]);
    //             }
    //         }
    
    //         // Handle sales
    //         if (isset($unitData['sales']) && is_array($unitData['sales'])) {
    //             foreach ($unitData['sales'] as $sale) {
    //                 Sale::create([
    //                     'unit_id' => $unit->id,
    //                     'from' => $sale['from'],
    //                     'to' => $sale['to'],
    //                     'sale_percentage' => $sale['sale_percentage'],
    //                 ]);
    //             }
    //         }
    
    //         // Handle special reservation times
    //         if (isset($unitData['special_reservation_times']) && is_array($unitData['special_reservation_times'])) {
    //             foreach ($unitData['special_reservation_times'] as $specialTime) {
    //                 SpecialReservationTimes::create([
    //                     'unit_id' => $unit->id,
    //                     'from' => $specialTime['from'],
    //                     'to' => $specialTime['to'],
    //                     'price' => $specialTime['price'],
    //                     'min_reservation_period' => $specialTime['min_reservation_period'],
    //                 ]);
    //             }
    //         }
    
    //         // Handle images if present
    //         if ($request->hasFile('images')) {
    //             foreach ($request->file('images') as $image) {
    //                 $path = $image->store('uploads/images', 'public');
    //                 UnitImage::create([
    //                     'unit_id' => $unit->id,
    //                     'image' => $path,
    //                 ]);
    //             }
    //         }
    
    //         // Handle videos if present
    //         if ($request->hasFile('videos')) {
    //             foreach ($request->file('videos') as $video) {
    //                 $path = $video->store('uploads/videos', 'public');
    //                 UnitVideo::create([
    //                     'unit_id' => $unit->id,
    //                     'video' => $path,
    //                 ]);
    //             }
    //         }
    
    //         // Return success response with the created unit
    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Unit created successfully',
    //             'unit' => $unit,
    //         ], 201);
    //     } catch (\Exception $e) {
    //         // Clean up uploaded files if creation fails
    //         if ($request->hasFile('images')) {
    //             foreach ($request->file('images') as $image) {
    //                 Storage::disk('public')->delete($image->path());
    //             }
    //         }
    //         if ($request->hasFile('videos')) {
    //             foreach ($request->file('videos') as $video) {
    //                 Storage::disk('public')->delete($video->path());
    //             }
    //         }
    
    //         // Return error response
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Failed to create unit',
    //             'error' => $e->getMessage(),
    //         ], 500);
    //     }
    // }
    
    public function get($id)
    {
        $unit = Unit::with([
            'city',
            'compound',
            'hotel',
            'type',
            'additionalFees',
            'availableDates',
            'sales',
            'cancelPolicies',
            'longTermReservations',
            'specialReservationTimes',
            'images',
            'videos',
            'rooms',
            'amenities'
        ])->find($id);
    
        if (!$unit) {
            return response()->json(['message' => 'Unit not found'], 404);
        }
    
        // Divide amenities into categories based on type
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
    
        // Add categorized amenities to the response
        $unitData = $unit->toArray();
        $unitData['unit_amenities'] = $unitAmenities;
        $unitData['reception_amenities'] = $receptionAmenities;
        $unitData['kitchen_amenities'] = $kitchenAmenities;
    
        // Remove the original amenities array
        unset($unitData['amenities']);
    
        return response()->json($unitData);
    }
    
    

    public function create(StoreUnitRequest $request)
    {
        try {
            DB::beginTransaction();
            
            $validated = $request->validated();
            $unitData = $validated;
            
            // Create the unit
            $unit = Unit::create([
                'owner_id' => $unitData['owner_id'],
                'type' => $unitData['type'],
                'unit_type_id' => $unitData['unit_type_id'],
                'city_id' => $unitData['city_id'],
                'compound_id' => $unitData['compound_id'] ?? null,
                'hotel_id' => $unitData['hotel_id'] ?? null,
                'address' => $unitData['address'] ?? null,
                'lat' => $unitData['lat'] ?? null,
                'lng' => $unitData['lng'] ?? null,
                'unit_number' => $unitData['unit_number'],
                'floors_count' => $unitData['floors_count'],
                'elevator' => $unitData['elevator'],
                'area' => $unitData['area'],
                'distance_unit_beach' => $unitData['distance_unit_beach'] ?? null,
                'beach_unit_transportation' => $unitData['beach_unit_transportation'] ?? null,
                'distance_unit_pool' => $unitData['distance_unit_pool'] ?? null,
                'pool_unit_transportation' => $unitData['pool_unit_transportation'] ?? null,
                'amenities' => json_encode($unitData['amenities'] ?? []),
                'room_count' => $unitData['room_count'],
                'toilet_count' => $unitData['toilet_count'],
                'reception' => json_encode($unitData['reception'] ?? []),
                'kitchen' => json_encode($unitData['kitchen'] ?? []),
                'description' => $unitData['description'] ?? null,
                'reservation_roles' => $unitData['reservation_roles'] ?? null,
                'reservation_type' => $unitData['reservation_type'],
                'price' => $unitData['price'],
                'insurance_amount' => $unitData['insurance_amount'],
                'max_individuals' => $unitData['max_individuals'],
                'youth_only' => $unitData['youth_only'],
                'min_reservation_days' => $unitData['min_reservation_days'] ?? null,
                'deposit' => $unitData['deposit'],
                'upon_arival_price' => $unitData['upon_arival_price'],
                'weekend_prices' => $unitData['weekend_prices'],
                'min_weekend_period' => $unitData['min_weekend_period'] ?? null,
                'weekend_price' => $unitData['weekend_price'] ?? null,
            ]);

            if (!empty($unitData['amenities'])) {
                $unit->amenities()->attach($unitData['amenities']);
            }

            // Handle rooms
            if (!empty($unitData['rooms'])) {
                foreach ($unitData['rooms'] as $roomData) {
                    $room = $unit->rooms()->create([
                        'bed_count' => $roomData['bed_count'],
                        'bed_sizes' => json_encode($roomData['bed_sizes'] ?? []),
                    ]);
                    
                    if (!empty($roomData['amenities'])) {
                        $room->amenities()->attach($roomData['amenities']);
                    }
                }
            }

            // Handle available dates
            if (!empty($unitData['available_dates'])) {
                $unit->availableDates()->createMany($unitData['available_dates']);
            }

            // Handle cancel policies
            if (!empty($unitData['cancel_policies'])) {
                $unit->cancelPolicies()->createMany($unitData['cancel_policies']);
            }

            // Handle additional fees
            if (!empty($unitData['additional_fees'])) {
                $unit->additionalFees()->createMany($unitData['additional_fees']);
            }

            // Handle long term reservations
            if (!empty($unitData['long_term_reservations'])) {
                $unit->longTermReservations()->createMany($unitData['long_term_reservations']);
            }

            // Handle sales
            if (!empty($unitData['sales'])) {
                $unit->sales()->createMany($unitData['sales']);
            }

            // Handle special reservation times
            if (!empty($unitData['special_reservation_times'])) {
                $unit->specialReservationTimes()->createMany($unitData['special_reservation_times']);
            }

            // Handle images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('uploads/units/images', 'public');
                    $unit->images()->create(['image' => $path]);
                }
            }

            // Handle videos
            if ($request->hasFile('videos')) {
                foreach ($request->file('videos') as $video) {
                    $path = $video->store('uploads/units/videos', 'public');
                    $unit->videos()->create(['video' => $path]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Unit created successfully',
                'data' => $unit->load([
                    'rooms.amenities', 
                    'availableDates', 
                    'cancelPolicies',
                    'additionalFees',
                    'longTermReservations',
                    'sales',
                    'specialReservationTimes',
                    'images',
                    'videos'
                ])
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Clean up any uploaded files
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

            return response()->json([
                'success' => false,
                'message' => 'Failed to create unit',
                'error' => $e->getMessage(),
                // 'trace' => config('app.debug') ? $e->getTrace() : null
            ], 500);
        }
    }
}
