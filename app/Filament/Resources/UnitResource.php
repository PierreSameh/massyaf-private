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
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists;
use Filament\Infolists\Components\IconEntry;

class UnitResource extends Resource
{
    protected static ?string $model = Unit::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
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
    
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                // Basic Information
                Section::make('Basic Information')
                    ->schema([
                        Section::make(__("Owner Info"))
                        ->schema([
                            ImageEntry::make('owner.image')->label(__('Profile Photo'))
                            ->circular()
                            ->defaultImageUrl(url('/images/downloaded.jpeg'))
                            ->extraImgAttributes([
                                'loading' => 'lazy',
                            ]),
                            ImageEntry::make('owner.id_image')->label(__('National ID'))
                            ->extraImgAttributes([
                                'alt' => 'not set',
                                'loading' => 'lazy',
                            ]),
                            TextEntry::make('owner.name')->label(__("Name")),
                            TextEntry::make('owner.phone_number')->label(__("Phone")),
                            TextEntry::make('owner.email')->label(__('Email')),
                        ])->columns(2),
                        TextEntry::make('name')->label('Name'),
                        TextEntry::make('code')->label('Code'),
                        TextEntry::make('type')->label('Type'),
                        TextEntry::make('status')->label('Status')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'waiting' => 'warning',
                            'active' => 'success',
                            'rejected' => 'danger',
                        }),
                        TextEntry::make('unit_number')->label('Unit Number'),
                        TextEntry::make('floors_count')->label('Floors Count')
                            ->numeric(),
                        IconEntry::make('elevator')->label('Elevator')
                            ->icon(fn (string $state): string => match ($state) {
                                '0' => 'heroicon-o-x-circle',
                                '1' => 'heroicon-o-check-circle',
                            })
                            ->color(fn (string $state): string => match ($state) {
                                '0' => 'danger',
                                '1' => 'success',
                            }),
                        TextEntry::make('area')->label('Area'),
                        TextEntry::make('room_count')->label('Room Count'),
                        TextEntry::make('toilet_count')->label('Toilet Count'),
                        TextEntry::make('description')->label('Description'),
                    ])->columns(2),
    
                // Pricing Information
                Section::make('Pricing Information')
                    ->schema([
                        TextEntry::make('price')->label('Price')->money('egp'),
                        TextEntry::make('insurance_amount')->label('Insurance Amount')->money('egp'),
                        TextEntry::make('deposit')->label('Deposit')->money('egp'),
                        TextEntry::make('upon_arival_price')->label('Upon Arrival Price')->money('egp'),
                        TextEntry::make('weekend_price')->label('Weekend Price')->money('egp'),
                        TextEntry::make('min_price')->label('Min Price')->money('egp'),
                        TextEntry::make('max_price')->label('Max Price')->money('egp'),
                    ])->columns(2),
    
                // Location Information
                Section::make('Location Information')
                    ->schema([
                        TextEntry::make('city.name')->label('City'),
                        TextEntry::make('hotel.name')->label('Hotel'),
                        TextEntry::make('address')->label('Address')
                        ->formatStateUsing(function ($state, $record) {
                            // If the hotel relationship is set and has an address, use the hotel address
                            if ($record->hotel && $record->hotel->address) {
                                return $record->hotel->address;
                            }
                            // Otherwise, use the unit's address
                            return $state;
                        }),
                    ])->columns(2),
    
                // Additional Fees
                Section::make('Additional Fees')
                    ->schema([
                        RepeatableEntry::make('additionalFees')
                            ->schema([
                                TextEntry::make('fees')->label('Fee Type'),
                                TextEntry::make('amount')->label('Amount')->money('usd'),
                            ])
                            ->columns(2),
                    ]),
    
                // Available Dates
                Section::make('Available Dates')
                    ->schema([
                        RepeatableEntry::make('availableDates')
                            ->schema([
                                TextEntry::make('from')->label('From'),
                                TextEntry::make('to')->label('To'),
                            ])
                            ->columns(2),
                    ]),
    
                // Sales
                Section::make('Sales')
                    ->schema([
                        RepeatableEntry::make('sales')
                            ->schema([
                                TextEntry::make('from')->label('From'),
                                TextEntry::make('to')->label('To'),
                                TextEntry::make('sale_percentage')->label('Sale Percentage'),
                            ])
                            ->columns(3),
                    ]),
    
                // Cancel Policies
                Section::make('Cancel Policies')
                    ->schema([
                        RepeatableEntry::make('cancelPolicies')
                            ->schema([
                                TextEntry::make('days')->label('Days'),
                                TextEntry::make('penalty')->label('Penalty')->money('usd'),
                            ])
                            ->columns(2),
                    ]),
    
                // Long Term Reservations
                Section::make('Long Term Reservations')
                    ->schema([
                        RepeatableEntry::make('longTermReservations')
                            ->schema([
                                TextEntry::make('more_than_days')->label('More Than Days'),
                                TextEntry::make('sale_percentage')->label('Sale Percentage'),
                            ])
                            ->columns(2),
                    ]),
    
                // Special Reservation Times
                Section::make('Special Reservation Times')
                    ->schema([
                        RepeatableEntry::make('specialReservationTimes')
                            ->schema([
                                TextEntry::make('from')->label('From'),
                                TextEntry::make('to')->label('To'),
                                TextEntry::make('price')->label('Price')->money('usd'),
                                TextEntry::make('min_reservation_period')->label('Min Reservation Period'),
                            ])
                            ->columns(4),
                    ]),
    
                // Rooms
                Section::make('Rooms')
                    ->schema([
                        RepeatableEntry::make('rooms')
                            ->schema([
                                TextEntry::make('bed_count')->label('Bed Count'),
                                TextEntry::make('bed_sizes')->label('Bed Sizes')
                                    ->formatStateUsing(function ($state) {
                                        // Ensure $state is an array before using implode
                                        return is_array($state) ? implode(', ', $state) : $state;
                                    }),
                            ])
                            ->columns(2),
                    ]),
    
                // Amenities
                Section::make('Amenities')
                    ->schema([
                        RepeatableEntry::make('amenities')
                            ->schema([
                                TextEntry::make('name')->label('Amenity Name'),
                            ])
                            ->columns(1),
                    ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ReservationsRelationManager::class
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
