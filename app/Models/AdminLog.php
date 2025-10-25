<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminLog extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'admin_id',
        'email',
        'ip_address',
        'country',
        'city',
        'region',
        'timezone',
        'browser',
        'platform',
        'device',
        'activity',
        'user_agent',
        'login_at',
        'logout_at',
        'session_duration',
    ];
    
    protected $casts = [
        'login_at' => 'datetime',
        'logout_at' => 'datetime',
    ];
    
    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
    
    // Helper method to calculate session duration
    public function getSessionDurationFormattedAttribute()
    {
        if (!$this->logout_at) {
            return 'Still active';
        }
        
        $seconds = $this->session_duration;
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $seconds = $seconds % 60;
        
        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }
}