<?php

namespace App\Http\Controllers\Master;

use App\export\JuraganOutletStatusExport;
use App\Http\Controllers\Controller;
use App\Http\Resources\JuraganSummary;
use App\Models\Driver\Drivers;
use App\Models\Driver\Vehicles;
use App\Models\JuraganManagement\Juragan;
use App\Models\OutletManagement\Outlet;
use App\Models\Warehouse\Cabinets;
use App\Models\Warehouse\RoutePlan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Province;
use App\Models\City;
use App\Models\District;
use App\Models\Village;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class JuraganController extends Controller
{
    public function summaryJuragan(Request $request){
        $startDate = null;
        $endDate = null;

        if($request->get("start_date") == "" || $request->get("start_date") == null ){
            $startDate = Carbon::now()->format("Y-m-d");
        }else{
            $startDate = Carbon::parse($request->get("start_date"))->format("Y-m-d");
        }

        if($request->get("end_date") == "" || $request->get("end_date") == null ){
            $endDate = Carbon::now()->format("Y-m-d");
        }else{
            $endDate = Carbon::parse($request->get("end_date"))->format("Y-m-d");
        }
        $juragan = new Juragan();
        $data = new JuraganSummary($juragan->summary($startDate, $endDate));
        return $data;
    }

    public function summaryOutlet(Request $request){
        $startDate = null;
        $endDate = null;

        if($request->get("start_date") == "" || $request->get("start_date") == null ){
            $startDate = Carbon::now()->format("Y-m-d");
        }else{
            $startDate = Carbon::parse($request->get("start_date"))->format("Y-m-d");
        }

        if($request->get("end_date") == "" || $request->get("end_date") == null ){
            $endDate = Carbon::now()->format("Y-m-d");
        }else{
            $endDate = Carbon::parse($request->get("end_date"))->format("Y-m-d");
        }
        $juragan = new Juragan();
        $data = new JuraganSummary($juragan->summaryMitra($startDate, $endDate));
        return $data;
    }

    public function summaryOutletMandiri(Request $request){
        $startDate = null;
        $endDate = null;

        if($request->get("start_date") == "" || $request->get("start_date") == null ){
            $startDate = Carbon::now()->format("Y-m-d");
        }else{
            $startDate = Carbon::parse($request->get("start_date"))->format("Y-m-d");
        }

        if($request->get("end_date") == "" || $request->get("end_date") == null ){
            $endDate = Carbon::now()->format("Y-m-d");
        }else{
            $endDate = Carbon::parse($request->get("end_date"))->format("Y-m-d");
        }
        $juragan = new Juragan();
        $data = new JuraganSummary($juragan->summaryMitraMandiri($startDate, $endDate));
        return $data;
    }

    public function summaryOutletProgress(Request $request){
        $startDate = null;
        $endDate = null;

        if($request->get("start_date") == "" || $request->get("start_date") == null ){
            $startDate = Carbon::now()->format("Y-m-d");
        }else{
            $startDate = Carbon::parse($request->get("start_date"))->format("Y-m-d");
        }

        if($request->get("end_date") == "" || $request->get("end_date") == null ){
            $endDate = Carbon::now()->format("Y-m-d");
        }else{
            $endDate = Carbon::parse($request->get("end_date"))->format("Y-m-d");
        }
        $juragan = new Juragan();
        $data = new JuraganSummary($juragan->summaryOutletProgress($startDate, $endDate));
        return $data;
    }

    public function statusOutletSummary(Request $request){
        $startDate = null;
        $endDate = null;
        $id = $request->get("id");
        $cityId = $request->get("city_id");

        if($request->get("start_date") == "" || $request->get("start_date") == null ){
            $startDate = Carbon::now()->format("Y-m-d");
        }else{
            $startDate = Carbon::parse($request->get("start_date"))->format("Y-m-d");
        }

        if($request->get("end_date") == "" || $request->get("end_date") == null ){
            $endDate = Carbon::now()->format("Y-m-d");
        }else{
            $endDate = Carbon::parse($request->get("end_date"))->format("Y-m-d");
        }
        $juragan = new Juragan();
        $data = new JuraganSummary($juragan->statusOutletSummary($startDate, $endDate, $id, $cityId));
        return $data;
    }

    // export
    public function ExportStatusOutletSummary(Request $request){
        $startDate = null;
        $endDate = null;
        $id = $request->get("id");
        $cityId = $request->get("city_id");

        if($request->get("start_date") == "" || $request->get("start_date") == null ){
            $startDate = Carbon::now()->format("Y-m-d");
        }else{
            $startDate = Carbon::parse($request->get("start_date"))->format("Y-m-d");
        }

        if($request->get("end_date") == "" || $request->get("end_date") == null ){
            $endDate = Carbon::now()->format("Y-m-d");
        }else{
            $endDate = Carbon::parse($request->get("end_date"))->format("Y-m-d");
        }
        $fileName = "JuraganOutletStatus-".$startDate."-".$endDate.".xlsx";
        return Excel::download(new JuraganOutletStatusExport($startDate, $endDate, $id, $cityId), $fileName);
    }

    public function Get(Request $request){
        $data = null;
        if($request->get("city_id") != ""){
            $data = Juragan::where("id_city", "=", $request->get("city_id"))->get();
        }else{
            $data = Juragan::get();
        }

        return response()->json($data);
    }


}