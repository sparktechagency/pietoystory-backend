<?php

namespace App\Http\Controllers;

use App\Models\County;
use App\Models\State;
use Illuminate\Http\Request;

class CheckZipCodeController extends Controller
{

    public function checkZipCode(Request $request){
        $zipCode = $request->get("zip_code");

        return response()->json([
            'status' => true,
            'message'=> 'Not working',
        ]);
    }

    public function getStates()
    {
        return response()->json(State::all()->pluck("state_name")->toArray());
    }

    public function getCounties(Request $request)
    {
        $stateId = $request->query('state_id');
        return response()->json(County::where('state_id', $stateId)->pluck('county_name')->toArray());
    }
}
