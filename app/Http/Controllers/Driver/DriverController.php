<?php

namespace App\Http\Controllers\Driver;

use App\export\DriverExport;
use App\Http\Controllers\GenericController;
use App\Models\City;
use App\Models\District;
use App\Models\Driver\Drivers;
use App\Models\Province;
use App\Models\Scheduler\Job;
use App\Models\UserType;
use App\Models\Village;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Ramsey\Uuid\Uuid;

class DriverController extends GenericController
{

    protected $relation = [
        'vehicle',
        'provinces',
        'cities',
        'districts',
        'villages'
    ];

    private function edit_rules($id)
    {
        $rules = [
            'phone' => ['required'],
            'provinces' => ['required'],
            'user_type' => ['required'],
            'cities' => ['required'],
            'districts' => ['required'],
            'villages' => ['required'],
            'password' => ['max:255'],
            'confirm_password' => ['same:password', 'max:255'],
            'name' => ['required', 'string', 'unique:' . Drivers::class . ',name,' . $id],
            'email' => ['required', 'unique:' . Drivers::class . ',email,' . $id]
        ];
        return $rules;
    }

    protected $rules = [
        'name' => 'required|unique:' . Drivers::class . ',name',
        'email' => 'required|unique:' . Drivers::class . ',email',
        'password' => 'required|min:6|max:255',
        'confirm_password' => 'required|same:password|min:6|max:255',
        'phone' => 'required',
        'provinces' => 'required',
        'user_type' => 'required',
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
        $query = Drivers::orderBy('created_at', 'DESC');
        $search = trim($request->query('search', ''));
        if ($search !== '') {
            $query->where(function ($query) use ($search) {
                $query->orWhereRaw("id ilike ?", ["%" . $search . "%"])
                    ->orWhereRaw("id_unilever ilike ?", ["%" . $search . "%"])
                    ->orWhereRaw("name ilike ?", ["%" . $search . "%"])
                    ->orWhereRaw("email ilike ?", ["%" . $search . "%"])
                    ->orWhereRaw("phone ilike ?", ["%" . $search . "%"]);
            });
        }
//        $data = $data->whereRaw("lower(phone) ilike ?", ["%" . $request->query("search") . "%"])
//            ->orWhereRaw("lower(id_unilever) ilike ?", ["%" . $request->query("search") . "%"])
//            ->orWhereRaw("lower(name) ilike ?", ["%" . $request->query("search") . "%"])
//            ->orWhereRaw("lower(email) ilike ?", ["%" . $request->query("search") . "%"]);

        // if($request->query("phone") != ""){
        // $data = $data->where("phone", $request->query("phone"));
        // }
        // if($request->query("unilever_id") != ""){
        // $data = $data->where("id_unilever", $request->query("sea"));
        // }
//        $data = $data->paginate(30);
        $data = $query->paginate(30);
        $data->appends($request->query());
        $data = [
            'datas' => $data
        ];
        return view('Driver/Driver/index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $provinces = Province::all()->pluck('name', 'id');
        $user_type = UserType::all()->pluck('name', 'id');
        $datas = [
            'user_type' => $user_type,
            'provinces' => $provinces,
            'route' => route('driver.store')
        ];
        return view('Driver/Driver/create', $datas);
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
            return Redirect::to(route('driver.create'))
                ->withErrors($validator)->withInput($request->all());
        } else {
            DB::beginTransaction();
            $phone = preg_replace('/^0/', '62', $request->phone);
            try {
                $data = new Drivers();
                $data->id = $request->village . Carbon::now()->getPreciseTimestamp(3);
                $data->name = $request->name;
                $data->email = $request->email;
                $data->address = $request->address;
                $data->phone = $phone;
                $data->id_user_types = $request->user_type;
                $data->status_active = 1;
                $data->is_deleted = 1;
                $data->id_unilever = 'DR-' . $phone;
                $data->id_province = $request->provinces;
                $data->id_city = $request->cities;
                $data->id_district = $request->districts;
                $data->id_village = $request->villages;
                $data->created_at = Carbon::now()->timestamp;
                $data->created_by = Auth::user()->id;
                $data->start_date = $request->start_date;
                $data->end_date = $request->end_date;
//                dd($data);
                $data->save();
                $driver_data = [
                    'id' => $data->id . Carbon::now()->getPreciseTimestamp(3),
                    'driver_id' => $data->id,
                    'username' => $phone,
                    'password' => bcrypt($request->input('password')),
                    'created_by' => Auth::user()->id,
                    'is_deleted' => 1
                ];
//                dd($driver_data);
                $data->login()->create($driver_data);
                DB::commit();
                $this->flashMessage('success', 'CREATE', 'Add data success');
                return Redirect::to(route('driver.index'));
            } catch (\Exception $e) {
                DB::rollback();
                Log::error($e);
                $this->flashMessage('error', 'CREATE', $e->getMessage());
                return Redirect::to(route('driver.create'));
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
        $data = Drivers::with(["province", "city", "district", "village"])->find($id);
        if ($data == null) {
            $this->flashMessage('error', 'SHOW', 'Data not found');
            return Redirect::to(route('driver.index'));
        }
        $datas = [
            'data' => $data, ''
        ];
        return view('Driver/Driver/show', $datas);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $data = Drivers::with(["province", "city", "district", "village"])->find($id);
            $provinces = Province::all()->pluck('name', 'id');
            $cities = City::where("id_province", $data->province->id)->pluck('name', 'id');
            $districts = District::where("id_city", $data->city->id)->pluck('name', 'id');
            $villages = Village::where("id_district", $data->district->id)->pluck('name', 'id');
            $user_type = UserType::all()->pluck('name', 'id');
            if ($data == null) {
                $this->flashMessage('error', 'EDIT', 'Data not found');
                return redirect(route('driver.index'));
            }
            $datas = [
                'data' => $data,
                'provinces' => $provinces,
                'cities' => $cities,
                'districts' => $districts,
                'villages' => $villages,
                'user_type' => $user_type,
            ];
            return view('Driver/Driver/edit', $datas);
        } catch (\Exception $e) {
            Log::error($e);
            $this->flashMessage('error', 'EDIT', $e->getMessage());
            return redirect(route('driver.index'));
        }
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
            return Redirect::to(route('driver.edit', ['driver' => $id]))
                ->withErrors($validator)->withInput($request->all());
        } else {
            DB::beginTransaction();
            $phone = preg_replace('/^0/', '62', $request->phone);
            try {
                $data = Drivers::find($id);
                $data->name = $request->name;
                $data->email = $request->email;
                $data->address = $request->address;
                $data->phone = $phone;

                $data->id_unilever = 'DR-' . $phone;
                $data->id_province = $request->provinces;
                $data->id_city = $request->cities;
                $data->id_district = $request->districts;
                $data->id_village = $request->villages;
                $data->updated_at = Carbon::now()->timestamp;
                $data->updated_by = Auth::user()->id;
                $data->start_date = $request->start_date;
                $data->end_date = $request->end_date;
                $data->save();
                $driver_data = [
                    'username' => $phone,
                    'updated_by' => Auth::user()->id
                ];
                if (!empty($request->password)) {
                    $driver_data['password'] = bcrypt($request->input('password'));
                }
                $data->login()->update($driver_data);
                DB::commit();
                $this->flashMessage('success', 'EDIT', 'Edit data success');
                return Redirect::to(route('driver.index'));
            } catch (\Exception $e) {
                DB::rollback();
                Log::error($e);
                $this->flashMessage('error', 'EDIT', $e->getMessage());
                return Redirect::to(route('driver.edit', ['driver' => $id]));
            }
        }
    }

