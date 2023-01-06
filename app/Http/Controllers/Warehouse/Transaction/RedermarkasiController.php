<?php

namespace App\Http\Controllers\Warehouse\Transaction;

use App\Http\Controllers\GenericController;
use App\Models\JuraganManagement\Juragan;
use App\Models\OutletManagement\Outlet;
use App\Models\Transactions\Redermarkasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use Auth;

class RedermarkasiController extends GenericController
{
    public function index(Request $request) {
        $outlet_data = Outlet::where('name', 'ILIKE', '%' . $request->outlet_name . '%')->get();
        $idJuraganAsal = $request->id_juragan_asal;
        $idJuraganTujuan = $request->id_juragan_tujuan;
        foreach($outlet_data as $i => $outlet) {
            $idOutlet[] = $outlet->id;
        }
        $data['juragans'] = Juragan::get();
        if(empty($request)) {
            $data['datas'] = Redermarkasi::join('juragan.juragans', 'transactions.redermarkasi.id_juragan_asal', 'juragan.juragans.id')
            ->select('juragan.juragans.*', 'transactions.redermarkasi.*', 'transactions.redermarkasi.id as id')->get();
            return view('Warehouse.Redermarkasi.index', $data);
        } elseif(empty($request->outlet_name)) {
            if(($idJuraganAsal != null) && ($idJuraganTujuan != null)) {
                $data['datas'] = Redermarkasi::join('juragan.juragans', 'transactions.redermarkasi.id_juragan_asal', 'juragan.juragans.id')
                ->where('transactions.redermarkasi.id_juragan_asal' ,$idJuraganAsal)
                ->where('transactions.redermarkasi.id_juragan_tujuan' ,$idJuraganTujuan)
                ->select('juragan.juragans.*', 'transactions.redermarkasi.*', 'transactions.redermarkasi.id as id')->get();
                return view('Warehouse.Redermarkasi.index', $data);
            } elseif(($idJuraganAsal == null) && ($idJuraganTujuan != null)) {
                $data['datas'] = Redermarkasi::join('juragan.juragans', 'transactions.redermarkasi.id_juragan_asal', 'juragan.juragans.id')
                ->where('transactions.redermarkasi.id_juragan_tujuan' ,$idJuraganTujuan)
                ->select('juragan.juragans.*', 'transactions.redermarkasi.*', 'transactions.redermarkasi.id as id')->get();
                return view('Warehouse.Redermarkasi.index', $data);
            } elseif(($idJuraganAsal != null) && ($idJuraganTujuan == null)) {
                $data['datas'] = Redermarkasi::join('juragan.juragans', 'transactions.redermarkasi.id_juragan_asal', 'juragan.juragans.id')
                ->where('transactions.redermarkasi.id_juragan_asal' ,$idJuraganAsal)
                ->select('juragan.juragans.*', 'transactions.redermarkasi.*', 'transactions.redermarkasi.id as id')->get();
                return view('Warehouse.Redermarkasi.index', $data);
            } else {
                $data['datas'] = Redermarkasi::join('juragan.juragans', 'transactions.redermarkasi.id_juragan_asal', 'juragan.juragans.id')
                ->select('juragan.juragans.*', 'transactions.redermarkasi.*', 'transactions.redermarkasi.id as id')->get();
                return view('Warehouse.Redermarkasi.index', $data);
            }
        } else {
            foreach($idOutlet as $i => $outlet) {
                $redermarkasi = Redermarkasi::where('id_submitted_outlets', 'ILIKE', '%' . $outlet . '%')->get();
                $datas = $redermarkasi;
            }
            $data['datas'] = $datas;
            return view('Warehouse.Redermarkasi.index', $data);
        }
    }

    public function create() {
        $data['juragans'] = Juragan::get();
        return view('Warehouse.Redermarkasi.create', $data);
    }

