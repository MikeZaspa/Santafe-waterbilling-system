<?php

namespace App\Http\Controllers;

use App\Models\AccountantBilling;
use App\Models\AdminConsumer;
use App\Models\Billing;
use App\Models\WaterRate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use App\Models\Notification;
class AccountantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin-accountant-consumer');
    }
    /**z
     * Get billings data for DataTables
     */
    /**
 * Get billings data for DataTables
 */
public function getBillings(Request $request)
{
    // Add whereNull('deleted_at') to exclude soft deleted records
    $query = AccountantBilling::with(['consumer' => function($query) {
        $query->select('id', 'first_name', 'last_name');
    }])
    ->whereNull('deleted_at') // This excludes soft deleted records
    ->orderBy('created_at', 'desc');

    // Apply filters
    if ($request->has('status') && $request->status) {
        $query->where('status', $request->status);
    }

    if ($request->has('month') && $request->month) {
        $query->whereMonth('due_date', Carbon::parse($request->month)->month)
              ->whereYear('due_date', Carbon::parse($request->month)->year);
    }

    return datatables()->eloquent($query)
        ->addIndexColumn()
        ->addColumn('consumer_name', function($billing) {
            return $billing->consumer ? $billing->consumer->first_name . ' ' . $billing->consumer->last_name : 'N/A';
        })
        ->editColumn('due_date', function($billing) {
            return $billing->due_date ? $billing->due_date->format('M d, Y') : '';
        })
        ->editColumn('total_amount', function($billing) {
            return '₱' . number_format($billing->total_amount, 2);
        })
        ->editColumn('status', function($billing) {
            return $billing->status;
        })
        ->addColumn('actions', function($billing) {
            return $billing->id;
        })
        ->rawColumns(['status', 'actions'])
        ->toJson();
}
    /**
     * Store a newly created resource in storage.
     */
   public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'consumer_id' => 'required|exists:admin_consumers,id',
        'current_reading' => 'nullable|numeric|min:0',
        'payment_method' => 'nullable|string|in:cash,gcash,maya',
        'due_date' => 'required|date',
        'status' => 'required|in:paid,unpaid,overdue',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors()
        ], 422);
    }

    try {
        DB::beginTransaction();

        $consumer = AdminConsumer::findOrFail($request->consumer_id);
        $dueDate = Carbon::parse($request->due_date);
        
        // Check if there's already a billing for this consumer in the same month/year
        $existingBilling = AccountantBilling::where('consumer_id', $consumer->id)
            ->whereMonth('due_date', $dueDate->month)
            ->whereYear('due_date', $dueDate->year)
            ->first();

        if ($existingBilling) {
            // If the existing billing is paid, return the billing details
            if ($existingBilling->status === 'paid') {
                $billingWithConsumer = AccountantBilling::with('consumer')->find($existingBilling->id);
                
                // Calculate next month due date
                $nextMonthDueDate = Carbon::parse($existingBilling->due_date);
                $nextMonthDueDate->addMonth();
                
                return response()->json([
                    'success' => false,
                    'errors' => [
                        'paid' => ['This consumer has already paid for this month.']
                    ],
                    'type' => 'paid',
                    'data' => $billingWithConsumer,
                    'next_month_due_date' => $nextMonthDueDate->format('Y-m-d')
                ], 422);
            } else {
                // If the existing billing is unpaid or overdue
                return response()->json([
                    'success' => false,
                    'errors' => [
                        'unpaid' => ['This consumer already has an unpaid billing for this month.']
                    ],
                    'type' => 'unpaid',
                    'data' => $existingBilling
                ], 422);
            }
        }

        // Get plumber reading for the selected billing month
        $monthlyReading = Billing::where('consumer_id', $consumer->id)
            ->whereMonth('reading_date', $dueDate->month)
            ->whereYear('reading_date', $dueDate->year)
            ->orderBy('reading_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$monthlyReading) {
            return response()->json([
                'success' => false,
                'errors' => [
                    'reading' => ['No reading found for ' . $dueDate->format('F Y') . '. Please add monthly reading first in Admin Plumber Consumer.']
                ]
            ], 422);
        }

        $previousReading = (float) $monthlyReading->previous_reading;
        $currentReading = (float) $monthlyReading->current_reading;

        // Validate current reading
        if ($currentReading < $previousReading) {
            return response()->json([
                'success' => false,
                'errors' => [
                    'reading' => ['Current reading cannot be less than the previous reading (' . $previousReading . ').']
                ]
            ], 422);
        }

        // Calculate consumption & total
        $consumption = $currentReading - $previousReading;
        $totalAmount = $this->calculateWaterBill($consumer->consumer_type, $consumption);
        
        // Calculate penalty if status is overdue
        $penaltyAmount = 0.00;
        if ($request->status === 'overdue') {
            $penaltyAmount = $this->calculatePenalty($request->due_date);
        }
        
        // Validate that not all readings are zero and total amount is not zero
        if ($previousReading == 0 && $currentReading == 0 && $consumption == 0 && $totalAmount == 0) {
            return response()->json([
                'success' => false,
                'errors' => [
                    'reading' => ['Cannot create billing with zero readings and zero amount. Please enter valid meter readings.']
                ]
            ], 422);
        }

        // Prepare billing data
        $billingData = [
            'consumer_id' => $consumer->id,
            'consumer_type' => $consumer->consumer_type,
            'meter_no' => $consumer->meter_no,
            'due_date' => $request->due_date,
            'previous_reading' => $previousReading,
            'current_reading' => $currentReading,
            'consumption' => $consumption,
            'payment_method' => $request->payment_method,
            'total_amount' => $totalAmount,
            'penalty_amount' => $penaltyAmount,
            'status' => $request->status,
        ];

        // Set payment date if status is paid
        if ($request->status === 'paid') {
            $billingData['payment_date'] = Carbon::now();
            
            // If paid after due date, add penalty
            if (Carbon::now()->greaterThan(Carbon::parse($request->due_date))) {
                $billingData['penalty_amount'] = $this->calculatePenalty($request->due_date, Carbon::now());
            }
        }

        $billing = AccountantBilling::create($billingData);

        // Create notification for the consumer
        Notification::create([
            'consumer_id' => $consumer->id,
            'billing_id' => $billing->id,
            'title' => 'New Billing Available',
            'message' => "A new billing for {$dueDate->format('F Y')} has been generated. Amount due: ₱" . number_format($totalAmount, 2),
            'type' => 'billing',
            'is_read' => false
        ]);

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Billing record created successfully',
            'data' => $billing
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => 'Error creating billing: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $billing = AccountantBilling::with('consumer')->findOrFail($id);
        return response()->json($billing);
    }

    /**
     * Show the form for editing the specified resource.
     */
    /**
 * Show the form for editing the specified resource.
 */
