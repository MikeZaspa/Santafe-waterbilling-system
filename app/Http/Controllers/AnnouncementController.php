<?php

namespace App\Http\Controllers;

use App\Models\AdminConsumer;
use App\Models\Announcement;
use App\Models\Notification;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function page()
    {
        return view('auth.admin-announcement');
    }

    public function index()
    {
        $announcements = Announcement::latest()->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $announcements->items(),
            'pagination' => [
                'current_page' => $announcements->currentPage(),
                'last_page' => $announcements->lastPage(),
                'per_page' => $announcements->perPage(),
                'total' => $announcements->total(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
            'is_active' => 'nullable|boolean',
        ]);

        $announcement = Announcement::create([
            'title' => $data['title'],
            'message' => $data['message'],
            'is_active' => (bool) ($data['is_active'] ?? true),
            'published_at' => now(),
        ]);

        if ($announcement->is_active) {
            $this->broadcastToConsumers($announcement, 'New announcement');
        }

        return response()->json([
            'success' => true,
            'message' => 'Announcement created successfully.',
            'data' => $announcement,
        ]);
    }

    public function show(Announcement $announcement)
    {
        return response()->json([
            'success' => true,
            'data' => $announcement,
        ]);
    }

    public function update(Request $request, Announcement $announcement)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
            'is_active' => 'nullable|boolean',
        ]);

        $announcement->update([
            'title' => $data['title'],
            'message' => $data['message'],
            'is_active' => (bool) ($data['is_active'] ?? $announcement->is_active),
            'published_at' => now(),
        ]);

        if ($announcement->is_active) {
            $this->broadcastToConsumers($announcement, 'Updated announcement');
        }

        return response()->json([
            'success' => true,
            'message' => 'Announcement updated successfully.',
            'data' => $announcement,
        ]);
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();

        return response()->json([
            'success' => true,
            'message' => 'Announcement deleted successfully.',
        ]);
    }

    public function toggleStatus(Announcement $announcement)
    {
        $announcement->is_active = !$announcement->is_active;
        $announcement->save();

        if ($announcement->is_active) {
            $announcement->published_at = now();
            $announcement->save();
            $this->broadcastToConsumers($announcement, 'Announcement activated');
        }

        return response()->json([
            'success' => true,
            'message' => 'Announcement status updated successfully.',
            'data' => $announcement,
        ]);
    }

    private function broadcastToConsumers(Announcement $announcement, string $prefix): void
    {
        $consumerIds = AdminConsumer::query()
            ->where('status', 'active')
            ->pluck('id');

        if ($consumerIds->isEmpty()) {
            return;
        }

        $title = $prefix . ': ' . $announcement->title;
        $message = $announcement->message;
        $now = now();

        $rows = [];
        foreach ($consumerIds as $consumerId) {
            $rows[] = [
                'consumer_id' => $consumerId,
                'billing_id' => null,
                'title' => $title,
                'message' => $message,
                'type' => 'announcement',
                'is_read' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        foreach (array_chunk($rows, 500) as $chunk) {
            Notification::insert($chunk);
        }
    }
}
