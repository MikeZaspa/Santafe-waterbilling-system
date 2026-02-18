<?php

namespace App\Http\Controllers;

use App\Models\WaterRate;
use Illuminate\Http\Request;

class WaterRateController extends Controller
{
    public function index()
    {
        $rates = WaterRate::orderBy('type')->orderBy('range')->get();
        return view('auth.water-rates', compact('rates'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:residential,commercial,institutional',
            'range' => 'required|string|max:20',
            'amount' => 'required|numeric|min:0'
        ]);

        WaterRate::create($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Water rate created successfully.'
            ]);
        }

        return redirect()->route('water-rates.index')
            ->with('success', 'Water rate created successfully.');
    }

    public function edit(WaterRate $waterRate)
    {
        $rates = WaterRate::orderBy('type')->orderBy('range')->get();
        return view('auth.water-rates', compact('waterRate', 'rates'));
    }

    public function update(Request $request, WaterRate $waterRate)
    {
        $validated = $request->validate([
            'type' => 'required|in:residential,commercial,institutional',
            'range' => 'required|string|max:20',
            'amount' => 'required|numeric|min:0'
        ]);
        

        $waterRate->update($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Water rate updated successfully.'
            ]);
        }

        return redirect()->route('water-rates.index')
            ->with('success', 'Water rate updated successfully.');
    }

    public function destroy(WaterRate $waterRate)
    {
        $waterRate->delete();

        return redirect()->route('water-rates.index')
            ->with('success', 'Water rate deleted successfully.');
    }

public function calculateAmount($type, $consumption)
{
    $rates = WaterRate::where('type', $type)
              ->orderBy('range')
              ->get();

    if ($rates->isEmpty()) {
        throw new \Exception("No water rates defined for {$type} type");
    }

    $consumption = max(0, (float) $consumption);
    $totalAmount = 0;

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
            $units_26_plus = $consumption - 25;
            $rate26PlusAmount = $rate_26_plus ? $rate_26_plus->amount : 17;
            $totalAmount += max(0, $units_26_plus) * $rate26PlusAmount;
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
}
public function calculateBill(Request $request)
{
    $request->validate([
        'type' => 'required|in:residential,commercial,institutional',
        'consumption' => 'required|numeric|min:0'
    ]);
    
    try {
        $amount = $this->calculateAmount($request->type, $request->consumption);
        
        return response()->json([
            'success' => true,
            'amount' => $amount  // Changed from data.amount to just amount
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 400);
    }
}
   
}