public function edit($id)
{
    try {
        $billing = AccountantBilling::with('consumer')->findOrFail($id);
        
        // Format the billing data for frontend
        $formattedBilling = [
            'id' => $billing->id,
            'previous_reading' => (float)$billing->previous_reading,
            'current_reading' => (float)$billing->current_reading,
            'consumption' => (float)$billing->consumption,
            'total_amount' => (float)$billing->total_amount,
            'due_date' => $billing->due_date->format('Y-m-d'),
            'status' => $billing->status,
        ];

        // Format the consumer data
        $formattedConsumer = [
            'id' => $billing->consumer->id,
            'first_name' => $billing->consumer->first_name,
            'middle_name' => $billing->consumer->middle_name,
            'last_name' => $billing->consumer->last_name,
            'suffix' => $billing->consumer->suffix,
            'consumer_type' => $billing->consumer->consumer_type,
            'meter_no' => $billing->consumer->meter_no,
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'billing' => $formattedBilling,
                'consumer' => $formattedConsumer
            ]
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to load billing data: ' . $e->getMessage()
        ], 404);
    }
}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
{
    $validator = Validator::make($request->all(), [
        'current_reading' => 'required|numeric|min:0',
        'due_date' => 'required|date',
        'status' => 'required|in:paid,unpaid,overdue',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors()
        ], 422);
    }

    try {
        DB::beginTransaction();

        $billing = AccountantBilling::findOrFail($id);
        $consumer = $billing->consumer;

        $previousReading = $billing->previous_reading;
        $currentReading = $request->current_reading;
        
        // Validate current reading is not less than previous
        if ($currentReading < $previousReading) {
            return response()->json([
                'success' => false,
                'message' => 'Current reading cannot be less than previous reading'
            ], 422);
        }

        $consumption = $currentReading - $previousReading;

        // Recalculate total amount if reading changed
        if ($currentReading != $billing->current_reading) {
            $totalAmount = $this->calculateWaterBill($consumer->consumer_type, $consumption);
        } else {
            $totalAmount = $billing->total_amount;
        }

        // Calculate penalty
        $penaltyAmount = 0.00;
        if ($request->status === 'overdue') {
            $penaltyAmount = $this->calculatePenalty($request->due_date);
        } elseif ($request->status === 'paid') {
            // If changing to paid status after due date, add penalty
            if (Carbon::now()->greaterThan(Carbon::parse($request->due_date))) {
                $penaltyAmount = $this->calculatePenalty($request->due_date, Carbon::now());
            }
        }

        $updateData = [
            'due_date' => $request->due_date,
            'current_reading' => $currentReading,
            'consumption' => $consumption,
            'total_amount' => $totalAmount,
            'penalty_amount' => $penaltyAmount,
            'status' => $request->status,
        ];

        // Set payment date if status is paid
        if ($request->status === 'paid') {
            $updateData['payment_date'] = Carbon::now();
        } else {
            $updateData['payment_date'] = null;
        }

        $billing->update($updateData);

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Billing updated successfully!'
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => 'Error updating billing: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Remove the specified resource from storage.
     */
    /**
 * Remove the specified resource from storage.
 */
/**
 * Remove the specified resource from storage.
 */
public function destroy($id)
{
    try {
        $billing = AccountantBilling::findOrFail($id);
        
        // Check if billing is paid
        if ($billing->status === 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete paid billing records. Please archive instead.'
            ], 422);
        }

        // Use forceDelete to permanently remove from database
        $billing->forceDelete();

        return response()->json([
            'success' => true,
            'message' => 'Billing deleted successfully!'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error deleting billing: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Get last reading for a consumer
     */
    public function getLastReading(Request $request, $consumerId)
    {
        $query = Billing::where('consumer_id', $consumerId);

        if ($request->filled('month')) {
            $monthDate = Carbon::parse($request->month);
            $query->whereMonth('reading_date', $monthDate->month)
                ->whereYear('reading_date', $monthDate->year);
        }

        $lastReading = $query
            ->orderBy('reading_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->first(['previous_reading', 'current_reading', 'reading_date']);

        return response()->json([
            'success' => true,
            'data' => $lastReading ?: [
                'previous_reading' => 0,
                'current_reading' => 0,
                'reading_date' => null
            ]
        ]);
    }

    /**
     * Calculate water bill based on consumption and rates
     */
     public function calculateWaterBill($type, $consumption)
    {
        $rates = WaterRate::where('type', $type)
                  ->orderBy('range')
                  ->get();

        if ($rates->isEmpty()) {
            throw new \Exception("No water rates defined for {$type} type");
        }

        $consumption = max(0, (float) $consumption);
        $totalAmount = 0;

        try {
            if ($type === 'commercial') {
                // 0-10: fixed
                $fixedRate = $rates->where('range', '0-10')->first();
                if ($consumption > 0 && $fixedRate) {
                    // Stored amount is per cu.m rate; first 10 cu.m is billed as fixed block.
                    $totalAmount += $fixedRate->amount * 10;
                }

                // 11-20: per unit
                $rate_11_20 = $rates->where('range', '11-20')->first();
                if ($consumption > 10 && $rate_11_20) {
                    $units_11_20 = min($consumption, 20) - 10;
                    $totalAmount += max(0, $units_11_20) * $rate_11_20->amount;
                }

                // 21-30: per unit
                $rate_21_30 = $rates->where('range', '21-30')->first();
                if ($consumption > 20 && $rate_21_30) {
                    $units_21_30 = min($consumption, 30) - 20;
                    $totalAmount += max(0, $units_21_30) * $rate_21_30->amount;
                }
                
                // 31+: per unit
                $rate_31_plus = $rates->first(function ($rate) {
                    return str_contains($rate->range, '+');
                });
                if ($consumption > 30) {
                    $units_31_plus = $consumption - 30;
                    $rate31PlusAmount = $rate_31_plus ? $rate_31_plus->amount : 29;
                    $totalAmount += max(0, $units_31_plus) * $rate31PlusAmount;
                }
            } elseif ($type === 'institutional') {
                // 0-5: free

                // 6-15: fixed
                $fixedRate = $rates->where('range', '6-15')->first();
                if ($consumption >= 6 && $fixedRate) {
                    // Stored amount is per cu.m rate; 6-15 is billed as fixed 10-cu.m block.
                    $totalAmount += $fixedRate->amount * 10;
                }

                // 16-25: per unit
                $rate_16_25 = $rates->where('range', '16-25')->first();
                if ($consumption > 15 && $rate_16_25) {
                    $units_16_25 = min($consumption, 25) - 15;
                    $totalAmount += max(0, $units_16_25) * $rate_16_25->amount;
                }

                // 26+: per unit
                $rate_26_plus = $rates->first(function ($rate) {
                    return str_contains($rate->range, '+');
                });
                if ($consumption > 25) {
                    $units_26_plus = max(0, $consumption - 25);
                    $rate26PlusAmount = $rate_26_plus ? $rate_26_plus->amount : 17;
                    $totalAmount += $units_26_plus * $rate26PlusAmount;
                }
            } else {
                // 0-10: fixed
                $fixedRate = $rates->where('range', '0-10')->first();
                if ($consumption > 0 && $fixedRate) {
                    // Stored amount is per cu.m rate; first 10 cu.m is billed as fixed block.
                    $totalAmount += $fixedRate->amount * 10;
                }

                // 11-20: per unit
                $rate_11_20 = $rates->where('range', '11-20')->first();
                if ($consumption > 10 && $rate_11_20) {
                    $units_11_20 = min($consumption, 20) - 10;
                    $totalAmount += max(0, $units_11_20) * $rate_11_20->amount;
                }

                // 21-30: per unit
                $rate_21_30 = $rates->where('range', '21-30')->first();
                if ($consumption > 20 && $rate_21_30) {
                    $units_21_30 = min($consumption, 30) - 20;
                    $totalAmount += max(0, $units_21_30) * $rate_21_30->amount;
                }

                // 31+: per unit
                $rate_31_plus = $rates->first(function ($rate) {
                    return str_contains($rate->range, '+');
                });
                if ($consumption > 30) {
                    $units_31_plus = $consumption - 30;
                    $rate31PlusAmount = $rate_31_plus ? $rate_31_plus->amount : 19;
                    $totalAmount += max(0, $units_31_plus) * $rate31PlusAmount;
                }
            }

            return round($totalAmount, 2);

        } catch (\Exception $e) {
            \Log::error("Water billing calculation error: " . $e->getMessage(), [
                'type' => $type,
                'consumption' => $consumption,
                'rates' => $rates->toArray()
            ]);
            throw new \Exception("Error calculating water bill: " . $e->getMessage());
        }
    }

public function getBillingDetails($id)
{
    try {
        $billing = AccountantBilling::with('consumer')->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => $billing
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to load billing data: ' . $e->getMessage()
        ], 404);
    }
}
public function getReceiptData($id)
{
    try {
        $billing = AccountantBilling::with('consumer')->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => $billing
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to load receipt data: ' . $e->getMessage()
        ], 404);
    }
}
public function calculatePenalty($dueDate, $paymentDate = null)
{
    $due = Carbon::parse($dueDate)->startOfDay();
    $paidOrNow = $paymentDate ? Carbon::parse($paymentDate)->startOfDay() : Carbon::now()->startOfDay();
    $graceDeadline = $due->copy()->addDays(3);

    if ($paidOrNow->greaterThan($graceDeadline)) {
        return 10.00; // Late penalty after 3-day grace period
    }

    return 0.00;
}


