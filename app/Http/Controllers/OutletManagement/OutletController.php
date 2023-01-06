<?php

namespace App\Http\Controllers\OutletManagement;

/**
 * Description of OutletController
 *
 * @author nuansa.ramadhan
 */

use App\export\OutletExport;
use App\Models\JuraganManagement\Juragan;
use App\Models\OutletManagement\MapOutlet;
use App\Models\Province;
use App\Models\City;
use App\Models\District;
use App\Models\Scheduler\OutletJob;
use App\Models\Village;
use App\Models\Warehouse\Cabinets;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use function foo\func;
use Illuminate\Http\Request;
use App\Http\Controllers\GenericController;
use App\Models\OutletManagement\Outlet;
use App\Models\OutletManagement\OutletStatusType;
use App\Models\OutletManagement\OwnershipStatus;
use App\Models\OutletManagement\OutletHasCabinet;
use App\Models\OutletManagement\StreetType;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class OutletController extends GenericController
{

    //put your code here
    public function __construct()
    {
        parent::__construct();
    }

    private $pagination = 30;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $users = new Outlet();
        $users = $users->whereHas("mapOutlet", function ($query) {
            $query->where("is_mitra", "=", 1);
        });
        if ($request->query("search") != "") {
            $users = $users->whereRaw("id_juragan ilike ?", ["%" . $request->query("search") . "%"])
                ->orWhereRaw("id ilike ?", ["%" . $request->query("search") . "%"])
                ->orWhereRaw("id_unilever ilike ?", ["%" . $request->query("search") . "%"])
                ->orWhereRaw("owner ilike ?", ["%" . $request->query("search") . "%"])
                ->orWhereRaw("phone ilike ?", ["%" . $request->query("search") . "%"]);
            $users = $users->orWhereHas("juragan", function ($query) use ($request) {
                $query->whereRaw("name ilike ?", ["%" . $request->query("search") . "%"]);
            });
            $data = $users->paginate($this->pagination);
            $data->appends(['search' => $request->query("search")]);

        } else {
            if ($request->query("id") != "") {
                $users = $users->where("id", $request->query("id"));
            }
            $data = $users->paginate($this->pagination);
        }
        // if($request->query("phone") != ""){
        // $users = $users->where("phone", $request->query("phone"));
        // }
        // if($request->query("unilever_id") != ""){
        // $users = $users->where("id_unilever", $request->query("unilever_id"));
        // }
//        $data = $users->paginate(30);
        $data = [
            'datas' => $data
        ];

        return view('OutletManagement/index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $juragans = Juragan::all()->pluck('name', 'id');
        $provinces = Province::all()->pluck('name', 'id');
        $statusTypes = OutletStatusType::all()->pluck('name', 'id');
        $ownershipStatus = OwnershipStatus::all()->pluck('name', 'id');
        $streetTypes = StreetType::all()->pluck('name', 'id');
        $cabinets = Cabinets::doesntHave("outlets")->get()->pluck('serialnumber', 'id');
        $datas = [
            'juragans' => $juragans,
            'statusTypes' => $statusTypes,
            'ownershipStatus' => $ownershipStatus,
            'streetTypes' => $streetTypes,
            'provinces' => $provinces,
            'cabinets' => $cabinets,
            'route' => route('outlet.store')
        ];
        return view('OutletManagement/create', $datas);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $location = new \stdClass();
        $rules = array(
            'unilever_id' => 'required|min:3|max:255',
            'juragan_id' => 'required|exists:App\Models\JuraganManagement\Juragan,id',
            'name' => 'required|min:3|max:255',
            'owner' => 'required|min:3|max:255',
            'phone' => 'required|unique:App\Models\OutletManagement\Outlet,phone',
            'phone2' => 'required',
            'address' => 'required|min:6|max:255',
            'latitude' => 'required',
            'longitude' => 'required',
            'provinces' => 'required|exists:provinces,id',
            'cities' => 'required|exists:cities,id',
            'districts' => 'required|exists:districts,id',
            'villages' => 'required|exists:villages,id',
            'cabinets' => 'required|exists:App\Models\Warehouse\Cabinets,id',
            'outlet_type_id' => 'required|exists:App\Models\OutletManagement\OutletStatusType,id',
            'ownership_status_id' => 'required|exists:App\Models\OutletManagement\OwnershipStatus,id',
            'street_type_id' => 'required|exists:App\Models\OutletManagement\StreetType,id',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Redirect::to(route('outlet.create'))
                ->withErrors($validator)->withInput($request->all());
        } else {
            DB::beginTransaction();
            try {
                $data = new Outlet();

                // set and save data
                // $data->id = sprintf("%s%s", $request->input('villages'), (int)Carbon::now()->getPreciseTimestamp(9));
                $data->id_unilever = $request->input("unilever_id");
                $data->id_juragan = $request->input("juragan_id");
                $data->name = $request->input('name');
                $data->owner = $request->input('owner');
                $data->phone = $request->input('phone');
                $data->phone2 = $request->input('phone2');
                $data->address = $request->input('address');
                $data->latitude = $request->input('latitude');
                $data->longitude = $request->input('longitude');
                $location->latitude = (float)$data->latitude;
                $location->longitude = (float)$data->longitude;
                $data->location = json_encode($location);

                $data->id_outlet_type = $request->input("outlet_type_id");
                $data->id_ownership_status = $request->input("ownership_status_id");
                $data->id_street_type = $request->input("street_type_id");
                $data->id_outlet_type = 1;
                $data->id_ownership_status = 1;
                $data->id_street_type = 1;

                $data->id_country = config('app.country_id');
                $data->id_province = $request->input('provinces');
                $data->id_city = $request->input('cities');
                $data->id_district = $request->input('districts');
                $data->id_village = $request->input('villages');

                $data->descriptions = $request->input('descriptions');

                $data->created_at = Carbon::now()->unix();
                $data->created_by = \auth()->user()->id;
                $data->is_deleted = 1;
                $data->status_active = 1;
                $data->save();

                $mapOutlet = new MapOutlet();
                $mapOutlet->id = Uuid::uuid4()->toString();
                // $mapOutlet->id_outlet;
                $mapOutlet->id_outlet = $data->id;
                $mapOutlet->is_mitra = 1;
                $mapOutlet->is_deleted = 1;
                $data->mapOutlet()->save($mapOutlet);

                $data->Cabinets()->sync([$request->input('cabinets')]);

                DB::commit();
                $this->flashMessage('success', 'CREATE', 'create data success');
                return Redirect::to(route('outlet.index'));
            } catch (\Exception $e) {
                DB::rollback();
                Log::error($e);
                $this->flashMessage('error', 'CREATE', $e->getMessage());
                return Redirect::to(route('outlet.create'))->withInput($request->all());
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
        $data = Outlet::with([
            "province", "city", "district", "village", "juragan", "cabinets", "StatusType", "OwnershipStatus",
            "StreetType"
        ])->find($id);
        if ($data == null) {
            $this->flashMessage('error', 'SHOW', 'Data not found');
            return Redirect::to(route('outlet.index'));
        }
        $datas = [
            'data' => $data
        ];
//        dd($data);
        return view('OutletManagement/show', $datas);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $cities = [];
        $districts = [];
        $villages = [];
        $data = Outlet::with(['province', 'city', 'district', 'village', 'juragan'])->find($id);
        if ($data == null) {
            $this->flashMessage('error', 'EDIT', 'Data not found');
            return redirect(route('outlet.index'));
        }
        $statusTypes = OutletStatusType::all()->pluck('name', 'id');
        $ownershipStatus = OwnershipStatus::all()->pluck('name', 'id');
        $streetTypes = StreetType::all()->pluck('name', 'id');
        $juragans = Juragan::all()->pluck('name', 'id');
        $provinces = Province::all()->pluck('name', 'id');

        if ($data->city != null) {
            $cities = City::where("id_province", $data->province->id)->pluck('name', 'id');
        }
        if ($data->district != null) {
            $districts = District::where("id_city", $data->city->id)->pluck('name', 'id');
        }
        if ($data->village != null) {
            $villages = Village::where("id_district", $data->district->id)->pluck('name', 'id');
        }

        $datas = [
            'data' => $data,
            'juragans' => $juragans,
            'statusTypes' => $statusTypes,
            'ownershipStatus' => $ownershipStatus,
            'streetType' => $streetTypes,
            'provinces' => $provinces,
            'cities' => $cities,
            'districts' => $districts,
            'villages' => $villages,
        ];

        return view('OutletManagement/edit', $datas);
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
        $location = new \stdClass();
        $rules = array(
            'unilever_id' => 'required|min:3|max:255',
            'juragan_id' => 'required|exists:App\Models\JuraganManagement\Juragan,id',
            'name' => 'required|min:3|max:255',
            'owner' => 'required|min:3|max:255',
            'phone' => 'required|unique:App\Models\OutletManagement\Outlet,phone,' . $id,
            'phone2' => 'required',
            'address' => 'required|min:6|max:255',
            'latitude' => 'required',
            'longitude' => 'required',
            'provinces' => 'required|exists:provinces,id',
            'cities' => 'required|exists:cities,id',
            'districts' => 'required|exists:districts,id',
            'villages' => 'required|exists:villages,id',
            'outlet_type_id' => 'required|exists:App\Models\OutletManagement\OutletStatusType,id',
            'ownership_status_id' => 'required|exists:App\Models\OutletManagement\OwnershipStatus,id',
            'street_type_id' => 'required|exists:App\Models\OutletManagement\StreetType,id',
        );

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Redirect::to(route('outlet.edit', ['outlet' => $id]))
                ->withErrors($validator)->withInput($request->all());
        } else {
            DB::beginTransaction();
            try {
                $data = Outlet::find($id);
                // set and save data juragan
                $data->id_unilever = $request->input("unilever_id");
                $data->id_juragan = $request->input("juragan_id");
                $data->name = $request->input('name');
                $data->owner = $request->input('owner');
                $data->phone = $request->input('phone');
                $data->phone2 = $request->input('phone2');
                $data->address = $request->input('address');
                $data->latitude = $request->input('latitude');
                $data->longitude = $request->input('longitude');
                $location->latitude = (float)$data->latitude;
                $location->longitude = (float)$data->longitude;
                $data->location = json_encode($location);

                $data->id_outlet_type = $request->input("outlet_type_id");
                $data->id_ownership_status = $request->input("ownership_status_id");
                $data->id_street_type = $request->input("street_type_id");

                $data->id_country = config('app.country_id');
                $data->id_province = $request->input('provinces');
                $data->id_city = $request->input('cities');
                $data->id_district = $request->input('districts');
                $data->id_village = $request->input('villages');

                $data->descriptions = $request->input('descriptions');

                $data->updated_at = Carbon::now()->unix();
                $data->updated_by = \auth()->user()->id;
                $data->save();
                DB::commit();
                $this->flashMessage('success', 'EDIT', 'Edit data success');
                return Redirect::to(route('outlet.index'));
            } catch (\Exception $ex) {
                DB::rollback();
                Log::error($ex);
                $this->flashMessage('error', 'EDIT', $ex->getMessage());
                return Redirect::to(route('outlet.edit', ['outlet' => $id]));
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

    public function importForm(Request $request)
    {
        $users = new OutletJob();
        $data = $users->with("status")->orderBy("created_at", "desc")->paginate(20);
        foreach ($data as $outlet) {
            $data_messages[] = json_decode($outlet->error_description);
        }
        
        // $datas = [
        //     'datas' => $data,
        //     'data_messages', @$data_messages
        // ];
        return view("OutletManagement/import")
        ->with('datas', $data)
        ->with('data_messages', @$data_messages); 
    }

    // public function doImport(Request $request)
    // {
    //     if (!$request->hasFile('outlet_import_file')) {
    //         $this->flashMessage('error', 'Upload File Outlet', 'No such a file');
    //         return Redirect::to(route('outlet.import_form'));
    //     }

    //     $file = $request->file("outlet_import_file");
    //     if (strtolower($file->getClientOriginalExtension()) != "csv") {
    //         $this->flashMessage('error', 'Upload File Outlet', 'not csv file');
    //         return Redirect::to(route('outlet.import_form'));
    //     }

    //     //Move Uploaded File
    //     $destinationPath = config("app.job_base_dir") . "/outlet_imports";
    //     $fileName = Uuid::uuid4()->toString() . "." . $file->getClientOriginalExtension();
    //     try {
    //         $file->move($destinationPath, $fileName);
    //         // save to job table
    //         $data = new OutletJob();
    //         $data->type = 1;
    //         $data->file_path = $destinationPath . "/";
    //         $data->file_name = $fileName;
    //         $data->file_name_origin = $file->getClientOriginalName();
    //         $data->status_id = 1;
    //         $data->created_at = Carbon::now();
    //         $data->created_by = auth()->user()->id;
    //         $data->save();
    //         $this->flashMessage('success', 'Upload File Outlet', 'Upload Success');
    //         return Redirect::to(route('outlet.import_form'));
    //     } catch (Exception $e) {
    //         Log::error($e);
    //         $this->flashMessage('error', 'Upload File Outlet', 'Upload Error');
    //         return Redirect::to(route('outlet.import_form'));
    //     }
    // }
    public function checkCSVError($importData_arr)
    {
        $error_message = [];

        if (count($importData_arr) > 15) {
            $message = 'Data melebihi 15 baris';
            array_push($error_message, $message);
        }

        if (count($importData_arr) < 1) {
            $message = 'Data kosong';
            array_push($error_message, $message);
        }

        foreach ($importData_arr as $index => $importData) {

            $unilever_id = explode(";", $importData[0])[0];
            $juragan_id = explode(";", $importData[0])[1];
            $name = explode(";", $importData[0])[2];
            $address = explode(";", $importData[0])[3];
            $phone = explode(";", $importData[0])[4];
            $phone2 = explode(";", $importData[0])[5];
            $owner = explode(";", $importData[0])[6];
            $outlet_type = explode(";", $importData[0])[7];
            $ownership_status = explode(";", $importData[0])[8];
            $steet_type_id = explode(";", $importData[0])[9];
            $latitude = explode(";", $importData[0])[11];
            $longitude = explode(";", $importData[0])[12];
            $id_province = explode(";", $importData[0])[13];
            $id_city = explode(";", $importData[0])[14];
            $id_district = explode(";", $importData[0])[15];
            $id_village = explode(";", $importData[0])[16];
            $cabinet = explode(";", $importData[0])[17];

            $check_juragan = Juragan::where('id', $juragan_id)->first();
            $check_phone = Outlet::where('phone', $phone)->first();
            $check_province = Province::find($id_province);
            $check_city = City::find($id_city);
            $check_district = District::find($id_district);
            $check_village = Village::find($id_village);
            $check_cabinet = Cabinets::where('qrcode', $cabinet)->first();
            $check_ownership_status = OwnershipStatus::find($ownership_status);
            $check_street_type = StreetType::find($steet_type_id);
            $check_outlet_type = OutletStatusType::find($outlet_type);

            if (strlen($unilever_id) < 3) {
                $message = 'Unilever ID baris ke-' . $index . ' kurang dari 3 karakter';
                array_push($error_message, $message);
            }

            if (!$check_juragan) {
                $message = 'Juragan ID baris ke-' . $index . ' tidak terdaftar';
                array_push($error_message, $message);
            }

            if (strlen($name) < 3) {
                $message = 'Unilever ID baris ke-' . $index . ' kurang dari 3 karakter';
                array_push($error_message, $message);
            }

            if (strlen($owner) < 3) {
                $message = 'Unilever ID baris ke-' . $index . ' kurang dari 3 karakter';
                array_push($error_message, $message);
            }

            if ($check_phone) {
                $message = 'Phone baris ke-' . $index . ' sudah terdaftar';
                array_push($error_message, $message);
            }

            if ($phone2 == "") {
                $message = 'Phone2 baris ke-' . $index . ' tidak boleh kosong';
                array_push($error_message, $message);
            }

            if (strlen($address) < 6) {
                $message = 'Address baris ke-' . $index . ' kurang dari 6 karakter';
                array_push($error_message, $message);
            }

            if ($latitude == "") {
                $message = 'Latitude baris ke-' . $index . ' tidak boleh kosong';
                array_push($error_message, $message);
            }

            if ($longitude == "") {
                $message = 'Longitude baris ke-' . $index . ' tidak boleh kosong';
                array_push($error_message, $message);
            }

            if (!$check_outlet_type) {
                $message = 'Outlet Type baris ke-' . $index . ' tidak valid';
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

            if (!$check_cabinet) {
                $message = 'Cabinet baris ke-' . $index . ' tidak terdaftar';
                array_push($error_message, $message);
            }

            if (!$check_ownership_status) {
                $message = 'Ownership ID baris ke-' . $index . ' tidak terdaftar';
                array_push($error_message, $message);
            }

            if (!$check_street_type) {
                $message = 'Street Type ID baris ke-' . $index . ' tidak terdaftar';
                array_push($error_message, $message);
            }
        }

        return $error_message;
    }

    public function doImport(Request $request)
    {
        if (!$request->hasFile('outlet_import_file')) {
            $this->flashMessage('error', 'Upload File Outlet', 'No such a file');
            return Redirect::to(route('outlet.import_form'));
        }

        $file = $request->file("outlet_import_file");
        if (strtolower($file->getClientOriginalExtension()) != "csv") {
            $this->flashMessage('error', 'Upload File Outlet', 'not csv file');
            return Redirect::to(route('outlet.import_form'));
        }

        //Move Uploaded File
        $destinationPath = config("app.job_base_dir") . "/outlet_imports";
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
                $this->createOutletJob($destinationPath, $fileName, $originalFileName, $check_error_messages, 3);
                $this->flashMessage('error', 'Upload File Outlet', 'Upload Error');
                return redirect('/import/outlet');
            } else {

                foreach ($importData_arr as $index => $importData) {
                    // dd($importData);
                    $location = new \stdClass();

                    $cabinet = explode(";", $importData[0])[17];

                    DB::beginTransaction();
                    $data = new Outlet();
                    $data->id_unilever = explode(";", $importData[0])[0];
                    $data->id_juragan = explode(";", $importData[0])[1];
                    $data->name = explode(";", $importData[0])[2];
                    $data->owner = explode(";", $importData[0])[6];
                    $data->phone = explode(";", $importData[0])[4];
                    $data->phone2 = explode(";", $importData[0])[5];
                    $data->address = explode(";", $importData[0])[3];
                    $data->latitude = explode(";", $importData[0])[11];
                    $data->longitude = explode(";", $importData[0])[12];
                    $location->latitude = (float) $data->latitude;
                    $location->longitude = (float) $data->longitude;
                    $data->location = json_encode($location);

                    $data->id_outlet_type = 1;
                    $data->id_ownership_status = 1;
                    $data->id_street_type = 1;

                    $data->id_country = config('app.country_id');
                    $data->id_province = explode(";", $importData[0])[13];
                    $data->id_city = explode(";", $importData[0])[14];
                    $data->id_district = explode(";", $importData[0])[15];
                    $data->id_village = explode(";", $importData[0])[16];

                    $data->descriptions = $request->input('descriptions');

                    $data->created_at = Carbon::now()->unix();
                    $data->created_by = \auth()->user()->id;
                    $data->is_deleted = 1;
                    $data->status_active = 1;
                    $data->save();

                    $outlet_id = Outlet::where('id_unilever', $data->id_unilever)->first()->id;

                    $mapOutlet = new MapOutlet();
                    $mapOutlet->id = Uuid::uuid4()->toString();
                    $mapOutlet->id_outlet = $outlet_id;
                    $mapOutlet->is_mitra = 1;
                    $mapOutlet->is_deleted = 1;
                    $mapOutlet->save();

                    $cabinet_id = Cabinets::where('qrcode', $cabinet)->first()->id;
                    $syncCabinet = new OutletHasCabinet();
                    $syncCabinet->outlet_id = $outlet_id;
                    $syncCabinet->cabinet_id = $cabinet_id;
                    $syncCabinet->created_at = Carbon::now();
                    $syncCabinet->created_by = \auth()->user()->id;                    
                    $syncCabinet->save();

                    DB::commit();
                }
                $this->createOutletJob($destinationPath, $fileName, $originalFileName, $check_error_messages, 2);
                $this->flashMessage('success', 'Upload File Outlet', 'Upload Success');
                return redirect('/import/outlet');
            }
        } catch (\Exception $e) {
            Log::error($e);
            DB::rollBack();
            dd($e);
            $this->flashMessage('error', 'Upload File Outlet', 'Upload Error');
            return redirect('/import/outlet');
        }
    }

    public function createOutletJob($destinationPath, $fileName, $originalFileName, $error_message, $status_id)
    {
        // save to job table        
        $data = new OutletJob();
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

        return $data->save();
    }

    public function export()
    {
        $request = Request::capture();
        $search = $request->query('search', '');
        $file_name = sprintf('outlet-%s.xlsx', Str::uuid()->toString());
        // dd('asd');
        return Excel::download(new OutletExport($search), $file_name);

        // return response()->streamDownload(function () use ($search) {
        //     echo file_get_contents(config('app.export_host') . '/outlet?' . http_build_query(['search' => $search]));
        // }, $file_name);
    }

    public function doImportCSDP(Request $request)
    {
        if (!$request->hasFile('import_file_csdp')) {
            $this->flashMessage('error', 'Upload CSDP', 'No such a file');
            return Redirect::to(route('outlet.index'));
        }

        $file = $request->file("import_file_csdp");
        if (!in_array(strtolower($file->getClientOriginalExtension()), ["csv", 'xlsx'])) {
            $this->flashMessage('error', 'Upload CSDP', 'not csv / xlsx file');
            return Redirect::to(route('outlet.index'));
        }

        try {
            DB::beginTransaction();
            $sheet = Excel::toArray(collect(), $file);
            $row = @$sheet[0];
            $columnOutletId = 6;
            $columnCSDP = 53;
            $errorRows = [];
            if (!empty($sheet) && !empty($row)) {
                foreach ($row as $rowNum => $column) {
                    if ($rowNum > 0) { // if not heading
                        $outlet = Outlet::find($column[$columnOutletId]);
                        $dataCSDP = $column[$columnCSDP];
                        if ($dataCSDP) {
                            if (strlen($dataCSDP) <= 20) {
                                $outlet->csdp = $dataCSDP;
                                $outlet->save();
                            } else {
                                $errorRows[] = $rowNum + 1;
                            }
                        }
                    }
                }
            }

            if (!empty($errorRows)) {
                $this->flashMessage('error', 'Upload CSDP', 'Data CSDP tidak valid pada baris ke-' . implode(',', $errorRows));
                return Redirect::to(route('outlet.index'));
            }
            DB::commit(); // End transaction
            $this->flashMessage('success', 'Upload CSDP', 'Upload Success');
            return Redirect::to(route('outlet.index'));
        } catch (Exception $e) {
            DB::rollback(); // Rollback transaction
            Log::error($e);
            $this->flashMessage('error', 'Upload CSDP', 'Upload Error');
            return Redirect::to(route('outlet.index'));
        }
    }


}