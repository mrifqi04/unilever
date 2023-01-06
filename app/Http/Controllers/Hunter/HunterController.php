<?php

namespace App\Http\Controllers\Hunter;

use App\export\HunterExport;
use App\export\HunterSurveyExport;
use App\Http\Controllers\GenericController;
use App\Models\City;
use App\Models\District;
use App\Models\Hunter\Hunter;
use App\Models\Hunter\Login;
use App\Models\Province;
use App\Models\Scheduler\HunterJob;
use App\Models\UserType;
use App\Models\Village;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Ramsey\Uuid\Uuid;

class HunterController extends GenericController
{
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
    public function index()
    {
        $request = Request::capture();
        $query = Hunter::where('is_deleted', 1);
        $canShow = Auth::user()->getPermissionByName('hunter.show');
        $canEdit = Auth::user()->getPermissionByName('hunter.edit');
        $canDestroy = Auth::user()->getPermissionByName('hunter.destroy');
        $search = trim($request->query('search', ''));
        if ($search !== '') {
            $query->where(function ($query) use ($search) {
                $query
                    ->orWhereRaw("id ilike ?", ["%" . $search . "%"])
                    ->orWhereRaw("id_unilever ilike ?", ["%" . $search . "%"])
                    ->orWhereRaw("name ilike ?", ["%" . $search . "%"])
                    ->orWhereRaw("email ilike ?", ["%" . $search . "%"])
                    ->orWhereRaw("phone ilike ?", ["%" . $search . "%"]);
            });
        }
        $hunters = $query->paginate(30);
        $hunters->appends(['search' => $request->query("search")]);
        $inSearch = count($request->query()) > 0;
        return view('hunter.hunter.index')
            ->with('in_search', $inSearch)
            ->with('can_show', $canShow)
            ->with('can_edit', $canEdit)
            ->with('can_destroy', $canDestroy)
            ->with('hunters', $hunters);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $provinces = Province::select(['id', 'name'])
            ->where('id_country', '01')
            ->where('is_deleted', 1)
            ->get()->pluck('name', 'id');
        $user_types = UserType::select(['id', 'name'])
            ->where('is_deleted', 1)
            ->get()->pluck('name', 'id');
        $cities = [];
        $districts = [];
        $villages = [];
        $actives = [
            1 => 'Yes',
            2 => 'No',
        ];
        return view('hunter.hunter.create')
            ->with('user_types', $user_types)
            ->with('provinces', $provinces)
            ->with('cities', $cities)
            ->with('districts', $districts)
            ->with('villages', $villages)
            ->with('actives', $actives);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $name = $request->input('name');
        $email = $request->input('email');
        $idProvince = $request->input('id_province');
        $idCity = $request->input('id_city');
        $idDistrict = $request->input('id_district');
        $idVillage = $request->input('id_village');
        $idUserTypes = $request->input('id_user_types');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $phone = $this->normalizePhone($request->input('phone'));
        $address = $request->input('address');
        $longitude = $request->input('longitude');
        $latitude = $request->input('latitude');
        $statusActive = $request->input('status_active');
        $password = $request->input('password');
        $confirmPassword = $request->input('confirm_password');

        $validator = Validator::make(
            [
                'name' => $name,
                'email' => $email,
                'id_province' => $idProvince,
                'id_city' => $idCity,
                'id_district' => $idDistrict,
                'id_village' => $idVillage,
                'id_user_types' => $idUserTypes,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'phone' => $phone,
                'address' => $address,
                'longitude' => $longitude,
                'latitude' => $latitude,
                'status_active' => $statusActive,
                'password' => $password,
                'confirm_password' => $confirmPassword,
            ],
            [
                'name' => 'required',
                'email' => 'required|email',
                'id_province' => 'required|exists:' . Province::class . ',id,is_deleted,1',
                'id_city' => 'required|exists:' . City::class . ',id,is_deleted,1',
                'id_district' => 'required|exists:' . District::class . ',id,is_deleted,1',
                'id_village' => 'required|exists:' . Village::class . ',id,is_deleted,1',
                'id_user_types' => 'required|exists:' . UserType::class . ',id,is_deleted,1',
                'start_date' => 'required|date_format:Y-m-d',
                'end_date' => 'required|date_format:Y-m-d',
                'phone' => [
                    'required',
                    'min:9',
                    'unique:' . Hunter::class . ',phone',
                ],
                'address' => 'required',
                'longitude' => 'required',
                'latitude' => 'required',
                'status_active' => 'required|numeric|in:0,1',
                'password' => 'required|min:6|max:255',
                'confirm_password' => 'required|same:password|min:6|max:255',
            ]
        );
        if ($validator->fails()) {
            $this->flashMessage('error', 'Create Hunter', $validator->errors()->first());
            return redirect()
                ->route('hunter.create')
                ->withInput($request->all());
        }
        DB::beginTransaction();

        $hunter = new Hunter();
        $hunter->id = $idVillage . strval(Carbon::now()->timestamp);
        $hunter->id_unilever = sprintf('HU-%s', $phone);
        $hunter->name = $name;
        $hunter->email = $email;
        $hunter->id_province = $idProvince;
        $hunter->id_city = $idCity;
        $hunter->id_district = $idDistrict;
        $hunter->id_village = $idVillage;
        $hunter->id_user_types = $idUserTypes;
        $hunter->start_date = $startDate;
        $hunter->end_date = $endDate;
        $hunter->phone = $phone;
        $hunter->address = $address;
        $hunter->longitude = $longitude;
        $hunter->latitude = $latitude;
        $hunter->status_active = $statusActive;
        $hunter->is_deleted = 1;
        $hunter->created_at = Carbon::now()->timestamp;
        $hunter->created_by = 1;
        $hunter->created_by_name = \auth()->user()->id;

        $login = new Login();
        $login->id = $hunter->id;
        $login->hunter_id = $hunter->id;
        $login->username = $hunter->phone;
        $login->password = bcrypt($request->input('password'));
        $login->created_at = Carbon::now();
        $login->created_by = \auth()->user()->id;
        $login->is_deleted = 1;
        try {
            $hunter->save();
            $hunter->login()->save($login);
            DB::commit();
            $this->flashMessage('success', 'Create hunter', "Create Hunter Success");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            $this->flashMessage('error', 'Create hunter', $e->getMessage());
            return redirect()
                ->route('hunter.create')
                ->withInput($request->all())
                ->with('message', sprintf('error creating hunter %s', $e->getMessage()));
        }
        $this->flashMessage('success', 'Create Hunter', 'Create Hunter Success');
        return redirect()->route('hunter.index');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $hunter = Hunter::where('id', $id)
            ->where('is_deleted', 1)
            ->first();
        if (is_null($hunter)) {
            $this->flashMessage('error', 'Show Hunter', 'Hunter Not Found');
            return redirect()
                ->route('hunter.index');
        }
        $user_types = UserType::select(['id', 'name'])
            ->where('is_deleted', 1)
            ->get()->pluck('name', 'id');
        $provinces = Province::select(['id', 'name'])
            ->where('id_country', '01')
            ->where('is_deleted', 1)
            ->get()->pluck('name', 'id');
        $cities = City::select(['id', 'name'])
            ->where('id_province', $hunter->id_province)
            ->where('is_deleted', 1)
            ->get()->pluck('name', 'id');
        $districts = District::select(['id', 'name'])
            ->where('id_city', $hunter->id_city)
            ->where('is_deleted', 1)
            ->get()->pluck('name', 'id');
        $villages = Village::select(['id', 'name'])
            ->where('id_district', $hunter->id_district)
            ->where('is_deleted', 1)
            ->get()->pluck('name', 'id');
        //        dd($hunter);
        return view('hunter.hunter.show')
            ->with('user_types', $user_types)
            ->with('provinces', $provinces)
            ->with('cities', $cities)
            ->with('districts', $districts)
            ->with('villages', $villages)
            ->with('hunter', $hunter);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $hunter = Hunter::where('id', $id)
            ->where('is_deleted', 1)
            ->first();
        if (is_null($hunter)) {
            $this->flashMessage('error', 'Edit Hunter', 'Hunter Not Found');
            return redirect()
                ->route('hunter.index');
        }
        $user_types = UserType::select(['id', 'name'])
            ->where('is_deleted', 1)
            ->get()->pluck('name', 'id');
        $provinces = Province::select(['id', 'name'])
            ->where('id_country', '01')
            ->where('is_deleted', 1)
            ->get()->pluck('name', 'id');
        $cities = City::select(['id', 'name'])
            ->where('id_province', $hunter->id_province)
            ->where('is_deleted', 1)
            ->get()->pluck('name', 'id');
        $districts = District::select(['id', 'name'])
            ->where('id_city', $hunter->id_city)
            ->where('is_deleted', 1)
            ->get()->pluck('name', 'id');
        $villages = Village::select(['id', 'name'])
            ->where('id_district', $hunter->id_district)
            ->where('is_deleted', 1)
            ->get()->pluck('name', 'id');
        $actives = [
            1 => 'Yes',
            2 => 'No',
        ];
        return view('hunter.hunter.edit')
            ->with('user_types', $user_types)
            ->with('provinces', $provinces)
            ->with('cities', $cities)
            ->with('districts', $districts)
            ->with('villages', $villages)
            ->with('actives', $actives)
            ->with('hunter', $hunter);
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
        $id_unilever = sprintf('HU-%s', $request->input('phone'));
        $name = $request->input('name');
        $email = $request->input('email');
        $idProvince = $request->input('id_province');
        $idCity = $request->input('id_city');
        $idDistrict = $request->input('id_district');
        $idVillage = $request->input('id_village');
        $idUserTypes = $request->input('id_user_types');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $phone = $this->normalizePhone($request->input('phone'));
        $address = $request->input('address');
        $longitude = $request->input('longitude');
        $latitude = $request->input('latitude');
        $statusActive = $request->input('status_active');
        $validator = Validator::make(
            [
                'id' => $id,
                'id_unilever' => $id_unilever,
                'name' => $name,
                'email' => $email,
                'id_province' => $idProvince,
                'id_city' => $idCity,
                'id_district' => $idDistrict,
                'id_village' => $idVillage,
                'id_user_types' => $idUserTypes,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'phone' => $phone,
                'address' => $address,
                'longitude' => $longitude,
                'latitude' => $latitude,
                'status_active' => $statusActive,
            ],
            [
                'id' => 'required|exists:' . Hunter::class . ',id,is_deleted,1',
                'id_unilever' => [
                    'required',
                    Rule::unique('pgsql.hunter.hunter', 'id_unilever')->ignore($id),
                ],
                'name' => 'required',
                'email' => 'required|email',
                'id_province' => 'required|exists:' . Province::class . ',id,is_deleted,1',
                'id_city' => 'required|exists:' . City::class . ',id,is_deleted,1',
                'id_district' => 'required|exists:' . District::class . ',id,is_deleted,1',
                'id_village' => 'required|exists:' . Village::class . ',id,is_deleted,1',
                'id_user_types' => 'required|exists:' . UserType::class . ',id,is_deleted,1',
                'start_date' => 'required|date_format:Y-m-d',
                'end_date' => 'required|date_format:Y-m-d',
                'phone' => [
                    'required',
                    'min:9',
                    Rule::unique('pgsql.hunter.hunter', 'phone')->ignore($id),
                ],
                'address' => 'required',
                'longitude' => 'required',
                'latitude' => 'required',
                'status_active' => 'required|numeric|in:1,2',
            ]
        );
        if ($validator->fails()) {
            $this->flashMessage('error', 'Edit Hunter', $validator->errors()->first());
            return redirect()
                ->route('hunter.edit', ['hunter' => $id])
                ->withInput($request->all())
                ->with('message', $validator->errors());
        }
        $hunter = Hunter::where('id', $id)
            ->where('is_deleted', 1)
            ->first();
        $hunter->id_unilever = sprintf('HU-%s', $phone);
        $hunter->name = $name;
        $hunter->email = $email;
        $hunter->id_province = $idProvince;
        $hunter->id_city = $idCity;
        $hunter->id_district = $idDistrict;
        $hunter->id_village = $idVillage;
        $hunter->id_user_types = $idUserTypes;
        $hunter->start_date = $startDate;
        $hunter->end_date = $endDate;
        $hunter->phone = $phone;
        $hunter->address = $address;
        $hunter->longitude = $longitude;
        $hunter->latitude = $latitude;
        $hunter->status_active = $statusActive;
        $hunter->updated_at = Carbon::now()->timestamp;
        try {
            $hunter->save();
        } catch (\Exception $e) {
            Log::error($e);
            $this->flashMessage('error', 'Edit Hunter', $e->getMessage());
            return redirect()
                ->route('hunter.edit', ['hunter' => $id])
                ->withInput($request->all());
        }
        $this->flashMessage('success', 'Edit Hunter', 'Edit Hunter Plan Success');
        return redirect()->route('hunter.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $validator = Validator::make(
            [
                'id' => $id,
            ],
            [
                'id' => 'required|exists:' . Hunter::class . ',id,is_deleted,1',
            ]
        );
        if ($validator->fails()) {
            $this->flashMessage('error', 'Delete Hunter', $validator->errors()->first());
            return redirect()
                ->route('hunter.index');
        }
        $hunter = Hunter::where('id', $id)
            ->where('is_deleted', 1)
            ->first();
        $hunter->is_deleted = 2;
        try {
            $hunter->save();
        } catch (\Exception $e) {
            Log::error($e);
            $this->flashMessage('error', 'Delete Hunter', $e->getMessage());
            return redirect()
                ->route('hunter.index');
        }
        $this->flashMessage('success', 'Delete Hunter', 'Delete Hunter Success');
        return redirect()->route('hunter.index');
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function import()
    {
        $data = HunterJob::with("status")->orderBy("created_at", "desc")->paginate(20);
        foreach ($data as $hunter) {
            $data_messages[] = json_decode($hunter->error_description);
        }

        return view("hunter.hunter.import")
            ->with('data', $data)
            ->with('data_messages', @$data_messages);
    }

    // public function doImport(Request $request)
    // {
    //     if (!$request->hasFile('import_file')) {
    //         $this->flashMessage('error', 'Upload File Hunter', 'No such a file');
    //         return redirect()->route('hunter.import');
    //     }

    //     $file = $request->file("import_file");
    //     if (strtolower($file->getClientMimeType()) != "text/csv" && $file->getClientOriginalExtension() != "csv") {
    //         $this->flashMessage('error', 'Upload File Hunter', 'not csv file');
    //         return redirect()->route('hunter.import');
    //     }

    //     //Move Uploaded File
    //     $destinationPath = config("app.job_base_dir") . "/hunter_imports";
    //     $fileName = Uuid::uuid4()->toString() . "." . $file->getClientOriginalExtension();
    //     try {
    //         $file->move($destinationPath, $fileName);
    //         // save to job table
    //         $data = new HunterJob();
    //         $data->type = 1;
    //         $data->file_path = $destinationPath . "/";
    //         $data->file_name = $fileName;
    //         $data->file_name_origin = $file->getClientOriginalName();
    //         $data->status_id = 1;
    //         $data->created_at = Carbon::now();
    //         $data->created_by = auth()->user()->id;
    //         $data->save();
    //         $this->flashMessage('success', 'Upload File Hunter', 'Upload Success');
    //         return redirect()->route('hunter.import');
    //     } catch (\Exception $e) {
    //         Log::error($e);
    //         $this->flashMessage('error', 'Upload File Hunter', 'Upload Error');
    //         return redirect()->route('hunter.import');
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
            $password = explode(";", $importData[0])[3];
            $password_confirmation = explode(";", $importData[0])[4];
            $phone = $this->normalizePhone(explode(";", $importData[0])[2]);
            $id_province = explode(";", $importData[0])[5];
            $id_city = explode(";", $importData[0])[6];
            $id_district = explode(";", $importData[0])[7];
            $id_village = explode(";", $importData[0])[8];

            $check_phone = Hunter::where('phone', $phone)->first();
            $check_province = Province::find($id_province);
            $check_city = City::find($id_city);
            $check_district = District::find($id_district);
            $check_village = Village::find($id_village);

            if ($check_phone) {
                $message = 'Nomor telp baris ke-' . $index . ' sudah terdaftar';
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

            if ($phone <= 9) {
                $message = 'Phone baris ke-' . $index . ' tidak boleh kurang dari 9 digit';
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
            $this->flashMessage('error', 'Upload File Hunter', 'No such a file');
            return redirect()->route('hunter.import');
        }

        $file = $request->file("import_file");
        if (strtolower($file->getClientMimeType()) != "text/csv" && $file->getClientOriginalExtension() != "csv") {
            $this->flashMessage('error', 'Upload File Hunter', 'not csv file');
            return redirect()->route('hunter.import');
        }

        //Move Uploaded File
        $destinationPath = config("app.job_base_dir") . "/hunter_imports";
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

            if ($check_error_messages) {                
                $this->createHunterJob($destinationPath, $fileName, $originalFileName, $check_error_messages, 3);
                $this->flashMessage('error', 'Upload File Hunter', 'Upload Error');
                return redirect('/hunter/import');
            } else {
                foreach ($importData_arr as $index => $importData) {
                    $phone = $this->normalizePhone(explode(";", $importData[0])[2]);
                    $password = explode(";", $importData[0])[3];
                    $id_village = explode(";", $importData[0])[8];
                    $start_date = Carbon::parse(explode(";", $importData[0])[13])->format("Y-m-d");
                    $end_date = Carbon::parse(explode(";", $importData[0])[14])->format("Y-m-d");

                    DB::beginTransaction();
                    $hunter = new Hunter();
                    $hunter->id = $id_village . strval(Carbon::now()->timestamp) . $index;
                    $hunter->id_unilever = sprintf('AU-%s', $phone);
                    $hunter->name = explode(";", $importData[0])[0];
                    $hunter->email = explode(";", $importData[0])[1];
                    $hunter->id_province = explode(";", $importData[0])[5];
                    $hunter->id_city = explode(";", $importData[0])[6];
                    $hunter->id_district = explode(";", $importData[0])[7];
                    $hunter->id_village = $id_village;
                    $hunter->id_user_types = explode(";", $importData[0])[12];
                    $hunter->start_date = $start_date;
                    $hunter->end_date = $end_date;
                    $hunter->phone = $phone;
                    $hunter->address = explode(";", $importData[0])[9];
                    $hunter->longitude = explode(";", $importData[0])[11];
                    $hunter->latitude = explode(";", $importData[0])[10];
                    $hunter->status_active = (int) explode(";", $importData[0])[15];
                    $hunter->is_deleted = 1;
                    $hunter->created_at = Carbon::now()->timestamp;
                    $hunter->created_by = \auth()->user()->id;
                    $hunter->created_by_name = \auth()->user()->id;
                    $login = new Login();
                    $login->id = $hunter->id;
                    $login->hunter_id = $hunter->id;
                    $login->username = $hunter->phone;
                    $login->password = bcrypt($password);
                    $login->created_at = Carbon::now();
                    $login->created_by = \auth()->user()->id;
                    $login->is_deleted = 1;
                    // dd($hunter, $login);
                    $hunter->save();
                    $login->save();
                    // $hunter->login()->save($login);
                    DB::commit();
                }
                $this->createHunterJob($destinationPath, $fileName, $originalFileName, $check_error_messages, 2);
                $this->flashMessage('success', 'Upload File Hunter', 'Upload Success');
                return redirect('/hunter/import');
            }
        } catch (\Exception $e) {
            Log::error($e);
            DB::rollBack();
            dd($e);
            $this->flashMessage('error', 'Upload File Hunter', 'Upload Error');
            return redirect('/hunter/import');
        }
    }

