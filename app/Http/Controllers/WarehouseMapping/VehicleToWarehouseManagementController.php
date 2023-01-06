<?php

namespace App\Http\Controllers\WarehouseMapping;

use App\Http\Controllers\GenericController;
use App\Models\UserManagement\Role;
use App\Models\UserManagement\User;
use App\Models\Driver\Vehicles;
use App\Models\WarehouseMapping\VehicleToWarehouseManagement;
use App\Models\Warehouse\WarehouseManagement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use Auth;
use Str;

class VehicleToWarehouseManagementController extends GenericController
{
    public function index(Request $request)
    {
        if (empty($request)) {
            $data['datas'] = VehicleToWarehouseManagement::join('warehouse.warehouse_managements', 'warehouse.vehicle_to_warehouses.id_warehouse_management', 'warehouse.warehouse_managements.id')
                ->where('warehouse.vehicle_to_warehouses.is_deleted', 1)
                ->select('warehouse.warehouse_managements.id as wid', 'warehouse.warehouse_managements.warehouse_name', 'warehouse.vehicle_to_warehouses.*')
                ->get();
            return view('WarehouseMapping.VehicleToWarehouseManagement.index', $data);
        } else {
            $search = trim($request->query('search', ''));
            $contains = Str::contains($search, ['wm', 'WM']);
            if ($contains) {
                $query = VehicleToWarehouseManagement::join('warehouse.warehouse_managements', 'warehouse.vehicle_to_warehouses.id_warehouse_management', 'warehouse.warehouse_managements.id')
                    ->select('warehouse.warehouse_managements.id as wid', 'warehouse.warehouse_managements.warehouse_name', 'warehouse.vehicle_to_warehouses.*')
                    ->where('warehouse.vehicle_to_warehouses.is_deleted', 1)
                    ->where('warehouse.warehouse_managements.id', substr($search, 2));
            } else {
                $query = VehicleToWarehouseManagement::join('warehouse.warehouse_managements', 'warehouse.vehicle_to_warehouses.id_warehouse_management', 'warehouse.warehouse_managements.id')
                    ->select('warehouse.warehouse_managements.id as wid', 'warehouse.warehouse_managements.warehouse_name', 'warehouse.vehicle_to_warehouses.*')
                    ->where('warehouse.vehicle_to_warehouses.is_deleted', 1)
                    ->where('warehouse.warehouse_managements.warehouse_name', 'ILIKE', '%' . $search . '%');
            }
        }
        $data['datas'] = $query->paginate(30);
        return view('WarehouseMapping.VehicleToWarehouseManagement.index', $data);
    }

    public function create()
    {
        $data['warehouseManagements'] = WarehouseManagement::where('is_deleted', 1)->get();
        return view('WarehouseMapping.VehicleToWarehouseManagement.create', $data);
    }

    public function edit($id)
    {
        $data['vehicleToWarehouse'] = VehicleToWarehouseManagement::where('id', $id)->where('is_deleted', 1)->first();
        $data['warehouses'] = WarehouseManagement::get();
        // return $data;
        return view('WarehouseMapping.VehicleToWarehouseManagement.edit', $data);
    }

    public function store(Request $request)
    {
        // return $request->all();
        if ($request->id_vehicle_mappings == null) {
            $this->flashMessage('error', 'ERROR', 'Data Vehicle Tidak Boleh Kosong');
            return Redirect::to(route('warehouse_management.index'));
        } else {
            try {
                $idVehicleMappings = $request->get('id_vehicle_mappings');
                $vehicleToWarehouse = new VehicleToWarehouseManagement;
                $vehicleToWarehouse->id_warehouse_management = $request->id_warehouse_management;
                $vehicleToWarehouse->id_vehicle_mappings = $idVehicleMappings;
                // $vehicleToWarehouse->id_vehicle_mappings = implode(',', $request->input('id_vehicle_mappings'));
                $vehicleToWarehouse->submitted_by = Auth::user()->id;
                $vehicleToWarehouse->vehicle_total = count(explode(',', $idVehicleMappings));
                // $vehicleToWarehouse->vehicle_total = count(collect($request->id_vehicle_mappings));
                $vehicleToWarehouse->created_at = Carbon::now();
                $vehicleToWarehouse->created_by = Auth::user()->id;
                // return $vehicleToWarehouse;
                $vehicleToWarehouse->save();

                $this->flashMessage('success', 'CREATE', 'Vehicle to Warehouse Management Created');
                return Redirect::to(route('vehicle_to_warehouse.index'));
            } catch (Exception $e) {
                $this->flashMessage('error', 'ERROR', $e->getMessage());
                return Redirect::to(route('vehicle_to_warehouse.index'));
            }
        }
        return view('WarehouseMapping.VehicleToWarehouseManagement.create');
    }

