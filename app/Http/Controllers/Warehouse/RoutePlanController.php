<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GenericController;
use App\Models\WarehouseMapping\JuraganToWarehouseManagement;
use App\Models\Adr;
use App\Models\Driver\DeliveryOrder;
use App\Models\Driver\Vehicles;
use App\Models\OutletManagement\Outlet;
use App\Models\Unilever\OutletProgress;
use App\Models\Province;
use App\Models\JuraganManagement\Juragan;
use App\Models\Warehouse\RoutePlan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RoutePlanController extends GenericController
{

    private function edit_rules($id)
    {
        $rules = [
            'outlet' => ['required'],
            'cities' => ['required'],
            // 'date' => ['required']
//            'license_number' => ['required', 'max:12', 'unique:' . Vehicles::class . ',license_number,' . $id],
        ];
        return $rules;
    }

    protected $rules = [
//        'license_number' => 'required|unique:' . Vehicles::class . ',license_number',
        'provinces' => 'required',
        'cities' => 'required',
        // 'date' => 'required'
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
        $authId = Auth::id();
        $getUserRole = Auth::user()->with('roles')->where('id', $authId)->first();
        $super_admin = $getUserRole->roles[0];
        $juraganToWarehouse = JuraganToWarehouseManagement::
                join('warehouse.warehouse_managements', 'juragan.juragan_to_warehouses.id_warehouse_management', 'warehouse.warehouse_managements.id')
                ->where('juragan.juragan_to_warehouses.is_deleted', 1)
                ->select('warehouse.warehouse_managements.id as wid', 'warehouse.warehouse_managements.warehouse_name', 'warehouse.warehouse_managements.id_warehouse_admins', 'juragan.juragan_to_warehouses.*')
                ->get();
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
        $data = RoutePlan::with($this->relation($request))->where('is_deleted', 1)->orderBy('created_at', 'DESC');
        // $data = RoutePlan::
        // join('outlet.outlet', 'driver.route_plans.id_outlet', 'outlet.outlet.id')->
        // join('juragan.juragans', 'outlet.outlet.id_juragan', 'juragan.juragans.id')->
        // with($this->relation($request))->where('driver.route_plans.is_deleted', 1)->orderBy('driver.route_plans.created_at', 'DESC');
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

        // dd($data->toSql());

        if ($request->query('start') != "" && $request->query('end') != "") {
            $start = Carbon::parse($request->query('start'));
            $end = Carbon::parse($request->query('end'));
            $data = $data->where('start_date', '>=', $start)->where('start_date', '<=', $end);
        }

        // Ini baris kode yang menyebabkan ketika routeplan sudah dipilih ke journey plan, maka ga muncul lagi di halaman index

        // $data = $data->whereHas('outlet.mapOutlet.outletProgress', function ($query) {
        //     $query->where('status_active', 1);
        //     $query->where('section', 'uli');
        //     $query->where('status_progress', '5');
        //     $query->select();
        // });

        if(($is_selected_admin == true)) {
            // $data = $data->with('journeyRoute')->whereIn('id_juragan', $current_juragan)->paginate(30);
            $data = $data->with('journeyRoute')->paginate(30);
            $data->appends($request->query());
        } elseif(($is_selected_admin == false) && ($super_admin->name == 'SUPER ADMIN')) {
            $data = $data->with('journeyRoute')->paginate(30);
            $data->appends($request->query());
        } elseif(($is_selected_admin == false) && ($super_admin->name == 'Admin Warehouse')) {
            $data = array();
        } else {
            $data = array();
        }
        
        $provinces = Province::all()->pluck('name', 'id');
        $data = [
            'provinces' => $provinces,
            'datas' => $data
        ];
        return view('Driver/RoutePlan/index', $data);
    }

    /**
     * Cancel route plan that active
     *
     * @return \Illuminate\Http\Response
     */
    public function cancelRoutePlan($id) {
        // Find the route plan by id
        $routePlan = RoutePlan::find($id);
        $routePlan->plan_status = 'canceled';
        $routePlan->canceled_by = Auth::id();
        $routePlan->canceled_at = Carbon::now();
        $routePlan->save();
        $this->flashMessage('success', 'UPDATE', 'Route Plan Canceled');
        return Redirect::to(route('route_plan.index'));
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
            'route' => route('route_plan.store')
        ];
        return view('Driver/RoutePlan/create', $datas);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //ADR0030000
//        dd($request->all());
        $validator = Validator::make($request->all(), $this->rules);
        if ($validator->fails()) {
            return Redirect::to(route('route_plan.create'))
                ->withErrors($validator)->withInput($request->all());
        } else {
            DB::beginTransaction();
            try {
                $count = Adr::find(1);
//                $adr = (date('Y-m-d', strtotime($count->updated_at)) == date('Y-m-d')) ? $count->current_counter : $count->current_counter + 1;
                $adr = $count->current_counter + 1;
                $do = new DeliveryOrder();
                $delivery_order = [
                    'id' => Carbon::now()->getPreciseTimestamp(3),
                    'adr' => $adr,
                    'id_province' => $request->provinces,
                    'is_deleted' => 1,
                    'id_city' => $request->cities,
                    'created_date' => Carbon::now(),
                    'created_at' => Carbon::now()->unix(),
                    'created_by' => Auth::user()->id
                ];
                $id_do = $do->create($delivery_order);
                $outlet = array_filter($request->outlet);
                $reportrange = array_filter($request->post('reportrange'));
                $cabinet = array_filter($request->cabinet);
//                echo "<pre>";
                for ($i = 0; $i < count($outlet); $i++) {
                    $data = new RoutePlan();
//                    print_r($outlet[$i].'<br>');
//                    print_r(Carbon::parse($date[$i]));
//                    print_r($cabinet[$i].'<br>');
//                    dd($request->outlet[$i]);
                    $date = $reportrange;
                    $start = Carbon::parse($date[$i]);
                    $end = Carbon::parse($date[$i]);
                    $data->id = Carbon::now()->getPreciseTimestamp(3);
                    $data->id_outlet = $outlet[$i];
                    $data->id_cabinet = $cabinet[$i];
                    $data->id_delivery_order = $id_do->id;
                    $data->is_deleted = 1;
                    $data->start_date = $start;
                    $data->end_date = $end;
                    $data->plan_status = 'ongoing';
                    $data->created_by = Auth::user()->id;
                    $data->created_at = Carbon::now()->unix();
                    $data->updated_at = Carbon::now()->unix();
                    $data->save();
                }
//                echo "</pre>";
//                dd('ok');
                $count->current_counter = $adr;
                $count->updated_at = now();
                $count->save();
                DB::commit();
                $this->flashMessage('success', 'CREATE', 'Add data success');
                return Redirect::to(route('route_plan.index'));
            } catch (\Exception $e) {
                DB::rollback();
                Log::error($e);
                $this->flashMessage('error', 'CREATE', $e->getMessage());
                return Redirect::to(route('route_plan.create'));
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
            'outlet.mapOutlet.OutletProgress',
            'journeyRoute',
        ];
        $data = RoutePlan::with($relation)->where('id', $id)->first();
        $survey = DB::table("v_request_detail")->where("id_outlet", "=", $data->id_outlet)->first();
//        $pictureIds = OutletProgress::getPictureIds($data->id_outlet)->map(function ($item) {
//            return $item->id;
//        });
        $pictureIds = OutletProgress::getPictureIds($survey->answers)->map(function ($item) {
            return $item->id;
        });
        $datas = [
            'data' => $data,
            'journey_plan_id' => count($data->journeyRoute) > 0 ? $data->journeyRoute[0]->id : '',
            'pictureIds' => $pictureIds,
            'survey' => $survey
        ];
//        dd($datas);
//        dd($pictureIds);
        return view('Driver/RoutePlan/show', $datas);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $route = RoutePlan::with($this->relation())->find($id);
        $datas = [
            'route_plan' => $route,
            'route' => route('route_plan.update', ['route_plan' => $id])
        ];
        return view('Driver/RoutePlan/edit', $datas);
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
//        $validator = Validator::make($request->all(), $this->rules);
//        if ($validator->fails()) {
//            return Redirect::to(route('route_plan.create'))
//                ->withErrors($validator)->withInput($request->all());
//        } else {
//            try {
//                DB::beginTransaction();
//                $data = RoutePlan::find($id);
//                $do = DeliveryOrder::find($data->id_delivery_order);
//                $do->id_province = $request->provinces;
//                $do->id_city = $request->city;
//                $do->save();
//                for ($i = 0; $i < count($request->outlet); $i++) {
//                    $ex_time = explode(' - ', $request->post('reportrange_' . $i));
//                    $start = date('Y-m-d H:i:s', strtotime($ex_time[0]));
//                    $end = date('Y-m-d H:i:s', strtotime($ex_time[1]));
//                    $data->id_outlet = $request->outlet[$i];
//                    $data->id_cabinet = $request->cabinet[$i];
//                    $data->start_date = $start;
//                    $data->end_date = $end;
//                    $data->updated_by = Auth::user()->id;
//                    $data->save();
//                }
//                DB::commit();
//                $this->flashMessage('success', 'CREATE', 'Add data success');
//                return Redirect::to(route('route_plan.index'));
//            } catch (\Exception $e) {
//                DB::rollback();
//                Log::error($e);
//                $this->flashMessage('error', 'CREATE', $e->getMessage());
//                return Redirect::to(route('route_plan.create'));
//            }
//        }
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

    public function exportActivity()
    {
        $request = Request::capture();
        $search = $request->query('search', '');
        $startDate = $request->query('fromDate', '');
        $endDate = $request->query('toDate', '');
        $file_name = sprintf('warehouse-activity-%s.xlsx', Str::uuid()->toString());
        return response()->streamDownload(function () use ($search, $startDate, $endDate) {
            echo file_get_contents('http://127.0.0.1:8000/warehouse/activity?' . http_build_query(['search' => $search, 'from_date' => $startDate, 'to_date' => $endDate]));
        }, $file_name);
    }
}
