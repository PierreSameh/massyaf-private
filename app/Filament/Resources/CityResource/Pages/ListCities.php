<?php

namespace App\Filament\Resources\CityResource\Pages;

use App\Filament\Resources\CityResource;
use App\Imports\CitiesImport;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

class ListCities extends ListRecords
{
    protected static string $resource = CityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Action::make('importCities')
                ->label('Import')
                ->color('danger')
                ->form([
                    FileUpload::make('attachment')
                        ->required()
                        ->directory('imports')
                        ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']) // Accept only .xlsx files
                ])
                ->action(function ($data){
                    $file = Public_path('storage/' . $data['attachment']);

                    Excel::import(new CitiesImport(), $file);
                })
        ];
    }
}
