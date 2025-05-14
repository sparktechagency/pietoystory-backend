<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CheckZipCodeController;
use App\Http\Controllers\GetDiscountChargeController;
use App\Http\Controllers\Payment\PaymentController;
use App\Http\Controllers\ReferralController;
use App\Http\Controllers\SendEnquiryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// public route
Route::post('/register', [AuthController::class, 'register']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/resend-otp', [AuthController::class, 'resendOtp']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);

// private route with jwt auth
Route::middleware('auth:api')->group(function () {
    Route::post('/change-password', [AuthController::class, 'changePassword']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/update-profile', [AuthController::class, 'updateProfile']);
    Route::post('/update-password', [AuthController::class, 'updatePassword']);
    
    // Route::post('/avatar', [AuthController::class, 'avatar']);
    // Route::post('/update-avatar', [AuthController::class, 'updateAvatar']);

    // All referrals data showing
    Route::get('/all-referred-info', [ReferralController::class, 'allReferredUsers']);

    // check zip code
    Route::post('/check-zip-code', [CheckZipCodeController::class, 'checkZipCode']);

    // send enquiry (mail send to admin)
    Route::post('/send-enquiry', [SendEnquiryController::class, 'sendEnquiry']);

    // discount $ monthly charge
    Route::get('/discount-charge', [GetDiscountChargeController::class, 'getDiscountCharge']);

    // create payment intent
    Route::post('/payment-intent', [PaymentController::class, 'paymentIntent']);
    Route::post('/payment-success', [PaymentController::class, 'paymentSuccess']);

    // get previous history
    Route::get('/get-previous-history', [PaymentController::class, 'getPreviousHistory']);
});
