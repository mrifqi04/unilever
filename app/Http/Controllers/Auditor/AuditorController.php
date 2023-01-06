<?php

namespace App\Http\Controllers\Auditor;

use App\export\AuditorExport;
use App\Http\Controllers\GenericController;
use App\Models\Auditor\Auditor;
use App\Models\Auditor\Login;
use App\Models\City;
use App\Models\District;
use App\Models\Province;
use App\Models\Scheduler\AuditorJob;
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

class AuditorController extends GenericController
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
        $query = Auditor::where('is_deleted', 1);
        $search = $request->query('search', '');
        $can_show = Auth::user()->getPermissionByName('auditor.show');
        $can_edit = Auth::user()->getPermissionByName('auditor.edit');
        $can_destroy = Auth::user()->getPermissionByName('auditor.destroy');

        $auditor_file_csv = AuditorJob::with("status")->orderBy('created_at', 'desc')->get();
        foreach ($auditor_file_csv as $pjp_file) {
            $data_messages[] = json_decode($pjp_file->error_description);
        }  

        if ($search !== '') {
            $query->where(function ($query) use ($search) {
                $query->orWhereRaw("id ilike ?", ["%" . $search . "%"])
                    ->orWhereRaw("id_unilever ilike ?", ["%" . $search . "%"])
                    ->orWhereRaw("name ilike ?", ["%" . $search . "%"])
                    ->orWhereRaw("email ilike ?", ["%" . $search . "%"])
                    ->orWhereRaw("phone ilike ?", ["%" . $search . "%"]);
            });
        }
        $datas = $query->paginate(30);

        return view('Auditor.auditor.index')
            ->with('can_show', $can_show)
            ->with('can_edit', $can_edit)
            ->with('can_destroy', $can_destroy)
            ->with('datas', $datas)
            ->with('search', $search)
            ->with('auditor_file_csv', $auditor_file_csv)
            ->with('data_messages', @$data_messages);
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
        return view('Auditor.auditor.create')
            ->with('user_types', $user_types)
            ->with('provinces', $provinces)
            ->with('cities', $cities)
            ->with('districts', $districts)
            ->with('villages', $villages)
            ->with('actives', $actives);
    }

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

            $check_province = Province::find($id_province);
            $check_city = City::find($id_city);
            $check_district = District::find($id_district);
            $check_village = Village::find($id_village);

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
        if (!$request->hasFile('import_file_auditor')) {
            $this->flashMessage('error', 'Upload File Auditor', 'No such a file');
            return redirect('/auditor');
        }

        $file = $request->file("import_file_auditor");
        if (strtolower($file->getClientMimeType()) != "text/csv" && $file->getClientOriginalExtension() != "csv") {
            $this->flashMessage('error', 'Upload File Auditor', 'not csv file');
            return redirect('/auditor');
        }

        //Move Uploaded File
        $destinationPath = config("app.job_base_dir") . "/auditor_management_imports";
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
                $this->createAuditorJob($destinationPath, $fileName, $originalFileName, $check_error_messages, 3);
                $this->flashMessage('error', 'Upload File Auditor', 'Upload Error');
                return redirect('/auditor');
            } else {
                foreach ($importData_arr as $importData) {
                    $phone = $this->normalizePhone(explode(";", $importData[0])[2]);
                    $password = explode(";", $importData[0])[3];
                    $id_village = explode(";", $importData[0])[8];
                    $start_date = Carbon::parse(explode(";", $importData[0])[13])->format("Y-m-d");
                    $end_date = Carbon::parse(explode(";", $importData[0])[14])->format("Y-m-d");

                    DB::beginTransaction();
                    $hunter = new Auditor();
                    $hunter->id = $id_village . strval(Carbon::now()->timestamp);
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
                    $hunter->status_active = explode(";", $importData[0])[15];
                    $hunter->is_deleted = 1;
                    $hunter->created_at = Carbon::now()->timestamp;
                    $hunter->created_by = \auth()->user()->id;
                    $hunter->created_by_name = \auth()->user()->id;

                    $login = new Login();
                    $login->id = $hunter->id;
                    $login->id_auditor = $hunter->id;
                    $login->username = $hunter->phone;
                    $login->password = bcrypt($password);
                    $login->created_at = Carbon::now();
                    $login->created_by = \auth()->user()->id;
                    $login->is_active = true;

                    $hunter->save();
                    $login->save();

                    DB::commit();
                }
                $this->flashMessage('success', 'Upload File Auditor', 'Upload Success');
                $this->createAuditorJob($destinationPath, $fileName, $originalFileName, $check_error_messages, 2);
                return redirect('/auditor');
            }
        } catch (\Exception $e) {
            Log::error($e);
            DB::rollBack();
            dd($e);
            $this->flashMessage('error', 'Upload File Auditor', 'Upload Error');
            return redirect('/auditor');
        }
    }

    public function createAuditorJob($destinationPath, $fileName, $originalFileName, $error_message, $status_id)
    {
        $data = new AuditorJob();
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
        // dd($content);
        $data->error_description = $content;
        $data->created_at = Carbon::now();
        $data->created_by = auth()->user()->id;
        $data->save();
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
        $id_province = $request->input('id_province');
        $id_city = $request->input('id_city');
        $id_district = $request->input('id_district');
        $id_village = $request->input('id_village');
        $id_user_types = $request->input('id_user_types');
        $start_date = Carbon::parse($request->input('start_date'))->format("Y-m-d");
        $end_date = Carbon::parse($request->input('end_date'))->format("Y-m-d");
        $phone = $this->normalizePhone($request->input('phone'));
        $address = $request->input('address');
        $longitude = $request->input('longitude');
        $latitude = $request->input('latitude');
        $status_active = $request->input('status_active');
        $password = $request->input('password');
        $confirm_password = $request->input('confirm_password');

        $validator = Validator::make(
            [
                'name' => $name,
                'email' => $email,
                'id_province' => $id_province,
                'id_city' => $id_city,
                'id_district' => $id_district,
                'id_village' => $id_village,
                'id_user_types' => $id_user_types,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'phone' => $phone,
                'address' => $address,
                'longitude' => $longitude,
                'latitude' => $latitude,
                'status_active' => $status_active,
                'password' => $password,
                'confirm_password' => $confirm_password,
            ],
            [
                'name' => 'required',
                'email' => 'required|email',
                'id_province' => 'required|exists:App\Models\Province,id,is_deleted,1',
                'id_city' => 'required|exists:App\Models\City,id,is_deleted,1',
                'id_district' => 'required|exists:App\Models\District,id,is_deleted,1',
                'id_village' => 'required|exists:App\Models\Village,id,is_deleted,1',
                'id_user_types' => 'required|exists:App\Models\UserType,id,is_deleted,1',
                'start_date' => 'required|date_format:Y-m-d',
                'end_date' => 'required|date_format:Y-m-d',
                'phone' => [
                    'required',
                    'min:9',
                    'unique:' . Auditor::class . ',phone',
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
            Log::info($validator->errors());
            return redirect()
                ->route('auditor.create')
                ->withInput($request->all())
                ->with('message', $validator->errors());
        }
        DB::beginTransaction();

        $hunter = new Auditor();
        $hunter->id = $id_village . strval(Carbon::now()->timestamp);
        $hunter->id_unilever = sprintf('AU-%s', $phone);
        $hunter->name = $name;
        $hunter->email = $email;
        $hunter->id_province = $id_province;
        $hunter->id_city = $id_city;
        $hunter->id_district = $id_district;
        $hunter->id_village = $id_village;
        $hunter->id_user_types = $id_user_types;
        $hunter->start_date = $start_date;
        $hunter->end_date = $end_date;
        $hunter->phone = $phone;
        $hunter->address = $address;
        $hunter->longitude = $longitude;
        $hunter->latitude = $latitude;
        $hunter->status_active = $status_active;
        $hunter->is_deleted = 1;
        $hunter->created_at = Carbon::now()->timestamp;
        $hunter->created_by = \auth()->user()->id;
        $hunter->created_by_name = \auth()->user()->id;

        $login = new Login();
        $login->id = $hunter->id;
        $login->id_auditor = $hunter->id;
        $login->username = $hunter->phone;
        $login->password = bcrypt($request->input('password'));
        $login->created_at = Carbon::now();
        $login->created_by = \auth()->user()->id;
        $login->is_active = true;
        try {
            $hunter->save();
            $hunter->login()->save($login);
            DB::commit();
            $this->flashMessage('success', 'Create Auditor', "Create Auditor Success");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            $this->flashMessage('error', 'Create Auditor', $e->getMessage());
            return redirect()
                ->route('auditor.create')
                ->withInput($request->all())
                ->with('message', sprintf('error creating Auditor %s', $e->getMessage()));
        }
        return redirect()->route('auditor.index');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $hunter = Auditor::where('id', $id)
            ->where('is_deleted', 1)
            ->first();
        if (is_null($hunter)) {
            return redirect()
                ->route('auditor.index')
                ->with('message', sprintf('auditor with id %s not found', $id));
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
        return view('Auditor.auditor.show')
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
        $hunter = Auditor::where('id', $id)
            ->where('is_deleted', 1)
            ->first();
        if (is_null($hunter)) {
            return redirect()
                ->route('auditor.index')
                ->with('message', sprintf('auditor with id %s not found', $id));
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
        return view('Auditor.auditor.edit')
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
        $id_unilever = sprintf('AU-%s', $request->input('phone'));
        $name = $request->input('name');
        $email = $request->input('email');
        $id_province = $request->input('id_province');
        $id_city = $request->input('id_city');
        $id_district = $request->input('id_district');
        $id_village = $request->input('id_village');
        $id_user_types = $request->input('id_user_types');
        $start_date = Carbon::parse($request->input('start_date'))->format("Y-m-d");
        $end_date = Carbon::parse($request->input('end_date'))->format("Y-m-d");
        $phone = $this->normalizePhone($request->input('phone'));
        $address = $request->input('address');
        $longitude = $request->input('longitude');
        $latitude = $request->input('latitude');
        $status_active = $request->input('status_active');
        $validator = Validator::make(
            [
                'id' => $id,
                'id_unilever' => $id_unilever,
                'name' => $name,
                'email' => $email,
                'id_province' => $id_province,
                'id_city' => $id_city,
                'id_district' => $id_district,
                'id_village' => $id_village,
                'id_user_types' => $id_user_types,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'phone' => $phone,
                'address' => $address,
                'longitude' => $longitude,
                'latitude' => $latitude,
                'status_active' => $status_active,
            ],
            [
                'id' => 'required|exists:App\Models\Auditor\Auditor,id,is_deleted,1',
                'id_unilever' => [
                    'required',
                    Rule::unique('pgsql.auditor.auditors', 'id_unilever')->ignore($id),
                ],
                'name' => 'required',
                'email' => 'required|email',
                'id_province' => 'required|exists:App\Models\Province,id,is_deleted,1',
                'id_city' => 'required|exists:App\Models\City,id,is_deleted,1',
                'id_district' => 'required|exists:App\Models\District,id,is_deleted,1',
                'id_village' => 'required|exists:App\Models\Village,id,is_deleted,1',
                'id_user_types' => 'required|exists:App\Models\UserType,id,is_deleted,1',
                'start_date' => 'required|date_format:Y-m-d',
                'end_date' => 'required|date_format:Y-m-d',
                'phone' => [
                    'required',
                    'min:9',
//                    'unique:' . Auditor::class . ',phone',
                    Rule::unique('pgsql.auditor.auditors', 'phone')->ignore($id),
                ],
                'address' => 'required',
                'longitude' => 'required',
                'latitude' => 'required',
                'status_active' => 'required|numeric|in:1,2',
            ]
        );
        if ($validator->fails()) {
            Log::info($validator->errors());
            Log::info($request->all());
            $this->flashMessage('error', 'Update Auditor', $validator->errors()->first());
            return redirect()
                ->route('auditor.edit', ['auditor' => $id])
                ->withInput($request->all());
        }
        $hunter = Auditor::where('id', $id)
            ->where('is_deleted', 1)
            ->first();
        $hunter->id_unilever = sprintf('AU-%s', $phone);
        $hunter->name = $name;
        $hunter->email = $email;
        $hunter->id_province = $id_province;
        $hunter->id_city = $id_city;
        $hunter->id_district = $id_district;
        $hunter->id_village = $id_village;
        $hunter->id_user_types = $id_user_types;
        $hunter->start_date = $start_date;
        $hunter->end_date = $end_date;
        $hunter->phone = $phone;
        $hunter->address = $address;
        $hunter->longitude = $longitude;
        $hunter->latitude = $latitude;
        $hunter->status_active = $status_active;
        $hunter->updated_at = Carbon::now()->timestamp;
        $hunter->updated_by = \auth()->user()->id;
        DB::beginTransaction();
        try {
            $hunter->save();
            $hunter->login->username = $hunter->phone;
            $hunter->login->updated_at = Carbon::now();
            $hunter->login->updated_by = \auth()->user()->id;
            $hunter->login->save();
            DB::commit();
            $this->flashMessage('success', 'Update Auditor', "Update Auditor Success");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            $this->flashMessage('error', 'Update Auditor', $e->getMessage());
            return redirect()
                ->route('auditor.edit', ['auditor' => $id])
                ->withInput($request->all());
        }
        return redirect()->route('auditor.index');
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
                'id' => 'required|exists:App\Models\Auditor\Auditor,id,is_deleted,1',
            ]
        );
        if ($validator->fails()) {
            return redirect()
                ->route('auditor.index')
                ->with('message', $validator->errors());
        }
        $hunter = Auditor::where('id', $id)
            ->where('is_deleted', 1)
            ->first();
        $hunter->is_deleted = 2;
        try {
            $hunter->save();
        } catch (\Exception $e) {
            Log::error($e);
            return redirect()
                ->route('auditor.index')
                ->with('message', sprintf('error deleting auditor %s', $e->getMessage()));
        }
        return redirect()->route('auditor.index');
    }

    public function export()
    {
        $request = Request::capture();
        $search = $request->query('search', '');
        $file_name = sprintf('auditor-%s.xlsx', Str::uuid()->toString());
        return Excel::download(new AuditorExport($search), $file_name);
    }
}
