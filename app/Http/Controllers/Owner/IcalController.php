<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\IcalExportService;
use App\Services\IcalImportService;

class IcalController extends Controller
{

public function exportIcal($unitId)
{
    try{
    $icalContent = (new IcalExportService())->exportForUnit($unitId);

    return response()->json([
        'success' => true,
        'message' => 'iCal data exported successfully.',
        'data' => [
            'ical' => $icalContent,
        ],
    ]);
    }catch(\Exception $e){
        return response()->json([
            "success" => false,
            "message" => $e->getMessage()
        ], 500);
    }
}

public function importIcal(Request $request, $unitId)
    {
        // Validate the request
        $request->validate([
            'ical' => 'required|string',
        ]);

        // Get the iCal content from the request
        $icalContent = $request->input('ical');

        // Import the iCal data
        $importedEvents = (new IcalImportService())->importIcal($icalContent, $unitId);

        // Return the results as JSON
        return response()->json([
            'success' => true,
            'message' => 'iCal data imported successfully.',
            'data' => [
                'imported_events' => $importedEvents,
            ],
        ]);
    }
}
