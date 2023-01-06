<?php

namespace App\Http\Controllers\Hunter;

use App\Http\Controllers\GenericController;
use App\Models\City;
use App\Models\District;
use App\Models\Hunter\Form;
use App\Models\Hunter\Hunter;
use App\Models\Hunter\JourneyPlan;
use App\Models\JuraganManagement\Juragan;
use App\Models\Province;
use App\Models\Village;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class JourneyPlanController extends GenericController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $request = Request::capture();
        $query = JourneyPlan::where('is_deleted', 1);
        $canShow = Auth::user()->getPermissionByName('hunter.show');
        $canEdit = Auth::user()->getPermissionByName('hunter.edit');
        $canDestroy = Auth::user()->getPermissionByName('hunter.destroy');
        $search = trim($request->query('search', ''));
        if ($search !== '') {
            $query->where(function ($query) use ($search) {
                $query
                    ->orWhereRaw("id ilike ?", ["%" . $search . "%"])
                    ->orWhereHas('assignTo', function ($query) use ($search) {
                        $query->where("name", "ilike", "%{$search}%");
                    })
                    ->orWhereHas('juragan', function ($query) use ($search) {
                        $query->where("name", "ilike", "%{$search}%");
                    });
            });
        }
        $journeyPlans = $query->paginate(30);
        $journeyPlans->appends(['search' => $request->query("search")]);
        return view('hunter.journeyplan.index')
            ->with('can_show', $canShow)
            ->with('can_edit', $canEdit)
            ->with('can_destroy', $canDestroy)
            ->with('journeyplans', $journeyPlans);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $juragans = Juragan::select(['id', 'name'])
            ->where('is_deleted', 1)
            ->get()->pluck('name', 'id');
        $hunters = Hunter::select(['id', 'name'])
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
        $districts = [];
        $villages = [];
        return view('hunter.journeyplan.create')
            ->with('juragans', $juragans)
            ->with('hunters', $hunters)
            ->with('forms', $forms)
            ->with('provinces', $provinces)
            ->with('cities', $cities)
            ->with('districts', $districts)
            ->with('villages', $villages);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $id = $request->input('id');
        $name = $request->input('name');
        $idJuragan = $request->input('id_juragan');
        $id_form = '3'; //$request->input('id_form');
        $idProvince = $request->input('id_province');
        $idCity = $request->input('id_city');
        $idDistrict = $request->input('id_district');
        $idVillage = $request->input('id_village');
        $assigner = auth()->user()->id; // $assigner = $request->input('assigner');
        $assignTo = $request->input('assign_to');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $validator = Validator::make(
            [
                'id' => $id,
                'name' => $name,
                'id_juragan' => $idJuragan,
                'id_form' => $id_form,
                'id_province' => $idProvince,
                'id_city' => $idCity,
                'id_district' => $idDistrict,
                'id_village' => $idVillage,
                'assigner' => $assigner,
                'assign_to' => $assignTo,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
            [
                'id' => 'required|unique:App\Models\Hunter\JourneyPlan,id',
                'name' => 'required',
                'id_juragan' => 'required|exists:App\Models\JuraganManagement\Juragan,id,is_deleted,1',
                'id_form' => 'required|exists:App\Models\Hunter\Form,id,is_deleted,1',
                'id_province' => 'required|exists:App\Models\Province,id,is_deleted,1',
                'id_city' => 'required|exists:App\Models\City,id,is_deleted,1',
                'id_district' => 'required|exists:App\Models\District,id,is_deleted,1',
                'id_village' => 'required|exists:App\Models\Village,id,is_deleted,1',
                'assigner' => 'required',
                'assign_to' => 'required|exists:App\Models\Hunter\Hunter,id,is_deleted,1',
                'start_date' => 'required|date_format:Y-m-d',
                'end_date' => 'required|date_format:Y-m-d',
            ]
        );
        if ($validator->fails()) {
            $this->flashMessage('error', 'Create Journey Plan', $validator->errors()->first());
            return redirect()
                ->route('journeyplan.create')
                ->withInput($request->all());
        }
        $journeyPlan = new JourneyPlan();
        $journeyPlan->id = $id;
        $journeyPlan->name = $name;
        $journeyPlan->id_juragan = $idJuragan;
        $journeyPlan->id_form = $id_form;
        $journeyPlan->id_province = $idProvince;
        $journeyPlan->id_city = $idCity;
        $journeyPlan->id_district = $idDistrict;
        $journeyPlan->id_village = $idVillage;
        $journeyPlan->assigner = $assigner;
        $journeyPlan->assign_to = $assignTo;
        $journeyPlan->start_date = $startDate;
        $journeyPlan->end_date = $endDate;
        $journeyPlan->is_deleted = 1;
        try {
            $journeyPlan->save();
        } catch (\Exception $e) {
            Log::error($e);
            $this->flashMessage('error', 'Create Journey Plan', $e->getMessage());
            return redirect()
                ->route('journeyplan.create')
                ->withInput($request->all());
        }
        $this->flashMessage('success', 'Create Journey Plan', 'Create Journey Plan Success');
        return redirect()->route('journeyplan.index');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $journeyPlan = JourneyPlan::where('id', $id)
            ->where('is_deleted', 1)
            ->first();
        if (is_null($journeyPlan)) {
            $this->flashMessage('error', 'Show Journey Plan', 'Journey Plan Not Found');
            return redirect()
                ->route('journeyplan.index');
        }
        $juragans = Juragan::select(['id', 'name'])
            ->where('is_deleted', 1)
            ->get()->pluck('name', 'id');
        $hunters = Hunter::select(['id', 'name'])
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
        $districts = District::select(['id', 'name'])
            ->where('id_city', $journeyPlan->id_city)
            ->where('is_deleted', 1)
            ->get()->pluck('name', 'id');
        $villages = Village::select(['id', 'name'])
            ->where('id_district', $journeyPlan->id_district)
            ->where('is_deleted', 1)
            ->get()->pluck('name', 'id');
        return view('hunter.journeyplan.show')
            ->with('juragans', $juragans)
            ->with('hunters', $hunters)
            ->with('forms', $forms)
            ->with('provinces', $provinces)
            ->with('cities', $cities)
            ->with('districts', $districts)
            ->with('villages', $villages)
            ->with('journeyplan', $journeyPlan);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $journeyPlan = JourneyPlan::where('id', $id)
            ->where('is_deleted', 1)
            ->first();
        if (is_null($journeyPlan)) {
            $this->flashMessage('error', 'Edit Journey Plan', 'Journey Plan Not Found');
            return redirect()
                ->route('journeyplan.index');
        }
        $juragans = Juragan::select(['id', 'name'])
            ->where('is_deleted', 1)
            ->get()->pluck('name', 'id');
        $hunters = Hunter::select(['id', 'name'])
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
        $districts = District::select(['id', 'name'])
            ->where('id_city', $journeyPlan->id_city)
            ->where('is_deleted', 1)
            ->get()->pluck('name', 'id');
        $villages = Village::select(['id', 'name'])
            ->where('id_district', $journeyPlan->id_district)
            ->where('is_deleted', 1)
            ->get()->pluck('name', 'id');
        return view('hunter.journeyplan.edit')
            ->with('juragans', $juragans)
            ->with('hunters', $hunters)
            ->with('forms', $forms)
            ->with('provinces', $provinces)
            ->with('cities', $cities)
            ->with('districts', $districts)
            ->with('villages', $villages)
            ->with('journeyplan', $journeyPlan);
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
        $name = $request->input('name');
        $idJuragan = $request->input('id_juragan');
        $id_form = '3'; //$request->input('id_form');
        $idProvince = $request->input('id_province');
        $idCity = $request->input('id_city');
        $idDistrict = $request->input('id_district');
        $idVillage = $request->input('id_village');
        $assigner = auth()->user()->id;
        $assign_to = $request->input('assign_to');
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $validator = Validator::make(
            [
                'id' => $id,
                'name' => $name,
                'id_juragan' => $idJuragan,
                'id_form' => $id_form,
                'id_province' => $idProvince,
                'id_city' => $idCity,
                'id_district' => $idDistrict,
                'id_village' => $idVillage,
                'assigner' => $assigner,
                'assign_to' => $assign_to,
                'start_date' => $start_date,
                'end_date' => $end_date,
            ],
            [
                'id' => 'required|exists:App\Models\Hunter\JourneyPlan,id,is_deleted,1',
                'name' => 'required',
                'id_juragan' => 'required|exists:App\Models\JuraganManagement\Juragan,id,is_deleted,1',
                'id_form' => 'required|exists:App\Models\Hunter\Form,id,is_deleted,1',
                'id_province' => 'required|exists:App\Models\Province,id,is_deleted,1',
                'id_city' => 'required|exists:App\Models\City,id,is_deleted,1',
                'id_district' => 'required|exists:App\Models\District,id,is_deleted,1',
                'id_village' => 'required|exists:App\Models\Village,id,is_deleted,1',
                'assigner' => 'required',
                'assign_to' => 'required|exists:App\Models\Hunter\Hunter,id,is_deleted,1',
                'start_date' => 'required|date_format:Y-m-d',
                'end_date' => 'required|date_format:Y-m-d',
            ]
        );
        if ($validator->fails()) {
            $this->flashMessage('error', 'Edit Journey Plan', $validator->errors()->first());
            return redirect()
                ->route('journeyplan.edit', ['journeyplan' => $id])
                ->withInput($request->all());
        }
        $journeyPlan = JourneyPlan::find($id);
        $now = Carbon::now();
        if (\Carbon\Carbon::parse($journeyPlan->start_date)->diffInDays($now) < 2) {
            $this->flashMessage('error', 'Edit Journey Plan', 'cant edit journey plan, start date exceed');
            return redirect()
                ->route('journeyplan.edit', ['journeyplan' => $id])
                ->withInput($request->all());
        }
        $journeyPlan->name = $name;
        $journeyPlan->id_juragan = $idJuragan;
        $journeyPlan->id_form = $id_form;
        $journeyPlan->id_province = $idProvince;
        $journeyPlan->id_city = $idCity;
        $journeyPlan->id_district = $idDistrict;
        $journeyPlan->id_village = $idVillage;
        $journeyPlan->assigner = $assigner;
        $journeyPlan->assign_to = $assign_to;
        $journeyPlan->start_date = $start_date;
        $journeyPlan->end_date = $end_date;
        try {
            $journeyPlan->save();
        } catch (\Exception $e) {
            Log::error($e);
            $this->flashMessage('error', 'Edit Journey Plan', $e->getMessage());
            return redirect()
                ->route('journeyplan.edit', ['journeyplan' => $id])
                ->withInput($request->all());
        }
        $this->flashMessage('success', 'Edit Journey Plan', 'Edit Journey Plan Success');
        return redirect()->route('journeyplan.index');
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
                'id' => 'required|exists:App\Models\Hunter\JourneyPlan,id,is_deleted,1',
            ]
        );
        if ($validator->fails()) {
            $this->flashMessage('error', 'Delete Journey Plan', $validator->errors()->first());
            return redirect()
                ->route('journeyplan.index');
        }
        $journeyPlan = JourneyPlan::where('id', $id)
            ->where('is_deleted', 1)
            ->first();
        $journeyPlan->is_deleted = 2;
        try {
            $journeyPlan->save();
        } catch (\Exception $e) {
            Log::error($e);
            $this->flashMessage('error', 'Delete Journey Plan', $e->getMessage());
            return redirect()
                ->route('journeyplan.index');
        }
        $this->flashMessage('success', 'Delete Journey Plan', 'Delete Journey Plan Success');
        return redirect()->route('journeyplan.index');
    }
}
