<?php

namespace App\Http\Controllers\Unilever;


use App\export\OutletUnileverIdNullExport;
use App\export\OutletCSDPExport;
use App\Http\Controllers\GenericController;
use App\Models\City;
use App\Models\District;
use App\Models\JuraganManagement\Juragan;
use App\Models\UserManagement\Role;
use App\Models\Warehouse\WarehouseManagement;
use App\Models\WarehouseMapping\JuraganToWarehouseManagement;
use App\Models\OutletManagement\Outlet;
use App\Models\Province;
use App\Models\Scheduler\UnileverOutletJob;
use App\Models\Unilever\OutletProgress;
use App\Models\Village;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Session;

class UnileverController extends GenericController
{

    public function index()
    {
        $authId = Auth::id();
        $getUserRole = Auth::user()->with('roles')->where('id', $authId)->first();
        $super_admin = $getUserRole->roles[0];
        $juraganToWarehouse = JuraganToWarehouseManagement::join('warehouse.warehouse_managements', 'juragan.juragan_to_warehouses.id_warehouse_management', 'warehouse.warehouse_managements.id')
            ->where('juragan.juragan_to_warehouses.is_deleted', 1)
            ->select('warehouse.warehouse_managements.id as wid', 'warehouse.warehouse_managements.warehouse_name', 'warehouse.warehouse_managements.id_warehouse_admins', 'juragan.juragan_to_warehouses.*')
            ->get();
        $is_selected_admin = false;
        $current_juragan = [];
        foreach ($juraganToWarehouse as $juragan) {
            $listAdmins =  explode(",", $juragan['id_warehouse_admins']);
            $current_juragan =  explode(",", $juragan['id_juragan_mappings']);
            foreach ($listAdmins as $listAdmin) {
                $listAdmin = trim($listAdmin);
                $contains = Str::contains($listAdmin, [$authId]);
                if ($contains) {
                    $is_selected_admin = true;
                    break;
                }
            }
            if ($is_selected_admin) {
                break;
            }
        }
        $request = Request::capture();
        $idProvince = $request->query('id_province', '');
        $idCity = $request->query('id_city', '');
        $idDistrict = $request->query('id_district', '');
        $idVillage = $request->query('id_village', '');
        $idJuragan = $request->query('id_juragan', '');
        $idOutlet = $request->query('id_outlet', '');
        $idProgress = $request->query('id_progress', '');
        $canShow = Auth::user()->getPermissionByName('unilever.show');
        $canApprove = Auth::user()->getPermissionByName('unilever.approve');
        $canReject = Auth::user()->getPermissionByName('unilever.reject');
        $canApproveBulk = Auth::user()->getPermissionByName('unilever.approve-bulk');
        $canRejectBulk = Auth::user()->getPermissionByName('unilever.reject-bulk');
        $canChangeDeliveryDate = Auth::user()->getPermissionByName('unilever.changeDeliveryDate');
        $canExportOutlet = Auth::user()->getPermissionByName('unilever.export-outlet');
        $query = DB::table("v_requests");
        if ($idProvince != '') {
            $query->where('id_province', '=', $idProvince);
        }
        if ($idCity != '') {
            $query->where('id_city', '=', $idCity);
        }
        //        if ($idDistrict != '') {
        //            $query->where('id_district', '=', $idDistrict);
        //        }
        //        if ($idVillage != '') {
        //            $query->where('id_village', '=', $idVillage);
        //        }
        if ($idJuragan != '') {
            $query->where('juragan_id', '=', $idJuragan);
        }
        if ($idOutlet != '') {
            $query->where('outlet_id', '=', $idOutlet);
        }
        if ($idProgress != '') {
            $query->where('status_progress', '=', $idProgress);
        }
        Session::put('filter_data', $request->query());

        if (($is_selected_admin == true)) {
            $data = $query->whereIn('juragan_id', $current_juragan)->paginate(30);
        } elseif (($is_selected_admin == false) && ($super_admin->name == 'SUPER ADMIN')) {
            $data = $query->paginate(30);
        } elseif (($is_selected_admin == false) && ($super_admin->name == 'Admin Warehouse')) {
            $data = '';
        } else {
            $data = '';
        }
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
        $progressClass = [
            '1' => 'default',
            '2' => 'primary',
            '3' => 'success',
            '4' => 'danger',
            '5' => 'info',
            '6' => 'warning',
            '7' => 'purple',
        ];
        $progress = [
            '1' => 'Deal',
            '2' => 'Tunda',
            '3' => 'Approve Juragan',
            '4' => 'Batal',
            '5' => 'Approve ULI',
            '6' => 'Terkirim',
            '7' => 'Sedang Terkirim',
        ];
        $inSearch = count($request->query()) > 0;

        if ($request->get('get_records', 0) == 1) {
            return $this->getRecords(compact(
                'progress',
                'progressClass',
                'is_selected_admin',
                'super_admin',
                'current_juragan'
            ));
        }
        return view("Unilever/index")
            ->with('in_search', $inSearch)
            ->with('canShow', $canShow)
            ->with('canApprove', $canApprove)
            ->with('canReject', $canReject)
            ->with('canApproveBulk', $canApproveBulk)
            ->with('canRejectBulk', $canRejectBulk)
            ->with('canChangeDeliveryDate', $canChangeDeliveryDate)
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
            ->with('id_progress', $idProgress);
    }

