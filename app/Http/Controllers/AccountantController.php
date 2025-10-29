<?php

namespace App\Http\Controllers;

use App\Models\AccountantBilling;
use App\Models\AdminConsumer;
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
    public function getBillings(Request $request)
{
    $query = AccountantBilling::with(['consumer' => function($query) {
        $query->select('id', 'first_name', 'last_name');
    }])->orderBy('created_at', 'desc');

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
            return $billing->status; // Make sure this returns the status string
        })
        ->addColumn('actions', function($billing) {
            return $billing->id; // This will be handled by frontend JavaScript
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
        'current_reading' => 'required|numeric|min:0',
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

        // Get previous reading
        $previousReading = AccountantBilling::where('consumer_id', $consumer->id)
            ->latest()
            ->value('current_reading') ?? 0;

        // Validate that we have a valid previous reading
        if ($previousReading === null || $previousReading === '') {
            return response()->json([
                'success' => false,
                'errors' => [
                    'reading' => ['Previous reading data is not available. Please check the consumer\'s billing history.']
                ]
            ], 422);
        }

        // Validate current reading
        if ($request->current_reading < $previousReading) {
            return response()->json([
                'success' => false,
                'errors' => [
                    'reading' => ['Current reading cannot be less than the previous reading (' . $previousReading . ').']
                ]
            ], 422);
        }

        // Calculate consumption & total
        $consumption = $request->current_reading - $previousReading;
        $totalAmount = $this->calculateWaterBill($consumer->consumer_type, $consumption);
        
        // Calculate penalty if status is overdue
        $penaltyAmount = 0.00;
        if ($request->status === 'overdue') {
            $penaltyAmount = $this->calculatePenalty($request->due_date);
        }
        
        // Validate that not all readings are zero and total amount is not zero
        if ($previousReading == 0 && $request->current_reading == 0 && $consumption == 0 && $totalAmount == 0) {
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
            'current_reading' => $request->current_reading,
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

        $billing->delete();

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
    public function getLastReading($consumerId)
    {
        $lastReading = AccountantBilling::where('consumer_id', $consumerId)
            ->latest()
            ->first(['previous_reading', 'current_reading']);

        return response()->json([
            'success' => true,
            'data' => $lastReading ?: [
                'previous_reading' => 0,
                'current_reading' => 0
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

        $totalAmount = 0;
        $remainingConsumption = max(0, $consumption); // Ensure non-negative

        try {
            if ($type === 'commercial') {
                foreach ($rates as $rate) {
                    if ($remainingConsumption <= 0) break;
                    
                    if ($rate->range === '0-10') {
                        if ($remainingConsumption > 0) {
                            $totalAmount += $rate->amount;
                            $rangeConsumption = min($remainingConsumption, 10);
                            $remainingConsumption -= $rangeConsumption;
                        }
                    } elseif ($rate->range === '11-20') {
                        $rangeConsumption = min($remainingConsumption, 10);
                        $totalAmount += $rangeConsumption * $rate->amount;
                        $remainingConsumption -= $rangeConsumption;
                    } elseif ($rate->range === '21-30') {
                        $rangeConsumption = min($remainingConsumption, 10);
                        $totalAmount += $rangeConsumption * $rate->amount;
                        $remainingConsumption -= $rangeConsumption;
                    } elseif (str_contains($rate->range, '+')) {
                        $totalAmount += $remainingConsumption * $rate->amount;
                        $remainingConsumption = 0;
                    }
                }
            } elseif ($type === 'institutional') {
                // Check if required rate ranges exist
                $fixedRate = $rates->where('range', '6-15')->first();
                $rate_16_25 = $rates->where('range', '16-25')->first();
                $rate_26_plus = $rates->filter(function($rate) {
                    return str_contains($rate->range, '+');
                })->first();

                // 0-5: free
                $units_0_5 = min($consumption, 5);
                $remaining = max(0, $consumption - $units_0_5);

                // 6-15: fixed price if consumption >= 6
                if ($consumption >= 6 && $fixedRate) {
                    $totalAmount += $fixedRate->amount;
                }

                // 16-25: per unit
                if ($consumption > 15 && $rate_16_25) {
                    $units_16_25 = min($consumption, 25) - 15;
                    $totalAmount += max(0, $units_16_25) * $rate_16_25->amount;
                }

                // 26+: per unit
                if ($consumption > 25 && $rate_26_plus) {
                    $units_26_plus = max(0, $consumption - 25);
                    $totalAmount += $units_26_plus * $rate_26_plus->amount;
                }
            } else {
                // RESIDENTIAL - FIXED PROGRESSIVE CALCULATION
                $previousMax = 0;
                
                foreach ($rates as $rate) {
                    if ($remainingConsumption <= 0) break;

                    if (str_contains($rate->range, '+')) {
                        // Open-ended range - charge remaining consumption
                        $rangeConsumption = $remainingConsumption;
                        $rangeAmount = $rangeConsumption * $rate->amount;
                    } else {
                        // Calculate tier range
                        $rangeParts = explode('-', $rate->range);
                        if (count($rangeParts) !== 2) {
                            continue; // Skip invalid ranges
                        }
                        
                        $min = (int)$rangeParts[0];
                        $max = (int)$rangeParts[1];
                        
                        // Calculate consumption in this tier
                        $tierMin = $previousMax + 1;
                        $tierMax = $max;
                        $rangeConsumption = max(0, min($consumption, $tierMax) - $tierMin + 1);
                        
                        $rangeAmount = $rangeConsumption * $rate->amount;
                        $previousMax = $max;
                    }
                    
                    $totalAmount += $rangeAmount;
                    $remainingConsumption -= $rangeConsumption;
                }

                // Add posos charge for residential (with safety check)
               // if ($consumption > 0) {
                    //$pososCharge = floor($consumption / 11) * 2;
                //    $totalAmount += $pososCharge;
              //  }
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
    $due = Carbon::parse($dueDate);
    $now = $paymentDate ? Carbon::parse($paymentDate) : Carbon::now();
    
    if ($now->greaterThan($due)) {
        return 10.00; // ₱10 penalty
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
}