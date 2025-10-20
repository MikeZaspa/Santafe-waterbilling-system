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
        try {
            Log::info('Password reset request received', ['email' => $request->email]);

            $request->validate([
                'email' => 'required|email|exists:admins,email'
            ], [
                'email.exists' => 'No account found with this email address.',
                'email.email' => 'Please enter a valid email address.'
            ]);

            $email = $request->email;
            
            // Generate reset token
            $token = Str::random(64);
            
            // Store token in database
            DB::table('password_resets')->updateOrInsert(
                ['email' => $email],
                [
                    'token' => Hash::make($token),
                    'created_at' => Carbon::now()
                ]
            );

            // Build reset URL - SIMPLE VERSION
            $resetUrl = url(route('password.reset.form', [
                'token' => $token, 
                'email' => $email
            ], false));

            Log::info('Reset URL generated', ['url' => $resetUrl]);

            // SIMPLE EMAIL TEST - Remove complex mail for now
            try {
                Mail::send('emails.password-reset', [
                    'resetUrl' => $resetUrl,
                    'email' => $email
                ], function ($message) use ($email) {
                    $message->to($email)
                            ->subject('Santa Fe Water - Password Reset Request');
                });

                Log::info('Password reset email sent successfully');
                
            } catch (\Exception $mailException) {
                Log::error('Mail sending failed: ' . $mailException->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send email. Please contact support.'
                ], 500);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Password reset link has been sent to your email!'
            ], 200);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Validation error in password reset', ['errors' => $e->errors()]);
            return response()->json([
                'success' => false,
                'message' => $e->errors()['email'][0] ?? 'Validation error'
            ], 422);
            
        } catch (\Exception $e) {
            Log::error('Password reset error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'System error. Please try again later.'
            ], 500);
        }
    }

    public function showResetForm(Request $request, $token = null)
    {
        try {
            // Get token and email from query parameters or route
            $token = $token ?: $request->query('token');
            $email = $request->query('email');
            
            Log::info('Reset form accessed', ['token' => $token, 'email' => $email]);

            if (!$token || !$email) {
                return redirect()->route('admin-login')->with('error', 'Invalid reset link.');
            }

            // Verify token exists and is valid
            $resetRecord = DB::table('password_resets')
                            ->where('email', $email)
                            ->first();

            if (!$resetRecord) {
                return redirect()->route('admin-login')->with('error', 'Invalid or expired reset token.');
            }

            // Check if token is expired (1 hour)
            if (Carbon::parse($resetRecord->created_at)->addHour()->isPast()) {
                DB::table('password_resets')->where('email', $email)->delete();
                return redirect()->route('admin-login')->with('error', 'Reset token has expired.');
            }

            return view('auth.reset-password', compact('token', 'email'));
            
        } catch (\Exception $e) {
            Log::error('Reset form error: ' . $e->getMessage());
            return redirect()->route('admin-login')->with('error', 'Invalid reset link.');
        }
    }

    public function resetPassword(Request $request)
    {
        try {
            $request->validate([
                'token' => 'required',
                'email' => 'required|email',
                'password' => 'required|min:8|confirmed',
            ]);

            // Verify token exists
            $resetRecord = DB::table('password_resets')
                            ->where('email', $request->email)
                            ->first();

            if (!$resetRecord) {
                return back()->withErrors(['email' => 'Invalid reset token.']);
            }

            // Check if token is expired
            if (Carbon::parse($resetRecord->created_at)->addHour()->isPast()) {
                DB::table('password_resets')->where('email', $request->email)->delete();
                return back()->withErrors(['email' => 'Reset token has expired.']);
            }

            // Verify token matches
            if (!Hash::check($request->token, $resetRecord->token)) {
                return back()->withErrors(['email' => 'Invalid reset token.']);
            }

            // Update password
            $admin = Admin::where('email', $request->email)->first();
            
            if (!$admin) {
                return back()->withErrors(['email' => 'User not found.']);
            }

            $admin->password = Hash::make($request->password);
            $admin->save();

            // Delete used token
            DB::table('password_resets')->where('email', $request->email)->delete();

            return redirect()
                ->route('admin-login')
                ->with('success', 'Password reset successfully!');

        } catch (\Exception $e) {
            Log::error('Password reset error: ' . $e->getMessage());
            return back()->withErrors(['email' => 'An error occurred. Please try again.']);
        }
    }
}