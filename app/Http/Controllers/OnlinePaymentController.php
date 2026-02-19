<?php

namespace App\Http\Controllers;

use App\Models\OnlinePayment;
use App\Models\AccountantBilling;
use App\Models\AdminConsumer;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class OnlinePaymentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'bill_id' => 'required|exists:accountant_billings,id',
            'payment_method' => 'required|in:gcash,maya',
            'reference_number' => 'required|string|max:255',
            'proof_image' => 'required|image|mimes:jpeg,png,jpg|max:5120'
        ]);

        try {
            $bill = AccountantBilling::findOrFail($request->bill_id);
            
            // Check if bill is already paid
            if ($bill->status === 'paid') {
                return response()->json([
                    'success' => false,
                    'message' => 'This bill has already been paid.'
                ], 422);
            }

            // Check if there's already a pending payment for this bill
            $existingPendingPayment = OnlinePayment::where('bill_id', $request->bill_id)
                ->where('status', 'pending')
                ->first();
                
            if ($existingPendingPayment) {
                return response()->json([
                    'success' => false,
                    'message' => 'There is already a pending payment for this bill. Please wait for verification.'
                ], 422);
            }

            // Remove previous rejected attempts for this bill before accepting a new submission.
            $rejectedPayments = OnlinePayment::where('bill_id', $request->bill_id)
                ->where('status', 'rejected')
                ->get();

            foreach ($rejectedPayments as $rejectedPayment) {
                if (!empty($rejectedPayment->proof_image) && Storage::disk('public')->exists($rejectedPayment->proof_image)) {
                    Storage::disk('public')->delete($rejectedPayment->proof_image);
                }
            }

            if ($rejectedPayments->isNotEmpty()) {
                OnlinePayment::whereIn('id', $rejectedPayments->pluck('id'))->delete();
            }

            // Upload proof image
            $imagePath = $request->file('proof_image')->store('payment-proofs', 'public');

            // Create online payment record
            $payment = OnlinePayment::create([
                'bill_id' => $request->bill_id,
                // Use the bill's consumer_id (admin_consumers.id) to keep relations consistent.
                'consumer_id' => $bill->consumer_id,
                'payment_method' => $request->payment_method,
                'amount' => $bill->total_amount,
                'reference_number' => $request->reference_number,
                'proof_image' => $imagePath,
                'status' => 'pending'
            ]);

            // Notify consumer that payment is submitted and waiting for verification
            if (!empty($bill->consumer_id)) {
                Notification::create([
                    'consumer_id' => $bill->consumer_id,
                    'billing_id' => $bill->id,
                    'title' => 'Payment Submitted',
                    'message' => 'Your payment was submitted and is now waiting for verification.',
                    'type' => 'payment',
                    'is_read' => false,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Payment submitted successfully. Waiting for verification.',
                'payment' => $payment
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to process payment: ' . $e->getMessage()
            ], 500);
        }
    }

    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');
        $search = $request->get('search', '');
        
        $payments = OnlinePayment::with(['bill', 'adminConsumer', 'verifier'])
            ->when($status, function($query) use ($status) {
                return $query->where('status', $status);
            })
            ->when($search, function($query) use ($search) {
                return $query->where('reference_number', 'like', "%{$search}%")
                    ->orWhereHas('adminConsumer', function($q) use ($search) {
                        $q->where('first_name', 'like', "%{$search}%")
                          ->orWhere('last_name', 'like', "%{$search}%")
                          ->orWhere('meter_no', 'like', "%{$search}%");
                    });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'payments' => $payments
        ]);
    }

    public function verify(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:verified,rejected',
            'admin_notes' => 'nullable|string'
        ]);

        try {
            $payment = OnlinePayment::with('bill')->findOrFail($id);
            
            if ($payment->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment has already been processed.'
                ], 422);
            }

            // Update payment status
            $payment->update([
                'status' => $request->status,
                'admin_notes' => $request->admin_notes,
                'verified_at' => now(),
                'verified_by' => Auth::id()
            ]);

            // If payment is verified, update the billing status
            if ($request->status === 'verified') {
                $payment->bill->update([
                    'status' => 'paid',
                    'paid_at' => now()
                ]);
                
                $consumerId = $payment->bill->consumer_id ?? $payment->consumer_id;
                if ($consumerId) {
                    Notification::create([
                        'consumer_id' => $consumerId,
                        'billing_id' => $payment->bill_id,
                        'title' => 'Payment Approved',
                        'message' => 'Your payment has been approved and your bill is now marked as paid.',
                        'type' => 'payment',
                        'is_read' => false,
                    ]);
                }

                $message = 'Payment verified successfully.';
            } else {
                $consumerId = $payment->bill->consumer_id ?? $payment->consumer_id;
                if ($consumerId) {
                    $rejectionMessage = 'Your payment was rejected. Please upload a new valid proof of payment.';
                    if (!empty($request->admin_notes)) {
                        $rejectionMessage .= ' Admin note: ' . $request->admin_notes;
                    }

                    Notification::create([
                        'consumer_id' => $consumerId,
                        'billing_id' => $payment->bill_id,
                        'title' => 'Payment Rejected',
                        'message' => $rejectionMessage,
                        'type' => 'payment',
                        'is_read' => false,
                    ]);
                }

                $message = 'Payment rejected.';
            }

            return response()->json([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to process verification: ' . $e->getMessage()
            ], 500);
        }
    }

   public function show($id)
{
    $payment = OnlinePayment::with([
        'adminConsumer', 
        'bill.consumer',  // Load bill with its consumer
        'verifier'
    ])->findOrFail($id);
    
    return response()->json([
        'success' => true,
        'data' => $payment
    ]);
}

    public function datatable(Request $request)
    {
        $query = OnlinePayment::with(['bill.consumer', 'adminConsumer']);

        // Handle status filter
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Handle search
        if ($request->has('search') && !empty($request->search['value'])) {
            $searchTerm = $request->search['value'];
            $query->where(function($q) use ($searchTerm) {
                $q->where('reference_number', 'like', "%{$searchTerm}%")
                  ->orWhere('bill_id', 'like', "%{$searchTerm}%")
                  ->orWhere('payment_method', 'like', "%{$searchTerm}%")
                  ->orWhere('amount', 'like', "%{$searchTerm}%")
                  ->orWhereHas('adminConsumer', function($q2) use ($searchTerm) {
                      $q2->where('first_name', 'like', "%{$searchTerm}%")
                         ->orWhere('last_name', 'like', "%{$searchTerm}%")
                         ->orWhere('meter_no', 'like', "%{$searchTerm}%");
                  })
                  ->orWhereHas('bill.consumer', function($q3) use ($searchTerm) {
                      $q3->where('first_name', 'like', "%{$searchTerm}%")
                         ->orWhere('last_name', 'like', "%{$searchTerm}%")
                         ->orWhere('meter_no', 'like', "%{$searchTerm}%");
                  });
            });
        }

        // Get total records count
        $totalRecords = OnlinePayment::count();
        
        // Get filtered count (before pagination)
        $filteredQuery = clone $query;
        $filteredCount = $filteredQuery->count();

        // Handle ordering
        if ($request->has('order') && count($request->order) > 0) {
            $orderColumnIndex = $request->order[0]['column'];
            $orderDirection = $request->order[0]['dir'];
            
            // Map column index to column name (updated for new column structure)
            $columns = [
                0 => 'id', 
                1 => 'admin_consumers.first_name', // Handle consumer name ordering
                2 => 'admin_consumers.meter_no',   // Handle meter number ordering
                3 => 'amount', 
                4 => 'payment_method', 
                5 => 'reference_number', 
                6 => 'created_at', 
                7 => 'status',
                8 => 'actions'
            ];
            
            if (isset($columns[$orderColumnIndex])) {
                $orderColumn = $columns[$orderColumnIndex];
                
                // Handle relationship ordering for consumer data
                if (strpos($orderColumn, 'admin_consumers.') === 0) {
                    $relationColumn = str_replace('admin_consumers.', '', $orderColumn);
                    
                    $query->leftJoin('admin_consumers', 'online_payments.consumer_id', '=', 'admin_consumers.id')
                          ->orderBy("admin_consumers.{$relationColumn}", $orderDirection)
                          ->select('online_payments.*');
                } else {
                    $query->orderBy($orderColumn, $orderDirection);
                }
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // Handle pagination
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        
        $data = $query->skip($start)->take($length)->get();

        // Format the data for DataTables
        $formattedData = $data->map(function($payment) {
            // Try to get consumer data from multiple possible sources
            $consumerData = null;
            
            // First try adminConsumer relationship
            if ($payment->adminConsumer) {
                $consumerData = [
                    'first_name' => $payment->adminConsumer->first_name,
                    'last_name' => $payment->adminConsumer->last_name,
                    'meter_no' => $payment->adminConsumer->meter_no
                ];
            } 
            // Then try bill->consumer relationship
            elseif ($payment->bill && $payment->bill->consumer) {
                $consumerData = [
                    'first_name' => $payment->bill->consumer->first_name,
                    'last_name' => $payment->bill->consumer->last_name,
                    'meter_no' => $payment->bill->consumer->meter_no
                ];
            }
            // Fallback to empty data
            else {
                $consumerData = [
                    'first_name' => 'N/A',
                    'last_name' => 'N/A',
                    'meter_no' => 'N/A'
                ];
            }

            return [
                'id' => $payment->id,
                'admin_consumer' => $consumerData,
                'amount' => $payment->amount,
                'payment_method' => $payment->payment_method,
                'reference_number' => $payment->reference_number,
                'created_at' => $payment->created_at->toDateTimeString(),
                'status' => $payment->status,
                'proof_image' => $payment->proof_image,
                'admin_notes' => $payment->admin_notes,
            ];
        });

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredCount,
            'data' => $formattedData
        ]);
    }

    public function pendingNotifications(Request $request)
    {
        $limit = (int) $request->input('limit', 10);
        $limit = max(1, min($limit, 50));

        $pendingQuery = OnlinePayment::with(['adminConsumer', 'bill.consumer'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc');

        $pendingCount = (clone $pendingQuery)->count();
        $pendingPayments = $pendingQuery->limit($limit)->get();

        $notifications = $pendingPayments->map(function ($payment) {
            $consumerName = 'N/A';
            $meterNo = 'N/A';

            if ($payment->adminConsumer) {
                $consumerName = trim(($payment->adminConsumer->first_name ?? '') . ' ' . ($payment->adminConsumer->last_name ?? ''));
                $meterNo = $payment->adminConsumer->meter_no ?? 'N/A';
            } elseif ($payment->bill && $payment->bill->consumer) {
                $consumerName = trim(($payment->bill->consumer->first_name ?? '') . ' ' . ($payment->bill->consumer->last_name ?? ''));
                $meterNo = $payment->bill->consumer->meter_no ?? 'N/A';
            }

            return [
                'id' => $payment->id,
                'consumer_name' => $consumerName ?: 'N/A',
                'meter_no' => $meterNo,
                'amount' => $payment->amount,
                'payment_method' => $payment->payment_method,
                'reference_number' => $payment->reference_number,
                'created_at' => $payment->created_at ? $payment->created_at->toIso8601String() : null,
            ];
        });

        return response()->json([
            'success' => true,
            'pending_count' => $pendingCount,
            'notifications' => $notifications,
        ]);
    }


    // Remove duplicate method
    public function submitPayment(Request $request)
    {
        // This is a duplicate of store() method, so remove it or keep only one
        return $this->store($request);
    }

    public function getProofImage($id)
{
    $payment = OnlinePayment::findOrFail($id);
    
    if (!$payment->proof_image) {
        abort(404, 'Image not found');
    }
    
    $path = storage_path('app/public/' . $payment->proof_image);
    
    if (!file_exists($path)) {
        abort(404, 'Image file not found');
    }
    
    $file = File::get($path);
    $type = File::mimeType($path);
    
    return response($file, 200)->header('Content-Type', $type);
}
}
