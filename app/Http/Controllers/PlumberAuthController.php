<?php

namespace App\Http\Controllers;

use App\Models\Plumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class PlumberAuthController extends Controller
{
    public function showLoginForm()
    {
        // If already logged in, redirect to dashboard
        if (Session::has('plumber_auth') && Session::get('plumber_auth')) {
            return redirect()->route('plumber.dashboard');
        }
        
        return view('auth.plumber-login');
    }

    // In your PlumberLoginController
public function login(Request $request)
{
    // Validate credentials
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    // Check plumber credentials (adjust this according to your logic)
    if ($this->isValidPlumber($credentials)) {
        // Set session properly
        Session::put('plumber_auth', true);
        Session::save(); // Force save
        
        // Debug: log successful login
        Log::info('Plumber login successful', ['session_id' => Session::getId()]);
        
        return redirect()->route('admin.plumber.dashboard');
    }

    return back()->withErrors(['email' => 'Invalid credentials']);
}

private function isValidPlumber($credentials)
{
    // Your plumber validation logic here
    // This could be checking against a database, config file, etc.
    return true; // Replace with actual validation
}

    public function logout(Request $request)
    {
        Session::forget('plumber_auth');
        Session::forget('plumber_id');
        Session::forget('plumber_name');
        Session::forget('plumber_role');

        return redirect('/plumber/login')->with('success', 'You have been logged out successfully.');
    }
}