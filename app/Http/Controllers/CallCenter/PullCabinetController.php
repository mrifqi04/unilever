<?php

namespace App\Http\Controllers\CallCenter;


use App\export\OutletRetractionUnileverIdNullExport;
use App\Http\Controllers\GenericController;
use App\Models\City;
use App\Models\District;
use App\Models\JuraganManagement\Juragan;
use App\Models\OutletManagement\Outlet;
use App\Models\OutletManagement\RetractionRejectReason as RejectReason;
use App\Models\Province;
use App\Models\CallCenter\OutletRetractionProgress;
use App\Models\Village;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class PullCabinetController extends GenericController
{

    public function index()
    {
        $request = Request::capture();
        $auth = Auth::user();
        $page = $request->query('page', '');
        $pageSize = 30;
        $idProvince = $request->query('id_province', '');
        $idCity = $request->query('id_city', '');
        $idDistrict = $request->query('id_district', '');
        $idVillage = $request->query('id_village', '');
        $idJuragan = $request->query('id_juragan', '');
        $idOutlet = $request->query('id_outlet', '');
        $idProgress = $request->query('id_progress', '');
        $canShow = $auth->getPermissionByName('callcenter.pull-cabinet.show');
        $canApprove = $auth->getPermissionByName('callcenter.pull-cabinet.approve');
        $canPostpone = $auth->getPermissionByName('callcenter.pull-cabinet.postpone');
        $canCancel = $auth->getPermissionByName('callcenter.pull-cabinet.cancel');
        $canExportOutlet = $auth->getPermissionByName('callcenter.pull-cabinet.export-outlet');
        $query1 = DB::table("v_retractions")->where(function ($query) {
            $query->whereRaw("section = 'uli'")->whereRaw("status_progress = '1'");
        });
        $query2 = DB::table("v_retractions")->whereRaw("section NOT IN ('partner', 'juragan', 'uli')")->union($query1);
        $query = DB::table(DB::raw("({$query2->toSql()}) as v_retractions"));
        if ($idProvince != '') {
            $query->whereRaw("id_province = '" . $idProvince . "'");
        }
        if ($idCity != '') {
            $query->whereRaw("id_city = '" . $idCity . "'");
        }

        if ($idJuragan != '') {
            $query->whereRaw("juragan_id = '" . $idJuragan . "'");
        }
        if ($idOutlet != '') {
            $query->whereRaw("outlet_id = '" . $idOutlet . "'");
        }
        if ($idProgress != '') {
            list($section, $idStatusProgress) = explode('#', $idProgress);
            $query->whereRaw("status_progress = '" . $idStatusProgress . "'");
            $query->whereRaw("section = '" . $section . "'");
        }
        
        $data = $query->paginate($pageSize, ['*'], 'page', $page);

        $provinces = Province::select(['id', 'name'])
            ->where('id_country', '01')
            ->where('is_deleted', 1)
            ->get()
            ->pluck('name', 'id');
        $cities = [];
        if ($idProvince != '') {
            $cities = City::select(['id', 'name'])
                ->where('id_province', $idProvince)
                ->where('is_deleted', 1)
                ->get()
                ->pluck('name', 'id');
        }
        $districts = [];
        if ($idCity != '') {
            $districts = District::select(['id', 'name'])
                ->where('id_city', $idCity)
                ->where('is_deleted', 1)
                ->get()
                ->pluck('name', 'id');
        }
        $villages = [];
        if ($idDistrict != '') {
            $villages = Village::select(['id', 'name'])
                ->where('id_district', $idDistrict)
                ->where('is_deleted', 1)
                ->get()
                ->pluck('name', 'id');
        }
        $juragans = Juragan::select(['id', 'name'])
            ->get()
            ->pluck('name', 'id');
        $outlets = Outlet::select(['id', 'name'])
            ->get()
            ->pluck('name', 'id');
        $rejectReasons = json_encode(RejectReason::select(['id', 'name'])
            ->get()->pluck('name', 'id'), true);
        $progressClass = [
            'uli#1'        => 'info',
            'callcenter#3' => 'primary',
            'callcenter#4' => 'danger',
            'callcenter#1' => 'info',
            'driver#7'     => 'purple',
            'driver#4'     => 'danger',
            'driver#3'     => 'primary',
            'driver#6'     => 'warning',
        ];
        $progress = [
            'uli#1'        => 'CMS ULI - Approve',
            'callcenter#3' => 'Call Center - Tunda',
            'callcenter#4' => 'Call Center - Cancel',
            'callcenter#1' => 'Call Center - Approve',
            'driver#7'     => 'Sedang Ditarik',
            'driver#4'     => 'Driver - Cancel',
            'driver#3'     => 'Driver - Postpone',
            'driver#6'     => 'Driver - Terkirim',
        ];
        $inSearch = count($request->query()) > 0;
        
        return view("CallCenter/PullCabinet/index")
            ->with('in_search', $inSearch)
            ->with('canShow', $canShow)
            ->with('canApprove', $canApprove)
            ->with('canCancel', $canCancel)
            ->with('canPostpone', $canPostpone)
            ->with('canExportOutlet', $canExportOutlet)
            ->with('provinces', $provinces)
            ->with('cities', $cities)
            ->with('districts', $districts)
            ->with('villages', $villages)
            ->with('juragans', $juragans)
            ->with('outlets', $outlets)
            ->with('progress', $progress)
            ->with('progressClass', $progressClass)
            ->with('data', $data)
            ->with('id_province', $idProvince)
            ->with('id_city', $idCity)
            ->with('id_district', $idDistrict)
            ->with('id_village', $idVillage)
            ->with('id_juragan', $idJuragan)
            ->with('id_outlet', $idOutlet)
            ->with('id_progress', $idProgress)
            ->with('rejectReasons', $rejectReasons);
    }

    public function show($id)
    {
        $auth = Auth::user();
        $canUpdate = $auth->getPermissionByName('callcenter.pull-cabinet.update');
        $canShow = $auth->getPermissionByName('callcenter.pull-cabinet.show');
        $canApprove = $auth->getPermissionByName('callcenter.pull-cabinet.approve');
        $canCancel = $auth->getPermissionByName('callcenter.pull-cabinet.cancel');
        $canPostpone = $auth->getPermissionByName('callcenter.pull-cabinet.postpone');

        $where = '';
        if (($juraganMappings = $this->getJuraganMappings()) !== null) {
            $where .= " AND o.id_juragan IN ('". implode("','", $juraganMappings). "')";
        }

        $sql = "
            SELECT
              orp.id as id,
              orp.signature_image_id,
              o.id as outlet_id,
              orp.reject_reason_id,
              orp.section,
              orp.status_progress,
              ha.id as hunter_id,
              ha.answers,
              ha.images as answer_images
            FROM outlet.outlet_retraction_progress AS orp
            INNER JOIN outlet.map_outlet AS mo ON orp.id_map_outlet = mo.id
            INNER JOIN outlet.outlet AS o ON mo.id_outlet = o.id
            INNER JOIN hunter.answers AS ha ON orp.id_answer = ha.id
            WHERE
              orp.id = ? AND
              orp.status_active = 1 AND
              orp.is_deleted = 1
              $where
            LIMIT 1
        ";
        $outletRetractionProgress = collect(DB::select(DB::raw($sql), [$id]))->first();
        if (!is_null($outletRetractionProgress)) {
            $survey = json_decode($outletRetractionProgress->answers);
        } else {
            $this->flashMessage('error', 'Approve Outlet', 'Invalid Outlet Survey');
            return redirect()
                ->route('callcenter.pull-cabinet.index');
        }
        $outlet = Outlet::with(["mapOutlet", "juragan", "province", "city", "district", "village", "StatusType", "OwnershipStatus", "StreetType"])
            ->find($outletRetractionProgress->outlet_id);
        $pictureIds = OutletRetractionProgress::getPictureIds($outletRetractionProgress->answer_images, $outletRetractionProgress->signature_image_id)->map(function ($item) {
            return $item->id;
        });
        $rejectReasons = json_encode(RejectReason::select(['id', 'name'])
            ->get()->pluck('name', 'id'), true);
        return view("CallCenter/PullCabinet/show")
            ->with('canUpdate', $canUpdate)
            ->with('canShow', $canShow)
            ->with('canApprove', $canApprove)
            ->with('canCancel', $canCancel)
            ->with('canPostpone', $canPostpone)
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
                ->route('callcenter.pull-cabinet.index');
        }
        $outletRetractionProgress = OutletRetractionProgress::find($id);
        if (!$outletRetractionProgress->canApprove()) {
            $this->flashMessage('error', 'Approve Outlet', 'Invalid Outlet Progress State');
            return redirect()
                ->route('callcenter.pull-cabinet.index');
        }
        DB::beginTransaction();
        try {
            $now = Carbon::now();
            $outletRetractionProgressApprove = $outletRetractionProgress->replicate();
            $id = $outletRetractionProgressApprove->outlet()->city()->first()->id . strval($now->getTimestamp());
            $outletRetractionProgressApprove->id = $id;
            $outletRetractionProgressApprove->send_date = $outletRetractionProgressApprove->recommend_date;
            $outletRetractionProgressApprove->status_progress = '1';
            $outletRetractionProgressApprove->section = 'callcenter';
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
                ->route('callcenter.pull-cabinet.index');
        }
        $this->flashMessage('success', 'Approve Outlet', 'Approve Outlet Success');
        return redirect()
            ->route('callcenter.pull-cabinet.index');
    }

    /**
     * Cancel the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function cancel(Request $request, $id)
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
            $this->flashMessage('error', 'Cancel Outlet', $validator->errors()->first());
            return redirect()
                ->route('callcenter.pull-cabinet.index');
        }
        $outletRetractionProgress = OutletRetractionProgress::find($id);
        if (!$outletRetractionProgress->canCancel()) {
            $this->flashMessage('error', 'Cancel Outlet', 'Invalid Outlet Progress State');
            return redirect()
                ->route('callcenter.pull-cabinet.index');
        }
        DB::beginTransaction();
        try {
            $now = Carbon::now();
            $outletRetractionProgressApprove = $outletRetractionProgress->replicate();
            $id = $outletRetractionProgressApprove->outlet()->city()->first()->id . $now->getTimestamp();
            $outletRetractionProgressApprove->id = $id;
            $outletRetractionProgressApprove->status_progress = '4';
            $outletRetractionProgressApprove->section = 'callcenter';
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
            $this->flashMessage('error', 'Cancel Outlet', 'Cancel Outlet Error');
            return redirect()
                ->route('callcenter.pull-cabinet.index');
        }
        $this->flashMessage('success', 'Cancel Outlet', 'Cancel Outlet Success');
        return redirect()
            ->route('callcenter.pull-cabinet.index');
    }

    /**
     * Postpone the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function postpone(Request $request, $id)
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
            $this->flashMessage('error', 'Tunda Outlet', $validator->errors()->first());
            return redirect()
                ->route('callcenter.pull-cabinet.index');
        }
        $outletRetractionProgress = OutletRetractionProgress::find($id);
        if (!$outletRetractionProgress->canPostpone()) {
            $this->flashMessage('error', 'Tunda Outlet', 'Invalid Outlet Progress State');
            return redirect()
                ->route('callcenter.pull-cabinet.index');
        }
        DB::beginTransaction();
        try {
            $now = Carbon::now();
            $outletRetractionProgressApprove = $outletRetractionProgress->replicate();
            $id = $outletRetractionProgressApprove->outlet()->city()->first()->id . $now->getTimestamp();
            $outletRetractionProgressApprove->id = $id;
            $outletRetractionProgressApprove->status_progress = '3';
            $outletRetractionProgressApprove->section = 'callcenter';
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
            $this->flashMessage('error', 'Tunda Outlet', 'Tunda Outlet Error');
            return redirect()
                ->route('callcenter.pull-cabinet.index');
        }
        $this->flashMessage('success', 'Tunda Outlet', 'Tunda Outlet Success');
        return redirect()
            ->route('callcenter.pull-cabinet.index');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make(
            ($request->all() + ['id' => $id]),
            [
                'id'            => 'required|exists:App\Models\Unilever\OutletRetractionProgress,id,is_deleted,1',
                'address_by_cc' => 'required',
                'phone_by_cc'   => 'required',
            ]
        );
        if ($validator->fails()) {
            $this->flashMessage('error', 'Save Outlet', $validator->errors()->first());
            return redirect()
                ->route('callcenter.pull-cabinet.show', ['id' => $id])
                ->withInput($request->all());
        }
        $outletRetractionProgress = OutletRetractionProgress::find($id);
        DB::beginTransaction();
        try {
            $outlet = $outletRetractionProgress->outlet();
            $outlet->address_by_cc = $request->get('address_by_cc', null);
            $outlet->phone_by_cc = $request->get('phone_by_cc', null);
            $outlet->updated_by = auth()->user()->id;
            $outlet->updated_at = Carbon::now()->getTimestamp();
            $outlet->save();
            DB::commit();
        } catch (\Exception $e) {
            Log::error($e);
            DB::rollBack();
            $this->flashMessage('error', 'Save Outlet', 'Save Outlet Error');
            return redirect()
                ->route('callcenter.pull-cabinet.index');
        }
        $this->flashMessage('success', 'Save Outlet', 'Save Outlet Success');
        return redirect()
            ->route('callcenter.pull-cabinet.index');
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
                ->route('callcenter.pull-cabinet.index');
        }
        $fileName = Str::uuid()->toString() . '.xlsx';
        return Excel::download(new OutletRetractionUnileverIdNullExport(), $fileName);
    }

    protected function getJuraganMappings() {
        $auth = Auth::user();
        if ($auth->roles->where('name', 'Admin Call Center')->isNotEmpty()) {
            $warehouse  = WarehouseManagement::where('id_warehouse_admins', 'ilike', '%'.$auth->id.'%')->with('juragans')->get();
            $juragans   = $warehouse->pluck('juragans')->flatten();
            $idJuragans = $juragans->pluck('id_juragan_mappings')->flatten()->implode(',');
            return ($idJuragans) ? explode(',', $idJuragans) : []; 
        }
        return;
    }
}
