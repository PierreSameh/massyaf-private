<?php

namespace App\Filament\Resources\UnCompletedReservationsResource\Pages;

use App\Filament\Resources\UnCompletedReservationsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUnCompletedReservations extends ListRecords
{
    protected static string $resource = UnCompletedReservationsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
