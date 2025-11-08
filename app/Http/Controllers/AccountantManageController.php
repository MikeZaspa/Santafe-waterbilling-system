<?php

namespace App\Http\Controllers;

use App\Models\Accountant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\TwoFactorCodeMailAccountant;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AccountantManageController extends Controller
{
      
    public function index() 
    {   
          // Check if consumer is authenticated
        if (!Auth::guard('admin')->check()) {
            return redirect('/admin-login');
        }
        $accountants = Accountant::all();
        return view('auth.admin-accountant', compact('accountants'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:admin_accountants,username|alpha_dash|min:3|max:20',
            'email' => 'required|email|unique:admin_accountants,email|max:255',
            'password' => 'required|min:8|confirmed',
            'first_name' => 'required|string|max:50',
            'middle_name' => 'nullable|string|max:50',
            'last_name' => 'required|string|max:50',
            'suffix' => 'nullable|string|max:10',
            'contact_number' => 'required|string|max:11|regex:/^09\d{9}$/',
            'address' => 'required|string|max:255',
            'status' => 'required|in:active,inactive'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'message' => 'Validation failed'
            ], 422);
        }

        try {
            $accountant = Accountant::create([
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'first_name' => $request->first_name,
                'middle_name' => $request->middle_name,
                'last_name' => $request->last_name,
                'suffix' => $request->suffix,
                'contact_number' => $request->contact_number,
                'address' => $request->address,
                'status' => $request->status
            ]);

            return response()->json([
                'message' => 'Accountant created successfully',
                'data' => $accountant
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create accountant',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        try {
            $accountant = Accountant::findOrFail($id);
            return response()->json($accountant);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Accountant not found'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $accountant = Accountant::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'username' => 'required|alpha_dash|min:3|max:20|unique:admin_accountants,username,' . $id,
            'email' => 'required|email|unique:admin_accountants,email,' . $id . '|max:255',
            'password' => 'nullable|min:8',
            'first_name' => 'required|string|max:50',
            'middle_name' => 'nullable|string|max:50',
            'last_name' => 'required|string|max:50',
            'suffix' => 'nullable|string|max:10',
            'contact_number' => 'required|string|max:11|regex:/^09\d{9}$/',
            'address' => 'required|string|max:255',
            'status' => 'required|in:active,inactive'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'message' => 'Validation failed'
            ], 422);
        }

        try {
            $updateData = [
                'username' => $request->username,
                'email' => $request->email,
                'first_name' => $request->first_name,
                'middle_name' => $request->middle_name,
                'last_name' => $request->last_name,
                'suffix' => $request->suffix,
                'contact_number' => $request->contact_number,
                'address' => $request->address,
                'status' => $request->status
            ];

            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            $accountant->update($updateData);

            return response()->json([
                'message' => 'Accountant updated successfully',
                'data' => $accountant
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update accountant',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $accountant = Accountant::findOrFail($id);
            $accountant->forceDelete(); // Changed from delete() to forceDelete()

            return response()->json([
                'message' => 'Accountant deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete accountant',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    // 2FA Methods
    public function show2FAModal()
    {
        return view('auth.accountant-2fa');
    }
    
    public function send2FACode(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);
        
        $accountant = Accountant::where('username', $request->username)->first();
        
        if (!$accountant || !Hash::check($request->password, $accountant->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }
        
        if ($accountant->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Account is inactive'
            ], 401);
        }
        
        // Generate 6-digit code - Fixed this line
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Save code and expiration
        $accountant->two_factor_code = $code;
        $accountant->two_factor_expires_at = Carbon::now()->addMinutes(10);
        $accountant->save();
        
        // Send email
        try {
            Mail::to($accountant->email)->send(new TwoFactorCodeMailAccountant($code, $accountant->first_name));
            
            return response()->json([
                'success' => true,
                'message' => 'Verification code sent to your email',
                'accountant_id' => $accountant->id
            ]);
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Failed to send 2FA email: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to send verification code',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function verify2FACode(Request $request)
    {
        $request->validate([
            'accountant_id' => 'required|exists:admin_accountants,id',
            'code' => 'required|string|size:6',
        ]);
        
        $accountant = Accountant::findOrFail($request->accountant_id);
        
        // Check if code is valid and not expired
        if ($accountant->two_factor_code !== $request->code) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid verification code'
            ], 401);
        }
        
        if (Carbon::now()->gt($accountant->two_factor_expires_at)) {
            return response()->json([
                'success' => false,
                'message' => 'Verification code has expired'
            ], 401);
        }
        
        // Clear the 2FA code
        $accountant->two_factor_code = null;
        $accountant->two_factor_expires_at = null;
        $accountant->save();
        
        // Log in the user using session directly
        session([
            'accountant_id' => $accountant->id,
            'accountant_name' => $accountant->first_name . ' ' . $accountant->last_name
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'redirect' => '/admin-accountant-dashboard'
        ]);
    }
    
    public function resend2FACode(Request $request)
    {
        $request->validate([
            'accountant_id' => 'required|exists:admin_accountants,id',
        ]);
        
        $accountant = Accountant::findOrFail($request->accountant_id);
        
        // Generate new 6-digit code - Fixed this line
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Save code and expiration
        $accountant->two_factor_code = $code;
        $accountant->two_factor_expires_at = Carbon::now()->addMinutes(10);
        $accountant->save();
        
        // Send email
        try {
            Mail::to($accountant->email)->send(new TwoFactorCodeMailAccountant($code, $accountant->first_name));
            
            return response()->json([
                'success' => true,
                'message' => 'New verification code sent to your email'
            ]);
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Failed to resend 2FA email: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to send verification code',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function logout(Request $request)
    {
        session()->forget(['accountant_id', 'accountant_name']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/accountant-login')->with('success', 'You have been logged out successfully.');
    }
}