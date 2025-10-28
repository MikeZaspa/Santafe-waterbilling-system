<?php

namespace App\Services;

use App\Models\Admin;
use Illuminate\Support\Facades\Mail;
use App\Mail\TwoFactorCodeMail;

class TwoFactorService
{
    public function generateCode(Admin $admin)
    {
        $code = rand(100000, 999999);
        
        $admin->update([
            'two_factor_code' => $code,
            'two_factor_expires_at' => now()->addMinutes(10)
        ]);
        
        return $code;
    }
    
    public function sendCode(Admin $admin)
    {
        $code = $this->generateCode($admin);
        
        Mail::to($admin->email)->send(new TwoFactorCodeMail($code));
        
        return $code;
    }
    
    public function verifyCode(Admin $admin, $code)
    {
        if (!$admin->two_factor_code || !$admin->two_factor_expires_at) {
            return false;
        }
        
        if ($admin->two_factor_code !== (string) $code) {
            return false;
        }
        
        if ($admin->two_factor_expires_at->isPast()) {
            return false;
        }
        
        // Clear the code after successful verification
        $admin->update([
            'two_factor_code' => null,
            'two_factor_expires_at' => null
        ]);
        
        return true;
    }
    
    public function isEnabled(Admin $admin)
    {
        return $admin->two_factor_enabled ?? false;
    }
    
    public function enable(Admin $admin)
    {
        $admin->update(['two_factor_enabled' => true]);
    }
    
    public function disable(Admin $admin)
    {
        $admin->update([
            'two_factor_enabled' => false,
            'two_factor_code' => null,
            'two_factor_expires_at' => null
        ]);
    }
}