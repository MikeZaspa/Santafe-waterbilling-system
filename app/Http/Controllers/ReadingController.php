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
    
    public function index()
    {
       // Temporary debug - remove after testing
    \Log::info('All session data:', Session::all());
    \Log::info('plumber_auth: ' . (Session::get('plumber_auth') ? 'TRUE' : 'FALSE'));
    \Log::info('plumber_logged_in: ' . (Session::get('plumber_logged_in') ? 'TRUE' : 'FALSE'));
    
    // Your existing check
    if (!Session::get('plumber_auth')) {
        abort(404, 'Page not found');
    }
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
            'recentDisconnections' => $recentDisconnections
        ]);
    }

    // Improved reconnect method with proper billing record creation
    public function reconnect(Request $request, $id)
    {
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

    // Method to get dashboard data for AJAX updates (for real-time updates)
    public function getDashboardData()
    {
        
        try {
            // Get current month and year
            $currentMonth = now()->month;
            $currentYear = now()->year;

            // Get consumption data for the current month including reconnected consumers
            $currentMonthConsumption = Billing::whereNotNull('current_reading')
                ->whereNotNull('previous_reading')
                ->where('consumption', '>', 0)
                ->whereMonth('reading_date', $currentMonth)
                ->whereYear('reading_date', $currentYear)
                ->sum('consumption');

            // Get completed readings count for current month
            $currentMonthCompleted = Billing::whereNotNull('current_reading')
                ->whereNotNull('previous_reading')
                ->whereMonth('reading_date', $currentMonth)
                ->whereYear('reading_date', $currentYear)
                ->count();

            // Get reconnection fees for current month
            $currentMonthReconnectionFees = Disconnection::where('status', 'reconnected')
                ->whereMonth('reconnection_date', $currentMonth)
                ->whereYear('reconnection_date', $currentYear)
                ->sum('reconnection_fee');

            return response()->json([
                'success' => true,
                'data' => [
                    'current_month_consumption' => floatval($currentMonthConsumption),
                    'current_month_completed' => $currentMonthCompleted,
                    'current_month_reconnection_fees' => floatval($currentMonthReconnectionFees),
                    'updated_at' => now()->toDateTimeString()
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Dashboard data error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load dashboard data'
            ], 500);
        }
    }

    // Add this method to get reconnected consumers for dashboard
    public function getReconnectedConsumers(Request $request)
    {
        try {
            $query = Disconnection::with(['consumer'])
                ->where('status', 'reconnected')
                ->orderBy('reconnection_date', 'desc');

            // Search filter
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->whereHas('consumer', function($consumerQuery) use ($search) {
                        $consumerQuery->where('first_name', 'like', "%{$search}%")
                                    ->orWhere('last_name', 'like', "%{$search}%")
                                    ->orWhere('middle_name', 'like', "%{$search}%");
                    })->orWhere('meter_no', 'like', "%{$search}%");
                });
            }

            // Date filter
            if ($request->has('date') && !empty($request->date)) {
                $query->whereDate('reconnection_date', $request->date);
            }

            $reconnectedConsumers = $query->get()
                ->map(function($disconnection) {
                    $consumer = $disconnection->consumer;
                    $fullName = $consumer ? 
                        trim($consumer->first_name . ' ' . 
                             ($consumer->middle_name ? $consumer->middle_name . ' ' : '') . 
                             $consumer->last_name . 
                             ($consumer->suffix ? ' ' . $consumer->suffix : '')) : 
                        'Unknown Consumer';

                    return [
                        'id' => $disconnection->id,
                        'consumer_name' => $fullName,
                        'meter_no' => $disconnection->meter_no,
                        'consumer_type' => $disconnection->consumer_type,
                        'reconnection_date' => $disconnection->reconnection_date,
                        'reconnection_fee' => $disconnection->reconnection_fee,
                        'formatted_date' => \Carbon\Carbon::parse($disconnection->reconnection_date)->format('M d, Y h:i A'),
                        'formatted_fee' => '₱' . number_format($disconnection->reconnection_fee, 2)
                    ];
                });

            return response()->json([
                'success' => true,
                'reconnected_consumers' => $reconnectedConsumers,
                'total_count' => $reconnectedConsumers->count()
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching reconnected consumers: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load reconnected consumers'
            ], 500);
        }
    }
}