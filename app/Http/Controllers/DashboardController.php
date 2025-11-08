<?php
namespace App\Http\Controllers;

use App\Models\AdminConsumer;
use App\Models\AdminLog;
use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

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
        
        // Get monthly consumer growth data (this is a simplified example)
        $monthlyGrowth = AdminConsumer::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('count')
            ->toArray();

        return view('auth.admin-dashboard', compact(
            'totalConsumers',
            'activeConsumers',
            'inactiveConsumers',
            'monthlyGrowth'
        ));
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