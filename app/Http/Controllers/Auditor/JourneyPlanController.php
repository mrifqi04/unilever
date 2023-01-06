<?php

namespace App\Http\Controllers\Auditor;

use App\Http\Controllers\GenericController;
use App\Models\Auditor\AuditPlan;
use App\Models\City;
use App\Models\District;
use App\Models\Auditor\Form;
use App\Models\Auditor\Auditor;
use App\Models\Auditor\JourneyPlan;
use App\Models\JuraganManagement\Juragan;
use App\Models\OutletManagement\Outlet;
use App\Models\Province;
use App\Models\Scheduler\PJPJob;
use App\Models\Village;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\StreamedResponse;

class JourneyPlanController extends GenericController
{
    /**
     * Display a listing of the resource.
     *
     * @return Response|View
     */
    public function index()
    {
        $pjp_file_csv = PJPJob::with("status")->orderBy("created_at", "desc")->get();
        $request = Request::capture();
        $query = JourneyPlan::where('is_deleted', '=', 1);
        // dd($query->where('id', 'anas test 1234')->first());
        $can_show = Auth::user()->getPermissionByName('auditor.journeyplan.show');
        $can_edit = Auth::user()->getPermissionByName('auditor.journeyplan.edit');
        $can_destroy = Auth::user()->getPermissionByName('auditor.journeyplan.destroy');
        $locationsData = DB::table('cities')
        ->leftJoin('provinces', 'cities.id_province', '=', 'provinces.id')
        ->select(
            'cities.id as city_id', 
            'cities.name as city_name', 
            'provinces.id as province_id', 
            'provinces.name as province_name'
            )
        ->get();
        $search = trim($request->query('search', ''));
        if ($search !== '') {
            $query->where(function ($query) use ($search) {
                $query
                    ->orWhereRaw("id ilike ?", ["%" . $search . "%"])
                    ->orWhereHas('city', function ($query) use ($search) {
                        $query->where("name", "ilike", "%{$search}%");
                    })
                    ->orWhereHas('assignTo', function ($query) use ($search) {
                        $query->where("name", "ilike", "%{$search}%");
                    })
                    ->orWhereHas('juragan', function ($query) use ($search) {
                        $query->where("name", "ilike", "%{$search}%");
                    });
            });
        }
        $journeyPlans = $query->orderBy('created_at', 'desc')
            ->paginate(30);
        $journeyPlans->appends(['search' => $request->query("search")]);

        foreach ($pjp_file_csv as $pjp_file) {
            $data_messages[] = json_decode($pjp_file->error_description);
        }

        return view('Auditor.journeyplan.index')
            ->with('can_show', $can_show)
            ->with('can_edit', $can_edit)
            ->with('can_destroy', $can_destroy)
            ->with('pjp_file_csv', $pjp_file_csv)
            ->with('data_messages', @$data_messages)
            ->with('locationsData', $locationsData)
            ->with('journeyplans', $journeyPlans);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response|View
     */
    public function create()
    {
        $juragans = Juragan::select(['id', 'name'])
            ->where('is_deleted', 1)
            ->get()->pluck('name', 'id');
        $hunters = Auditor::select(['id', 'name'])
            ->where('is_deleted', 1)
            ->get()->pluck('name', 'id');
        $forms = Form::select(['id', 'name'])
            ->where('is_deleted', 1)
            ->get()->pluck('name', 'id');
        $provinces = Province::select(['id', 'name'])
            ->where('id_country', '01')
            ->where('is_deleted', 1)
            ->get()->pluck('name', 'id');
        $cities = [];
        return view('Auditor.journeyplan.create')
            ->with('juragans', $juragans)
            ->with('hunters', $hunters)
            ->with('forms', $forms)
            ->with('provinces', $provinces)
            ->with('cities', $cities);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response | RedirectResponse
     */
    public function store(Request $request)
    {
        // return $request->all();
        $id = $request->input('id');
        $name = $request->input('name');
        $id_juragan = $request->input('id_juragan');
        $id_form = '9';
        $id_province = $request->input('id_province');
        $id_city = $request->input('id_city');
        $assigner = \auth()->user()->id;
        $assign_to = $request->input('assign_to');
        $start_date = Carbon::parse($request->input('start_date'))->format("Y-m-d");
        $end_date = Carbon::parse($request->input('end_date'))->format("Y-m-d");
        $outlets = $request->input("outlets");
        $validator = Validator::make(
            [
                'id' => $id,
                'name' => $name,
                'id_juragan' => $id_juragan,
                'id_form' => $id_form,
                'id_province' => $id_province,
                'id_city' => $id_city,
                'assigner' => $assigner,
                'assign_to' => $assign_to,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'outlets' => $outlets,
            ],
            [
                'id' => 'required|unique:App\Models\Auditor\JourneyPlan,id',
                'name' => 'required',
                'id_juragan' => 'required|exists:App\Models\JuraganManagement\Juragan,id,is_deleted,1',
                'id_form' => 'required|exists:App\Models\Auditor\Form,id,is_deleted,1',
                'id_province' => 'required|exists:App\Models\Province,id,is_deleted,1',
                'id_city' => 'required|exists:App\Models\City,id,is_deleted,1',
                'assigner' => 'required',
                'assign_to' => 'required|exists:App\Models\Auditor\Auditor,id,is_deleted,1',
                'start_date' => 'required|date_format:Y-m-d',
                'end_date' => 'required|date_format:Y-m-d',
                'outlets.*' => [
                    'required',
                    Rule::in(Outlet::where("id_juragan", "=", $id_juragan)->whereHas("mapOutlet", function ($query) {
                        $query->where("is_mitra", "=", "1");
                    })->select('id')->get()->pluck("id")->toArray()),
                ],
            ]
        );
        if ($validator->fails()) {
            $this->flashMessage('error', 'Create Journey Plan Error', $validator->errors()->first());
            Log::info($validator->errors());
            return redirect()
                ->route('auditor.journeyplan.create')
                ->withInput($request->all());
        }
        DB::beginTransaction();
        $juragan = Juragan::find($id_juragan);
        $journeyplan = new JourneyPlan();
        $journeyplan->id = $id;
        $journeyplan->name = $name;
        $journeyplan->id_juragan = $id_juragan;
        $journeyplan->id_form = $id_form;
        $journeyplan->id_province = $id_province;
        $journeyplan->id_city = $id_city;
        $journeyplan->id_district = $juragan->id_district;
        $journeyplan->id_village = $juragan->id_village;
        $journeyplan->assigner = $assigner;
        $journeyplan->assign_to = $assign_to;
        $journeyplan->start_date = $start_date;
        $journeyplan->end_date = $end_date;
        $journeyplan->is_deleted = 1;
        $journeyplan->created_at = Carbon::now()->unix();
        $journeyplan->created_by = \auth()->user()->id;
        $journeyplan->updated_at = $journeyplan->created_at;
        $journeyplan->updated_by = \auth()->user()->id;
        try {
            $journeyplan->save();
            // insert to audit plan
            $auditPlans = [];
            foreach ($outlets as $outlet) {
                $auditPlan = new AuditPlan();
                $auditPlan->id = Uuid::uuid4()->toString();
                $auditPlan->id_journey_plan = $journeyplan->id;
                $auditPlan->id_outlet = $outlet;
                $auditPlan->is_deleted = 1;
                $auditPlan->created_at = Carbon::now()->unix();
                $auditPlan->created_by = \auth()->user()->id;
                $auditPlan->updated_at = $auditPlan->created_at;
                $auditPlan->updated_by = \auth()->user()->id;
                $auditPlans[] = $auditPlan;
            }
            // return $auditPlans;
            $journeyplan->auditPlans()->saveMany($auditPlans);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->flashMessage('error', 'Create Journey Plan Error', $e->getMessage());
            Log::error($e);
            return redirect()
                ->route('auditor.journeyplan.create')
                ->withInput($request->all());
        }
        return redirect()->route('auditor.journeyplan.index');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response | RedirectResponse | View
     */
    public function show($id)
    {
        $journeyPlan = JourneyPlan::with([
            "juragan",
            "auditPlans",
            "auditPlans.outlet",
            "province",
            "city",
            "district",
            "village"
        ])->where('id', $id)
            ->where('is_deleted', 1)
            ->first();
        
        if (is_null($journeyPlan)) {
            $this->flashMessage('error', 'Show Journey Plan', sprintf('Journey Plan With Id %s Not Found', $id));
            return redirect()
                ->route('auditor.journeyplan.index');
        }
        return view('Auditor.journeyplan.show')
            ->with('journeyplan', $journeyPlan);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Response | RedirectResponse | View
     */
    public function edit($id)
    {
        $journeyPlan = JourneyPlan::with(['auditPlans' => function ($query) {
            $query->where('is_deleted', '=', 1);
        }])->where('id', '=', $id)
            ->where('is_deleted', '=', 1)
            ->first();
        if (is_null($journeyPlan)) {
            $this->flashMessage('error', 'Edit Journey Plan', sprintf('Journey Plan With Id %s Not Found', $id));
            return redirect()
                ->route('auditor.journeyplan.index')
                ->with('message', sprintf('journey plan with id %s not found', $id));
        }
        $juragans = Juragan::select(['id', 'name'])
            ->where('is_deleted', 1)
            ->get()->pluck('name', 'id');
        $auditors = Auditor::select(['id', 'name'])
            ->where('is_deleted', 1)
            ->get()->pluck('name', 'id');
        $forms = Form::select(['id', 'name'])
            ->where('is_deleted', 1)
            ->get()->pluck('name', 'id');
        $provinces = Province::select(['id', 'name'])
            ->where('id_country', '01')
            ->where('is_deleted', 1)
            ->get()->pluck('name', 'id');
        $cities = City::select(['id', 'name'])
            ->where('id_province', $journeyPlan->id_province)
            ->where('is_deleted', 1)
            ->get()->pluck('name', 'id');
        return view('Auditor.journeyplan.edit')
            ->with('juragans', $juragans)
            ->with('auditors', $auditors)
            ->with('forms', $forms)
            ->with('provinces', $provinces)
            ->with('cities', $cities)
            ->with('journeyplan', $journeyPlan);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response | RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $name = $request->input('name');
        $id_form = '9';
        $assigner = '1';
        $end_date = $request->input('end_date');
        $validator = Validator::make(
            [
                'id' => $id,
                'name' => $name,                
                'id_form' => $id_form,                                
                'assigner' => $assigner,                                
                'end_date' => $end_date,
            ],
            [
                'id' => 'required|exists:App\Models\Auditor\JourneyPlan,id,is_deleted,1',
                'name' => 'required',                
                'id_form' => 'required|exists:App\Models\Auditor\Form,id,is_deleted,1',             
                'assigner' => 'required',                
                'end_date' => 'required|date_format:Y-m-d',
            ]
        );
        
        if ($validator->fails()) {
            $this->flashMessage('error', 'Update Journey Plan', $validator->errors()->first());
            return redirect()
            ->route('auditor.journeyplan.edit', ['journeyplan' => $id])
            ->withInput($request->all());
        }
        $journeyPlan = JourneyPlan::find($id);
        // return $journeyPlan;
        $now = Carbon::now();       
        
        if (!(strtotime($request->end_date) >= strtotime('today'))) {
            $this->flashMessage('error', 'Update Journey Plan', "Can't Update Journey Plan, End Date Exceed Start Date");
            return redirect()
                ->route('auditor.journeyplan.edit', ['journeyplan' => $id])
                ->withInput($request->all());
        }

        try {
            $journeyPlan->name = $name;            
            $journeyPlan->id_form = $id_form;            
            $journeyPlan->assigner = $assigner;   

            $journeyPlan->end_date = $end_date;
            $journeyPlan->updated_by = \auth()->user()->id;
            $journeyPlan->updated_at = $now->unix();
            $journeyPlan->save();
            $this->flashMessage('success', 'Update Journey Plan', $name . ' Has Been Updated.');
        } catch (\Exception $e) {
            $this->flashMessage('error', 'Update Journey Plan Error', $e->getMessage());
            return redirect()
                ->route('auditor.journeyplan.edit', ['journeyplan' => $id])
                ->withInput($request->all());
        }
        return redirect()->route('auditor.journeyplan.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response | RedirectResponse
     */
    public function destroy($id)
    {
        $validator = Validator::make(
            [
                'id' => $id,
            ],
            [
                'id' => 'required|exists:App\Models\Auditor\JourneyPlan,id,is_deleted,1',
            ]
        );
        if ($validator->fails()) {
            $this->flashMessage('error', 'Delete Journey Plan', $validator->errors()->first());
            return redirect()
                ->route('auditor.journeyplan.index');
        }
        try {
            $journeyPlan = JourneyPlan::where('id', $id)
                ->where('is_deleted', 1)
                ->first();
            $deleted_by = \auth()->user()->id;
            $now = Carbon::now();
            if (Carbon::parse($journeyPlan->start_date)->diffInDays($now) < 2) {
                $this->flashMessage('error', 'Delete Journey Plan', "Can't Delete Journey Plan, Start Date Exceeds Than 2 Days");
                return redirect()
                    ->route('auditor.journeyplan.index');
            }
            DB::transaction(function () use ($journeyPlan, $deleted_by, $now) {
                $journeyPlan->is_deleted = 2;
                $journeyPlan->updated_by = $deleted_by;
                $journeyPlan->updated_at = $now->unix();
                $journeyPlan->save();
                AuditPlan::where('id_journey_plan', '=', $journeyPlan->id)
                    ->where('is_deleted', '=', 1)
                    ->update([
                        'is_deleted' => 2,
                        'updated_by' => $deleted_by,
                        'updated_at' => $now->unix(),
                    ]);
            });
        } catch (\Exception $e) {
            $this->flashMessage('error', 'Delete Journey Plan Error', $e->getMessage());
            return redirect()
                ->route('auditor.journeyplan.index');
        }
        return redirect()->route('auditor.journeyplan.index');
    }

    /**
     * @return StreamedResponse
     */
    public function export()
    {
        $request = Request::capture();
        $search = $request->query('search', '');
        $startDate = $request->query('fromDate', '');
        $endDate = $request->query('toDate', '');
        $file_name = sprintf('auditor_acitivity-%s.xlsx', Str::uuid()->toString());
        return response()->streamDownload(function () use ($search, $startDate, $endDate) {
            echo file_get_contents(config('app.export_host') . '/auditor?' . http_build_query(['search' => $search, 'from_date' => $startDate, 'to_date' => $endDate]));
        }, $file_name);
    }

    public function doImport(Request $request)
    {
        if (!$request->hasFile('import_file_pjp')) {
            $this->flashMessage('error', 'Upload File Hunter', 'No such a file');
            return redirect('/auditor/journeyplan');
        }

        $file = $request->file("import_file_pjp");
        if (strtolower($file->getClientMimeType()) != "text/csv" && $file->getClientOriginalExtension() != "csv") {
            $this->flashMessage('error', 'Upload File Hunter', 'not csv file');
            return redirect('/auditor/journeyplan');
        }

        //Move Uploaded File
        $destinationPath = config("app.job_base_dir") . "/auditor_imports";
        $fileName = Uuid::uuid4()->toString() . "." . $file->getClientOriginalExtension();
        $originalFileName = $file->getClientOriginalName();

        try {
            DB::beginTransaction();
            $file = fopen($file, "r");
            // Read through the file and store the contents as an array
            $importData_arr = array();
            $i = 0;
            //Read the contents of the uploaded file 
            while (($filedata = fgetcsv($file, 1000, ",")) !== FALSE) {
                $num = count($filedata);
                // Skip first row (Remove below comment if you want to skip the first row)
                if ($i == 0) {
                    $i++;
                    continue;
                }
                for ($c = 0; $c < $num; $c++) {
                    $importData_arr[$i][] = $filedata[$c];
                }
                $i++;
            }

            $check_error_messages = $this->checkPJPError($importData_arr);

            if ($check_error_messages) {                
                $this->createPJPJob($destinationPath, $fileName, $originalFileName, $check_error_messages, 3);
                $this->flashMessage('error', 'Upload File Journey Plan', 'Upload Error');
                DB::commit();
                return redirect('/auditor/journeyplan');
            } else {
                foreach ($importData_arr as $importData) {
                    $id_juragan = explode(";", $importData[0])[6];
                    $juragan = Juragan::find($id_juragan);
                    $journeyplan = new JourneyPlan();
                    $journeyplan->id = explode(";", $importData[0])[0];;
                    $journeyplan->name = explode(";", $importData[0])[1];
                    $journeyplan->start_date = explode(";", $importData[0])[2];
                    $journeyplan->end_date = explode(";", $importData[0])[3];
                    $journeyplan->id_province = explode(";", $importData[0])[4];
                    $journeyplan->id_city = explode(";", $importData[0])[5];
                    $journeyplan->id_juragan = explode(";", $importData[0])[6];
                    $journeyplan->assign_to = explode(";", $importData[0])[7];
                    $journeyplan->id_form = 9;
                    $journeyplan->id_district = $juragan->id_district;
                    $journeyplan->id_village = $juragan->id_village;
                    $journeyplan->assigner = \auth()->user()->id;
                    $journeyplan->is_deleted = 1;
                    $journeyplan->created_at = Carbon::now()->unix();
                    $journeyplan->created_by = \auth()->user()->id;
                    $journeyplan->updated_at = $journeyplan->created_at;
                    $journeyplan->updated_by = \auth()->user()->id;
                    $journeyplan->save();
                    // insert to audit plan                                         
                    $auditPlan = new AuditPlan();
                    // note: start get outlet -> outlet.outlet
                    $get_outlet = Outlet::find(explode(";", $importData[0])[8]);
                    // note: end get outlet -> outlet.outlet
                    $auditPlan->id = Uuid::uuid4()->toString();
                    $auditPlan->id_journey_plan = $journeyplan->id;
                    $auditPlan->id_outlet = explode(";", $importData[0])[8];
                    $auditPlan->is_deleted = 1;
                    $auditPlan->created_at = Carbon::now()->unix();
                    $auditPlan->created_by = \auth()->user()->id;
                    $auditPlan->updated_at = $auditPlan->created_at;
                    $auditPlan->updated_by = \auth()->user()->id;
                    $auditPlan->transaksi_akhir = $get_outlet->final_transaction_in_one_month;
                    $auditPlan->pembelian_dalam_satu_bulan = $get_outlet->number_of_purchases_in_one_month;
                    $auditPlan->save();

                }
                $this->flashMessage('success', 'Upload File Journey Plan', 'Upload Success');
                $this->createPJPJob($destinationPath, $fileName, $originalFileName, $check_error_messages, 2);
                DB::commit();
                return redirect('/auditor/journeyplan');
            }
        } catch (\Exception $e) {
            Log::error($e);
            DB::rollBack();
            dd($e);
            $this->flashMessage('error', 'Upload File Journey Plan', 'Upload Error');
            return redirect('/auditor/journeyplan');
        }
    }

    public function checkPJPError($importData_arr)
    {
        $error_message = [];

        if (count($importData_arr) > 15) {
            $message = 'Data melebihi 15 baris';
            array_push($error_message, $message);
        }

        foreach ($importData_arr as $index => $importData) {
            $now_date = Carbon::now()->toDateTimeString();

            $id_pjp = explode(";", $importData[0])[0];
            $start_date = explode(";", $importData[0])[2];
            $id_juragan = explode(";", $importData[0])[6];
            $assign_to = explode(";", $importData[0])[7];
            $outlet_id = explode(";", $importData[0])[8];
            
            $check_pjp_id = JourneyPlan::find($id_pjp);            
            $check_start_date = date("Y-m-d", strtotime($start_date)) < date("Y-m-d", strtotime($now_date));            
            $check_juragan_id = Juragan::find($id_juragan);
            $check_auditor_id = Auditor::find($assign_to);
            $check_outlet_id = Outlet::find($outlet_id);
            $juragan_outlet = @$check_outlet_id->id_juragan;
            $auditor_city = @$check_auditor_id->id_city;
            $juragan_city = @$check_juragan_id->id_city;

            if ($check_pjp_id) {
                $message = 'ID PJP baris ke-' . $index . ' sudah terdaftar';
                array_push($error_message, $message);
            }
            if ($check_start_date) {
                $message = 'Start Date baris ke-' . $index . ' lebih lampau tanggal upload';
                array_push($error_message, $message);
            }            
            if (!$check_juragan_id) {
                $message = 'Juragan pada baris ke-' . $index . ' belum terdaftar';
                array_push($error_message, $message);
            }

            if (!$check_auditor_id) {
                $message = 'Auditor pada baris ke-' . $index . ' tidak terdaftar';
                array_push($error_message, $message);
            }

            if (!$check_outlet_id) {
                $message = 'Outlet pada baris ke-' . $index . ' tidak terdaftar';
                array_push($error_message, $message);
            }

            if ($juragan_outlet != $id_juragan) {
                $message = 'Outlet pada baris ke-' . $index . ' bukan milik juragan yang dipilih';
                array_push($error_message, $message);
            }

            if ($auditor_city != $juragan_city) {
                $message = 'Kota Juragan pada baris ke-' . $index . ' tidak sama dengan kota auditor';
                array_push($error_message, $message);
            }
        }

        return $error_message;
    }

    public function createPJPJob($destinationPath, $fileName, $originalFileName, $error_message, $status_id)
    {
        $data = new PJPJob();
        $data->type = 1;
        $data->file_path = $destinationPath . "/";
        $data->file_name = $fileName;
        $data->file_name_origin = $originalFileName;
        $data->status_id = $status_id;
        $content = array();
        foreach ($error_message as $message) {
            array_push($content, $message);
        }
        $content = json_encode($content);        
        $data->error_description = $content;
        $data->created_at = Carbon::now();
        $data->created_by = auth()->user()->id;        
        $data->save();        
    }
}