    private function getRecords($parent)
    {
        $request   = Request::capture();
        $draw      = $request->query('draw', '');
        $start     = $request->query('start', 1);
        $pageSize  = $request->query('length', 10);
        if (!is_numeric($start)) $start = 1;
        if (!is_numeric($pageSize)) $pageSize = 10;
        $page      = ($start > 0) ? ($start / $pageSize + 1) : 1;
        $keyword   = $request->get('search')['value'];
        $getOrder  = $request->get('order', []);
        $listOrder = ['id', 'city', 'id_leveredge', 'juragan', 'outlet_id', 'csdp', 'outlet_name', 'owner', 'address', 'phone', 'status_progress', 'created_date', 'recommend_date', 'send_date'];
        $order     = $listOrder[$getOrder[0]['column']];
        $orderType = $getOrder[0]['dir'] ? $getOrder[0]['dir'] : 'asc';

        $query = DB::table("v_requests");

        if (($parent['is_selected_admin'] == true)) {
            $query->whereIn('juragan_id', $parent['current_juragan']);
        }


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
            $query->where('id_province', '=', $idProvince);
            $isSearching = true;
        }
        if ($idCity != '') {
            $query->where('id_city', '=', $idCity);
            $isSearching = true;
        }
        //        if ($idDistrict != '') {
        //            $query->where('id_district', '=', $idDistrict);
        //        }
        //        if ($idVillage != '') {
        //            $query->where('id_village', '=', $idVillage);
        //        }
        if ($idJuragan != '') {
            $query->where('juragan_id', '=', $idJuragan);
            $isSearching = true;
        }
        if ($idOutlet != '') {
            $query->where('outlet_id', '=', $idOutlet);
            $isSearching = true;
        }
        if ($idProgress != '') {
            $query->where('status_progress', '=', $idProgress);
            $isSearching = true;
        }
        if ($csdp != '') {
            $query->where('csdp', '=', $csdp);
            $isSearching = true;
        }
        if ($sendDate != '') {
            $query->whereRaw("date(send_date) = '" . $sendDate . "'");
            $isSearching = true;
        }

        if ($isSearching == true) $totalFilter = $query->count();

        if (($parent['is_selected_admin'] == true) || (($parent['is_selected_admin'] == false) && ($parent['super_admin']->name == 'SUPER ADMIN'))) {
            $data = $query->orderBy($order, $orderType)
                ->paginate($pageSize, ['*'], 'page', $page);
        } else {
            $data = collect([]);
        }

        $ret['draw'] = $draw;
        $ret['recordsTotal'] = $total;
        $ret['recordsFiltered'] = $totalFilter;
        $ret['data'] = $data->map(function ($value) use ($parent) {
            $value->created_date_format = Carbon::parse($value->created_date)->format('Y-m-d');
            $value->recommend_date_format = $value->recommend_date ? Carbon::parse($value->recommend_date)->format('Y-m-d') : null;
            $value->send_date_format = $value->send_date ? Carbon::parse($value->send_date)->format('Y-m-d') : null;
            $value->status_class = @$parent['progressClass'][$value->status_progress] ? $parent['progressClass'][$value->status_progress] : 'inverse';

            return $value;
        });

