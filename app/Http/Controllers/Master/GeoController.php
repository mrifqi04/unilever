<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Driver\Drivers;
use App\Models\Driver\Vehicles;
use App\Models\OutletManagement\Outlet;
use App\Models\JuraganManagement\Juragan;
use App\Models\Warehouse\WarehouseManagement;
use App\Models\WarehouseMapping\JuraganToWarehouseManagement;
use App\Models\WarehouseMapping\DriverToWarehouseManagement;
use App\Models\Warehouse\Cabinets;
use App\Models\Warehouse\RoutePlan;
use App\Models\Warehouse\RetractionRoutePlan;
use App\Models\Warehouse\RedeploymentRoutePlan;
use Carbon\Carbon;
use Auth;
use DB;
use Str;
use Illuminate\Http\Request;
use App\Models\Province;
use App\Models\City;
use App\Models\District;
use App\Models\OutletManagement\OutletRetractionProgress;
use App\Models\Village;
use Illuminate\Routing\Route;

class GeoController extends Controller
{

    public function province()
    {
        $users = new Province();
        $data = $users->all();

        return response()->json($data);
    }

    public function city(Request $request)
    {
        $users = new City();
        $data = $users->where("id_province", $request->get("province_id"))->get();
        return response()->json($data);
    }

    public function district(Request $request)
    {
        $users = new District();
        $data = $users->where("id_city", $request->get("city_id"))->get();
        return response()->json($data);
    }

    public function village(Request $request)
    {
        $users = new Village();
        $data = $users->where("id_district", $request->get("district_id"))->get();
        return response()->json($data);
    }

    public function driver(Request $request)
    {
        // Not done, still bug
        $list_juragan_outlet = $this->checkMapping();
        $is_selected_admin = $list_juragan_outlet['is_selected_admin'];
        $current_driver = $list_juragan_outlet['current_driver'];
        $super_admin = $list_juragan_outlet['super_admin'];
        if (($is_selected_admin == true)) {
            $user = Drivers::whereIn('id', $current_driver);
            if ($request->is_with_city == 1) {
                $user->where("id_city", $request->city_id);
            }
            $users = $user->get();
        } elseif (($is_selected_admin == false) && ($super_admin->name == 'SUPER ADMIN')) {
            $user = new Drivers;
            if ($request->is_with_city == 1) {
                $user->where("id_city", $request->city_id);
            }
            $users = $user::get();
        } elseif (($is_selected_admin == false) && ($super_admin->name == 'Admin Warehouse')) {
            $users = '';
        } else {
            $users = '';
        }
        $data = $users;
        return response()->json($data);
    }

    public function vehicle(Request $request)
    {
        $list_juragan_outlet = $this->checkMapping();
        $is_selected_admin = $list_juragan_outlet['is_selected_admin'];
        $current_vehicle = $list_juragan_outlet['current_vehicle'];
        $super_admin = $list_juragan_outlet['super_admin'];
        $users = new Vehicles();
        if (($is_selected_admin == true)) {
            if ($request->is_with_city == 1) {
                $user->where("id_city", $request->city_id);
            }
            $data = $users->whereIn('id', $current_vehicle)->get(['id', 'license_number']);
        } elseif (($is_selected_admin == false) && ($super_admin->name == 'SUPER ADMIN')) {
            if ($request->is_with_city == 1) {
                $user->where("id_city", $request->city_id);
            }
            $data = $users->where("id_city", $request->city_id)->get(['id', 'license_number']);
        } elseif (($is_selected_admin == false) && ($super_admin->name == 'Admin Warehouse')) {
            $data = '';
        } else {
            $data = '';
        }
        return response()->json($data);
    }

