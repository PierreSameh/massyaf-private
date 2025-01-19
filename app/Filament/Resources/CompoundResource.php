<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompoundResource\Pages;
use App\Filament\Resources\CompoundResource\RelationManagers;
use App\Forms\Components\MapBoundaryInput;
use App\Models\Compound;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CompoundResource extends Resource
{
    protected static ?string $model = Compound::class;

    public static function getNavigationGroup(): ?string
    {
        return __('Add Data');
    }

    public static function getLabel(): ?string
    {
        return __('Compound');  // Translation function works here
    }
    public static function getPluralLabel(): ?string
    {
        return __('Compounds');  // For plural label translations
    }
    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\TextInput::make('name')
                ->label(__('Name'))
                ->required()
                ->maxLength(255),
            Forms\Components\Select::make('city_id')
                ->label(__('City'))
                ->options(\App\Models\City::all()->pluck('name', 'id'))
                ->searchable()
                ->required()
                ->placeholder(__('Select a city'))
                ->reactive(),
                Forms\Components\FileUpload::make('images')
                ->label(__('Images'))
                ->disk('public')
                ->directory('compounds')
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
            Forms\Components\TextInput::make('address')
                ->label(__('Address'))
                ->required()
                ->maxLength(255),
            MapBoundaryInput::make('location')
                ->label(__("Location"))
                ->apiKey(env('GOOGLE_MAPS_API_KEY'))
                ->reactive()
                ->afterStateUpdated(function (Forms\Set $set, $state) {
                    $set('lng', $state->detail->lng);
                    $set('lat', $state->detail->lat);
                })
                ->columnSpanFull()
                ->latField('lat')
                ->lngField('lng'),
            Forms\Components\Hidden::make('lng'),
            Forms\Components\Hidden::make('lat'), 
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('city.name')
                    ->label(__('City')),
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
            'index' => Pages\ListCompounds::route('/'),
            'create' => Pages\CreateCompound::route('/create'),
            'edit' => Pages\EditCompound::route('/{record}/edit'),
        ];
    }
}
