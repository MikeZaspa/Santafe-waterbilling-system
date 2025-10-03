<?php

namespace App\Http\Controllers;

use App\Models\Disconnection;
use App\Models\Billing;
use App\Models\AdminConsumer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DisconnectionController extends Controller
{
    public function index()
    {
        $disconnections = Disconnection::with(['consumer', 'billing'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('auth.admin-plumber-disconnection', compact('disconnections'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'consumer_id' => 'required|exists:admin_consumers,id', // Changed to admin_consumers
            'billing_id' => 'required|exists:billings,id',
            'reason' => 'required|string|max:255',
            'disconnection_date' => 'required|date',
            'notes' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            // Create disconnection record
            $disconnection = Disconnection::create([
                'consumer_id' => $request->consumer_id,
                'billing_id' => $request->billing_id,
                'reason' => $request->reason,
                'notes' => $request->notes,
                'disconnection_date' => $request->disconnection_date,
                'status' => 'disconnected',
                'disconnected_by' => auth()->id() ?? 1
            ]);

            // Update billing record status
            $billing = Billing::find($request->billing_id);
            $billing->update([
                'disconnection_status' => 'disconnected'
            ]);

            // Update consumer status
            $consumer = AdminConsumer::find($request->consumer_id);
            $consumer->update([
                'status' => 'disconnected',
                'disconnection_date' => $request->disconnection_date
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Consumer disconnected successfully!',
                'disconnection' => $disconnection
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to disconnect consumer: ' . $e->getMessage()
            ], 500);
        }
    }

    public function reconnect(Request $request, Disconnection $disconnection)
{
    // Validate the request
    $request->validate([
        'reconnection_date' => 'required|date',
        'notes' => 'nullable|string|max:500'
    ]);

    try {
        DB::beginTransaction();

        // Update the disconnection record
        $disconnection->update([
            'reconnection_date' => $request->reconnection_date,
            'status' => 'reconnected',
            'notes' => $request->notes ?? $disconnection->notes,
            'reconnected_by' => auth()->id()
        ]);

        // Update the related billing record
        if ($disconnection->billing) {
            $disconnection->billing->update([
                'disconnection_status' => 'reconnected' // or 'active' depending on your needs
            ]);
        }

        // Update the consumer status back to active
        if ($disconnection->consumer) {
            $disconnection->consumer->update([
                'status' => 'active',
                'disconnection_date' => null // Clear disconnection date
            ]);
        }

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Consumer reconnected successfully',
            'data' => $disconnection
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        
        return response()->json([
            'success' => false,
            'message' => 'Failed to reconnect consumer: ' . $e->getMessage()
        ], 500);
    }
}


    public function getDisconnectionHistory($consumerId)
    {
        $disconnections = Disconnection::with('billing')
            ->where('consumer_id', $consumerId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'disconnections' => $disconnections
        ]);
    }
}