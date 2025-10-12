<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Admin;

class ForgotPasswordController extends Controller
{
    public function sendResetLink(Request $request)
{
    $request->validate(['email' => 'required|email']);

    Log::info('Password reset requested for: ' . $request->email);

    // Check if admin exists
    $admin = Admin::where('email', $request->email)->first();

    if (!$admin) {
        return response()->json([
            'success' => false,
            'message' => 'No account found with this email address.'
        ], 404);
    }

    // Generate reset token
    $token = Str::random(64);
    
    // Store token in database
    DB::table('password_resets')->updateOrInsert(
        ['email' => $request->email],
        [
            'token' => Hash::make($token),
            'created_at' => Carbon::now()
        ]
    );

     try {
        // Use route helper with query parameters
        $resetUrl = route('password.reset.form') . '?token=' . urlencode($token) . '&email=' . urlencode($request->email);
        
        // Send email with properly encoded URL
        Mail::send('emails.password-reset', ['resetUrl' => $resetUrl], function ($message) use ($request) {
            $message->to($request->email)
                    ->subject('Santa Fe Water - Password Reset Request');
        });

        Log::info('Password reset email sent successfully');
        
        return response()->json([
            'success' => true,
            'message' => 'Password reset link has been sent to your email!'
        ]);
        
    } catch (\Exception $e) {
        Log::error('Failed to send password reset email: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'Failed to send email. Please try again later.'
        ], 500);
    }
}

   public function showResetForm(Request $request)
{
    $token = $request->get('token');
    $email = $request->get('email');
    
    if (!$token || !$email) {
        abort(404, 'Reset token or email missing');
    }
    
    return view('auth.reset-password', compact('token', 'email'));
}

public function resetPassword(Request $request)
{
    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|min:8|confirmed',
    ]);

    Log::info('Password reset attempt', ['email' => $request->email]);

    // Verify token
    $resetRecord = DB::table('password_resets')
                    ->where('email', $request->email)
                    ->first();

    if (!$resetRecord) {
        return back()->withErrors(['email' => 'Invalid reset token.'])->withInput();
    }

    // Check if token is expired (1 hour)
    if (Carbon::parse($resetRecord->created_at)->addHour()->isPast()) {
        DB::table('password_resets')->where('email', $request->email)->delete();
        return back()->withErrors(['email' => 'Reset token has expired.'])->withInput();
    }

    // Verify token matches
    if (!Hash::check($request->token, $resetRecord->token)) {
        return back()->withErrors(['email' => 'Invalid reset token.'])->withInput();
    }

    // Update password
    $admin = Admin::where('email', $request->email)->first();
    if ($admin) {
        $admin->password = Hash::make($request->password);
        $admin->save();

        // Delete used token
        DB::table('password_resets')->where('email', $request->email)->delete();

        Log::info('Password reset successful for: ' . $request->email);

        return redirect()->route('admin.login')->with('success', 'Password reset successfully! You can now login with your new password.');
    }

    return back()->withErrors(['email' => 'User not found.'])->withInput();
}
}