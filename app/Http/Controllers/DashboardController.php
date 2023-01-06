<?php

namespace App\Http\Controllers;


use App\Http\Resources\HomeDashboardCabinet;
use App\Http\Resources\HomeDashboardJuragan;
use App\Http\Resources\HomeDashboardOutlet;
use App\Models\JuraganManagement\Juragan;
use App\Models\OutletManagement\Outlet;
use App\Models\Warehouse\Cabinets;
use App\Models\Auditor\Login;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class DashboardController extends Controller {
    //put your code here
    public function __construct() {
        $this->middleware('auth');
    }

    public function index(){
        // $auditor = Login::where('username', 6281310299811)->first();
        // $auditor->update([
        //     'password' => Hash::make(123456)
        // ]);
        // if ($auditor) {
        //     dd('sukses');
        // } else {
        //     dd('fail');
        // }
        return view('Dashboard');
    }

    public function totalJuragan(Request $request){
        $data = new HomeDashboardJuragan(new Juragan());
        return $data;
    }

    public function totalOutlet(Request $request){
        $data = new HomeDashboardOutlet(new Outlet());
        return $data;
    }

    public function totalCabinet(Request $request){
        $data = new HomeDashboardCabinet(new Cabinets());
        return $data;
    }

    public function getAllLocationsData()
    {
        $locationsData = DB::table('provinces')
            ->leftJoin('cities', 'provinces.id', '=', 'cities.id_province')
            ->leftJoin('districts', 'cities.id', '=', 'districts.id_city')
            ->leftJoin('villages', 'districts.id', '=', 'villages.id_district')
            ->select(
                'provinces.id as province_id',
                'provinces.name as province_name',
                'cities.id as city_id',
                'cities.name as city_name',
                'districts.id as district_id',
                'districts.name as district_name',
                'villages.id as village_id',
                'villages.name as village_name'
            )                     
            ->get();        
        
        return response()->json(['data' => $locationsData]);
    }

    public function policy()
    {
        return view('policy');
    }
}