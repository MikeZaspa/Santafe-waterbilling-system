<?php

namespace App\Http\Controllers;

use App\Models\AccountantBilling;
use App\Models\AdminConsumer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use PDF;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PaidBillsExport;

class ReportController extends Controller
{
    private const REPORT_STATUSES = ['paid', 'unpaid', 'overdue'];

    public function data(Request $request)
    {
        try {
            $query = AccountantBilling::with('consumer')
                ->whereIn('status', self::REPORT_STATUSES)
                ->select('accountant_billings.*');

            $status = strtolower((string) $request->input('status', ''));
            if ($status !== '' && in_array($status, self::REPORT_STATUSES, true)) {
                $query->where('status', $status);
            }

            if ($request->has('month') && $request->month != '') {
                $month = Carbon::parse($request->month);
                $query->whereMonth('due_date', $month->month)
                      ->whereYear('due_date', $month->year);
            }
            
            // Add name search functionality
            if ($request->has('name') && $request->name != '') {
                $searchTerm = $request->name;
                $query->whereHas('consumer', function($q) use ($searchTerm) {
                    $q->where('first_name', 'like', '%' . $searchTerm . '%')
                      ->orWhere('last_name', 'like', '%' . $searchTerm . '%')
                      ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ['%' . $searchTerm . '%']);
                });
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('consumer_name', function($row) {
                    return $row->consumer ? $row->consumer->first_name.' '.$row->consumer->last_name : 'N/A';
                })
                ->addColumn('meter_no', function($row) {
                    return $row->meter_no ?: ($row->consumer ? $row->consumer->meter_number : 'N/A');
                })
                ->addColumn('status', function($row) {
                    $status = strtolower((string) ($row->status ?? ''));
                    return in_array($status, self::REPORT_STATUSES, true) ? $status : 'unpaid';
                })
                ->make(true);

        } catch (\Exception $e) {
            \Log::error('Report data error: '.$e->getMessage());
            return response()->json([
                'error' => 'Failed to load report data: '.$e->getMessage()
            ], 500);
        }
    }

    public function export(Request $request)
    {
        $format = $request->format ?? 'excel';
        $month = $request->month ? Carbon::parse($request->month) : null;
        $status = strtolower((string) $request->input('status', ''));

        $query = AccountantBilling::with('consumer')
            ->whereIn('status', self::REPORT_STATUSES)
            ->orderBy('due_date', 'desc');

        if ($status !== '' && in_array($status, self::REPORT_STATUSES, true)) {
            $query->where('status', $status);
        }

        if ($month) {
            $query->whereMonth('due_date', $month->month)
                  ->whereYear('due_date', $month->year);
        }
        
        // Add name search functionality for export
        if ($request->has('name') && $request->name != '') {
            $searchTerm = $request->name;
            $query->whereHas('consumer', function($q) use ($searchTerm) {
                $q->where('first_name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('last_name', 'like', '%' . $searchTerm . '%')
                  ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ['%' . $searchTerm . '%']);
            });
        }

        $data = $query->get()->map(function($item) {
            return [
                'ID' => $item->id,
                'Consumer' => $item->consumer ? $item->consumer->first_name.' '.$item->consumer->last_name : 'N/A',
                'Meter No.' => $item->meter_no ?: ($item->consumer ? $item->consumer->meter_number : 'N/A'),
                'Due Date' => $item->due_date ? $item->due_date->format('M d, Y') : 'N/A',
                'Consumption (m³)' => number_format($item->consumption, 2),
                'Payment Method' => $item->formatted_payment_method['name'] ?? ucfirst($item->payment_method),
                'Total Amount' => '₱' . number_format($item->total_amount, 2),
                'Amount Paid' => '₱' . number_format($item->amount_paid, 2),
                'Status' => ucfirst(strtolower((string) ($item->status ?? 'unpaid')))
            ];
        });

        $statusSegment = $status !== '' ? $status : 'all_statuses';
        $filename = 'bills_report_' . $statusSegment . '_' . ($month ? $month->format('Y_m') : 'all_time');

        if ($format === 'pdf') {
            $pdf = PDF::loadView('exports.report', ['data' => $data]);
            return $pdf->download("$filename.pdf");
        } elseif ($format === 'csv') {
            return response()->streamDownload(function() use ($data) {
                $file = fopen('php://output', 'w');
                fputcsv($file, array_keys($data[0]));
                foreach ($data as $row) {
                    fputcsv($file, $row);
                }
                fclose($file);
            }, "$filename.csv");
        } else {
            return Excel::download(new PaidBillsExport($data), "$filename.xlsx");
        }
    }
}
