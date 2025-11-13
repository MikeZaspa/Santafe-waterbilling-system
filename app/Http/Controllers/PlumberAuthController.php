<?php

namespace App\Http\Controllers;

use App\Models\Plumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class PlumberAuthController extends Controller
{
    public function showLoginForm()
    {
        if (Session::has('plumber_auth') && Session::get('plumber_auth')) {
            return redirect()->route('plumber.dashboard');
        }
        
        return view('auth.plumber-login');
    }

    public function login(Request $request)
{
    $credentials = $request->validate([
        'username' => 'required|string',
        'password' => 'required|string',
    ]);

    // GAMITA ANG 'username' FIELD
    // Kung ang imong login form naggamit og 'username', gamita ang `username` key.
    if (Auth::guard('plumber')->attempt(['username' => $credentials['username'], 'password' => $credentials['password']])) {
        $request->session()->regenerate();
        
        // Redirect sa intended page o dashboard
        return redirect()->intended(route('plumber.dashboard'));
    }

    // Kung fail, ibalik sa login uban sa error
    return back()->withErrors([
        'username' => 'The provided credentials do not match our records.',
    ])->withInput($request->only('username'));
}

    private function isValidPlumber($credentials)
    {
        // Example: Check against database
        $plumber = Plumber::where('username', $credentials['username'])->first();

        if ($plumber && Hash::check($credentials['password'], $plumber->password)) {
            return true;
        }

        return false;
    }

    public function logout(Request $request)
    {
        Session::forget('plumber_auth');
        Session::forget('plumber_id');
        Session::save();

        return redirect('/plumber/login')->with('success', 'You have been logged out successfully.');
    }
}