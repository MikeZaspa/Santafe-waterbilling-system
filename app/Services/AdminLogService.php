<?php

namespace App\Services;

use App\Models\AdminLog;
use Illuminate\Http\Request;

class AdminLogService
{
    /**
     * Log admin activity
     */
    public function logActivity($admin, $activity, Request $request, $locationData = [])
    {
        $logData = [
            'admin_id' => $admin ? $admin->id : null,
            'activity' => $activity,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'browser' => $this->getBrowser($request->userAgent()),
            'platform' => $this->getPlatform($request->userAgent()),
            'login_at' => now(),
        ];
        
        // Add location data if available
        if (!empty($locationData)) {
            $logData = array_merge($logData, $locationData);
        }
        
        AdminLog::create($logData);
    }
    
    /**
     * Log admin login
     */
    public function logLogin($admin, Request $request, $activity = 'login_successful', $locationData = [])
    {
        $logData = [
            'admin_id' => $admin->id,
            'activity' => $activity,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'browser' => $this->getBrowser($request->userAgent()),
            'platform' => $this->getPlatform($request->userAgent()),
            'login_at' => now(),
        ];
        
        // Add location data if available
        if (!empty($locationData)) {
            $logData = array_merge($logData, $locationData);
        }
        
        AdminLog::create($logData);
    }
    
    /**
     * Log admin logout
     */
    public function logLogout($admin)
    {
        // Find the most recent login log without logout time
        $log = AdminLog::where('admin_id', $admin->id)
            ->whereNull('logout_at')
            ->orderBy('login_at', 'desc')
            ->first();
            
        if ($log) {
            $log->logout_at = now();
            
            // Calculate session duration in seconds
            $loginTime = $log->login_at;
            $logoutTime = now();
            $duration = $logoutTime->diffInSeconds($loginTime);
            
            $log->session_duration = $duration;
            $log->save();
        }
    }
    
    /**
     * Extract browser name from user agent
     */
    private function getBrowser($userAgent)
    {
        $browsers = [
            'Chrome' => 'Chrome',
            'Firefox' => 'Firefox',
            'Safari' => 'Safari',
            'Edge' => 'Edge',
            'Opera' => 'Opera',
            'MSIE' => 'Internet Explorer',
        ];
        
        foreach ($browsers as $key => $value) {
            if (preg_match("/$key/i", $userAgent)) {
                return $value;
            }
        }
        
        return 'Unknown';
    }
    
    /**
     * Extract platform name from user agent
     */
    private function getPlatform($userAgent)
    {
        $platforms = [
            'Windows' => 'Windows',
            'Mac' => 'Mac',
            'Linux' => 'Linux',
            'Android' => 'Android',
            'iOS' => 'iOS',
            'iPhone' => 'iPhone',
            'iPad' => 'iPad',
        ];
        
        foreach ($platforms as $key => $value) {
            if (preg_match("/$key/i", $userAgent)) {
                return $value;
            }
        }
        
        return 'Unknown';
    }
}