<?php

namespace App\Http\Controllers\WarehouseMapping;

use App\Http\Controllers\GenericController;
use App\Models\UserManagement\Role;
use App\Models\UserManagement\User;
use App\Models\JuraganManagement\Juragan;
use App\Models\WarehouseMapping\JuraganToWarehouseManagement;
use App\Models\Warehouse\WarehouseManagement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use Auth;
use Str;

class JuraganToWarehouseManagementController extends GenericController
{
    public function index(Request $request) {
        if(empty($request)) {
            $data['datas'] = JuraganToWarehouseManagement::
                        join('warehouse.warehouse_managements', 'juragan.juragan_to_warehouses.id_warehouse_management', 'warehouse.warehouse_managements.id')
                        ->where('juragan.juragan_to_warehouses.is_deleted', 1)
                        ->select('warehouse.warehouse_managements.id as wid', 'warehouse.warehouse_managements.warehouse_name', 'juragan.juragan_to_warehouses.*')
                        ->get();
            return view('WarehouseMapping.JuraganToWarehouseManagement.index', $data);

        } else {
            $search = trim($request->query('search', ''));
            $contains = Str::contains($search, ['wm', 'WM']);
            if ($contains) {
                $query = JuraganToWarehouseManagement::
                join('warehouse.warehouse_managements', 'juragan.juragan_to_warehouses.id_warehouse_management', 'warehouse.warehouse_managements.id')
                        ->select('warehouse.warehouse_managements.id as wid', 'warehouse.warehouse_managements.warehouse_name', 'juragan.juragan_to_warehouses.*')
                        ->where('juragan.juragan_to_warehouses.is_deleted', 1)
                        ->where('warehouse.warehouse_managements.id', substr($search,2));
            } else {
                $query = JuraganToWarehouseManagement::join('warehouse.warehouse_managements', 'juragan.juragan_to_warehouses.id_warehouse_management', 'warehouse.warehouse_managements.id')
                        ->select('warehouse.warehouse_managements.id as wid', 'warehouse.warehouse_managements.warehouse_name', 'juragan.juragan_to_warehouses.*')
                        ->where('juragan.juragan_to_warehouses.is_deleted', 1)
                        ->where('warehouse.warehouse_managements.warehouse_name', 'ILIKE', '%' . $search . '%');
            }
        }
        $data['datas'] = $query->paginate(30);
        return view('WarehouseMapping.JuraganToWarehouseManagement.index', $data);
    }

    public function create() {
        $data['warehouseManagements'] = WarehouseManagement::where('is_deleted', 1)->get();
        return view('WarehouseMapping.JuraganToWarehouseManagement.create', $data);
    }

    public function edit($id) {
        $data['juraganToWarehouse'] = JuraganToWarehouseManagement::where('id', $id)->where('is_deleted', 1)->first();
        $data['warehouses'] = WarehouseManagement::get();
        return view('WarehouseMapping.JuraganToWarehouseManagement.edit', $data);
    }

    public function store(Request $request) {
        if($request->id_juragan_mappings == null) {
            $this->flashMessage('error', 'ERROR', 'Data Juragan Tidak Boleh Kosong');
            return Redirect::to(route('warehouse_management.index'));
        } else {
            try {
                $juraganToWarehouse = new JuraganToWarehouseManagement;
                $juraganToWarehouse->id_warehouse_management = $request->id_warehouse_management;
                $juraganToWarehouse->id_juragan_mappings = implode(',', $request->input('id_juragan_mappings'));
                $juraganToWarehouse->submitted_by = Auth::user()->id;
                $juraganToWarehouse->juragan_total = count(collect($request->id_juragan_mappings));
                $juraganToWarehouse->created_at = Carbon::now();
                $juraganToWarehouse->created_by = Auth::user()->id;
                $juraganToWarehouse->save();

                $this->flashMessage('success', 'CREATE', 'Juragan to Warehouse Management Created');
                return Redirect::to(route('juragan_to_warehouse.index'));
            } catch(Exception $e) {
                $this->flashMessage('error', 'ERROR', $e->getMessage());
                return Redirect::to(route('juragan_to_warehouse.index'));
            }
        }
        return view('WarehouseMapping.JuraganToWarehouseManagement.create');
    }

    public function update(Request $request) {
        if($request->id_juragan_mappings == null) {
            $this->flashMessage('error', 'ERROR', 'Data Juragan Tidak Boleh Kosong');
            return Redirect::to(route('juragan_to_warehouse.index'));
        } else {
            try {
                $juraganToWarehouse = JuraganToWarehouseManagement::where('id', $request->id_juragan_to_warehouse)->where('is_deleted', 1)->first();
                $juraganToWarehouse->id_warehouse_management = $request->id_warehouse_management;
                $juraganToWarehouse->id_juragan_mappings = implode(',', $request->input('id_juragan_mappings'));
                $juraganToWarehouse->updated_by = Auth::user()->id;
                $juraganToWarehouse->juragan_total = count(collect($request->id_juragan_mappings));
                $juraganToWarehouse->updated_at = Carbon::now();
                $juraganToWarehouse->save();

                $this->flashMessage('success', 'UPDATE', 'Juragan to Warehouse Management Updated');
                return Redirect::to(route('juragan_to_warehouse.index'));
            } catch(Exception $e) {
                $this->flashMessage('error', 'ERROR', $e->getMessage());
                return Redirect::to(route('juragan_to_warehouse.index'));
            }
        }
    }

    public function show($id) {
        $data['juraganToWarehouse'] = JuraganToWarehouseManagement::where('id', $id)->where('is_deleted', 1)->first();
        $admin_id = '';
            $listJuragans =  explode(",", $data['juraganToWarehouse']['id_juragan_mappings']);
            foreach($listJuragans as $listJuragan) {
                $listJuragan = trim($listJuragan);
                $juraganId = $listJuragan;

                $juragan = Juragan::find($juraganId);
                $current_juragan[] = $juraganId;
            }
        $data['current_juragan'] = $current_juragan;
        return view('WarehouseMapping.JuraganToWarehouseManagement.show', $data);
    }

    public function delete($id) {
        $data = JuraganToWarehouseManagement::where('id', $id)->where('is_deleted', 1)->first();
        $data->is_deleted = 2;
        $data->save();
        $this->flashMessage('success', 'DELETED', 'Juragan to Warehouse Management Deleted');
        return Redirect::to(route('juragan_to_warehouse.index'));
    }

    public function getJuragan() {
        $juragans = Juragan::get();
        $output = '';
        foreach ($juragans as $key => $reg) {

            $output .= '<tr>' .
                '<td><input type="checkbox" id="val'."$key".'" name="id_juragan_mappings[]" value='."$reg->id".'>&nbsp;</td>' .
                '<td>' . $reg->id . '</td>' .
                '<td>' . $reg->id_unilever_owner . '</td>' .
                '<td>' . $reg->name . '</td>'.
                '<td>' . $reg->email . '</td>'.
                '<td>' . $reg->phone . '</td>'.
                '</tr>';
        }
        return response()->json($output);
    }

    public function getWarehouseName($id) {
        $data['warehouse'] = WarehouseManagement::find($id);
        return $data;
    }
}