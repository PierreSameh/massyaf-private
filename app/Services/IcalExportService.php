<?php

namespace App\Services;

use App\Models\AvailableDate;
use Spatie\IcalendarGenerator\Components\Calendar;
use Spatie\IcalendarGenerator\Components\Event;
use Carbon\Carbon;

class IcalExportService
{
    /**
     * Export booked dates to an iCal file.
     *
     * @param int $unitId
     * @return string
     */
    public function exportForUnit($unitId)
    {
        // Fetch booked dates for the specific unit
        $bookedDates = AvailableDate::where('unit_id', $unitId)->get();

        // Create a new calendar
        $calendar = Calendar::create('Booked Dates for Unit ' . $unitId);

        foreach ($bookedDates as $date) {
            // Convert `from` and `to` to DateTime objects
            $from = Carbon::parse($date->from);
            $to = Carbon::parse($date->to);

            // Create an event for each booked date
            $event = Event::create()
                ->name('Booked Date for Unit ' . $unitId)
                ->startsAt($from) // Pass DateTime object
                ->endsAt($to) // Pass DateTime object
                ->description('This date is booked for the unit.');

            // Add the event to the calendar
            $calendar->event($event);
        }

        // Generate the iCal content
        return $calendar->get();
    }
}