<?php

namespace App\Filament\Resources\UnitResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;

class ReservationsRelationManager extends RelationManager
{
    protected static string $relationship = 'reservations';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Reservations');
    }
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('unit_id')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->emptyStateHeading(__('No reservations yet'))
            ->recordTitleAttribute('unit_id')
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('user.name'),
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
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }
}