    public function importForm()
    {
        $users = new Job();
        $data = $users->with("status")->where('type', 2)->orderBy("created_at", "desc")->paginate(20);
        foreach ($data as $driver) {
            $data_messages[] = json_decode($driver->error_description);
        }        
        $datas = [
            'datas' => $data,
            'data_messages' => @$data_messages
        ];
        return view("Driver/Driver/import", $datas);
    }

    // public function doImport(Request $request)
    // {
    //     if (!$request->hasFile('import_file')) {
    //         $this->flashMessage('error', 'Upload File Driver', 'No such a file');
    //         return Redirect::to(route('driver.import_form'));
    //     }

    //     $file = $request->file("import_file");
    //     if (strtolower($file->getClientOriginalExtension()) != "csv") {
    //         $this->flashMessage('error', 'Upload File Driver', 'not csv file');
    //         return Redirect::to(route('driver.import_form'));
    //     }

    //     //Move Uploaded File
    //     $destinationPath = config("app.job_base_dir") . "/driver_imports";
    //     $fileName = Uuid::uuid4()->toString() . "." . $file->getClientOriginalExtension();
    //     try {
    //         $file->move($destinationPath, $fileName);
    //         // save to job table
    //         $data = new Job();
    //         $data->type = 2;
    //         $data->action = 'insert';
    //         $data->file_path = $destinationPath . "/";
    //         $data->file_name = $fileName;
    //         $data->file_name_origin = $file->getClientOriginalName();
    //         $data->status_id = 1;
    //         $data->created_at = Carbon::now();
    //         $data->created_by = auth()->user()->id;
    //         $data->save();
    //         $this->flashMessage('success', 'Upload File Driver', 'Upload Success');
    //         return Redirect::to(route('driver.import_form'));
    //     } catch (Exception $e) {
    //         Log::error($e);
    //         $this->flashMessage('error', 'Upload File Driver', 'Upload Error');
    //         return Redirect::to(route('driver.import_form'));
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
            $name = explode(";", $importData[0])[0];
            $phone = preg_replace('/^0/', '62', explode(";", $importData[0])[1]);
            $email = explode(";", $importData[0])[2];
            $password = explode(";", $importData[0])[3];
            $password_confirmation = explode(";", $importData[0])[4];
            $user_type = explode(";", $importData[0])[5];
            $id_province = explode(";", $importData[0])[6];
            $id_city = explode(";", $importData[0])[7];
            $id_district = explode(";", $importData[0])[8];
            $id_village = explode(";", $importData[0])[9];

