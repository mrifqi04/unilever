<?php

namespace App\Http\Controllers\WarehouseMapping;

use App\Http\Controllers\GenericController;
use App\Models\UserManagement\Role;
use App\Models\UserManagement\User;
use App\Models\Driver\Drivers;
use App\Models\WarehouseMapping\DriverToWarehouseManagement;
use App\Models\Warehouse\WarehouseManagement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use Auth;
use Str;

class DriverToWarehouseManagementController extends GenericController
{
    public function index(Request $request)
    {
        if (empty($request)) {
            $data['datas'] = DriverToWarehouseManagement::join('warehouse.warehouse_managements', 'driver.driver_to_warehouses.id_warehouse_management', 'warehouse.warehouse_managements.id')
                ->where('driver.driver_to_warehouses.is_deleted', 1)
                ->select('warehouse.warehouse_managements.id as wid', 'warehouse.warehouse_managements.warehouse_name', 'driver.driver_to_warehouses.*')
                ->get();
            return view('WarehouseMapping.DriverToWarehouseManagement.index', $data);
        } else {
            $search = trim($request->query('search', ''));
            $contains = Str::contains($search, ['wm', 'WM']);
            if ($contains) {
                $query = DriverToWarehouseManagement::join('warehouse.warehouse_managements', 'driver.driver_to_warehouses.id_warehouse_management', 'warehouse.warehouse_managements.id')
                    ->select('warehouse.warehouse_managements.id as wid', 'warehouse.warehouse_managements.warehouse_name', 'driver.driver_to_warehouses.*')
                    ->where('driver.driver_to_warehouses.is_deleted', 1)
                    ->where('warehouse.warehouse_managements.id', substr($search, 2));
            } else {
                $query = DriverToWarehouseManagement::join('warehouse.warehouse_managements', 'driver.driver_to_warehouses.id_warehouse_management', 'warehouse.warehouse_managements.id')
                    ->select('warehouse.warehouse_managements.id as wid', 'warehouse.warehouse_managements.warehouse_name', 'driver.driver_to_warehouses.*')
                    ->where('driver.driver_to_warehouses.is_deleted', 1)
                    ->where('warehouse.warehouse_managements.warehouse_name', 'ILIKE', '%' . $search . '%');
            }
        }
        $data['datas'] = $query->paginate(30);
        return view('WarehouseMapping.DriverToWarehouseManagement.index', $data);
    }

    public function create()
    {
        $data['warehouseManagements'] = WarehouseManagement::where('is_deleted', 1)->get();
        return view('WarehouseMapping.DriverToWarehouseManagement.create', $data);
    }

    public function edit($id)
    {
        $data['driverToWarehouse'] = DriverToWarehouseManagement::where('id', $id)->where('is_deleted', 1)->first();
        $data['warehouses'] = WarehouseManagement::get();
        return view('WarehouseMapping.DriverToWarehouseManagement.edit', $data);
    }

    public function store(Request $request)
    {
        if ($request->id_driver_mappings == null) {
            $this->flashMessage('error', 'ERROR', 'Data Juragan Tidak Boleh Kosong');
            return Redirect::to(route('warehouse_management.index'));
        } else {
            try {
                $idDriverMappings = $request->get('id_driver_mappings', '');
                $driverToWarehouse = new DriverToWarehouseManagement;
                $driverToWarehouse->id_warehouse_management = $request->id_warehouse_management;
                $driverToWarehouse->id_driver_mappings = $idDriverMappings;
                // $driverToWarehouse->id_driver_mappings = implode(',', $request->input('id_driver_mappings'));
                $driverToWarehouse->submitted_by = Auth::user()->id;
                $driverToWarehouse->driver_total = count(explode(',', $idDriverMappings));
                $driverToWarehouse->created_at = Carbon::now();
                $driverToWarehouse->created_by = Auth::user()->id;
                $driverToWarehouse->save();

                $this->flashMessage('success', 'CREATE', 'Driver to Warehouse Management Created');
                return Redirect::to(route('driver_to_warehouse.index'));
            } catch (Exception $e) {
                $this->flashMessage('error', 'ERROR', $e->getMessage());
                return Redirect::to(route('driver_to_warehouse.index'));
            }
        }
        return view('WarehouseMapping.DriverToWarehouseManagement.create');
    }