public function getExistingBilling(Request $request)
{
    $consumerId = $request->consumer_id;
    $dueDate = Carbon::parse($request->due_date);
    
    $existingBilling = AccountantBilling::where('consumer_id', $consumerId)
        ->whereMonth('due_date', $dueDate->month)
        ->whereYear('due_date', $dueDate->year)
        ->first();
    
    if ($existingBilling) {
        return response()->json([
            'success' => true,
            'data' => $existingBilling,
            'type' => $existingBilling->status === 'paid' ? 'paid' : 'unpaid'
        ]);
    }
    
    return response()->json([
        'success' => false,
        'message' => 'No existing billing found for this month'
    ]);
}

public function getBillableConsumers(Request $request)
{
    $monthDate = $request->filled('month')
        ? Carbon::parse($request->month)
        : Carbon::now();

    $month = $monthDate->month;
    $year = $monthDate->year;

    $consumers = AdminConsumer::where('status', 'active')
        ->whereIn('id', function ($query) use ($month, $year) {
            $query->select('consumer_id')
                ->from('billings')
                ->whereMonth('reading_date', $month)
                ->whereYear('reading_date', $year);
        })
        ->whereNotIn('id', function ($query) use ($month, $year) {
            $query->select('consumer_id')
                ->from('accountant_billings')
                ->whereMonth('due_date', $month)
                ->whereYear('due_date', $year)
                ->whereNull('deleted_at');
        })
        ->select('id', 'first_name', 'middle_name', 'last_name', 'suffix', 'meter_no', 'consumer_type')
        ->orderBy('last_name')
        ->orderBy('first_name')
        ->get();

    return response()->json($consumers);
}
}

