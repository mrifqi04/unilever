<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\GenericController;
use App\Models\Driver\JourneyPlan;

// import model
use App\Models\Driver\RetractionJourneyPlan;
use App\Models\OutletManagement\OutletRetractionProgress;

use App\Models\Driver\Answer;
use App\Models\Driver\JourneyHasRoutePlan;
use App\Models\Driver\RedeploymentJourneyPlan;
use App\Models\Driver\RetractionJourneyHasRoutePlan;
use App\Models\OutletManagement\MapOutlet;
use App\Models\OutletManagement\OutletActivity;
use App\Models\OutletManagement\OutletProgress;
use App\Models\Province;
use Illuminate\Support\Str;
use PDF;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;
use App\Models\WarehouseMapping\JuraganToWarehouseManagement;

class DeliveryController extends GenericController
{

    protected $rules = [
        'provinces' => 'required',
        'cities' => 'required',
        'date' => 'required',
        'route_plan' => 'required',
        'driver' => 'required',
        'vehicle' => 'required'
    ];

    private function relation()
    {
        return [
            'journeyRoute',
            'driver',
            'vehicle',
            'user',
            'journeyRoute.outlet' => function ($query) {
                $query->select('id', 'name', 'address', 'phone', 'id_province', 'id_city', 'id_district', 'id_village');
            },
            'journeyRoute.deliveryOrders',
            'journeyRoute.deliveryProgress' => function ($query) {
                $query->select('id', 'id_route_plan', 'status_progress');
            },
            'journeyRoute.outlet.province' => function ($query) {
                $query->select('id', 'name');
            },
            'journeyRoute.outlet.city' => function ($query) {
                $query->select('id', 'name');
            },
            'journeyRoute.outlet.district' => function ($query) {
                $query->select('id', 'name');
            },
            'journeyRoute.outlet.village' => function ($query) {
                $query->select('id', 'name');
            }
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function index(Request $request)
    // {

    //     $authId = Auth::id();
    //     $getUserRole = Auth::user()->with('roles')->where('id', $authId)->first();
    //     $super_admin = $getUserRole->roles[0];
    //     $juraganToWarehouse = JuraganToWarehouseManagement::join('warehouse.warehouse_managements', 'juragan.juragan_to_warehouses.id_warehouse_management', 'warehouse.warehouse_managements.id')
    //         ->where('juragan.juragan_to_warehouses.is_deleted', 1)
    //         ->select('warehouse.warehouse_managements.id as wid', 'warehouse.warehouse_managements.warehouse_name', 'warehouse.warehouse_managements.id_warehouse_admins', 'juragan.juragan_to_warehouses.*')
    //         ->get();
    //     $is_selected_admin = false;
    //     $current_juragan = [];
    //     foreach ($juraganToWarehouse as $juragan) {
    //         $listAdmins =  explode(",", $juragan['id_warehouse_admins']);
    //         $current_juragan =  explode(",", $juragan['id_juragan_mappings']);
    //         foreach ($listAdmins as $listAdmin) {
    //             $listAdmin = trim($listAdmin);
    //             $contains = Str::contains($listAdmin, [$authId]);
    //             if ($contains) {
    //                 $is_selected_admin = true;
    //                 break;
    //             }
    //         }
    //         if ($is_selected_admin) {
    //             break;
    //         }
    //     }

    //     $data = JourneyPlan::with($this->relation())->orderBy('created_at', 'DESC');
    //     if (($is_selected_admin == true)) {
    //         $data = $data->whereHas('journeyRoute.outlet', function ($query) use ($current_juragan, $request) {
    //             $query->whereIn('id_juragan', $current_juragan);
    //         });
    //     }
    //     if ($request->query('cities') != "") {
    //         $data = $data->whereHas('journeyRoute.outlet.city', function ($query) use ($request) {
    //             $query->where('id', $request->query('cities'));
    //         });
    //     }

    //     if ($request->query('province') != "") {
    //         $data = $data->whereHas('journeyRoute.outlet.province', function ($query) use ($request) {
    //             $query->where('id', $request->query('province'));
    //         });
    //     }

    //     if ($request->query('search') != "") {
    //         $data = $data->whereRaw("lower(name) ilike ?", ["%" . $request->query("search") . "%"]);
    //         $data = $data->orWhereHas('user', function ($query) use ($request) {
    //             $query->whereRaw("lower(name) ilike ?", ["%" . $request->query("search") . "%"]);
    //         });
    //         $data = $data->orWhereHas('driver', function ($query) use ($request) {
    //             $query->whereRaw("lower(name) ilike ?", ["%" . $request->query("search") . "%"]);
    //         });
    //         $data = $data->orWhereHas('vehicle', function ($query) use ($request) {
    //             $query->whereRaw("lower(license_number) ilike ?", ["%" . $request->query("search") . "%"]);
    //         });
    //     }

    //     // dd($data->paginate(30));


    //     if ($request->query('start') != "" && $request->query('end') != "") {
    //         $start = Carbon::parse($request->query('start'));
    //         $end = Carbon::parse($request->query('end'));
    //         $data = $data->where('start_date', '>=', $start)->where('start_date', '<=', $end);
    //     }


    //     //        $data = JourneyPlan::orderBy('created_at', 'DESC');
    //     //        $data = $data->whereHas('routePlan.deliveryProgress', function ($query) {
    //     //            $query->select();
    //     //        });
    //     //        dd($data->toSql());
    //     $data = $data->paginate(30);
    //     $data->appends($request->query());
    //     $provinces = Province::all()->pluck('name', 'id');
    //     $data = [
    //         'provinces' => $provinces,
    //         'datas' => $data
    //     ];

    //     return view('Warehouse/Delivery/index', $data);
    // }

    public function index(Request $request)
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

        $query0 = JourneyPlan::with($this->relation())->selectRaw("*, 'Deploy' as journey_plan_type");;
        $query1 = RetractionJourneyPlan::selectRaw("*, 'Tarik' as journey_plan_type");
        $query2 = RedeploymentJourneyPlan::selectRaw("*, 'Tukar' as journey_plan_type");

        for ($i = 0; $i <= 2; ++$i) {
            if (($is_selected_admin == true)) {
                ${"query$i"} = ${"query$i"}->whereHas('journeyRoute.outlet', function ($query) use ($current_juragan, $request) {
                    $query->whereIn('id_juragan', $current_juragan);
                });
            }
            if ($request->query('cities') != "") {
                ${"query$i"} = ${"query$i"}->whereHas('journeyRoute.outlet.city', function ($query) use ($request) {
                    $query->where('id', $request->query('cities'));
                });
            }

            if ($request->query('province') != "") {
                ${"query$i"} = ${"query$i"}->whereHas('journeyRoute.outlet.province', function ($query) use ($request) {
                    $query->where('id', $request->query('province'));
                });
            }

            if ($request->query('search') != "") {
                ${"query$i"} = ${"query$i"}->whereRaw("lower(name) ilike ?", ["%" . $request->query("search") . "%"]);
                ${"query$i"} = ${"query$i"}->orWhereHas('user', function ($query) use ($request) {
                    $query->whereRaw("lower(name) ilike ?", ["%" . $request->query("search") . "%"]);
                });
                ${"query$i"} = ${"query$i"}->orWhereHas('driver', function ($query) use ($request) {
                    $query->whereRaw("lower(name) ilike ?", ["%" . $request->query("search") . "%"]);
                });
                ${"query$i"} = ${"query$i"}->orWhereHas('vehicle', function ($query) use ($request) {
                    $query->whereRaw("lower(license_number) ilike ?", ["%" . $request->query("search") . "%"]);
                });
            }

            // dd($data->paginate(30));


            if ($request->query('start') != "" && $request->query('end') != "") {
                $start = Carbon::parse($request->query('start'));
                $end = Carbon::parse($request->query('end'));
                ${"query$i"} = ${"query$i"}->where('start_date', '>=', $start)->where('start_date', '<=', $end);
            }
        }


        //        $data = JourneyPlan::orderBy('created_at', 'DESC');
        //        $data = $data->whereHas('routePlan.deliveryProgress', function ($query) {
        //            $query->select();
        //        });
        //        dd($data->toSql());
        switch ($request->query('journey_plan_type')) {
            case 'Deploy':
                $data = $query0;
                break;

            case 'Tarik':
                $data = $query1;
                break;

            case 'Tukar':
                $data = $query2;
                break;

            default:
                $data = $query0->union($query1)->union($query2);
                break;
        }

        $data = $data->paginate(30);
        $data->appends($request->query());
        foreach ($data->items() as $key => $value) {
            if (!$request->query('journey_plan_type')) {
                $journeyPlanType = $value->journey_plan_type;
                switch ($journeyPlanType) {
                    case 'Tarik':
                        $value = RetractionJourneyPlan::where('id', $value->id)->with($this->relation())->first();
                        $value->journey_plan_type = $journeyPlanType;
                        break;
                    case 'Tukar':
                        $value = RedeploymentJourneyPlan::where('id', $value->id)->with($this->relation())->first();
                        $value->journey_plan_type = $journeyPlanType;
                        break;

                    default:
                        # code...
                        break;
                }
            }
            $data[$key] = $value;
        }
        $provinces = Province::all()->pluck('name', 'id');
        $data = [
            'provinces' => $provinces,
            'datas' => $data
        ];

        return view('Warehouse/Delivery/index', $data);
    }


    /**
     * Cancel journey plan that active
     *
     * @return \Illuminate\Http\Response
     */
    public function cancelJourneyPlan($id)
    {
        // Find the journey plan by id
        $journeyPlan = JourneyPlan::find($id);
        // Find the relation with between journey plan and route plan
        $route_plan_id = $journeyPlan->journeyRoute[0]->pivot->id_route_plan;
        // Find specific answer for certain route_plan_id
        $answer = Answer::where('id_route_plan', $route_plan_id);
        if ($answer == null) {
            $journeyPlan->plan_status = 'canceled';
            $journeyPlan->canceled_by = Auth::id();
            $journeyPlan->canceled_at = Carbon::now();
            $journeyPlan->save();
            $this->flashMessage('success', 'UPDATE', 'Journey Plan Canceled');
            return Redirect::to(route('delivery.index'));
        } else {
            $this->flashMessage('error', 'GAGAL', 'Tidak dapat cancel, driver sudah mengisi data form');
            return Redirect::to(route('delivery.index'));
        }
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
            'route' => route('delivery.store')
        ];
        return view('Warehouse/Delivery/create', $datas);
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
            return Redirect::to(route('delivery.create'))
                ->withErrors($validator)->withInput($request->all());
        } else {
            try {
                DB::beginTransaction();
                for ($i = 0; $i < count($request->route_plan); $i++) {
                    $ex = explode('_', $request->route_plan[$i]);
                    $route_plan[] = $ex[0];
                    $outlet[] = $ex[1];
                }
                $journeyPlanType = $request->get('journey_plan_type', '');
                switch ($journeyPlanType) {
                    case 'Tarik':
                        $data = new RetractionJourneyPlan();
                        break;

                    default:
                        $data = new JourneyPlan();
                        break;
                }
                // dd($data);
                $data->id = Uuid::uuid4()->toString();
                $data->name = $request->name;
                $data->assigner = Auth::user()->id;
                $data->assign_to = $request->driver;
                $data->id_province = $request->provinces;
                $data->id_city = $request->cities;
                $data->id_vehicle = $request->vehicle;
                $data->start_date = date('Y-m-d H:i:s.u', strtotime($request->date));
                $data->end_date = date('Y-m-d H:i:s.u', strtotime($request->date));
                $data->is_deleted = 1;
                $data->plan_status = 'ongoing';
                $data->created_at = Carbon::now()->unix();
                $data->save();
                switch ($journeyPlanType) {
                    case 'Tarik':
                        foreach ($route_plan as $value) {
                            $journeyHasRoute = new RetractionJourneyHasRoutePlan();
                            $journeyHasRoute->id_journey_plan = $data->id;
                            $journeyHasRoute->id_route_plan = $value;
                            $journeyHasRoute->save();
                            break;
                        }
                        break;

                    default:
                        foreach ($route_plan as $value) {
                            $journeyHasRoute = new JourneyHasRoutePlan();
                            $journeyHasRoute->id_journey_plan = $data->id;
                            $journeyHasRoute->id_route_plan = $value;
                            $journeyHasRoute->save();
                            break;
                        }
                }
                foreach ($outlet as $ot) {
                    $mapo = MapOutlet::where('id_outlet', $ot)->first();
                    $outact = new OutletActivity();
                    $mbuh = OutletActivity::where('id_map_outlet', $mapo->id)->orderBy('created_date', 'desc')->first();
                    switch ($journeyPlanType) {
                        case 'Tarik':                            
                            $mbuh_outpro = OutletRetractionProgress::where('id_map_outlet', $mapo->id)->where('status_active', 1)->first();
                            $outpro = $mbuh_outpro->replicate();
                            $id_answer = $mbuh_outpro->id_answer;
                            break;

                        default:
                            $mbuh_outpro = OutletProgress::where('id_map_outlet', $mapo->id)->where('status_active', 1)->first();
                            $outpro = new OutletProgress();
                            $id_answer = $mbuh->id_answer;
                            break;
                    }
                    $outact->id = Uuid::uuid4()->toString();
                    $outact->id_map_outlet = $mapo->id;
                    $outact->id_answer =  $id_answer;
                    $outact->is_mandiri = $mapo->is_mandiri;
                    $outact->section = 'driver';
                    $outact->is_deleted = 1;
                    $outact->is_mandiri = $mapo->is_mandiri;
                    $outact->created_date = Carbon::now();
                    $outact->created_by = Auth::user()->id;
                    $outact->save();

                    $outpro->id = Uuid::uuid4()->toString();
                    $outpro->id_outlet_activity = $outact->id;
                    $outpro->status_progress = '7';
                    $outpro->id_map_outlet = $mapo->id;
                    $outpro->id_answer = $id_answer;
                    $outpro->status_active = 1;
                    $outpro->created_date = Carbon::now();
                    $outpro->created_at = Carbon::now()->unix();
                    $outpro->updated_at = Carbon::now()->unix();
                    $outpro->created_by = Auth::user()->id;
                    $outpro->section = 'driver';
                    $outpro->recommend_date = $mbuh_outpro->recommend_date;
                    $outpro->send_date = $mbuh_outpro->send_date;
                    $outpro->save();
                    $mbuh_outpro->status_active = 2;
                    $mbuh_outpro->save();
                }
                DB::commit();
                $this->flashMessage('success', 'CREATE', 'Add data success');
                return Redirect::to(route('delivery.index'));
            } catch (\Exception $e) {
                DB::rollback();
                dd($e);

                Log::error($e);
                $this->flashMessage('error', 'CREATE', $e->getMessage());
                return Redirect::to(route('delivery.create'));
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, $type)
    {
        switch ($type) {
            case 'tarik':
                $journeyPlan = RetractionJourneyPlan::find($id);
                break;
            case 'deploy':
                $journeyPlan = JourneyPlan::find($id);
                break;
            default:
                $journeyPlan = JourneyPlan::find($id);
                break;
        }
        $provinces = Province::all()->pluck('name', 'id');
        $journeyPlan->journeyRoute->map(function ($value) {
            $value->route_plan = $value->id . '_' . $value->id_outlet;
        });
        $ret = [
            'data' => $journeyPlan,
            'provinces' => $provinces,
            'route' => route('delivery.store'),
            'type' => $type,
        ];
        return view('Warehouse/Delivery/edit', $ret);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id, $type)
    {        
        $request->merge(['journey_plan_type' => $type]);
        $validator = Validator::make($request->all(), $this->rules);
        if ($validator->fails()) {
            return Redirect::to(route('delivery.edit', ['delivery' => $id, 'type' => $type]))
                ->withErrors($validator)->withInput($request->all());
        } else {
            DB::beginTransaction();
            try {
                for ($i = 0; $i < count($request->route_plan); $i++) {
                    $ex = explode('_', $request->route_plan[$i]);
                    $route_plan[] = $ex[0];
                    $outlet[] = $ex[1];
                }
                switch ($type) {
                    case 'tarik':
                        $data = RetractionJourneyPlan::find($id);
                        break;

                    case 'tukar':
                        $data = RedeploymentJourneyPlan::find($id);
                        break;

                    case 'deploy':
                        $data = JourneyPlan::find($id);
                        break;

                    default:
                        break;
                }

                if (!(Auth::user()->getPermissionByName('delivery.update') && (strtotime('today') <= strtotime($data->end_date) || strtotime('today') > strtotime($data->end_date)) && ($data->plan_status != 'canceled'))) {
                    $this->flashMessage('error', 'UPDATE', 'Invalid Journey Plan State');
                    return redirect()
                        ->route('delivery.index');
                }

                $data->name = $request->name;
                $data->assigner = Auth::user()->id;
                $data->assign_to = $request->driver;
                $data->id_province = $request->provinces;
                $data->id_city = $request->cities;
                $data->id_vehicle = $request->vehicle;
                $data->start_date = date('Y-m-d H:i:s.u', strtotime($request->date));
                $data->end_date = date('Y-m-d H:i:s.u', strtotime($request->date));
                $data->is_deleted = 1;
                $data->plan_status = 'ongoing';
                $data->updated_at = Carbon::now()->unix();
                $data->updated_by = Auth::user()->id;
                $data->save();
                $data->journeyRoute()->sync($route_plan);

                DB::commit();
                $this->flashMessage('success', 'UPDATE', 'Update data success');
                return Redirect::to(route('delivery.index'));
            } catch (\Exception $e) {
                DB::rollback();
                Log::error($e);
                $this->flashMessage('error', 'UPDATE', $e->getMessage());
                return Redirect::to(route('delivery.edit', ['delivery' => $id, 'type' => $type]));
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
        //
    }

    public function exportPDF(Request $request)
    {
        $relation = [
            'journeyRoute',
            'journeyRoute.deliveryOrders',
            'journeyRoute.outlet',
            'journeyRoute.outlet.juragan',
            'journeyRoute.cabinet'
        ];
        $id = $request->get('id');
        $type = $request->get('type');
        
        switch ($type) {
            case 'Tarik':
                $header = RetractionJourneyPlan::with(['journeyRoute', 'journeyRoute.deliveryOrders', 'driver', 'vehicle'])->where('id', $id)->first();
                $data = RetractionJourneyPlan::with($relation)->where('id', $id);
                break;

            default:
                $header = JourneyPlan::with(['journeyRoute', 'journeyRoute.deliveryOrders', 'driver', 'vehicle'])->where('id', $id)->first();
                $data = JourneyPlan::with($relation)->where('id', $id);
                break;
        }

        $data = $data->get();
        $datas = [
            'header' => $header,
            'data' => $data
        ];

        $pdf = PDF::setOptions([
            'dpi' => 50,
            'defaultFont' => 'sans-serif',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'debugCss' => false
        ])->loadView('Warehouse.Delivery.export', $datas)->setPaper('a4', 'landscape');
    
        return $pdf->download('ADR00' . $header->journeyRoute[0]->deliveryOrders->adr . '.pdf');
    }

    public function exportActivity()
    {
        $request = Request::capture();
        $fromDate = Carbon::parse($request->query('fromDate', Carbon::now()->format('Y-m-d')));
        $toDate = Carbon::parse($request->query('toDate', Carbon::now()->format('Y-m-d')));
        $file_name = sprintf('driver-activity-%s.xlsx', Str::uuid()->toString());
        $query = http_build_query([
            'from_date' => $fromDate->format('Y-m-d'),
            'to_date' => $toDate->format('Y-m-d')
        ]);
        dd(config('app.export_host') . '/driver/activity?' . $query);
        return response()->streamDownload(function () use ($query) {
            echo file_get_contents(config('app.export_host') . '/driver/activity?' . $query);
        }, $file_name);
    }
}
