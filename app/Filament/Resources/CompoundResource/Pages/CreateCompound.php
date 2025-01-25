<?php

namespace App\Filament\Resources\CompoundResource\Pages;

use App\Filament\Resources\CompoundResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCompound extends CreateRecord
{
    protected static string $resource = CompoundResource::class;

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
