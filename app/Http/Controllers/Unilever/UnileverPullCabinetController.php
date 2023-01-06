<?php

namespace App\Http\Controllers\Unilever;


use App\export\OutletRetractionUnileverIdNullExport;
use App\Http\Controllers\GenericController;
use App\Models\City;
use App\Models\District;
use App\Models\JuraganManagement\Juragan;
use App\Models\OutletManagement\Outlet;
use App\Models\OutletManagement\RetractionRejectReason as RejectReason;
use App\Models\Province;
use App\Models\Unilever\OutletRetractionProgress;
use App\Models\Village;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class UnileverPullCabinetController extends GenericController
{

    public function index()
    {
        $request = Request::capture();
        $auth = Auth::user();
        $canShow = $auth->getPermissionByName('unilever.pull-cabinet.show');
        $canApprove = $auth->getPermissionByName('unilever.pull-cabinet.approve');
        $canReject = $auth->getPermissionByName('unilever.pull-cabinet.reject');
        $canApproveBulk = $auth->getPermissionByName('unilever.pull-cabinet.approve-bulk');
        $canRejectBulk = $auth->getPermissionByName('unilever.pull-cabinet.reject-bulk');
        $canChangeDeliveryDate = $auth->getPermissionByName('unilever.pull-cabinet.change-delivery-date');
        $canExportOutlet = $auth->getPermissionByName('unilever.pull-cabinet.export-outlet');
        
        $provinces = Province::select(['id', 'name'])
            ->where('id_country', '01')
            ->where('is_deleted', 1)
            ->get()
            ->pluck('name', 'id');
        $juragans = Juragan::select(['id', 'name'])
            ->get()
            ->pluck('name', 'id');
        $outlets = Outlet::select(['id', 'name'])
            ->get()
            ->pluck('name', 'id');
        $rejectReasons = json_encode(RejectReason::select(['id', 'name'])
            ->get()->pluck('name', 'id'), true);

        $progressClass = [
            'partner#1'    => 'success',
            'uli#1'        => 'info',
            'uli#2'        => 'danger',
            'callcenter#3' => 'primary',
            'callcenter#4' => 'danger',
            'callcenter#1' => 'info',
            'driver#7'     => 'purple',
            'driver#4'     => 'danger',
            'driver#3'     => 'primary',
            'driver#6'     => 'warning',
        ];
        $progress = [
            'partner#1'    => 'Approve Partner',
            'uli#1'        => 'CMS ULI - Approve',
            'uli#2'        => 'CMS ULI - Reject',
            'callcenter#3' => 'Call Center - Tunda',
            'callcenter#4' => 'Call Center - Reject',
            'callcenter#1' => 'Call Center - Approve',
            'driver#7'     => 'Sedang Ditarik',
            'driver#4'     => 'Driver - Cancel',
            'driver#3'     => 'Driver - Postpone',
            'driver#6'     => 'Driver - Terkirim',
        ];
        
        if ($request->get('get_records', 0) == 1) {
            return $this->getRecords(compact('progress', 'progressClass'));
        }

        $inSearch = count($request->query()) > 0;
        return view("Unilever/PullCabinet/index")
            ->with('in_search', $inSearch)
            ->with('canShow', $canShow)
            ->with('canApprove', $canApprove)
            ->with('canReject', $canReject)
            ->with('canApproveBulk', $canApproveBulk)
            ->with('canRejectBulk', $canRejectBulk)
            ->with('canChangeDeliveryDate', $canChangeDeliveryDate)
            ->with('canExportOutlet', $canExportOutlet)
            ->with('provinces', $provinces)
            ->with('juragans', $juragans)
            ->with('outlets', $outlets)
            ->with('progress', $progress)
            ->with('progressClass', $progressClass)
            ->with('rejectReasons', $rejectReasons);
    }

    private function getRecords($parent) {
        $request   = Request::capture();
        $draw      = $request->query('draw', '');
        $start     = $request->query('start', 1);
        $pageSize  = $request->query('length', 10);
        if (!is_numeric($start)) $start = 1;
        if (!is_numeric($pageSize)) $pageSize = 10;
        $page      = ($start > 0) ? ($start / $pageSize + 1) : 1;
        $keyword   = $request->get('search')['value'];
        $getOrder  = $request->get('order', []);
        $listOrder = ['id', 'city', 'id_leveredge', 'juragan', 'outlet_id', 'csdp', 'outlet_name', 'owner', 'address', 'phone', 'status_progress', 'created_date', 'send_date'];
        $order     = $listOrder[$getOrder[0]['column']];
        $orderType = $getOrder[0]['dir'] ? $getOrder[0]['dir'] : 'asc';

        $query1 = DB::table("v_retractions")->where(function ($query) {
            $query->whereRaw("section = 'partner'")->whereRaw("status_progress = '1'");
        });
        $query2 = DB::table("v_retractions")->whereRaw("section NOT IN ('partner', 'juragan')")->union($query1);
        $query = DB::table(DB::raw("({$query2->toSql()}) as v_retractions"));
        
        $total       = $query->count();
        $totalFilter = $total;

        // FILTER
        $isSearching = false;
        $idProvince = $request->query('id_province', '');
        $idCity = $request->query('id_city', '');
        $idDistrict = $request->query('id_district', '');
        $idVillage = $request->query('id_village', '');
        $idJuragan = $request->query('id_juragan', '');
        $idOutlet = $request->query('id_outlet', '');
        $idProgress = $request->query('id_progress', '');
        $csdp = $request->query('csdp', '');
        $sendDate = $request->query('send_date', '');
        if ($idProvince != '') {
            $query->whereRaw("id_province = '" . $idProvince . "'");
            $isSearching = true;
        }
        if ($idCity != '') {
            $query->whereRaw("id_city = '" . $idCity . "'");
            $isSearching = true;
        }

        if ($idJuragan != '') {
            $query->whereRaw("juragan_id = '" . $idJuragan . "'");
            $isSearching = true;
        }
        if ($idOutlet != '') {
            $query->whereRaw("outlet_id = '" . $idOutlet . "'");
            $isSearching = true;
        }
        if ($csdp != '') {
            $query->whereRaw("csdp = '" . $csdp . "'");
            $isSearching = true;
        }
        if ($sendDate != '') {
            $query->whereRaw("date(send_date) = '" . $sendDate . "'");
            $isSearching = true;
        }
        if ($idProgress != '') {
            list($section, $idStatusProgress) = explode('#', $idProgress);
            $query->whereRaw("status_progress = '" . $idStatusProgress . "'");
            $query->whereRaw("section = '" . $section . "'");
            $isSearching = true;
        }
        
        if ($isSearching == true) $totalFilter = $query->count();

        $data = $query->orderBy($order, $orderType)->paginate($pageSize, ['*'], 'page', $page);
        
        $ret['draw'] = $draw;
        $ret['recordsTotal'] = $total;
        $ret['recordsFiltered'] = $totalFilter;
        $ret['data'] = $data->map(function($value) use ($parent) {
            $value->created_date_format = Carbon::parse($value->created_date)->format('Y-m-d');
            $value->send_date_format = Carbon::parse($value->send_date)->format('Y-m-d');
            $value->status_class = @$parent['progressClass'][$value->section.'#'.$value->status_progress] ? $parent['progressClass'][$value->section.'#'.$value->status_progress] : 'inverse';
            $value->status = @$parent['progress'][$value->section.'#'.$value->status_progress];
            return $value;
        });

        return response()->json($ret);
    }

    public function show($id)
    {
        $auth = Auth::user();
        $canShow = $auth->getPermissionByName('unilever.pull-cabinet.show');
        $canApprove = $auth->getPermissionByName('unilever.pull-cabinet.approve');
        $canReject = $auth->getPermissionByName('unilever.pull-cabinet.reject');
        $canChangeDeliveryDate = $auth->getPermissionByName('unilever.pull-cabinet.change-delivery-date');
        
        $sql = '
            SELECT
              orp.id as id,
              o.id as outlet_id,
              orp.reject_reason_id,
              orp.section,
              ha.id as hunter_id,
              ha.answers
            FROM outlet.outlet_retraction_progress AS orp
            INNER JOIN outlet.map_outlet AS mo ON orp.id_map_outlet = mo.id
            INNER JOIN outlet.outlet AS o ON mo.id_outlet = o.id
            INNER JOIN hunter.answers AS ha ON orp.id_answer = ha.id
            WHERE
              orp.id = ? AND
              orp.status_active = 1 AND
              orp.is_deleted = 1
            LIMIT 1
        ';
        $outletRetractionProgress = collect(DB::select(DB::raw($sql), [$id]))->first();
        if (!is_null($outletRetractionProgress)) {
            $survey = json_decode($outletRetractionProgress->answers);
        } else {
            $this->flashMessage('error', 'Approve Outlet', 'Invalid Outlet Survey');
            return redirect()
            ->route('unilever.pull-cabinet.index');
        }
        $outlet = Outlet::with(["mapOutlet", "juragan", "province", "city", "district", "village", "StatusType", "OwnershipStatus", "StreetType"])
            ->find($outletRetractionProgress->outlet_id);
        $pictureIds = OutletRetractionProgress::getPictureIds($outletRetractionProgress->answers)->map(function ($item) {
            return $item->id;
        });
        $rejectReasons = json_encode(RejectReason::select(['id', 'name'])
            ->get()->pluck('name', 'id'), true);
        return view("Unilever/PullCabinet/show")
            ->with('canShow', $canShow)
            ->with('canApprove', $canApprove)
            ->with('canReject', $canReject)
            ->with('canChangeDeliveryDate', $canChangeDeliveryDate)
            ->with('data', $outlet)
            ->with('outletRetractionProgress', $outletRetractionProgress)
            ->with('survey', $survey)
            ->with('pictureIds', $pictureIds)
            ->with('rejectReasons', $rejectReasons);
    }

    /**
     * Approve the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function approve(Request $request, $id)
    {
        $validator = Validator::make(
            [
                'id' => $id,
            ],
            [
                'id' => 'required|exists:App\Models\Unilever\OutletRetractionProgress,id,is_deleted,1',
            ]
        );
        if ($validator->fails()) {
            $this->flashMessage('error', 'Approve Outlet', $validator->errors()->first());
            return redirect()
                ->route('unilever.pull-cabinet.index');
        }
        $outletRetractionProgress = OutletRetractionProgress::find($id);
        if (!$outletRetractionProgress->canApprove()) {
            $this->flashMessage('error', 'Approve Outlet', 'Invalid Outlet Progress State');
            return redirect()
                ->route('unilever.pull-cabinet.index');
        }
        DB::beginTransaction();
        try {
            $now = Carbon::now();
            $outletRetractionProgressApprove = $outletRetractionProgress->replicate();
            $id = $outletRetractionProgressApprove->outlet()->city()->first()->id . strval($now->getTimestamp());
            $outletRetractionProgressApprove->id = $id;
            $outletRetractionProgressApprove->send_date = $outletRetractionProgressApprove->recommend_date;
            $outletRetractionProgressApprove->status_progress = '1';
            $outletRetractionProgressApprove->section = 'uli';
            $outletRetractionProgressApprove->created_by = auth()->user()->id;
            $outletRetractionProgressApprove->created_date = $now;
            $outletRetractionProgressApprove->updated_by = auth()->user()->id;
            $outletRetractionProgressApprove->created_at = $now->getTimestamp();
            $outletRetractionProgressApprove->updated_at = $now->getTimestamp();
            $outletRetractionProgressApprove->save();
            $outletRetractionProgress->status_active = 2;
            $outletRetractionProgress->updated_by = auth()->user()->id;
            $outletRetractionProgress->updated_at = Carbon::now()->getTimestamp();
            $outletRetractionProgress->save();
            DB::commit();
        } catch (\Exception $e) {
            Log::error($e);
            DB::rollBack();
            $this->flashMessage('error', 'Approve Outlet', 'Approve Outlet Error');
            return redirect()
                ->route('unilever.pull-cabinet.index');
        }
        $this->flashMessage('success', 'Approve Outlet', 'Approve Outlet Success');
        return redirect()
            ->route('unilever.pull-cabinet.index');
    }

    /**
     * Reject the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function reject(Request $request, $id)
    {
        $rejectReasonId = $request->get('reject_reason_id', null);
        $validator = Validator::make(
            [
                'id'               => $id,
                'reject_reason_id' => $rejectReasonId
            ],
            [
                'id'               => 'required|exists:App\Models\Unilever\OutletRetractionProgress,id,is_deleted,1',
                'reject_reason_id' => 'required'
            ]
        );
        if ($validator->fails()) {
            $this->flashMessage('error', 'Reject Outlet', $validator->errors()->first());
            return redirect()
                ->route('unilever.pull-cabinet.index');
        }
        $outletRetractionProgress = OutletRetractionProgress::find($id);
        if (!$outletRetractionProgress->canReject()) {
            $this->flashMessage('error', 'Reject Outlet', 'Invalid Outlet Progress State');
            return redirect()
                ->route('unilever.pull-cabinet.index');
        }
        DB::beginTransaction();
        try {
            $now = Carbon::now();
            $outletRetractionProgressApprove = $outletRetractionProgress->replicate();
            $id = $outletRetractionProgressApprove->outlet()->city()->first()->id . $now->getTimestamp();
            $outletRetractionProgressApprove->id = $id;
            $outletRetractionProgressApprove->status_progress = '2';
            $outletRetractionProgressApprove->section = 'uli';
            $outletRetractionProgressApprove->reject_reason_id = $rejectReasonId;
            $outletRetractionProgressApprove->created_by = auth()->user()->id;
            $outletRetractionProgressApprove->created_date = $now;
            $outletRetractionProgressApprove->updated_by = auth()->user()->id;
            $outletRetractionProgressApprove->created_at = $now->getTimestamp();
            $outletRetractionProgressApprove->updated_at = $now->getTimestamp();
            $outletRetractionProgressApprove->save();
            $outletRetractionProgress->status_active = 2;
            $outletRetractionProgress->updated_by = auth()->user()->id;
            $outletRetractionProgress->updated_at = Carbon::now()->getTimestamp();
            $outletRetractionProgress->save();
            DB::commit();
        } catch (\Exception $e) {
            Log::error($e);
            DB::rollBack();
            $this->flashMessage('error', 'Reject Outlet', 'Reject Outlet Error');
            return redirect()
                ->route('unilever.pull-cabinet.index');
        }
        $this->flashMessage('success', 'Reject Outlet', 'Reject Outlet Success');
        return redirect()
            ->route('unilever.pull-cabinet.index');
    }

    /**
     * Change Delivery Date the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function changeDeliveryDate(Request $request, $id)
    {
        $sendDate = $request->input('send_date');
        $validator = Validator::make(
            [
                'id' => $id,
                'send_date' => $sendDate,
            ],
            [
                'id' => 'required|exists:App\Models\Unilever\OutletRetractionProgress,id,is_deleted,1',
                'send_date' => 'required|date_format:Y-m-d',
            ]
        );
        if ($validator->fails()) {
            $this->flashMessage('error', 'Change Delivery Date', $validator->errors()->first());
            return redirect()
                ->route('unilever.pull-cabinet.index');
        }
        $outletRetractionProgress = OutletRetractionProgress::find($id);
        if (!$outletRetractionProgress->canChangeDeliveryDate()) {
            $this->flashMessage('error', 'Change Delivery Date', 'Invalid Outlet Progress State');
            return redirect()
                ->route('unilever.pull-cabinet.index');
        }
        $now = Carbon::now();
        $outletRetractionProgress->send_date = Carbon::parse($sendDate);
        $outletRetractionProgress->updated_by = auth()->user()->id;
        $outletRetractionProgress->updated_at = $now->getTimestamp();
        $outletRetractionProgress->save();
        $this->flashMessage('success', 'Change Delivery Date', 'Change Delivery Date Success');
        return redirect()
            ->route('unilever.pull-cabinet.index');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportOutlet()
    {
        $outletRetractionUnileverIdNullExport = new OutletRetractionUnileverIdNullExport();
        if ($outletRetractionUnileverIdNullExport->collection()->count() < 1) {
            $this->flashMessage('info', 'Export Outlet', 'Outlet Data Is Empty');
            return redirect()
                ->route('unilever.pull-cabinet.index');
        }
        $fileName = Str::uuid()->toString() . '.xlsx';
        return Excel::download(new OutletRetractionUnileverIdNullExport(), $fileName);
    }
}