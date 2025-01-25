<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AmenitieResource\Pages;
use App\Filament\Resources\AmenitieResource\RelationManagers;
use App\Models\Amenitie;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AmenitieResource extends Resource
{
    protected static ?string $model = Amenitie::class;

    public static function getNavigationGroup(): ?string
    {
        return __('Add Data');
    }
    public static function getLabel(): ?string
    {
        return __('Amenitie');  // Translation function works here
    }
    public static function getPluralLabel(): ?string
    {
        return __('Amenities');  // For plural label translations
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('Name'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('type')
                    ->label(__('Type'))
                    ->options([
                        "unit" => __("Unit"),
                        "hotel" => __("Hotel"),
                        "room" => __("Room"),
                        "kitchen" => __("Kitchen"),
                        "reception" => __("Reception"),
                    ])
                    ->required(),
                Forms\Components\Toggle::make('is_global')
                    ->label(__('Global'))
                    ->onColor('success')
                    ->offColor('danger')
                    ->default(true)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')->label(__("Type"))
                    ->formatStateUsing(function ($state){
                        switch ($state) {
                            case "unit":
                                return __("Unit");
                            case "hotel":
                                return __("Hotel");
                            case "room":
                                return __("Room");
                            case "kitchen":
                                return __("Kitchen");
                            case "reception":
                                return __("Reception");
                        }
                    }),
                Tables\Columns\ToggleColumn::make('is_global')->label(__('Global'))
                    ->onColor('success')
                    ->offColor('danger'),
                // Tables\Columns\TextColumn::make('created_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_global')->label(__("Global")),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAmenities::route('/'),
            'create' => Pages\CreateAmenitie::route('/create'),
            'edit' => Pages\EditAmenitie::route('/{record}/edit'),
        ];
    }
}
