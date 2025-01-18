<?php

namespace App\Filament\Resources\CompoundResource\Pages;

use App\Filament\Resources\CompoundResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCompound extends EditRecord
{
    protected static string $resource = CompoundResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public $coordinates = '';

    // Load existing marker data when the component is initialized
    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Set the coordinates property using the record data
        $this->coordinates = json_encode([
            [
                'lat' => $this->record->lat_top_right,
                'lng' => $this->record->lng_top_right,
            ],
            [
                'lat' => $this->record->lat_top_left,
                'lng' => $this->record->lng_top_left,
            ],
            [
                'lat' => $this->record->lat_bottom_right,
                'lng' => $this->record->lng_bottom_right,
            ],
            [
                'lat' => $this->record->lat_bottom_left,
                'lng' => $this->record->lng_bottom_left,
            ],
        ]);
        \Log::info('Coordinates from database:', [$this->coordinates]);

        return $data;
    }

    // Handle the form submission
    protected function mutateFormDataBeforeSave(array $data): array
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
