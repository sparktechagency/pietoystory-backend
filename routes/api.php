<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CheckZipCodeController;
use App\Http\Controllers\GetDiscountChargeController;
use App\Http\Controllers\Payment\PaymentController;
use App\Http\Controllers\QuotesController;
use App\Http\Controllers\ReferralController;
use App\Http\Controllers\SendEnquiryController;
use Illuminate\Support\Facades\Route;

// public route
Route::post('/register', [AuthController::class, 'register']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/resend-otp', [AuthController::class, 'resendOtp']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);

Route::post('/check-zip-code', [CheckZipCodeController::class, 'checkZipCode']);
Route::get('/get-states', [CheckZipCodeController::class, 'getStates']);
Route::get('/get-counties', [CheckZipCodeController::class, 'getCounties']);

Route::get('/quote', [QuotesController::class, 'quote']);

Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/update-profile', [AuthController::class, 'updateProfile']);
    Route::post('/update-password', [AuthController::class, 'updatePassword']);
    Route::get('/all-referred-info', [ReferralController::class, 'allReferredUsers']);
    Route::post('/send-enquiry', [SendEnquiryController::class, 'sendEnquiry']);
    Route::get('/discount-charge', [GetDiscountChargeController::class, 'getDiscountCharge']);
    
    Route::post('/payment-intent', [PaymentController::class, 'paymentIntent']);
    Route::post('/payment-success', [PaymentController::class, 'paymentSuccess']);
    Route::get('/get-previous-history', [PaymentController::class, 'getPreviousHistory']);
});
