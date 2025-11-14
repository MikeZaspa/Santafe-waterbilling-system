<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Billing;
use App\Models\Disconnection;
use App\Models\AdminConsumer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class ReadingController extends Controller
{
    public function __construct()
    {
        // Apply plumber middleware to all methods in this controller
        $this->middleware('plumber');
    }
    
    public function index()
    {
        // Check if plumber is authenticated using Auth guard
        if (!Auth::guard('plumber')->check()) {
            return redirect('/plumber-login')->with('error', 'Please login to access the dashboard.');
        }

        $plumber = Auth::guard('plumber')->user();
        
        // Count readings with both current and previous readings (completed)
        $completedCount = Billing::whereNotNull('current_reading')
                               ->whereNotNull('previous_reading')
                               ->count();
        
        // Count readings without current reading (pending)
        $pendingCount = Billing::whereNull('current_reading')->count();
        
        // Count reconnections (including those with fees)
        $reconnectionCount = Disconnection::where('status', 'reconnected')->count();

        // Calculate total reconnection fees collected this month
        $monthlyReconnectionFees = Disconnection::where('status', 'reconnected')
            ->whereMonth('reconnection_date', now()->month)
            ->whereYear('reconnection_date', now()->year)
            ->sum('reconnection_fee');

        // Count disconnected consumers
        $disconnectedCount = Disconnection::where('status', 'disconnected')->count();
        
        // Total count of all readings
        $totalCount = Billing::count();
        
        // Get monthly consumption data - INCLUDE RECONNECTED CONSUMERS
        $monthlyConsumption = Billing::select(
                DB::raw('MONTH(reading_date) as month'),
                DB::raw('SUM(consumption) as total_consumption')
            )
            ->whereNotNull('current_reading')
            ->whereNotNull('previous_reading')
            ->where('consumption', '>', 0) // Only include positive consumption
            ->whereYear('reading_date', date('Y'))
            ->groupBy(DB::raw('MONTH(reading_date)'))
            ->orderBy(DB::raw('MONTH(reading_date)'))
            ->get();
        
        // Prepare consumption data for all months
        $consumptionData = array_fill(0, 12, 0);
        foreach ($monthlyConsumption as $data) {
            $consumptionData[$data->month - 1] = floatval($data->total_consumption);
        }
        
        // Get monthly completed readings count - INCLUDE RECONNECTED CONSUMERS
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
        $recentDisconnections = Disconnection::with(['consumer'])
            ->where('status', 'disconnected')
            ->orderBy('disconnection_date', 'desc')
            ->limit(5)
            ->get();

        return view('auth.admin-plumber-dashboard', [
            'completedCount' => $completedCount,
            'pendingCount' => $pendingCount,
            'disconnectedCount' => $disconnectedCount,
            'reconnectionCount' => $reconnectionCount,
            'monthlyReconnectionFees' => $monthlyReconnectionFees,
            'totalCount' => $totalCount,
            'consumptionData' => $consumptionData,
            'completedData' => $completedData,
            'recentDisconnections' => $recentDisconnections,
            'plumber' => $plumber // Pass plumber data to view
        ]);
    }

    // Add plumber check to other methods
    public function reconnect(Request $request, $id)
    {
        // Check if plumber is authenticated
        if (!Auth::guard('plumber')->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Please login to perform this action.'
            ], 401);
        }

        try {
            DB::beginTransaction();

            $disconnection = Disconnection::with('consumer')->findOrFail($id);
            
            // Update disconnection record
            $disconnection->update([
                'status' => 'reconnected',
                'reconnection_date' => now()->format('Y-m-d H:i:s'),
                'reconnection_notes' => $request->notes,
                'reconnection_fee' => 500.00, // ₱500 reconnection fee
                'updated_at' => now(),
            ]);

            // Update consumer status to active
            $consumer = AdminConsumer::find($disconnection->consumer_id);
            if ($consumer) {
                $consumer->update([
                    'status' => 'active',
                    'updated_at' => now()
                ]);
            }

            // Create a new billing record for the reconnected consumer with current date
            $newBilling = Billing::create([
                'consumer_id' => $disconnection->consumer_id,
                'consumer_type' => $disconnection->consumer_type,
                'meter_no' => $disconnection->meter_no,
                'previous_reading' => $disconnection->current_reading, // Use the last reading as previous
                'current_reading' => $disconnection->current_reading, // Same as previous initially
                'consumption' => 0, // Zero consumption for reconnection
                'reading_date' => now()->format('Y-m-d'), // Current date
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Consumer reconnected successfully. Reconnection fee of ₱' . number_format(500, 2) . ' has been applied.',
                'consumer_name' => $consumer ? $consumer->first_name . ' ' . $consumer->last_name : 'Unknown Consumer'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Reconnection error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error reconnecting consumer: ' . $e->getMessage()
            ], 500);
        }
    }

    // Add authentication check to other methods...
    public function getDashboardData()
    {
        // Check if plumber is authenticated
        if (!Auth::guard('plumber')->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Please login to access dashboard data.'
            ], 401);
        }

        try {
            // ... rest of your existing code
        } catch (\Exception $e) {
            \Log::error('Dashboard data error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load dashboard data'
            ], 500);
        }
    }
}