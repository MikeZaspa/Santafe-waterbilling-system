<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Plumber extends Model
{
    use HasFactory, SoftDeletes;

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