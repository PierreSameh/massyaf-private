<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUnitRequest;
use App\Models\AdditionalFee;
use App\Models\Amenitie;
use App\Models\AvailableDate;
use App\Models\CancelPoliciy;
use App\Models\LongTermReservations;
use App\Models\Profit;
use App\Models\Room;
use App\Models\Sale;
use App\Models\SpecialReservationTimes;
use App\Models\Unit;
use App\Models\UnitImage;
use App\Models\UnitVideo;
use App\Services\CodeGeneratorService;
use App\Traits\PushNotificationTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UnitController extends Controller
{
    use PushNotificationTrait;

    protected $codeGeneratorService;

    public function __construct(CodeGeneratorService $codeGeneratorService)
    {
        $this->codeGeneratorService = $codeGeneratorService;
    }

    public function getAll(Request $request)
    {
        $owner = $request->user();
        $units = Unit::with([
            'city',
            'compound',
            'hotel',
            'unitType',
            'additionalFees',
            'availableDates',
            'sales',
            'cancelPolicies',
            'longTermReservations',
            'specialReservationTimes',
            'images',
            'videos',
            'rooms.amenities',
            'amenities',
            'reservations'
        ])->where('owner_id', $owner->id)
            ->latest()
            ->get();

        return response()->json([
            "success" => true,
            "units" => $units
        ], 200);
    }
    public function getPaginate(Request $request)
    {
        $owner = $request->user();
        $units = Unit::with([
            'city',
            'compound',
            'hotel',
            'unitType',
            'additionalFees',
            'availableDates',
            'sales',
            'cancelPolicies',
            'longTermReservations',
            'specialReservationTimes',
            'images',
            'videos',
            'rooms.amenities',
            'amenities',
            'reservations'
        ])->where('owner_id', $owner->id)->paginate((int) $request->per_page ?: 10);

        return response()->json([
            "success" => true,
            "units" => $units
        ], 200);
    }

    public function get($id)
    {
        $unit = Unit::with([
            'city',
            'compound',
            'hotel',
            'unitType',
            'additionalFees',
            'availableDates',
            'sales',
            'cancelPolicies',
            'longTermReservations',
            'specialReservationTimes',
            'images',
            'videos',
            'rooms.amenities',
            'amenities',
            'reservations'
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
                case 'hotel':
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

    public function calculateAppProfit(Request $request)
    {
        try {
            $request->validate([
                "price" => "required|numeric",
                "unit_type" => "required|in:unit,hotel"
            ]);

            $appProfit = Profit::where("type", $request->unit_type)
                ->where("from", "<=", $request->price)
                ->where("to", ">=", $request->price)
                ->latest()
                ->first();


            if (!$appProfit) {
                // Try to get the nearest lower range
                $appProfit = Profit::where("type", $request->unit_type)
                    ->where("to", "<=", $request->price)
                    ->latest()
                    ->first();
            }
            $appProfitAmount = $appProfit ? ($request->price * ($appProfit->percentage / 100)) : 0;

            return response()->json([
                "success" => true,
                "app_profit" => $appProfitAmount
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "حدث خطأ في الخادم",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    public function create(StoreUnitRequest $request)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validated();
            $unitData = $validated;
            $owner = $request->user();
            //generate unique code for units
            $parentId = isset($unitData['compound_id']) ?: $unitData['hotel_id'];
            $code = $this->codeGeneratorService->unit(
                $unitData['type'],
                $parentId,
                $unitData['room_count'] ?: null,
                $unitData['floors_count'] ?: null
            );

            // Create the unit
            $unit = Unit::create([
                'code' => $code,
                'owner_id' => $owner->id,
                'type' => $unitData['type'],
                'name' => $unitData['name'],
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
                'room_count' => $unitData['room_count'],
                'toilet_count' => $unitData['toilet_count'],
                'description' => $unitData['description'] ?? null,
                'reservation_roles' => $unitData['reservation_roles'] ?? null,
                'reservation_type' => $unitData['reservation_type'],
                'price' => $unitData['price'],
                'insurance_amount' => $unitData['insurance_amount'],
                'max_individuals' => $unitData['max_individuals'],
                'youth_only' => $unitData['youth_only'],
                'pets_allowed' => $unitData['pets_allowed'],
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
            if (!empty($unitData['reception'])) {
                $unit->amenities()->attach($unitData['reception']);
            }
            if (!empty($unitData['kitchen'])) {
                $unit->amenities()->attach($unitData['kitchen']);
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
                $this->pushNotification(
                    '🎉 عرض خاص بانتظارك!',
                    "لقد تم إضافة خصم جديد على {$unit->name} الذي تحبه! لا تفوّت الفرصة واستفد من الخصم الآن. العرض لفترة محدودة فقط!"
                );
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
            \Log::error("Error creating unit: " . $e->getMessage() . "request: " . json_encode($request->all()));
            return response()->json([
                'success' => false,
                'message' => 'Failed to create unit',
                'error' => $e->getMessage(),
                // 'trace' => config('app.debug') ? $e->getTrace() : null
            ], 500);
        }
    }

    public function update(StoreUnitRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $unit = Unit::find($id);

            if (!$unit) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unit not found',
                ], 404);
            }
            $owner = $request->user();
            if ($unit->owner_id != $owner->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized to update this unit'
                ], 403);
            }
            $validated = $request->validated();
            $unitData = $validated;

            // Update the unit
            $unit->update([
                'type' => $unitData['type'],
                'name' => $unitData['name'],
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
                'pets_allowed' => $unitData['pets_allowed'],
                'min_reservation_days' => $unitData['min_reservation_days'] ?? null,
                'deposit' => $unitData['deposit'],
                'upon_arival_price' => $unitData['upon_arival_price'],
                'weekend_prices' => $unitData['weekend_prices'],
                'min_weekend_period' => $unitData['min_weekend_period'] ?? null,
                'weekend_price' => $unitData['weekend_price'] ?? null,
            ]);

            // Update amenities
            if (isset($unitData['amenities']) || isset($unitData['reception']) || isset($unitData['kitchen'])) {
                $allAmenities = array_merge(
                    $unitData['amenities'] ?? [],
                    $unitData['reception'] ?? [],
                    $unitData['kitchen'] ?? []
                );

                $validAmenities = Amenitie::whereIn('id', $allAmenities)->pluck('id')->toArray();
                $invalidAmenities = array_diff($allAmenities, $validAmenities);

                if (!empty($invalidAmenities)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Some amenities are invalid: ' . implode(', ', $invalidAmenities),
                    ], 400);
                }

                $unit->amenities()->sync($validAmenities);
            }


            // Update rooms
            if (isset($unitData['rooms'])) {
                // Delete existing rooms and their relationships
                $unit->rooms()->each(function ($room) {
                    $room->amenities()->detach();
                    $room->delete();
                });

                // Create new rooms
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

            // Update available dates
            if (isset($unitData['available_dates'])) {
                $unit->availableDates()->delete();
                $unit->availableDates()->createMany($unitData['available_dates']);
            }

            // Update cancel policies
            if (isset($unitData['cancel_policies'])) {
                $unit->cancelPolicies()->delete();
                $unit->cancelPolicies()->createMany($unitData['cancel_policies']);
            }

            // Update additional fees
            if (isset($unitData['additional_fees'])) {
                $unit->additionalFees()->delete();
                $unit->additionalFees()->createMany($unitData['additional_fees']);
            }

            // Update long term reservations
            if (isset($unitData['long_term_reservations'])) {
                $unit->longTermReservations()->delete();
                $unit->longTermReservations()->createMany($unitData['long_term_reservations']);
            }

            //Remove Sales
            if (!isset($unitData['sales'])) {
                $unit->sales()->delete();
            }
            // Update sales
            if (isset($unitData['sales'])) {
                $unit->sales()->delete();
                $unit->sales()->createMany($unitData['sales']);
            }

            // Update special reservation times
            if (isset($unitData['special_reservation_times'])) {
                $unit->specialReservationTimes()->delete();
                $unit->specialReservationTimes()->createMany($unitData['special_reservation_times']);
            }

            // Handle images
            if ($request->hasFile('images')) {
                // Delete existing images
                foreach ($unit->images as $image) {
                    Storage::disk('public')->delete($image->image);
                    $image->delete();
                }

                // Upload new images
                foreach ($request->file('images') as $image) {
                    $path = $image->store('uploads/units/images', 'public');
                    $unit->images()->create(['image' => $path]);
                }
            }

            // Handle videos
            if ($request->hasFile('videos')) {
                // Delete existing videos
                foreach ($unit->videos as $video) {
                    Storage::disk('public')->delete($video->video);
                    $video->delete();
                }

                // Upload new videos
                foreach ($request->file('videos') as $video) {
                    $path = $video->store('uploads/units/videos', 'public');
                    $unit->videos()->create(['video' => $path]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Unit updated successfully',
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
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            // Clean up any newly uploaded files
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
            \Log::error("Error updating unit: " . $e->getMessage() . "request: " . json_encode($request->all()));

            return response()->json([
                'success' => false,
                'message' => 'Failed to update unit',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function addUnavailableDates(Request $request, $unitId)
    {
        try {
            $validated = $request->validate([
                'unavailable_dates' => 'required|array',
                'unavailable_dates.*.from' => ['required', 'date'],
                'unavailable_dates.*.to' => ['required', 'date', 'after_or_equal:available_dates.*.from'],
            ]);

            $owner = auth()->user();
            $unit = Unit::find($unitId);
            if (!$unit) {
                return response()->json([
                    "success" => false,
                    "message" => "Unit not found"
                ], 404);
            }
            if ($unit->owner_id != $owner->id) {
                return response()->json([
                    "success" => false,
                    "message" => "You are not the owner of this unit"
                ], 401);
            }

            $unit->availableDates()->createMany($validated['unavailable_dates']);

            return response()->json([
                "success" => true,
                "message" => "Unavailable dates added successfully"
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "حدث خطأ في الخادم",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $unit = Unit::find($id);

            if (!$unit) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unit not found',
                ], 404);
            }

            // Delete related images and videos
            foreach ($unit->images as $image) {
                Storage::disk('public')->delete($image->image);
                $image->delete();
            }

            foreach ($unit->videos as $video) {
                Storage::disk('public')->delete($video->video);
                $video->delete();
            }

            // Delete unit
            $unit->delete();

            return response()->json([
                'success' => true,
                'message' => 'Unit deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete unit',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
