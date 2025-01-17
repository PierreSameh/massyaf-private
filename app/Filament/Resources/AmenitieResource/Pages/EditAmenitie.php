<?php

namespace App\Filament\Resources\AmenitieResource\Pages;

use App\Filament\Resources\AmenitieResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAmenitie extends EditRecord
{
    protected static string $resource = AmenitieResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
