<?php

namespace App\Filament\Resources\HotelResource\Pages;

use App\Filament\Resources\HotelResource;
use App\Imports\HotelsImport;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Maatwebsite\Excel\Facades\Excel;
class ListHotels extends ListRecords
{
    protected static string $resource = HotelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Action::make('importHotels')
                ->label(__('Import'))
                ->color('danger')
                ->form([
                    FileUpload::make('attachment')
                        ->label(__('Attachment'))
                        ->required()
                        ->directory('imports')
                        ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']) 
                ])
                ->action(function ($data){
                    $file = Public_path('storage/' . $data['attachment']);

                    Excel::import(new HotelsImport(), $file);
                })
        ];
    }
}
