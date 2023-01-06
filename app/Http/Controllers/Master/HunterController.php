<?php

namespace App\Http\Controllers\Master;


use App\Http\Controllers\Controller;
use App\Models\Hunter\Hunter;
use Illuminate\Http\Request;

class HunterController extends Controller
{
    public function Get(Request $request){
        $outlet = Hunter::where("id_city", "=", $request->get("city_id"))->get();
        return response()->json($outlet);
    }

}