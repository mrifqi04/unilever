<?php

namespace App\Http\Controllers\JuraganManagement;

/**
 * Description of JuraganController
 *
 * @author nuansa.ramadhan
 */

use App\export\JuraganExport;
use App\export\JuraganValidasiToko;
use App\export\OutletStatusExport;
use App\Http\Resources\JuraganSummary;
use App\Models\Province;
use App\Models\City;
use App\Models\District;
use App\Models\Scheduler\JuraganJob;
use App\Models\Village;
use Illuminate\Http\Request;
use App\Http\Controllers\GenericController;
use App\Models\JuraganManagement\Juragan;
use App\Models\JuraganManagement\Login;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Mockery\Exception;
use Ramsey\Uuid\Uuid;

class JuraganController extends GenericController
{

    //put your code here
    public function __construct()
    {
        parent::__construct();
    }

    private function normalizePhone(string $arg): string
    {
        $result = '';
        if (trim($arg) != '') {
            if ($arg[0] == '0') {
                $result = '62' . substr($arg, 1, strlen($arg) - 1);
            } else {
                $result = $arg;
            }
        }
        return $result;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //        $users = new Juragan();
        $users = Juragan::where('is_deleted', 1);
        if ($request->query("search", '') != "") {
            $users = $users->whereRaw("id_unilever_owner ilike ?", ["%" . $request->query("search") . "%"])
                ->orWhereRaw("id ilike ?", ["%" . $request->query("search") . "%"])
                ->orWhereRaw("name ilike ?", ["%" . $request->query("search") . "%"])
                ->orWhereRaw("email ilike ?", ["%" . $request->query("search") . "%"])
                ->orWhereRaw("phone ilike ?", ["%" . $request->query("search") . "%"]);
        }
        $data = $users->paginate(30);
        $data->appends($request->query());
        $data = [
            'datas' => $data
        ];

        return view('Juragan/index', $data);
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
            'route' => route('user.store')
        ];
        return view('Juragan/create', $datas);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = array(
            'name' => 'required|min:3|max:255',
            'unilever_id' => 'required|min:3|max:255',
            'email' => 'required|email|unique:App\Models\JuraganManagement\Juragan,email',
            'phone' => 'required|unique:App\Models\JuraganManagement\Juragan,phone',
            'password' => 'required|min:6|max:255',
            'confirm_password' => 'required|same:password|min:6|max:255',
            'address' => 'required|min:6|max:255',
            'zip_code' => 'required',
            'radius' => 'required',
            'radius_threshold' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'provinces' => 'required|exists:provinces,id',
            'cities' => 'required|exists:cities,id',
            'districts' => 'required|exists:districts,id',
            'villages' => 'required|exists:villages,id',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Redirect::to(route('juragan.create'))
                ->withErrors($validator)->withInput($request->all());
        } else {
            DB::beginTransaction();
            try {
                $data = new Juragan();
                $login = new Login();

                // set and save data juragan
                $data->id = $request->input('villages') . Carbon::now()->getPreciseTimestamp(3);
                $data->id_unilever_owner = $request->input("unilever_id");
                $data->name = $request->input('name');
                $data->email = $request->input('email');
                $data->phone = $this->normalizePhone($request->input('phone'));
                Log::info($this->normalizePhone($request->input('phone')));
                $data->address = $request->input('address');
                $data->zip_code = $request->input('zip_code');
                $data->latitude = $request->input('latitude');
                $data->longitude = $request->input('longitude');
                $data->id_country = config('app.country_id');
                $data->id_province = $request->input('provinces');
                $data->id_city = $request->input('cities');
                $data->id_district = $request->input('districts');
                $data->id_village = $request->input('villages');
                $data->radius_default = $request->input('radius');
                $data->radius_threshold = $request->input('radius_threshold');
                $data->created_by = \auth()->user()->id;
                $data->is_deleted = 1;
                $data->save();

                // set and save data login juragan
                $login->id = $data->id;
                $login->juragan_id = $data->id;
                $login->username = $data->email;
                $login->password = bcrypt($request->input('password'));
                $login->created_by = \auth()->user()->id;
                $login->is_deleted = 1;
                $data->login()->save($login);
                DB::commit();
                $this->flashMessage('success', 'CREATE', 'create data success');
                return Redirect::to(route('juragan.index'));
            } catch (\Exception $e) {
                DB::rollback();
                Log::error($e);
                $this->flashMessage('error', 'CREATE', $e->getMessage());
                return Redirect::to(route('juragan.create'));
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
        $data = Juragan::with(["province", "city", "district", "village", "creator", "updater"])->find($id);
        if ($data == null) {
            $this->flashMessage('error', 'SHOW', 'Data not found');
            return Redirect::to(route('juragan.index'));
        }
        //        dd($data);
        $datas = [
            'data' => $data
        ];
        return view('Juragan/show', $datas);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = Juragan::with(['province', 'city', 'district', 'village'])->find($id);
        if ($data == null) {
            $this->flashMessage('error', 'EDIT', 'Data not found');
            return redirect(route('juragan.index'));
        }
        try {
            $provinces = Province::all()->pluck('name', 'id');
            $cities = City::where("id_province", $data->province->id)->pluck('name', 'id');
            $districts = District::where("id_city", $data->city->id)->pluck('name', 'id');
            $villages = Village::where("id_district", $data->district->id)->pluck('name', 'id');
        } catch (\Exception $e) {
            Log::error($e);
        }

        $datas = [
            'data' => $data,
            'provinces' => $provinces,
            'cities' => $cities,
            'districts' => $districts,
            'villages' => $villages,
        ];

        return view('Juragan/edit', $datas);
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
        $rules = array(
            'name' => 'required|min:3|max:255',
            'unilever_id' => 'required|min:3|max:255',
            'email' => 'required|email|unique:App\Models\JuraganManagement\Juragan,email,' . $id,
            'phone' => 'required|unique:App\Models\JuraganManagement\Juragan,phone,' . $id,
            'address' => 'required|min:6|max:255',
            'zip_code' => 'required',
            'radius' => 'required',
            'radius_threshold' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'provinces' => 'required|exists:provinces,id',
            'cities' => 'required|exists:cities,id',
            'districts' => 'required|exists:districts,id',
            'villages' => 'required|exists:villages,id',
        );

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Redirect::to(route('juragan.edit', ['juragan' => $id]))
                ->withErrors($validator)->withInput($request->all());
        } else {
            DB::beginTransaction();
            try {
                $data = Juragan::find($id);
                // set and save data juragan
                $data->id_unilever_owner = $request->input("unilever_id");
                $data->name = $request->input('name');
                $data->email = $request->input('email');
                $data->phone = $this->normalizePhone($request->input('phone'));
                //                Log::info($this->normalizePhone($request->input('phone')));
                $data->address = $request->input('address');
                $data->zip_code = $request->input('zip_code');
                $data->latitude = $request->input('latitude');
                $data->longitude = $request->input('longitude');
                $data->radius_default = $request->input('radius');
                $data->radius_threshold = $request->input('radius_threshold');
                $data->id_country = config('app.country_id');
                $data->id_province = $request->input('provinces');
                $data->id_city = $request->input('cities');
                $data->id_district = $request->input('districts');
                $data->id_village = $request->input('villages');
                $data->updated_by = \auth()->user()->id;
                $data->save();

                // set and save data login juragan
                $data->login->username = $data->email;
                $data->login->save();
                DB::commit();
                $this->flashMessage('success', 'EDIT', 'Edit data success');
                return Redirect::to(route('juragan.index'));
            } catch (\Exception $ex) {
                DB::rollback();
                Log::error($ex);
                $this->flashMessage('error', 'EDIT', $ex->getMessage());
                return Redirect::to(route('juragan.edit', ['juragan' => $id]));
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
        //        $data = Role::find($id);
        //        if ($data->delete()) {
        //            Session::flash('message', 'delete data success');
        //        } else {
        //            Session::flash('message', 'delete data failed');
        //        }
        //
        //        return Redirect::to(route('role.index'));
        throw new \Exception("Not implemented yet");
    }

    /**
     * Show the form for reset password.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function resetpassword($id)
    {
        $data = Juragan::find($id);
        if ($data == null) {
            $this->flashMessage('error', 'RESET PASSWORD', 'Data not found');
            return redirect(route('juragan.index'));
        }
        $datas = [
            'data' => $data
        ];
        return view('Juragan/resetpassword', $datas);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function doresetpassword(Request $request, $id)
    {
        $rules = array(
            'password' => [
                'required',
                'min:6',
                'max:255'
            ],
            'confirm_password' => 'required|same:password|min:6|max:255',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Redirect::to(route('juragan.resetpassword', ['id' => $id]))
                ->withErrors($validator)->withInput($request->all());
        } else {
            DB::beginTransaction();
            try {
                $data = Login::where("juragan_id", $id)->first();
                $data->password = bcrypt($request->input('password'));
                $data->updated_by = \Illuminate\Support\Facades\Auth::user()->id;
                $data->save();
                DB::commit();
                $this->flashMessage('success', 'RESET PASSWORD', 'Reset Password success');
                return Redirect::to(route('juragan.index'));
            } catch (\Exception $ex) {
                DB::rollback();
                Log::error($ex);
                $this->flashMessage('error', 'RESET PASSWORD', $ex->getMessage());
                return Redirect::to(route('juragan.resetpassword', ['id' => $id]));
            }
        }
    }

    public function summary(Request $request)
    {
        $startDate = null;
        $endDate = null;

        if ($request->get("start_date") == "" || $request->get("start_date") == null) {
            $startDate = Carbon::now()->format("Y-m-d");
        } else {
            $startDate = Carbon::parse($request->get("start_date"))->format("Y-m-d");
        }

        if ($request->get("end_date") == "" || $request->get("end_date") == null) {
            $endDate = Carbon::now()->format("Y-m-d");
        } else {
            $endDate = Carbon::parse($request->get("end_date"))->format("Y-m-d");
        }


        $juraganActive = DB::select(DB::raw("select
              c.id,c.name as city_name, count(j.id) as total
            from juragan.juragans as j
            left join public.cities as c on c.id=j.id_city
            where j.is_deleted = 1
              and j.created_at::date >= :startDate::date and j.created_at::date <= :endDate::date
            group by c.id,c.name
            order by c.name"), ['startDate' => $startDate, 'endDate' => $endDate]);
        $datas = [
            'datas' => $juraganActive,
        ];
        return view('Juragan/dashboard', $datas);
    }

    public function importForm(Request $request)
    {
        $users = new JuraganJob();
        $data = $users->with("status")->orderBy("created_at", "desc")->paginate(20);

        foreach ($data as $juragan) {
            $data_messages[] = json_decode($juragan->error_description);
        }

        $datas = [
            'datas' => $data,
            'data_messages' => @$data_messages
        ];

        return view("Juragan/import", $datas);
    }

    // public function doImport(Request $request)
    // {
    //     if (!$request->hasFile('juragan_import_file')) {
    //         $this->flashMessage('error', 'Upload File Juragan', 'No such a file');
    //         return Redirect::to(route('juragan.import_form'));
    //     }

    //     $file = $request->file("juragan_import_file");
    //     if (strtolower($file->getClientOriginalExtension()) != "csv") {
    //         $this->flashMessage('error', 'Upload File Juragan', 'not csv file');
    //         return Redirect::to(route('juragan.import_form'));
    //     }

    //     //Move Uploaded File
    //     $destinationPath = config("app.job_base_dir") . "/juragan_imports";
    //     $fileName = Uuid::uuid4()->toString() . "." . $file->getClientOriginalExtension();
    //     try {
    //         $file->move($destinationPath, $fileName);
    //         // save to job table
    //         $data = new JuraganJob();
    //         $data->type = 1;
    //         $data->file_path = $destinationPath . "/";
    //         $data->file_name = $fileName;
    //         $data->file_name_origin = $file->getClientOriginalName();
    //         $data->status_id = 1;
    //         $data->created_at = Carbon::now();
    //         $data->created_by = auth()->user()->id;
    //         $data->save();
    //         $this->flashMessage('success', 'Upload File Juragan', 'Upload Success');
    //         return Redirect::to(route('juragan.import_form'));
    //     } catch (Exception $e) {
    //         Log::error($e);
    //         $this->flashMessage('error', 'Upload File Juragan', 'Upload Error');
    //         return Redirect::to(route('juragan.import_form'));
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
            $name = explode(";", $importData[0])[2];
            $phone = preg_replace('/^0/', '62', explode(";", $importData[0])[3]);
            $email = explode(";", $importData[0])[4];
            $password = explode(";", $importData[0])[5];
            $password_confirmation = explode(";", $importData[0])[6];
            $id_province = explode(";", $importData[0])[10];
            $id_city = explode(";", $importData[0])[11];
            $id_district = explode(";", $importData[0])[12];
            $id_village = explode(";", $importData[0])[13];

            $check_name = Juragan::where('name', $name)->first();
            $check_email = Juragan::where('email', $email)->first();
            $check_province = Province::find($id_province);
            $check_city = City::find($id_city);
            $check_district = District::find($id_district);
            $check_village = Village::find($id_village);

            if ($check_name) {
                $message = 'Nama juragan baris ke-' . $index . ' sudah terdaftar';
                array_push($error_message, $message);
            }

            if ($check_email) {
                $message = 'Email juragan baris ke-' . $index . ' sudah terdaftar';
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
            $this->flashMessage('error', 'Upload File Juragan', 'No such a file');
            return Redirect::to(route('juragan.import_form'));
        }

        $file = $request->file("import_file");
        if (strtolower($file->getClientOriginalExtension()) != "csv") {
            $this->flashMessage('error', 'Upload File Juragan', 'not csv file');
            return Redirect::to(route('juragan.import_form'));
        }

        //Move Uploaded File
        $destinationPath = config("app.job_base_dir") . "/juragan_imports";
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
                $this->createJuraganJob($destinationPath, $fileName, $originalFileName, $check_error_messages, 3);
                $this->flashMessage('error', 'Upload File Juragan', 'Upload Error');
                return redirect('/import/juragan');
            } else {
                foreach ($importData_arr as $index => $importData) {

                    DB::beginTransaction();
                    $data = new Juragan();
                    $login = new Login();

                    // set and save data juragan
                    $data->id = explode(";", $importData[0])[13] . Carbon::now()->getPreciseTimestamp(3);
                    $data->id_unilever_owner = explode(";", $importData[0])[0];
                    $data->id_leveredge = explode(";", $importData[0])[1];
                    $data->name = explode(";", $importData[0])[2];
                    $data->phone = $this->normalizePhone(explode(";", $importData[0])[3]);
                    Log::info($this->normalizePhone(explode(";", $importData[0])[3]));
                    $data->email = explode(";", $importData[0])[4];
                    $data->address = explode(";", $importData[0])[7];
                    $data->radius_default = explode(";", $importData[0])[8];
                    $data->radius_threshold = explode(";", $importData[0])[9];
                    $data->id_country = config('app.country_id');
                    $data->id_province = explode(";", $importData[0])[10];
                    $data->id_city = explode(";", $importData[0])[11];
                    $data->id_district = explode(";", $importData[0])[12];
                    $data->id_village = explode(";", $importData[0])[13];
                    $data->zip_code = explode(";", $importData[0])[14];
                    $data->latitude = explode(";", $importData[0])[15];
                    $data->longitude = explode(";", $importData[0])[16];
                    $data->created_by = \auth()->user()->id;
                    $data->is_deleted = 1;
                    $data->save();

                    // set and save data login juragan
                    $login->id = $data->id . Carbon::now()->getPreciseTimestamp(3);
                    $login->juragan_id = $data->id;
                    $login->username = $data->email;
                    $login->password = bcrypt(explode(";", $importData[0])[5]);
                    $login->created_by = \auth()->user()->id;
                    $login->is_deleted = 1;

                    $login->save();

                    DB::commit();
                }
                $this->createJuraganJob($destinationPath, $fileName, $originalFileName, $check_error_messages, 2);
                $this->flashMessage('success', 'Upload File Juragan', 'Upload Success');
                return redirect('/import/juragan');
            }
        } catch (\Exception $e) {
            Log::error($e);
            DB::rollback();
            dd($e);
            $this->flashMessage('error', 'Upload File Juragan', 'Upload Error');
            return Redirect::to(route('juragan.import_form'));
        }
    }

    public function createJuraganJob($destinationPath, $fileName, $originalFileName, $error_message, $status_id)
    {
        // save to job table
        $data = new JuraganJob();
        $data->type = 1;
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
        $data->save();

        return $data->save();
    }

    public function StatusApprovalOutlet(Request $request)
    {
        $startDate = null;
        $endDate = null;
        $provinceId = null;
        $cityId = null;
        $id = null;
        $name = null;
        $outletName = null;
        $model = DB::table("v_jurgan_outlet_status");
        if ($request->query("provinces") != "") {
            $model = $model->where("id_province", '=', $request->query("provinces"));
            $provinceId = $request->query("provinces");
        }
        if ($request->query("cities") != "") {
            $model = $model->where("id_city", '=', $request->query("cities"));
            $cityId = $request->query("cities");
        }
        if ($request->query("search") != "") {
            $model = $model->where("juragan_id", 'ilike', '%' . $request->query("search") . '%')
                ->orWhere("outlet_id", 'ilike', '%' . $request->query("search") . '%')
                ->orWhere("outlet_name", 'ilike', '%' . $request->query("search") . '%')
                ->orWhere("owner", 'ilike', '%' . $request->query("search") . '%')
                ->orWhere("phone", 'ilike', '%' . $request->query("search") . '%')
                ->orWhere("juragan", 'ilike', '%' . $request->query("search") . '%');
            $outletName = $request->query("search");
        }

        if ($request->get("start_date", '') !== "" && $request->get("end_date", '') !== "") {
            $startDate = Carbon::parse($request->get("start_date"));
            $endDate = Carbon::parse($request->get("end_date"));
            $model = $model->whereBetween("outlet_created", [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
            $startDate = $startDate->unix();
            $endDate = $endDate->unix();
        }

        $model = $model->paginate(10);
        $model->appends($request->query());
        if ($request->get("export") == "" || $request->get("export") == null) {
            $datas = [
                'datas' => $model,
            ];
            return view("Juragan/statusoutlet", $datas);
        } else {
            $fileName = "JuraganOutletStatus-" . Carbon::now()->unix() . ".xlsx";
            return Excel::download(new OutletStatusExport($startDate, $endDate, $provinceId, $cityId, $id, $name, $outletName), $fileName);
        }
    }

    public function export()
    {
        $request = Request::capture();
        $search = $request->query('search', '');
        $file_name = sprintf('juragan-%s.xlsx', Str::uuid()->toString());
        return Excel::download(new JuraganExport($search), $file_name);
    }

    public function exportValidasiToko()
    {
        $request = Request::capture();
        $fromDate = Carbon::parse($request->query('fromDate', Carbon::now()->format('Y-m-d')));
        $toDate = Carbon::parse($request->query('toDate', Carbon::now()->format('Y-m-d')));
        $file_name = sprintf('juragan-validasi-toko-%s.xlsx', Str::uuid()->toString());
        $query = http_build_query([
            'from_date' => $fromDate->format('Y-m-d'),
            'to_date' => $toDate->format('Y-m-d')
        ]);
       
        return Excel::download(new JuraganValidasiToko($fromDate, $toDate), $file_name);
    }

    public function exportTambahMandiri()
    {
        $request = Request::capture();
        $fromDate = Carbon::parse($request->query('fromDate', Carbon::now()->format('Y-m-d')));
        $toDate = Carbon::parse($request->query('toDate', Carbon::now()->format('Y-m-d')));
        $file_name = sprintf('juragan-tambah-mandiri-%s.xlsx', Str::uuid()->toString());
        $query = http_build_query([
            'from_date' => $fromDate->format('Y-m-d'),
            'to_date' => $toDate->format('Y-m-d')
        ]);
        return response()->streamDownload(function () use ($query) {
            echo file_get_contents(config('app.export_host') . '/juragan/kirim_mandiri?' . $query);
        }, $file_name);
    }
}
