<?php

namespace App\Http\Controllers\Warehouse\Transaction;

use App\export\OutletTukarMandiri;
use App\Http\Controllers\Controller;
use App\Models\Province;
use App\Models\Transactions\Status;
use App\Models\Transactions\Transaction;
use App\Models\Unilever\OutletProgress;
use App\Models\UserManagement\Role;
use Carbon\Carbon;
use function foo\func;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PDF;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Ramsey\Uuid\Uuid;

//class MandiriController extends GenericController
class MandiriController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

//    protected $relation = [
//        'DetailMandiri',
//        'MandiriTimelines',
//        'Approval'
//    ];

    private function relationIndex($request)
    {
        return [
            'DetailMandiri',
            'DetailMandiri.Outlet',
            'DetailMandiri.DestOutlet',
            'DetailMandiri.ShippingMandiri',
            'DetailMandiri.ShippingMandiri.ShippingMandiriAnswer',
            'Status',
            'Juragan',
        ];
    }

    private function relation($request)
    {
        return [
            'DetailMandiri',
            'DetailMandiri.Cabinet',
            'DetailMandiri.Outlet',
            'DetailMandiri.DestOutlet',
            'DetailMandiri.ShippingMandiri',
            'DetailMandiri.ShippingMandiri.ShippingMandiriAnswer',
            'MandiriTimelines',
            'MandiriTimelines.Status',
            'Approval',
            'Approval.UliUser',
            'Approval.AsmUser',
            'Status',
            'Juragan',
        ];
    }

    public function index(Request $request)
    {

        $trx = Transaction::with($this->relationIndex($request));
		$trx->whereHas('DetailMandiri', function ($query) {});
        if (Auth::user()->roles[0]->id == '64132dec-322e-474a-a003-4c68ccc510fd') {
            $trx->whereHas('Approval', function ($query) {
                $query->where('unilever_approval_status_id', 1);
                $query->where('asm_user_id', "");
            });
        }

        if (Auth::user()->roles[0]->id == 'af100e62-62fa-48a6-b52a-d68d22a32e6a') {
            $trx->where('status_id', '=', 5);
        }
        if($request->query->get("juragan") != ""){
            $trx->whereHas("Juragan", function ($query) use($request){
                $search = $request->query->get("juragan");
                $query->where(function ($query) use ($search) {
                    $query->orWhereRaw("id_unilever_owner ilike ?", ["%" . $search . "%"])
                        ->orWhereRaw("name ilike ?", ["%" . $search . "%"]);
                });
            });
        }

        $trx = $trx->orderBy('created_at', 'DESC')->paginate(10);

        $data = [
            'trx' => $trx,
            'provinces' => Province::all()->pluck('name', 'id')
        ];
        return view('Warehouse/Cabinet/Self/index', $data);
    }

    public function status(Request $request, $id)
    {
        $trx = Transaction::with($this->relation($request))->where('id', $id)->first();
        $ss = Status::where('id', $request->status)->first();

        DB::beginTransaction();
        try {
            $trx->status_id = (int)$request->status;
            $trx->updated_by = Auth::user()->id;
            $trx->updated_at = now();

            if (Auth::user()->roles[0]->id == "64132dec-322e-474a-a003-4c68ccc510fd") {
                $reason = (isset($request->reason)) ? $request->reason : "";
                $dm = [
                    'asm_approval_status_id' => (int)$request->approval_status,
                    'asm_user_id' => Auth::user()->id,
                    'asm_approved_at' => now(),
                    'asm_approval_notes' => $reason,
                    'updated_by' => Auth::user()->id,
                    'updated_at' => now()->format('Y-m-d H:i:s.uO')
                ];
                $trx->Approval()->where('transaction_id', $id)->update($dm);
            } else {
                $pro = new OutletProgress();
                $prog = $pro->where('id_map_outlet', $trx->DetailMandiri->destoutlet->mapoutlet->id)->where('status_progress', '3')->first();
                $outletProgress = $pro::find($prog->id);
                Log::info($outletProgress);
                if ((int)$request->status == 2) {
                    if (!$outletProgress->canApprove()) {
                        return response()->json(['status' => false, 'message' => 'Invalid Outlet Progress State', 'type' => 'error', 'title' => 'Change Status']);
                    }
                }

                if ((int)$request->status == 6) {
                    if (!$outletProgress->canReject()) {
                        return response()->json(['status' => false, 'message' => 'Invalid Outlet Progress State', 'type' => 'error', 'title' => 'Change Status']);
                    }
                }
                $now = Carbon::now();
                $outletProgressApprove = $outletProgress->replicate();
                if ((int)$request->status == 2) {
                    OutletProgress::where('id_map_outlet', $trx->DetailMandiri->destoutlet->mapoutlet->id)->update(['status_active' => 2]);
                    $prog_id = $outletProgressApprove->outlet()->city()->first()->id . strval($now->getTimestamp());
                    $outletProgressApprove->id = $prog_id;
                    $outletProgressApprove->section = 'uli';
                    $outletProgressApprove->status_progress = ((int)$request->status == 2) ? '5' : '4';
                    $outletProgressApprove->created_by = auth()->user()->id;
                    $outletProgressApprove->created_date = $now;
                    $outletProgressApprove->updated_by = auth()->user()->id;
                    $outletProgressApprove->created_at = $now->getTimestamp();
                    $outletProgressApprove->updated_at = $now->getTimestamp();
                    $outletProgressApprove->save();
                }

                $reason = (isset($request->reason)) ? $request->reason : "";
                $dm = [
                    'id' => Uuid::uuid4(),
                    'transaction_id' => $id,
                    'unilever_approval_status_id' => (int)$request->approval_status,
                    'unilever_user_id' => Auth::user()->id,
                    'unilever_approved_at' => now(),
                    'unilever_approval_notes' => $reason,
                    'asm_approval_status_id' => (int)$request->approval_status,
                    'asm_user_id' => "",
                    'asm_approved_at' => now()->format('Y-m-d H:i:s.uO'),
                    'asm_approval_notes' => "",
                    'created_by' => Auth::user()->id,
                    'created_at' => now()
                ];
                $trx->Approval()->insert($dm);
            }
            $hm = [
                'id' => Uuid::uuid4(),
                'transaction_id' => $id,
                'status_id' => $request->status,
                'created_at' => now()->format('Y-m-d H:i:s.uO'),
                'created_by' => Auth::user()->id,
            ];
            $trx->MandiriTimelines()->insert($hm);
            $trx->save();
            DB::commit();
            return response()->json(['status' => true, 'message' => 'Success change to status ' . $ss->name, 'type' => 'success', 'title' => 'Change Status']);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
            return response()->json(['status' => false, 'message' => $e->getMessage(), 'type' => 'error', 'title' => 'Change Status']);
        }
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
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $trx = Transaction::with($this->relation([]))->where('id', $id)->orderBy('created_at', 'DESC');
        $datas = $trx->first();
        if ($datas->DetailMandiri->ShippingMandiri()->where('shipping_type_id', 1)->count() > 0) {
            $asw_tarik = null;
            $asw_kirim = null;
            $img_tarik = array();
            $img_kirim = array();
            foreach ($datas->DetailMandiri->ShippingMandiri()->where('shipping_type_id', 1)->get() as $test) {
                if ($test->ShippingMandiriAnswer != null) {
                    $asw_tarik = $test->ShippingMandiriAnswer->answer;
                    $img_tarik[] = $test->ShippingMandiriAnswer->images;
                } else {
                    $asw_tarik = "";
                    $img_tarik = [];
                }
            }
            foreach ($datas->DetailMandiri->ShippingMandiri()->where('shipping_type_id', 2)->get() as $test) {
                if ($test->ShippingMandiriAnswer != null) {
                    $asw_kirim = $test->ShippingMandiriAnswer->answer;
                    $img_kirim[] = $test->ShippingMandiriAnswer->images;
                } else {
                    $asw_kirim = "";
                    $img_kirim = [];
                }
            }
            if ($datas->DetailMandiri->ShippingMandiri()->where('shipping_type_id', 1)->first()->ShippingMandiriAnswer != null) {
                if ($img_tarik != null) {
                    $tarik = json_decode($img_tarik[0]);
                    $kirim = json_decode($img_kirim[0]);
                    for ($i = 0; $i < count($tarik); $i++) {
                        $pictureOrg[] = $tarik[$i]->id;
                    }
                    for ($i = 0; $i < count($kirim); $i++) {
                        $pictureIds[] = $kirim[$i]->id;
                    }
                } else {
                    $pictureOrg = [];
                    $pictureIds = [];
                }
            } else {
                $pictureOrg = [];
                $pictureIds = [];
            }

        } else {
            $pictureOrg = [];
            $pictureIds = [];
        }
//        dd($asw_tarik);

        $data = [
            'data' => $datas,
            'survey_tarik' => $asw_tarik,
            'survey_kirim' => $asw_kirim,
            'pictureOrg' => $pictureOrg,
            'pictureDest' => $pictureIds
        ];
        return view('Warehouse/Cabinet/Self/show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
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

    public function StatusApprovalOutlet(Request $request)
    {
        $startDate = null;
        $endDate = null;
        $provinceId = null;
        $cityId = null;
        $id = null;
        $name = null;
        $outletName = null;
        $model = DB::table("v_jurgan_outlet_status");
        if ($request->query("provinces") != "") {
            $model = $model->where("id_province", '=', $request->query("provinces"));
            $provinceId = $request->query("provinces");
        }
        if ($request->query("cities") != "") {
            $model = $model->where("id_city", '=', $request->query("cities"));
            $cityId = $request->query("cities");
        }
        if ($request->query("juragan_id") != "") {
            $model = $model->where("juragan_id", '=', $request->query("juragan_id"));
            $id = $request->query("juragan_id");
        }
        if ($request->query("juragan_name") != "") {
            $model = $model->where("juragan", 'ilike', '%' . $request->query("juragan_name") . '%');
            $name = $request->query("juragan_name");
        }
        if ($request->query("outlet_name") != "") {
            $model = $model->where("outlet_name", 'ilike', '%' . $request->query("outlet_name") . '%');
            $outletName = $request->query("outlet_name");
        }

        if ($request->get("start_date") != "" || $request->get("start_date") != null) {
            $startDate = Carbon::parse($request->get("start_date"));
            $model = $model->where("outlet_created", '>=', $startDate->unix());
            $startDate = $startDate->unix();
        }

        if ($request->get("end_date") != "" || $request->get("end_date") != null) {
            $endDate = Carbon::parse($request->get("end_date"));
            $model = $model->where("outlet_created", '<=', $endDate->unix());
            $endDate = $endDate->unix();
        }
        if ($request->get("export") == "" || $request->get("export") == null) {
            $datas = [
                'datas' => $model->paginate(10),
            ];
            return view("Warehouse/Cabinet/Self/index", $datas);
        } else {
            $fileName = "TarikMandiri-" . Carbon::now()->unix() . ".xlsx";
            return Excel::download(new OutletStatusExport($startDate, $endDate, $provinceId, $cityId, $id, $name, $outletName), $fileName);
        }

    }

    public function export()
    {
        $request = Request::capture();
        $search = $request->query('search', '');
        $startDate = $request->query('fromDate', '');
        $endDate = $request->query('toDate', '');
        // dd(file_get_contents('http://127.0.0.1:8000/juragan/tukar_mandiri?' . http_build_query(['search' => $search, 'from_date' => $startDate, 'to_date' => $endDate])));
        $file_name = sprintf('juragan_tukar_mandiri-%s.xlsx', Str::uuid()->toString());
        return response()->streamDownload(function () use ($search, $startDate, $endDate) {
            echo file_get_contents('http://127.0.0.1:8000/juragan/tukar_mandiri?' . http_build_query(['search' => $search, 'from_date' => $startDate, 'to_date' => $endDate]));
        }, $file_name);
        
        // return Excel::download(new OutletTukarMandiri($search, $startDate, $endDate), $file_name);
        
    }

    public function exportActivity()
    {
        $request = Request::capture();
        $search = $request->query('search', '');
        $startDate = $request->query('fromDate', '');
        $endDate = $request->query('toDate', '');
        $file_name = sprintf('unilever-activity-%s.xlsx', Str::uuid()->toString());
        return response()->streamDownload(function () use ($search, $startDate, $endDate) {
            echo file_get_contents('http://127.0.0.1:8000/unilever/activity?' . http_build_query(['search' => $search, 'from_date' => $startDate, 'to_date' => $endDate]));
        }, $file_name);
    }
}
