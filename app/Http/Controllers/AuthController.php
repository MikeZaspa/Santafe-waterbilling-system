<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Services\AdminLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationCodeMail;
use App\Mail\TwoFactorCodeMail;

class AuthController extends Controller
{
protected $adminLogService;

public function __construct(AdminLogService $adminLogService)
{
$this->adminLogService = $adminLogService;
}

public function showRegistrationForm()
{
return view('auth.admin-register');
}

public function showLoginForm()
{

return view('auth.admin-login');
}

public function showDashboard()
{

// Check if admin is authenticated
if (!Auth::guard('admin')->check()) {
return redirect()->route('admin-login');
}

return view('auth.admin-dashboard');
}

public function showConsumerForm()
{
return view('auth.admin-consumer');
}

public function showPLumber()
{
return view('auth.admin-plumber');
}

public function showPlumberConsumerForm()
{
return view('auth.admin-plumber-consumer');
}

public function showPlumberForm()
{
return view('auth.admin-plumber-dashboard');
}

public function showAccountantForm()
{
return view('auth.admin-accountant-dashboard');
}

public function showAccountantConsumerForm()
{
return view('auth.admin-accountant-consumer');
}

public function showRatesForm()
{
return view('auth.water-rates');
}

public function showManageConsumerForm()
{
return view('auth.admin-consumer-form');
}

public function showAccountantreportsForm()
{
return view('auth.admin-accountant-reports');
}

public function showConsumerPortalForm()
{
return view('auth.consumer-portal');
}

public function showHistoryForm()
{
return view('auth.consumer-history');
}

public function showPaymentForm()
{
return view('auth.consumer-dashboard');
}

public function showPaidForm()
{
return view('auth.consumer-paid');
}

public function showOnlineBillingForm()
{
return view('auth.online-billing');
}

public function showPaymentVerificationForm()
{
return view('auth.paymentVerificationSection');
}

public function showAdminAccountant()
{
return view('auth.admin-accountant');
}

public function showDisconnectionForm()
{
return view('auth.admin-plumber-disconnection');
}

public function showInformation()
{
return view('auth.consumer-information');
}

public function showMainForm()
{
return view('auth.main-form');
}

public function showNotice()
{
return view('auth.admin-accountant-notice');
}

public function showConsumerNotice()
{
return view('auth.consumer/consumer-notice');
}

public function consumerDashboard()
{
return view('auth.dashboard-consumer');
}

public function consumerprofile()
{
return view('auth.consumer-profile');
}
public function Showadminlogs()
{
return view('auth.admin-logs');
}
public function ShowAnnouncement()
{
return view('auth.admin-announcement');
}
public function showVerifyForm()
{
$email = session('verification_email');

if (!$email) {
return redirect()->route('admin-register')->with('error', 'Please register first.');
}

return view('auth.verify', compact('email'));
}

public function register(Request $request)
{
$validator = Validator::make($request->all(), [
'first_name' => 'required|string|max:255',
'middle_name' => 'nullable|string|max:255',
'last_name' => 'required|string|max:255',
'birthdate' => 'required|date',
'gender' => 'required|in:male,female,other',
'role' => 'required|in:admin,accountant,plumber',
'email' => 'required|string|email|max:255|unique:admins',
'contact_number' => 'required|string|max:20',
'password' => ['required', 'confirmed', Password::min(8)
->letters()
->mixedCase()
->numbers()
->symbols()],
]);

if ($validator->fails()) {
return redirect()->back()
->withErrors($validator)
->withInput();
}

// Generate verification code
$verificationCode = rand(100000, 999999);

$admin = Admin::create([
'first_name' => $request->first_name,
'middle_name' => $request->middle_name,
'last_name' => $request->last_name,
'birthdate' => $request->birthdate,
'gender' => $request->gender,
'role' => $request->role,
'email' => $request->email,
'contact_number' => $request->contact_number,
'password' => Hash::make($request->password),
'verification_code' => $verificationCode,
'verification_code_sent_at' => now(),
]);

// Send verification email
Mail::to($admin->email)->send(new VerificationCodeMail($verificationCode));

// Store email in session for verification
$request->session()->put('verification_email', $admin->email);

return redirect()->route('verify')
->with('success', 'Registration successful! Please check your email for the verification code.');
}

public function verifyCode(Request $request)
{
$request->validate([
'digit1' => 'required|digits:1',
'digit2' => 'required|digits:1',
'digit3' => 'required|digits:1',
'digit4' => 'required|digits:1',
'digit5' => 'required|digits:1',
'digit6' => 'required|digits:1',
'email' => 'required|email'
]);

$code = (string) (
$request->digit1 .
$request->digit2 .
$request->digit3 .
$request->digit4 .
$request->digit5 .
$request->digit6
);

$user = Admin::where('email', $request->email)->first();

if (!$user) {
return $this->jsonOrRedirect($request, false, 'User not found.');
}

if ((string) $user->verification_code !== $code) {
return $this->jsonOrRedirect($request, false, 'Invalid verification code.');
}

if ($user->verification_code_sent_at->diffInMinutes(now()) > 1) {
return $this->jsonOrRedirect($request, false, 'Verification code has expired. Please request a new one.');
}

// Mark user as verified
$user->email_verified_at = now();
$user->verification_code = null;
$user->save();

$request->session()->forget('verification_email');

return $this->jsonOrRedirect($request, true, 'Email verified successfully. You can now login.', route('admin-login'));
}

public function resendCode(Request $request)
{
$request->validate([
'email' => 'required|email'
]);

$user = Admin::where('email', $request->email)->first();

if (!$user) {
return response()->json([
'success' => false,
'message' => 'User not found.'
], 404);
}

// Generate new code
$newCode = rand(100000, 999999);
$user->verification_code = $newCode;
$user->verification_code_sent_at = now();
$user->save();

// Send email
Mail::to($user->email)->send(new VerificationCodeMail($newCode));

return response()->json([
'success' => true,
'message' => 'A new verification code has been sent to your email.'
]);
}

/**
* Helper: Return JSON if AJAX, redirect if normal request
*/
private function jsonOrRedirect(Request $request, $success, $message, $redirect = null)
{
if ($request->expectsJson()) {
return response()->json([
'success' => $success,
'message' => $message,
'redirect' => $redirect
]);
}

if ($success) {
return $redirect
? redirect($redirect)->with('success', $message)
: back()->with('success', $message);
} else {
return back()->with('error', $message);
}
}

/**
* Check credentials without logging in
*/
public function checkCredentials(Request $request)
{
    $validator = Validator::make($request->all(), [
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if ($validator->fails()) {
        // Log validation errors with location data
        $this->adminLogService->logActivity(
            null,
            'failed_login_attempt - validation_errors: ' . implode(', ', $validator->errors()->all()),
            $request,
            'failed'
        );
        
        return response()->json([
            'success' => false,
            'errors' => $validator->errors()
        ], 422);
    }

    $credentials = $request->only('email', 'password');

    // Check if admin exists first
    $admin = Admin::where('email', $request->email)->first();

    if (!$admin) {
        // Log failed login attempt
        $this->adminLogService->logActivity(
            null,
            'failed_login_attempt - email_not_found',
            $request
        );

        return response()->json([
            'success' => false,
            'errors' => [
                'email' => 'The provided email does not exist in our system.'
            ]
        ], 401);
    }

    // Check if email is verified
    if (!$admin->email_verified_at) {
        // Store email in session for verification
        $request->session()->put('verification_email', $admin->email);
        
        // Log verification required attempt
        $this->adminLogService->logActivity(
            $admin,
            'failed_login_attempt - email_not_verified',
            $request
        );

        return response()->json([
            'success' => false,
            'redirect' => route('verify'),
            'message' => 'Please verify your email address before logging in.'
        ], 403);
    }

    // Check if admin is active
    if (!$admin->active) {
        // Log attempted login to inactive account
        $this->adminLogService->logActivity(
            $admin,
            'failed_login_attempt - account_inactive',
            $request
        );

        return response()->json([
            'success' => false,
            'errors' => [
                'email' => 'Your account is inactive. Please contact administrator.'
            ]
        ], 403);
    }

    // Attempt authentication
    if (Auth::guard('admin')->attempt($credentials)) {
        // Don't log in yet, just verify credentials
        Auth::guard('admin')->logout();

        // Generate and send 2FA code
        $twoFactorCode = rand(100000, 999999);
        $admin->two_factor_code = $twoFactorCode;
        $admin->two_factor_expires_at = now()->addMinutes(10);
        $admin->save();

        // Send 2FA code via email
        Mail::to($admin->email)->send(new TwoFactorCodeMail($twoFactorCode));

        return response()->json([
            'success' => true,
            'message' => 'Credentials verified. Please check your email for the verification code.'
        ]);
    }

    // Log failed login attempt (wrong password)
    $this->adminLogService->logActivity(
        $admin,
        'failed_login_attempt - wrong_password',
        $request
    );

    return response()->json([
        'success' => false,
        'errors' => [
            'password' => 'The provided password is incorrect.'
        ]
    ], 401);
}

/**
* Verify two-factor authentication code
*/
public function verifyTwoFactor(Request $request)
{
$request->validate([
'email' => 'required|email',
'password' => 'required',
'digit1' => 'required|digits:1',
'digit2' => 'required|digits:1',
'digit3' => 'required|digits:1',
'digit4' => 'required|digits:1',
'digit5' => 'required|digits:1',
'digit6' => 'required|digits:1',
]);

$code = (string) (
$request->digit1 .
$request->digit2 .
$request->digit3 .
$request->digit4 .
$request->digit5 .
$request->digit6
);

$admin = Admin::where('email', $request->email)->first();

if (!$admin) {
return response()->json([
'success' => false,
'message' => 'User not found.'
], 404);
}

// Check if 2FA code is valid and not expired
if ((string) $admin->two_factor_code !== $code) {
return response()->json([
'success' => false,
'message' => 'Invalid verification code.'
]);
}

if (now()->gt($admin->two_factor_expires_at)) {
return response()->json([
'success' => false,
'message' => 'Verification code has expired. Please request a new one.'
]);
}

// Clear 2FA code
$admin->two_factor_code = null;
$admin->two_factor_expires_at = null;
$admin->save();

// Now actually log in the user
$credentials = $request->only('email', 'password');

if (Auth::guard('admin')->attempt($credentials)) {
$request->session()->regenerate();

// Log successful login
$this->adminLogService->logLogin($admin, $request, 'Login Successfully');

return response()->json([
'success' => true,
'redirect' => '/admin-dashboard'
]);
}

return response()->json([
'success' => false,
'message' => 'Authentication failed.'
], 401);
}

/**
* Resend two-factor authentication code
*/
public function resendTwoFactor(Request $request)
{
$request->validate([
'email' => 'required|email'
]);

$admin = Admin::where('email', $request->email)->first();

if (!$admin) {
return response()->json([
'success' => false,
'message' => 'User not found.'
], 404);
}

// Generate new 2FA code
$twoFactorCode = rand(100000, 999999);
$admin->two_factor_code = $twoFactorCode;
$admin->two_factor_expires_at = now()->addMinutes(10);
$admin->save();

// Send 2FA code via email
Mail::to($admin->email)->send(new TwoFactorCodeMail($twoFactorCode));

return response()->json([
'success' => true,
'message' => 'A new verification code has been sent to your email.'
]);
}

public function login(Request $request)
{
$validator = Validator::make($request->all(), [
'email' => 'required|email',
'password' => 'required',
]);

if ($validator->fails()) {
if ($request->expectsJson()) {
return response()->json([
'success' => false,
'errors' => $validator->errors()
], 422);
}

return redirect()->back()
->withErrors($validator)
->withInput();
}

$credentials = $request->only('email', 'password');

// Check if admin exists first
$admin = Admin::where('email', $request->email)->first();

if (!$admin) {
// Log failed login attempt
$this->adminLogService->logActivity(
null,
'failed_login_attempt - email_not_found',
$request
);

if ($request->expectsJson()) {
return response()->json([
'success' => false,
'errors' => [
'email' => 'The provided email does not exist in our system.'
]
], 401);
}

return back()->withErrors([
'email' => 'The provided email does not exist in our system.',
])->onlyInput('email');
}

// Check if email is verified
if (!$admin->email_verified_at) {
// Store email in session for verification
$request->session()->put('verification_email', $admin->email);

if ($request->expectsJson()) {
return response()->json([
'success' => false,
'redirect' => route('verify'),
'message' => 'Please verify your email address before logging in.'
], 403);
}

return redirect()->route('verify')
->with('error', 'Please verify your email address before logging in.');
}

// Check if admin is active
if (!$admin->active) {
// Log attempted login to inactive account
$this->adminLogService->logActivity(
$admin,
'failed_login_attempt - account_inactive',
$request
);

if ($request->expectsJson()) {
return response()->json([
'success' => false,
'errors' => [
'email' => 'Your account is inactive. Please contact administrator.'
]
], 403);
}

return back()->withErrors([
'email' => 'Your account is inactive. Please contact administrator.',
])->onlyInput('email');
}

// Attempt authentication
if (Auth::guard('admin')->attempt($credentials)) {
$admin = Auth::guard('admin')->user();

// Log successful login
$this->adminLogService->logLogin($admin, $request, 'login_successful');

$request->session()->regenerate();

if ($request->expectsJson()) {
return response()->json([
'success' => true,
'redirect' => '/admin-dashboard'
]);
}

// Redirect all admins to the same dashboard
return redirect()->intended('/admin-dashboard');
}

// Log failed login attempt (wrong password)
$this->adminLogService->logActivity(
$admin,
'Login Failed',
$request
);

if ($request->expectsJson()) {
return response()->json([
'success' => false,
'errors' => [
'password' => 'The provided password is incorrect.'
]
], 401);
}

return back()->withErrors([
'password' => 'The provided password is incorrect.',
])->onlyInput('email');
}

public function logout(Request $request)
{
$admin = Auth::guard('admin')->user();

if ($admin) {
// Log logout activity
$this->adminLogService->logLogout($admin);
}

Auth::guard('admin')->logout();
$request->session()->invalidate();
$request->session()->regenerateToken();

return redirect('/admin-login');
}

public function checkAuthStatus(Request $request)
{
// Check if admin is authenticated via Auth guard
$isAuthenticated = Auth::guard('admin')->check();

// Also check session data as a backup
$sessionAuthenticated = $request->session()->get('admin_authenticated', false);

// Update last activity time if authenticated
if ($isAuthenticated || $sessionAuthenticated) {
$request->session()->put('admin_last_activity', now());
}

return response()->json([
'authenticated' => $isAuthenticated || $sessionAuthenticated,
'admin_id' => $isAuthenticated ? Auth::guard('admin')->id() : $request->session()->get('admin_id'),
'last_activity' => $request->session()->get('admin_last_activity')
]);
}

/**
* Refresh session to prevent timeout
*/
public function refreshSession(Request $request)
{
if (Auth::guard('admin')->check()) {
$request->session()->put('admin_last_activity', now());
return response()->json(['success' => true]);
}

return response()->json(['success' => false], 401);
}

}