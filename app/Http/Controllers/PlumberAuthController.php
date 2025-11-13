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
        return view('auth.plumber-login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $plumber = Plumber::where('username', $request->username)
                         ->where('status', 'active')
                         ->first();

        if ($plumber && Hash::check($request->password, $plumber->password)) {
            // Create plumber session
            Session::put('plumber_auth', true);
            Session::put('plumber_id', $plumber->id);
            Session::put('plumber_name', $plumber->first_name . ' ' . $plumber->last_name);
            Session::put('plumber_role', 'plumber');

            return redirect()->intended('admin-plumber-dashboard');
        }

        return back()->withErrors([
            'username' => 'Invalid credentials or account inactive.',
        ])->withInput($request->only('username'));
    }

    public function logout(Request $request)
    {
        Session::forget('plumber_auth');
        Session::forget('plumber_id');
        Session::forget('plumber_name');
        Session::forget('plumber_role');

        return redirect('/plumber/login');
    }
}