<?php

namespace App\Http\Controllers\Warehouse\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Province;
use App\Models\Transactions\Transaction;
use PDF;
use Illuminate\Http\Request;

class TarikCabinetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */



    private function relation($request)
    {
        return [
            'DetailMandiri',
            'DetailMandiri.Cabinet',
            'DetailMandiri.Outlet',
            'MandiriTimelines',
            'Approval',
        ];
    }

    public function index(Request $request)
    {
        $trx = Transaction::with($this->relation($request))->orderBy('created_at', 'DESC');
        $trx = $trx->paginate(10);
        $data = [
            'trx' => $trx,
            'provinces' => Province::all()->pluck('name', 'id')
        ];
        return view('Warehouse/Cabinet/Withdraw/index', $data);
//        return view('Warehouse/Cabinet/Withdraw/index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function exportPDF(Request $request)
    {
//        $relation = [
//            'journeyRoute',
//            'journeyRoute.deliveryOrders',
//            'journeyRoute.outlet',
//            'journeyRoute.outlet.juragan',
//            'journeyRoute.cabinet'
//        ];
//        $header = JourneyPlan::with(['journeyRoute', 'journeyRoute.deliveryOrders', 'driver', 'vehicle'])->where('id', $request->get('id'))->first();
//        $data = JourneyPlan::with($relation)->where('id', $request->get('id'));
//        $data = $data->get();
//        $datas = [
//            'header' => $header,
//            'data' => $data
//        ];
//        dd($data);
        $pdf = PDF::setOptions([
            'dpi' => 160,
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'debugCss' => false
//        ])->loadView('Warehouse.Delivery.export', $datas)->setPaper('a4', 'landscape');
        ])->loadView('export.export')->setPaper('a4', 'portrait');
        return $pdf->stream();
//        return $pdf->download('Penarikan.pdf');
    }

    public function formExportPDF(Request $request)
    {
//        $relation = [
//            'journeyRoute',
//            'journeyRoute.deliveryOrders',
//            'journeyRoute.outlet',
//            'journeyRoute.outlet.juragan',
//            'journeyRoute.cabinet'
//        ];
//        $header = JourneyPlan::with(['journeyRoute', 'journeyRoute.deliveryOrders', 'driver', 'vehicle'])->where('id', $request->get('id'))->first();
//        $data = JourneyPlan::with($relation)->where('id', $request->get('id'));
//        $data = $data->get();
//        $datas = [
//            'header' => $header,
//            'data' => $data
//        ];
//        dd($data);
        $pdf = PDF::setOptions([
            'dpi' => 160,
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'debugCss' => false
//        ])->loadView('Warehouse.Delivery.export', $datas)->setPaper('a4', 'landscape');
        ])->loadView('export.withdraw_form')->setPaper('a4', 'portrait');
        return $pdf->stream();
//        return $pdf->download('Penarikan.pdf');
    }

    public function AdrExportPDF(Request $request)
    {
//        $relation = [
//            'journeyRoute',
//            'journeyRoute.deliveryOrders',
//            'journeyRoute.outlet',
//            'journeyRoute.outlet.juragan',
//            'journeyRoute.cabinet'
//        ];
//        $header = JourneyPlan::with(['journeyRoute', 'journeyRoute.deliveryOrders', 'driver', 'vehicle'])->where('id', $request->get('id'))->first();
//        $data = JourneyPlan::with($relation)->where('id', $request->get('id'));
//        $data = $data->get();
//        $datas = [
//            'header' => $header,
//            'data' => $data
//        ];
//        dd($data);
        $pdf = PDF::setOptions([
            'dpi' => 160,
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'debugCss' => false
//        ])->loadView('Warehouse.Delivery.export', $datas)->setPaper('a4', 'landscape');
        ])->loadView('export.adr')->setPaper('a4', 'portrait');
        return $pdf->stream();
//        return $pdf->download('Penarikan.pdf');
    }
}
