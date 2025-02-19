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
        try {
            $filePath = (new IcalExportService())->exportForUnit($unitId);
            // return response()->download(storage_path("public/app/{$filePath}"), "unit_{$unitId}.ics");
            return response()->download(public_path("storage/" . $filePath), "unit_{$unitId}.ics");
        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => $e->getMessage()
            ], 500);
        }
    }
    

    public function importIcal(Request $request, $unitId)
    {
        $request->validate([
            'ical_file' => 'required|file|mimes:ics',
        ]);
    
        // Store uploaded file
        $filePath = $request->file('ical_file')->store('icals');
    
        // Import from the stored file
        $importedEvents = (new IcalImportService())->importIcal($filePath, $unitId);
    
        return response()->json([
            'success' => true,
            'message' => 'iCal data imported successfully.',
            'data' => [
                'imported_events' => $importedEvents,
            ],
        ]);
    }
    
}