        return response()->json($ret);
    }

    public function show($id)
    {
        $canShow = Auth::user()->getPermissionByName('unilever.show');
        $canApprove = Auth::user()->getPermissionByName('unilever.approve');
        $canReject = Auth::user()->getPermissionByName('unilever.reject');
        $canChangeDeliveryDate = Auth::user()->getPermissionByName('unilever.changeDeliveryDate');
        $outlet = Outlet::with(["mapOutlet", "juragan", "province", "city", "district", "village", "StatusType", "OwnershipStatus", "StreetType"])
            ->find($id);
        $sql = '
            SELECT
              ha.id,
              ha.answers
            FROM outlet.outlet_progress AS op
            INNER JOIN outlet.map_outlet AS mo ON op.id_map_outlet = mo.id
            INNER JOIN outlet.outlet AS o ON mo.id_outlet = o.id
            INNER JOIN hunter.answers AS ha ON op.id_answer = ha.id
            WHERE
              o.id = ? AND
              op.status_active = 1 AND
              op.is_deleted = 1
            LIMIT 1
        ';
        $outletProgress = collect(DB::select(DB::raw($sql), [$id]))->first();
        if (!is_null($outletProgress)) {
            $survey = json_decode($outletProgress->answers);
        } else {
            $this->flashMessage('error', 'Approve Outlet', 'Invalid Outlet Survey');
            return redirect()
                ->route('unilever.index');
        }
        $pictureIds = OutletProgress::getPictureIds($outletProgress->answers)->map(function ($item) {
            return $item->id;
        });
        return view("Unilever/show")
            ->with('canShow', $canShow)
            ->with('canApprove', $canApprove)
            ->with('canReject', $canReject)
            ->with('canChangeDeliveryDate', $canChangeDeliveryDate)
            ->with('data', $outlet)
            ->with('outletProgress', $outletProgress)
            ->with('survey', $survey)
            ->with('pictureIds', $pictureIds);
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
                'id' => 'required|exists:App\Models\Unilever\OutletProgress,id,is_deleted,1',
            ]
        );
        if ($validator->fails()) {
            $this->flashMessage('error', 'Approve Outlet', $validator->errors()->first());
            return redirect()
                ->route('unilever.index');
        }
        $outletProgress = OutletProgress::find($id);
        if (!$outletProgress->canApprove()) {
            $this->flashMessage('error', 'Approve Outlet', 'Invalid Outlet Progress State');
            return redirect()
                ->route('unilever.index');
        }
        DB::beginTransaction();
        try {
            $now = Carbon::now();
            $outletProgressApprove = $outletProgress->replicate();
            $id = $outletProgressApprove->outlet()->city()->first()->id . strval($now->getTimestamp());
            $outletProgressApprove->id = $id;
            $outletProgressApprove->send_date = $outletProgressApprove->recommend_date;
            $outletProgressApprove->status_progress = '5';
            $outletProgressApprove->section = 'uli';
            $outletProgressApprove->created_by = auth()->user()->id;
            $outletProgressApprove->created_date = $now;
            $outletProgressApprove->updated_by = auth()->user()->id;
            $outletProgressApprove->created_at = $now->getTimestamp();
            $outletProgressApprove->updated_at = $now->getTimestamp();
            $outletProgressApprove->save();
            $outletProgress->status_active = 2;
            $outletProgress->updated_by = auth()->user()->id;
            $outletProgress->updated_at = Carbon::now()->getTimestamp();
            $outletProgress->save();
            DB::commit();
        } catch (\Exception $e) {
            Log::error($e);
            DB::rollBack();
            $this->flashMessage('error', 'Approve Outlet', 'Approve Outlet Error');
            return redirect()
                ->route('unilever.index');
        }
        $this->flashMessage('success', 'Approve Outlet', 'Approve Outlet Success');
        return redirect()
            ->route('unilever.index');
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
        $validator = Validator::make(
            [
                'id' => $id,
            ],
            [
                'id' => 'required|exists:App\Models\Unilever\OutletProgress,id,is_deleted,1',
            ]
        );
        if ($validator->fails()) {
            $this->flashMessage('error', 'Reject Outlet', $validator->errors()->first());
            return redirect()
                ->route('unilever.index');
        }
        $outletProgress = OutletProgress::find($id);
        if (!$outletProgress->canReject()) {
            $this->flashMessage('error', 'Reject Outlet', 'Invalid Outlet Progress State');
            return redirect()
                ->route('unilever.index');
        }
        DB::beginTransaction();
        try {
            $now = Carbon::now();
            $outletProgressApprove = $outletProgress->replicate();
            $id = $outletProgressApprove->outlet()->city()->first()->id . $now->getTimestamp();
            $outletProgressApprove->id = $id;
            $outletProgressApprove->status_progress = '4';
            $outletProgressApprove->section = 'uli';
            $outletProgressApprove->created_by = auth()->user()->id;
            $outletProgressApprove->created_date = $now;
            $outletProgressApprove->updated_by = auth()->user()->id;
            $outletProgressApprove->created_at = $now->getTimestamp();
            $outletProgressApprove->updated_at = $now->getTimestamp();
            $outletProgressApprove->save();
            $outletProgress->status_active = 2;
            $outletProgress->updated_by = auth()->user()->id;
            $outletProgress->updated_at = Carbon::now()->getTimestamp();
            $outletProgress->save();
            DB::commit();
        } catch (\Exception $e) {
            Log::error($e);
            DB::rollBack();
            $this->flashMessage('error', 'Reject Outlet', 'Reject Outlet Error');
            return redirect()
                ->route('unilever.index');
        }
        $this->flashMessage('success', 'Reject Outlet', 'Reject Outlet Success');
        return redirect()
            ->route('unilever.index');
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
                'id' => 'required|exists:App\Models\Unilever\OutletProgress,id,is_deleted,1',
                'send_date' => 'required|date_format:Y-m-d',
            ]
        );
        if ($validator->fails()) {
            $this->flashMessage('error', 'Change Delivery Date', $validator->errors()->first());
            return redirect()
                ->route('unilever.index');
        }
        $outletProgress = OutletProgress::find($id);
        if (!$outletProgress->canChangeDeliveryDate()) {
            $this->flashMessage('error', 'Change Delivery Date', 'Invalid Outlet Progress State');
            return redirect()
                ->route('unilever.index');
        }
        $now = Carbon::now();
        $outletProgress->send_date = Carbon::parse($sendDate);
        $outletProgress->updated_by = auth()->user()->id;
        $outletProgress->updated_at = $now->getTimestamp();
        $outletProgress->save();
        $this->flashMessage('success', 'Change Delivery Date', 'Change Delivery Date Success');
        return redirect()
            ->route('unilever.index');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportOutlet()
    {
        $outletUnileverIdNullExport = new OutletUnileverIdNullExport();
        if ($outletUnileverIdNullExport->collection()->count() < 1) {
            $this->flashMessage('info', 'Export Outlet', 'Outlet Data Is Empty');
            return redirect()
                ->route('unilever.index');
        }
        $fileName = Str::uuid()->toString() . '.xlsx';
        return Excel::download(new OutletUnileverIdNullExport(), $fileName);
    }

    /**
     * @return \Illuminate\Http\RedirectResponse | \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportCSDP()
    {
        // return 'test';
        $request = Request::capture();
        // return $request;
        $juraganId = $request->query('juraganId');
        $fromDate = !is_null($request->query('fromDate')) ? Carbon::parse($request->query('fromDate')) : null;
        $toDate = !is_null($request->query('toDate')) ? Carbon::parse($request->query('toDate')) : null;
        // if (trim($juraganId) === '') {
        //     $this->flashMessage('info', 'Export CSDP', 'Juragan Id Required');
        //     return redirect()
        //         ->route('outlet.index');
        // }
        if ($fromDate xor $toDate) {
            $this->flashMessage('info', 'Export CSDP', 'From And To Date Required');
            return redirect()
                ->route('outlet.index');
        }
        $juragan = Juragan::find($juraganId);
        if (is_null($juragan)) {
            $juragan_name = "Semua Juragan";
        } else {
            $juragan_name = $juragan->name;
        }
        $exportDate = \Carbon\Carbon::now();
        $fileName = sprintf(
            '%s-%s.xlsx',
            str_replace(' ', '_', $juragan_name),
            $exportDate->format('Y-m-d')
        );
        $outletCSDPExport = new OutletCSDPExport($juraganId, $fromDate, $toDate, $exportDate);
        if ($outletCSDPExport->collection()->count() < 1) {
            $this->flashMessage('info', 'Export CSDP', 'CSDP Data Is Empty');
            return redirect()
                ->route('outlet.index');
        }
        return Excel::download($outletCSDPExport, $fileName);
    }


    /**
     * @return \Illuminate\Http\Response
     */
    public function importOutlet()
    {
        $data = UnileverOutletJob::with("status")->orderBy("created_at", "desc")->paginate(20);
        return view("Unilever.import")
            ->with('data', $data);
    }

    public function doImportOutlet(Request $request)
    {
        if (!$request->hasFile('import_file')) {
            $this->flashMessage('error', 'Upload Outlet', 'No such a file');
            return redirect()->route('hunter.import-outlet');
        }

        $file = $request->file("import_file");
        if (strtolower($file->getClientMimeType()) != "text/csv" && $file->getClientOriginalExtension() != "csv") {
            $this->flashMessage('error', 'Upload Outlet', 'not csv file');
            return redirect()->route('unilever.import-outlet');
        }

        //Move Uploaded File
        $destinationPath = config("app.job_base_dir") . "/unilever_outlet_imports";
        $fileName = $file->getClientOriginalName();
        try {
            $file->move($destinationPath, $fileName);
            // save to job table
            $data = new UnileverOutletJob();
            $data->type = 1;
            $data->file_path = $destinationPath . "/";
            $data->file_name = $fileName;
            $data->status_id = 1;
            $data->created_at = Carbon::now();
            $data->created_by = auth()->user()->id;
            $data->save();
            $this->flashMessage('success', 'Upload Outlet', 'Upload Success');
            return redirect()->route('unilever.import-outlet');
        } catch (\Exception $e) {
            Log::error($e);
            $this->flashMessage('error', 'Upload Outlet', 'Upload Error');
            return redirect()->route('unilever.import-outlet');
        }
    }
}
