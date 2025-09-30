<?php

namespace App\Http\Controllers;

use App\Models\Disconnection;
use App\Models\Consumer;
use App\Models\Billing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DisconnectionController extends Controller
{
    /**
     * Display disconnections page
     */
    public function index()
    {
        $disconnections = Disconnection::with(['consumer', 'billing'])
            ->orderBy('disconnection_date', 'desc')
            ->get();
            
        return view('auth.admin-plumber-disconnection', compact('disconnections'));
    }

    /**
     * Store a new disconnection
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'consumer_id' => 'required|exists:consumers,id',
            'billing_id' => 'required|exists:billings,id',
            'amount_due' => 'required|numeric|min:0',
            'reason' => 'required|string|max:255',
            'disconnection_date' => 'required|date',
            'notes' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            // Create disconnection record
            $disconnection = Disconnection::create($validated);

            // Update consumer status
            $consumer = Consumer::find($validated['consumer_id']);
            $consumer->update(['status' => 'disconnected']);

            // Update billing status
            $billing = Billing::find($validated['billing_id']);
            $billing->update(['status' => 'disconnected']);

            DB::commit();

            return response()->json([
                'message' => 'Consumer disconnected successfully',
                'disconnection' => $disconnection
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to disconnect consumer: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reconnect a consumer
     */
    public function reconnect(Request $request)
    {
        $validated = $request->validate([
            'consumer_id' => 'required|exists:consumers,id',
            'billing_id' => 'required|exists:billings,id'
        ]);

        try {
            DB::beginTransaction();

            // Update consumer status
            $consumer = Consumer::find($validated['consumer_id']);
            $consumer->update(['status' => 'active']);

            // Update billing status
            $billing = Billing::find($validated['billing_id']);
            $billing->update(['status' => 'active']);

            // Mark disconnection as resolved
            Disconnection::where('consumer_id', $validated['consumer_id'])
                ->where('status', 'active')
                ->update([
                    'status' => 'resolved',
                    'reconnection_date' => now()
                ]);

            DB::commit();

            return response()->json([
                'message' => 'Consumer reconnected successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to reconnect consumer: ' . $e->getMessage()
            ], 500);
        }
    }
}