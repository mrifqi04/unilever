<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GenericController;
use App\Models\WarehouseMapping\JuraganToWarehouseManagement;
use App\Models\Adr;
use App\Models\Driver\DeliveryOrder;
use App\Models\Driver\Vehicles;
use App\Models\OutletManagement\Outlet;
use App\Models\Unilever\OutletRetractionProgress;
use App\Models\Province;
use App\Models\JuraganManagement\Juragan;
use App\Models\Warehouse\Cabinets;
use App\Models\Warehouse\RetractionRoutePlan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\Warehouse\RoutePlan;
use App\Models\OutletManagement\OutletRetractionProgress as OutletRetractionProgressManagement;
use PDF;

class RoutePlanPullCabinetController extends GenericController
{
    protected $rules = [
        'provinces'     => 'required',
        'cities'        => 'required',
        // 'start'         => 'required',
        // 'end'           => 'required',
        'outlet.*'      => 'required',
        'cabinet.*'     => 'required',
        'reportrange.*' => 'required',
    ];

    private function relation()
    {
        return [
            'deliveryOrders',
            'outlet.province' => function ($query) {
                $query->select('id', 'name');
            },
            'outlet.city' => function ($query) {
                $query->select('id', 'name');
            },
            'outlet.district' => function ($query) {
                $query->select('id', 'name');
            },
            'outlet.village' => function ($query) {
                $query->select('id', 'name');
            },
            'outlet' => function ($query) {
                $query->select('id', 'name', 'address', 'phone', 'id_province', 'id_city', 'id_district', 'id_village','status_active');
            },
            'outlet.juragan.juragans',
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $page     = $request->query('page', '');
        $pageSize = 30;

        $authId      = Auth::id();
        $getUserRole = Auth::user()->with('roles')->where('id', $authId)->first();
        $super_admin = $getUserRole->roles[0];
        $juraganToWarehouse = JuraganToWarehouseManagement::
                join('warehouse.warehouse_managements', 'juragan.juragan_to_warehouses.id_warehouse_management', 'warehouse.warehouse_managements.id')
                ->where('juragan.juragan_to_warehouses.is_deleted', 1)
                ->select('warehouse.warehouse_managements.id as wid', 'warehouse.warehouse_managements.warehouse_name', 'warehouse.warehouse_managements.id_warehouse_admins', 'juragan.juragan_to_warehouses.*')
                ->get();
        // dd($juraganToWarehouse);
        $is_selected_admin = false;
        $current_juragan = [];
        foreach($juraganToWarehouse as $juragan) {
            $listAdmins =  explode(",", $juragan['id_warehouse_admins']);
            $current_juragan =  explode(",", $juragan['id_juragan_mappings']);
            foreach($listAdmins as $listAdmin) {
                $listAdmin = trim($listAdmin);
                $contains = Str::contains($listAdmin, [$authId]);
                if($contains) {
                    $is_selected_admin = true;
                    break;
                }
            }
            if($is_selected_admin){
                break;
            }
        }
        $data = RetractionRoutePlan::where('plan_status', '!=', 'canceled')->with($this->relation($request))->where('is_deleted', 1)->orderBy('created_at', 'DESC');        

        if ($request->query('cities') != "") {
            $data = $data->whereHas('outlet.city', function ($query) use ($request) {
                $query->where('id', $request->query('cities'));
            });
        }
        if ($request->query('province') != "") {
            $data = $data->whereHas('outlet.province', function ($query) use ($request) {
                $query->where('id', $request->query('province'));
            });
        }
        if ($request->query('search') != "") {
            $data = $data->whereHas('deliveryOrders', function ($query) use ($request) {
                $query->whereRaw("lower(adr) like ?", ["%" . $request->query("search") . "%"]);
            });
            $data = $data->orWhereHas('outlet', function ($query) use ($request) {
                $query->whereRaw("lower(name) like ?", ["%" . $request->query("search") . "%"])
                    ->orWhereRaw("lower(phone) like ?", ["%" . $request->query("search") . "%"])
                    ->orWhereRaw("lower(owner) like ?", ["%" . $request->query("search") . "%"]);
            });
        }

        if ($request->query('id_outlet') != "") {
            $data->where('id_outlet', $request->query('id_outlet'));
        }

        if ($request->query('start') != "" && $request->query('end') != "") {
            $start = Carbon::parse($request->query('start'));
            $end = Carbon::parse($request->query('end'));
            $data = $data->where('start_date', '>=', $start)->where('start_date', '<=', $end);
        }

        if(($is_selected_admin == true)) {
            // $data = $data->with('journeyRoute')->whereIn('id_juragan', $current_juragan)->paginate($pageSize, ['*'], 'page', $page);
            $data = $data->with('journeyRoute')->paginate($pageSize, ['*'], 'page', $page);
            $data->appends($request->query());
        } elseif(($is_selected_admin == false) && ($super_admin->name == 'SUPER ADMIN')) {
            $data = $data->with('journeyRoute')->paginate($pageSize, ['*'], 'page', $page);
            $data->appends($request->query());
        } elseif(($is_selected_admin == false) && ($super_admin->name == 'Admin Warehouse')) {
            $data = '';
        } else {
            $data = '';
        }

        
        $provinces = Province::all()->pluck('name', 'id');
        $data = [
            'provinces' => $provinces,
            'datas' => $data
        ];
        return view('Warehouse/RoutePlanPullCabinet/index', $data);
    }

    /**
     * Cancel route plan that active
     *
     * @return \Illuminate\Http\Response
     */
    public function cancel($id) {
        // Find the route plan by id
        $routePlan = RetractionRoutePlan::where('id', $id)
            ->where('is_deleted', 1)
            ->first();

        if ($routePlan->plan_status == 'canceled' && !$routePlan->journeyRoute->isEmpty()) {
            $this->flashMessage('error', 'Cancel Route Plan', 'Invalid Route Plan');
            return Redirect::to(route('warehouse.route-plan-pull-cabinet.index'));
        }

        $routePlan->plan_status = 'canceled';
        $routePlan->canceled_by = Auth::id();
        $routePlan->canceled_at = Carbon::now();
        $routePlan->save();
        $this->flashMessage('success', 'UPDATE', 'Route Plan Canceled');
        return Redirect::to(route('warehouse.route-plan-pull-cabinet.index'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $provinces = Province::all()->pluck('name', 'id');
        $datas = [
            'provinces' => $provinces,
            'route' => route('warehouse.route-plan-pull-cabinet.store')
        ];
        return view('Warehouse/RoutePlanPullCabinet/create', $datas);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), $this->rules);
        if ($validator->fails()) {
            return Redirect::to(route('warehouse.route-plan-pull-cabinet.create'))
                ->withErrors($validator)->withInput($request->all());
        } else {
            DB::beginTransaction();
            try {
                $count = Adr::find(1);
                $adr   = (date('Y-m-d', strtotime($count->updated_at)) == date('Y-m-d')) ? $count->current_counter : $count->current_counter + 1;
                $adr   = $count->current_counter + 1;
                $do    = new DeliveryOrder();
                $delivery_order = [
                    'id'           => Carbon::now()->getPreciseTimestamp(3),
                    'adr'          => $adr,
                    'id_province'  => $request->provinces,
                    'is_deleted'   => 1,
                    'id_city'      => $request->cities,
                    'created_date' => Carbon::now(),
                    'created_at'   => Carbon::now()->unix(),
                    'created_by'   => Auth::user()->id
                ];
                $id_do       = $do->create($delivery_order);
                $outlet      = array_filter($request->outlet);
                $reportrange = array_filter($request->post('reportrange'));
                $cabinet     = array_filter($request->cabinet);

                for ($i = 0; $i < count($outlet); $i++) {
                    $data                    = new RetractionRoutePlan();
                    $date                    = $reportrange;
                    $start                   = Carbon::parse($date[$i]);
                    $end                     = Carbon::parse($date[$i]);
                    $data->id                = Carbon::now()->getPreciseTimestamp(3);
                    $data->id_outlet         = $outlet[$i];
                    $getOutlet = Outlet::find($data->id_outlet);
                    if ($getOutlet) {
                        if ($getOutlet->Cabinets->first()->id != $cabinet[$i]) {
                            $this->updateQrCode($cabinet[$i], $getOutlet->Cabinets->first()->serialnumber);
                            $cabinet[$i] = $getOutlet->Cabinets->first()->id;
                        }
                    }
                    $data->id_cabinet = $cabinet[$i];
                    $data->id_delivery_order = $id_do->id;
                    $data->is_deleted        = 1;
                    $data->start_date        = $start;
                    $data->end_date          = $end;
                    $data->plan_status       = 'ongoing';
                    $data->created_by        = Auth::user()->id;
                    $data->created_at        = Carbon::now()->unix();
                    $data->updated_at        = Carbon::now()->unix();
                    $data->save();

                    OutletRetractionProgressManagement::whereHas('mapOutlet', function ($query) use ($outlet, $i) {
                        $query->where('id_outlet', '=', $outlet[$i]);
                    })->where('status_active', 1)->update(['send_date' => $start]);
                }

                $count->current_counter = $adr;
                $count->updated_at = now();
                $count->save();
                DB::commit();
                $this->flashMessage('success', 'CREATE', 'Add data success');
                return Redirect::to(route('warehouse.route-plan-pull-cabinet.index'));
            } catch (\Exception $e) {
                DB::rollback();
                Log::error($e);
                $this->flashMessage('error', 'CREATE', $e->getMessage());
                return Redirect::to(route('warehouse.route-plan-pull-cabinet.create'));
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $relation = [
            'deliveryProgress',
            'deliveryOrders',
            'outlet',
            'outlet.juragan',
            'outlet.district',
            'outlet.village',
            'outlet.mapOutlet.outletRetractionProgress',
            'journeyRoute',
        ];
        $data       = RetractionRoutePlan::with($relation)->where('id', $id)->first();
        $survey     = DB::table("v_retraction_detail")->where("id_outlet", "=", $data->id_outlet)->first();
        $pictureIds = ($survey) ? OutletRetractionProgress::getPictureIds($survey->answer_images, $survey->signature_image_id)->map(function ($item) {
            return $item->id;
        }) : [];
        $datas = [
            'data'            => $data,
            'journey_plan_id' => count($data->journeyRoute) > 0 ? $data->journeyRoute[0]->id : '',
            'pictureIds'      => $pictureIds,
            'survey'          => $survey
        ];

        return view('Warehouse/RoutePlanPullCabinet/show', $datas);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $relation = [
            'deliveryProgress',
            'deliveryOrders',
            'cabinet',
        ];
        $data  = RetractionRoutePlan::with($relation)
        ->with(['outlet.mapOutlet.outletRetractionProgress' => function ($query) {
            $query->where('status_active', 1);
        }])->where('id', $id)->first();
        $provinces = Province::all()->pluck('name', 'id');
        $datas = [
            'provinces' => $provinces,
            'data'      => $data,
        ];

        return view('Warehouse/RoutePlanPullCabinet/edit', $datas);
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
        $validator = Validator::make($request->all(), $this->rules);
        if ($validator->fails()) {
            return Redirect::to(route('warehouse.route-plan-pull-cabinet.edit', ['id' => $id]))
                ->withErrors($validator)->withInput($request->all());
        } else {
            DB::beginTransaction();
            try {
                $data = RetractionRoutePlan::find($id);

                if(!(Auth::user()->getPermissionByName('warehouse.route-plan-pull-cabinet.update') && ($data->plan_status != 'canceled') && $data->journeyRoute->isEmpty())) {
                    $this->flashMessage('error', 'UPDATE', 'Invalid Route Plan State');
                    return redirect()
                        ->route('warehouse.route-plan-pull-cabinet.index');
                }

                $do              = DeliveryOrder::find($data->id_delivery_order);
                $do->id_province = $request->provinces;
                $do->id_city     = $request->cities;
                $do->updated_at  = Carbon::now()->unix();
                $do->updated_by  = Auth::user()->id;
                $do->save();

                $outlet      = array_filter($request->outlet);
                $reportrange = array_filter($request->post('reportrange'));
                $cabinet     = array_filter($request->cabinet);

                for ($i = 0; $i < count($outlet); $i++) {
                    $date             = $reportrange;
                    $start            = Carbon::parse($date[$i]);
                    $end              = Carbon::parse($date[$i]);
                    $data->id_outlet  = $outlet[$i];
                    if ($data->id_cabinet != $cabinet[$i]) {
                        $this->updateQrCode($cabinet[$i], $data->cabinet->serialnumber);
                    } else {
                        $data->id_cabinet = $cabinet[$i];
                    }
                    $data->start_date = $start;
                    $data->end_date   = $end;
                    $data->updated_by = Auth::user()->id;
                    $data->updated_at = Carbon::now()->unix();
                    $data->save();
                    OutletRetractionProgressManagement::whereHas('mapOutlet', function ($query) use ($outlet, $i) {
                        $query->where('id_outlet', '=', $outlet[$i]);
                    })->where('status_active', 1)->update(['send_date' => $start]);
                }
                
                DB::commit();
                $this->flashMessage('success', 'UPDATE', 'Update data success');
                return Redirect::to(route('warehouse.route-plan-pull-cabinet.index'));
            } catch (\Exception $e) {
                DB::rollback();
                Log::error($e);
                $this->flashMessage('error', 'UPDATE', $e->getMessage());
                return Redirect::to(route('warehouse.route-plan-pull-cabinet.edit', ['id' => $id]));
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $validator = Validator::make(
            [
                'id' => $id,
            ],
            [
                'id' => 'required|exists:App\Models\Warehouse\RetractionRoutePlan,id,is_deleted,1',
            ]
        );
        if ($validator->fails()) {
            return redirect()
                ->route('warehouse.route-plan-pull-cabinet.index')
                ->with('message', $validator->errors());
        }
        $routePlan = RetractionRoutePlan::where('id', $id)
            ->where('is_deleted', 1)
            ->first();
        
        if ($routePlan->plan_status == 'canceled' && !$routePlan->journeyRoute->isEmpty()) {
            $this->flashMessage('error', 'Cancel Route Plan', 'Invalid Route Plan');
            return Redirect::to(route('warehouse.route-plan-pull-cabinet.index'));
        }

        $routePlan->is_deleted = 2;
        try {
            $routePlan->save();
            $this->flashMessage('success', 'Delete Route Plan', "Delete Route Plan Success");
        } catch (\Exception $e) {
            Log::error($e);
            return redirect()
                ->route('warehouse.route-plan-pull-cabinet.index')
                ->with('message', sprintf('error deleting auditor %s', $e->getMessage()));
        }
        return redirect()->route('warehouse.route-plan-pull-cabinet.index');
    }

    public function export()
    {
        $request   = Request::capture();
        $search    = $request->query('search', '');
        $startDate = $request->query('fromDate', '');
        $endDate   = $request->query('toDate', '');
        $file_name = sprintf('warehouse-activity-%s.xlsx', Str::uuid()->toString());
        return response()->streamDownload(function () use ($search, $startDate, $endDate) {
            echo file_get_contents('http://127.0.0.1:8000/warehouse/activity?' . http_build_query(['search' => $search, 'from_date' => $startDate, 'to_date' => $endDate]));
        }, $file_name);
    }

    private function updateQrCode($qrcode, $serialnumber) {
        if ($qrcode) {
            $existQr = Cabinets::where('qrcode_by_auditor', $qrcode)->first();
            if($existQr){
                $existQr->qrcode_by_auditor = NULL;
                $existQr->updated_at = Carbon::now();
                $existQr->save();
            }
            $cabinet = Cabinets::where('serialnumber', $serialnumber)->first();
            if($cabinet){
                $cabinet->qrcode_by_auditor = $qrcode;
                $cabinet->updated_at = Carbon::now();
                $cabinet->save();                    
            }
        }
    }

    public function downloadArt(Request $request, $id)
    {
        $auth = Auth::user();
        if (!$auth) {
            $auth = DB::table("driver.v_login")->where("id", $request->get('user_id'))->first();
        }
        $relation = [
            'outlet',
            'outlet.Cabinets',
            'outlet.mapOutlet.outletRetractionProgress',
            'journeyRoute',
        ];
        $data = RetractionRoutePlan::with($relation)->where('id', $id)->first();
        $outletRetractionProgress = $data->outlet->mapOutlet->outletRetractionProgress->where('status_active', 1)->first();
        $survey = DB::table("v_retraction_detail")->where("id_outlet", "=", $data->id_outlet)->first();

        $datas = [
            'auth' => $auth,
            'data' => $data,
            'outletRetractionProgress' => $outletRetractionProgress,
            'signature_outlet' => $this->getPicture(@$survey->signature_image_id, 1)
        ];

        $pdf = PDF::setOptions([
            'dpi' => 150,
            'defaultFont' => 'sans-serif',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'debugCss' => false
        ])->loadView('Warehouse/RoutePlanPullCabinet/download-art', $datas)->setPaper('a4');

        // return $pdf->stream('ART-'.$data->outlet->id.'.pdf');
        return $pdf->download('ART-'.$data->outlet->id.'.pdf');
    }
}
