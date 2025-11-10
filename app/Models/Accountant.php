<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Accountant extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'admin_accountants';
    protected $fillable = [
        'first_name',
        'last_name',
        'middle_name',
        'suffix',
        'contact_number',
        'address',
        'username',
        'email',
        'password',
        'status',
        'two_factor_code',
        'two_factor_expires_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_code',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'two_factor_expires_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    /**
     * Generate a 2FA code for the user
     *
     * @return string
     */
    public function generateTwoFactorCode()
    {
        $this->timestamps = false;
        $this->two_factor_code = rand(100000, 999999);
        $this->two_factor_expires_at = now()->addMinutes(10);
        $this->save();
        
        return $this->two_factor_code;
    }
    
    /**
     * Reset the 2FA code
     */
    public function resetTwoFactorCode()
    {
        $this->timestamps = false;
        $this->two_factor_code = null;
        $this->two_factor_expires_at = null;
        $this->save();
    }
    
    /**
     * Check if the 2FA code is valid
     *
     * @param string $code
     * @return bool
     */
    public function verifyTwoFactorCode($code)
    {
        if (!$this->two_factor_code || !$this->two_factor_expires_at) {
            return false;
        }
        
        return $this->two_factor_code === $code && $this->two_factor_expires_at->isFuture();
    }
}