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
    public function index(Request $request)
    {
        if (empty($request)) {
            $data['datas'] = JuraganToWarehouseManagement::join('warehouse.warehouse_managements', 'juragan.juragan_to_warehouses.id_warehouse_management', 'warehouse.warehouse_managements.id')
                ->where('juragan.juragan_to_warehouses.is_deleted', 1)
                ->select('warehouse.warehouse_managements.id as wid', 'warehouse.warehouse_managements.warehouse_name', 'juragan.juragan_to_warehouses.*')
                ->get();
            return view('WarehouseMapping.JuraganToWarehouseManagement.index', $data);
        } else {
            $search = trim($request->query('search', ''));
            $contains = Str::contains($search, ['wm', 'WM']);
            if ($contains) {
                $query = JuraganToWarehouseManagement::join('warehouse.warehouse_managements', 'juragan.juragan_to_warehouses.id_warehouse_management', 'warehouse.warehouse_managements.id')
                    ->select('warehouse.warehouse_managements.id as wid', 'warehouse.warehouse_managements.warehouse_name', 'juragan.juragan_to_warehouses.*')
                    ->where('juragan.juragan_to_warehouses.is_deleted', 1)
                    ->where('warehouse.warehouse_managements.id', substr($search, 2));
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

    public function create()
    {
        $data['warehouseManagements'] = WarehouseManagement::where('is_deleted', 1)->get();
        $data['juragan_to_warehouse'] = JuraganToWarehouseManagement::whereNotNull('id_juragan_mappings')->get(['id_juragan_mappings']);
        // return $data;
        return view('WarehouseMapping.JuraganToWarehouseManagement.create', $data);
    }

    public function edit($id)
    {
        $data['juraganToWarehouse'] = JuraganToWarehouseManagement::where('id', $id)->where('is_deleted', 1)->first();
        $data['warehouses'] = WarehouseManagement::get();
        return view('WarehouseMapping.JuraganToWarehouseManagement.edit', $data);
    }

    public function store(Request $request)
    {
        // return $request->all();
        if ($request->id_juragan_mappings == null) {
            $this->flashMessage('error', 'ERROR', 'Data Juragan Tidak Boleh Kosong');
            return Redirect::to(route('warehouse_management.index'));
        } else {
            try {
                $idJuraganMappings = $request->get('id_juragan_mappings', '');
                $juraganToWarehouse = new JuraganToWarehouseManagement;
                $juraganToWarehouse->id_warehouse_management = $request->id_warehouse_management;
                $juraganToWarehouse->id_juragan_mappings = $idJuraganMappings;
                $juraganToWarehouse->submitted_by = Auth::user()->id;
                $juraganToWarehouse->juragan_total = count(explode(',', $idJuraganMappings));
                $juraganToWarehouse->created_at = Carbon::now();
                $juraganToWarehouse->created_by = Auth::user()->id;
                // return $juraganToWarehouse;
                $juraganToWarehouse->save();

                $this->flashMessage('success', 'CREATE', 'Juragan to Warehouse Management Created');
                return Redirect::to(route('juragan_to_warehouse.index'));
            } catch (Exception $e) {
                $this->flashMessage('error', 'ERROR', $e->getMessage());
                return Redirect::to(route('juragan_to_warehouse.index'));
            }
        }
        return view('WarehouseMapping.JuraganToWarehouseManagement.create');
    }

    public function update(Request $request)
    {
        if ($request->id_juragan_mappings == null) {
            $this->flashMessage('error', 'ERROR', 'Data Juragan Tidak Boleh Kosong');
            return Redirect::to(route('juragan_to_warehouse.index'));
        } else {
            try {
                $idJuraganMappings = $request->get('id_juragan_mappings', '');
                $juraganToWarehouse = JuraganToWarehouseManagement::where('id', $request->id_juragan_to_warehouse)->where('is_deleted', 1)->first();
                $juraganToWarehouse->id_warehouse_management = $request->id_warehouse_management;
                $juraganToWarehouse->id_juragan_mappings = $idJuraganMappings;
                $juraganToWarehouse->updated_by = Auth::user()->id;
                $juraganToWarehouse->juragan_total = count(explode(',', $idJuraganMappings));
                $juraganToWarehouse->updated_at = Carbon::now();
                $juraganToWarehouse->save();

                $this->flashMessage('success', 'UPDATE', 'Juragan to Warehouse Management Updated');
                return Redirect::to(route('juragan_to_warehouse.index'));
            } catch (Exception $e) {
                $this->flashMessage('error', 'ERROR', $e->getMessage());
                return Redirect::to(route('juragan_to_warehouse.index'));
            }
        }
    }

    public function show($id)
    {
        $data['juraganToWarehouse'] = JuraganToWarehouseManagement::where('id', $id)->where('is_deleted', 1)->first();
        $admin_id = '';
        $listJuragans =  explode(",", $data['juraganToWarehouse']['id_juragan_mappings']);
        foreach ($listJuragans as $listJuragan) {
            $listJuragan = trim($listJuragan);
            $juraganId = $listJuragan;

            $juragan = Juragan::find($juraganId);
            $current_juragan[] = $juraganId;
        }
        $data['current_juragan'] = $current_juragan;
        return view('WarehouseMapping.JuraganToWarehouseManagement.show', $data);
    }

    public function delete($id)
    {
        $data = JuraganToWarehouseManagement::where('id', $id)->where('is_deleted', 1)->first();
        $data->is_deleted = 2;
        $data->save();
        $this->flashMessage('success', 'DELETED', 'Juragan to Warehouse Management Deleted');
        return Redirect::to(route('juragan_to_warehouse.index'));
    }

    public function getJuraganOld()
    {
        $juragans = Juragan::get();
        $output = '';
        foreach ($juragans as $key => $reg) {

            $output .= '<tr>' .
                '<td><input type="checkbox" id="val' . "$key" . '" class="id_juragan" name="id_juragan_mappings[]" value=' . "$reg->id" . '>&nbsp;</td>' .
                '<td>' . $reg->id . '</td>' .
                '<td>' . $reg->id_unilever_owner . '</td>' .
                '<td>' . $reg->id_leveredge . '</td>' .
                '<td>' . $reg->name . '</td>' .
                '<td>' . $reg->email . '</td>' .
                '<td>' . $reg->phone . '</td>' .
                '</tr>';
        }
        return response()->json($output);
    }

    public function getJuragan()
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
        $listOrder = ['id', 'id', 'id_unilever_owner', 'name', 'email', 'phone', 'created_at'];
        $order     = $listOrder[$getOrder[0]['column']];
        $orderType = $getOrder[0]['dir'] ? $getOrder[0]['dir'] : 'asc';

        $query = Juragan::where('is_deleted', 1)->orderBy($order, $orderType);
        $ownedJuragan = [];
        if ($id) {
            $query = JuraganToWarehouseManagement::whereNotNull('id_juragan_mappings')->get(['id_juragan_mappings']);
            if ($isShow == 1) {
                $query->whereIn('id', $ownedJuragan);
            } else {
                $query->whereNotIn('id', $ownedJuragan);
            }
            $juragan_mappings = $query->get(['id_juragan_mappings']);
            if ($juragan_mappings) {
                $ownedJuragan = array_unique(explode(',', $juragan_mappings->implode('id_juragan_mappings', ',')));
            }
        }
        if ($isShow == 1) {
            $query->whereIn('id', $ownedJuragan);
        } else {
            $query->whereNotIn('id', $ownedJuragan);
        }
        $total = $query->count();
        $totalFilter = $total;
        if ($keyword) {
            $query->where(function ($query) use ($keyword) {
                $query->WhereRaw("id ilike ?", ["%" . $keyword . "%"])
                    ->orWhereRaw("id_unilever_owner ilike ?", ["%" . $keyword . "%"])
                    ->orWhereRaw("name ilike ?", ["%" . $keyword . "%"])
                    ->orWhereRaw("email ilike ?", ["%" . $keyword . "%"])
                    ->orWhereRaw("phone ilike ?", ["%" . $keyword . "%"]);
            });
            $totalFilter = $query->count();
        }
        $juragan = $query->paginate($pageSize, ['*'], 'page', $page);
        $output['draw'] = $draw;
        $output['recordsTotal'] = $total;
        $output['recordsFiltered'] = $totalFilter;
        $output['data'] = [];
        $output['ownedJuragan'] = $ownedJuragan;
        foreach ($juragan as $key => $value) {
            $output['data'][] = [
                // '<input type="checkbox" id="val' . "$key" . '" class="id_juragan" name="id_juragan_mappings[]" value=' . "$value->id" . '>&nbsp;',
                '<input type="checkbox" id="val' . "$key" . '" class="id_juragan" value=' . "$value->id" . '>&nbsp;',
                $value->id,
                $value->id_unilever_owner,
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
