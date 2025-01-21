<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UnCompletedReservationsResource\Pages;
use App\Filament\Resources\UnCompletedReservationsResource\RelationManagers;
use App\Models\Reservation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UnCompletedReservationsResource extends Resource
{
    protected static ?string $model = Reservation::class;

    public static function getLabel(): ?string
    {
        return __('Incompleted Reservation');  // Translation function works here
    }
    public static function getPluralLabel(): ?string
    {
        return __('Incompleted Reservations');  // For plural label translations
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Reservations');
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
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
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListUnCompletedReservations::route('/'),
            // 'create' => Pages\CreateUnCompletedReservations::route('/create'),
            // 'edit' => Pages\EditUnCompletedReservations::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->onlyTrashed();
    }
}
