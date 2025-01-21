<?php

namespace App\Filament\Resources\OwnerResource\RelationManagers;

use App\Models\Reservation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\Summarizers\Sum;
use Illuminate\Database\Eloquent\Model;

class ReservationsRelationManager extends RelationManager
{
    protected static string $relationship = 'ownerReservations';

 
    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Owner Reservations');
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
        $ownerId = $this->ownerRecord->id;

        $totalOwnerProfit = Reservation::query()
        ->whereRelation('unit.owner', 'owner_id', $ownerId) // Filter by owner ID
        ->where('status', 'approved')
        ->where('paid', true)
        ->sum('owner_profit');
        return $table
            ->emptyStateHeading(__('No reservations yet'))
            ->recordTitleAttribute('unit_id')
            ->defaultSort('created_at', 'desc')
            ->header(function () use ($totalOwnerProfit) {
                return view('filament.tables.custom-header', [
                    'totalOwnerProfit' => $totalOwnerProfit,
                ]);
            })
            ->columns([
                Tables\Columns\TextColumn::make('unit_id')->label(__("Unit ID")),
                Tables\Columns\TextColumn::make('unit.name')->label(__("Unit Name")),
                Tables\Columns\TextColumn::make('user.name')->label(__("User Name")),
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
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__("Creation Date"))
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
                Tables\Actions\Action::make('view')
                ->label(__('View'))
                ->url(fn(Reservation $record) => ( '/admin/reservations/' . $record->id))
                ->openUrlInNewTab(false), // Ensure it doesn't open in a new tab
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }
}
