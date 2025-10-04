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

    $totalAmount = 0;
    $remainingConsumption = $consumption;

    if ($type === 'commercial') {
        foreach ($rates as $rate) {
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
            if ($remainingConsumption <= 0) break;
        }
    } elseif ($type === 'institutional') {
        // 0-5: free
        if ($consumption > 5) {
            $units_0_5 = 5;
        } else {
            $units_0_5 = $consumption;
        }
        $remaining = $consumption - $units_0_5;

        // 6-15: fixed price if consumption >= 6
        $fixedRate = $rates->where('range', '6-15')->first();
        if ($consumption >= 6 && $fixedRate) {
            $totalAmount += $fixedRate->amount;
        }

        // 16-25: per unit
        $rate_16_25 = $rates->where('range', '16-25')->first();
        if ($consumption > 15 && $rate_16_25) {
            $units_16_25 = min($consumption, 25) - 15;
            $totalAmount += $units_16_25 * $rate_16_25->amount;
        }

        // 26+: per unit
        $rate_26_plus = $rates->filter(function($rate) {
            return str_contains($rate->range, '+');
        })->first();
        if ($consumption > 25 && $rate_26_plus) {
            $units_26_plus = $consumption - 25;
            $totalAmount += $units_26_plus * $rate_26_plus->amount;
        }
    } else {
        // Residential (original logic)
        foreach ($rates as $rate) {
            if (str_contains($rate->range, '+')) {
                $rangeConsumption = $remainingConsumption;
                $rangeAmount = $rangeConsumption * $rate->amount;
            } else {
                $rangeParts = explode('-', $rate->range);
                $min = (int)$rangeParts[0];
                $max = (int)$rangeParts[1];
                $rangeConsumption = min($remainingConsumption, $max - $min + 1);
                $rangeAmount = $rangeConsumption * $rate->amount;
            }
            $totalAmount += $rangeAmount ?? 0;
            $remainingConsumption -= $rangeConsumption ?? 0;
            if ($remainingConsumption <= 0) break;
        }
    }

    // Add posos charge if residential
    if ($type === 'residential') {
        $pososCharge = floor($consumption / 11) * 2;
        $totalAmount += $pososCharge;
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