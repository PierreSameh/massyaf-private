<?php

namespace App\Filament\Resources\CityResource\Pages;

use App\Filament\Resources\CityResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCity extends CreateRecord
{
    protected static string $resource = CityResource::class;

    // Add the coordinates property
    public $coordinates = [];

    // Handle the form submission
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Merge the coordinates into the form data
        return array_merge($data, [
            'coordinates' => $this->coordinates,
        ]);
    }
}
