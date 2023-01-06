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
    public function index(Request $request) {
        if(empty($request)) {
            $data['datas'] = DriverToWarehouseManagement::
                        join('warehouse.warehouse_managements', 'driver.driver_to_warehouses.id_warehouse_management', 'warehouse.warehouse_managements.id')
                        ->where('driver.driver_to_warehouses.is_deleted', 1)
                        ->select('warehouse.warehouse_managements.id as wid', 'warehouse.warehouse_managements.warehouse_name', 'driver.driver_to_warehouses.*')
                        ->get();
            return view('WarehouseMapping.DriverToWarehouseManagement.index', $data);

        } else {
            $search = trim($request->query('search', ''));
            $contains = Str::contains($search, ['wm', 'WM']);
            if ($contains) {
                $query = DriverToWarehouseManagement::
                join('warehouse.warehouse_managements', 'driver.driver_to_warehouses.id_warehouse_management', 'warehouse.warehouse_managements.id')
                        ->select('warehouse.warehouse_managements.id as wid', 'warehouse.warehouse_managements.warehouse_name', 'driver.driver_to_warehouses.*')
                        ->where('driver.driver_to_warehouses.is_deleted', 1)
                        ->where('warehouse.warehouse_managements.id', substr($search,2));
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

    public function create() {
        $data['warehouseManagements'] = WarehouseManagement::where('is_deleted', 1)->get();
        return view('WarehouseMapping.DriverToWarehouseManagement.create', $data);
    }

    public function edit($id) {
        $data['driverToWarehouse'] = DriverToWarehouseManagement::where('id', $id)->where('is_deleted', 1)->first();
        $data['warehouses'] = WarehouseManagement::get();
        return view('WarehouseMapping.DriverToWarehouseManagement.edit', $data);
    }

    public function store(Request $request) {
        if($request->id_driver_mappings == null) {
            $this->flashMessage('error', 'ERROR', 'Data Juragan Tidak Boleh Kosong');
            return Redirect::to(route('warehouse_management.index'));
        } else {
            try {
                $driverToWarehouse = new DriverToWarehouseManagement;
                $driverToWarehouse->id_warehouse_management = $request->id_warehouse_management;
                $driverToWarehouse->id_driver_mappings = implode(',', $request->input('id_driver_mappings'));
                $driverToWarehouse->submitted_by = Auth::user()->id;
                $driverToWarehouse->driver_total = count(collect($request->id_driver_mappings));
                $driverToWarehouse->created_at = Carbon::now();
                $driverToWarehouse->created_by = Auth::user()->id;
                $driverToWarehouse->save();

                $this->flashMessage('success', 'CREATE', 'Driver to Warehouse Management Created');
                return Redirect::to(route('driver_to_warehouse.index'));
            } catch(Exception $e) {
                $this->flashMessage('error', 'ERROR', $e->getMessage());
                return Redirect::to(route('driver_to_warehouse.index'));
            }
        }
        return view('WarehouseMapping.DriverToWarehouseManagement.create');
    }

    public function update(Request $request) {
        if($request->id_driver_mappings == null) {
            $this->flashMessage('error', 'ERROR', 'Data Juragan Tidak Boleh Kosong');
            return Redirect::to(route('driver_to_warehouse.index'));
        } else {
            try {
                $driverToWarehouse = DriverToWarehouseManagement::where('id', $request->id_driver_to_warehouse)->where('is_deleted', 1)->first();
                $driverToWarehouse->id_warehouse_management = $request->id_warehouse_management;
                $driverToWarehouse->id_driver_mappings = implode(',', $request->input('id_driver_mappings'));
                $driverToWarehouse->updated_by = Auth::user()->id;
                $driverToWarehouse->driver_total = count(collect($request->id_driver_mappings));
                $driverToWarehouse->updated_at = Carbon::now();
                $driverToWarehouse->save();

                $this->flashMessage('success', 'UPDATE', 'Driver to Warehouse Management Updated');
                return Redirect::to(route('driver_to_warehouse.index'));
            } catch(Exception $e) {
                $this->flashMessage('error', 'ERROR', $e->getMessage());
                return Redirect::to(route('driver_to_warehouse.index'));
            }
        }
    }

    public function show($id) {
        $data['driverToWarehouse'] = DriverToWarehouseManagement::where('id', $id)->where('is_deleted', 1)->first();
        $driver_id = '';
            $listDrivers =  explode(",", $data['driverToWarehouse']['id_driver_mappings']);
            foreach($listDrivers as $listDriver) {
                $listDriver = trim($listDriver);
                $driver_id = $listDriver;

                $driver = Drivers::find($driver_id);
                $current_driver[] = $driver_id;
            }
        $data['current_driver'] = $current_driver;
        return view('WarehouseMapping.DriverToWarehouseManagement.show', $data);
    }

    public function delete($id) {
        $data = DriverToWarehouseManagement::where('id', $id)->where('is_deleted', 1)->first();
        $data->is_deleted = 2;
        $data->save();
        $this->flashMessage('success', 'DELETED', 'Driver to Warehouse Management Deleted');
        return Redirect::to(route('driver_to_warehouse.index'));
    }

    public function getDriver() {
        $drivers = Drivers::get();
        $output = '';
        foreach ($drivers as $key => $reg) {

            $output .= '<tr>' .
                '<td><input type="checkbox" id="val'."$key".'" name="id_driver_mappings[]" value='."$reg->id".'>&nbsp;</td>' .
                '<td>' . $reg->id_unilever . '</td>' .
                '<td>' . $reg->name . '</td>' .
                '<td>' . $reg->email . '</td>' .
                '<td>' . $reg->phone . '</td>' .
                '</tr>';
        }
        return response()->json($output);
    }

    public function getWarehouseName($id) {
        $data['warehouse'] = WarehouseManagement::find($id);
        return $data;
    }
}