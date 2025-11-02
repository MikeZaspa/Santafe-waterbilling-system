<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AdminConsumer;
use App\Models\ConsumerAccount;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AccountManagementController extends Controller
{
    // Get list of accounts for DataTable (excluding soft deleted)
    public function index()
    {
        $accounts = ConsumerAccount::with('consumer')
            ->select('consumer_accounts.*')
            ->latest();
            
        return datatables()->eloquent($accounts)->toJson();
    }

    // Get consumers without accounts (excluding soft deleted accounts)
    public function data()
    {
        try {
            $consumers = AdminConsumer::select('id', 'first_name', 'middle_name', 'last_name', 'suffix', 'meter_no')
                ->whereDoesntHave('account', function($query) {
                    $query->whereNull('deleted_at'); // Only consider non-deleted accounts
                })
                ->orderBy('last_name')
                ->orderBy('first_name')
                ->get();
                
            return $consumers;
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch consumers',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Store new account
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'consumer_id' => 'required|exists:admin_consumers,id|unique:consumer_accounts,consumer_id,NULL,id,deleted_at,NULL',
                'username' => 'required|string|unique:consumer_accounts,username,NULL,id,deleted_at,NULL',
                'email' => 'nullable|email|unique:consumer_accounts,email,NULL,id,deleted_at,NULL',
                'password' => 'required|string|min:8'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $account = ConsumerAccount::create([
                'consumer_id' => $request->consumer_id,
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'created_by' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Account created successfully',
                'data' => $account
            ]);

        } catch (\Exception $e) {
            \Log::error('Account creation failed: '.$e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create account',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        try {
            $account = ConsumerAccount::with('consumer')->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $account
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Account not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'nullable|email|unique:consumer_accounts,email,'.$id.',id,deleted_at,NULL',
            'password' => 'sometimes|string|min:8'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $account = ConsumerAccount::findOrFail($id);

            $updateData = [
                'updated_by' => Auth::id()
            ];

            if ($request->filled('email')) {
                $updateData['email'] = $request->email;
            }

            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            $account->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Account updated successfully',
                'data' => $account
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update account',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Force delete (permanently remove from database)
    public function destroy($id)
    {
        try {
            \Log::info("Attempting to delete account ID: $id");
            
            $account = ConsumerAccount::findOrFail($id);
            \Log::info("Account found:", $account->toArray());
            
            // Use forceDelete for permanent deletion
            $account->forceDelete();
            \Log::info("Account permanently deleted successfully");

            return response()->json([
                'success' => true,
                'message' => 'Account deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            \Log::error("Failed to delete account ID: $id - " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete account',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Optional: If you want to support soft deletes instead, use this method:
    public function softDestroy($id)
    {
        try {
            \Log::info("Attempting to soft delete account ID: $id");
            
            $account = ConsumerAccount::findOrFail($id);
            \Log::info("Account found:", $account->toArray());
            
            // Soft delete (sets deleted_at timestamp)
            $account->delete();
            \Log::info("Account soft deleted successfully");

            return response()->json([
                'success' => true,
                'message' => 'Account deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            \Log::error("Failed to soft delete account ID: $id - " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete account',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}