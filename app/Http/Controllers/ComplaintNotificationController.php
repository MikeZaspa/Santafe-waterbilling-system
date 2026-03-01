<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ComplaintNotificationController extends Controller
{
    public function adminIndex(Request $request): JsonResponse
    {
        return $this->buildResponse($request, 'admin_complaints_last_seen_at');
    }

    public function adminMarkAllRead(Request $request): JsonResponse
    {
        return $this->markAllRead($request, 'admin_complaints_last_seen_at');
    }

    public function plumberIndex(Request $request): JsonResponse
    {
        return $this->buildResponse($request, 'plumber_complaints_last_seen_at');
    }

    public function plumberMarkAllRead(Request $request): JsonResponse
    {
        return $this->markAllRead($request, 'plumber_complaints_last_seen_at');
    }

    private function buildResponse(Request $request, string $sessionKey): JsonResponse
    {
        $limit = (int) $request->integer('limit', 8);
        if ($limit < 1) {
            $limit = 8;
        }
        if ($limit > 20) {
            $limit = 20;
        }

        $unreadQuery = $this->unreadComplaintsQuery($request, $sessionKey);
        $unreadCount = (clone $unreadQuery)->count();

        $notifications = $unreadQuery
            ->take($limit)
            ->get()
            ->map(function (Complaint $complaint) {
                $consumer = $complaint->consumer;
                $fullName = trim(implode(' ', array_filter([
                    $consumer?->first_name,
                    $consumer?->middle_name,
                    $consumer?->last_name,
                    $consumer?->suffix,
                ])));

                return [
                    'id' => $complaint->id,
                    'consumer_id' => $complaint->consumer_id,
                    'consumer_name' => $fullName ?: 'Unknown Consumer',
                    'meter_no' => $consumer?->meter_no ?? 'N/A',
                    'message' => $complaint->plainMessage(),
                    'has_attachment' => !empty($complaint->attachment_path),
                    'created_at' => $complaint->created_at?->toIso8601String(),
                    'time_ago' => $complaint->created_at?->diffForHumans() ?? 'Just now',
                ];
            })
            ->values();

        return response()->json([
            'success' => true,
            'unread_count' => $unreadCount,
            'notifications' => $notifications,
        ]);
    }

    private function markAllRead(Request $request, string $sessionKey): JsonResponse
    {
        $request->session()->put($sessionKey, now()->toDateTimeString());

        return response()->json([
            'success' => true,
            'message' => 'Complaint notifications marked as read.',
        ]);
    }

    private function unreadComplaintsQuery(Request $request, string $sessionKey)
    {
        $query = Complaint::with(['consumer:id,first_name,middle_name,last_name,suffix,meter_no'])
            ->where('message', 'not like', Complaint::ADMIN_REPLY_PREFIX . '%')
            ->latest();

        $lastSeen = $request->session()->get($sessionKey);
        if (!empty($lastSeen)) {
            try {
                $query->where('created_at', '>', Carbon::parse($lastSeen));
            } catch (\Throwable $exception) {
                // Ignore invalid session values and fall back to all records.
            }
        }

        return $query;
    }
}
