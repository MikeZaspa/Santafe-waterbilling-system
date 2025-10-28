<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ConsumerAccount;
use App\Models\AdminConsumer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ConsumerController extends Controller
{
    /**
     * Display the consumer's profile.
     *
     * @return \Illuminate\View\View
     */
    public function profile()
    {
        // Check if consumer is authenticated
        if (!Auth::guard('consumer')->check()) {
            return redirect('/consumer/login');
        }
        
        $account = Auth::guard('consumer')->user();
        $consumer = $account->consumer;
        
        return view('auth.consumer-profile', [
            'consumer' => $consumer,
            'account' => $account
        ]);
    }
    
    
}