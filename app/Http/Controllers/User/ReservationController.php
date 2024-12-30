<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\AvailableDate;
use App\Models\Reservation;
use App\Models\ReservationId;
use App\Models\Unit;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function reserve(Request $request){
        try{
            //Inputs Validation
            $request->validate([
                "unit_id" => "required|exists:units,id",
                "date_from" => "required|date",
                "date_to" => "required|date",
                "adults_count" => "required|integer|min:1",
                "children_count" => "nullable|integer|min:0",
                "ids.*" => "required|image|max:2048"
            ]);
            $user = $request->user();
            // Get Unit
            $unit = Unit::find($request->unit_id);
            // Units Data to Check
            $unitSales = $unit->sales()->get();
            $unitSpecialPrices = $unit->specialReservationTimes()->get();
            $unitLongTermReservations = $unit->longTermReservations()->get();
        
            // Calculate Days Count
            $dateFrom = Carbon::parse($request->date_from);
            $dateTo = Carbon::parse($request->date_to);
            $daysCount = $dateFrom->diffInDays($dateTo) + 1; // Include the start day
        
            //Check on Availabe Dates
            $isAvailable = AvailableDate::where('unit_id', $request->unit_id)
                ->whereDate('from', '<=', $dateFrom)
                ->whereDate('to', '>=', $dateTo)
                ->exists();
        
            if (!$isAvailable) {
                return response()->json(['message' => 'الفترة المحددة غير متاحة للحجز'], 400);
            }
            //Check on Current reservations on Unit
            $reservations = Reservation::where('unit_id', $unit->id)
                ->whereDate('date_from', '<=', $dateFrom)
                ->whereDate('date_to', '>=', $dateTo)
                ->exists();
        
            if ($reservations) {
                return response()->json(['message' => 'الفترة المحددة غير متاحة للحجز'], 400);
            }
            //Check on Min Reservation Days
            if($daysCount < $unit->min_reservation_days){
                return response()->json([
                    "success" => false,
                    "message" => "يجب الا تقل مدة الحجز عن " . $unit->min_reservation_days . " ايام"
                ], 400);
            }
            //Set the defaults
            $salePercentage = 0;
            $price = $unit->price;
            //default reservation status depending on type
            $status = $unit->reservation_type == "direct" ? "accepted" : "pending";
            //Check on Sales
            if($unitSales->count() > 0){
                foreach($unitSales as $sale){
                    if($sale->from <= $dateFrom && $sale->to >= $dateTo){
                        $salePercentage += $sale->sale_percentage;
                    }
                }
            }
        
            if($unitLongTermReservations->count() > 0){
                foreach($unitLongTermReservations as $unitLongTermReservation){
                    if($daysCount >= $unitLongTermReservation->more_than_days){
                        $salePercentage += $unitLongTermReservation->sale_percentage;
                    }
                }
            }
            if($unit->weekend_price){
            // Check if the reservation includes Friday or Saturday
            $friday = Carbon::FRIDAY; 
            $saturday = Carbon::SATURDAY;
            
            if (($dateFrom->dayOfWeek === $friday || $dateFrom->dayOfWeek === $saturday) && 
                ($dateTo->dayOfWeek === $friday || $dateTo->dayOfWeek === $saturday) && $daysCount <= 2)
                {
                    if($daysCount < $unit->min_weekend_period){
                        return response()->json([
                            "success" => false,
                            "message" => "يجب الا تقل مدة الحجز عن " . $unit->min_weekend_period . " ايام"
                        ], 400);
                    }
                    $price = $unit->weekend_price;
                }
            }
            if($unitSpecialPrices->count() > 0){
                foreach($unitSpecialPrices as $unitSpecialPrice){
                    if($unitSpecialPrice->from <= $dateFrom && $unitSpecialPrice->to >= $dateTo){
                        if($daysCount < $unitSpecialPrice->min_reservation_period){
                            // Reservation Period in special times
                            return response()->json([
                                "success" => false,
                                "message" => "يجب الا تقل مدة الحجز عن " . $unitSpecialPrice->min_reservation_period . " ايام"
                            ], 400);
                        }
                        $price = $unitSpecialPrice->price;
                    }
                }
            }
        
            if($salePercentage > 0){
                $saleAmount = ($price * $salePercentage) / 100;
                $price -= $saleAmount;
            }
            $bookAdvance = ($price * $unit->deposit) / 100;
            $reservation = Reservation::create([
                "user_id" => $user->id,
                "unit_id" => $request->unit_id,
                "date_from" => $request->date_from,
                "date_to"=> $request->date_to,
                "adults_count" => $request->adults_count,
                "children_count" => $request->children_count ?? null,
                "book_advance" => $bookAdvance,
                "booking_price" => $price, 
                "status" => $status,
            ]);
            if ($request->hasFile('ids')) {
                foreach ($request->file('ids') as $image) {
                    // Save each image
                    $path = $image->store('reservation_ids', 'public'); // Store in 'storage/app/public/reservation_ids'
                
                    // Save the path in the database
                    ReservationId::create([
                        "reservation_id" => $reservation->id,
                        "path" => $path
                    ]);
                }
            }
            return response()->json([
                "success" => true,
                "message" => "تم الحجز بنجاح",
                "reservation" => $reservation,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "حدث خطاء في الخادم",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    public function getAll() {
        $user = auth()->user();
        $reservations = Reservation::where('user_id', $user->id)
            ->with('unit.images')
            ->get();
        // Calculate Days Count
        foreach ($reservations as $reservation) {        
            $dateFrom = Carbon::parse($reservation->date_from);
            $dateTo = Carbon::parse($reservation->date_to);
            $reservation->days_count = $dateFrom->diffInDays($dateTo) + 1; // Include the start day
        }
        return response()->json([
            "success" => true,
            "reservations" => $reservations
        ], 200);
    }

    public function get($id) {
        $user = auth()->user();
        $reservation = Reservation::where('id', $id)
            ->where('user_id', $user->id)
            ->with('ids','unit.rooms', 'unit.images')
            ->first();
        // Calculate Days Count
        $dateFrom = Carbon::parse($reservation->date_from);
        $dateTo = Carbon::parse($reservation->date_to);
        $reservation->days_count = $dateFrom->diffInDays($dateTo) + 1; // Include the start day
        return response()->json([
            "success" => true,
            "reservation" => $reservation
        ], 200);
    }

    public function cancel($id) {
        $user = auth()->user();
        $reservation = Reservation::where('id', $id)
            ->where('user_id', $user->id)
            ->first();
        $reservation->status = "canceled_user";
        $reservation->save();
        return response()->json([
            "success" => true,
            "message" => "تم الغاء الحجز بنجاح"
        ], 200);
    }
}
