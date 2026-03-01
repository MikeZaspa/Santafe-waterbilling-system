<?php

namespace App\Http\Controllers;

use App\Models\AdminConsumer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ComplaintTypingController extends Controller
{
    private const TTL_SECONDS = 12;
    private const PRESENCE_TTL_SECONDS = 30;

    public function adminTyping(Request $request): JsonResponse
    {
        if (!Auth::guard('admin')->check()) {
            return response()->json(['success' => false], 401);
        }

        $validated = $request->validate([
            'consumer_id' => 'required|exists:admin_consumers,id',
            'is_typing' => 'required|boolean',
        ]);

        $consumerId = (int) $validated['consumer_id'];
        $isTyping = (bool) $validated['is_typing'];
        $key = $this->typingKey($consumerId, 'admin');

        if ($isTyping) {
            Cache::put($key, true, now()->addSeconds(self::TTL_SECONDS));
        } else {
            Cache::forget($key);
        }

        return response()->json(['success' => true]);
    }

    public function consumerTyping(Request $request): JsonResponse
    {
        if (!Auth::guard('consumer')->check()) {
            return response()->json(['success' => false], 401);
        }

        $validated = $request->validate([
            'is_typing' => 'required|boolean',
        ]);

        $consumerId = (int) (Auth::guard('consumer')->user()?->consumer?->id ?? 0);
        if ($consumerId <= 0) {
            return response()->json(['success' => false], 401);
        }

        $this->touchConsumerPresence($consumerId);

        $isTyping = (bool) $validated['is_typing'];
        $key = $this->typingKey($consumerId, 'consumer');

        if ($isTyping) {
            Cache::put($key, true, now()->addSeconds(self::TTL_SECONDS));
        } else {
            Cache::forget($key);
        }

        return response()->json(['success' => true]);
    }

    public function consumerHeartbeat(): JsonResponse
    {
        if (!Auth::guard('consumer')->check()) {
            return response()->json(['success' => false], 401);
        }

        $consumerId = (int) (Auth::guard('consumer')->user()?->consumer?->id ?? 0);
        if ($consumerId <= 0) {
            return response()->json(['success' => false], 401);
        }

        $this->touchConsumerPresence($consumerId);

        return response()->json(['success' => true]);
    }

    public function adminOnlineStatuses(Request $request): JsonResponse
    {
        if (!Auth::guard('admin')->check()) {
            return response()->json(['success' => false], 401);
        }

        $rawIds = $request->query('ids', []);
        $ids = is_array($rawIds) ? $rawIds : [];
        $consumerIds = collect($ids)
            ->map(fn ($value) => (int) $value)
            ->filter(fn (int $id) => $id > 0)
            ->unique()
            ->take(200)
            ->values();

        $statuses = [];
        foreach ($consumerIds as $consumerId) {
            $statuses[(string) $consumerId] = Cache::has($this->presenceKey((int) $consumerId));
        }

        return response()->json([
            'success' => true,
            'statuses' => $statuses,
        ]);
    }

    public function adminStatus(AdminConsumer $consumer): JsonResponse
    {
        if (!Auth::guard('admin')->check()) {
            return response()->json(['success' => false], 401);
        }

        $isTyping = Cache::has($this->typingKey((int) $consumer->id, 'consumer'));

        return response()->json([
            'success' => true,
            'is_typing' => $isTyping,
        ]);
    }

    public function consumerStatus(): JsonResponse
    {
        if (!Auth::guard('consumer')->check()) {
            return response()->json(['success' => false], 401);
        }

        $consumerId = (int) (Auth::guard('consumer')->user()?->consumer?->id ?? 0);
        if ($consumerId <= 0) {
            return response()->json(['success' => false], 401);
        }

        $this->touchConsumerPresence($consumerId);

        $isTyping = Cache::has($this->typingKey($consumerId, 'admin'));

        return response()->json([
            'success' => true,
            'is_typing' => $isTyping,
        ]);
    }

    private function typingKey(int $consumerId, string $role): string
    {
        return 'complaint_typing:' . $consumerId . ':' . $role;
    }

    private function presenceKey(int $consumerId): string
    {
        return 'complaint_presence:' . $consumerId . ':consumer';
    }

    private function touchConsumerPresence(int $consumerId): void
    {
        Cache::put($this->presenceKey($consumerId), true, now()->addSeconds(self::PRESENCE_TTL_SECONDS));
    }
}
