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

        // Check plumber credentials (adjust this according to your logic)
        if ($this->isValidPlumber($credentials)) {
            // Set session properly
            Session::put('plumber_auth', true);
            Session::save(); // Force save

            // Optional: Store plumber ID for future use
            Session::put('plumber_id', $plumber->id); // if you have plumber object

            Log::info('Plumber login successful', ['session_id' => Session::getId()]);
            
            return redirect()->route('plumber.dashboard');
        }

        return back()->withErrors(['username' => 'Invalid credentials']);
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