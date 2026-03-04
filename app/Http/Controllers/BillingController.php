<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use App\Models\AdminConsumer;
use App\Models\Disconnection;
use App\Models\CutConsumer;
use App\Models\AccountantBilling;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth; // Add this line
use Illuminate\Support\Facades\DB;

class BillingController extends Controller
{
   public function index()
  {
     
    if(request()->ajax()) {
        $billings = Billing::with('consumer')
            ->orderBy('reading_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->get()
            ->unique('consumer_id')
            ->values();
            
        return response()->json(['data' => $billings]);
    }
    
     
    return view('auth.admin-plumber-consumer');
}
   public function create()
    {
        $latestReadings = Billing::select('consumer_id', DB::raw('MAX(reading_date) as last_reading_date'))
            ->groupBy('consumer_id');

        $consumers = AdminConsumer::query()
            ->leftJoinSub($latestReadings, 'latest_billings', function ($join) {
                $join->on('admin_consumers.id', '=', 'latest_billings.consumer_id');
            })
            ->where('admin_consumers.status', 'active')
            ->where(function ($query) {
                $query->whereNull('latest_billings.last_reading_date')
                    ->orWhereRaw('DATE_ADD(latest_billings.last_reading_date, INTERVAL 1 MONTH) <= CURDATE()');
            })
            ->select([
                'admin_consumers.id',
                'admin_consumers.first_name',
                'admin_consumers.middle_name',
                'admin_consumers.last_name',
                'admin_consumers.suffix',
                'admin_consumers.meter_no',
                'admin_consumers.consumer_type',
            ])
            ->orderBy('admin_consumers.last_name')
            ->orderBy('admin_consumers.first_name')
            ->get();
            
        return response()->json($consumers);
    }

    public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'consumer_id' => 'required|exists:admin_consumers,id',
        'meter_no' => 'required|string|max:50',
        'previous_reading' => 'required|numeric|min:0',
        'current_reading' => 'required|numeric|min:0|gt:previous_reading',
        'reading_date' => 'required|date|before_or_equal:today',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'errors' => $validator->errors(),
            'message' => 'Validation failed'
        ], 422);
    }

    $validated = $validator->validated();
    $consumer = AdminConsumer::findOrFail($validated['consumer_id']);

    $readingDate = \Carbon\Carbon::parse($validated['reading_date']);
    $lastReading = Billing::where('consumer_id', $validated['consumer_id'])
        ->orderBy('reading_date', 'desc')
        ->orderBy('created_at', 'desc')
        ->orderBy('id', 'desc')
        ->first();

    if ($lastReading) {
        $nextAllowedReadingDate = $lastReading->reading_date->copy()->addMonthNoOverflow();

        if ($readingDate->lt($nextAllowedReadingDate)) {
            return response()->json([
                'message' => 'Next reading for this consumer is on ' . $nextAllowedReadingDate->format('Y-m-d') . '.'
            ], 422);
        }
    }

    $alreadyReadThisMonth = Billing::where('consumer_id', $validated['consumer_id'])
        ->whereMonth('reading_date', $readingDate->month)
        ->whereYear('reading_date', $readingDate->year)
        ->exists();

    if ($alreadyReadThisMonth) {
        return response()->json([
            'message' => 'This consumer already has a reading for ' . $readingDate->format('F Y') . '.'
        ], 422);
    }

    try {
        $billing = Billing::create([
            'consumer_id' => $validated['consumer_id'],
            'consumer_type' => $consumer->consumer_type,
            'meter_no' => $validated['meter_no'],
            'previous_reading' => $validated['previous_reading'],
            'current_reading' => $validated['current_reading'],
            'consumption' => $validated['current_reading'] - $validated['previous_reading'],
            'reading_date' => $validated['reading_date'],
        ]);

        return response()->json([
            'message' => 'Billing record created successfully.',
            'billing' => $billing->load('consumer')
        ], 201);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Error creating billing record',
            'error' => $e->getMessage()
        ], 500);
    }
}

    public function show(Billing $billing)
    {
        return response()->json($billing->load('consumer'));
    }

   public function edit(Billing $billing)
{
    $consumers = AdminConsumer::where('status', 'active')
        ->select(['id', 'first_name', 'middle_name', 'last_name', 'suffix', 'meter_no', 'consumer_type'])
        ->orderBy('last_name')
        ->orderBy('first_name')
        ->get();

    return response()->json([
        'billing' => $billing->load('consumer'),
        'consumers' => $consumers,
        'consumer' => $billing->consumer // Add this line
    ]);
}

    public function update(Request $request, Billing $billing)
    {
        $validator = Validator::make($request->all(), [
            'consumer_id' => 'required|exists:admin_consumers,id',
            'meter_no' => 'required|string|max:50',
            'previous_reading' => 'required|numeric|min:0',
            'current_reading' => 'required|numeric|min:0|gt:previous_reading',
            'reading_date' => 'required|date|before_or_equal:today',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'message' => 'Validation failed'
            ], 422);
        }

        $validated = $validator->validated();
        $consumer = AdminConsumer::findOrFail($validated['consumer_id']);

        try {
            $billing->update([
                'consumer_id' => $validated['consumer_id'],
                'meter_no' => $validated['meter_no'],
                'previous_reading' => $validated['previous_reading'],
                'current_reading' => $validated['current_reading'],
                'consumption' => $validated['current_reading'] - $validated['previous_reading'],
                'reading_date' => $validated['reading_date'],
            ]);

            return response()->json([
                'message' => 'Billing record updated successfully.',
                'billing' => $billing->fresh()->load('consumer')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating billing record',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Billing $billing)
    {
        try {
            $billing->delete();
            return response()->json(['message' => 'Billing record deleted successfully.']);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error deleting billing record',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getLastReading(Request $request, $consumerId)
{
    $query = Billing::where('consumer_id', $consumerId);

    if ($request->filled('month')) {
        $monthDate = \Carbon\Carbon::parse($request->month);
        $query->whereMonth('reading_date', $monthDate->month)
            ->whereYear('reading_date', $monthDate->year);
    }

    $lastReading = $query
        ->orderBy('reading_date', 'desc')
        ->orderBy('created_at', 'desc')
        ->first();

    return response()->json([
        'last_reading' => $lastReading ? [
            'previous_reading' => $lastReading->previous_reading,
            'current_reading' => $lastReading->current_reading,
            'reading_date' => $lastReading->reading_date->format('Y-m-d')
        ] : null
    ]);
}

// In the disconnect method of BillingController
public function disconnect(Request $request)
{
    $validator = Validator::make($request->all(), [
        'consumer_id' => 'required|exists:admin_consumers,id',
        'billing_id' => 'required|exists:billings,id',
        'reason' => 'required|string|max:255',
        'disconnection_date' => 'required|date',
        'notes' => 'nullable|string',
        'reconnection_fee' => 'nullable|numeric|min:0'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'errors' => $validator->errors(),
            'message' => 'Validation failed'
        ], 422);
    }

    DB::beginTransaction();
    try {
        $consumer = AdminConsumer::findOrFail($request->consumer_id);
        $billing = Billing::findOrFail($request->billing_id);

        // Create disconnection record with billing data
        $disconnection = Disconnection::create([
            'consumer_id' => $consumer->id,
            'billing_id' => $billing->id,
            'name' => $this->formatConsumerName($consumer),
            'consumer_type' => $billing->consumer_type,
            'meter_no' => $billing->meter_no,
            'previous_reading' => $billing->previous_reading,
            'current_reading' => $billing->current_reading,
            'consumption' => $billing->consumption,
            'reading_date' => $billing->reading_date,
            'reason' => $request->reason,
            'disconnection_date' => $request->disconnection_date,
            'notes' => $request->notes,
            'disconnected_by' => auth()->id() ?? 1,
            'status' => 'disconnected',
            'reconnection_fee' => $request->reconnection_fee ?? 500.00
        ]);

        // Delete the billing record from the main table
        $billing->delete();

        // Update consumer status to 'disconnected'
        $consumer->update([
            'status' => 'disconnected'
        ]);

        Notification::create([
            'consumer_id' => $consumer->id,
            'billing_id' => null,
            'title' => 'Water Service Disconnected',
            'message' => 'Your water service was disconnected on ' . now()->format('M d, Y') . '. Reason: ' . $request->reason . '.',
            'type' => 'system',
            'is_read' => false,
        ]);

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Consumer successfully disconnected and moved to disconnection list.'
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Disconnection error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Failed to disconnect consumer: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Get all disconnected consumers
     *//**
 * Get all disconnected consumers
 */
public function getDisconnectedConsumers()
{
    try {
        // Only get consumers that are still disconnected (not reconnected)
        $disconnectedConsumers = Disconnection::where('status', 'disconnected')
            ->orderBy('disconnection_date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $disconnectedConsumers
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to load disconnected consumers: ' . $e->getMessage()
        ], 500);
    }
}

 
public function restoreDisconnectedConsumer(Request $request, $id)
{
    DB::beginTransaction();
    try {
        $disconnection = Disconnection::findOrFail($id);
        $consumer = AdminConsumer::find($disconnection->consumer_id);

        if (!$consumer) {
            return response()->json([
                'success' => false,
                'message' => 'Original consumer record not found'
            ], 404);
        }

        // Check if already restored
        if ($consumer->status === 'active') {
            return response()->json([
                'success' => false,
                'message' => 'This consumer is already active'
            ], 400);
        }

        // Restore consumer status to active
        $consumer->update([
            'status' => 'active'
        ]);

        // Create a new billing record with the disconnection data
        $billing = Billing::create([
            'consumer_id' => $disconnection->consumer_id,
            'consumer_type' => $disconnection->consumer_type,
            'meter_no' => $disconnection->meter_no,
            'previous_reading' => $disconnection->previous_reading,
            'current_reading' => $disconnection->current_reading,
            'consumption' => $disconnection->consumption,
            'reading_date' => $disconnection->reading_date,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Update the disconnection record with reconnection info
        $reconnectionNotes = $request->notes ? "Reconnected: " . $request->notes : "Reconnected on " . now()->format('Y-m-d H:i:s');
        
        $disconnection->update([
            'status' => 'reconnected',
            'reconnection_date' => now()->format('Y-m-d H:i:s'),
            'reconnection_notes' => $reconnectionNotes,
            'reconnection_fee' => 500.00, // Set the reconnection fee
            'reconnected_by' => auth()->id() ?? 1
        ]);

        Notification::create([
            'consumer_id' => $consumer->id,
            'billing_id' => null,
            'title' => 'Water Service Reconnected',
            'message' => 'Your water service was reconnected on ' . now()->format('M d, Y') . '. Reconnection fee: ₱500.00.',
            'type' => 'system',
            'is_read' => false,
        ]);

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Consumer successfully restored to active records and billing information has been recovered.',
            'billing' => $billing,
            'reconnected_at' => now()->format('Y-m-d H:i:s')
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Restore consumer error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Failed to restore consumer: ' . $e->getMessage()
        ], 500);
    }
}
    // Add this method to get consumers for dropdown
    public function getConsumers()
    {
        $consumers = AdminConsumer::where('status', 'active')
            ->orderBy('first_name')
            ->get();
            
        return response()->json($consumers);
    }

     public function getConsumerInfo(Billing $billing)
    {
        try {
            $consumer = $billing->consumer;
            
            if (!$consumer) {
                return response()->json([
                    'error' => 'Consumer not found'
                ], 404);
            }

            return response()->json([
                'consumer' => $consumer,
                'billing' => $billing
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to load consumer information: ' . $e->getMessage()
            ], 500);
        }
    }
     public function getBillingsData(Request $request)
    {
        AccountantBilling::applyAutomaticOverduePenalties();

        $billings = AccountantBilling::active()
            ->with('consumer')
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->when($request->filled('payment_method'), function ($query) use ($request) {
                $query->where('payment_method', $request->payment_method);
            })
            ->when($request->filled('month'), function ($query) use ($request) {
                $monthDate = \Carbon\Carbon::parse($request->month);
                $query->whereMonth('due_date', $monthDate->month)
                    ->whereYear('due_date', $monthDate->year);
            })
            ->when(!$request->filled('month'), function ($query) {
                $query->whereNotExists(function ($subQuery) {
                    $subQuery->select(DB::raw(1))
                        ->from('accountant_billings as newer')
                        ->whereColumn('newer.consumer_id', 'accountant_billings.consumer_id')
                        ->whereNull('newer.deleted_at')
                        ->where('newer.is_archived', false)
                        ->where(function ($compareQuery) {
                            $compareQuery->whereColumn('newer.due_date', '>', 'accountant_billings.due_date')
                                ->orWhere(function ($sameDateQuery) {
                                    $sameDateQuery->whereColumn('newer.due_date', '=', 'accountant_billings.due_date')
                                        ->whereColumn('newer.id', '>', 'accountant_billings.id');
                                });
                        });
                });
            })
            ->orderBy('due_date', 'desc')
            ->orderBy('created_at', 'desc');

        return datatables()->of($billings)
            ->addIndexColumn()
            ->addColumn('actions', function($billing) {
                return ''; // Actions will be rendered in the view
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function getArchivedBillingsData(Request $request)
    {
        $billings = AccountantBilling::archived()
            ->with(['consumer'])
            ->orderBy('archived_at', 'desc');

        return datatables()->of($billings)
            ->addIndexColumn()
            ->addColumn('actions', function($billing) {
                return ''; // Actions will be rendered in the view
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function archive(Request $request, $id)
    {
        try {
            $billing = AccountantBilling::findOrFail($id);

            // Check if already archived
            if ($billing->is_archived) {
                return response()->json([
                    'success' => false,
                    'message' => 'This billing record is already archived.'
                ], 400);
            }

            // Use auth() helper instead of Auth facade for better reliability
            $billing->update([
                'is_archived' => true,
                'archived_at' => now(),
                'archived_by' => auth()->id(), // Using auth() helper
                'archive_reason' => $request->reason,
                'archive_notes' => $request->notes
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Billing record archived successfully.'
            ]);

        } catch (\Exception $e) {
            \Log::error('Archive error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to archive billing record: ' . $e->getMessage()
            ], 500);
        }
    }

    public function restore($id)
    {
        try {
            $billing = AccountantBilling::findOrFail($id);

            // Check if actually archived
            if (!$billing->is_archived) {
                return response()->json([
                    'success' => false,
                    'message' => 'This billing record is not archived.'
                ], 400);
            }

            $billing->update([
                'is_archived' => false,
                'archived_at' => null,
                'archived_by' => null,
                'archive_reason' => null,
                'archive_notes' => null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Billing record restored successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to restore billing record: ' . $e->getMessage()
            ], 500);
        }
    }

    public function forceDelete($id)
    {
        try {
            $billing = AccountantBilling::findOrFail($id);

            // Only allow deletion of archived records
            if (!$billing->is_archived) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only archived records can be permanently deleted.'
                ], 400);
            }

            $billing->forceDelete(); // Use forceDelete for permanent deletion

            return response()->json([
                'success' => true,
                'message' => 'Archived billing record permanently deleted.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete archived record: ' . $e->getMessage()
            ], 500);
        }
    }

    public function emptyArchive()
    {
        try {
            $archivedCount = AccountantBilling::archived()->count();

            if ($archivedCount === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Archive is already empty.'
                ], 400);
            }

            AccountantBilling::archived()->forceDelete(); // Use forceDelete

            return response()->json([
                'success' => true,
                'message' => "Successfully emptied archive. {$archivedCount} records deleted."
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to empty archive: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getArchiveDetails($id)
    {
        try {
            $billing = AccountantBilling::with(['consumer'])
                ->archived()
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $billing
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load archive details.'
            ], 404);
        }
    }

    // Payment and receipt methods
    public function getBillingDetails($id)
    {
        try {
            AccountantBilling::applyAutomaticOverduePenalties();

            $billing = AccountantBilling::with('consumer')->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $billing
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load billing details.'
            ], 404);
        }
    }

    public function getReceipt($id)
    {
        try {
            AccountantBilling::applyAutomaticOverduePenalties();

            $billing = AccountantBilling::with('consumer')->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $billing
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate receipt.'
            ], 404);
        }
    }

     public function cutConsumer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'consumer_id' => 'required|exists:admin_consumers,id',
            'billing_id' => 'required|exists:billings,id',
            'reason' => 'required|string|max:255',
            'cut_date' => 'required|date',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'message' => 'Validation failed'
            ], 422);
        }

        DB::beginTransaction();
        try {
            $consumer = AdminConsumer::findOrFail($request->consumer_id);
            $billing = Billing::findOrFail($request->billing_id);

            // Store billing data before deletion
            $billingData = [
                'consumer_id' => $billing->consumer_id,
                'consumer_type' => $billing->consumer_type,
                'meter_no' => $billing->meter_no,
                'previous_reading' => $billing->previous_reading,
                'current_reading' => $billing->current_reading,
                'consumption' => $billing->consumption,
                'reading_date' => $billing->reading_date,
            ];

            // Create cut consumer record with billing data
            $cutConsumer = CutConsumer::create([
                'consumer_id' => $consumer->id,
                'billing_id' => $billing->id,
                'name' => $this->formatConsumerName($consumer),
                'consumer_type' => $consumer->consumer_type,
                'meter_no' => $consumer->meter_no,
                'reason' => $request->reason,
                'cut_date' => $request->cut_date,
                'notes' => $request->notes,
                'cut_by' => auth()->id() ?? 1,
                'billing_data' => json_encode($billingData) // Store billing data for restoration
            ]);

            // Delete the billing record
            $billing->delete();

            // Update consumer status to 'cut'
            $consumer->update([
                'status' => 'cut'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Consumer successfully cut and moved to cut consumers list.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Cut consumer error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to cut consumer: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all cut consumers
     */
    public function getCutConsumers()
    {
        try {
            $cutConsumers = CutConsumer::orderBy('cut_date', 'desc')->get();

            return response()->json([
                'success' => true,
                'data' => $cutConsumers
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load cut consumers: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Restore a cut consumer
     */
    public function restoreConsumer($id)
    {
        DB::beginTransaction();
        try {
            $cutConsumer = CutConsumer::findOrFail($id);
            $consumer = AdminConsumer::find($cutConsumer->consumer_id);

            if (!$consumer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Original consumer record not found'
                ], 404);
            }

            // Restore consumer status to active
            $consumer->update([
                'status' => 'active'
            ]);

            // Restore the billing record if billing data exists
            if ($cutConsumer->billing_data) {
                $billingData = json_decode($cutConsumer->billing_data, true);
                
                Billing::create([
                    'consumer_id' => $billingData['consumer_id'],
                    'consumer_type' => $billingData['consumer_type'],
                    'meter_no' => $billingData['meter_no'],
                    'previous_reading' => $billingData['previous_reading'],
                    'current_reading' => $billingData['current_reading'],
                    'consumption' => $billingData['consumption'],
                    'reading_date' => $billingData['reading_date'],
                ]);
            }

            // Delete the cut consumer record
            $cutConsumer->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Consumer successfully restored to active records.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Restore consumer error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to restore consumer: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper method to format consumer name
     */
    private function formatConsumerName($consumer)
    {
        $name = $consumer->first_name;
        if ($consumer->middle_name) $name .= ' ' . $consumer->middle_name;
        $name .= ' ' . $consumer->last_name;
        if ($consumer->suffix) $name .= ' ' . $consumer->suffix;
        return trim($name);
    }
}
