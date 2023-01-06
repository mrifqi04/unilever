<?php

namespace App\Http\Controllers\Driver;

use App\export\DriverExport;
use App\export\VehicleExport;
use App\Http\Controllers\GenericController;
use App\Models\City;
use App\Models\District;
use App\Models\Driver\Vehicles;
use App\Models\Province;
use App\Models\Scheduler\Job;
use App\Models\Village;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Ramsey\Uuid\Uuid;

class VehicleController extends GenericController
{

    private function edit_rules($id)
    {
        $rules = [
            'provinces' => ['required'],
            'cities' => ['required'],
            'districts' => ['required'],
            'villages' => ['required'],
            'license_number' => ['required', 'max:12', 'unique:' . Vehicles::class . ',license_number,' . $id],
        ];
        return $rules;
    }

    protected $rules = [
        'license_number' => 'required|unique:' . Vehicles::class . ',license_number',
        'provinces' => 'required',
        'cities' => 'required',
        'districts' => 'required',
        'villages' => 'required'
    ];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = Vehicles::with(["province", "city", "district", "village"]);
        $licenseNumber = $request->query("license_number", "");
        if (trim($licenseNumber) !== "") {
            $data = $data->where("license_number", "ilike", "%{$licenseNumber}%");
        }
        $data = $data->orderBy('created_at', 'DESC');
        $data = $data->paginate(30);
        $data->appends($request->query());
        $data = [
            'datas' => $data
        ];
//        dd($data);
        return view('Driver/Vehicle/index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $provinces = Province::all()->pluck('name', 'id');
        $datas = [
            'provinces' => $provinces,
            'route' => route('vehicle.store')
        ];
        return view('Driver/Vehicle/create', $datas);
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
            return Redirect::to(route('vehicle.create'))
                ->withErrors($validator)->withInput($request->all());
        } else {
            DB::beginTransaction();
            try {
                $data = new Vehicles();
                $data->id = $request->village . Carbon::now()->getPreciseTimestamp(3);
                $data->license_number = $request->license_number;
                $data->id_province = $request->provinces;
                $data->id_city = $request->cities;
                $data->id_district = $request->districts;
                $data->id_village = $request->villages;
                $data->created_by = Auth::user()->id;
                $data->created_at = Carbon::now()->timestamp;
                $data->save();
                DB::commit();
                $this->flashMessage('success', 'CREATE', 'Add data success');
                return Redirect::to(route('vehicle.index'));
            } catch (\Exception $e) {
                DB::rollback();
                Log::error($e);
                $this->flashMessage('error', 'CREATE', $e->getMessage());
                return Redirect::to(route('vehicle.create'));
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
        $data = Vehicles::with(["province", "city", "district", "village"])->find($id);
        if ($data == null) {
            $this->flashMessage('error', 'SHOW', 'Data not found');
            return Redirect::to(route('vehicle.index'));
        }
        $datas = [
            'data' => $data
        ];
        return view('Driver/Vehicle/show', $datas);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = Vehicles::with(["province", "city", "district", "village"])->find($id);
        $provinces = Province::all()->pluck('name', 'id');
        $cities = City::where("id_province", $data->province->id)->pluck('name', 'id');
        $districts = District::where("id_city", $data->city->id)->pluck('name', 'id');
        $villages = Village::where("id_district", $data->district->id)->pluck('name', 'id');
        if ($data == null) {
            $this->flashMessage('error', 'EDIT', 'Data not found');
            return redirect(route('vehicle.index'));
        }
        $datas = [
            'data' => $data,
            'provinces' => $provinces,
            'cities' => $cities,
            'districts' => $districts,
            'villages' => $villages,
        ];
//        dd($datas);
        return view('Driver/Vehicle/edit', $datas);
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
            return Redirect::to(route('vehicle.edit', ['vehicle' => $id]))
                ->withErrors($validator)->withInput($request->all());
        } else {
            DB::beginTransaction();
            try {
                $data = Vehicles::find($id);
                $data->license_number = $request->license_number;
                $data->id_province = $request->provinces;
                $data->id_city = $request->cities;
                $data->id_district = $request->districts;
                $data->id_village = $request->villages;
                $data->updated_by = Auth::user()->id;
                $data->updated_at = Carbon::now()->timestamp;
                $data->save();
                DB::commit();
                $this->flashMessage('success', 'EDIT', 'Edit data success');
                return Redirect::to(route('vehicle.index'));
            } catch (\Exception $e) {
                DB::rollback();
                Log::error($e);
                $this->flashMessage('error', 'EDIT', $e->getMessage());
                return Redirect::to(route('vehicle.edit', ['vehicle' => $id]));
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

    public function importForm(Request $request)
    {
        $users = new Job();
        $data = $users->with("status")->where('type', 3)->orderBy("created_at", "desc")->paginate(20);
        foreach ($data as $vehicle) {
            $data_messages[] = json_decode($vehicle->error_description);
        }  
        
        $datas = [
            'datas' => $data,
            'data_messages' => @$data_messages
        ];
        return view("Driver/Vehicle/import", $datas);
    }

    // public function doImport(Request $request)
    // {
    //     if (!$request->hasFile('import_file')) {
    //         $this->flashMessage('error', 'Upload File Vehicle', 'No such a file');
    //         return Redirect::to(route('vehicle.import_form'));
    //     }

    //     $file = $request->file("import_file");
    //     if (strtolower($file->getClientOriginalExtension()) != "csv") {
    //         $this->flashMessage('error', 'Upload File Vehicle', 'not csv file');
    //         return Redirect::to(route('vehicle.import_form'));
    //     }

    //     //Move Uploaded File
    //     $destinationPath = config("app.job_base_dir") . "/vehicle_imports";
    //     $fileName = Uuid::uuid4()->toString() . "." . $file->getClientOriginalExtension();
    //     try {
    //         $file->move($destinationPath, $fileName);
    //         // save to job table
    //         $data = new Job();
    //         $data->type = 3;
    //         $data->action = 'insert';
    //         $data->file_path = $destinationPath . "/";
    //         $data->file_name = $fileName;
    //         $data->file_name_origin = $file->getClientOriginalName();
    //         $data->status_id = 1;
    //         $data->created_at = Carbon::now();
    //         $data->created_by = auth()->user()->id;
    //         $data->save();
    //         $this->flashMessage('success', 'Upload File Vehicle', 'Upload Success');
    //         return Redirect::to(route('vehicle.import_form'));
    //     } catch (Exception $e) {
    //         Log::error($e);
    //         $this->flashMessage('error', 'Upload File Vehicle', 'Upload Error');
    //         return Redirect::to(route('vehicle.import_form'));
    //     }
    // }

    public function checkCSVError($importData_arr)
    {
        $error_message = [];

        if (count($importData_arr) > 15) {
            $message = 'Data melebihi 15 baris';
            array_push($error_message, $message);
        }

        foreach ($importData_arr as $index => $importData) {
            $license_number = explode(";", $importData[0])[0];
            $id_province = explode(";", $importData[0])[1];
            $id_city = explode(";", $importData[0])[2];
            $id_district = explode(";", $importData[0])[3];
            $id_village = explode(";", $importData[0])[4];

            $check_license_number = Vehicles::where('license_number', $license_number)->first();
            $check_province = Province::find($id_province);
            $check_city = City::find($id_city);
            $check_district = District::find($id_district);
            $check_village = Village::find($id_village);

            if ($check_license_number) {
                $message = 'License Number baris ke-' . $index . ' sudah terdaftar';
                array_push($error_message, $message);
            }

            if (!$check_province) {
                $message = 'ID Province baris ke-' . $index . ' tidak valid';
                array_push($error_message, $message);
            }

            if (!$check_city) {
                $message = 'ID City baris ke-' . $index . ' tidak valid';
                array_push($error_message, $message);
            }

            if (!$check_district) {
                $message = 'ID District baris ke-' . $index . ' tidak valid';
                array_push($error_message, $message);
            }

            if (!$check_village) {
                $message = 'ID Village baris ke-' . $index . ' tidak valid';
                array_push($error_message, $message);
            }
        }

        return $error_message;
    }

    public function doImport(Request $request)
    {
        if (!$request->hasFile('import_file')) {
            $this->flashMessage('error', 'Upload File Vehicle', 'No such a file');
            return Redirect::to(route('vehicle.import_form'));
        }

        $file = $request->file("import_file");
        if (strtolower($file->getClientOriginalExtension()) != "csv") {
            $this->flashMessage('error', 'Upload File Vehicle', 'not csv file');
            return Redirect::to(route('vehicle.import_form'));
        }

        //Move Uploaded File
        $destinationPath = config("app.job_base_dir") . "/vehicle_imports";
        $fileName = Uuid::uuid4()->toString() . "." . $file->getClientOriginalExtension();
        $originalFileName = $file->getClientOriginalName();

        try {
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

            $check_error_messages = $this->checkCSVError($importData_arr);
            // dd($check_error_messages);
            if ($check_error_messages) {
                $this->createVehicleJob($destinationPath, $fileName, $originalFileName, $check_error_messages, 3);
                $this->flashMessage('error', 'Upload File Vehicle', 'Upload Error');
                return redirect('/import/vehicle');
            } else {
                foreach ($importData_arr as $index => $importData) {                  
                    $id_village = explode(";", $importData[0])[4];                    

                    DB::beginTransaction();
                    $data = new Vehicles();
                    $data->id = $id_village . Carbon::now()->getPreciseTimestamp(3);
                    $data->license_number = explode(";", $importData[0])[0];
                    $data->id_province = explode(";", $importData[0])[1];
                    $data->id_city = explode(";", $importData[0])[2];
                    $data->id_district = explode(";", $importData[0])[3];
                    $data->id_village = $id_village;
                    $data->created_by = Auth::user()->id;
                    $data->created_at = Carbon::now()->timestamp;
                    $data->save();
                    DB::commit();
                }
                $this->createVehicleJob($destinationPath, $fileName, $originalFileName, $check_error_messages, 2);
                $this->flashMessage('success', 'Upload File Vehicle', 'Upload Success');
                return redirect('/import/vehicle');
            }
        } catch (\Exception $e) {
            Log::error($e);
            $this->flashMessage('error', 'Upload File Vehicle', 'Upload Error');
            return Redirect::to(route('vehicle.import_form'));
        }
    }

    public function createVehicleJob($destinationPath, $fileName, $originalFileName, $error_message, $status_id)
    {
        $data = new Job();
        $data->type = 3;
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

        return $data->save();
    }

    public function export()
    {
        $request = Request::capture();
        $search = $request->query('search', '');
        $file_name = sprintf('vehicle-%s.xlsx', Str::uuid()->toString());
        return Excel::download(new VehicleExport($search), $file_name);
    }
}
