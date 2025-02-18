<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\AvailableDate;
use App\Models\Profit;
use App\Models\Promocode;
use App\Models\Reservation;
use App\Models\ReservationId;
use App\Models\Transaction;
use App\Models\Unit;
use App\Traits\PayTabsPayment;
use App\Traits\PushNotificationTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class ReservationController extends Controller
{
    use PushNotificationTrait, PayTabsPayment;

    public function calculatePrice(Request $request)
{
    try {
        // Inputs Validation
        $request->validate([
            "unit_id" => "required|exists:units,id",
            "date_from" => "required|date",
            "date_to" => "required|date|after_or_equal:date_from",
            "promocode" => "nullable|exists:promocodes,promocode",

        ]);

        // Get Unit
        $unit = Unit::findOrFail($request->unit_id);

        // Calculate Days Count
        $dateFrom = Carbon::parse($request->date_from);
        $dateTo = Carbon::parse($request->date_to);
        $daysCount = $dateFrom->diffInDays($dateTo) + 1;

        // Check Availability
        $isAvailable = AvailableDate::where('unit_id', $unit->id)
            ->whereDate('from', '<=', $dateFrom)
            ->whereDate('to', '>=', $dateTo)
            ->exists();

        if ($isAvailable) {
            return response()->json(['message' => 'الفترة المحددة غير متاحة للحجز'], 400);
        }

        // Check Existing Reservations
        $reservations = Reservation::where('unit_id', $unit->id)
            ->whereNotIn('status', ['canceled_user', 'canceled_owner'])
            ->whereDate('date_from', '<=', $dateFrom)
            ->whereDate('date_to', '>=', $dateTo)
            ->exists();

        if ($reservations) {
            return response()->json(['message' => 'الفترة المحددة غير متاحة للحجز'], 400);
        }

        // Check Minimum Reservation Days
        if ($daysCount < $unit->min_reservation_days) {
            return response()->json([
                "success" => false,
                "message" => "يجب الا تقل مدة الحجز عن " . $unit->min_reservation_days . " ايام"
            ], 400);
        }

        // Initialize Price
        $salePercentage = 0;
        $price = $unit->price;

        // Apply Sales
        foreach ($unit->sales as $sale) {
            if ($sale->from <= $dateFrom && $sale->to >= $dateTo) {
                $salePercentage += $sale->sale_percentage;
            }
        }

        // Apply Long-Term Discounts
        foreach ($unit->longTermReservations as $longTerm) {
            if ($daysCount >= $longTerm->more_than_days) {
                $salePercentage += $longTerm->sale_percentage;
            }
        }

        // Weekend Pricing
        if ($unit->weekend_price) {
            $friday = Carbon::FRIDAY;
            $saturday = Carbon::SATURDAY;

            if (($dateFrom->dayOfWeek === $friday || $dateFrom->dayOfWeek === $saturday) &&
                ($dateTo->dayOfWeek === $friday || $dateTo->dayOfWeek === $saturday) && $daysCount <= 2
            ) {
                if ($daysCount < $unit->min_weekend_period) {
                    return response()->json([
                        "success" => false,
                        "message" => "يجب الا تقل مدة الحجز عن " . $unit->min_weekend_period . " ايام"
                    ], 400);
                }
                $price = $unit->weekend_price;
            }
        }

        // Special Prices
        foreach ($unit->specialReservationTimes as $specialPrice) {
            if ($specialPrice->from <= $dateFrom && $specialPrice->to >= $dateTo) {
                if ($daysCount < $specialPrice->min_reservation_period) {
                    return response()->json([
                        "success" => false,
                        "message" => "يجب الا تقل مدة الحجز عن " . $specialPrice->min_reservation_period . " ايام"
                    ], 400);
                }
                $price = $specialPrice->price;
            }
        }

        // Additional Fees
        foreach ($unit->additionalFees as $fee) {
            $price += $fee->amount;
        }

        // Apply Discounts
        if ($salePercentage > 0) {
            $saleAmount = ($price * $salePercentage) / 100;
            $price -= $saleAmount;
        }


        //App Profit
        $appProfit = Profit::where("type", $unit->type)
        ->where("from", "<=", $price)
        ->where("to", ">=", $price)
        ->latest()
        ->first();
                    

        if (!$appProfit) {
            // Try to get the nearest lower range
            $appProfit = Profit::where("type", $unit->type)
                ->where("to", "<=", $price)
                ->latest()
                ->first();
        }
            
        // If no profit entry is found, set the profit amount to 0
        $totalPrice = $price * $daysCount;
        if ($request->promocode){
            $promocode = Promocode::where('promocode', $request->promocode)->first();
            if ($promocode->percentage > 0) {
                $totalPrice -= ($totalPrice * $promocode->percentage) / 100;
            } elseif($promocode->amount_total > 0) {
                $totalPrice -= $promocode->amount_total;
            } elseif($promocode->amount_night > 0) {
                $price -= $promocode->amount_night;
                $totalPrice = $price * $daysCount;
            }
        }
        $appProfitAmount = $appProfit ? ($totalPrice * ($appProfit->percentage / 100)) : 0;
        // Deposit Calculation
        $bookAdvance = ($totalPrice * $unit->deposit) / 100;
        return response()->json([
            "success" => true,
            "message" => "تم حساب السعر بنجاح",
            "price" => $price,
            "book_advance" => $bookAdvance,
            "owner_profit" => $totalPrice - $appProfitAmount,
            "app_profit" => $appProfitAmount,
        ]);
    } catch (\Exception $e) {
        return response()->json([
            "success" => false,
            "message" => "حدث خطاء في الخادم",
            "error" => $e->getMessage()
        ], 500);
    }
}


    public function reserve(Request $request)
    {
        try {
            //Inputs Validation
            $request->validate([
                "unit_id" => "required|exists:units,id",
                "date_from" => "required|date",
                "date_to" => "required|date",
                "adults_count" => "required|integer|min:1",
                "children_count" => "nullable|integer|min:0",
                "promocode" => "nullable|exists:promocodes,promocode",
                "ids.*" => "nullable|image|max:10284"
            ]);
            $user = $request->user();
            // Get Unit
            $unit = Unit::find($request->unit_id);

            // Units Data to Check
            $unitSales = $unit->sales()->get();
            $unitSpecialPrices = $unit->specialReservationTimes()->get();
            $unitLongTermReservations = $unit->longTermReservations()->get();
            $unitAdditionalFees = $unit->additionalFees()->get();

            // Calculate Days Count
            $dateFrom = Carbon::parse($request->date_from);
            $dateTo = Carbon::parse($request->date_to);
            $daysCount = $dateFrom->diffInDays($dateTo) + 1; // Include the start day

            //Check on Availabe Dates
            $isAvailable = AvailableDate::where('unit_id', $request->unit_id)
                ->whereDate('from', '<=', $dateFrom)
                ->whereDate('to', '>=', $dateTo)
                ->exists();

            if ($isAvailable) {
                return response()->json(['message' => 'الفترة المحددة غير متاحة للحجز'], 400);
            }
            //Check on Current reservations on Unit
            $reservations = Reservation::where('unit_id', $unit->id)
                ->whereNotIn('status', ['canceled_user', 'canceled_owner'])
                ->whereDate('date_from', '<=', $dateFrom)
                ->whereDate('date_to', '>=', $dateTo)
                ->exists();

            if ($reservations) {
                return response()->json(['message' => 'الفترة المحددة غير متاحة للحجز'], 400);
            }
            //Check on Min Reservation Days
            if ($daysCount < $unit->min_reservation_days) {
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
            if ($unitSales->count() > 0) {
                foreach ($unitSales as $sale) {
                    if ($sale->from <= $dateFrom && $sale->to >= $dateTo) {
                        $salePercentage += $sale->sale_percentage;
                    }
                }
            }

            if ($unitLongTermReservations->count() > 0) {
                foreach ($unitLongTermReservations as $unitLongTermReservation) {
                    if ($daysCount >= $unitLongTermReservation->more_than_days) {
                        $salePercentage += $unitLongTermReservation->sale_percentage;
                    }
                }
            }
            if ($unit->weekend_price) {
                // Check if the reservation includes Friday or Saturday
                $friday = Carbon::FRIDAY;
                $saturday = Carbon::SATURDAY;

                if (($dateFrom->dayOfWeek === $friday || $dateFrom->dayOfWeek === $saturday) &&
                    ($dateTo->dayOfWeek === $friday || $dateTo->dayOfWeek === $saturday) && $daysCount <= 2
                ) {
                    if ($daysCount < $unit->min_weekend_period) {
                        return response()->json([
                            "success" => false,
                            "message" => "يجب الا تقل مدة الحجز عن " . $unit->min_weekend_period . " ايام"
                        ], 400);
                    }
                    $price = $unit->weekend_price;
                }
            }
            if ($unitSpecialPrices->count() > 0) {
                foreach ($unitSpecialPrices as $unitSpecialPrice) {
                    if ($unitSpecialPrice->from <= $dateFrom && $unitSpecialPrice->to >= $dateTo) {
                        if ($daysCount < $unitSpecialPrice->min_reservation_period) {
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
            // Check on additional fees
            if ($unitAdditionalFees->count() > 0) {
                foreach ($unitAdditionalFees as $unitAdditionalFee) {
                    $price += $unitAdditionalFee->amount;
                }
            }
            // Calculate the total price
            if ($salePercentage > 0) {
                $saleAmount = ($price * $salePercentage) / 100;
                $price -= $saleAmount;
            }

            //App Profit
            $appProfit = Profit::where("type", $unit->type)
            ->where("from", "<=", $price)
            ->where("to", ">=", $price)
            ->latest()
            ->first();
            
            if (!$appProfit) {
                // Try to get the nearest lower range
                $appProfit = Profit::where("type", $unit->type)
                    ->where("to", "<=", $price)
                    ->latest()
                    ->first();
            }
                
             // If no profit entry is found, set the profit amount to 0
             $totalPrice = $price * $daysCount;
             if ($request->promocode){
                $promocode = Promocode::where('promocode', $request->promocode)->first();
                if ($promocode->percentage > 0) {
                    $totalPrice -= ($totalPrice * $promocode->percentage) / 100;
                } elseif($promocode->amount_total > 0) {
                    $totalPrice -= $promocode->amount_total;
                } elseif($promocode->amount_night > 0) {
                    $price -= $promocode->amount_night;
                    $totalPrice = $price * $daysCount;
                }
            }
            $appProfitAmount = $appProfit ? ($totalPrice * ($appProfit->percentage / 100)) : 0;
            $bookAdvance = ($totalPrice * $unit->deposit) / 100;

            $reservation = Reservation::create([
                "user_id" => $user->id,
                "unit_id" => $request->unit_id,
                "date_from" => $request->date_from,
                "date_to" => $request->date_to,
                "adults_count" => $request->adults_count,
                "children_count" => $request->children_count ?? null,
                "book_advance" => $bookAdvance,
                "booking_price" => $totalPrice,
                "owner_profit" => $totalPrice - $appProfitAmount,
                "app_profit" => $appProfitAmount,
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
            //Create a transaction for the reservation process
            $owner = $unit->owner;
            $transaction = Transaction::create([
                "sender_id" => $user->id,
                "receiver_id" => $owner->id,
                "amount" => $reservation->book_advance,
                "type" => "booking",
                "created_at" => now()
            ]);
            //Update Reservation transaction id
            $reservation->transaction_id = $transaction->id;
            $reservation->save();

            if($unit->reservation_type == "direct"){
                $paymentUrl = $this->createPayTabsPayment($transaction->amount, $transaction->id);
            } else {
            $this->pushNotification(
                'تم حجز الوحدة',
                "تم حجز الوحدة من فضلك ارفق اثباتات الهوية",
                $user->id,
                "upload_ids",
                $reservation->id
            );
            }
            //Payment Process
            return response()->json([
                "success" => true,
                "message" => "تم الحجز بنجاح",
                "payment_url" => $paymentUrl['data']['redirect_url'] ?? null,
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


    public function uploadIds(Request $request){
        try{
        $validator = Validator::make($request->all(), [
            "ids.*" => "required|image|max:10284",
            "reservation_id" => "required|exists:reservations,id"
        ]);

        if($validator->fails()){
            return response()->json([
                "success" => false,
                "message" => $validator->errors()->first()
            ], 422);
        }

        $reservation = Reservation::find($request->reservation_id);
        foreach ($request->file('ids') as $image) {
            // Save each image
            $path = $image->store('reservation_ids', 'public'); // Store in 'storage/app/public/reservation_ids'

            // Save the path in the database
            ReservationId::create([
                "reservation_id" => $reservation->id,
                "path" => $path
            ]);
        }
        return response()->json([
            "success" => true,
            "message" => "تم رفع اثباتات الهوية بنجاح"
        ]);
    } catch (\Exception $e) {
        return response()->json([
            "success" => false,
            "message" => "حدث خطاء في الخادم",
            "error" => $e->getMessage()
        ], 500);
    }
    }

    public function getAll()
    {
        $user = auth()->user();
        $reservations = Reservation::where('user_id', $user->id)
            ->with('unit.images')
            ->where('paid', 1)
            ->get();

        return response()->json([
            "success" => true,
            "reservations" => $reservations
        ], 200);
    }

    public function get($id)
    {
        $user = auth()->user();
        $reservation = Reservation::where('id', $id)
            ->where('user_id', $user->id)
            ->with('ids', 'unit.rooms', 'unit.images', 'user', 'unit.owner')
            ->first();
        if (!$reservation) {
            return response()->json([
                "success" => false,
                "message" => "الحجز غير موجود"
            ], 404);
        }

        return response()->json([
            "success" => true,
            "reservation" => $reservation
        ], 200);
    }



    public function cancel($id)
    {
        $user = auth()->user();

        // Retrieve the reservation
        $reservation = Reservation::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$reservation) {
            return response()->json([
                "success" => false,
                "message" => "الحجز غير موجود"
            ], 404);
        }
        $owner = $reservation->unit->owner;
        // Check if the reservation already has a status of canceled
        if ($reservation->status === 'canceled_user' || $reservation->status === 'canceled_owner') {
            return response()->json([
                "success" => false,
                "message" => "تم الغاء الحجز مسبقًا"
            ], 400);
        }

        // Retrieve the unit and its cancellation policies
        $unit = $reservation->unit; // Ensure the Reservation model has a `unit` relationship
        $cancelPolicies = $unit->cancelPolicies()
            ->orderBy('days', 'asc') // Sort by days in descending order
            ->get();
        if($reservation->paid == 1) {
            if ($cancelPolicies->count()) {
            // Calculate days before the reservation starts
            $daysBeforeReservation = now()->diffInDays($reservation->date_from, false);
            $penaltyPercentage = 0; // Default penalty percentage

            // Determine the penalty based on cancellation policies
            foreach ($cancelPolicies as $policy) {
                if ($daysBeforeReservation >= $policy->days) {
                    $penaltyPercentage = $policy->penalty;
                    break;
                }
            }
            // Calculate the penalty amount
            $penaltyAmount = ($reservation->book_advance * $penaltyPercentage) / 100;

            $user->balance += $reservation->book_advance - $penaltyAmount;
            $user->save();

            $this->pushNotification(
                'تم إلغاء الحجز واسترجاع المقدم',
                "لقد تم إلغاء حجزك {$unit->name} بنجاح، وتم استرجاع مبلغ المقدم إلى حسابك.",
                $user->id,
            );

            $owner->balance -= $reservation->book_advance - $penaltyAmount;
            $owner->save();

            $this->pushNotification(
                ' تم إلغاء الحجز وخصم المقدم',
                "نود إعلامك بأن حجز {$user->name} قد تم إلغاؤه من قِبل المستخدم. تم خصم مبلغ المقدم وفقًا لسياسة الإلغاء.",
                $owner->id,
            );

            $transaction = Transaction::create([
                "sender_id" => $owner->id,
                "receiver_id" => $user->id,
                "amount" => $reservation->book_advance - $penaltyAmount,
                "status" => "completed",
                "type" => "cancel_booking",
                "created_at" => now()
            ]);
            //transaction and notification
            } else {
                $user->balance += $reservation->book_advance;
                $user->save();

                $this->pushNotification(
                    'تم إلغاء الحجز واسترجاع المقدم',
                    "لقد تم إلغاء حجزك {$unit->name} بنجاح، وتم استرجاع مبلغ المقدم إلى حسابك.",
                    $user->id,
                );


                $owner->balance -= $reservation->book_advance;
                $owner->save();

                $this->pushNotification(
                    ' تم إلغاء الحجز وخصم المقدم',
                    "نود إعلامك بأن حجز {$user->name} قد تم إلغاؤه من قِبل المستخدم. تم خصم مبلغ المقدم وفقًا لسياسة الإلغاء.",
                    $owner->id,
                );

                $transaction = Transaction::create([
                    "sender_id" => $owner->id,
                    "receiver_id" => $user->id,
                    "amount" => $reservation->book_advance,
                    "status" => "completed",
                    "type" => "cancel_booking",
                    "created_at" => now()
                ]);
                //transaction and notification
            }
        }   
        // Update the reservation's status and cancellation details
        $reservation->status = 'canceled_user';
        $reservation->cancelled_at = now();
        $reservation->save();
        $penaltyResponse = $reservation->paid == 1 ? $penaltyAmount : null;
        $refundResponse = $reservation->paid == 1 ? $reservation->book_advance - $penaltyAmount : null;
        return response()->json([
            "success" => true,
            "message" => "تم الغاء الحجز بنجاح",
            "penalty" => $penaltyResponse,
            "refunded_amount" => $refundResponse,
        ], 200);
    }
}
