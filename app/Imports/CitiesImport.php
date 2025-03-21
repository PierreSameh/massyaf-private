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
        $name = trim($row['name'] ?? '') ?: null; 
        $description = trim($row['description'] ?? '') ?: null;
        $features = trim($row['features'] ?? '') ?: null;
    
        return new City([
            "name" => $name,
            "description" => $description,
            "features" => $features,
            "created_at" => now(),
        ]);
    }
}
