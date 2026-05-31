<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ShippingRate;

class ShippingController extends Controller
{
    public function calculate(Request $request)
    {
        $data = $request->validate([
            'carrier' => 'nullable|string',
            'weight' => 'required|numeric|min:0',
            'distance_km' => 'required|numeric|min:0',
        ]);

        $carrier = $data['carrier'] ?? null;
        $weight = floatval($data['weight']);
        $distance = floatval($data['distance_km']);

        $rate = null;
        if ($carrier) {
            $rate = ShippingRate::where('carrier', $carrier)->first();
        }

        if (!$rate) {
            // fallback rates
            $base = 30.00;
            $perKm = 0.50;
            $perKg = 10.00;
        } else {
            $base = floatval($rate->base_cost);
            $perKm = floatval($rate->cost_per_km);
            $perKg = floatval($rate->cost_per_kg);
        }

        $cost = $base + ($perKm * $distance) + ($perKg * $weight);

        return response()->json([
            'carrier' => $carrier ?? 'custom',
            'weight' => $weight,
            'distance_km' => $distance,
            'cost' => round($cost, 2),
        ]);
    }
}
