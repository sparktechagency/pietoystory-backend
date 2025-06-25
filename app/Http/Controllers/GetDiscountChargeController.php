<?php

namespace App\Http\Controllers;

use App\Models\Quotes;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class GetDiscountChargeController extends Controller
{
    public function getDiscountCharge()
    {

        $user = Quotes::where('user_id', Auth::id())->latest()->first();

        $discount = null;
        $charge = null;


        if (!$user) {
            $discount = true;
            $charge = true;
        } else {
            if ($user) {
                $discount = false;
                $lastOrderMonth = Carbon::parse($user->created_at);
                $currentMonth = Carbon::parse(now()); //->addMonth(1));
                if ($lastOrderMonth->format('m') == $currentMonth->format('m')) {
                    $charge = false;
                } else {
                    $charge = true;
                }
            }
        }

        return response()->json([
            'ok' => true,
            'message' => 'Get discount and charge',
            'getDiscount' => $discount,
            'getCharge' => $charge,
        ]);
    }
}
