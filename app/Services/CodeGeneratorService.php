<?php

namespace App\Services;

use App\Models\Compound;
use App\Models\Hotel;
use App\Models\Unit;

class CodeGeneratorService
{
    public function unit(string $type,int $parentId, int $rooms = null, int $floor = null){

        switch ($type){
            case "unit":
                $parent = Compound::find($parentId);
                break;
           
            case "hotel":
                $parent = Hotel::find($parentId);
                break;
        }
        // Generate the base code (without sequence)
        $baseCode = $parent->name . $rooms . $floor;
        // Initialize the sequence number
        $sequence = 1;
        $uniqueCode = $baseCode . $sequence;

        // Check if the code already exists, and increment the sequence if it does
        while (Unit::where('code', $uniqueCode)->exists()) {
            $uniqueCode = $baseCode . $sequence;
            $sequence++;
        }

        return $uniqueCode;
    }

    public function reservation(string $parentCode, string $dateFrom, int $duration): string{
        // Convert the string to a timestamp
        $date = strtotime($dateFrom);

        // Format the date to "ddmmyyyy"
        $formattedDate = date('dmY', $date);

        $formattedNumber = str_pad($duration, 2, '0', STR_PAD_LEFT);

        $uniqueCode = $parentCode . "-" . $formattedDate . "-" . $formattedNumber;

        return $uniqueCode;
    }
}