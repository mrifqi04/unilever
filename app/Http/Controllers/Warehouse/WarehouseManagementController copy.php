<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\GenericController;
use App\Models\UserManagement\Role;
use App\Models\UserManagement\User;
use App\Models\Warehouse\WarehouseManagement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use Auth;
use Str;

class WarehouseManagementController extends GenericController
{
    public function index(Request $request) {
        if(empty($request)) {
            $role = Role::with('users')->get();
            $data['datas'] = WarehouseManagement::where('is_deleted', 1)->get();
            return view('Warehouse.WarehouseManagement.index', $data);

        } else {
            $search = trim($request->query('search', ''));
            $contains = Str::contains($search, ['wm', 'WM']);
            if ($contains) {
                $query = WarehouseManagement::where('is_deleted', 1)->where('id', substr($search,2));
            } else {
                $query = WarehouseManagement::where('is_deleted', 1)->where('warehouse_name', 'ILIKE', '%' . $search . '%');
            }
        }
        $data['datas'] = $query->paginate(30);
        return view('Warehouse.WarehouseManagement.index', $data);
    }

    public function create() {
        return view('Warehouse.WarehouseManagement.create');
    }

    public function edit($id) {
        $data['warehouseManagement'] = WarehouseManagement::where('id', $id)
            ->where('is_deleted', 1)
            ->first();
        return view('Warehouse.WarehouseManagement.edit', $data);
    }

    public function store(Request $request) {
        if($request->id_warehouse_admins == null) {
            $this->flashMessage('error', 'ERROR', 'Data Admin Tidak Boleh Kosong');
            return Redirect::to(route('warehouse_management.index'));
        } else {
            try {
                $warehouseManagement = new WarehouseManagement;
                $warehouseManagement->warehouse_name = $request->warehouse_name;
                $warehouseManagement->warehouse_description = $request->warehouse_description;
                $warehouseManagement->id_warehouse_admins = implode(',', $request->input('id_warehouse_admins'));
                $warehouseManagement->submitted_by = Auth::user()->id;
                $warehouseManagement->admin_total = count(collect($request->id_warehouse_admins));
                $warehouseManagement->created_at = Carbon::now();
                $warehouseManagement->created_by = Auth::user()->id;
                $warehouseManagement->save();

                $this->flashMessage('success', 'CREATE', 'Warehouse Management Created');
                return Redirect::to(route('warehouse_management.index'));
            } catch(Exception $e) {
                $this->flashMessage('error', 'ERROR', $e->getMessage());
                return Redirect::to(route('warehouse_management.index'));
            }
        }
        return view('Warehouse.WarehouseManagement.create');
    }

    public function update(Request $request) {
        if($request->id_warehouse_admins == null) {
            $this->flashMessage('error', 'ERROR', 'Data Admin Tidak Boleh Kosong');
            return Redirect::to(route('warehouse_management.index'));
        } else {
            try {
                $warehouseManagement = WarehouseManagement::where('id', $request->id_warehouse_management)
                    ->where('is_deleted', 1)
                    ->first();
                $warehouseManagement->warehouse_name = $request->warehouse_name;
                $warehouseManagement->warehouse_description = $request->warehouse_description;
                $warehouseManagement->id_warehouse_admins = implode(',', $request->input('id_warehouse_admins'));
                $warehouseManagement->submitted_by = Auth::user()->id;
                $warehouseManagement->admin_total = count(collect($request->id_warehouse_admins));
                $warehouseManagement->updated_at = Carbon::now();
                $warehouseManagement->updated_by = Auth::user()->id;
                $warehouseManagement->save();

                $this->flashMessage('success', 'UPDATE', 'Warehouse Management Updated');
                return Redirect::to(route('warehouse_management.index'));
            } catch(Exception $e) {
                $this->flashMessage('error', 'ERROR', $e->getMessage());
                return Redirect::to(route('warehouse_management.index'));
            }
        }
        return view('Warehouse.WarehouseManagement.create');
    }

    public function show($id) {
        $data['warehouseManagement'] = WarehouseManagement::where('id', $id)->where('is_deleted', 1)->first();
        $admin_id = '';
            $listAdmins =  explode(",", $data['warehouseManagement']['id_warehouse_admins']);
            foreach($listAdmins as $listAdmin) {
                $listAdmin = trim($listAdmin);
                $admin_id = $listAdmin;

                $admin = User::find($admin_id);
                $current_admin[] = $admin_id;
            }
        $data['current_admin'] = $current_admin;
        return view('Warehouse.WarehouseManagement.show', $data);
    }

    public function delete($id) {
        $warehouse = WarehouseManagement::where('id', $id)
                ->where('is_deleted', 1)
                ->first();
        $warehouse->is_deleted = 2;
        $warehouse->save();
        $this->flashMessage('success', 'DELETED', 'Warehouse Management Deleted');
        return Redirect::to(route('warehouse_management.index'));
    }

    public function getAdminCreate() {
        $role = Role::with('users')->get();
        $admins = $role[3]->users;
        $output = '';
        foreach ($admins as $key => $reg) {

            $output .= '<tr>' .
                '<td><input type="checkbox" id="val'."$key".'" name="id_warehouse_admins[]" value='."$reg->id".'>&nbsp;</td>' .
                '<td>' . $reg->username . '</td>' .
                '<td>' . $reg->name . '</td>'.
                '<td>' . $reg->phone . '</td>'.
                '<td>' . 'Admin Warehouse' . '</td>'.
                '<td>' . $reg->created_at . '</td>'.
                '</tr>';
        }
        return response()->json($output);
    }

    public function getAdminEdit() {
        $role = Role::with('users')->get();
        $admins = $role[3]->users;
        $output = '';
        foreach ($admins as $key => $reg) {

            $output .= '<tr>' .
                '<td><input type="checkbox" id="val'."$key".'" name="id_warehouse_admins[]" value='."$reg->id".'>&nbsp;</td>' .
                '<td>' . $reg->username . '</td>' .
                '<td>' . $reg->name . '</td>'.
                '<td>' . $reg->phone . '</td>'.
                '<td>' . 'Admin Warehouse' . '</td>'.
                '<td>' . $reg->created_at . '</td>'.
                '</tr>';
        }
        return response()->json($output);
    }
}