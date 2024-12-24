<?php

namespace App\Http\Controllers\Chat\Owner;

use App\Models\Chat;
use App\Models\Message;
use App\Events\LiveChat;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LiveChatOwnerController extends Controller
{
    public function sendOwner(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:255',
            'reciver_id' => 'required|exists:users,id'
        ]);

        $chat = Chat::where('user_id', $request->user()->id)
        ->where('owner_id', $request->reciver_id)->firstOrCreate([
            'user_id' => $request->user()->id,
            'owner_id' => $request->reciver_id
        ]);

        $user = $request->user();
        $message = Message::create([
            'sender_type' => "owner",
            'chat_id' => $chat->id,
            'message' => $request->message,
        ]);

        if (!$message) {
            return responseApi(400, 'Failed to send message');
        }

        broadcast(new LiveChat($message))->toOthers();
        return responseApi(200, 'Message sent successfully');
    }
}
