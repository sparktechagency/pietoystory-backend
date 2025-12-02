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

        // validation roles
        $validator = Validator::make($request->all(), [
            'zip_code' => 'nullable|digits:5',
            'how_often' => 'nullable|numeric|in:1,2,3,4',
            'how_many_dogs' => 'nullable|numeric|in:1,2,3,4',
            // 'total_area_size'   => 'required|numeric|min:1|max:32670',
            'total_area_size' => 'nullable|numeric|in:1,2,3,4',
            'area_to_clean' => 'nullable|string|max:255',
        ], [
            'zip_code.digits' => 'Zip code must be exactly 5 digits.',
            'how_often.in' => 'Invalid value for how often. Allowed: 1, 2, 3, 4.',
            'how_many_dogs.in' => 'You can select 1 to 4 dogs only.',
            'total_area_size.in' => 'Invalid value for total area size. Allowed: 1, 2, 3, 4.',
            'total_area_size.min' => 'Total area size must be greater than 0.',
            'total_area_size.max' => 'Total area size must not be greater than 32,670.',
        ]);

        // check validation
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()
            ], 422);
        }

        $user = Quotes::where('user_id', Auth::id())->latest()->first();

        $discount = null;
        $charge = null;
        $discount_amount = 0;


        if (!$user) {
            // $discount = true;
            // $charge = true;
            $discount = false;
            $charge = false;
        } else {
            // if ($user) {
            // $discount = false;
            $discount = false;
            $charge = false;
            // $lastOrderMonth = Carbon::parse($user->created_at);
            // $currentMonth = Carbon::parse(now()); //->addMonth(1));
            // if ($lastOrderMonth->format('m') == $currentMonth->format('m')) {
            //     $charge = false;
            // } else {
            //     $charge = true;
            // }
            // }
        }
        

        $total_area_size = $request->total_area_size;
        $how_often = $request->how_often ?? 1;
        $dogs = $request->how_many_dogs ?? 1;
        $area_to_clean = $request->area_to_clean ?? 1;
        $zip_code = $request->zip_code;
        $cost = 0;

        // if ($total_area_size > 0 && $total_area_size < 8712) {
        if ($total_area_size == 1) {
            if ($how_often == '1') {
                switch ($dogs) {
                    case '1':
                        $cost = 11.55;
                        break;

                    case '2':
                        $cost = 12.12;
                        break;

                    case '3':
                        $cost = 12.47;
                        break;
                    case '4':
                        $cost = 12.93;
                        break;

                    default:
                        $message = 'Dogs only - 1 to 4 dog.';
                }
            } elseif ($how_often == '2') {
                switch ($dogs) {
                    case '1':
                        $cost = 15;
                        break;

                    case '2':
                        $cost = 15.94;
                        break;

                    case '3':
                        $cost = 17.55;
                        break;
                    case '4':
                        $cost = 18.94;
                        break;

                    default:
                        $message = 'Dogs only - 1 to 4 dog.';
                }
            } elseif ($how_often == '3') {
                switch ($dogs) {
                    case '1':
                        $cost = 26.5;
                        break;

                    case '2':
                        $cost = 25.93;
                        break;

                    case '3':
                        $cost = 28.24;
                        break;
                    case '4':
                        $cost = 30.02;
                        break;

                    default:
                        $message = 'Dogs only - 1 to 4 dog.';
                }
            } elseif ($how_often == '4') {
                switch ($dogs) {
                    case '1':
                        $cost = 42;
                        break;

                    case '2':
                        $cost = 46.2;
                        break;

                    case '3':
                        $cost = 52;
                        break;
                    case '4':
                        $cost = 59;
                        break;

                    default:
                        $message = 'Dogs only - 1 to 4 dog.';
                }
                return response()->json([
                    'ok' => false,
                    'message' => $message,
                ]);
            } else {
                return response()->json([
                    'ok' => false,
                    'message' => 'How often only - Two times a week, Ones a week, By weekly, Ones a month',
                ]);
            }
            // } elseif ($total_area_size > 8712 && $total_area_size < 14520) {
        } elseif ($total_area_size == 2) {
            if ($how_often == '1') {
                switch ($dogs) {
                    case '1':
                        $cost = 14.43;
                        break;

                    case '2':
                        $cost = 16.86;
                        break;

                    case '3':
                        $cost = 17.55;
                        break;
                    case '4':
                        $cost = 18.24;
                        break;

                    default:
                        $message = 'Dogs only - 1 to 4 dog.';
                }
            } elseif ($how_often == '2') {
                switch ($dogs) {
                    case '1':
                        $cost = 20.76;
                        break;

                    case '2':
                        $cost = 22.17;
                        break;

                    case '3':
                        $cost = 23.79;
                        break;
                    case '4':
                        $cost = 24.4;
                        break;

                    default:
                        $message = 'Dogs only - 1 to 4 dog.';
                }
            } elseif ($how_often == '3') {
                switch ($dogs) {
                    case '1':
                        $cost = 33.33;
                        break;

                    case '2':
                        $cost = 37.03;
                        break;

                    case '3':
                        $cost = 40.74;
                        break;
                    case '4':
                        $cost = 43.98;
                        break;

                    default:
                        $message = 'Dogs only - 1 to 4 dog.';
                }
            } elseif ($how_often == '4') {
                switch ($dogs) {
                    case '1':
                        $cost = 58;
                        break;

                    case '2':
                        $cost = 65;
                        break;

                    case '3':
                        $cost = 72;
                        break;
                    case '4':
                        $cost = 80;
                        break;

                    default:
                        $message = 'Dogs only - 1 to 4 dog.';
                }
                return response()->json([
                    'ok' => false,
                    'message' => $message,
                ]);
            } else {
                return response()->json([
                    'ok' => false,
                    'message' => 'How often only - Two times a week, Ones a week, By weekly, Ones a month',
                ]);
            }
            // } elseif ($total_area_size > 14520 && $total_area_size < 21780) {
        } elseif ($total_area_size == 3) {
            if ($how_often == '1') {
                switch ($dogs) {
                    case '1':
                        $cost = 20.79;
                        break;

                    case '2':
                        $cost = 21.48;
                        break;

                    case '3':
                        $cost = 22.17;
                        break;
                    case '4':
                        $cost = 22.98;
                        break;

                    default:
                        $message = 'Dogs only - 1 to 4 dog.';
                }
            } elseif ($how_often == '2') {
                switch ($dogs) {
                    case '1':
                        $cost = 27.71;
                        break;

                    case '2':
                        $cost = 29.56;
                        break;

                    case '3':
                        $cost = 30.72;
                        break;
                    case '4':
                        $cost = 32.33;
                        break;

                    default:
                        $message = 'Dogs only - 1 to 4 dog.';
                }
            } elseif ($how_often == '3') {
                switch ($dogs) {
                    case '1':
                        $cost = 46.3;
                        break;

                    case '2':
                        $cost = 49.54;
                        break;

                    case '3':
                        $cost = 52.78;
                        break;
                    case '4':
                        $cost = 56.02;
                        break;

                    default:
                        $message = 'Dogs only - 1 to 4 dog.';
                }
            } elseif ($how_often == '4') {
                switch ($dogs) {
                    case '1':
                        $cost = 70;
                        break;

                    case '2':
                        $cost = 85;
                        break;

                    case '3':
                        $cost = 90;
                        break;
                    case '4':
                        $cost = 95;
                        break;

                    default:
                        $message = 'Dogs only - 1 to 4 dog.';
                }
                return response()->json([
                    'ok' => false,
                    'message' => $message,
                ]);
            } else {
                return response()->json([
                    'ok' => false,
                    'message' => 'How often only - Two times a week, Ones a week, By weekly, Ones a month',
                ]);
            }
            // } elseif ($total_area_size > 21780 && $total_area_size < 32670) {
        } elseif ($total_area_size == 4) {
            if ($how_often == '1') {
                switch ($dogs) {
                    case '1':
                        $cost = 24.25;
                        break;

                    case '2':
                        $cost = 25.4;
                        break;

                    case '3':
                        $cost = 26.56;
                        break;
                    case '4':
                        $cost = 27.71;
                        break;

                    default:
                        $message = 'Dogs only - 1 to 4 dog.';
                }
            } elseif ($how_often == '2') {
                switch ($dogs) {
                    case '1':
                        $cost = 38.11;
                        break;

                    case '2':
                        $cost = 41.18;
                        break;

                    case '3':
                        $cost = 42.26;
                        break;
                    case '4':
                        $cost = 44.57;
                        break;

                    default:
                        $message = 'Dogs only - 1 to 4 dog.';
                }
            } elseif ($how_often == '3') {
                switch ($dogs) {
                    case '1':
                        $cost = 60.19;
                        break;

                    case '2':
                        $cost = 64.81;
                        break;

                    case '3':
                        $cost = 69.44;
                        break;
                    case '4':
                        $cost = 70.07;
                        break;

                    default:
                        $message = 'Dogs only - 1 to 4 dog.';
                }
            } elseif ($how_often == '4') {
                switch ($dogs) {
                    case '1':
                        $cost = 90;
                        break;

                    case '2':
                        $cost = 100;
                        break;

                    case '3':
                        $cost = 110;
                        break;
                    case '4':
                        $cost = 120;
                        break;

                    default:
                        $message = 'Dogs only - 1 to 4 dog.';
                }
                return response()->json([
                    'ok' => false,
                    'message' => $message,
                ]);
            } else {
                return response()->json([
                    'ok' => false,
                    'message' => 'How often only - Two times a week, Ones a week, By weekly, Ones a month',
                ]);
            }
        } else {
            return response()->json([
                'ok' => false,
                'message' => 'Area is 1 to 32670 sq.ft.',
            ]);
        }

        $amount = $cost;

        if ($discount == true) {
            $cost = $cost / 100 * 50;
            $discount_amount = $cost;
            $cost = $cost + 15;
        } elseif ($charge == true) {
            $cost = $cost + 15;
        }

        $total_cost = $cost;

        if ($how_often == 1) {
            $how_often = 'Two times a week';
        } elseif ($how_often == 2) {
            $how_often = 'Ones a week';
        } elseif ($how_often == 3) {
            $how_often = 'Bi-weekly';
        } elseif ($how_often == 4) {
            $how_often = 'Ones a month';
        }

        if ($total_area_size == 1) {
            $total_area_size = 'Under 0.2 acre';
        } elseif ($total_area_size == 2) {
            $total_area_size = '0.2-1/3 acre';
        } elseif ($total_area_size == 3) {
            $total_area_size = '1/3-1/2 acre';
        } elseif ($total_area_size == 4) {
            $total_area_size = '1/2-3/4 acre';
        }


        return response()->json([
            'ok' => true,
            'message' => 'Get discount and charge and services',
            'getDiscount' => $discount,
            'getCharge' => $charge,
            'price_per_visit' => number_format($amount, 2),
            'discount_amount' => '-' . number_format($discount_amount, 2),
            'discount_percentage' => '50%',
            'monthly_charge' => '+$15.00',
            'total_cost' => number_format($total_cost, 2),
            'zip_code' => $zip_code,
            'how_often' => $how_often,
            'how_many_dogs' => $dogs,
            'total_area' => $total_area_size,
            'area_to_clean' => $area_to_clean,
        ]);
    }
}
