<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Transactions\Transaction;
use App\Models\Transactions\TransactionDetailMandiri;
use App\Models\Unilever\OutletProgress;
use PDF;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        $relation = [
            'Cabinet',
            'ShippingMandiri',
            'Outlet',
            'transction',
            'transction.Juragan'

        ];
        $trx = TransactionDetailMandiri::with($relation)->where('id', $request->query('id'))->first();
        $datas = [
            'data' => $trx
        ];
        $pdf = PDF::setOptions([
            'dpi' => 160,
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'debugCss' => false
        ])->loadView('export.export', $datas)->setPaper('a4', 'portrait');
        return $pdf->stream();
    }

    public function formExportPDF(Request $request)
    {
        $relation = [
            'DetailMandiri',
            'DetailMandiri.Outlet',
            'Juragan'

        ];
        $fileName = '';
        $trx = Transaction::with($relation)->where('id', $request->query('id'))->first();
        $picture = OutletProgress::findPicture($trx->DetailMandiri->outlet_signature_id);
        if (!is_null($picture)) {
            $fileName = substr($picture->basedir, 1, strlen($picture->basedir) - 1) . $picture->filename;
        }
		
		$file = file_get_contents('/home/ubuntu'.$fileName);
        if (trim('/home/ubuntu'.$fileName) == '' || !file_exists('/home/ubuntu'.$fileName)) {
            $fileName = '';
        }else{
            $fileName = base64_encode($file);
        }

        $datas = [
            'data' => $trx,
            'ttd' => "data:image/png;base64,".$fileName
        ];
        $pdf = PDF::setOptions([
            'dpi' => 160,
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'debugCss' => false
//        ])->loadView('Warehouse.Delivery.export', $datas)->setPaper('a4', 'landscape');
        ])->loadView('export.withdraw_form', $datas)->setPaper('a4', 'portrait');
        return $pdf->stream();
//        return $pdf->download('Penarikan.pdf');
    }

    public function AdrExportPDF(Request $request)
    {
        $relation = [
            'Cabinet',
            'ShippingMandiri',
            'DestOutlet',
            'transction',
            'transction.Juragan'

        ];
        $trx = TransactionDetailMandiri::with($relation)->where('id', $request->query('id'))->first();
        $datas = [
            'data' => $trx
        ];
        $pdf = PDF::setOptions([
            'dpi' => 160,
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'debugCss' => false
//        ])->loadView('Warehouse.Delivery.export', $datas)->setPaper('a4', 'landscape');
        ])->loadView('export.adr', $datas)->setPaper('a4', 'portrait');
        return $pdf->stream();
//        return $pdf->download('Penarikan.pdf');
    }
}
