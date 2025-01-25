<?php

namespace App\Console\Commands;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Console\Command;
use App\Models\Chat;
use App\Models\Message;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Actions\Action;

class CheckUnansweredChats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-unanswered-chats';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $unansweredInterval = Setting::first()->chat_timer ?: 15;
    
        $unansweredChats = Chat::whereHas('messages', function ($query) use ($unansweredInterval) {
            $query->where('sender_type', 'user')
                  ->where('seen', 0)
                  ->where('admin_notified', 0)
                  ->where('created_at', '<=', now()->subMinutes($unansweredInterval));
        })
        ->whereDoesntHave('messages', function ($query) use ($unansweredInterval) {
            $query->where('sender_type', 'owner')
                  ->where('created_at', '>=', now()->subMinutes($unansweredInterval));
        })
        ->get();
    
        \Log::info('Unanswered Chats Count: ' . $unansweredChats->count());
    
        // Get all admin users
        $adminUsers = User::where('type', 'admin')->get();
        \Log::info('Admin Users Count: ' . $adminUsers->count());
    
        // Additional logging to check if loop is being entered
        if ($unansweredChats->isEmpty()) {
            \Log::info('No unanswered chats found to process');
            return;
        }
    
        foreach ($unansweredChats as $chat) {
            \Log::info('Processing unanswered chat: ' . $chat->id);
    
            $lastUserMessage = Message::where('chat_id', $chat->id)
                ->where('sender_type', 'user')
                ->where('seen', 0)
                ->where('created_at', '<=', now()->subMinutes($unansweredInterval))
                ->orderBy('created_at', 'desc')
                ->first();
    
            if (!$lastUserMessage) {
                \Log::info('No valid last user message found for chat: ' . $chat->id);
                continue;
            }
    
            foreach ($adminUsers as $admin) {
                \Log::info('Sending notification to admin: ' . $admin->id . ' for chat: ' . $chat->id);
                
                try {
                    $chat->admin_notified = true;
                    $chat->save();
                    Notification::make()
                        ->icon('heroicon-o-exclamation-circle')
                        ->iconColor('warning')
                        ->title('تنبيه محادثة لم يتم الرد عليها')
                        ->body("المحادثة رقم " . $chat->id . " بها رسالة لم يتم الرد عليها من قبل  " . $lastUserMessage->sender_name . " ارسلت في " . $lastUserMessage->created_at)
                        ->actions([
                            Action::make('view')
                            ->label(__('View'))
                            ->url('/admin/chats/' . $chat->id) // Direct string, not a closure
                            ->openUrlInNewTab(false), // Ensure it doesn't open in a new tab,
                        ])
                        ->sendToDatabase($admin);
                } catch (\Exception $e) {
                    \Log::error('Notification sending failed: ' . $e->getMessage());
                }
            }
        }
    
        $this->info('Unanswered chats checked and notifications sent.');
    }
}