    public function update(Request $request)
    {
        if ($request->id_driver_mappings == null) {
            $this->flashMessage('error', 'ERROR', 'Data Juragan Tidak Boleh Kosong');
            return Redirect::to(route('driver_to_warehouse.index'));
        } else {
            try {
                $idDriverMappings = $request->get('id_driver_mappings', '');
                $driverToWarehouse = DriverToWarehouseManagement::where('id', $request->id_driver_to_warehouse)->where('is_deleted', 1)->first();
                $driverToWarehouse->id_warehouse_management = $request->id_warehouse_management;
                $driverToWarehouse->id_driver_mappings = $idDriverMappings;
                $driverToWarehouse->updated_by = Auth::user()->id;
                $driverToWarehouse->driver_total = count(explode(',', $idDriverMappings));
                $driverToWarehouse->updated_at = Carbon::now();
                $driverToWarehouse->save();

                $this->flashMessage('success', 'UPDATE', 'Driver to Warehouse Management Updated');
                return Redirect::to(route('driver_to_warehouse.index'));
            } catch (Exception $e) {
                $this->flashMessage('error', 'ERROR', $e->getMessage());
                return Redirect::to(route('driver_to_warehouse.index'));
            }
        }
    }

    public function show($id)
    {
        $data['driverToWarehouse'] = DriverToWarehouseManagement::where('id', $id)->where('is_deleted', 1)->first();
        $driver_id = '';
        $listDrivers =  explode(",", $data['driverToWarehouse']['id_driver_mappings']);
        foreach ($listDrivers as $listDriver) {
            $listDriver = trim($listDriver);
            $driver_id = $listDriver;

            $driver = Drivers::find($driver_id);
            $current_driver[] = $driver_id;
        }
        $data['current_driver'] = $current_driver;
        return view('WarehouseMapping.DriverToWarehouseManagement.show', $data);
    }

    public function delete($id)
    {
        $data = DriverToWarehouseManagement::where('id', $id)->where('is_deleted', 1)->first();
        $data->is_deleted = 2;
        $data->save();
        $this->flashMessage('success', 'DELETED', 'Driver to Warehouse Management Deleted');
        return Redirect::to(route('driver_to_warehouse.index'));
    }

    public function getDriverOld()
    {
        $drivers = Drivers::get();
        $output = '';
        foreach ($drivers as $key => $reg) {

            $output .= '<tr>' .
                '<td><input type="checkbox" id="val' . "$key" . '" name="id_driver_mappings[]" value=' . "$reg->id" . '>&nbsp;</td>' .
                '<td>' . $reg->id_unilever . '</td>' .
                '<td>' . $reg->name . '</td>' .
                '<td>' . $reg->email . '</td>' .
                '<td>' . $reg->phone . '</td>' .
                '</tr>';
        }
        return response()->json($output);
    }

    public function getDriver()
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
        $listOrder = ['id', 'id_unilever_owner', 'name', 'email', 'phone', 'created_at'];
        $order     = $listOrder[$getOrder[0]['column']];
        $orderType = $getOrder[0]['dir'] ? $getOrder[0]['dir'] : 'asc';

        $query = Drivers::where('is_deleted', 1)->orderBy($order, $orderType);
        $ownedDriver = [];
        if ($id) {
            $query = DriverToWarehouseManagement::whereNotNull('id_driver_mappings')->get(['id_driver_mappings']);
            if ($isShow == 1) {
                $query->whereIn('id', $ownedDriver);
            } else {
                $query->whereNotIn('id', $ownedDriver);
            }
            $juragan_mappings = $query->get(['id_driver_mappings']);
            if ($juragan_mappings) {
                $ownedDriver = array_unique(explode(',', $juragan_mappings->implode('id_driver_mappings', ',')));
            }
        }
        if ($isShow == 1) {
            $query->whereIn('id', $ownedDriver);
        } else {
            $query->whereNotIn('id', $ownedDriver);
        }
        $total = $query->count();
        $totalFilter = $total;
        if ($keyword) {
            $query->where(function ($query) use ($keyword) {
                $query->WhereRaw("id_unilever ilike ?", ["%" . $keyword . "%"])
                    ->orWhereRaw("name ilike ?", ["%" . $keyword . "%"])
                    ->orWhereRaw("email ilike ?", ["%" . $keyword . "%"])
                    ->orWhereRaw("phone ilike ?", ["%" . $keyword . "%"]);
            });
            $totalFilter = $query->count();
        }
        $driver = $query->paginate($pageSize, ['*'], 'page', $page);
        $output['draw'] = $draw;
        $output['recordsTotal'] = $total;
        $output['recordsFiltered'] = $totalFilter;
        $output['data'] = [];
        $output['ownedDriver'] = $ownedDriver;
        foreach ($driver as $key => $value) {
            $output['data'][] = [
                // '<input type="checkbox" id="val' . "$key" . '" class="id_driver" name="id_driver_mappings[]" value=' . "$value->id" . '>&nbsp;',
                '<input type="checkbox" id="val' . "$key" . '" class="id_driver" value=' . "$value->id" . '>&nbsp;',
                $value->id_unilever,
                $value->name,
                $value->email,
                $value->phone,
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
