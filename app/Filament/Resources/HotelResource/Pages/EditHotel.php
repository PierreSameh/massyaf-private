<?php

namespace App\Filament\Resources\HotelResource\Pages;

use App\Filament\Resources\HotelResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditHotel extends EditRecord
{
    protected static string $resource = HotelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public $coordinates = [];

    protected function beforeSave(): void
    {
        // No-op to override default save behavior
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Ensure coordinates are in the correct format
        if (isset($data['coordinates']) && is_array($data['coordinates'])) {
            $this->coordinates = $data['coordinates'];
        }

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        \Log::info('Data before saving:', $data);

        // Merge the coordinates into the form data
        $data = array_merge($data, [
            'coordinates' => $this->coordinates,
        ]);

        // Save the updated record
        $record->fill($data);
        $record->save();

        \Log::info('Record updated successfully:', $record->toArray());
        return $record;
    }
}
