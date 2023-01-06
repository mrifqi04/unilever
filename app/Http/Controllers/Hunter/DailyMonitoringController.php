<?php

namespace App\Http\Controllers\Hunter;

use App\export\HunterDailyMonitoringExport;
use App\Http\Controllers\Controller;
use App\Models\Hunter\DailyMonitoring;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class DailyMonitoringController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $request = Request::capture();
        $hunter_id = $request->query('hunter_id', '');
        $hunter_name = $request->query('hunter_name', '');
        $date = $request->query('date', '');
        if (trim($date) == '') {
            $date = Carbon::now()->format('Y-m-d');
        }
        $current_page = $request->query('page', 1);
        $per_page = 30;
        $collection = DailyMonitoring::getMonitoring($hunter_id, $hunter_name, $date);
        $total = $collection->count();
        $chunks = $collection->chunk($per_page);
        $items = [];
        if (isset($chunks[$current_page - 1])) {
            $items = $chunks[$current_page - 1];
        }
        $outlets = new Paginator($items, $total, $per_page, $current_page, [
            'path' => $request->url(),
            'query' => $request->query(),
        ]);
        return view('hunter.dailymonitoring.index')
            ->with('outlets', $outlets)
            ->with('hunter_id', $hunter_id)
            ->with('hunter_name', $hunter_name)
            ->with('date', $date);
    }

    public function export()
    {
        $request = Request::capture();
        $hunter_id = $request->query('hunter_id', '');
        $hunter_name = $request->query('hunter_name', '');
        $date = $request->query('date', '');
        if (trim($date) == '') {
            $date = Carbon::now()->format('Y-m-d');
        }
        $collection = DailyMonitoring::getMonitoring($hunter_id, $hunter_name, $date);
        $file_name = sprintf('%s.xlsx', Str::uuid()->toString());
        return Excel::download(new HunterDailyMonitoringExport($collection), $file_name);
    }
}
