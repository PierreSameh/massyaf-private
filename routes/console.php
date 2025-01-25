<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// check on payment expiration on reservation
Schedule::command('reservations:soft-delete-unpaid')->everyMinute();
Schedule::command('app:check-unanswered-chats')->everyMinute();
