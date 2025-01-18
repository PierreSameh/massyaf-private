<?php

namespace App\Filament\Resources\UnCompletedReservationsResource\Pages;

use App\Filament\Resources\UnCompletedReservationsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUnCompletedReservations extends EditRecord
{
    protected static string $resource = UnCompletedReservationsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
