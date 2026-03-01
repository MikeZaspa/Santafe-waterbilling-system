<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ConsumerComplaintController extends Controller
{
    public function index()
    {
        if (!Auth::guard('consumer')->check()) {
            return redirect('/consumer-portal');
        }

        $account = Auth::guard('consumer')->user();
        $consumer = $account->consumer;

        $complaints = Complaint::where('consumer_id', $consumer->id)
            ->orderBy('created_at')
            ->get();
        $notifications = Notification::where('consumer_id', $consumer->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('auth.consumer-complaints', [
            'consumer' => $consumer,
            'complaints' => $complaints,
            'notifications' => $notifications,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:5000',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
        ]);

        $consumer = Auth::guard('consumer')->user()->consumer;
        $attachmentPath = null;

        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('complaints', 'public');
        }

        $complaint = Complaint::create([
            'consumer_id' => $consumer->id,
            'message' => $this->sanitizeConsumerMessage($validated['message']),
            'attachment_path' => $attachmentPath,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'complaint' => [
                    'id' => $complaint->id,
                    'consumer_id' => $complaint->consumer_id,
                    'message' => $complaint->plainMessage(),
                    'is_admin' => false,
                    'created_at' => $complaint->created_at?->toIso8601String(),
                    'has_attachment' => !empty($complaint->attachment_path),
                    'attachment_url' => !empty($complaint->attachment_path)
                        ? route('consumer.complaints.attachment', $complaint->id)
                        : null,
                ],
            ]);
        }

        return redirect()->route('consumer.complaints.index')->with('success', 'Complaint submitted successfully. Admin and plumber have been notified.');
    }

    public function update(Request $request, Complaint $complaint)
    {
        $complaint = $this->authorizeComplaint($complaint, true);

        $validated = $request->validate([
            'message' => 'required|string|max:5000',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
        ]);

        if ($request->hasFile('attachment')) {
            if (!empty($complaint->attachment_path) && Storage::disk('public')->exists($complaint->attachment_path)) {
                Storage::disk('public')->delete($complaint->attachment_path);
            }

            $complaint->attachment_path = $request->file('attachment')->store('complaints', 'public');
        }

        $complaint->message = $this->sanitizeConsumerMessage($validated['message']);
        $complaint->save();

        return redirect()->route('consumer.complaints.index')->with('success', 'Complaint updated successfully.');
    }

    public function destroy(Complaint $complaint)
    {
        $complaint = $this->authorizeComplaint($complaint, true);

        if (!empty($complaint->attachment_path) && Storage::disk('public')->exists($complaint->attachment_path)) {
            Storage::disk('public')->delete($complaint->attachment_path);
        }

        $complaint->delete();

        return redirect()->route('consumer.complaints.index')->with('success', 'Complaint deleted successfully.');
    }

    public function destroyConversation()
    {
        $consumer = Auth::guard('consumer')->user()?->consumer;

        if (!$consumer) {
            return redirect('/consumer-portal');
        }

        $complaints = Complaint::where('consumer_id', $consumer->id)->get();

        if ($complaints->isEmpty()) {
            return redirect()->route('consumer.complaints.index')->with('success', 'No conversation to delete.');
        }

        foreach ($complaints as $complaint) {
            if (!empty($complaint->attachment_path) && Storage::disk('public')->exists($complaint->attachment_path)) {
                Storage::disk('public')->delete($complaint->attachment_path);
            }
        }

        Complaint::where('consumer_id', $consumer->id)->delete();

        return redirect()->route('consumer.complaints.index')->with('success', 'Complaint conversation deleted successfully.');
    }

    public function attachment(Complaint $complaint)
    {
        $complaint = $this->authorizeComplaint($complaint);

        if (empty($complaint->attachment_path) || !Storage::disk('public')->exists($complaint->attachment_path)) {
            abort(404, 'Attachment not found.');
        }

        return response()->file(storage_path('app/public/' . $complaint->attachment_path));
    }

    public function live(Request $request): JsonResponse
    {
        if (!Auth::guard('consumer')->check()) {
            return response()->json([
                'success' => false,
                'messages' => [],
                'latest_id' => 0,
            ], 401);
        }

        $consumer = Auth::guard('consumer')->user()->consumer;
        $sinceId = max(0, (int) $request->integer('since_id', 0));

        $query = Complaint::where('consumer_id', $consumer->id)
            ->orderBy('id');

        if ($sinceId > 0) {
            $query->where('id', '>', $sinceId);
        }

        $messages = $query->get()->map(function (Complaint $complaint) {
            return [
                'id' => $complaint->id,
                'message' => $complaint->plainMessage(),
                'is_admin' => $complaint->isAdminReply(),
                'created_at' => $complaint->created_at?->toIso8601String(),
                'has_attachment' => !empty($complaint->attachment_path),
                'attachment_url' => !empty($complaint->attachment_path)
                    ? route('consumer.complaints.attachment', $complaint->id)
                    : null,
            ];
        })->values();

        $latestId = (int) ($messages->last()['id'] ?? $sinceId);

        return response()->json([
            'success' => true,
            'messages' => $messages,
            'latest_id' => $latestId,
        ]);
    }

    private function authorizeComplaint(Complaint $complaint, bool $mustBeConsumerMessage = false): Complaint
    {
        $consumerId = Auth::guard('consumer')->user()?->consumer?->id;

        if (!$consumerId || $complaint->consumer_id !== $consumerId) {
            abort(403, 'Unauthorized action.');
        }

        if ($mustBeConsumerMessage && $complaint->isAdminReply()) {
            abort(403, 'Unauthorized action.');
        }

        return $complaint;
    }

    private function sanitizeConsumerMessage(string $message): string
    {
        $cleanMessage = trim($message);
        $prefix = Complaint::ADMIN_REPLY_PREFIX;

        if (str_starts_with($cleanMessage, $prefix)) {
            $withoutPrefix = trim(substr($cleanMessage, strlen($prefix)));
            if ($withoutPrefix !== '') {
                return $withoutPrefix;
            }
        }

        return $cleanMessage;
    }
}