    public function edit($id) {
        $data['juragans'] = Juragan::get();
        // $data['redermarkasi'] = Redermarkasi::join('juragan.juragans', 'transactions.redermarkasi.id_juragan_asal', 'juragan.juragans.id')
        // ->where('transactions.redermarkasi.id', $id)
        // ->select('juragan.juragans.*', 'transactions.redermarkasi.*', 'transactions.redermarkasi.id as id')->first();
                // $data['redermarkasi'] = Redermarkasi::join('juragan.juragans', 'transactions.redermarkasi.id_juragan_asal', 'juragan.juragans.id')
        $data['redermarkasi'] = Redermarkasi::find($id);
        return view('Warehouse.Redermarkasi.edit', $data);
    }

    public function store(Request $request) {
        $idJuraganAsal = $request->id_juragan_asal;
        $idJuraganTujuan = $request->id_juragan_tujuan;
        if($request->id_submitted_outlets == null) {
            $this->flashMessage('error', 'ERROR', 'Data Outlet Tidak Boleh Kosong');
            return Redirect::to(route('redermarkasi.index'));
        } else {
            try {
                $redermarkasi = Redermarkasi::find($request->id_redermarkasi);
                $redermarkasi->latest_status = 'Submitted';
                $redermarkasi->updated_at = Carbon::now();
                $redermarkasi->save();
    
                $this->flashMessage('success', 'SUBMITTED', 'Redermarkasi Submitted');
                return Redirect::to(route('redermarkasi.index'));
            } catch(Exception $e) {
                $this->flashMessage('error', 'ERROR', $e->getMessage());
                return Redirect::to(route('redermarkasi.index'));
            }
        }
    }

    public function update(Request $request) {
        $idJuraganAsal = $request->id_juragan_asal;
        $idJuraganTujuan = $request->id_juragan_tujuan;
        $idSubmittedOutlets = $request->get('id_submitted_outlets', null);
        if($idSubmittedOutlets == null) {
            $this->flashMessage('error', 'ERROR', 'Data Outlet Tidak Boleh Kosong');
            return Redirect::to(route('redermarkasi.index'));
        } else {
            try {
                $redermarkasi = Redermarkasi::find($request->id_redermarkasi);
                $redermarkasi->id_juragan_asal = $idJuraganAsal;
                $redermarkasi->id_juragan_tujuan = $idJuraganTujuan;
                $redermarkasi->id_submitted_outlets = $idSubmittedOutlets;
                $redermarkasi->submitted_by = Auth::user()->id;
                $redermarkasi->latest_status = 'Draft';
                $redermarkasi->outlet_total = count(explode(',', $idSubmittedOutlets));
                $redermarkasi->created_at = Carbon::now();
                $redermarkasi->save();
    
                $outlet_id = '';
                $listOutlets =  explode(",", $redermarkasi['id_submitted_outlets']);
                foreach($listOutlets as $listOutlet) {
                    $listOutlet = trim($listOutlet);
                    $outlet_id = $listOutlet;
    
                    $outlet = Outlet::find($outlet_id);
                    $outlet->on_redermarkasi = 1;
                    $outlet->save();
                }
                $this->flashMessage('success', 'DRAFT', 'Redermarkasi Drafted');
                return Redirect::to(route('redermarkasi.index'));
            } catch(Exception $e) {
                $this->flashMessage('error', 'ERROR', $e->getMessage());
                return Redirect::to(route('redermarkasi.index'));
            }
        }
    }

    public function show($id) {
        $data['juragans'] = Juragan::get();
        $data['redermarkasi'] = Redermarkasi::join('juragan.juragans', 'transactions.redermarkasi.id_juragan_asal', 'juragan.juragans.id')
        ->where('transactions.redermarkasi.id', $id)
        ->select('juragan.juragans.*', 'transactions.redermarkasi.*', 'transactions.redermarkasi.id as id')->first();

        $outlet_id = '';
            $listOutlets =  explode(",", $data['redermarkasi']['id_submitted_outlets']);
            foreach($listOutlets as $listOutlet) {
                $listOutlet = trim($listOutlet);
                $outlet_id = $listOutlet;

                $outlet = Outlet::find($outlet_id);
                $current_outlet[] = $outlet_id;
            }
        $data['current_outlet'] = $current_outlet;
        return view('Warehouse.Redermarkasi.show', $data);
    }

