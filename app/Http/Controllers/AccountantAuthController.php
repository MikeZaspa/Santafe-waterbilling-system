<?php

namespace App\Http\Controllers;

use App\Models\Accountant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AccountantAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.accountant-login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $accountant = Accountant::where('username', $request->username)
                               ->where('status', 'active')
                               ->first();

        if ($accountant && Hash::check($request->password, $accountant->password)) {
            // Create accountant session
            Session::put('accountant_auth', true);
            Session::put('accountant_id', $accountant->id);
            Session::put('accountant_name', $accountant->first_name . ' ' . $accountant->last_name);
            Session::put('accountant_role', 'accountant');

            return redirect()->intended('admin-accountant-dashboard');
        }

        return back()->withErrors([
            'username' => 'Invalid credentials or account inactive.',
        ])->withInput($request->only('username'));
    }

    public function logout(Request $request)
    {
        Session::forget('accountant_auth');
        Session::forget('accountant_id');
        Session::forget('accountant_name');
        Session::forget('accountant_role');

        return redirect('/accountant/login');
    }
}