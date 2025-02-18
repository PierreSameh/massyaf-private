<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Transaction;
use App\Models\User;
use App\Services\CodeGeneratorService;
use App\Traits\PushNotificationTrait;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReservationController extends Controller
{
    use PushNotificationTrait;
    protected $codeGeneratorService;

    public function __construct(CodeGeneratorService $codeGeneratorService)
    {
        $this->codeGeneratorService = $codeGeneratorService;
    }
    public function getAll()
    {
        $user = auth()->user();
        $reservations = Reservation::whereRelation("unit", "owner_id", "=", $user->id)
            ->with('unit.images', 'unit.rooms')
            ->whereNotIn('status', ['canceled_user', 'canceled_owner'])
            // ->where('paid', 1)
            ->latest()
            ->get();

        return response()->json([
            "success" => true,
            "data" => [
                "reservations" => $reservations,
                "count" => count($reservations)
            ]
        ], 200);
    }

    public function get($id)
    {
        $user = auth()->user();
        $reservation = Reservation::where('id', $id)
            ->whereRelation("unit", "owner_id", "=", $user->id)
            ->with('unit.images', 'unit.rooms', 'user')
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
        $reservation = Reservation::with('unit')->where('id', $id)
            ->whereRelation("unit", "owner_id", "=", $user->id)
            ->whereNotIn('status', ['canceled_user', 'canceled_owner'])
            ->where('paid', 1)
            ->first();

        if (!$reservation) {
            return response()->json([
                "success" => false,
                "message" => "الحجز غير موجود"
            ], 404);
        }
        $reservation->status = "canceled_owner";
        $reservation->cancelled_at = now();
        $reservation->save();
        $unit = $reservation->unit; // Ensure the Reservation model has a `unit` relationship
        //Take the book advance from the owner
        $user->balance -= $reservation->book_advance;
        $user->save();

        //Return money to customer
        $customer = User::where('id', $reservation->user_id)->first();
        $customer->balance += $reservation->book_advance;
        $customer->save();
        $transaction = Transaction::create([
            "sender_id" => $user->id,
            "receiver_id" => $customer->id,
            "amount" => $reservation->book_advance,
            "status" => "completed",
            "type" => "cancel_booking",
            "created_at" => now()
        ]);

        // notify customer
        $this->pushNotification(
            'تم إلغاء الحجز من قِبل المالك',
            "نأسف لإبلاغك أن حجزك {$unit->name} قد تم إلغاؤه من قِبل المالك.",
            $customer->id,
        );

        /*
            Should create transactions and notification
        */
        return response()->json([
            "success" => true,
            "message" => "تم الغاء الحجز بنجاح"
        ], 200);
    }
    public function accept($id)
    {
        try{
        $user = auth()->user();
        $reservation = Reservation::where('id', $id)
            ->where('status', 'pending')
            ->whereRelation("unit", "owner_id", "=", $user->id)
            // ->where('paid', 1)
            ->first();
        if (!$reservation) {
            return response()->json([
                "success" => false,
                "message" => "الحجز غير موجود"
            ], 404);
        }
        $reservation->status = "accepted";
        $reservation->save();
        $unit = $reservation->unit; // Ensure the Reservation model has a `unit` relationship
        $customer = User::where('id', $reservation->user_id)->first();

        $this->pushNotification(
            'تم قبول الحجز',
            "تم قبول الحجز {$unit->name} من فضلك اكمل عملية الدفع",
            $customer->id,
            "accept_reservation",
            $reservation->id
        );

        return response()->json([
            "success" => true,
            "message" => "تم قبول الحجز بنجاح"
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            "success" => false,
            "message" => "حدث خطأ في الخادم",
            "error" => $e->getMessage()
        ], 500);
    }
    }
    public function approve($id)
    {
        $user = auth()->user();
        $reservation = Reservation::with('user', 'unit')->where('id', $id)
            ->where('status', 'accepted')
            ->whereRelation("unit", "owner_id", "=", $user->id)
            // ->where('paid', 1)
            ->first();
        if (!$reservation) {
            return response()->json([
                "success" => false,
                "message" => "الحجز غير موجود"
            ], 404);
        }
        $code = $this->codeGeneratorService->reservation(
            $reservation->unit->code,
            $reservation->date_from,
            $reservation->days_count
        );
        $reservation->status = "approved";
        $reservation->code = $code;
        $reservation->approved_at = now();
        $reservation->save();

        $this->pushNotification(
            '✅ تم تأكيد حجزك بنجاح!',
            "تهانينا! تم تأكيد حجزك {$reservation->unit->name} من قِبل المالك. نحن متحمسون لخدمتك قريبًا!",
            $reservation->user->id,
        );

        return response()->json([
            "success" => true,
            "message" => "تم تأكيد الحجز بنجاح"
        ], 200);
    }
}
