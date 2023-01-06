<?php

namespace App\Http\Controllers\Master;


use App\Http\Controllers\Controller;
use App\Models\OutletManagement\Outlet;
use Illuminate\Http\Request;

class OutletController extends Controller
{
    public function GetOutlet(Request $request){
        $outlet = Outlet::where("id_juragan", "=", $request->get("id"))->whereHas("mapOutlet", function ($query){
            $query->where("is_mitra", "=", "1");
        })->get();
        return response()->json($outlet);
    }

}