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
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DateTimePicker::make('date_from')
                    ->required(),
                Forms\Components\DateTimePicker::make('date_to')
                    ->required(),
                Forms\Components\TextInput::make('adults_count')
                    ->numeric(),
                Forms\Components\TextInput::make('children_count')
                    ->numeric(),
                Forms\Components\Toggle::make('paid')
                    ->required(),
                Forms\Components\Select::make('status')
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
                Tables\Columns\TextColumn::make('unit_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('unit.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('unit.owner.name')->label(__("Owner"))
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('book_advance')
                    ->money('EGP'),
                Tables\Columns\TextColumn::make('booking_price')
                    ->money('EGP'),
                Tables\Columns\TextColumn::make('status')
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
                    ->icon(fn (string $state): string => match ($state) {
                        '0' => 'heroicon-o-clock',
                        '1' => 'heroicon-o-check-circle',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        '0' => 'warning',
                        '1' => 'success',
                    }),
                Tables\Columns\TextColumn::make('date_from')
                    ->date('d/m/y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('date_to')
                    ->date('d/m/y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('code')
                    ->searchable()
                    ->placeholder('N/A'),
                Tables\Columns\TextColumn::make('created_at')
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
                Section::make('Reservation Details')
                    ->schema([
                        TextEntry::make('code')
                            ->label('Reservation Code'),
                        TextEntry::make('date_from')
                            ->label('Check-In Date')
                            ->dateTime(),
                        TextEntry::make('date_to')
                            ->label('Check-Out Date')
                            ->dateTime(),
                        TextEntry::make('adults_count')
                            ->label('Adults'),
                        TextEntry::make('children_count')
                            ->label('Children')
                            ->placeholder('N/A'),
                        TextEntry::make('book_advance')
                            ->label('Advance Payment')
                            ->money('egp'),
                        TextEntry::make('booking_price')
                            ->label('Total Price')
                            ->money('egp'),
                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'approved' => 'success',
                                'cancelled' => 'danger',
                                default => 'gray',
                            }),
                        TextEntry::make('approved_at')
                            ->label('Approved At')
                            ->dateTime(),
                        TextEntry::make('cancelled_at')
                            ->label('Cancelled At')
                            ->dateTime()
                            ->placeholder('N/A'),
                    ])
                    ->columns(2),
    
                // Unit Details
                Section::make('Unit Details')
                    ->schema([
                        TextEntry::make('unit.code')
                            ->label('Unit Code'),
                        TextEntry::make('unit.name')
                            ->label('Unit Name'),
                        TextEntry::make('unit.type')
                            ->label('Unit Type'),
                        TextEntry::make('unit.address')
                            ->label('Address')
                            ->formatStateUsing(function ($record) {
                                return $record->unit->address ?: $record->unit->hotel->address;
                            })
                            ->placeholder('N/A'),
                        TextEntry::make('unit.description')
                            ->label('Description')
                            ->html()
                            ->placeholder('N/A'),
                        TextEntry::make('unit.rooms')
                            ->label('Rooms')
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
                                            return "Room ID: {$room['id']}, Beds: {$room['bed_count']}, Sizes: " . implode(', ', $room['bed_sizes']);
                                        }
                                        return 'Invalid room data';
                                    })->implode('<br>');
                                }
    
                                return 'No room data available';
                            })
                            ->html(), // Allow HTML for line breaks
                    ])
                    ->columns(2),
    
                // User Details
                Section::make('User Details')
                    ->schema([
                        TextEntry::make('user.name')
                            ->label('User Name'),
                        TextEntry::make('user.email')
                            ->label('Email'),
                        TextEntry::make('user.phone_number')
                            ->label('Phone Number'),
                    ])
                    ->columns(2),
    
                // Owner Details
                Section::make('Owner Details')
                    ->schema([
                        TextEntry::make('unit.owner.name')
                            ->label('Owner Name'),
                        TextEntry::make('unit.owner.email')
                            ->label('Owner Email'),
                        TextEntry::make('unit.owner.phone_number')
                            ->label('Owner Phone Number'),
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
