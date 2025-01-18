<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Reservation;
use App\Traits\PushNotificationTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SoftDeleteUnpaidReservations extends Command
{
    use PushNotificationTrait;

    protected $signature = 'reservations:soft-delete-unpaid';
    protected $description = 'Soft delete reservations not paid after 4 hours of creation';

    public function handle()
    {
        $reservations = Reservation::with('unit', 'user')->where('paid', false)
            ->where('created_at', '<=', Carbon::now()->subHours(4))
            ->get();

        foreach ($reservations as $reservation) {
            $reservation->delete();
            $this->pushNotification(
                ' تم إلغاء الحجز لعدم إتمام الدفع',
                "نأسف لإبلاغك أنه تم إلغاء حجزك لـ{$reservation->unit->name} بسبب عدم إتمام عملية الدفع خلال المهلة المحددة (4 ساعات).",
                $reservation->user->id,
            );
        }


        $this->info('Unpaid reservations older than 4 hours have been soft deleted.');
    }
}
