<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\FreeCleaning;
use App\Models\Quotes;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class PaymentController extends Controller
{
    public function paymentIntent(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'user_id'        => 'required|numeric',
            'amount' => 'required',
            'currency' => 'required',
            'payment_method'  => 'required',
            'use_free_cleanup' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'ok' => false,
                'message' => $validator->errors()
            ], 422);
        }

        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => $request->amount * 100,
                'currency' => $request->currency,
                'payment_method' => $request->payment_method,
                'metadata' => [],
            ]);

            return response()->json([
                'ok' => true,
                'message' => 'Payment intent successfully created',
                'data' => $paymentIntent,
            ], 201);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function paymentSuccess(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'payment_intent_id' => 'sometimes',
            'user_id'           => 'required',
            'zip_code'          => 'required',
            'how_often'         => 'required',
            'amount_of_dogs'    => 'required',
            'total_area'        => 'required',
            'area_to_clean'     => 'required',
            'use_free_cleanup'  => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'ok' => false,
                'message' => $validator->errors()
            ], 422);
        }

        if($request->use_free_cleanup === '1'){

            $user = FreeCleaning::where('user_id',$request->user_id)->first();

            // Create the order with the correct pricing
            $order = Quotes::create([
                'payment_intent_id'  => null,
                'user_id'            => $request->user_id,
                'zip_code'           => $request->zip_code,
                'how_often'          => $request->how_often,
                'amount_of_dogs'     => $request->amount_of_dogs,
                'total_area'         => $request->total_area,
                'area_to_clean'      => $request->area_to_clean,
                'cost'               => null,
                'status'             => 'used_coin',
            ]);

            $user->increment('used_coins');

            return response()->json([
                'ok'  => true,
                'message' => 'Coin used done. Order recorded successfully',
                'data'    => $order,
            ], 200);
        }

        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            // Retrieve the payment intent from Stripe
            $paymentIntent = PaymentIntent::retrieve($request->payment_intent_id);

            if ($paymentIntent->status === 'requires_confirmation') {


                // Create the order with the correct pricing
                $order = Quotes::create([
                    'payment_intent_id'  => $paymentIntent->id,
                    'user_id'            => $request->user_id,
                    'zip_code'           => $request->zip_code,
                    'how_often'          => $request->how_often,
                    'amount_of_dogs'     => $request->amount_of_dogs,
                    'total_area'         => $request->total_area,
                    'area_to_clean'      => $request->area_to_clean,
                    'cost'               => $paymentIntent->amount / 100,
                    'status'             => 'success',
                ]);


                return response()->json([
                    'ok'  => true,
                    'message' => 'Payment done. order recorded successfully',
                    'data'    => $order,
                ], 200);
            } else {
                return response()->json([
                    'ok'  => false,
                    'message' => 'Payment failed. Status: ' . $paymentIntent->status,
                ], 400);
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'ok'  => false,
                'message' => 'Payment failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getPreviousHistory($id)
    {

        $user = User::find($id);

        // User Not Found
        if (!$user) {
            return response()->json([
                'ok' => false,
                'message' => 'User not found',
            ], 404);
        }

        $userOrderHistory = Quotes::where('user_id', $id)->latest()->get();
        return response()->json([
            'ok'  => true,
            'message' => 'User previous order history list',
            'data' => $userOrderHistory
        ], 200);
    }
}