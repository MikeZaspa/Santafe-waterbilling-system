<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; // <-- 1. I-import kini
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable; // <-- 2. I-import kini (maayo nga i-add)

class Plumber extends Authenticatable // <-- 3. I-extend kini diri
{
    use HasFactory, SoftDeletes, Notifiable; // <-- 4. Dugang ang Notifiable trait

    // Specify the table name explicitly
    protected $table = 'admin_plumbers';

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
    ];
}