<?php

namespace App\Filament\Resources\CityResource\Pages;

use App\Filament\Resources\CityResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCity extends CreateRecord
{
    protected static string $resource = CityResource::class;

    // Add the coordinates property
    public $coordinates = '';

    // Handle the form submission
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Decode the coordinates JSON string into an array
        $coordinates = json_decode($this->coordinates, true);

        // Merge the coordinates into the form data
        return array_merge($data, [
            'lat_top_right' => $coordinates[0]['lat'] ?? null,
            'lng_top_right' => $coordinates[0]['lng'] ?? null,
            'lat_top_left' => $coordinates[1]['lat'] ?? null,
            'lng_top_left' => $coordinates[1]['lng'] ?? null,
            'lat_bottom_right' => $coordinates[2]['lat'] ?? null,
            'lng_bottom_right' => $coordinates[2]['lng'] ?? null,
            'lat_bottom_left' => $coordinates[3]['lat'] ?? null,
            'lng_bottom_left' => $coordinates[3]['lng'] ?? null,
        ]);
    }
}
