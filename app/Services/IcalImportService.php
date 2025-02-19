<?php

namespace App\Services;

use App\Models\AvailableDate;
use ICal\ICal;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class IcalImportService
{
    /**
     * Import booked dates from an iCal (.ics) file.
     *
     * @param string $filePath
     * @param int $unitId
     * @return array
     */
    public function importIcal($filePath, $unitId)
    {
        // Get the file contents
        $icalContent = Storage::disk('local')->get($filePath);

        // Parse iCal data
        $ical = new ICal(false, ['defaultSpan' => 1]);
        $ical->initString($icalContent);

        $events = $ical->events();
        $importedEvents = [];

        foreach ($events as $event) {
            $from = Carbon::parse($event->dtstart);
            $to = Carbon::parse($event->dtend);

            // Save to database
            $newDate = AvailableDate::create([
                'unit_id' => $unitId,
                'from' => $from,
                'to' => $to,
            ]);

            $importedEvents[] = $newDate;
        }

        return $importedEvents;
    }
}
