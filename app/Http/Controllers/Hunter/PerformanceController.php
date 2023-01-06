<?php

namespace App\Http\Controllers\Hunter;

use App\Http\Controllers\Controller;
use App\Models\Hunter\Performance;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Illuminate\Support\Carbon;

class PerformanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $request = Request::capture();
        $summary_from_date = $request->query('summary_from_date', '');
        if (trim($summary_from_date) == '') {
            $summary_from_date = Carbon::now();
        } else {
            $summary_from_date = Carbon::parse($summary_from_date);
        }
        $summary_to_date = $request->query('summary_to_date', '');
        if (trim($summary_to_date) == '') {
            $summary_to_date = Carbon::now();
        } else {
            $summary_to_date = Carbon::parse($summary_to_date);
        }
        $performance_juragan_name = $request->query('performance_juragan_name', '');
        $performance_hunter_id = $request->query('performance_hunter_id', '');
        $performance_hunter_name = $request->query('performance_hunter_name', '');
        $performance_from_date = $request->query('performance_from_date', '');
        if (trim($performance_from_date) == '') {
            $performance_from_date = Carbon::now();
        } else {
            $performance_from_date = Carbon::parse($performance_from_date);
        }
        $performance_to_date = $request->query('performance_to_date', '');
        if (trim($performance_to_date) == '') {
            $performance_to_date = Carbon::now();
        } else {
            $performance_to_date = Carbon::parse($performance_to_date);
        }
        $per_page = 30;
        $current_page = $request->query('summary_page', 1);
        $collection = Performance::getSummaries($summary_from_date, $summary_to_date);
        $total = $collection->count();
        $chunks = $collection->chunk($per_page);
        $items = [];
        if (isset($chunks[$current_page - 1])) {
            $items = $chunks[$current_page - 1];
        }
        $summaries = new Paginator($items, $total, $per_page, $current_page, [
            'path' => $request->url(),
            'query' => $request->query(),
            'pageName' => 'summary_page',
        ]);
        $current_page = $request->query('performance_page', 1);
        $collection = Performance::getPerformance(
            $performance_juragan_name, $performance_hunter_id, $performance_hunter_name, $performance_from_date,
            $performance_to_date);
        $total = $collection->count();
        $chunks = $collection->chunk($per_page);
        $items = [];
        if (isset($chunks[$current_page - 1])) {
            $items = $chunks[$current_page - 1];
        }
        $performances = new Paginator($items, $total, $per_page, $current_page, [
            'path' => $request->url(),
            'query' => $request->query(),
            'pageName' => 'performance_page',
        ]);
        $dailys = Performance::getDailys($performance_from_date, $performance_to_date);
        return view('hunter.performance.index')
            ->with('summaries', $summaries)
            ->with('performances', $performances)
            ->with('dailys', $dailys)
            ->with('summary_from_date', $summary_from_date->format('Y-m-d'))
            ->with('summary_to_date', $summary_to_date->format('Y-m-d'))
            ->with('performance_juragan_name', $performance_juragan_name)
            ->with('performance_hunter_id', $performance_hunter_id)
            ->with('performance_hunter_name', $performance_hunter_name)
            ->with('performance_from_date', $performance_from_date->format('Y-m-d'))
            ->with('performance_to_date', $performance_to_date->format('Y-m-d'));
    }
}
