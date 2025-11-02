<?php

namespace App\Http\Controllers;

use App\Models\Plumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use App\Mail\TwoFactorCodeMailPlumber;
use Carbon\Carbon;
class PlumberController extends Controller
{
    /**
     * Display a listing of plumbers (for admin)
     */
    public function index()
    {
        $plumbers = Plumber::all();
        return view('auth.admin-plumber', compact('plumbers'));
    }

    /**
     * Store a newly created plumber (for admin)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'suffix' => 'nullable|string|max:20',
            'contact_number' => 'required|string|regex:/^09\d{9}$/|max:11',
            'address' => 'required|string|max:500',
            'username' => 'required|string|max:255|unique:admin_plumbers,username',
            'email' => 'required|string|email|max:255|unique:admin_plumbers,email',
            'password' => 'required|string|min:8|confirmed',
            'status' => 'required|in:active,inactive',
        ]);

        // Set null for empty optional fields
        $validated['middle_name'] = $validated['middle_name'] ?? null;
        $validated['suffix'] = $validated['suffix'] ?? null;

        // Hash password before saving
        $validated['password'] = Hash::make($validated['password']);

        $plumber = Plumber::create($validated);

        return response()->json([
            'message' => 'Plumber created successfully',
            'plumber' => $plumber
        ]);
    }

    /**
     * Show the form for editing the specified plumber (for admin)
     */
    public function edit($id)
    {
        $plumber = Plumber::findOrFail($id);
        return response()->json($plumber);
    }

    /**
     * Update the specified plumber (for admin)
     */
    public function update(Request $request, $id)
    {
        $plumber = Plumber::findOrFail($id);

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'suffix' => 'nullable|string|max:20',
            'contact_number' => 'required|string|regex:/^09\d{9}$/|max:11',
            'address' => 'required|string|max:500',
            'username' => 'required|string|max:255|unique:admin_plumbers,username,' . $id,
            'email' => 'required|string|email|max:255|unique:admin_plumbers,email,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
            'status' => 'required|in:active,inactive',
        ]);

        $validated['middle_name'] = $validated['middle_name'] ?? null;
        $validated['suffix'] = $validated['suffix'] ?? null;

        // Only update password if provided
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $plumber->update($validated);

        return response()->json([
            'message' => 'Plumber updated successfully',
            'plumber' => $plumber
        ]);
    }

    /**
     * Remove the specified plumber (for admin)
     */
    public function destroy($id)
    {
        $plumber = Plumber::findOrFail($id);

        // Use regular delete() instead of forceDelete() to utilize soft deletes
        $plumber->delete();

        return response()->json([
            'message' => 'Plumber deleted successfully'
        ]);
    }

    /**
     * Show the plumber login form
     */
    public function showLoginForm()
    {
        return view('auth.plumber-login');
    }

    /**
     * Handle plumber login request
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $plumber = Plumber::where('username', $credentials['username'])->first();

        if (!$plumber || !Hash::check($credentials['password'], $plumber->password)) {
            return response()->json([
                'success' => false,
                'message' => 'The provided credentials do not match our records.'
            ], 401);
        }

        if ($plumber->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Your account is inactive. Please contact the administrator.'
            ], 401);
        }

        // Generate a random 6-digit code
        $code = rand(100000, 999999);
        
        // Save the code and expiration time to the plumber record
        $plumber->two_factor_code = $code;
        $plumber->two_factor_expires_at = Carbon::now()->addMinutes(10);
        $plumber->save();
        
        // Send the code via email
        try {
            Mail::to($plumber->email)->send(new TwoFactorCodeMailPlumber($code, $plumber->first_name));
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send verification code. Please try again.'
            ], 500);
        }
        
        // Store plumber ID in session for verification
        Session::put('plumber_id_for_2fa', $plumber->id);
        
        return response()->json([
            'success' => true,
            'message' => 'A verification code has been sent to your email.',
            'requires_2fa' => true
        ]);
    }

    /**
     * Verify 2FA code
     */
    public function verify2FA(Request $request)
    {
        $request->validate([
            'code' => 'required|string|digits:6',
        ]);
        
        $plumberId = Session::get('plumber_id_for_2fa');
        
        if (!$plumberId) {
            return response()->json([
                'success' => false,
                'message' => 'Session expired. Please login again.'
            ], 401);
        }
        
        $plumber = Plumber::find($plumberId);
        
        if (!$plumber) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid session. Please login again.'
            ], 401);
        }
        
        // Check if the code matches and hasn't expired
        if ($plumber->two_factor_code !== $request->code) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid verification code.'
            ], 401);
        }
        
        if (Carbon::now()->gt($plumber->two_factor_expires_at)) {
            return response()->json([
                'success' => false,
                'message' => 'Verification code has expired. Please request a new one.'
            ], 401);
        }
        
        // Clear the 2FA code
        $plumber->two_factor_code = null;
        $plumber->two_factor_expires_at = null;
        $plumber->save();
        
        // Log in the plumber
        Session::put('plumber_logged_in', true);
        Session::put('plumber_id', $plumber->id);
        Session::put('plumber_data', $plumber->toArray());
        
        // Clear the temporary session
        Session::forget('plumber_id_for_2fa');
        
        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'redirect' => '/admin-plumber-dashboard'  // Direct URL to match your route
        ]);
    }

    /**
     * Resend 2FA code
     */
    public function resend2FA(Request $request)
    {
        $plumberId = Session::get('plumber_id_for_2fa');
        
        if (!$plumberId) {
            return response()->json([
                'success' => false,
                'message' => 'Session expired. Please login again.'
            ], 401);
        }
        
        $plumber = Plumber::find($plumberId);
        
        if (!$plumber) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid session. Please login again.'
            ], 401);
        }
        
        // Generate a new random 6-digit code
        $code = rand(100000, 999999);
        
        // Save the code and expiration time to the plumber record
        $plumber->two_factor_code = $code;
        $plumber->two_factor_expires_at = Carbon::now()->addMinutes(10);
        $plumber->save();
        
        // Send the code via email
        try {
            Mail::to($plumber->email)->send(new TwoFactorCodeMailPlumber($code, $plumber->first_name));
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send verification code. Please try again.'
            ], 500);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'A new verification code has been sent to your email.'
        ]);
    }
    /**
     * Show the plumber dashboard
     */
    public function dashboard()
    {
        // Check if plumber is logged in using session
        if (!Session::get('plumber_logged_in')) {
            return redirect()->route('plumber.login');
        }

        $plumberId = Session::get('plumber_id');
        $plumber = Plumber::find($plumberId);

        if (!$plumber) {
            return redirect()->route('plumber.login');
        }

        return view('plumber.dashboard', compact('plumber'));
    }

    /**
     * Logout the plumber
     */
    public function logout(Request $request)
    {
        // Clear plumber session
        Session::forget('plumber_logged_in');
        Session::forget('plumber_id');
        Session::forget('plumber_data');
        Session::forget('plumber_id_for_2fa');

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('plumber.login')->with('success', 'You have been logged out successfully.');
    }
}