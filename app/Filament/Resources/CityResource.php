<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CityResource\Pages;
use App\Filament\Resources\CityResource\RelationManagers;
use App\Forms\Components\MapInput;
use App\Models\City;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CityResource extends Resource
{
    protected static ?string $model = City::class;

    public static function getNavigationGroup(): ?string
    {
        return __('Add Data');
    }

    public static function getLabel(): ?string
    {
        return __('City');  // Translation function works here
    }
    public static function getPluralLabel(): ?string
    {
        return __('Cities');  // For plural label translations
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('Name'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\FileUpload::make('images')
                    ->label(__('Images'))
                    ->disk('public')
                    ->directory('cities')
                    ->multiple()
                    ->columnSpanFull()
                    ->reorderable()
                    ->panelLayout('grid')
                    ->image()
                    ->required(),
                Forms\Components\RichEditor::make('description')->label(__("Description"))
                    ->columnSpanFull(),
                Forms\Components\RichEditor::make('features')->label(__('Features'))
                    ->columnSpanFull(),
                MapInput::make('coordinates')
                    ->label(__("Zone Boundaries"))
                    ->apiKey(env('GOOGLE_MAPS_API_KEY'))
                    ->reactive()
                    ->columnSpanFull(),
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
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Creation Date'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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
            'index' => Pages\ListCities::route('/'),
            'create' => Pages\CreateCity::route('/create'),
            'edit' => Pages\EditCity::route('/{record}/edit'),
        ];
    }
}
