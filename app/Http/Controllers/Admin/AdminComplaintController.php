<?php

namespace App\Http\Controllers\Admin;

use App\Models\Complaint;
use App\Models\ComplaintMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;

class AdminComplaintController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin.auth');
    }

    public function index()
    {
        $complaints = Complaint::with(['consumer', 'messages'])
            ->latest('last_message_at')
            ->paginate(15);

        return view('admin.complaints.index', compact('complaints'));
    }

    public function show(Complaint $complaint)
    {
        $complaint->markMessagesAsRead();

        return response()->json([
            'success' => true,
            'complaint' => [
                'id' => $complaint->id,
                'subject' => $complaint->subject,
                'status' => $complaint->status,
                'consumer' => [
                    'id' => $complaint->consumer->id,
                    'name' => $complaint->consumer->first_name . ' ' . $complaint->consumer->last_name,
                    'email' => $complaint->consumer->email,
                    'phone' => $complaint->consumer->phone,
                ],
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

    public function addReply(Request $request, Complaint $complaint)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:5000',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
            'status' => 'nullable|in:open,in_progress,resolved,closed',
        ]);

        $admin = Auth::guard('admin')->user();
        $attachmentPath = null;

        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('complaints', 'public');
        }

        $complaint->addMessage('admin', 'Admin', $validated['message'], $attachmentPath);

        if (isset($validated['status'])) {
            $complaint->update(['status' => $validated['status']]);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Reply sent successfully.',
            ]);
        }

        return redirect()->back()->with('success', 'Reply sent successfully.');
    }

    public function updateStatus(Request $request, Complaint $complaint)
    {
        $validated = $request->validate([
            'status' => 'required|in:open,in_progress,resolved,closed',
        ]);

        $complaint->update(['status' => $validated['status']]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully.',
            ]);
        }

        return redirect()->back()->with('success', 'Status updated successfully.');
    }

    public function attachment(Complaint $complaint, $messageId = null)
    {
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
}
