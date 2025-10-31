<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\VerifyOTPMail;
use App\Models\FreeCleaning;
use App\Models\Referral;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    // user register
    public function register(Request $request)
    {
        // email or phone number must be given for user
        if (!($request->email || $request->phone_number)) {
            return response()->json([
                'ok' => false,
                'message' => 'Email or Phone number must be given'
            ]);
        }

        // create otp
        $otp = rand(100000, 999999);
        $otp_expires_at = Carbon::now()->addMinutes(10);

        // rear case handle
        $rearUser = null;
        if ($request->email) {
            $rearUser = User::where('email', $request->email)->first();
        } else {
            $rearUser = User::where('phone_number', $request->phone_number)->first();
        }

        if (($rearUser && $rearUser->status == 'inactive')) {

            $rearUser->otp = $otp;
            $rearUser->otp_expires_at = $otp_expires_at;
            $rearUser->save();

            // Send OTP Email
            $email_otp = [
                'userName' => explode('@', $request->email)[0],
                'otp' => $otp,
                'validity' => '10 minute'
            ];

            // Send OTP Phone number
            $phone_otp = "Your OTP code: {$otp}. Valid for 10 minutes.";


            if ($rearUser->email) {
                try {
                    Mail::to($rearUser->email)->send(new VerifyOTPMail($email_otp));
                } catch (Exception $e) {
                    Log::error($e->getMessage());
                }
            } else {
                try {
                    $basic = new \Vonage\Client\Credentials\Basic(env('VONAGE_API_KEY'), env('VONAGE_API_SECRET'));
                    $client = new \Vonage\Client($basic);

                    $client->sms()->send(new \Vonage\SMS\Message\SMS($request->phone_number, env('VONAGE_SMS_FROM'), $phone_otp));
                } catch (\Throwable $th) {
                    return response()->json([
                        'ok' => false,
                        'message' => 'Free trial credit over. please upgrade your vonage account',
                        'error' => $th->getMessage(),
                    ]);
                }
            }

            // json response
            return response()->json([
                'ok' => true,
                'message' => $request->email ? 'Your account already exists, please verify your account, check you email for OTP' : 'Your account already exists, please verify your account, check you phone number for OTP',
            ], 201);
        }

        // validation roles
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email',
            'phone_number' => 'sometimes|string|max:15|unique:users,phone_number',
            'location' => 'sometimes|string',
            'parent_referral_code' => 'sometimes|string|max:8',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // check validation
        if ($validator->fails()) {
            return response()->json([
                'ok' => false,
                'message' => $validator->errors()
            ], 422);
        }

        // create referral_code
        $referral_code = strtoupper(Str::random(8));

        // parent_referral_code convert uppercase
        $parent_referral_code = strtoupper($request->parent_referral_code);

        // Check
        // $parent = null;
        // if ($request->has('ref')) {
        //     $parent = User::where('referral_code', $request->ref)->first();
        // } else {
        //     $parent = User::where('referral_code', $request->parent_referral_code)->first();
        // }

        $parent = User::where('referral_code', $request->parent_referral_code)->first();

        if ($parent_referral_code) {
            if (!$parent) {
                return response()->json([
                    'ok' => false,
                    'message' => 'Referral code is invalid',
                ]);
            } else {
                $user = User::create([
                    'full_name' => $request->full_name,
                    'email' => $request->email,
                    'phone_number' => $request->phone_number,
                    'login_type' => $request->email ? 'email' : 'phone',
                    'home_address' => $request->location,
                    'referral_code' => $referral_code,
                    'password' => Hash::make($request->password),
                    'status' => 'inactive',
                    'otp' => $otp,
                    'otp_expires_at' => $otp_expires_at,
                    'referred_by' => $parent->id,
                ]);
            }
        } else {
            $user = User::create([
                'full_name' => $request->full_name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'login_type' => $request->email ? 'email' : 'phone',
                'home_address' => $request->location,
                'referral_code' => $referral_code,
                'password' => Hash::make($request->password),
                'status' => 'inactive',
                'otp' => $otp,
                'otp_expires_at' => $otp_expires_at,
            ]);
        }

        // Send OTP Email
        $email_otp = [
            'userName' => explode('@', $request->email)[0],
            'otp' => $otp,
            'validity' => '10 minute'
        ];

        // Send OTP Phone number
        $phone_otp = "Your OTP code: {$otp}. Valid for 10 minutes.";


        if ($user->email) {
            try {
                Mail::to($user->email)->send(new VerifyOTPMail($email_otp));
            } catch (Exception $e) {
                Log::error($e->getMessage());
            }
        } else {
            try {
                $basic = new \Vonage\Client\Credentials\Basic(env('VONAGE_API_KEY'), env('VONAGE_API_SECRET'));
                $client = new \Vonage\Client($basic);

                $client->sms()->send(new \Vonage\SMS\Message\SMS($request->phone_number, env('VONAGE_SMS_FROM'), $phone_otp));
            } catch (\Throwable $th) {
                return response()->json([
                    'ok' => false,
                    'message' => 'Free trial credit over. please upgrade your vonage account',
                    'error' => $th->getMessage(),
                ]);
            }
        }

        // json response
        return response()->json([
            'ok' => true,
            'message' => $request->email ? 'Register successfully, OTP send you email, please verify your account' : 'Register successfully, OTP send you phone number, please verify your account',
        ], 201);
    }

    // verify otp
    public function verifyOtp(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'otp' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'ok' => false,
                'message' => $validator->errors()
            ], 422);
        }

        $user = User::where('otp', $request->otp)->first();

        if (!$user) {
            return response()->json([
                'ok' => false,
                'message' => 'Invalid OTP'
            ], 401);
        }

        // check otp
        if ($user->otp_expires_at > Carbon::now()) {

            // user status update
            $user->otp = null;
            $user->otp_expires_at = null;
            $user->otp_verified_at = Carbon::now();
            $user->status = 'active';
            $user->save();

            if ($user->referred_by != null) {

                // coin +2 logic only got parent user
                $parent = User::where('id', $user->referred_by)->first();
                if ($parent && $parent->status == 'active') {

                    // referral table
                    Referral::create([
                        'user_id' => $user->id,
                        'parent_id' => $parent->id,
                        'parent_referral_code' => $parent->referral_code
                    ]);

                    // coin +2 logic only got parent user
                    $checkParent = FreeCleaning::where('user_id', $parent->id)->first();
                    if (!$checkParent) {
                        $coin = new FreeCleaning();
                        $coin->user_id = $parent->id;
                        $coin->earn_coins = $coin->earn_coins + 2;
                        $coin->save();
                    } else {
                        $checkParent->user_id = $parent->id;
                        $checkParent->earn_coins = $checkParent->earn_coins + 2;
                        $checkParent->save();
                    }
                }
            }

            // custom token time
            $tokenExpiry = Carbon::now()->addDays(7);
            $customClaims = ['exp' => $tokenExpiry->timestamp];
            $token = JWTAuth::customClaims($customClaims)->fromUser($user);

            // Generate JWT Token
            // $token = JWTAuth::fromUser($user);

            // json response
            return response()->json([
                'ok' => true,
                'message' => $user->email ? 'Email verified successfully' : 'Phone number verified successfully',
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => $tokenExpiry,
                // 'expires_in' => $tokenExpiry->diffInSeconds(Carbon::now()),
                // 'expires_in' => JWTAuth::factory()->getTTL() * 60,
            ], 200);
        } else {

            return response()->json([
                'ok' => false,
                'message' => 'Invalid OTP'
            ], 401);
        }
    }

    // resend otp
    public function resendOtp(Request $request)
    {
        // validation roles
        $validator = Validator::make($request->all(), [
            'email' => 'sometimes|string|email',
            'phone_number' => 'sometimes|string|max:15',
        ]);

        // check validation
        if ($validator->fails()) {
            return response()->json([
                'ok' => false,
                'message' => $validator->errors()
            ], 422);
        }

        // Check if User Exists (by Email or Phone)
        $user = null;
        if ($request->email) {
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return response()->json([
                    'ok' => false,
                    'message' => 'Email not registered'
                ], 404);
            }
        } elseif ($request->phone_number) {
            $user = User::where('phone_number', $request->phone_number)->first();
            if (!$user) {
                return response()->json([
                    'ok' => false,
                    'message' => 'Phone number not registered'
                ], 404);
            }
        }

        $otp = rand(100000, 999999);
        $otp_expires_at = Carbon::now()->addMinutes(10);

        // DB::table('users')->updateOrInsert(
        //     ['email' => $request->email],
        //     ['otp' => $otp, 'otp_expires_at' => $otp_expires_at]
        // );

        // email user or phone user check and update otp and otp expires at
        if ($user->status == 'inactive') {
            $user->otp = $otp;
            $user->otp_expires_at = $otp_expires_at;
            $user->save();
        } else {
            return response()->json([
                'ok' => false,
                'message' => 'User already verified.'
            ], 200);
        }


        // Send OTP Email
        $data = [
            'userName' => explode('@', $request->email)[0],
            'otp' => $otp,
            'validity' => '10 minute'
        ];

        // Send OTP Phone number
        $phone_otp = "Your OTP code: {$otp}. Valid for 10 minutes.";

        if ($user->email) {
            try {
                Mail::to($user->email)->send(new VerifyOTPMail($data));
            } catch (Exception $e) {
                Log::error($e->getMessage());
            }
        } else {
            try {
                $basic = new \Vonage\Client\Credentials\Basic(env('VONAGE_API_KEY'), env('VONAGE_API_SECRET'));
                $client = new \Vonage\Client($basic);

                $client->sms()->send(new \Vonage\SMS\Message\SMS($request->phone_number, env('VONAGE_SMS_FROM'), $phone_otp));
            } catch (\Throwable $th) {
                return response()->json([
                    'ok' => false,
                    'message' => 'Free trial credit over. please upgrade your vonage account',
                    'error' => $th->getMessage(),
                ]);
            }
        }

        return response()->json([
            'ok' => true,
            'message' => $user->email ? 'OTP resend to your email' : 'OTP resend to your phone number'
        ], 200);
    }

    // user login
    public function login(Request $request)
    {
        // Validation Rules
        $validator = Validator::make($request->all(), [
            'email' => 'sometimes|required_without:phone_number|email|max:255',
            'phone_number' => 'sometimes|required_without:email|string|max:15',
            'password' => 'required|string|min:6',
            'remember_me' => 'sometimes|boolean'
        ]);

        // Custom Error Message (Optional)
        $validator->sometimes('email', 'required_without:phone_number', function ($input) {
            return empty($input->phone_number);
        });

        $validator->sometimes('phone_number', 'required_without:email', function ($input) {
            return empty($input->email);
        });

        // Return Validation Errors
        if ($validator->fails()) {
            return response()->json([
                'ok' => false,
                'message' => $validator->errors(),
            ], 422);
        }

        // Check if User Exists (by Email or Phone)
        $user = null;
        if ($request->email) {
            $user = User::where('email', $request->email)->first();
        } elseif ($request->phone_number) {
            $user = User::where('phone_number', $request->phone_number)->first();
        }

        // User Not Found
        if (!$user) {
            return response()->json([
                'ok' => false,
                'message' => 'User not found',
            ], 404);
        }

        // Check Account Status
        if ($user->status !== 'active') {
            return response()->json([
                'ok' => false,
                'message' => 'Your account is inactive. Please contact support.',
            ], 403);
        }

        // Verify Password
        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'ok' => false,
                'message' => 'Invalid password',
            ], 401);
        }

        // Generate JWT Token with remember me
        $tokenExpiry = $request->remember_me ? Carbon::now()->addDays(30) : Carbon::now()->addDays(7);
        $customClaims = ['exp' => $tokenExpiry->timestamp];
        $token = JWTAuth::customClaims($customClaims)->fromUser($user);

        // Generate JWT Token
        // $token = JWTAuth::fromUser($user);

        // Return Success Response
        return response()->json([
            'ok' => true,
            'message' => 'Login successful',
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $tokenExpiry,
            // 'expires_in' => $tokenExpiry->diffInSeconds(Carbon::now()),
            'user' => $user,
        ], 200);
    }

    // User Logout
    public function logout(Request $request)
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return response()->json([
                'status' => true,
                'message' => 'Successfully logged out'
            ]);
        } catch (JWTException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to logout, please try again'
            ], 500);
        }
    }

    // forgot password
    public function forgotPassword(Request $request)
    {
        // Validation Rules
        $validator = Validator::make($request->all(), [
            'email' => 'sometimes|required_without:phone_number|email|max:255',
            'phone_number' => 'sometimes|required_without:email|string|max:15',
        ]);

        // Custom Error Message (Optional)
        $validator->sometimes('email', 'required_without:phone_number', function ($input) {
            return empty($input->phone_number);
        });

        $validator->sometimes('phone_number', 'required_without:email', function ($input) {
            return empty($input->email);
        });

        // Return Validation Errors
        if ($validator->fails()) {
            return response()->json([
                'ok' => false,
                'message' => $validator->errors(),
            ], 422);
        }

        // Check if User Exists (by Email or Phone)
        $user = null;
        if ($request->email) {
            $user = User::where('email', $request->email)->first();
        } elseif ($request->phone_number) {
            $user = User::where('phone_number', $request->phone_number)->first();
        }

        // User Not Found
        if (!$user) {
            return response()->json([
                'ok' => false,
                'message' => 'User not found',
            ], 404);
        }


        $otp = rand(100000, 999999);
        $otp_expires_at = Carbon::now()->addMinutes(10);

        // DB::table('users')->updateOrInsert(
        //     ['email' => $request->email],
        //     ['otp' => $otp, 'otp_expires_at' => $otp_expires_at, 'status' => 'inactive']
        // );

        if ($user->status == 'active') {
            $user->otp_verified_at = null;
            $user->otp = $otp;
            $user->otp_expires_at = $otp_expires_at;
            $user->status = 'inactive';
            $user->save();
        } else {
            return response()->json([
                'ok' => false,
                'message' => 'Your are not verified user',
            ], 404);
        }

        $data = [
            'userName' => explode('@', $request->email)[0],
            'otp' => $otp,
            'validity' => '10 minutes'
        ];

        // Send OTP Phone number
        $phone_otp = "Your OTP code: {$otp}. Valid for 10 minutes.";


        if ($user->email) {
            try {
                Mail::to($request->email)->send(new VerifyOTPMail($data));
            } catch (Exception $e) {
                Log::error($e->getMessage());
                return response()->json(['error' => 'Failed to send OTP.'], 500);
            }
        } else {
            try {
                $basic = new \Vonage\Client\Credentials\Basic(env('VONAGE_API_KEY'), env('VONAGE_API_SECRET'));
                $client = new \Vonage\Client($basic);

                $client->sms()->send(new \Vonage\SMS\Message\SMS($request->phone_number, env('VONAGE_SMS_FROM'), $phone_otp));
            } catch (\Throwable $th) {
                return response()->json([
                    'ok' => false,
                    'message' => 'Free trial credit over. please upgrade your vonage account',
                    'error' => $th->getMessage(),
                ]);
            }
        }

        return response()->json([
            'ok' => true,
            'message' => $user->email ? 'OTP send to your email' : 'OTP send to your phone number'
        ], 200);
    }

    // after forgot password then change password
    public function changePassword(Request $request)
    {


        // Validation Rules
        $validator = Validator::make($request->all(), [
            'email' => 'sometimes|required_without:phone_number|email|max:255',
            'phone_number' => 'sometimes|required_without:email|string|max:15',
            'password' => 'required|string|min:6|confirmed'
        ]);

        // Custom Error Message (Optional)
        $validator->sometimes('email', 'required_without:phone_number', function ($input) {
            return empty($input->phone_number);
        });

        $validator->sometimes('phone_number', 'required_without:email', function ($input) {
            return empty($input->email);
        });

        // Return Validation Errors
        if ($validator->fails()) {
            return response()->json([
                'ok' => false,
                'message' => $validator->errors(),
            ], 422);
        }

        // Check if User Exists (by Email or Phone)
        $user = null;
        if ($request->email) {
            $user = User::where('email', $request->email)->first();
        } elseif ($request->phone_number) {
            $user = User::where('phone_number', $request->phone_number)->first();
        }

        // User Not Found
        if (!$user) {
            return response()->json([
                'ok' => false,
                'message' => 'User not found',
            ], 404);
        }
        ;

        if ($user->status == 'active') {
            $user->password = Hash::make($request->password);
            $user->save();
            return response()->json([
                '0k' => true,
                'message' => 'Password change successfully!',
            ]);
        } else {
            return response()->json([
                'ok' => false,
                'message' => 'Your are not verified user'
            ]);
        }
    }

    // user profile by id
    public function profile()
    {
        $user = User::find(Auth::id());
        if (!$user) {
            return response()->json([
                'ok' => false,
                'message' => 'User not found'
            ], 404);
        }

        // array
        $decodedArray = json_decode($user->dog_names, false);
        $user->dog_names = $decodedArray;
        // $user->avatar = asset($user->avatar);
        $user->avatar = $user->avatar ? $user->avatar : 'https://ui-avatars.com/api/?background=random&name=' . urlencode($user->full_name);

        return response()->json([
            'ok' => true,
            'message' => 'User profile',
            'data' => $user
        ], 200);
    }

    // user profile update by id
    public function updateProfile(Request $request)
    {
        // validation roles
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'email' => 'sometimes|string|email|max:255',
            'phone_number' => 'sometimes|string|max:15',
            'home_address' => 'required|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'dog_names' => 'required|array',
            'avatar' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB max
        ]);

        // check validation
        if ($validator->fails()) {
            return response()->json([
                'ok' => false,
                'message' => $validator->errors()
            ], 422);
        }

        $user = User::find(Auth::id());

        // User Not Found
        if (!$user) {
            return response()->json([
                'ok' => false,
                'message' => 'User not found',
            ], 404);
        }

        if ($request->hasFile('avatar')) {
            if ($user->avatar && file_exists(public_path($user->avatar))) {
                unlink(public_path($user->avatar));
            }

            $file = $request->file('avatar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $filepath = $file->storeAs('avatars', $filename, 'public');
            $user->avatar = '/storage/' . $filepath;
            $user->save();
        }


        $user->full_name = $request->full_name;

        // if ($user->email != null) {
        //     $user->contact = $request->phone_number;
        // } else {
        //     $user->contact = $request->email;
        // }

        if ($user->email != null) {
            $user->phone_number = $request->phone_number;
        } else {
            $user->email = $request->email;
        }


        $user->home_address = $request->home_address;
        $user->city = $request->city;
        $user->state = $request->state;
        $user->dog_names = $request->dog_names;
        $user->save();

        return response()->json([
            'ok' => true,
            'message' => 'Profile updated successfully!',
        ]);
    }

    // user update your account password
    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|min:6',
            'password' => 'required|string|min:6|confirmed'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'ok' => false,
                'message' => $validator->errors()
            ], 422);
        }

        $user = User::find(Auth::id());

        if (!$user) {
            return response()->json([
                'ok' => false,
                'message' => 'User not found'
            ], 404);
        }

        if (Hash::check($request->current_password, $user->password)) {
            $user->password = Hash::make($request->password);
            $user->save();

            return response()->json([
                'ok' => true,
                'message' => 'Password updated successfully!',
            ]);
        } else {
            return response()->json([
                'ok' => false,
                'message' => 'Invalid current password!',
            ]);
        }
    }

    // upload avatar
    // public function avatar(Request $request)
    // {
    //     $user = User::findOrFail(Auth::id());


    //     $validator = Validator::make($request->all(), [
    //         'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB max
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'ok' => false,
    //             'message' => $validator->errors()
    //         ], 422);
    //     }

    //     if ($request->hasFile('avatar')) {
    //         $file      = $request->file('avatar');
    //         $filename  = time() . '_' . $file->getClientOriginalName();
    //         $filepath  = $file->storeAs('avatars', $filename, 'public');

    //         $user->avatar = '/storage/' . $filepath;
    //         $user->save();

    //         return response()->json([
    //             'message' => 'Image uploaded successfully!',
    //             'path'    => $user->avatar,
    //         ]);
    //     }

    //     return response()->json([
    //         'message' => 'No image uploaded!',
    //     ], 400);
    // }

    // update profile avatar
    // public function updateAvatar(Request $request)
    // {
    //     $user = User::findOrFail(Auth::id());

    //     $validator = Validator::make($request->all(), [
    //         'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB max
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'ok' => false,
    //             'message' => $validator->errors()
    //         ], 422);
    //     }

    //     if ($request->hasFile('avatar')) {
    //         if ($user->avatar && file_exists(public_path($user->avatar))) {
    //             unlink(public_path($user->avatar));
    //         }

    //         $file      = $request->file('avatar');
    //         $filename  = time() . '_' . $file->getClientOriginalName();
    //         $filepath  = $file->storeAs('avatars', $filename, 'public');

    //         $user->avatar = '/storage/' . $filepath;
    //         $user->save();

    //         return response()->json([
    //             'ok'      => true,
    //             'message' => 'Avatar updated successfully!',
    //             'path'    => $user->avatar,
    //         ]);
    //     }

    //     return response()->json([
    //         'ok' => false,
    //         'message' => 'No image uploaded!',
    //     ], 400);
    // }
}
