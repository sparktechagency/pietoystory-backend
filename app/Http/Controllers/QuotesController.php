<?php

namespace App\Http\Controllers;

use App\Models\Quotes;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class QuotesController extends Controller
{
    public function quote(Request $request)
    {
        // ----------------------------
        // Validation
        // ----------------------------
        $validator = Validator::make($request->all(), [
            'zip_code' => 'nullable|digits:5',
            'how_often' => 'nullable|numeric|in:0,1,2,3',
            'how_many_dogs' => 'nullable|numeric|in:0,1,2,3',
            'total_area_size' => 'nullable|numeric|in:1,2,3,4',
            'area_to_clean' => 'nullable|string|max:255',
        ], [
            'zip_code.digits' => 'Zip code must be exactly 5 digits.',
            'how_often.in' => 'Invalid: Allowed 0 to 3.',
            'how_many_dogs.in' => 'Dogs allowed: 0 to 3.',
            'total_area_size.in' => 'Invalid area value: allowed 1 to 4.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()
            ], 422);
        }

        // Inputs
        $userId = $request->user_id;
        $zip_code = $request->zip_code;
        $how_often = $request->how_often + 1 ?? 1;
        $dogs = $request->how_many_dogs + 1 ?? 1;
        $total_area = $request->total_area_size ?? 1;
        $area_to_clean = $request->area_to_clean;

        // ----------------------------
        // Discount & Charge Logic
        // ----------------------------
        $lastQuote = Quotes::where('user_id', $userId)->latest()->first();

        $discount = $lastQuote ? false : true;
        $charge = $lastQuote ? false : true;

        // ----------------------------
        // Cost Table (Easy to Maintain)
        // ----------------------------
        $pricing = [
            1 => [ // area 1
                1 => [1 => 11.55, 2 => 12.12, 3 => 12.47, 4 => 12.93],
                2 => [1 => 15, 2 => 15.94, 3 => 17.55, 4 => 18.94],
                3 => [1 => 26.5, 2 => 25.93, 3 => 28.24, 4 => 30.02],
                4 => [1 => 42, 2 => 46.2, 3 => 52, 4 => 59]
            ],
            2 => [
                1 => [1 => 14.43, 2 => 16.86, 3 => 17.55, 4 => 18.24],
                2 => [1 => 20.76, 2 => 22.17, 3 => 23.79, 4 => 24.4],
                3 => [1 => 33.33, 2 => 37.03, 3 => 40.74, 4 => 43.98],
                4 => [1 => 58, 2 => 65, 3 => 72, 4 => 80]
            ],
            3 => [
                1 => [1 => 20.79, 2 => 21.48, 3 => 22.17, 4 => 22.98],
                2 => [1 => 27.71, 2 => 29.56, 3 => 30.72, 4 => 32.33],
                3 => [1 => 46.3, 2 => 49.54, 3 => 52.78, 4 => 56.02],
                4 => [1 => 70, 2 => 85, 3 => 90, 4 => 95]
            ],
            4 => [
                1 => [1 => 24.25, 2 => 25.4, 3 => 26.56, 4 => 27.71],
                2 => [1 => 38.11, 2 => 41.18, 3 => 42.26, 4 => 44.57],
                3 => [1 => 60.19, 2 => 64.81, 3 => 69.44, 4 => 70.07],
                4 => [1 => 90, 2 => 100, 3 => 110, 4 => 120]
            ],
        ];

        // ----------------------------
        // Validate Pricing Exists
        // ----------------------------
        if (!isset($pricing[$total_area][$how_often][$dogs])) {
            return response()->json([
                'ok' => false,
                'message' => 'Invalid combination of area, dogs, or frequency.'
            ]);
        }

        $cost = $pricing[$total_area][$how_often][$dogs];
        $amount = $cost;
        $discount_amount = 0;

        // ----------------------------
        // Apply Discount or Charge
        // ----------------------------
        if ($discount) {
            $discount_amount = ($cost * 50) / 100;
            $cost = $discount_amount + 15;
        } elseif ($charge) {
            $cost += 15;
        }

        // ----------------------------
        // Transform Display Labels
        // ----------------------------
        $howOftenLabels = [
            1 => "Two times a week",
            2 => "Ones a week",
            3 => "Bi-weekly",
            4 => "Ones a month"
        ];

        $areaLabels = [
            1 => "Under 0.2 acre",
            2 => "0.2-1/3 acre",
            3 => "1/3-1/2 acre",
            4 => "1/2-3/4 acre"
        ];

        return response()->json([
            'ok' => true,
            'message' => 'Quote calculated successfully.',
            'getDiscount' => $discount,
            'getCharge' => $charge,
            'price_per_visit' => number_format($amount, 2),
            'discount_amount' => number_format($discount_amount, 2),
            'discount_percentage' => $discount ? "50%" : "0%",
            'monthly_charge' => $charge ? "+$15.00" : "0",
            'total_cost' => number_format($cost, 2),

            'zip_code' => $zip_code,
            'how_often' => $howOftenLabels[$how_often],
            'how_many_dogs' => $dogs,
            'total_area' => $areaLabels[$total_area],
            'area_to_clean' => $area_to_clean,
        ]);
    }
}
