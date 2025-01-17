<?php

namespace App\Filament\Resources\UnitResource\Pages;

use App\Filament\Resources\UnitResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Pages\Actions\Action;
use Filament\Notifications\Notification;

class ViewUnit extends ViewRecord
{
    protected static string $resource = UnitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()->label(__('Ownership Documents')),
            // Activate Action
            Action::make('activate')
            ->label('Activate Unit')
            ->color('success')
            ->icon('heroicon-o-check-circle')
            ->requiresConfirmation()
            ->visible(fn () => $this->record->status === 'waiting' || $this->record->status === 'rejected')
            ->action(fn () => $this->activateUnit()),
        
            // Reject Action
            Action::make('reject')
                ->label('Reject Unit')
                ->color('danger')
                ->icon('heroicon-o-x-circle')
                ->requiresConfirmation()
                ->visible(fn () => $this->record->status === 'waiting' || $this->record->status === 'active')
                ->action(fn () => $this->rejectUnit()),
        ];
    }

    protected function activateUnit(): void
    {
        // Logic to activate the unit
        $this->record->update(['status' => 'active']);

        // Notify the user
        Notification::make()
            ->title('Unit Activated')
            ->body('The unit has been activated successfully.')
            ->success()
            ->send();
    }

    protected function rejectUnit(): void
    {
        // Logic to reject the unit
        $this->record->update(['status' => 'rejected']);

        // Notify the user
        Notification::make()
            ->title('Unit Rejected')
            ->body('The unit has been rejected successfully.')
            ->danger()
            ->send();
    }
}
