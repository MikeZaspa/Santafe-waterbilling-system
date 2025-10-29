<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ConsumerAccount;
use App\Models\Billing;
use App\Models\Notice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ConsumerAuthController extends Controller
{
    // Show login form
    public function showLoginForm()
    {
        return view('auth.consumer-portal');
    }

    // Handle login request
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Find the consumer account with consumer relationship
        $account = ConsumerAccount::with('consumer')->where('username', $credentials['username'])->first();

        if (!$account) {
            return back()->withErrors([
                'username' => 'The provided credentials do not match our records.',
            ])->onlyInput('username');
        }

        // Verify the password
        if (!Hash::check($credentials['password'], $account->password)) {
            return back()->withErrors([
                'password' => 'The provided password is incorrect.',
            ])->onlyInput('username');
        }
        
        // Log in the user
        Auth::guard('consumer')->login($account);
        
        // Redirect to dashboard
        return redirect()->route('consumer.dashboard');
    }
    
    // Dashboard method (for when user is already logged in)
    public function dashboard()
    {
        // Check if consumer is authenticated
        if (!Auth::guard('consumer')->check()) {
            return redirect('/consumer/login');
        }
        
        $account = Auth::guard('consumer')->user();
        $consumer = $account->consumer;
        
        // Get the consumer's bills
        $bills = $consumer->billings()->with('consumer')->orderBy('created_at', 'desc')->get();
        
        // Calculate bill counts
        $paidCount = $bills->where('status', 'paid')->count();
        $unpaidCount = $bills->where('status', 'unpaid')->count();
        $overdueCount = $bills->where('status', 'overdue')->count();
        
        // Get notices for this consumer
        $notices = Notice::with('consumer')
            ->where('consumer_id', $consumer->id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Get notifications for this consumer
    $notifications = \App\Models\Notification::where('consumer_id', $consumer->id)
        ->orderBy('created_at', 'desc')
        ->get();
    
    return view('auth.consumer-login', [
        'consumer' => $consumer,
        'bills' => $bills,
        'notices' => $notices,
        'notifications' => $notifications,
        'paidCount' => $paidCount,
        'unpaidCount' => $unpaidCount,
        'overdueCount' => $overdueCount
    ]);
    }
    
    // Logout method
    public function logout(Request $request)
    {
        Auth::guard('consumer')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/consumer-portal');
    }

    // Add these methods to ConsumerAuthController
public function markNotificationAsRead($id)
{
    $notification = Notification::findOrFail($id);
    
    // Check if the notification belongs to the authenticated consumer
    if ($notification->consumer_id !== Auth::guard('consumer')->user()->consumer->id) {
        return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
    }
    
    $notification->is_read = true;
    $notification->save();
    
    return response()->json(['success' => true]);
}

public function markAllNotificationsAsRead()
{
    $consumerId = Auth::guard('consumer')->user()->consumer->id;
    
    Notification::where('consumer_id', $consumerId)
        ->where('is_read', false)
        ->update(['is_read' => true]);
    
    return response()->json(['success' => true]);
}

public function createNotification(Request $request)
{
    $consumerId = Auth::guard('consumer')->user()->consumer->id;
    
    Notification::create([
        'consumer_id' => $consumerId,
        'title' => $request->title,
        'message' => $request->message,
        'type' => $request->type,
        'is_read' => false
    ]);
    
    return response()->json(['success' => true]);
}
}