<?php

namespace App\Services;

use App\Models\AvailableDate;
use Spatie\IcalendarGenerator\Components\Calendar;
use Spatie\IcalendarGenerator\Components\Event;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class IcalExportService
{
    /**
     * Export booked dates to an iCal (.ics) file.
     *
     * @param int $unitId
     * @return string
     */
    public function exportForUnit($unitId)
    {
        // Create icals directory if it doesn't exist
        Storage::disk('public')->makeDirectory('icals');
    
        // Rest of your existing code...
        $bookedDates = AvailableDate::where('unit_id', $unitId)->get();
        $calendar = Calendar::create('Booked Dates for Unit ' . $unitId);
    
        foreach ($bookedDates as $date) {
            $from = Carbon::parse($date->from);
            $to = Carbon::parse($date->to);
    
            $event = Event::create()
                ->name('Booked Date for Unit ' . $unitId)
                ->startsAt($from)
                ->endsAt($to)
                ->description('This date is booked for the unit.');
    
            $calendar->event($event);
        }
    
        $icalContent = $calendar->get();
        $filePath = "icals/" . now()->format('Y-m-d') . "unit_{$unitId}.ics";
        Storage::disk('public')->put($filePath, $icalContent);
        
        \Log::info('File written to ' . $filePath);
        \Log::info('File exists: ' . (Storage::disk('public')->exists($filePath) ? 'yes' : 'no'));
    
        return $filePath;
    }
}
