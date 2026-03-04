<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ConsumerAccount;
use App\Models\Billing;
use App\Models\AccountantBilling;
use App\Models\Notice;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use App\Mail\TwoFactorCodeMailConsumer;

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

        // Check if user is locked out
        $lockoutKey = 'login_lockout_' . $request->ip();
        $attemptsKey = 'login_attempts_' . $request->ip();
        
        if (Cache::has($lockoutKey)) {
            $remainingTime = Cache::get($lockoutKey) - time();
            return response()->json([
                'success' => false,
                'locked' => true,
                'message' => 'Too many failed login attempts. Please try again later.',
                'remaining_time' => $remainingTime
            ], 429);
        }

        // Find the consumer account with consumer relationship
        $account = ConsumerAccount::with('consumer')->where('username', $credentials['username'])->first();

        if (!$account) {
            $this->handleFailedLogin($request);
            return response()->json([
                'success' => false,
                'message' => 'The provided credentials do not match our records.',
                'attempts' => Cache::get($attemptsKey, 0)
            ], 401);
        }

        // Verify the password
        if (!Hash::check($credentials['password'], $account->password)) {
            $this->handleFailedLogin($request);
            return response()->json([
                'success' => false,
                'message' => 'The provided password is incorrect.',
                'attempts' => Cache::get($attemptsKey, 0)
            ], 401);
        }
        
        // Reset attempts on successful login
        Cache::forget($attemptsKey);
        
        // Generate 2FA code
        $account->generateTwoFactorCode();
        
        // Send 2FA code via email
        Mail::to($account->email)->send(new TwoFactorCodeMailConsumer($account->two_factor_code));
        
        // Store account ID in session for verification
        session(['2fa_account_id' => $account->id]);
        
        // Return success response with 2FA flag
        return response()->json([
            'success' => true,
            'requires_2fa' => true,
            'message' => 'Login successful. Please verify with 2FA.',
            'account_id' => $account->id
        ]);
    }
    
    // Handle failed login attempts
    private function handleFailedLogin(Request $request)
    {
        $attemptsKey = 'login_attempts_' . $request->ip();
        $lockoutKey = 'login_lockout_' . $request->ip();
        
        // Increment attempts
        $attempts = Cache::increment($attemptsKey);
        
        // Set expiration for attempts counter (5 minutes)
        Cache::put($attemptsKey, $attempts, 300);
        
        // Lock out after 3 attempts for 30 seconds
        if ($attempts >= 3) {
            $lockoutTime = time() + 30; // 30 seconds from now
            Cache::put($lockoutKey, $lockoutTime, 30);
        }
    }
    
    // Verify 2FA code
    public function verify2FA(Request $request)
    {
        $request->validate([
            'two_factor_code' => 'required|digits:6',
        ]);
        
        $accountId = session('2fa_account_id');
        if (!$accountId) {
            return response()->json([
                'success' => false,
                'message' => 'Session expired. Please try again.'
            ]);
        }
        
        $account = ConsumerAccount::find($accountId);
        if (!$account) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid session. Please try again.'
            ]);
        }
        
        if (!$account->verifyTwoFactorCode($request->two_factor_code)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired verification code.'
            ]);
        }
        
        // Reset 2FA code
        $account->resetTwoFactorCode();
        
        // Clear session
        session()->forget('2fa_account_id');
        
        // Log in the user
        Auth::guard('consumer')->login($account);
        
        // Return success response
        return response()->json([
            'success' => true,
            'message' => 'Authentication successful. Redirecting to dashboard...',
            // Use an explicit path because "consumer.dashboard" is defined multiple times in routes/web.php.
            'redirect' => url('/consumer/dashboard')
        ]);
    }
    
    // Resend 2FA code
    public function resend2FA(Request $request)
    {
        $accountId = session('2fa_account_id');
        if (!$accountId) {
            return response()->json(['success' => false, 'message' => 'Session expired. Please try again.']);
        }
        
        $account = ConsumerAccount::find($accountId);
        if (!$account) {
            return response()->json(['success' => false, 'message' => 'Invalid session. Please try again.']);
        }
        
        // Generate new 2FA code
        $account->generateTwoFactorCode();
        
        // Send 2FA code via email
        Mail::to($account->email)->send(new TwoFactorCodeMailConsumer($account->two_factor_code));
        
        return response()->json(['success' => true, 'message' => 'A new verification code has been sent to your email.']);
    }
    
    // Dashboard method (for when user is already logged in)
    public function dashboard()
    {
        // Check if consumer is authenticated
        if (!Auth::guard('consumer')->check()) {
            return redirect('/consumer-portal');
        }
        
        $account = Auth::guard('consumer')->user();
        $consumer = $account->consumer;

        AccountantBilling::applyAutomaticOverduePenalties($consumer->id);
        
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

    public function notifications(Request $request)
    {
        $consumerId = Auth::guard('consumer')->user()->consumer->id;
        $loadAll = $request->boolean('all');
        $limit = (int) $request->input('limit', 20);
        $limit = max(1, min($limit, 50));

        $baseQuery = Notification::where('consumer_id', $consumerId)->orderBy('created_at', 'desc');
        $notificationsQuery = clone $baseQuery;
        if (!$loadAll) {
            $notificationsQuery->limit($limit);
        }

        $notifications = $notificationsQuery->get()->map(function ($notification) {
            return [
                'id' => $notification->id,
                'title' => $notification->title,
                'message' => $notification->message,
                'type' => $notification->type,
                'is_read' => (bool) $notification->is_read,
                'created_at' => $notification->created_at ? $notification->created_at->toIso8601String() : null,
                'time_ago' => $notification->created_at ? $notification->created_at->diffForHumans() : 'Just now',
            ];
        });

        $unreadCount = (clone $baseQuery)->where('is_read', false)->count();

        return response()->json([
            'success' => true,
            'unread_count' => $unreadCount,
            'notifications' => $notifications,
        ]);
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
