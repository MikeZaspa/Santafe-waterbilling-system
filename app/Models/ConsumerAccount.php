<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Str;

class ConsumerAccount extends Authenticatable
{
   use HasFactory, SoftDeletes;
   
   protected $guard = 'consumer';

    protected $fillable = [
        'consumer_id',
        'username',
        'email',
        'password',
        'two_factor_code',
        'two_factor_expires_at',
        'created_by',
        'updated_by'
    ];

    protected $hidden = [
        'password',
        'two_factor_code'
    ];

    protected $casts = [
        'two_factor_expires_at' => 'datetime',
    ];

    public function consumer()
    {
        return $this->belongsTo(AdminConsumer::class, 'consumer_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Generate a new 2FA code for the user
     */
    public function generateTwoFactorCode()
    {
        $this->two_factor_code = rand(100000, 999999);
        $this->two_factor_expires_at = now()->addMinutes(10);
        $this->save();
    }

    /**
     * Reset the 2FA code
     */
    public function resetTwoFactorCode()
    {
        $this->two_factor_code = null;
        $this->two_factor_expires_at = null;
        $this->save();
    }

    /**
     * Check if the 2FA code is valid
     */
    public function verifyTwoFactorCode($code)
    {
        if (!$this->two_factor_code || !$this->two_factor_expires_at) {
            return false;
        }

        if ($this->two_factor_expires_at->isPast()) {
            return false;
        }

        return $this->two_factor_code === $code;
    }
}