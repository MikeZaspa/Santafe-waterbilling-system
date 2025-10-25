<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Billing;
use App\Models\Disconnection;
use App\Models\AdminConsumer;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReadingController extends Controller
{
    public function index()
{
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
    // Add this method to handle reconnection with fee
    public function reconnect(Request $request, $id)
{
    try {
        $disconnection = Disconnection::findOrFail($id);
        
        // Update disconnection record
        $disconnection->update([
            'status' => 'reconnected',
            'reconnection_date' => now()->format('Y-m-d'),
            'notes' => $request->notes . ' [Reconnection Fee: ₱' . number_format($disconnection->reconnection_fee, 2) . ']',
        ]);

        // Update consumer status to active
        $consumer = AdminConsumer::find($disconnection->consumer_id);
        if ($consumer) {
            $consumer->update(['status' => 'active']);
        }

        // Create a new billing record for the reconnected consumer
        Billing::create([
            'consumer_id' => $disconnection->consumer_id,
            'consumer_type' => $disconnection->consumer_type,
            'meter_no' => $disconnection->meter_no,
            'previous_reading' => $disconnection->current_reading,
            'current_reading' => $disconnection->current_reading, // Same as previous for now
            'consumption' => 0,
            'reading_date' => now()->format('Y-m-d'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Consumer reconnected successfully. Reconnection fee of ₱' . number_format($disconnection->reconnection_fee, 2) . ' has been applied.'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error reconnecting consumer: ' . $e->getMessage()
        ], 500);
    }
}
}