            $check_name = Drivers::where('name', $name)->first();
            $check_email = Drivers::where('email', $email)->first();
            $check_province = Province::find($id_province);
            $check_city = City::find($id_city);
            $check_district = District::find($id_district);
            $check_village = Village::find($id_village);

            if ($check_name) {
                $message = 'Nama driver baris ke-' . $index . ' sudah terdaftar';
                array_push($error_message, $message);
            }

            if ($check_email) {
                $message = 'Email driver baris ke-' . $index . ' sudah terdaftar';
                array_push($error_message, $message);
            }

            if (strlen($password) < 6) {
                $message = 'Password baris ke-' . $index . ' kurang dari 6 karakter';
                array_push($error_message, $message);
            }

            if ($password != $password_confirmation) {
                $message = 'Password dan Password confirmation baris ke-' . $index . ' tidak sama';
                array_push($error_message, $message);
            }

            if ($phone == '') {
                $message = 'Phone baris ke-' . $index . ' tidak boleh kosong';
                array_push($error_message, $message);
            }

            if ($user_type == '') {
                $message = 'User type baris ke-' . $index . ' tidak boleh kosong';
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
            $this->flashMessage('error', 'Upload File Driver', 'No such a file');
            return Redirect::to(route('driver.import_form'));
        }

        $file = $request->file("import_file");
        if (strtolower($file->getClientOriginalExtension()) != "csv") {
            $this->flashMessage('error', 'Upload File Driver', 'not csv file');
            return Redirect::to(route('driver.import_form'));
        }

        //Move Uploaded File
        $destinationPath = config("app.job_base_dir") . "/driver_imports";
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
                $this->createDriverJob($destinationPath, $fileName, $originalFileName, $check_error_messages, 3);
                $this->flashMessage('error', 'Upload File Hunter', 'Upload Error');
                return redirect('/import/driver');
            } else {
                foreach ($importData_arr as $index => $importData) {
                    $phone = preg_replace('/^0/', '62', explode(";", $importData[0])[1]);
                    $password = explode(";", $importData[0])[3];
                    $id_village = explode(";", $importData[0])[9];
                    $start_date = Carbon::parse(explode(";", $importData[0])[10])->format("Y-m-d");
                    $end_date = Carbon::parse(explode(";", $importData[0])[11])->format("Y-m-d");

                    DB::beginTransaction();
                    $data = new Drivers();
                    $data->id = $id_village . Carbon::now()->getPreciseTimestamp(3);
                    $data->name = explode(";", $importData[0])[0];
                    $data->email = explode(";", $importData[0])[2];
                    $data->phone = $phone;
                    $data->id_user_types = explode(";", $importData[0])[5];
                    $data->status_active = 1;
                    $data->is_deleted = 1;
                    $data->id_unilever = 'DR-' . $phone;
                    $data->id_province = explode(";", $importData[0])[6];
                    $data->id_city = explode(";", $importData[0])[7];
                    $data->id_district = explode(";", $importData[0])[8];
                    $data->id_village = $id_village;
                    $data->created_at = Carbon::now()->timestamp;
                    $data->created_by = Auth::user()->id;
                    $data->start_date = $start_date;
                    $data->end_date = $end_date;
                    // dd($data);
                    $data->save();
                    $driver_data = [
                        'id' => $data->id . Carbon::now()->getPreciseTimestamp(3),
                        'driver_id' => $data->id,
                        'username' => $phone,
                        'password' => bcrypt($password),
                        'created_by' => Auth::user()->id,
                        'is_deleted' => 1
                    ];
                    // dd($driver_data);
                    $data->login()->create($driver_data);
                    DB::commit();
                    // $file->move($destinationPath, $fileName);

                }
                $this->createDriverJob($destinationPath, $fileName, $originalFileName, $check_error_messages, 2);
                $this->flashMessage('success', 'Upload File Driver', 'Upload Success');
                return redirect('/import/driver');
            }
        } catch (\Exception $e) {
            Log::error($e);
            $this->flashMessage('error', 'Upload File Driver', 'Upload Error');
            return Redirect::to(route('driver.import_form'));
        }
    }

    public function createDriverJob($destinationPath, $fileName, $originalFileName, $error_message, $status_id)
    {
        // save to job table
        $data = new Job();
        $data->type = 2;
        $data->action = 'insert';
        $data->file_path = $destinationPath . "/";
        $data->file_name = $fileName;
        $data->file_name_origin = $originalFileName;
        $content = array();
        foreach ($error_message as $message) {
            array_push($content, $message);
        }
        $content = json_encode($content);
        $data->error_description = $content;
        $data->status_id = $status_id;
        $data->created_at = Carbon::now();
        $data->created_by = auth()->user()->id;

        return $data->save();
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
        $file_name = sprintf('driver-%s.xlsx', Str::uuid()->toString());
        return Excel::download(new DriverExport($search), $file_name);
    }
}