    public function outlet(Request $request)
    {
        $list_juragan_outlet = $this->checkMapping();
        $is_selected_admin = $list_juragan_outlet['is_selected_admin'];
        $current_juragan = $list_juragan_outlet['current_juragan'];
        $super_admin = $list_juragan_outlet['super_admin'];
        $start = date('Y-m-d', strtotime($request->get("start")));
        $end = date('Y-m-d', strtotime($request->get("end")));
        $users = new Outlet();
        $users = $users->whereHas('mapOutlet.outletProgress', function ($query) use ($start, $end) {
            $query->where('status_active', 1);
            $query->where('section', 'uli');
            $query->where('status_progress', '5');
            // $query->whereDate('send_date', '>=', $start);
            // $query->whereDate('send_date', '<=', $end);
            $query->select();
        });

        if (($is_selected_admin == true)) {
            $data = $users->where("id_city", $request->city_id)->whereIn('id_juragan', $current_juragan)->get();
        } elseif (($is_selected_admin == false) && ($super_admin->name == 'SUPER ADMIN')) {
            $data = $users->where("id_city", $request->city_id)->get();
        } elseif (($is_selected_admin == false) && ($super_admin->name == 'Admin Warehouse')) {
            $data = '';
        } else {
            $data = '';
        }
        return response()->json($data);
    }

    public function cabinet()
    {
        $request = Request::capture();
        $id = $request->get('id', '');
        $qrcode = $request->get('qrcode', '');
        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 0);

        $query = new Cabinets();
        if ($id !== '') {
            $query = $query->where('id', 'ilike', '%' . $id . '%');
        } elseif ($qrcode !== '') {
            $query = $query->where('qrcode', 'ilike', '%' . $qrcode . '%');
        }
        if ($perPage > 0) {
            $data = $query->paginate($perPage, $columns = ['id', 'qrcode', 'brand', 'qrcode_by_auditor'], 'page', $page);
        } else {
            $data = $query->all(['id', 'qrcode', 'brand', 'qrcode_by_auditor']);
        }