    public function createHunterJob($destinationPath, $fileName, $originalFileName, $error_message, $status_id)
    {
        $data = new HunterJob();
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

        return $data->save();
    }

    public function export()
    {
        $request = Request::capture();
        $search = $request->query('search', '');
        $file_name = sprintf('hunter-%s.xlsx', Str::uuid()->toString());
        dd(file_get_contents(config('app.export_host') . '/hunter?' . http_build_query(['search' => $search])));
        return response()->streamDownload(function () use ($search) {
            echo file_get_contents(config('app.export_host') . '/hunter?' . http_build_query(['search' => $search]));
        }, $file_name);
    }

    public function exportSurvey()
    {
        $request = Request::capture();
        $fromDate = Carbon::parse($request->query('fromDate', Carbon::now()->format('Y-m-d')));
        $toDate = Carbon::parse($request->query('toDate', Carbon::now()->format('Y-m-d')));
        $file_name = sprintf('hunter-survey-%s.xlsx', Str::uuid()->toString());
        $query = http_build_query([
            'from_date' => $fromDate->format('Y-m-d'),
            'to_date' => $toDate->format('Y-m-d')
        ]);
        // dd(file_get_contents(config('app.export_host') . '/hunter/journey_plan?' . $query));
        return response()->streamDownload(function () use ($query) {
            echo file_get_contents(config('app.export_host') . '/hunter/journey_plan?' . $query);
        }, $file_name);
    }
}
