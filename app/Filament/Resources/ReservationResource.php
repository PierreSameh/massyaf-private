<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReservationResource\Pages;
use App\Filament\Resources\ReservationResource\RelationManagers;
use App\Models\Reservation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
class ReservationResource extends Resource
{
    protected static ?string $model = Reservation::class;

    public static function getNavigationGroup(): ?string
    {
        return __('Reservations');
    }
    public static function getLabel(): ?string
    {
        return __('Reservation');  // Translation function works here
    }
    public static function getPluralLabel(): ?string
    {
        return __('Reservations');  // For plural label translations
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DateTimePicker::make('date_from')
                    ->label(__("Start Date"))
                    ->required(),
                Forms\Components\DateTimePicker::make('date_to')
                    ->label(__("End Date"))
                    ->required(),
                Forms\Components\TextInput::make('adults_count')
                    ->label(__("Adults Count"))
                    ->numeric(),
                Forms\Components\TextInput::make('children_count')
                    ->label(__("Children Count"))
                    ->numeric(),
                Forms\Components\Toggle::make('paid')
                    ->label(__("Paid"))
                    ->required(),
                Forms\Components\Select::make('status')
                    ->label(__("Status"))
                    ->options([
                        "pending" => __("Pending"),
                        "accepted" => __("Accepted"),
                        "approved" => __("Approved"),
                        "rejected" => __("Rejected"),
                        "canceled_user" => __("Cancelled By User"),
                        "canceled_owner" => __("Cancelled By Owner"), 
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('unit_id')->label(__("Unit ID")),
                Tables\Columns\TextColumn::make('unit.name')->label(__("Unit Name")),
                Tables\Columns\TextColumn::make('unit.owner.name')->label(__("Owner"))->searchable(),
                Tables\Columns\TextColumn::make('user.name')->label(__("User Name"))->searchable(),
                Tables\Columns\TextColumn::make('book_advance')
                    ->label(__("Book Advance"))
                    ->money('EGP'),
                Tables\Columns\TextColumn::make('booking_price')
                    ->label(__("Booking Price"))
                    ->money('EGP'),
                Tables\Columns\TextColumn::make('owner_profit')
                    ->label(__("Owner Profits"))
                    ->money('EGP'),
                Tables\Columns\TextColumn::make('status')
                    ->label(__("Status"))
                    ->formatStateUsing(function ($state) {
                        switch ($state) {
                            case 'pending':
                                return __("Pending");
                            case 'approved':
                                return __("Approved");
                            case 'rejected':
                                return __("Rejected");
                            case 'accepted':
                                return __("Accepted");
                            case 'canceled_user':
                                return __('Cancelled By User');
                            case 'canceled_owner':
                                return __('Cancelled By Owner');
                            default:
                                 return __('Undefinded');
                        }     
                    }),
                Tables\Columns\IconColumn::make('paid')
                    ->label(__("Paid"))
                    ->icon(fn (string $state): string => match ($state) {
                        '0' => 'heroicon-o-clock',
                        '1' => 'heroicon-o-check-circle',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        '0' => 'warning',
                        '1' => 'success',
                    }),
                Tables\Columns\TextColumn::make('date_from')
                    ->label(__("Start Date"))
                    ->date('d/m/y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('date_to')
                    ->label(__("End Date"))
                    ->date('d/m/y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('code')->label(__("Reservation Code"))
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__("Creation Date"))
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                ->options([
                    "pending" => __("Pending"),
                    "accepted" => __("Accepted"),
                    "approved" => __("Approved"),
                    "rejected" => __("Rejected"),
                    "canceled_user" => __("Cancelled By User"),
                    "canceled_owner" => __("Cancelled By Owner"), 
                ]),
                Tables\Filters\TernaryFilter::make('paid'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
                // Reservation Details
                Section::make(__('Reservation Details'))
                    ->schema([
                        TextEntry::make('code')
                            ->label(__('Reservation Code')),
                        TextEntry::make('date_from')
                            ->label(__('Start Date'))
                            ->dateTime(),
                        TextEntry::make('date_to')
                            ->label(__('End Date'))
                            ->dateTime(),
                        TextEntry::make('adults_count')
                            ->label(__("Adults Count")),
                        TextEntry::make('children_count')
                            ->label(__('Children Count'))
                            ->placeholder(__('N/A')),
                        TextEntry::make('book_advance')
                            ->label(__('Book Advance'))
                            ->money('egp'),
                        TextEntry::make('booking_price')
                            ->label(__('Booking Price'))
                            ->money('egp'),
                        TextEntry::make('status')
                            ->label(__('Status'))
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'approved' => 'success',
                                'cancelled' => 'danger',
                                default => 'gray',
                            })
                            ->formatStateUsing(function ($state) {
                                switch ($state) {
                                    case 'pending':
                                        return __("Pending");
                                    case 'approved':
                                        return __("Approved");
                                    case 'rejected':
                                        return __("Rejected");
                                    case 'accepted':
                                        return __("Accepted");
                                    case 'canceled_user':
                                        return __('Cancelled By User');
                                    case 'canceled_owner':
                                        return __('Cancelled By Owner');
                                    default:
                                         return __('Undefinded');
                                }     
                            }),
                        TextEntry::make('approved_at')
                            ->label(__('Approved At'))
                            ->dateTime(),
                        TextEntry::make('cancelled_at')
                            ->label(__('Cancelled At'))
                            ->dateTime()
                            ->placeholder(__('N/A')),
                    ])
                    ->columns(2),
    
                // Unit Details
                Section::make(__('Unit Details'))
                    ->schema([
                        TextEntry::make('unit.code')
                            ->label(__('Unit Code')),
                        TextEntry::make('unit.name')
                            ->label(__('Unit Name')),
                        TextEntry::make('unit.type')
                            ->label(__('Unit Type')),
                        TextEntry::make('unit.address')
                            ->label(__('Address'))
                            ->formatStateUsing(function ($record) {
                                return $record->unit->address ?: $record->unit->hotel->address;
                            })
                            ->placeholder(__('N/A')),
                        TextEntry::make('unit.description')
                            ->label(__('Description'))
                            ->html()
                            ->placeholder(__('N/A')),
                        TextEntry::make('unit.rooms')
                            ->label(__('Rooms'))
                            ->formatStateUsing(function ($state) {
                                // Decode JSON if $state is a string
                                if (is_string($state)) {
                                    $state = json_decode($state, true);
                                }
    
                                // Ensure $state is an array
                                if (is_array($state)) {
                                    return collect($state)->map(function ($room) {
                                        // Ensure $room is an array and has the required keys
                                        if (is_array($room) && isset($room['id'], $room['bed_count'], $room['bed_sizes'])) {
                                            return __("Room ID") . ": {$room['id']}, " . __("Beds") . ": {$room['bed_count']}, " . __("Sizes") . ":" . implode(', ', $room['bed_sizes']);
                                        }
                                        return __('Invalid room data');
                                    })->implode('<br>');
                                }
    
                                return __('No room data available');
                            })
                            ->html(), // Allow HTML for line breaks
                    ])
                    ->columns(2),
    
                // User Details
                Section::make(__('User Details'))
                    ->schema([
                        TextEntry::make('user.name')
                            ->label(__('User Name')),
                        TextEntry::make('user.email')
                            ->label(__('Email')),
                        TextEntry::make('user.phone_number')
                            ->label(__('Phone Number')),
                    ])
                    ->columns(2),
    
                // Owner Details
                Section::make(__('Owner Details'))
                    ->schema([
                        TextEntry::make('unit.owner.name')
                            ->label(__('Owner Name')),
                        TextEntry::make('unit.owner.email')
                            ->label(__('Email')),
                        TextEntry::make('unit.owner.phone_number')
                            ->label(__('Phone Number')),
                    ])
                    ->columns(2),
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
            'index' => Pages\ListReservations::route('/'),
            'create' => Pages\CreateReservation::route('/create'),
            'view' => Pages\ViewReservation::route('/{record}'),
            'edit' => Pages\EditReservation::route('/{record}/edit'),
        ];
    }
}