        return response()->json($data);
    }

    public function cabinetByOutlet()
    {
        $request = Request::capture();
        $outletId = $request->get('outlet_id', '');
        $outlet = Outlet::where('id', $outletId)->whereHas('Cabinets')->with('Cabinets:id,qrcode,qrcode_by_auditor,brand')->first();
        $data = ($outlet) ? $outlet->Cabinets->unique('id') : [];
        return response()->json($data);
    }

    public function route_plan(Request $request)
    {
        $list_juragan_outlet = $this->checkMapping();
        $is_selected_admin = $list_juragan_outlet['is_selected_admin'];
        $current_juragan = $list_juragan_outlet['current_juragan'];
        $super_admin = $list_juragan_outlet['super_admin'];
        $relation = [
            'deliveryOrders',
            'outlet'
        ];
        $date = date('Y-m-d', strtotime($request->get("date")));
        $idJourneyPlan = $request->get('id_journey_plan', '');
        $type = $request->get('type', '');
        switch ($type) {
            case 'Tarik':
                $data = new RetractionRoutePlan();
                break;

            case 'Tukar':
                $data = new RedeploymentRoutePlan();
                break;

            default:
                $data = new RoutePlan();
                break;
        }

        $data = $data->with($relation);
        $data->where('is_deleted', 1);
        switch ($type) {
            case 'Tarik':
                $data->whereDate('start_date', $date);
                break;
            case 'Tukar':
                $data->whereDate('start_date', $date);
                break;
            default:
                break;
        }
        // $data = $data->whereDate('end_date', '<=', $date);
        $data = $data->whereDoesntHave('journeyRoute', function ($query) use ($idJourneyPlan) {
            $query->where('plan_status', '!=', 'canceled');
            if ($idJourneyPlan) {
                $query->where('id_journey_plan', '!=', $idJourneyPlan);
            }
        });
        if (($is_selected_admin == true)) {
            $data = $data->whereHas('outlet', function ($query) use ($request) {
                $query->where('id_city', '=', $request->get("city_id"));
                $query->whereIn('id_juragan', $this->checkMapping()['current_juragan']);
            });
        } elseif (($is_selected_admin == false) && ($super_admin->name == 'SUPER ADMIN')) {
            $data = $data->whereHas('outlet', function ($query) use ($request) {
                $query->where('id_city', '=', $request->get("city_id"));
            });
        } elseif (($is_selected_admin == false) && ($super_admin->name == 'Admin Warehouse')) {
            $data = '';
        } else {
            $data = '';
        }
        $data = $data->get();
        //        $data = new City();
        //        $data = $data->with($relation);
        //        $data = $data->whereHas('deliveryOrders.routePlan', function ($query) use ($date){
        //            $query->whereDate('start_date', '>=', $date);
        //            $query->whereDate('start_date', '<=', $date);
        //        });
        //        $data = $data->whereDoesntHave('deliveryOrders.routePlan.journeyRoute');
        //        $data = $data->where("id", $request->get("city_id"))->first();
        return response()->json($data);
    }

    public function checkMapping_old()
    {
        $authId = Auth::id();
        $getUserRole = Auth::user()->with('roles')->where('id', $authId)->first();
        $super_admin = $getUserRole->roles[0];
        $juraganToWarehouse = JuraganToWarehouseManagement::join('warehouse.warehouse_managements', 'juragan.juragan_to_warehouses.id_warehouse_management', 'warehouse.warehouse_managements.id')
            ->join('driver.driver_to_warehouses', 'juragan.juragan_to_warehouses.id_warehouse_management', 'driver.driver_to_warehouses.id_warehouse_management')
            ->join('warehouse.vehicle_to_warehouses', 'juragan.juragan_to_warehouses.id_warehouse_management', 'warehouse.vehicle_to_warehouses.id_warehouse_management')
            ->where('juragan.juragan_to_warehouses.is_deleted', 1)
            ->select('warehouse.warehouse_managements.id as wid', 'warehouse.warehouse_managements.warehouse_name', 'warehouse.warehouse_managements.id_warehouse_admins', 'juragan.juragan_to_warehouses.id_juragan_mappings', 'driver.driver_to_warehouses.id_driver_mappings', 'warehouse.vehicle_to_warehouses.id_vehicle_mappings')
            ->get();
        $is_selected_admin = false;
        foreach ($juraganToWarehouse as $juragan) {
            $listAdmins =  explode(",", $juragan->id_warehouse_admins);
            $listJuragans =  explode(",", $juragan->id_juragan_mappings);
            $listDrivers =  explode(",", $juragan->id_driver_mappings);
            $listVehicles =  explode(",", $juragan->id_vehicle_mappings);
            foreach ($listAdmins as $listAdmin) {
                $listAdmin = trim($listAdmin);
                $contains = Str::contains($listAdmin, [$authId]);
                if ($contains) {
                    $is_selected_admin = true;
                    break;
                }
            }
            foreach ($listJuragans as $listJuragan) {
                $listJuragan = trim($listJuragan);
                $current_juragan[] = $listJuragan;
            }
            foreach ($listDrivers as $listDriver) {
                $listDriver = trim($listDriver);
                $current_driver[] = str_replace('"', "", $listDriver);
            }
            foreach ($listVehicles as $listVehicle) {
                $listVehicle = trim($listVehicle);
                $current_vehicle[] = str_replace('"', "", $listVehicle);
            }
            if ($is_selected_admin) {
                break;
            }
        }
        $data['is_selected_admin'] = $is_selected_admin;
        $data['current_juragan'] = $current_juragan;
        $data['current_driver'] = $current_driver;
        $data['current_vehicle'] = $current_vehicle;
        $data['super_admin'] = $super_admin;
        return $data;
    }

    // done by hana 14/08/2021
    // old function > checkMapping_old()
    public function checkMapping()
    {
        $authId = Auth::id();
        $getUserRole = Auth::user()->with('roles')->where('id', $authId)->first();
        $super_admin = $getUserRole->roles[0];
        $warehouse  = WarehouseManagement::where('id_warehouse_admins', 'ilike', '%' . $authId . '%')->where('is_deleted', 1)->with(['juragans', 'drivers', 'vehicles'])->get();
        $juragans   = $warehouse->pluck('juragans')->flatten();
        $idJuragans = $juragans->pluck('id_juragan_mappings')->flatten()->implode(',');
        $drivers   = $warehouse->pluck('drivers')->flatten();
        $idDrivers = $drivers->pluck('id_driver_mappings')->flatten()->implode(',');
        $vehicles   = $warehouse->pluck('vehicles')->flatten();
        $idVehicles = $vehicles->pluck('id_vehicle_mappings')->flatten()->implode(',');

        $data['is_selected_admin'] = $warehouse->isNotEmpty() ? true : false;
        $data['current_juragan'] = ($idJuragans) ? explode(',', $idJuragans) : [];
        $data['current_driver'] = ($idDrivers) ? explode(',', $idDrivers) : [];
        $data['current_vehicle'] = ($idVehicles) ? explode(',', $idVehicles) : [];
        $data['super_admin'] = $super_admin;
        return $data;
    }

    // public function outletRetraction(Request $request)
    // {
    //     // $list_juragan_outlet = $this->checkMapping();
    //     // $is_selected_admin   = $list_juragan_outlet['is_selected_admin'];
    //     // $current_juragan     = $list_juragan_outlet['current_juragan'];
    //     // $super_admin         = $list_juragan_outlet['super_admin'];
    //     $date_tarik               = date('Y-m-d', strtotime($request->get("date_tarik")));
    //     // $end                 = date('Y-m-d', strtotime($request->get("end")));
    //     $users               = new Outlet();
    //     // $users               = $users->whereHas('mapOutlet.outletRetractionProgress', function ($query) use ($start, $end) {
    //     $id_route_plan       = $request->get("id_route_plan", '');
    //     // cek routeplan berdasarkan tanggal penarikan = tgl yang ada di approval tarik kabinet tgl jadwal tarik
    //     $users               = $users->whereHas('Cabinets')->whereHas('mapOutlet.outletRetractionProgress', function ($query) use ($date_tarik, $id_route_plan) {
    //         $query->where('status_active', 1);
    //         $query->where('section', 'callcenter');
    //         $query->where('status_progress', '1');
    //         if ($id_route_plan) {
    //             $query->whereNotIn(
    //                 'id_outlet',
    //                 RetractionRoutePlan::where('plan_status', 'ongoing')
    //                     ->where('is_deleted', 1)
    //                     ->where('id', '!=', $id_route_plan)
    //                     ->get()
    //                     ->pluck('id_outlet')
    //             );
    //         } else {
    //             $query = OutletRetractionProgress::where('section', 'uli')->get();
    //             // $query->whereNotIn('id_outlet', 
    //             //     RetractionRoutePlan::where('plan_status', 'ongoing')
    //             //         ->where('is_deleted', 1)
    //             //         ->get()
    //             //         ->pluck('id_outlet')
    //             // );
    //         }
    //         // $query->whereDate('send_date', '=', $date_tarik);
    //         // $query->whereDate('send_date', '<=', $end);
    //         // $query->select();
    //     });

    //     // if (($is_selected_admin == true)) {
    //     //     $data = $users->where("id_city", $request->city_id)->whereIn('id_juragan', $current_juragan)->get();
    //     // } elseif (($is_selected_admin == false) && ($super_admin->name == 'SUPER ADMIN')) {
    //     //     $data = $users->where("id_city", $request->city_id)->get();
    //     // } elseif (($is_selected_admin == false) && ($super_admin->name == 'Admin Warehouse')) {
    //     //     $data = '';
    //     // } else {
    //     //     $data = '';
    //     // }

    //     $data = $users->get();


    //     return response()->json($data);
    // }

    public function outletRetraction(Request $request)
    {
        $list_juragan_outlet = $this->checkMapping();
        $is_selected_admin   = $list_juragan_outlet['is_selected_admin'];
        $current_juragan     = $list_juragan_outlet['current_juragan'];
        $super_admin         = $list_juragan_outlet['super_admin'];
        $date_tarik               = date('Y-m-d', strtotime($request->get("date_tarik")));
        // $end                 = date('Y-m-d', strtotime($request->get("end")));
        $users               = new Outlet();
        // $users               = $users->whereHas('mapOutlet.outletRetractionProgress', function ($query) use ($start, $end) {

        // cek routeplan berdasarkan tanggal penarikan = tgl yang ada di approval tarik kabinet tgl jadwal tarik
        $users = $users->whereHas('Cabinets')->whereHas('mapOutlet.outletRetractionProgress', function ($query) use ($date_tarik) {
            $query->where('status_active', 1);
            $query->where('section', 'callcenter');
            $query->where('status_progress', '1');
            // $query = OutletRetractionProgress::where('section', 'uli')->get();            
        });


        if (($is_selected_admin == true)) {
            $users->where("id_city", $request->city_id)->whereIn('id_juragan', $current_juragan)->get();
        } elseif (($is_selected_admin == false) && ($super_admin->name == 'SUPER ADMIN')) {
            $users->where("id_city", $request->city_id)->get();
        } elseif (($is_selected_admin == false) && ($super_admin->name == 'Admin Warehouse')) {
            $data = '';
        } else {
            $data = '';
        }

        $data = $users->get();


        return response()->json($data);
    }
    public function outletRedeployment(Request $request)
    {
        $list_juragan_outlet = $this->checkMapping();
        $is_selected_admin   = $list_juragan_outlet['is_selected_admin'];
        $current_juragan     = $list_juragan_outlet['current_juragan'];
        $super_admin         = $list_juragan_outlet['super_admin'];
        $date_tukar               = date('Y-m-d', strtotime($request->get("date_tukar")));
        // $start               = date('Y-m-d', strtotime($request->get("start")));
        // $end                 = date('Y-m-d', strtotime($request->get("end")));
        $id_route_plan       = $request->get("id_route_plan", '');
        $users               = new Outlet();
        // $users               = $users->whereHas('mapOutlet.outletRedeploymentProgress', function ($query) use ($start, $end) {
        $users               = $users->whereHas('mapOutlet.outletRedeploymentProgress', function ($query) use ($date_tukar, $id_route_plan) {
            $query->where('status_active', 1);
            $query->where('section', 'callcenter');
            $query->where('status_progress', '1');
            if ($id_route_plan) {
                $query->whereNotIn(
                    'id_outlet',
                    RedeploymentRoutePlan::where('plan_status', 'ongoing')
                        ->where('is_deleted', 1)
                        ->where('id', '!=', $id_route_plan)
                        ->get()
                        ->pluck('id_outlet')
                );
            } else {
                $query->whereNotIn(
                    'id_outlet',
                    RedeploymentRoutePlan::where('plan_status', 'ongoing')
                        ->where('is_deleted', 1)
                        ->get()
                        ->pluck('id_outlet')
                );
            }
            $query->whereDate('send_date', '=', $date_tukar);
            // $query->whereDate('send_date', '<=', $end);
            $query->select();
        });
        if (($is_selected_admin == true)) {
            $data = $users->where("id_city", $request->city_id)->get();
        } elseif (($is_selected_admin == false) && ($super_admin->name == 'SUPER ADMIN')) {
            $data = $users->where("id_city", $request->city_id)->get();
        } elseif (($is_selected_admin == false) && ($super_admin->name == 'Admin Warehouse')) {
            $data = $users->where("id_city", $request->city_id)->get();
        } else {
            $data = '';
        }
        return response()->json($data);
    }

    public function outletSubstitute(Request $request)
    {
        $outlet = new Outlet();
        $primaryTable = $outlet->getModel()->getTable();
        $outlet = $outlet
            ->whereDoesntHave('Cabinets')
            ->where('is_substitute', true);

        if ($idJuragan = $request->get('id_juragan')) {
            $outlet->where('id_juragan', $idJuragan);
        }

        // ambil outlet yg tidak dalam proses tukar mandiri
        $outlet->leftJoin(DB::raw('(SELECT tdm.destination_outlet_id
                FROM transactions.transactions t, transactions.transaction_detail_mandiri tdm 
                WHERE t.id = tdm.transaction_id AND t.status_id NOT IN (6, 7)
            ) as tdm'), function ($join) use ($primaryTable) {
            $join->on('tdm.destination_outlet_id', '=', $primaryTable . '.id');
        });
        $outlet->whereNull('tdm.destination_outlet_id');

        $data = $outlet->get(['id', 'name']);
        return response()->json($data);
    }

    public function allOutlet(Request $request)
    {
        $query = new Outlet();
        if ($search = $request->get('search', null)) {
            $query = $query->where('id', 'ilike', '%' . $search . '%')
                ->orWhere('name', 'ilike', '%' . $search . '%');
        }

        $data = $query->get(['id', 'name']);
        return response()->json($data);
    }
}
