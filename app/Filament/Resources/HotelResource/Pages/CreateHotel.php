<?php

namespace App\Filament\Resources\HotelResource\Pages;

use App\Filament\Resources\HotelResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateHotel extends CreateRecord
{
    protected static string $resource = HotelResource::class;

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
