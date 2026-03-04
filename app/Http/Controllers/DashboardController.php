<?php
namespace App\Http\Controllers;

use App\Models\AdminConsumer;
use App\Models\AdminLog;
use App\Models\Admin;
use App\Models\Complaint;
use App\Models\Notification;
use App\Models\Plumber;
use App\Models\Accountant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    
    public function index()
    {
          // Check if consumer is authenticated
        if (!Auth::guard('admin')->check()) {
            return redirect('/admin-login');
        }

        $totalConsumers = AdminConsumer::count();
        $activeConsumers = AdminConsumer::where('status', 'active')->count();
        $inactiveConsumers = AdminConsumer::where('status', 'inactive')->count();
        $totalPlumbers = Plumber::count();
        $totalAccountants = Accountant::count();
        
        // Get monthly consumer growth data (this is a simplified example)
        $monthlyGrowth = AdminConsumer::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('count')
            ->toArray();

        $complaints = Complaint::with(['consumer:id,first_name,middle_name,last_name,suffix,meter_no'])
            ->orderBy('created_at')
            ->get();

        $complaintConversations = $complaints
            ->groupBy('consumer_id')
            ->map(function ($messages) {
                $consumer = optional($messages->first())->consumer;
                $consumerName = trim(implode(' ', array_filter([
                    $consumer?->first_name,
                    $consumer?->middle_name,
                    $consumer?->last_name,
                    $consumer?->suffix,
                ])));
                $lastMessage = $messages->last();

                return [
                    'consumer_id' => $messages->first()->consumer_id,
                    'consumer_name' => $consumerName ?: 'Unknown Consumer',
                    'meter_no' => $consumer?->meter_no ?? 'N/A',
                    'messages' => $messages,
                    'last_message' => $lastMessage?->plainMessage() ?? '',
                    'last_message_at' => $lastMessage?->created_at,
                ];
            })
            ->sortByDesc(function (array $conversation) {
                return $conversation['last_message_at'] ?? now()->subYears(50);
            })
            ->take(20)
            ->values();

        $totalComplaints = $complaints->count();

        return view('auth.admin-dashboard', compact(
            'totalConsumers',
            'activeConsumers',
            'inactiveConsumers',
            'totalPlumbers',
            'totalAccountants',
            'monthlyGrowth',
            'complaintConversations',
            'totalComplaints'
        ));
    }

    public function replyToComplaint(Request $request)
    {
        if (!Auth::guard('admin')->check()) {
            return redirect('/admin-login');
        }

        $validated = $request->validate([
            'consumer_id' => 'required|exists:admin_consumers,id',
            'message' => 'required|string|max:5000',
        ]);

        $consumerId = (int) $validated['consumer_id'];
        $complaint = Complaint::create([
            'consumer_id' => (int) $validated['consumer_id'],
            'message' => Complaint::ADMIN_REPLY_PREFIX . trim($validated['message']),
        ]);

        $consumer = AdminConsumer::select(['id', 'first_name', 'middle_name', 'last_name', 'suffix', 'meter_no'])
            ->find($consumerId);
        $consumerName = trim(implode(' ', array_filter([
            $consumer?->first_name,
            $consumer?->middle_name,
            $consumer?->last_name,
            $consumer?->suffix,
        ])));

        Notification::create([
            'consumer_id' => $consumerId,
            'billing_id' => null,
            'title' => 'Complaint Reply',
            'message' => 'Admin replied to your complaint: ' . Str::limit($validated['message'], 120),
            'type' => 'system',
            'is_read' => false,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'complaint' => [
                    'id' => $complaint->id,
                    'consumer_id' => $consumerId,
                    'consumer_name' => $consumerName !== '' ? $consumerName : 'Unknown Consumer',
                    'meter_no' => $consumer?->meter_no ?? 'N/A',
                    'message' => $complaint->plainMessage(),
                    'has_attachment' => false,
                    'attachment_url' => null,
                    'is_admin' => true,
                    'created_at' => $complaint->created_at?->toIso8601String(),
                ],
            ]);
        }

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'Reply sent successfully.')
            ->with('open_chat_consumer_id', $consumerId);
    }

    public function destroyComplaintConversation(AdminConsumer $consumer)
    {
        if (!Auth::guard('admin')->check()) {
            return redirect('/admin-login');
        }

        $complaints = Complaint::where('consumer_id', $consumer->id)->get();

        if ($complaints->isEmpty()) {
            return redirect()->route('admin.dashboard')->with('success', 'No conversation to delete.');
        }

        foreach ($complaints as $complaint) {
            if (!empty($complaint->attachment_path) && Storage::disk('public')->exists($complaint->attachment_path)) {
                Storage::disk('public')->delete($complaint->attachment_path);
            }
        }

        Complaint::where('consumer_id', $consumer->id)->delete();

        return redirect()->route('admin.dashboard')->with('success', 'Consumer complaint conversation deleted successfully.');
    }

    public function complaintAttachment(Request $request, Complaint $complaint)
    {
        if (!Auth::guard('admin')->check()) {
            return redirect('/admin-login');
        }

        if (empty($complaint->attachment_path) || !Storage::disk('public')->exists($complaint->attachment_path)) {
            abort(404, 'Attachment not found.');
        }

        $rawUrl = route('admin.complaints.attachment', [
            'complaint' => $complaint->id,
            'raw' => 1,
        ]);

        if ($request->boolean('preview') && !$request->boolean('raw')) {
            $mimeType = (string) Storage::disk('public')->mimeType($complaint->attachment_path);

            return response()->view('auth.partials.complaint-attachment-preview', [
                'rawUrl' => $rawUrl,
                'fileName' => basename($complaint->attachment_path),
                'isImage' => str_starts_with($mimeType, 'image/'),
                'isPdf' => $mimeType === 'application/pdf',
            ]);
        }

        return response()->file(storage_path('app/public/' . $complaint->attachment_path));
    }
    
    /**
     * API endpoint to get admin logs with pagination and filtering
     */
    public function getAdminLogs(Request $request)
    {
        $query = AdminLog::with('admin');
        
        // Apply filters
        if ($request->filled('admin_id')) {
            $query->where('admin_id', $request->admin_id);
        }
        
        if ($request->filled('activity')) {
            $query->where('activity', 'like', '%' . $request->activity . '%');
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('login_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('login_at', '<=', $request->date_to);
        }
        
        $logs = $query->orderBy('login_at', 'desc')->paginate(20);
        
        // Calculate statistics
        $statistics = [
            'total' => AdminLog::count(),
            'successful' => AdminLog::where('activity', 'like', '%successful%')->count(),
            'failed' => AdminLog::where('activity', 'like', '%failed%')->count(),
            'active' => AdminLog::whereNull('logout_at')->count(),
        ];
        
        return response()->json([
            'logs' => $logs,
            'statistics' => $statistics
        ]);
    }
    
    /**
     * API endpoint to get all admins for the filter dropdown
     */
    public function getAdmins()
    {
        $admins = Admin::select('id', 'email', 'first_name', 'last_name')->get();
        return response()->json($admins);
    }

}
