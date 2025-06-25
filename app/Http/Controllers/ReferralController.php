<?php

namespace App\Http\Controllers;

use App\Models\FreeCleaning;
use App\Models\Referral;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ReferralController extends Controller
{
    public function allReferredUsers()
    {
        $user = User::find(Auth::id());

        if (!$user) {
            return response()->json([
                'ok' => false,
                'message' => 'User not found'
            ]);
        }

        try {
            $totalReferList = Referral::with('user')->where('parent_id', $user->id)->latest()->get();

            $userCoinData = FreeCleaning::where('user_id', $user->id)->first();
            $userCoinData['remaining_coins'] = $userCoinData->earn_coins - $userCoinData->used_coins;

            return response()->json([
                'ok' => true,
                'message' => 'All referrals data showing',
                'user' => $user,
                'totalRefer' => count($totalReferList),
                'userCoinData' => $userCoinData,
                'totalReferList' => $totalReferList,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'ok' => false,
                'message' => 'This user has not referred anyone.',
                'user' => $user,
                'totalRefer' => 0,
                'userCoinData' => [
                    "id"=> null,
                    "user_id"=> null,
                    "earn_coins"=> 0,
                    "used_coins"=> 0,
                    "remaining_coins"=> 0,
                    "created_at"=> null,
                    "updated_at"=> null,
                ],
                'totalReferList' => 'Not referred list',
            ]);
        }
    }
}
