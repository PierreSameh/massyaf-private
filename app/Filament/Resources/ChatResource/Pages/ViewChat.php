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
use Illuminate\Contracts\Support\Htmlable;

class ViewChat extends Page
{
    
    protected static string $resource = ChatResource::class;

 
    public function getTitle(): string | Htmlable
    {
        return __('View Chat');
    }


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
                        TextEntry::make('user1.name')->label('User 1'),
                        TextEntry::make('user2.name')->label('User 2'),
                        TextEntry::make('created_at')->label('Created At')->dateTime(),
                    ])
                    ->columns(2),
            ]);
    }

    protected function getViewData(): array
    {
        // Load the chat messages with the sender information
        $messages = $this->record->messages()
            ->with(['chat.user1', 'chat.user2'])
            ->orderBy('created_at', 'asc')
            ->get();

        return [
            'messages' => $messages,
        ];
    }
}