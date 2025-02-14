<?php

namespace App\Http\Controllers\Global;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Chat;
use App\Models\Message;
use App\Traits\PushNotificationTrait;
use Pusher\Pusher;

class ChatController extends Controller
{
    use PushNotificationTrait;

    public function sendMessage(Request $request)
    {
        try {
            $request->validate([
                'message' => 'required|string|max:255',
                'receiver_id' => 'required|exists:users,id'
            ]);

            $user = auth()->user();
            if ($user->id == $request->receiver_id) {
                return response()->json([
                    'success' => false,
                    "message" => "لا يمكنك ارسال رسالة إلى نفسك"
                ], 400);
            }

            // Ensure chat exists between these two users
            $chat = Chat::where(function ($query) use ($user, $request) {
                $query->where('user1_id', $user->id)->where('user2_id', $request->receiver_id);
            })->orWhere(function ($query) use ($user, $request) {
                $query->where('user1_id', $request->receiver_id)->where('user2_id', $user->id);
            })->first();

            if (!$chat) {
                $chat = Chat::create([
                    'user1_id' => $user->id,
                    'user2_id' => $request->receiver_id,
                ]);
            }

            $message = Message::create([
                'sender_id' => $user->id,
                'chat_id' => $chat->id,
                'message' => $request->message,
                'seen' => false,
                'created_at' => now()
            ]);

            // Push notification
            $pusher = new Pusher(env('PUSHER_APP_KEY'), env('PUSHER_APP_SECRET'), env('PUSHER_APP_ID'), ['cluster' => env('PUSHER_APP_CLUSTER')]);

            $pusher->trigger(
                "channel_" . $request->receiver_id,
                "chat",
                $message
            );

            $this->pushNotification(
                ' لديك رسالة جديدة!',
                "مرحباً، لقد تلقيت رسالة جديدة من {$user->name}.",
                $request->receiver_id
            );

            return response()->json([
                'success' => true,
                'message' => "Message sent successfully"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                "message" => "حدث خطأ في الخادم",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    public function getChats()
    {
        $user = auth()->user();
        $chats = Chat::where('user1_id', $user->id)
            ->orWhere('user2_id', $user->id)
            ->with(['messages' => function ($query) {
                $query->latest(); // Get the latest message
            }])
            ->with(['user1', 'user2'])
            ->get()
            ->sortByDesc(function ($chat) {
                return optional($chat->messages->first())->created_at;
            })
            ->values();

        return response()->json([
            "success" => true,
            "chats" => $chats
        ]);
    }

    public function getMessages($chat_id)
    {
        $user = auth()->user();
        $chat = Chat::where('id', $chat_id)
            ->where(function ($query) use ($user) {
                $query->where('user1_id', $user->id)
                    ->orWhere('user2_id', $user->id);
            })
            ->with('messages')
            ->first();

        if (!$chat) {
            return response()->json([
                "success" => false,
                "message" => "لم يتم العثور على الدردشة"
            ], 404);
        }

        return response()->json([
            "success" => true,
            "messages" => $chat->messages
        ]);
    }

    public function seenMessages($chat_id)
    {
        $user = auth()->user();
        Message::where('chat_id', $chat_id)
            ->where('sender_id', '!=', $user->id)
            ->where('seen', false)
            ->update(['seen' => true]);

        return response()->json([
            "success" => true,
            "message" => "تم تحديث الرسائل"
        ]);
    }

    public function deleteChat($chat_id)
    {
        $user = auth()->user();
        $chat = Chat::where('id', $chat_id)
            ->where(function ($query) use ($user) {
                $query->where('user1_id', $user->id)
                    ->orWhere('user2_id', $user->id);
            })
            ->first();

        if (!$chat) {
            return response()->json([
                "success" => false,
                "message" => "لم يتم العثور على الدردشة"
            ], 404);
        }

        $chat->delete();
        return response()->json([
            "success" => true,
            "message" => "تم حذف الدردشة"
        ]);
    }
}