    public function draft(Request $request) {
        $idJuraganAsal = $request->id_juragan_asal;
        $idJuraganTujuan = $request->id_juragan_tujuan;
        $idSubmittedOutlets = $request->get('id_submitted_outlets', null);
        if($idSubmittedOutlets == null) {
            $this->flashMessage('error', 'ERROR', 'Data Outlet Tidak Boleh Kosong');
            return Redirect::to(route('redermarkasi.index'));
        } else {
            try {
                $redermarkasi = new Redermarkasi;
                $redermarkasi->id_juragan_asal = $idJuraganAsal;
                $redermarkasi->id_juragan_tujuan = $idJuraganTujuan;
                $redermarkasi->id_submitted_outlets = $idSubmittedOutlets;
                $redermarkasi->submitted_by = Auth::user()->id;
                $redermarkasi->latest_status = 'Draft';
                $redermarkasi->outlet_total = count(explode(',', $idSubmittedOutlets));
                $redermarkasi->created_at = Carbon::now();
                $redermarkasi->save();
    
                $outlet_id = '';
                $listOutlets =  explode(",", $redermarkasi['id_submitted_outlets']);
                foreach($listOutlets as $listOutlet) {
                    $listOutlet = trim($listOutlet);
                    $outlet_id = $listOutlet;
    
                    $outlet = Outlet::find($outlet_id);
                    $outlet->on_redermarkasi = 1;
                    $outlet->save();
                }
                $this->flashMessage('success', 'DRAFT', 'Redermarkasi Drafted');
                return Redirect::to(route('redermarkasi.index'));
            } catch(Exception $e) {
                $this->flashMessage('error', 'ERROR', $e->getMessage());
                return Redirect::to(route('redermarkasi.index'));
            }
        }
    }

    public function approve($id) {
        $redermarkasi = Redermarkasi::find($id);
        $outlet_id = '';
        $listOutlets =  explode(",", $redermarkasi['id_submitted_outlets']);
        foreach($listOutlets as $listOutlet) {
            $listOutlet = trim($listOutlet);
            $outlet_id = $listOutlet;

            $outlet = Outlet::find($outlet_id);
            $outlet->id_juragan = $redermarkasi->id_juragan_tujuan;
            $outlet->on_redermarkasi = 0;
            $outlet->save();
        }
        $redermarkasi->latest_status = 'Completed';
        $redermarkasi->approved_by = Auth::user()->id;
        $redermarkasi->approved_at = Carbon::now();
        $redermarkasi->save();
        $this->flashMessage('success', 'APPROVE', 'Redermarkasi Approved');
        return Redirect::to(route('redermarkasi.index'));
    }

    public function reject(Request $request) {
        $redermarkasi = Redermarkasi::find($request->id_redermarkasi);
        $outlet_id = '';
        $listOutlets =  explode(",", $redermarkasi['id_submitted_outlets']);
        foreach($listOutlets as $listOutlet) {
            $listOutlet = trim($listOutlet);
            $outlet_id = $listOutlet;

            $outlet = Outlet::find($outlet_id);
            $outlet->id_juragan = $redermarkasi->id_juragan_tujuan;
            $outlet->on_redermarkasi = 0;
            $outlet->save();
        }
        $redermarkasi->latest_status = 'Rejected';
        $redermarkasi->rejected_by = Auth::user()->id;
        $redermarkasi->rejected_at = Carbon::now();
        $redermarkasi->rejected_reason = $request->rejected_reason;
        $redermarkasi->save();
        $this->flashMessage('error', 'REJECT', 'Redermarkasi Rejected');
        return Redirect::to(route('redermarkasi.index'));
    }

