<?php

namespace App\Http\Controllers\Warehouse;

use App\export\CabinetExport;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GenericController;
use App\Models\Warehouse\Cabinets;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class CabinetController extends GenericController
{

//    public function __construct() {
//        parent::__construct();
//    }

    private function edit_rules($id)
    {
        $rules = [
            'brand' => ['required'],
            'model' => ['required'],
            'serialnumber' => ['required', 'unique:' . Cabinets::class . ',serialnumber,' . $id],
            'qrcode' => ['required', 'unique:' . Cabinets::class . ',qrcode,' . $id]
        ];
        return $rules;
    }

    protected $rules = [
        'serialnumber' => 'required|unique:' . Cabinets::class . ',serialnumber',
        'qrcode' => 'required|unique:' . Cabinets::class . ',qrcode',
        'brand' => 'required',
        'model' => 'required'
    ];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = Cabinets::orderBy('created_at', 'DESC');
        if ($request->query("search") != "") {
            $data = $data->whereRaw("lower(brand) ilike ?", ["%" . $request->query("search") . "%"])
                ->orWhereRaw("lower(model_type) ilike ?", ["%" . $request->query("search") . "%"])
                ->orWhereRaw("lower(qrcode) ilike ?", ["%" . $request->query("search") . "%"])
                ->orWhereRaw("lower(serialnumber) ilike ?", ["%" . $request->query("search") . "%"]);
        }
        $data = $data->paginate(30);
        $data->appends($request->query());
        $data = [
            'datas' => $data
        ];
//        dd($data);
        return view('Warehouse/Cabinet/index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $datas = [
            'route' => route('vehicle.store')
        ];
        return view('Warehouse/Cabinet/create', $datas);
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
            return Redirect::to(route('cabinet.create'))
                ->withErrors($validator)->withInput($request->all());
        } else {
            DB::beginTransaction();
            try {
                $data = new Cabinets();
                $data->id = Carbon::now()->getPreciseTimestamp(3);
                $data->serialnumber = $request->serialnumber;
                $data->qrcode = $request->qrcode;
                $data->brand = $request->brand;
                $data->model_type = $request->model;
                $data->created_by = Auth::user()->id;
                $data->created_at = Carbon::now();
                $data->save();
                DB::commit();
                $this->flashMessage('success', 'CREATE', 'Add data success');
                return Redirect::to(route('cabinet.index'));
            } catch (\Exception $e) {
                DB::rollback();
                Log::error($e);
                $this->flashMessage('error', 'CREATE', $e->getMessage());
                return Redirect::to(route('cabinet.create'));
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
        $data = Cabinets::find($id);
        if ($data == null) {
//            $this->flashMessage('error', 'SHOW', 'Data not found');
            return Redirect::to(route('cabinet.index'));
        }
        $datas = [
            'data' => $data
        ];
        return view('Warehouse/Cabinet/show', $datas);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = Cabinets::find($id);
        if ($data == null) {
//            $this->flashMessage('error', 'EDIT', 'Data not found');
            return redirect(route('vehicle.index'));
        }
        $datas = [
            'data' => $data,
        ];
//        dd($datas);
        return view('Warehouse/Cabinet/edit', $datas);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), $this->edit_rules($id));
        if ($validator->fails()) {
            return Redirect::to(route('cabinet.edit', ['cabinet' => $id]))
                ->withErrors($validator)->withInput($request->all());
        } else {
            DB::beginTransaction();
            try {
                $data = Cabinets::find($id);
                $data->serialnumber = $request->serialnumber;
                $data->qrcode = $request->qrcode;
                $data->brand = $request->brand;
                $data->model_type = $request->model;
                $data->updated_by = Auth::user()->id;
                $data->updated_at = Carbon::now();
                $data->save();
                DB::commit();
                $this->flashMessage('success', 'EDIT', 'Edit data success');
                return Redirect::to(route('cabinet.index'));
            } catch (\Exception $e) {
                DB::rollback();
                Log::error($e);
                $this->flashMessage('error', 'EDIT', $e->getMessage());
                return Redirect::to(route('cabinet.edit', ['cabinet' => $id]));
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
        throw new \Exception("Not implemented yet");
    }

    public function export()
    {
        $request = Request::capture();
        $search = $request->query('search', '');
        $startDate = $request->query('fromDate', '');
        $endDate = $request->query('toDate', '');
        $file_name = sprintf('cabinet-%s.xlsx', Str::uuid()->toString());
        return response()->streamDownload(function () use ($search, $startDate, $endDate) {
            echo file_get_contents('http://127.0.0.1:8000/cabinet?' . http_build_query(['search' => $search, 'from_date' => $startDate, 'to_date' => $endDate]));
        }, $file_name);
    }
}