    public function update(Request $request)
    {
        if ($request->id_vehicle_mappings == null) {
            $this->flashMessage('error', 'ERROR', 'Data Juragan Tidak Boleh Kosong');
            return Redirect::to(route('vehicle_to_warehouse.index'));
        } else {
            try {
                $idVehicleMappings = $request->get('id_vehicle_mappings', '');
                $vehicleToWarehouse = VehicleToWarehouseManagement::where('id', $request->id_vehicle_to_warehouse)->where('is_deleted', 1)->first();
                $vehicleToWarehouse->id_warehouse_management = $request->id_warehouse_management;
                $vehicleToWarehouse->id_vehicle_mappings = $idVehicleMappings;
                // $vehicleToWarehouse->id_vehicle_mappings = implode(',', $request->input('id_vehicle_mappings'));
                $vehicleToWarehouse->updated_by = Auth::user()->id;
                $vehicleToWarehouse->vehicle_total = count(explode(',', $idVehicleMappings));
                $vehicleToWarehouse->updated_at = Carbon::now();
                $vehicleToWarehouse->save();

                $this->flashMessage('success', 'UPDATE', 'Vehicle to Warehouse Management Updated');
                return Redirect::to(route('vehicle_to_warehouse.index'));
            } catch (Exception $e) {
                $this->flashMessage('error', 'ERROR', $e->getMessage());
                return Redirect::to(route('vehicle_to_warehouse.index'));
            }
        }
    }

    public function show($id)
    {
        $data['vehicleToWarehouse'] = VehicleToWarehouseManagement::where('id', $id)->where('is_deleted', 1)->first();
        $vehicle_id = '';
        $listVehicles =  explode(",", $data['vehicleToWarehouse']['id_vehicle_mappings']);
        foreach ($listVehicles as $listVehicle) {
            $listVehicle = trim($listVehicle);
            $vehicle_id = $listVehicle;

            $vehicle = Vehicles::find($vehicle_id);
            $current_vehicle[] = $vehicle_id;
        }
        $data['current_vehicle'] = $current_vehicle;
        return view('WarehouseMapping.VehicleToWarehouseManagement.show', $data);
    }

    public function delete($id)
    {
        $data = VehicleToWarehouseManagement::where('id', $id)->where('is_deleted', 1)->first();
        $data->is_deleted = 2;
        $data->save();
        $this->flashMessage('success', 'DELETED', 'Vehicle to Warehouse Management Deleted');
        return Redirect::to(route('vehicle_to_warehouse.index'));
    }

    public function getVehicleOld()
    {
        $vehicles = Vehicles::get();
        $output = '';
        foreach ($vehicles as $key => $reg) {

            $output .= '<tr>' .
                '<td><input type="checkbox" id="val' . "$key" . '" name="id_vehicle_mappings[]" value=' . "$reg->id" . '>&nbsp;</td>' .
                '<td>' . $reg->license_number . '</td>' .
                '</tr>';
        }
        return response()->json($output);
    }

    public function getVehicle()
    {
        $request   = Request::capture();
        $isShow    = $request->get('is_show', 0);
        $id        = $request->query('id', '');
        $draw      = $request->query('draw', '');
        $start     = $request->query('start', '');
        $pageSize  = $request->query('length', '');
        $page      = ($start > 0) ? ($start / $pageSize + 1) : 1;
        $keyword   = $request->get('search')['value'];
        $getOrder  = $request->get('order', []);
        $listOrder = ['license_number'];
        // $listOrder = ['id', 'id', 'id_unilever_owner', 'name', 'email', 'phone', 'created_at'];
        $order     = $listOrder[$getOrder[0]['column']];
        $orderType = $getOrder[0]['dir'] ? $getOrder[0]['dir'] : 'asc';

        $query = Vehicles::where('is_deleted', 1)->orderBy($order, $orderType);
        $ownedVehicle = [];
        if ($id) {
            $query = VehicleToWarehouseManagement::whereNotNull('id_vehicle_mappings')->get(['id_vehicle_mappings']);
            if ($isShow == 1) {
                $query->whereIn('id', $ownedVehicle);
            } else {
                $query->whereNotIn('id', $ownedVehicle);
            }
            $vehicle_mappings = $query->get(['id_vehicle_mappings']);
            if ($vehicle_mappings) {
                $ownedVehicle = array_unique(explode(',', $vehicle_mappings->implode('id_vehicle_mappings', ',')));
            }
        }
        if ($isShow == 1) {
            $query->whereIn('id', $ownedVehicle);
        } else {
            $query->whereNotIn('id', $ownedVehicle);
        }
        $total = $query->count();
        $totalFilter = $total;
        if ($keyword) {
            $query->where(function ($query) use ($keyword) {
                $query->orWhereRaw("license_number ilike ?", ["%" . $keyword . "%"]);
            });
            $totalFilter = $query->count();
        }

        $vehicles = $query->paginate($pageSize, ['*'], 'page', $page);
        $output['draw'] = $draw;
        $output['recordsTotal'] = $total;
        $output['recordsFiltered'] = $totalFilter;
        $output['data'] = [];
        $output['ownedVehicle'] = $ownedVehicle;
        foreach ($vehicles as $key => $value) {
            $output['data'][] = [
                '<input type="checkbox" id="val' . "$key" . '" class="id_vehicle" value=' . "$value->id" . '>&nbsp;',
                $value->license_number,
                Carbon::parse($value->created_at)->format("Y-m-d H:i:s"),
            ];
        }
        return response()->json($output);
    }

    public function getWarehouseName($id)
    {
        $data['warehouse'] = WarehouseManagement::find($id);
        return $data;
    }
}
