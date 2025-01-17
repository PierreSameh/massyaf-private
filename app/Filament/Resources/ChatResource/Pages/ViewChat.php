<?php
namespace App\Filament\Resources\ChatResource\Pages;

use App\Filament\Resources\ChatResource;
use App\Models\Chat;
use Filament\Pages\Actions;
use Filament\Resources\Pages\Page;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;

class ViewChat extends Page
{
    protected static string $resource = ChatResource::class;

    protected static string $view = 'filament.resources.chat-resource.pages.view-chat';

    public Chat $record;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\EditAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Chat Details')
                    ->schema([
                        TextEntry::make('user.name')->label('User'),
                        TextEntry::make('owner.name')->label('Owner'),
                        TextEntry::make('created_at')->label('Created At')->dateTime(),
                    ])
                    ->columns(2),
            ]);
    }

    protected function getViewData(): array
    {
        // Load the chat messages with the sender information
        $messages = $this->record->messages()
            ->with(['chat.user', 'chat.owner'])
            ->orderBy('created_at', 'asc')
            ->get();

        return [
            'messages' => $messages,
        ];
    }
}