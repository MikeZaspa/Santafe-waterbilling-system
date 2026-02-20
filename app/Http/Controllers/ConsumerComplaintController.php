<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
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
            ->latest()
            ->get();

        return view('auth.consumer-complaints', [
            'consumer' => $consumer,
            'complaints' => $complaints,
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

        Complaint::create([
            'consumer_id' => $consumer->id,
            'message' => $validated['message'],
            'attachment_path' => $attachmentPath,
        ]);

        return redirect()->route('consumer.complaints.index')->with('success', 'Complaint submitted successfully. Admin and plumber have been notified.');
    }

    public function update(Request $request, Complaint $complaint)
    {
        $complaint = $this->authorizeComplaint($complaint);

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

        $complaint->message = $validated['message'];
        $complaint->save();

        return redirect()->route('consumer.complaints.index')->with('success', 'Complaint updated successfully.');
    }

    public function destroy(Complaint $complaint)
    {
        $complaint = $this->authorizeComplaint($complaint);

        if (!empty($complaint->attachment_path) && Storage::disk('public')->exists($complaint->attachment_path)) {
            Storage::disk('public')->delete($complaint->attachment_path);
        }

        $complaint->delete();

        return redirect()->route('consumer.complaints.index')->with('success', 'Complaint deleted successfully.');
    }

    public function attachment(Complaint $complaint)
    {
        $complaint = $this->authorizeComplaint($complaint);

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
