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

    public static function getNavigationGroup(): ?string
    {
        return __('Accounts');
    }

    public static function getLabel(): ?string
    {
        return __('Unit');  // Translation function works here
    }
    public static function getPluralLabel(): ?string
    {
        return __('Units');  // For plural label translations
    }
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
                    ->label(__('ID'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('owner.name')->label(__('Owner Name')),
                Tables\Columns\TextColumn::make('type')->label(__('Type')),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('unitType.name')
                    ->label(__('Unit Type'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('city.name')
                    ->label(__('City'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('compound.name')
                    ->label(__('Compound'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('hotel.name')
                    ->label(__('Hotel'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('status')
                    ->label(__('Status'))
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
                    ->label(__('Rate'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('code')
                    ->label(__("Code"))
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Creation Date'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label(__('Type'))
                    ->options([
                        'hotel' => __('Hotel Rooms'),
                        'unit' => __('Units'),
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('Status'))
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
                Section::make(__('Basic Information'))
                    ->schema([
                        Section::make(__("Owner Info"))
                        ->schema([
                            ImageEntry::make('owner.image')->label(__('Profile Photo'))
                            ->circular()
                            ->defaultImageUrl(url('public/images/download.jpeg'))
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
                        TextEntry::make('name')->label(__('Name')),
                        TextEntry::make('code')->label(__('Code')),
                        TextEntry::make('type')->label(__('Type'))
                            ->formatStateUsing(function ($record){
                                return $record->type == "unit" ? __("Unit") : __("Hotel");
                            }),
                        TextEntry::make('status')->label(__('Status'))
                        ->badge()
                        ->formatStateUsing(function ($state){
                            switch($state){
                                case 'waiting':
                                    return __("Waiting");
                                case 'active':
                                    return __("Active");
                                case 'rejected':
                                    return __("Rejected");
                                default:
                                    return __("Unkown");
                            }
                        })
                        ->color(fn (string $state): string => match ($state) {
                            'waiting' => 'warning',
                            'active' => 'success',
                            'rejected' => 'danger',
                        }),
                        TextEntry::make('unit_number')->label(__('Unit Number')),
                        TextEntry::make('floors_count')->label(__('Floors Count'))
                            ->numeric(),
                        IconEntry::make('elevator')->label(__('Elevator'))
                            ->icon(fn (string $state): string => match ($state) {
                                '0' => 'heroicon-o-x-circle',
                                '1' => 'heroicon-o-check-circle',
                            })
                            ->color(fn (string $state): string => match ($state) {
                                '0' => 'danger',
                                '1' => 'success',
                            }),
                        TextEntry::make('area')->label(__('Area')),
                        TextEntry::make('room_count')->label(__('Room Count')),
                        TextEntry::make('toilet_count')->label(__('Toilet Count')),
                        TextEntry::make('description')->label(__('Description')),
                    ])->columns(2),
    
                // Pricing Information
                Section::make(__('Pricing Information'))
                    ->schema([
                        TextEntry::make('price')->label(__('Price'))->money('egp'),
                        TextEntry::make('insurance_amount')->label(__('Insurance Amount'))->money('egp'),
                        TextEntry::make('deposit')->label(__('Deposit'))->money('egp'),
                        TextEntry::make('upon_arival_price')->label(__('Upon Arrival Amount'))->money('egp'),
                        TextEntry::make('weekend_price')->label(__('Weekend Price'))->money('egp'),
                    ])->columns(2),
    
                // Location Information
                Section::make(__('Location Information'))
                    ->schema([
                        TextEntry::make('city.name')->label(__('City')),
                        TextEntry::make('hotel.name')->label(__('Hotel')),
                        TextEntry::make('address')->label(__('Address'))
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
                Section::make(__('Additional Fees'))
                    ->schema([
                        RepeatableEntry::make('additionalFees')
                            ->schema([
                                TextEntry::make('fees')->label(__('Fee Type')),
                                TextEntry::make('amount')->label(__('Amount'))->money('egp'),
                            ])
                            ->columns(2),
                    ]),
    
                // Available Dates
                Section::make(__('Available Dates'))
                    ->schema([
                        RepeatableEntry::make('availableDates')
                            ->schema([
                                TextEntry::make('from')->label(__('From')),
                                TextEntry::make('to')->label(__('To')),
                            ])
                            ->columns(2),
                    ]),
    
                // Sales
                Section::make(__('Sales'))
                    ->schema([
                        RepeatableEntry::make('sales')
                            ->schema([
                                TextEntry::make('from')->label(__('From')),
                                TextEntry::make('to')->label(__('To')),
                                TextEntry::make('sale_percentage')->label(__('Sale Percentage')),
                            ])
                            ->columns(3),
                    ]),
    
                // Cancel Policies
                Section::make(__('Cancel Policies'))
                    ->schema([
                        RepeatableEntry::make('cancelPolicies')
                            ->schema([
                                TextEntry::make('days')->label(__('Days')),
                                TextEntry::make('penalty')->label(__('Penalty'))->money('egp'),
                            ])
                            ->columns(2),
                    ]),
    
                // Long Term Reservations
                Section::make(__('Long Term Reservations'))
                    ->schema([
                        RepeatableEntry::make('longTermReservations')
                            ->schema([
                                TextEntry::make('more_than_days')->label(__('More Than (Days)')),
                                TextEntry::make('sale_percentage')->label(__('Sale Percentage')),
                            ])
                            ->columns(2),
                    ]),
    
                // Special Reservation Times
                Section::make(__('Special Reservation Times'))
                    ->schema([
                        RepeatableEntry::make('specialReservationTimes')
                            ->schema([
                                TextEntry::make('from')->label(__('From')),
                                TextEntry::make('to')->label(__('To')),
                                TextEntry::make('price')->label(__('Price'))->money('egp'),
                                TextEntry::make('min_reservation_period')->label(__('Min Reservation Period')),
                            ])
                            ->columns(4),
                    ]),
    
                // Rooms
                Section::make(__('Rooms'))
                    ->schema([
                        RepeatableEntry::make('rooms')
                            ->schema([
                                TextEntry::make('bed_count')->label(__('Bed Count')),
                                TextEntry::make('bed_sizes')->label(__('Bed Sizes'))
                                    ->formatStateUsing(function ($state) {
                                        // Ensure $state is an array before using implode
                                        return is_array($state) ? implode(', ', $state) : $state;
                                    }),
                            ])
                            ->columns(2),
                    ]),
    
                // Amenities
                Section::make(__('Amenities'))
                    ->schema([
                        RepeatableEntry::make('amenities')
                            ->schema([
                                TextEntry::make('name')->label(__('Amenity Name')),
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
