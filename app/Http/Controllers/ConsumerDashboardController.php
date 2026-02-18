<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ConsumerAccount;
use App\Models\Billing;
use App\Models\Consumer;
use App\Models\Notice;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ConsumerDashboardController extends Controller
{
    /**
     * Display consumer dashboard with billing information
     */
    public function index()
    {
        // Check if consumer is authenticated
        if (!Auth::guard('consumer')->check()) {
            return redirect('/consumer/login');
        }
        
        $account = Auth::guard('consumer')->user();
        $consumer = $account->consumer;
        
        // Get the consumer's bills
        $bills = $consumer->billings()->with('consumer')->orderBy('created_at', 'desc')->get();
        
        // Calculate bill counts
        $paidCount = $bills->where('status', 'paid')->count();
        $unpaidCount = $bills->where('status', 'unpaid')->count();
        $overdueCount = $bills->where('status', 'overdue')->count();
        $totalCount = $bills->count();
        
        // Get notices for this consumer
        $notices = Notice::with('consumer')
            ->where('consumer_id', $consumer->id)
            ->orderBy('created_at', 'desc')
            ->get();
        $notifications = Notification::where('consumer_id', $consumer->id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Get recent bills (latest 5)
        $recentBills = $bills->take(5);
        
        // Prepare data for monthly consumption chart
        $monthlyConsumption = [];
        $monthlyLabels = [];
        
        // Get bills from the last 12 months - using created_at as fallback
        $monthlyBills = $consumer->billings()
            ->where('created_at', '>=', Carbon::now()->subMonths(12))
            ->orderBy('created_at', 'asc')
            ->get();
        
        // Group bills by month and calculate total consumption
        $monthlyData = [];
        foreach ($monthlyBills as $bill) {
            // Try different possible date fields
            $dateField = 'created_at';
            if (isset($bill->billing_month)) {
                $dateField = 'billing_month';
            } elseif (isset($bill->bill_date)) {
                $dateField = 'bill_date';
            } elseif (isset($bill->due_date)) {
                $dateField = 'due_date';
            }
            
            $month = Carbon::parse($bill->$dateField)->format('M Y');
            if (!isset($monthlyData[$month])) {
                $monthlyData[$month] = 0;
            }
            
            // Try different possible consumption fields
            if (isset($bill->consumption)) {
                $monthlyData[$month] += $bill->consumption;
            } elseif (isset($bill->volume)) {
                $monthlyData[$month] += $bill->volume;
            } elseif (isset($bill->usage)) {
                $monthlyData[$month] += $bill->usage;
            }
        }
        
        // Prepare data for chart
        foreach ($monthlyData as $month => $consumption) {
            $monthlyLabels[] = $month;
            $monthlyConsumption[] = $consumption;
        }
        
        return view('auth.dashboard-consumer', [
            'consumer' => $consumer,
            'bills' => $bills,
            'recentBills' => $recentBills,
            'notices' => $notices,
            'notifications' => $notifications,
            'paidCount' => $paidCount,
            'unpaidCount' => $unpaidCount,
            'overdueCount' => $overdueCount,
            'totalCount' => $totalCount,
            'monthlyLabels' => $monthlyLabels,
            'monthlyConsumption' => $monthlyConsumption
        ]);
    }
}
