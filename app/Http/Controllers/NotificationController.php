<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function getNotifications(Request $request)
    {
        $consumerId = $request->get('consumer_id'); // For consumer dashboard
        $userId = Auth::id(); // For admin dashboard

        $notifications = Notification::with('consumer')
            ->when($consumerId, function($query) use ($consumerId) {
                return $query->where('consumer_id', $consumerId);
            })
            ->when($userId, function($query) use ($userId) {
                return $query->where('user_id', $userId);
            })
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        $unreadCount = Notification::when($consumerId, function($query) use ($consumerId) {
                return $query->where('consumer_id', $consumerId);
            })
            ->when($userId, function($query) use ($userId) {
                return $query->where('user_id', $userId);
            })
            ->unread()
            ->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount
        ]);
    }

    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    public function markAllAsRead(Request $request)
    {
        $consumerId = $request->get('consumer_id');
        $userId = Auth::id();

        Notification::when($consumerId, function($query) use ($consumerId) {
                return $query->where('consumer_id', $consumerId);
            })
            ->when($userId, function($query) use ($userId) {
                return $query->where('user_id', $userId);
            })
            ->unread()
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);

        return response()->json(['success' => true]);
    }

    public function clearAll(Request $request)
    {
        $consumerId = $request->get('consumer_id');
        $userId = Auth::id();

        Notification::when($consumerId, function($query) use ($consumerId) {
                return $query->where('consumer_id', $consumerId);
            })
            ->when($userId, function($query) use ($userId) {
                return $query->where('user_id', $userId);
            })
            ->delete();

        return response()->json(['success' => true]);
    }
}