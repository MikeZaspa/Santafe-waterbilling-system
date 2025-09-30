<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Billing;
use App\Models\Disconnection; // Add this line
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReadingController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        
        // Count readings with both current and previous readings (completed)
        $completedCount = Billing::whereNotNull('current_reading')
                               ->whereNotNull('previous_reading')
                               ->count();
        
        // Count readings without current reading (pending)
        $pendingCount = Billing::whereNull('current_reading')->count();
        
        $reconnectionCount = Disconnection::where('status', 'reconnected')->count();

        // Count disconnected consumers - FIXED: Added import and corrected query
        $disconnectedCount = Disconnection::where('status', 'disconnected')->count();
        
        // Total count of all readings
        $totalCount = Billing::count();
        
        // Get monthly consumption data
        $monthlyConsumption = Billing::select(
                DB::raw('MONTH(reading_date) as month'),
                DB::raw('SUM(current_reading - previous_reading) as total_consumption')
            )
            ->whereNotNull('current_reading')
            ->whereNotNull('previous_reading')
            ->whereYear('reading_date', date('Y'))
            ->groupBy(DB::raw('MONTH(reading_date)'))
            ->orderBy(DB::raw('MONTH(reading_date)'))
            ->get();
        
        // Prepare consumption data for all months
        $consumptionData = array_fill(0, 12, 0);
        foreach ($monthlyConsumption as $data) {
            $consumptionData[$data->month - 1] = $data->total_consumption;
        }
        
        // Get monthly completed readings count
        $monthlyCompleted = Billing::select(
                DB::raw('MONTH(reading_date) as month'),
                DB::raw('COUNT(*) as count')
            )
            ->whereNotNull('current_reading')
            ->whereNotNull('previous_reading')
            ->whereYear('reading_date', date('Y'))
            ->groupBy(DB::raw('MONTH(reading_date)'))
            ->orderBy(DB::raw('MONTH(reading_date)'))
            ->get();
        
        // Prepare completed readings data for all months
        $completedData = array_fill(0, 12, 0);
        foreach ($monthlyCompleted as $data) {
            $completedData[$data->month - 1] = $data->count;
        }

        // Get recent disconnections for dashboard
        $recentDisconnections = Disconnection::with(['billing.consumer'])
            ->where('status', 'disconnected')
            ->orderBy('disconnection_date', 'desc')
            ->limit(5)
            ->get();

       return view('auth.admin-plumber-dashboard', [
            'completedCount' => $completedCount,
            'pendingCount' => $pendingCount,
            'disconnectedCount' => $disconnectedCount,
            'reconnectionCount' => $reconnectionCount, // Add this line
            'totalCount' => $totalCount,
            'consumptionData' => $consumptionData,
            'completedData' => $completedData,
            'recentDisconnections' => $recentDisconnections
        ]);
    }
}