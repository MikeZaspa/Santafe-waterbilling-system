<?php

namespace App\Http\Controllers;

use App\Models\AdminLog;
use App\Models\Admin;
use Illuminate\Http\Request;

class AdminLogController extends Controller
{
    public function index()
    {
        try {
            $logs = AdminLog::with('admin')
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            // Get all admins for the filter dropdown
            $admins = Admin::all();

            // Debug: Check if data is being retrieved
            \Log::info('Admin logs retrieved:', ['count' => $logs->count()]);

            return view('auth.admin-logs', compact('logs', 'admins'));
            
        } catch (\Exception $e) {
            \Log::error('Error retrieving admin logs: ' . $e->getMessage());
            return view('auth.admin-logs')->with('error', 'Error loading logs: ' . $e->getMessage());
        }
    }

    public function show(AdminLog $log)
    {
        return view('auth.admin-log-detail', compact('log'));
    }

    public function filter(Request $request)
    {
        $query = AdminLog::with('admin');

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

        $logs = $query->orderBy('created_at', 'desc')->paginate(20);
        
        // Get all admins for the filter dropdown
        $admins = Admin::all();

        return view('auth.admin-logs', compact('logs', 'admins'));
    }
}