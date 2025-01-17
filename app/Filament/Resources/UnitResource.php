<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UnitResource\Pages;
use App\Filament\Resources\UnitResource\RelationManagers;
use App\Models\Unit;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Grouping\Group;

class UnitResource extends Resource
{
    protected static ?string $model = Unit::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('owner_id')
                    ->required()
                    ->numeric(),
                Forms\Components\FileUpload::make('ownership_documents')
                    ->label(__('Ownership Documents'))
                    ->disk('public')
                    ->directory('ownership_documents')
                    ->multiple()
                    ->columnSpanFull()
                    ->reorderable()
                    ->panelLayout('grid')
                    ->image()
                    ->openable()
                    ->downloadable()
                    ->required(),
                Forms\Components\TextInput::make('type')
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('rate')
                    ->numeric(),
                Forms\Components\TextInput::make('unit_type_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('city_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('compound_id')
                    ->numeric(),
                Forms\Components\TextInput::make('hotel_id')
                    ->tel()
                    ->numeric(),
                Forms\Components\TextInput::make('status')
                    ->required(),
                Forms\Components\TextInput::make('address')
                    ->maxLength(255),
                Forms\Components\TextInput::make('lat')
                    ->maxLength(255),
                Forms\Components\TextInput::make('lng')
                    ->maxLength(255),
                Forms\Components\TextInput::make('unit_number')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('floors_count')
                    ->required()
                    ->numeric()
                    ->default(1),
                Forms\Components\Toggle::make('elevator')
                    ->required(),
                Forms\Components\TextInput::make('area')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('distance_unit_beach')
                    ->numeric(),
                Forms\Components\TextInput::make('beach_unit_transportation')
                    ->required(),
                Forms\Components\TextInput::make('distance_unit_pool')
                    ->numeric(),
                Forms\Components\TextInput::make('pool_unit_transportation')
                    ->required(),
                Forms\Components\TextInput::make('room_count')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('toilet_count')
                    ->required()
                    ->numeric(),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('reservation_roles')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('reservation_type')
                    ->required(),
                Forms\Components\TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                Forms\Components\TextInput::make('insurance_amount')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('max_individuals')
                    ->required()
                    ->numeric(),
                Forms\Components\Toggle::make('youth_only')
                    ->required(),
                Forms\Components\TextInput::make('min_reservation_days')
                    ->numeric(),
                Forms\Components\TextInput::make('deposit')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('upon_arival_price')
                    ->required()
                    ->numeric(),
                Forms\Components\Toggle::make('weekend_prices')
                    ->required(),
                Forms\Components\TextInput::make('min_weekend_period')
                    ->numeric(),
                Forms\Components\TextInput::make('weekend_price')
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('owner.name'),
                Tables\Columns\TextColumn::make('type'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                    Tables\Columns\TextColumn::make('unitType.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('city.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('compound.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('hotel.name')
                ->numeric()
                ->sortable(),
                Tables\Columns\IconColumn::make('status')
                    ->icon(fn (string $state): string => match ($state) {
                        'waiting' => 'heroicon-o-clock',
                        'active' => 'heroicon-o-check-circle',
                        'rejected' => 'heroicon-o-x-circle'
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'waiting' => 'warning',
                        'active' => 'success',
                        'rejected' => 'danger'
                    }),                
                Tables\Columns\TextColumn::make('rate')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'hotel' => __('Hotel Rooms'),
                        'unit' => __('Units'),
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'waiting' => __('Waiting'),
                        'active' => __('Active'),
                        'rejected' => __('Rejected'),
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
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
            'index' => Pages\ListUnits::route('/'),
            'create' => Pages\CreateUnit::route('/create'),
            'view' => Pages\ViewUnit::route('/{record}'),
            'edit' => Pages\EditUnit::route('/{record}/edit'),
        ];
    }
}
