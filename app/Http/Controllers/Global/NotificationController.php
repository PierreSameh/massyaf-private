<?php

namespace App\Http\Controllers\Global;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = AppNotification::where('user_id', auth()->id())
            ->orWhereNull('user_id')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $notifications
        ]);
    }

    public function markAsRead(AppNotification $notification)
    {
        // Check if the notification belongs to the authenticated user
        if ($notification->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => __('Unauthorized access')
            ], 403);
        }

        $notification->update(['read' => true]);

        return response()->json([
            'success' => true,
            'message' => __('Notification marked as read')
        ]);
    }


    public function markAllAsRead()
    {
        AppNotification::where('user_id', auth()->id())
            ->where('read', false)
            ->update(['read' => true]);

        return response()->json([
            'success' => true,
            'message' => __('All notifications marked as read')
        ]);
    }

    public function destroy(AppNotification $notification)
    {
        // Check if the notification belongs to the authenticated user
        if ($notification->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => __('Unauthorized access')
            ], 403);
        }

        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => __('Notification deleted successfully')
        ]);
    }

    public function destroyAll()
    {
        AppNotification::where('user_id', auth()->id())->delete();

        return response()->json([
            'success' => true,
            'message' => __('All notifications deleted successfully')
        ]);
    }
}
