<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HotelResource\Pages;
use App\Filament\Resources\HotelResource\RelationManagers;
use App\Models\Hotel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Forms\Components\MapBoundaryInput;

class HotelResource extends Resource
{
    protected static ?string $model = Hotel::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('address')
                    ->maxLength(255),
                Forms\Components\Select::make('city_id')
                    ->label('City')
                    ->options(\App\Models\City::all()->pluck('name', 'id'))
                    ->searchable()
                    ->required()
                    ->placeholder('Select a city')
                    ->reactive(),
                Forms\Components\Textarea::make('details')
                    ->columnSpanFull(),
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
                    ->searchable(),
                Tables\Columns\TextColumn::make('city.name')
                    ->label(__("City"))
                    ->searchable(),
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
            'index' => Pages\ListHotels::route('/'),
            'create' => Pages\CreateHotel::route('/create'),
            'edit' => Pages\EditHotel::route('/{record}/edit'),
        ];
    }
}
