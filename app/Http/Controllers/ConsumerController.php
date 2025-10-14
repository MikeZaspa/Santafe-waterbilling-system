<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ConsumerAccount;

class ConsumerController extends Controller
{
    // Your controller methods here
    public function history()
    {
        $consumer = auth()->guard('consumer')->user();
        $history = []; // Add your logic to get consumer history
        
        return view('auth.consumer-history', compact('consumer', 'history'));
    }

    public function getNotifications(Request $request)
    {
        $consumer = Auth::guard('consumer')->user(); // Adjust based on your auth guard
        
        $notifications = $consumer->notifications()
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return response()->json([
            'success' => true,
            'notifications' => $notifications
        ]);
    }

    public function getUnreadCount(Request $request)
    {
        $consumer = Auth::guard('consumer')->user();
        
        $count = $consumer->unreadNotifications()->count();

        return response()->json([
            'success' => true,
            'count' => $count
        ]);
    }

    public function markNotificationsRead(Request $request)
    {
        $consumer = Auth::guard('consumer')->user();
        
        if ($request->has('all') && $request->all) {
            $consumer->unreadNotifications()->update(['read_at' => now()]);
        } elseif ($request->has('notification_id')) {
            $consumer->notifications()
                ->where('id', $request->notification_id)
                ->update(['read_at' => now()]);
        }

        return response()->json(['success' => true]);
    }
}