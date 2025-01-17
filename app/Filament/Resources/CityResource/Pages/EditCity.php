<?php

namespace App\Filament\Resources\CityResource\Pages;

use App\Filament\Resources\CityResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCity extends EditRecord
{
    protected static string $resource = CityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public $coordinates = '';

    public function mount($record): void
    {
        parent::mount($record);
        
        // Get the data from the form
        $data = $this->form->getState();
        
        // Initialize the coordinates from the form data
        if (isset($data['lat_top_right'])) {
            $this->coordinates = json_encode([
                [
                    'lat' => (float)$data['lat_top_right'],
                    'lng' => (float)$data['lng_top_right']
                ],
                [
                    'lat' => (float)$data['lat_top_left'],
                    'lng' => (float)$data['lng_top_left']
                ],
                [
                    'lat' => (float)$data['lat_bottom_right'],
                    'lng' => (float)$data['lng_bottom_right']
                ],
                [
                    'lat' => (float)$data['lat_bottom_left'],
                    'lng' => (float)$data['lng_bottom_left']
                ]
            ]);
        }
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Decode the coordinates JSON string into an array
        $coordinates = json_decode($this->coordinates, true);

        // Merge the coordinates into the form data if they exist
        if (is_array($coordinates) && count($coordinates) === 4) {
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

        return $data;
    }

    protected function getFormModel(): \Illuminate\Database\Eloquent\Model|string|null
    {
        return $this->record;
    }

}
