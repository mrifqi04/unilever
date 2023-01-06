<?php

namespace App\Http\Controllers\Master;


use App\Http\Controllers\Controller;
use App\Models\Auditor\Auditor;
use Illuminate\Http\Request;

class AuditorController extends Controller
{
    public function Get(Request $request){
        $outlet = Auditor::where("id_city", "=", $request->get("city_id"))
            ->where('is_deleted', '=', 1)->get();
        return response()->json($outlet);
    }

}