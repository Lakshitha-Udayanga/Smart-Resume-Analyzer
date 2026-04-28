<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Carbon\Carbon;

class NotificationController extends Controller
{
    /**
     * Get the latest unread notifications.
     */
    public function getLatestNotifications()
    {
        $notifications = Notification::where('is_read', false)
            ->latest()
            ->limit(20)
            ->get()
            ->map(function ($notif) {
                return [
                    'id' => $notif->id,
                    'type' => $notif->type,
                    'title' => $notif->title,
                    'message' => $notif->message,
                    'time' => $notif->created_at->diffForHumans(),
                    'created_at' => $notif->created_at,
                ];
            });

        return response()->json([
            'status' => 'success',
            'new_count' => $notifications->count(),
            'notifications' => $notifications
        ]);
    }

    /**
     * Mark a notification as read (clicked).
     */
    public function markAsRead($id)
    {
        $notification = Notification::find($id);

        if ($notification) {
            $notification->update(['is_read' => true]);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Notification marked as read'
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Notification not found'
        ], 404);
    }
}
