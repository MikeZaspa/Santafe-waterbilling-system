<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\AdminLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AdminLogService
{
    public function logLogin(Admin $admin, Request $request, string $activity = 'login')
    {
        $ip = $request->ip();
        $geolocation = $this->getGeolocation($ip);
        $userAgent = $request->userAgent();
        $deviceInfo = $this->parseUserAgent($userAgent);

        return AdminLog::create([
            'admin_id' => $admin->id,
            'email' => $admin->email,
            'ip_address' => $ip,
            'country' => $geolocation['country'] ?? null,
            'city' => $geolocation['city'] ?? null,
            'region' => $geolocation['region'] ?? null,
            'timezone' => $geolocation['timezone'] ?? null,
            'browser' => $deviceInfo['browser'],
            'platform' => $deviceInfo['platform'],
            'device' => $deviceInfo['device'],
            'user_agent' => $userAgent,
            'activity' => $activity,
            'login_at' => now(),
        ]);
    }

    public function logLogout(Admin $admin)
    {
        $latestLog = AdminLog::where('admin_id', $admin->id)
            ->whereNull('logout_at')
            ->latest()
            ->first();

        if ($latestLog) {
            $latestLog->update([
                'logout_at' => now(),
                'session_duration' => now()->diffInSeconds($latestLog->login_at),
            ]);
        }
    }

    public function logActivity(Admin $admin, string $activity, Request $request = null)
    {
        $ip = $request ? $request->ip() : null;
        $userAgent = $request ? $request->userAgent() : null;

        return AdminLog::create([
            'admin_id' => $admin->id,
            'email' => $admin->email,
            'ip_address' => $ip,
            'activity' => $activity,
            'login_at' => now(),
        ]);
    }

    private function getGeolocation(string $ip)
    {
        // For local IPs or testing, return empty data
        if ($ip === '127.0.0.1' || strpos($ip, '192.168.') === 0 || strpos($ip, '10.') === 0) {
            return [
                'country' => 'Local',
                'city' => 'Local Network',
                'region' => 'Local',
                'timezone' => config('app.timezone', 'UTC'),
            ];
        }

        try {
            // Using ipapi.co (free tier available)
            $response = Http::timeout(5)->get("http://ipapi.co/{$ip}/json/");
            
            if ($response->successful()) {
                $data = $response->json();
                return [
                    'country' => $data['country_name'] ?? null,
                    'city' => $data['city'] ?? null,
                    'region' => $data['region'] ?? null,
                    'timezone' => $data['timezone'] ?? null,
                ];
            }
        } catch (\Exception $e) {
            // Log error or use fallback
            \Log::error('Geolocation error: ' . $e->getMessage());
        }

        return [];
    }

    private function parseUserAgent(string $userAgent)
    {
        $browser = 'Unknown';
        $platform = 'Unknown';
        $device = 'Desktop';

        // Simple parsing - you might want to use a package like jenssegers/agent
        if (strpos($userAgent, 'Firefox') !== false) {
            $browser = 'Firefox';
        } elseif (strpos($userAgent, 'Chrome') !== false) {
            $browser = 'Chrome';
        } elseif (strpos($userAgent, 'Safari') !== false) {
            $browser = 'Safari';
        } elseif (strpos($userAgent, 'Edge') !== false) {
            $browser = 'Edge';
        }

        if (strpos($userAgent, 'Windows') !== false) {
            $platform = 'Windows';
        } elseif (strpos($userAgent, 'Mac') !== false) {
            $platform = 'Mac';
        } elseif (strpos($userAgent, 'Linux') !== false) {
            $platform = 'Linux';
        } elseif (strpos($userAgent, 'Android') !== false) {
            $platform = 'Android';
            $device = 'Mobile';
        } elseif (strpos($userAgent, 'iPhone') !== false || strpos($userAgent, 'iPad') !== false) {
            $platform = 'iOS';
            $device = strpos($userAgent, 'iPad') !== false ? 'Tablet' : 'Mobile';
        }

        return [
            'browser' => $browser,
            'platform' => $platform,
            'device' => $device,
        ];
    }
}