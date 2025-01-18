<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WithdrawResource\Pages;
use App\Filament\Resources\WithdrawResource\RelationManagers;
use App\Models\Withdraw;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Traits\PushNotificationTrait;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\DB;

class WithdrawResource extends Resource
{
    protected static ?string $model = Withdraw::class;

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
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\TextColumn::make('user.name')->label(__('User Name')),
                Tables\Columns\TextColumn::make('user.phone_number')->label(__("User Phone")),
                Tables\Columns\TextColumn::make('user.email')->label(__('User Email')),
                // Tables\Columns\TextColumn::make('user.balance')->label(__('User Balance'))
                //     ->money('egp'),
                Tables\Columns\TextColumn::make('amount')->label(__('Amount'))
                    ->money('egp'),
                Tables\Columns\IconColumn::make('status')
                    ->icon(fn (string $state): string => match ($state) {
                        'pending' => 'heroicon-o-clock',
                        'approved' => 'heroicon-o-check-circle',
                        'rejected' => 'heroicon-o-x-circle',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('bankAccount.bank')
                    ->label(__('Bank')),
                Tables\Columns\TextColumn::make('bankAccount.account_number')
                    ->label(__('Account Number'))
                    ->copyable()
                    ->copyMessage(__("Account Number Copied!")),
                Tables\Columns\TextColumn::make('created_at')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        "pending" => __("Pending"),
                        "approved" => __("Approved"),
                        "rejected" => __("Rejected"),
                    ])
                    ->default('pending'),
            ])
            ->actions([
                // Approve Action
                Action::make('approve')
                    ->label(__('Approve'))
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(function ($record) {
                        DB::transaction(function () use ($record) {
                            // Ensure the withdrawal is in a "pending" state
                            if ($record->status !== 'pending') {
                                throw new \Exception(__('This withdrawal request has already been processed.'));
                            }
                
                            // Update the status to "approved"
                            $record->status = 'approved';
                            $record->save();
                
                            // Deduct the withdrawal amount from the user's balance
                            $user = $record->user;
                            $withdrawalAmount = $record->amount;
                
                            if ($user->balance < $withdrawalAmount) {
                                throw new \Exception(__('The user does not have sufficient balance to complete this withdrawal.'));
                            }
                
                            $user->balance -= $withdrawalAmount;
                            $user->save();
                
                            // Send notification to the user
                            $title = __('Withdrawal Approved');
                            $body = __('Your withdrawal request has been approved. The amount has been deducted from your balance.');
                            $userId = $record->user->id;
                
                            // Use the PushNotificationTrait
                            // app(PushNotificationTrait::class)->pushNotification($title, $body, $userId);
                        });
                    })
                    ->requiresConfirmation(), // Add confirmation dialog
            
                // Reject Action
                Action::make('reject')
                    ->label(__('Reject'))
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->action(function ($record) {
                        // Update the status to "rejected"
                        $record->status = 'rejected';
                        $record->save();
    
                        // Send notification to the user
                        $title = __('Withdrawal Rejected');
                        $body = __('Your withdrawal request has been rejected.');
                        $userId = $record->user->id;
    
                        // Use the PushNotificationTrait
                        app(PushNotificationTrait::class)->pushNotification($title, $body, $userId);
                    })
                    ->requiresConfirmation(), // Add confirmation dialog
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
            'index' => Pages\ListWithdraws::route('/'),
            // 'create' => Pages\CreateWithdraw::route('/create'),
            // 'edit' => Pages\EditWithdraw::route('/{record}/edit'),
        ];
    }
}
