<?php

namespace App\Http\Controllers;

use App\Mail\SendEnquiryMail;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class SendEnquiryController extends Controller
{
    public function sendEnquiry(Request $request)
    {
         // validation roles
         $validator = Validator::make($request->all(), [
            'full_name'             => 'required|string|max:255',
            'email'                 => 'required|string|email|max:255',
            'enquiry'               => 'required|string',
        ]);

        // check validation
        if ($validator->fails()) {
            return response()->json([
                'ok' => false,
                'message'   => $validator->errors()
            ], 422);
        }

        $data = [
            'full_name' => $request->full_name,
            'email' => $request->email,
            'enquiry' => $request->enquiry,
        ];


        try {
            Mail::to('clearpathpetwasteinfo@gmail.com')->send(new SendEnquiryMail($data));
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }


        // json response
        return response()->json([
            'ok' => true,
            'message' => 'Send enquiry mail to the admin',
        ], 201);
    }
}
