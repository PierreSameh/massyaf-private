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
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
                Tables\Columns\TextColumn::make('unit_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('unit.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('unit.owner.name')->label(__("Owner"))
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.phone_number')
                    ->label(__('User Phone'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.email')
                    ->label(__("User Email"))
                    ->searchable(),      
                Tables\Columns\TextColumn::make('book_advance')
                    ->money('EGP'),
                Tables\Columns\TextColumn::make('booking_price')
                    ->money('EGP'),
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
                Tables\Columns\TextColumn::make('created_at')
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
