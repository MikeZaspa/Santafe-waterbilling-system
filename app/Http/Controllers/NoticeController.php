<?php

namespace App\Http\Controllers;

use App\Models\Notice;
use App\Models\AdminConsumer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NoticeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    
    public function index(Request $request)
    {
        $query = Notice::with('consumer');

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('notice', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('consumer', function($q) use ($search) {
                      $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('meter_no', 'like', "%{$search}%");
                  });
            });
        }

        // Status filter
        if ($request->has('status') && !empty($request->status)) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Date filter
        if ($request->has('date') && !empty($request->date)) {
            $query->whereDate('created_at', $request->date);
        }

        $notices = $query->latest()->paginate(10);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $notices->items(),
                'pagination' => [
                    'current_page' => $notices->currentPage(),
                    'last_page' => $notices->lastPage(),
                    'per_page' => $notices->perPage(),
                    'total' => $notices->total(),
                ]
            ]);
        }

        return view('admin-accountant-notice', compact('notices'));
    }

    /**
     * Get consumers for dropdown
     */
    public function getConsumers()
    {
        try {
            $consumers = AdminConsumer::select('id', 'first_name', 'middle_name', 'last_name', 'suffix', 'meter_no')
                ->where('status', 'active')
                ->orderBy('last_name')
                ->orderBy('first_name')
                ->get();

            return response()->json($consumers);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to load consumers: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'consumer_id' => 'required|exists:admin_consumers,id',
            'notice' => 'required|string|max:1000',
            'description' => 'nullable|string|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $notice = Notice::create([
                'consumer_id' => $request->consumer_id,
                'notice' => $request->notice,
                'description' => $request->description,
                'is_active' => true
            ]);

            $notice->load('consumer');

            return response()->json([
                'success' => true,
                'message' => 'Notice created successfully',
                'data' => $notice
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create notice: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Notice $notice)
    {
        $notice->load('consumer');
        return response()->json([
            'success' => true,
            'data' => $notice
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Notice $notice)
    {
        $notice->load('consumer');
        return response()->json([
            'success' => true,
            'data' => $notice
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Notice $notice)
    {
        $validator = Validator::make($request->all(), [
            'consumer_id' => 'required|exists:admin_consumers,id',
            'notice' => 'required|string|max:1000',
            'description' => 'nullable|string|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $notice->update([
                'consumer_id' => $request->consumer_id,
                'notice' => $request->notice,
                'description' => $request->description,
            ]);

            $notice->load('consumer');

            return response()->json([
                'success' => true,
                'message' => 'Notice updated successfully',
                'data' => $notice
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update notice: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Notice $notice)
    {
        try {
            $notice->delete();

            return response()->json([
                'success' => true,
                'message' => 'Notice deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete notice: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle notice status
     */
    public function toggleStatus(Notice $notice)
    {
        try {
            $notice->update([
                'is_active' => !$notice->is_active
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Notice status updated successfully',
                'data' => [
                    'is_active' => $notice->is_active
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update notice status: ' . $e->getMessage()
            ], 500);
        }
    }
}
