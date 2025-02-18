<?php

namespace App\Services;

use App\Models\AvailableDate;
use ICal\ICal;
use Carbon\Carbon;

class IcalImportService
{
    /**
     * Import iCal data.
     *
     * @param string $icalContent
     * @param int $unitId
     * @return array
     */
    public function importIcal($icalContent, $unitId)
    {
        // Initialize the ICal parser
        $ical = new ICal();
        $ical->initString($icalContent);

        // Get all events from the iCal content
        $events = $ical->events();

        $importedEvents = [];

        foreach ($events as $event) {
            // Extract the start and end datetimes
            $from = Carbon::parse($event->dtstart);
            $to = Carbon::parse($event->dtend);

            // Create or update the booked date in the database
            $availableDate = AvailableDate::updateOrCreate(
                [
                    'unit_id' => $unitId,
                    'from' => $from,
                    'to' => $to,
                ],
                [
                    'unit_id' => $unitId,
                    'from' => $from,
                    'to' => $to,
                ]
            );

            // Add the imported event to the results
            $importedEvents[] = [
                'id' => $availableDate->id,
                'unit_id' => $unitId,
                'from' => $from->toDateTimeString(),
                'to' => $to->toDateTimeString(),
            ];
        }

        return $importedEvents;
    }
}