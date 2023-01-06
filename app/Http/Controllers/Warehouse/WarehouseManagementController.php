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
    public function index(Request $request)
    {
        if (empty($request)) {
            $role = Role::with('users')->get();
            $data['datas'] = WarehouseManagement::where('is_deleted', 1)->get();
            return view('Warehouse.WarehouseManagement.index', $data);
        } else {
            $search = trim($request->query('search', ''));
            $contains = Str::contains($search, ['wm', 'WM']);
            if ($contains) {
                $query = WarehouseManagement::where('is_deleted', 1)->where('id', substr($search, 2));
            } else {
                $query = WarehouseManagement::where('is_deleted', 1)->where('warehouse_name', 'ILIKE', '%' . $search . '%');
            }
        }
        $data['datas'] = $query->paginate(30);
        return view('Warehouse.WarehouseManagement.index', $data);
    }

    public function create()
    {
        return view('Warehouse.WarehouseManagement.create');
    }

    public function edit($id)
    {
        $data['warehouseManagement'] = WarehouseManagement::where('id', $id)
            ->where('is_deleted', 1)
            ->first();
        return view('Warehouse.WarehouseManagement.edit', $data);
    }

    public function store(Request $request)
    {
        // return $request->all();
        if ($request->id_warehouse_admins == null) {
            $this->flashMessage('error', 'ERROR', 'Data Admin Tidak Boleh Kosong');
            return Redirect::to(route('warehouse_management.index'));
        } else {
            try {
                $id_warehouse_admins = $request->get('id_warehouse_admins');
                $warehouseManagement = new WarehouseManagement;
                $warehouseManagement->warehouse_name = $request->warehouse_name;
                $warehouseManagement->warehouse_description = $request->warehouse_description;
                $warehouseManagement->id_warehouse_admins = $id_warehouse_admins;
                // $warehouseManagement->id_warehouse_admins = implode(',', $request->input('id_warehouse_admins'));
                $warehouseManagement->submitted_by = Auth::user()->id;
                $warehouseManagement->admin_total = count(explode(',', $id_warehouse_admins));
                $warehouseManagement->created_at = Carbon::now();
                $warehouseManagement->created_by = Auth::user()->id;
                // return $warehouseManagement;
                $warehouseManagement->save();

                $this->flashMessage('success', 'CREATE', 'Warehouse Management Created');
                return Redirect::to(route('warehouse_management.index'));
            } catch (Exception $e) {
                $this->flashMessage('error', 'ERROR', $e->getMessage());
                return Redirect::to(route('warehouse_management.index'));
            }
        }
        return view('Warehouse.WarehouseManagement.create');
    }

    public function update(Request $request)
    {
        // return $request->all();
        if ($request->id_warehouse_admins == null) {
            $this->flashMessage('error', 'ERROR', 'Data Admin Tidak Boleh Kosong');
            return Redirect::to(route('warehouse_management.index'));
        } else {
            try {
                $id_warehouse_admins = $request->get('id_warehouse_admins', '');
                $arr_id_warehouse_admins = explode(',', $id_warehouse_admins);
                
                // get only admin Admin Call Center || Admin Warehouse
                $arr_id_warehouse_admins = User::whereHas('roles', function ($qr) {
                    $qr->whereIn('name', ['Admin Warehouse', 'Admin Call Center']);
                })->whereIn('id', $arr_id_warehouse_admins)->get(['id'])->pluck(['id']);
                $id_warehouse_admins = $arr_id_warehouse_admins->implode(',');

                $warehouseManagement = WarehouseManagement::where('id', $request->id_warehouse_management)
                    ->where('is_deleted', 1)
                    ->first();
                $warehouseManagement->warehouse_name = $request->warehouse_name;
                $warehouseManagement->warehouse_description = $request->warehouse_description;
                $warehouseManagement->id_warehouse_admins = $id_warehouse_admins;
                // $warehouseManagement->id_warehouse_admins = implode(',', $id_warehouse_admins);
                $warehouseManagement->submitted_by = Auth::user()->id;
                // $warehouseManagement->admin_total = count(explode(',', $id_warehouse_admins));
                // $warehouseManagement->admin_total = count($id_warehouse_admins);
                $warehouseManagement->admin_total = count($arr_id_warehouse_admins);
                $warehouseManagement->updated_at = Carbon::now();
                $warehouseManagement->updated_by = Auth::user()->id;
                // return $warehouseManagement;
                $warehouseManagement->save();

                $this->flashMessage('success', 'UPDATE', 'Warehouse Management Updated');
                return Redirect::to(route('warehouse_management.index'));
            } catch (Exception $e) {
                $this->flashMessage('error', 'ERROR', $e->getMessage());
                return Redirect::to(route('warehouse_management.index'));
            }
        }
        return view('Warehouse.WarehouseManagement.create');
    }

    public function show($id)
    {
        $data['warehouseManagement'] = WarehouseManagement::where('id', $id)->where('is_deleted', 1)->first();
        $admin_id = '';
        $listAdmins =  explode(",", $data['warehouseManagement']['id_warehouse_admins']);
        foreach ($listAdmins as $listAdmin) {
            $listAdmin = trim($listAdmin);
            $admin_id = $listAdmin;

            $admin = User::find($admin_id);
            $current_admin[] = $admin_id;
        }
        $data['current_admin'] = $current_admin;
        return view('Warehouse.WarehouseManagement.show', $data);
    }

    public function delete($id)
    {
        $warehouse = WarehouseManagement::where('id', $id)
            ->where('is_deleted', 1)
            ->first();
        $warehouse->is_deleted = 2;
        $warehouse->save();
        $this->flashMessage('success', 'DELETED', 'Warehouse Management Deleted');
        return Redirect::to(route('warehouse_management.index'));
    }

    public function getAdminCreateOld()
    {
        $role = Role::with('users')->get();
        $selectedRoles = $role->whereIn('name', ['Admin Warehouse', 'Admin Call Center']);
        $admins = $selectedRoles->pluck('users')->flatten();
        $output = '';
        foreach ($admins as $key => $reg) {

            $output .= '<tr>' .
                '<td><input type="checkbox" id="val' . "$key" . '" name="id_warehouse_admins[]" value=' . "$reg->id" . '>&nbsp;</td>' .
                '<td>' . $reg->username . '</td>' .
                '<td>' . $reg->name . '</td>' .
                '<td>' . $reg->phone . '</td>' .
                '<td>' . 'Admin Warehouse' . '</td>' .
                '<td>' . $reg->created_at . '</td>' .
                '</tr>';
        }
        return response()->json($output);
    }

    public function getAdminCreate()
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
        $listOrder = ['username', 'name', 'phone', 'role'];
        $listOrder = ['id', 'id', 'id_unilever_owner', 'name', 'email', 'phone', 'created_at'];
        $order     = $listOrder[$getOrder[0]['column']];
        $orderType = $getOrder[0]['dir'] ? $getOrder[0]['dir'] : 'asc';

        $query = User::whereHas('roles', function ($qr) {
            $qr->whereIn('name', ['Admin Warehouse', 'Admin Call Center']);
        });
        // $query = User::with(['roles']);

        $queryExcludeUsers = WarehouseManagement::whereNotNull('id_warehouse_admins')->where('is_deleted', 1);
        $ownedRole = [];
        // if ($id) $queryExcludeUsers->where('id', '!=', $id);
        if ($id) {
            $queryExcludeUsers->where('id', '!=', $id);
        }
        $excludeUsers = $queryExcludeUsers->get(['id_warehouse_admins'])->pluck('id_warehouse_admins');
        // $excludeUsers = $queryExcludeUsers->get(['id_warehouse_admins']);
        if ($excludeUsers->isNotEmpty()) {
            $excludeUsersId = array_unique(explode(',', $excludeUsers->implode(',')));
            // $query->whereDoesntHave('users', function ($qu) use ($excludeUsersId) {
            $query->whereNotIn('id', $excludeUsersId);
            // });
        }

        // put total before keyword filter and after global filter
        $total = $query->count();
        $totalFilter = $total;

        if ($keyword) {
            $query->where(function ($query) use ($keyword) {
                $query->whereRaw("username ilike ?", ["%" . $keyword . "%"])
                    ->orWhereRaw("name ilike ?", ["%" . $keyword . "%"])
                    ->orWhereRaw("phone ilike ?", ["%" . $keyword . "%"]);
                // ->orWhereHas('roles', function ($qr) use ($keyword) {
                //     $qr->orWhereRaw("name ilike ?", ["%" . $keyword . "%"]);
                // });
            });
            $totalFilter = $query->count();
        }

        $admins = $query->paginate($pageSize, ['*'], 'page', $page);

        // // $selectedRoles = $role;
        // $selectedRoles = $role->whereIn('name', ['Admin Warehouse', 'Admin Call Center']);
        // $query = $selectedRoles->pluck('users')->flatten(); //->pluck('users')->flatten()
        // // $query = Role::with('users');
        // // $query = Role::with('users')->orderBy($order, $orderType);
        // $ownedRole = [];
        // if ($id) {
        //     // $query = WarehouseManagement::whereNotNull('id_warehouse_admins')->get(['id_warehouse_admins']);
        //     $role = Role::with('users')->get(['id']);
        //     // $selectedRoles = $role;
        //     $selectedRoles = $role->whereIn('name', ['Admin Warehouse', 'Admin Call Center']);
        //     $query = $selectedRoles->pluck('users')->flatten(); //->pluck('users')->flatten()
        //     if ($isShow == 1) {
        //         $query->whereIn('id', $ownedRole);
        //     } else {
        //         $query->whereNotIn('id', $ownedRole);
        //     }
        //     $admin_mappings = $query->get(['id']);
        //     if ($admin_mappings) {
        //         $ownedRole = array_unique(explode(',', $admin_mappings->implode('id', ',')));
        //     }
        // }
        // if ($isShow == 1) {
        //     $query->whereIn('id', $ownedRole);
        // } else {
        //     $query->whereNotIn('id', $ownedRole);
        // }
        // $total = $query->count();
        // $totalFilter = $total;
        // if ($keyword) {
        //     $query->where(function ($query) use ($keyword) {
        //         $query->whereRaw("username ilike ?", ["%" . $keyword . "%"])
        //             ->orWhereRaw("name ilike ?", ["%" . $keyword . "%"])
        //             ->orWhereRaw("phone ilike ?", ["%" . $keyword . "%"])
        //             ->orWhereRaw("role ilike ?", ["%" . $keyword . "%"]);
        //     });
        //     $totalFilter = $query->count();
        // }

        // $admins = $roles->pluck('users')->flatten();;
        // $admins = $query->paginate($pageSize, ['*'], 'page', $page);
        $output['draw'] = $draw;
        $output['recordsTotal'] = $total;
        $output['recordsFiltered'] = $totalFilter;
        $output['data'] = [];
        $output['ownedRole'] = $ownedRole;
        foreach ($admins as $key => $value) {
            $output['data'][] = [
                '<input type="checkbox" id="val' . "$key" . '" class="id_admin" value=' . "$value->id" . '>&nbsp;',
                $value->username,
                $value->name,
                $value->phone,
                $value->role,
                Carbon::parse($value->created_at)->format("Y-m-d H:i:s"),
            ];
        }
        return response()->json($output);
    }

    public function getAdminEdit()
    {
        $role = Role::with('users')->get();
        $selectedRoles = $role->whereIn('name', ['Admin Warehouse', 'Admin Call Center']);
        $admins = $selectedRoles->pluck('users')->flatten();
        $output = '';
        foreach ($admins as $key => $reg) {

            $output .= '<tr>' .
                '<td><input type="checkbox" id="val' . "$key" . '" name="id_warehouse_admins[]" value=' . "$reg->id" . '>&nbsp;</td>' .
                '<td>' . $reg->username . '</td>' .
                '<td>' . $reg->name . '</td>' .
                '<td>' . $reg->phone . '</td>' .
                '<td>' . 'Admin Warehouse' . '</td>' .
                '<td>' . $reg->created_at . '</td>' .
                '</tr>';
        }
        return response()->json($output);
    }
}
