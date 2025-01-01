<?php

namespace App\Http\Controllers\User;

use App\Events\LiveChat;
use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\Message;
use App\Traits\PushNotificationTrait;
use Illuminate\Http\Request;

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

            $user = $request->user();

            if ($user->id == $request->receiver_id) {
                return response()->json([
                    'success' => false,
                    "message" => "لا يمكنك ارسال رسالة إلى نفسك"
                ], 400);
            }
            $chat = Chat::where('owner_id', $request->receiver_id)
                ->where('user_id', $user->id)->firstOrCreate([
                    'user_id' => $user->id,
                    'owner_id' => $request->receiver_id,
                ]);

            $message = Message::create([
                'sender_type' => "user",
                'chat_id' => $chat->id,
                'message' => $request->message,
                'created_at' => now()
            ]);

            broadcast(new LiveChat($message))->toOthers();

            $this->pushNotification(
                ' لديك رسالة جديدة!',
                "مرحباً، لقد تلقيت رسالة جديدة من {$user->name}.",
                $request->receiver_id,
            );

            return responseApi(200, 'Message sent successfully');
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
        $chats = Chat::where('user_id', $user->id)->with('owner')
            ->withCount(['messages as unseen_messages_count' => function ($query) {
                $query->where('seen', false)->where('sender_type', 'owner'); // Count only unseen messages
            }])
            ->with(['messages' => function ($query) {
                $query->latest(); // Get the latest message
            }])
            ->get()
            ->sortByDesc(function ($chat) {
                return optional($chat->messages->first())->created_at; // Order by latest message
            })
            ->values();

        return response()->json([
            "success" => true,
            "chats" => $chats
        ], 200);
    }

    public function getMessages($id)
    {

        $user = auth()->user();

        $chat = Chat::where('id', $id)
            ->where('user_id', $user->id)
            ->with('owner')
            ->withCount(['messages as unseen_messages_count' => function ($query) {
                $query->where('seen', false)->where('sender_type', 'owner'); // Count only unseen messages
            }])
            ->with(['messages' => function ($query) {
                $query->latest(); // Get the latest message
            }])
            ->first();
        if (!$chat) {
            return response()->json([
                "success" => false,
                "message" => "لم يتم العثور على الدردشة"
            ], 404);
        }
        return response()->json([
            "success" => true,
            "messages" => $chat
        ], 200);
    }

    public function seenMessages($id)
    {
        $user = auth()->user();
        $messages = Message::where('chat_id', $id)
            ->where('sender_type', 'owner')
            ->where('seen', false)->update(['seen' => true]);
        return response()->json([
            "success" => true,
            "message" => "تم تحديث الرسائل"
        ], 200);
    }

    public function delete($id)
    {
        $user = auth()->user();
        $chat = Chat::where('id', $id)->where('user_id', $user->id)->first();
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
        ], 200);
    }

    public function muteChat($id)
    {
        $user = auth()->user();
        $chat = Chat::where('id', $id)->where('user_id', $user->id)->first();
        if (!$chat) {
            return response()->json([
                "success" => false,
                "message" => "لم يتم العثور على الدردشة"
            ], 404);
        }
        $chat->muted_for_user = true;
        $chat->save();
        return response()->json([
            "success" => true,
            "message" => "تم تعطيل الاشعارات في الدردشة"
        ], 200);
    }
}
