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
use App\Models\Accountant;
use App\Models\ConsumerAccount;
use App\Models\Plumber;

class ForgotPasswordController extends Controller
{
    private const ACCOUNT_TYPES = ['admin', 'accountant', 'consumer', 'plumber'];

    public function sendResetLink(Request $request)
    {
        try {
            Log::info('Password reset request received', [
                'email' => $request->email,
                'account_type' => $request->account_type,
            ]);

            $request->validate([
                'email' => 'required|email',
                'account_type' => 'nullable|in:' . implode(',', self::ACCOUNT_TYPES),
            ], [
                'email.email' => 'Please enter a valid email address.',
                'account_type.in' => 'Invalid account type.',
            ]);

            $account = $this->resolveAccount(
                $request->email,
                $request->account_type
            );

            if (!$account) {
                return response()->json([
                    'success' => false,
                    'message' => 'No account found with this email address.',
                ], 422);
            }

            $email = $account['email'];
            $accountType = $account['type'];
            $resetKey = $this->buildResetKey($accountType, $email);
            
            // Generate reset token
            $token = Str::random(64);
            
            // Store token in database
            DB::table('password_resets')->updateOrInsert(
                ['email' => $resetKey],
                [
                    'token' => Hash::make($token),
                    'created_at' => Carbon::now()
                ]
            );

            // Build reset URL - SIMPLE VERSION
            $resetUrl = url(route('password.reset.form', [
                'token' => $token,
                'email' => $email,
                'account_type' => $accountType,
                'key' => $resetKey,
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
            $accountType = $request->query('account_type');
            $resetKey = $request->query('key');
            
            Log::info('Reset form accessed', [
                'token' => $token,
                'email' => $email,
                'account_type' => $accountType,
                'key' => $resetKey,
            ]);

            if (!$token) {
                return redirect()->route('admin-login')->with('error', 'Invalid reset link.');
            }

            $account = null;

            if ($resetKey) {
                $parsed = $this->parseResetKey($resetKey);
                if ($parsed) {
                    $account = $this->resolveAccount($parsed['email'], $parsed['type']);
                }
            }

            if (!$account && $email) {
                $account = $this->resolveAccount($email, $accountType);
            }

            if (!$account) {
                return redirect()->route('admin-login')->with('error', 'Invalid reset link.');
            }

            $email = $account['email'];
            $accountType = $account['type'];
            $resetKey = $this->buildResetKey($accountType, $email);

            // Verify token exists and is valid
            $resetRecord = DB::table('password_resets')
                            ->where('email', $resetKey)
                            ->first();

            if (!$resetRecord) {
                return redirect()->route('admin-login')->with('error', 'Invalid or expired reset token.');
            }

            // Check if token is expired (1 hour)
            if (Carbon::parse($resetRecord->created_at)->addHour()->isPast()) {
                DB::table('password_resets')->where('email', $resetKey)->delete();
                return redirect()->route('admin-login')->with('error', 'Reset token has expired.');
            }

            $loginUrl = $this->loginUrlForType($accountType);
            return view('auth.reset-password', compact('token', 'email', 'accountType', 'resetKey', 'loginUrl'));
            
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
                'account_type' => 'nullable|in:' . implode(',', self::ACCOUNT_TYPES),
                'reset_key' => 'nullable|string',
                'password' => 'required|min:8|confirmed',
            ]);

            $resetKey = $request->reset_key;
            $parsed = null;

            if ($resetKey) {
                $parsed = $this->parseResetKey($resetKey);
                if (!$parsed) {
                    return back()->withErrors(['email' => 'Invalid reset link.']);
                }
            } elseif ($request->account_type) {
                $resetKey = $this->buildResetKey($request->account_type, $request->email);
            } else {
                // Legacy fallback for old links that used plain email.
                $resetKey = $request->email;
            }

            // Verify token exists
            $resetRecord = DB::table('password_resets')
                            ->where('email', $resetKey)
                            ->first();

            if (!$resetRecord) {
                return back()->withErrors(['email' => 'Invalid reset token.']);
            }

            // Check if token is expired
            if (Carbon::parse($resetRecord->created_at)->addHour()->isPast()) {
                DB::table('password_resets')->where('email', $resetKey)->delete();
                return back()->withErrors(['email' => 'Reset token has expired.']);
            }

            // Verify token matches
            if (!Hash::check($request->token, $resetRecord->token)) {
                return back()->withErrors(['email' => 'Invalid reset token.']);
            }

            $account = null;

            if ($parsed) {
                $account = $this->resolveAccount($parsed['email'], $parsed['type']);
            } elseif ($request->account_type) {
                $account = $this->resolveAccount($request->email, $request->account_type);
            } else {
                $account = $this->resolveAccount($request->email);
            }

            if (!$account) {
                return back()->withErrors(['email' => 'User not found.']);
            }

            $accountType = $account['type'];

            // Update password
            $account['model']->password = Hash::make($request->password);
            $account['model']->save();

            // Delete used token
            DB::table('password_resets')->where('email', $resetKey)->delete();

            return redirect()
                ->to($this->loginUrlForType($accountType))
                ->with('success', 'Password reset successfully!');

        } catch (\Exception $e) {
            Log::error('Password reset error: ' . $e->getMessage());
            return back()->withErrors(['email' => 'An error occurred. Please try again.']);
        }
    }

    private function resolveAccount(string $email, ?string $preferredType = null): ?array
    {
        if ($preferredType) {
            $account = $this->findAccountByType($preferredType, $email);
            if (!$account) {
                return null;
            }

            return [
                'type' => $preferredType,
                'email' => $account->email,
                'model' => $account,
            ];
        }

        foreach (self::ACCOUNT_TYPES as $type) {
            $account = $this->findAccountByType($type, $email);
            if ($account) {
                return [
                    'type' => $type,
                    'email' => $account->email,
                    'model' => $account,
                ];
            }
        }

        return null;
    }

    private function findAccountByType(string $type, string $email): ?object
    {
        $normalizedEmail = mb_strtolower(trim($email));

        return match ($type) {
            'admin' => Admin::whereRaw('LOWER(email) = ?', [$normalizedEmail])->first(),
            'accountant' => Accountant::whereRaw('LOWER(email) = ?', [$normalizedEmail])->first(),
            'consumer' => ConsumerAccount::whereRaw('LOWER(email) = ?', [$normalizedEmail])->first(),
            'plumber' => Plumber::whereRaw('LOWER(email) = ?', [$normalizedEmail])->first(),
            default => null,
        };
    }

    private function buildResetKey(string $type, string $email): string
    {
        return $type . '|' . mb_strtolower(trim($email));
    }

    private function parseResetKey(?string $resetKey): ?array
    {
        if (!$resetKey || strpos($resetKey, '|') === false) {
            return null;
        }

        [$type, $email] = explode('|', $resetKey, 2);
        if (!in_array($type, self::ACCOUNT_TYPES, true) || !$email) {
            return null;
        }

        return [
            'type' => $type,
            'email' => $email,
        ];
    }

    private function loginUrlForType(string $type): string
    {
        return match ($type) {
            'accountant' => url('/accountant-login'),
            'consumer' => url('/consumer-portal'),
            'plumber' => url('/plumber-login'),
            default => route('admin-login'),
        };
    }
}
