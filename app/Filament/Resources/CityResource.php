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

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                MapInput::make('coordinates')
                ->afterStateUpdated(function ($state, $set) {
                    $coordinates = json_decode($state, true);
                    if (is_array($coordinates) && count($coordinates) === 4) {
                        $set('lat_top_right', $coordinates[0]['lat']);
                        $set('lng_top_right', $coordinates[0]['lng']);
                        $set('lat_top_left', $coordinates[1]['lat']);
                        $set('lng_top_left', $coordinates[1]['lng']);
                        $set('lat_bottom_right', $coordinates[2]['lat']);
                        $set('lng_bottom_right', $coordinates[2]['lng']);
                        $set('lat_bottom_left', $coordinates[3]['lat']);
                        $set('lng_bottom_left', $coordinates[3]['lng']);
                    }
                }),
                Forms\Components\Hidden::make('lat_top_right'),
                Forms\Components\Hidden::make('lng_top_right'),
                Forms\Components\Hidden::make('lat_top_left'),
                Forms\Components\Hidden::make('lng_top_left'),
                Forms\Components\Hidden::make('lat_bottom_right'),
                Forms\Components\Hidden::make('lng_bottom_right'),
                Forms\Components\Hidden::make('lat_bottom_left'),
                Forms\Components\Hidden::make('lng_bottom_left'),
    
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
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
