<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\AdminLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AdminLogService
{
    public function logLogin(Admin $admin, Request $request, string $activity = 'admin-login')
    {
        return AdminLog::create($this->buildLogPayload($admin, $request, $activity));
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

    public function logActivity(Admin $admin = null, string $activity, Request $request = null)
    {
        return AdminLog::create($this->buildLogPayload($admin, $request, $activity));
    }

    private function buildLogPayload(?Admin $admin, ?Request $request, string $activity): array
    {
        $ip = $request ? $request->ip() : null;
        $userAgent = $request ? (string) $request->userAgent() : '';
        $geolocation = [];
        $deviceInfo = ['browser' => 'Unknown', 'platform' => 'Unknown', 'device' => 'Unknown'];
        $requestLocation = $this->extractLocationFromRequest($request);

        if ($ip) {
            $geolocation = $this->getGeolocation($ip);
            $deviceInfo = $this->parseUserAgent($userAgent);
        }

        $municipality = $requestLocation['municipality']
            ?? ($geolocation['municipality'] ?? null)
            ?? ($geolocation['city'] ?? null);

        $street = $requestLocation['street']
            ?? ($geolocation['street'] ?? null);

        $barangay = $requestLocation['barangay']
            ?? ($geolocation['barangay'] ?? null);

        return [
            'admin_id' => $admin ? $admin->id : null,
            'email' => $admin ? $admin->email : ($request ? $request->input('email') : null),
            'ip_address' => $ip,
            'country' => $geolocation['country'] ?? null,
            'city' => $geolocation['city'] ?? $municipality,
            'municipality' => $municipality,
            'street' => $street,
            'barangay' => $barangay,
            'region' => $geolocation['region'] ?? null,
            'timezone' => $geolocation['timezone'] ?? null,
            'latitude' => $requestLocation['latitude'] ?? ($geolocation['latitude'] ?? null),
            'longitude' => $requestLocation['longitude'] ?? ($geolocation['longitude'] ?? null),
            'browser' => $deviceInfo['browser'],
            'platform' => $deviceInfo['platform'],
            'device' => $deviceInfo['device'],
            'user_agent' => $userAgent ?: null,
            'activity' => $activity,
            'login_at' => now(),
        ];
    }

    private function extractLocationFromRequest(?Request $request): array
    {
        if (!$request) {
            return [
                'municipality' => null,
                'street' => null,
                'barangay' => null,
                'latitude' => null,
                'longitude' => null,
            ];
        }

        return [
            'municipality' => $this->cleanLocationValue($request->input('municipality')),
            'street' => $this->cleanLocationValue($request->input('street')),
            'barangay' => $this->cleanLocationValue($request->input('barangay')),
            'latitude' => $request->filled('latitude') ? (float) $request->input('latitude') : null,
            'longitude' => $request->filled('longitude') ? (float) $request->input('longitude') : null,
        ];
    }

    private function cleanLocationValue($value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);
        return $value === '' ? null : $value;
    }

    private function getGeolocation(string $ip)
    {
        // For local IPs or testing, return empty data
        if ($ip === '127.0.0.1' || strpos($ip, '192.168.') === 0 || strpos($ip, '10.') === 0) {
            return [
                'country' => 'Local',
                'city' => 'Local Network',
                'municipality' => 'Local Network',
                'street' => null,
                'barangay' => null,
                'region' => 'Local',
                'timezone' => config('app.timezone', 'UTC'),
                'latitude' => null,
                'longitude' => null,
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
                    'municipality' => $data['city'] ?? null,
                    'street' => null,
                    'barangay' => null,
                    'region' => $data['region'] ?? null,
                    'timezone' => $data['timezone'] ?? null,
                    'latitude' => $data['latitude'] ?? null,
                    'longitude' => $data['longitude'] ?? null,
                ];
            }
            
            // Try alternative service if first one fails
            $response = Http::timeout(5)->get("http://ip-api.com/json/{$ip}");
            
            if ($response->successful()) {
                $data = $response->json();
                if ($data['status'] === 'success') {
                    return [
                        'country' => $data['country'] ?? null,
                        'city' => $data['city'] ?? null,
                        'municipality' => $data['city'] ?? null,
                        'street' => null,
                        'barangay' => null,
                        'region' => $data['regionName'] ?? null,
                        'timezone' => $data['timezone'] ?? null,
                        'latitude' => $data['lat'] ?? null,
                        'longitude' => $data['lon'] ?? null,
                    ];
                }
            }
        } catch (\Exception $e) {
            // Log error or use fallback
            \Log::error('Geolocation error: ' . $e->getMessage());
        }

        return [
            'country' => null,
            'city' => null,
            'municipality' => null,
            'street' => null,
            'barangay' => null,
            'region' => null,
            'timezone' => null,
            'latitude' => null,
            'longitude' => null,
        ];
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
