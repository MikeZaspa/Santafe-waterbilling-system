<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\ComplaintMessage;
use App\Models\Notification;
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
            ->latest('last_message_at')
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

    public function show(Complaint $complaint)
    {
        $this->authorizeComplaint($complaint);

        $consumer = Auth::guard('consumer')->user()->consumer;
        $complaint->markMessagesAsRead();

        return response()->json([
            'success' => true,
            'complaint' => [
                'id' => $complaint->id,
                'subject' => $complaint->subject,
                'status' => $complaint->status,
                'created_at' => $complaint->created_at->format('M d, Y h:i A'),
            ],
            'messages' => $complaint->messages()->get()->map(function ($msg) {
                return [
                    'id' => $msg->id,
                    'sender_type' => $msg->sender_type,
                    'sender_name' => $msg->sender_name,
                    'message' => $msg->message,
                    'attachment_path' => $msg->attachment_path,
                    'has_attachment' => !empty($msg->attachment_path),
                    'created_at' => $msg->created_at->format('M d, Y h:i A'),
                ];
            }),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
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
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'attachment_path' => $attachmentPath,
            'status' => 'open',
        ]);

        // Create initial message in the conversation thread
        $complaint->addMessage('consumer', $consumer->first_name . ' ' . $consumer->last_name, $validated['message'], $attachmentPath);

        return redirect()->route('consumer.complaints.index')->with('success', 'Complaint submitted successfully. Admin will respond shortly.');
    }

    public function addReply(Request $request, Complaint $complaint)
    {
        $this->authorizeComplaint($complaint);

        $validated = $request->validate([
            'message' => 'required|string|max:5000',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
        ]);

        $consumer = Auth::guard('consumer')->user()->consumer;
        $attachmentPath = null;

        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('complaints', 'public');
        }

        $complaint->addMessage('consumer', $consumer->first_name . ' ' . $consumer->last_name, $validated['message'], $attachmentPath);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Reply sent successfully.',
            ]);
        }

        return redirect()->route('consumer.complaints.index')->with('success', 'Reply sent successfully.');
    }

    public function update(Request $request, Complaint $complaint)
    {
        $complaint = $this->authorizeComplaint($complaint);

        $validated = $request->validate([
            'subject' => 'nullable|string|max:255',
            'status' => 'nullable|in:open,in_progress,resolved,closed',
        ]);

        if (isset($validated['subject'])) {
            $complaint->subject = $validated['subject'];
        }

        if (isset($validated['status'])) {
            $complaint->status = $validated['status'];
        }

        $complaint->save();

        return redirect()->route('consumer.complaints.index')->with('success', 'Complaint updated successfully.');
    }

    public function destroy(Complaint $complaint)
    {
        $complaint = $this->authorizeComplaint($complaint);

        // Delete all attachments
        $complaint->messages()->each(function ($message) {
            if (!empty($message->attachment_path) && Storage::disk('public')->exists($message->attachment_path)) {
                Storage::disk('public')->delete($message->attachment_path);
            }
        });

        if (!empty($complaint->attachment_path) && Storage::disk('public')->exists($complaint->attachment_path)) {
            Storage::disk('public')->delete($complaint->attachment_path);
        }

        $complaint->delete();

        return redirect()->route('consumer.complaints.index')->with('success', 'Complaint deleted successfully.');
    }

    public function attachment(Complaint $complaint, $messageId = null)
    {
        $this->authorizeComplaint($complaint);

        if ($messageId) {
            $message = ComplaintMessage::find($messageId);
            if (!$message || $message->complaint_id !== $complaint->id) {
                abort(404, 'Message not found.');
            }

            if (empty($message->attachment_path) || !Storage::disk('public')->exists($message->attachment_path)) {
                abort(404, 'Attachment not found.');
            }

            return response()->file(storage_path('app/public/' . $message->attachment_path));
        }

        if (empty($complaint->attachment_path) || !Storage::disk('public')->exists($complaint->attachment_path)) {
            abort(404, 'Attachment not found.');
        }

        return response()->file(storage_path('app/public/' . $complaint->attachment_path));
    }

    private function authorizeComplaint(Complaint $complaint): Complaint
    {
        $consumerId = Auth::guard('consumer')->user()?->consumer?->id;

        if (!$consumerId || $complaint->consumer_id !== $consumerId) {
            abort(403, 'Unauthorized action.');
        }

        return $complaint;
    }
}
