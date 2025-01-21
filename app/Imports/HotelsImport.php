<?php

namespace App\Imports;

use App\Models\City;
use App\Models\Hotel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class HotelsImport implements ToModel, WithHeadingRow
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
        $cityName = trim($row['city'] ?? '') ?: null;
        $city = City::firstOrCreate([
            'name' => $cityName
        ]);
        
        return new Hotel([
            "name" => $name,
            "city_id" => $city->id,
            "description" => $description,
            "features" => $features,
            "created_at" => now(),
        ]);
    }
}
