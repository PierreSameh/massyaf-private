<?php

namespace App\Filament\Resources\CompoundResource\Pages;

use App\Filament\Resources\CompoundResource;
use App\Imports\CompoundsImport;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Maatwebsite\Excel\Facades\Excel;
class ListCompounds extends ListRecords
{
    protected static string $resource = CompoundResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Action::make('importCompounds')
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

                    Excel::import(new CompoundsImport(), $file);
                })
        ];
    }
}
