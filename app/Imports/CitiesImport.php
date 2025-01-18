<?php

namespace App\Imports;

use App\Models\City;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CitiesImport implements ToModel, WithHeadingRow
{
    use Importable;
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {

        $name = trim($row['name'] ?? ''); // Use null coalescing to avoid undefined key errors
        
        if (empty($name)) {
            \Log::warning('Skipped row due to missing or invalid "name": ', $row);
            return null;
        }
    
        return new City([
            "name" => $name,
            "created_at" => now(),
        ]);
    }
}