    public function getOutletCreate($id_juragan_asal)
    {
        $request   = Request::capture();
        $draw      = $request->query('draw', '');
        $start     = $request->query('start', '');
        $pageSize  = $request->query('length', '');
        $page      = ($start > 0) ? ($start / $pageSize + 1) : 1;
        $keyword   = $request->get('search')['value'];
        $getOrder  = $request->get('order', []);
        $listOrder = ['id', 'id', 'name', 'address'];
        $order     = $listOrder[$getOrder[0]['column']];
        $orderType = $getOrder[0]['dir'] ? $getOrder[0]['dir'] : 'asc';

        $juraganAsalId = $id_juragan_asal;
        $users = new Outlet();
        $users = $users->whereHas("mapOutlet", function ($query) {
            $query->where("is_mitra", "=", 1);
        });
        $query = $users->with('juragan')->where('id_juragan', $juraganAsalId);
        $total       = $query->count();
        $totalFilter = $total;

        if ($keyword) {
            $query->where(function($query) use ($keyword) {
                $query->whereRaw("name ilike ?", ["%" . $keyword . "%"])
                        ->orWhereRaw("address ilike ?", ["%" . $keyword . "%"]);                
            });
            $totalFilter = $query->count();
        }
        $data = $query->paginate($pageSize, ['*'], 'page', $page);
        $output['draw'] = $draw;
        $output['recordsTotal'] = $total;
        $output['recordsFiltered'] = $totalFilter;
        $output['data'] = [];

        foreach ($data as $key => $reg) {

            $output['data'][] = [
                '<input type="checkbox" id="val'."$key".'" class="id_outlet" value='."$reg->id".'>&nbsp;',
                $reg->id,
                $reg->name,
                $reg->address
            ];

        }
        return response()->json($output);
    }

    public function getOutletEdit($id_juragan_asal)
    {
        $request   = Request::capture();
        $draw      = $request->query('draw', '');
        $start     = $request->query('start', '');
        $pageSize  = $request->query('length', '');
        $page      = ($start > 0) ? ($start / $pageSize + 1) : 1;
        $keyword   = $request->get('search')['value'];
        $getOrder  = $request->get('order', []);
        $listOrder = ['id', 'id', 'name', 'address'];
        $order     = $listOrder[$getOrder[0]['column']];
        $orderType = $getOrder[0]['dir'] ? $getOrder[0]['dir'] : 'asc';

        $juraganAsalId = $id_juragan_asal;
        $users = new Outlet();
        $users = $users->whereHas('mapOutlet.outletProgress', function ($query) {
            $query->where('status_active', 1);
            $query->where('section', 'uli');
            $query->where('status_progress', '5');
            $query->select();
        });
        $query = $users->with('juragan')->where('id_juragan', $juraganAsalId);
        $total       = $query->count();
        $totalFilter = $total;

        if ($keyword) {
            $query->where(function($query) use ($keyword) {
                $query->whereRaw("name ilike ?", ["%" . $keyword . "%"])
                        ->orWhereRaw("address ilike ?", ["%" . $keyword . "%"]);                
            });
            $totalFilter = $query->count();
        }
        $data = $query->paginate($pageSize, ['*'], 'page', $page);
        $output['draw'] = $draw;
        $output['recordsTotal'] = $total;
        $output['recordsFiltered'] = $totalFilter;
        $output['data'] = [];

        foreach ($data as $key => $reg) {

            $output['data'][] = [
                '<input type="checkbox" id="val'."$key".'" class="id_outlet" value='."$reg->id".'>&nbsp;',
                $reg->id,
                $reg->name,
                $reg->address
            ];

        }
        return response()->json($output);
    }
}