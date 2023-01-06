<?php

namespace App\Http\Controllers\Auditor;

use App\export\AuditorExport;
use App\Http\Controllers\GenericController;
use App\Models\Auditor\Auditor;
use App\Models\Auditor\Login;
use App\Models\City;
use App\Models\District;
use App\Models\JuraganManagement\Juragan;
use App\Models\Province;
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

class PullCabinetController extends GenericController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $request = Request::capture();
        $page = $request->query('page', '');
        $pageSize = 30;
        $query = Auditor::where('is_deleted', 1);
        $search = $request->query('search', '');
        $canShow = Auth::user()->getPermissionByName('auditor.pull-cabinet.show');
        $canEdit = Auth::user()->getPermissionByName('auditor.pull-cabinet.edit');
        $canDestroy = Auth::user()->getPermissionByName('auditor.pull-cabinet.destroy');
        if ($search !== '') {
            $query->where(function ($query) use ($search) {
                $query->orWhereRaw("id ilike ?", ["%" . $search . "%"])
                    ->orWhereRaw("id_unilever ilike ?", ["%" . $search . "%"])
                    ->orWhereRaw("name ilike ?", ["%" . $search . "%"])
                    ->orWhereRaw("email ilike ?", ["%" . $search . "%"])
                    ->orWhereRaw("phone ilike ?", ["%" . $search . "%"]);
            });
        }
        $datas = $query->paginate($pageSize, ['*'], 'page', $page);
        return view('Auditor.pullcabinet.index')
            ->with('can_show', $canShow)
            ->with('can_edit', $canEdit)
            ->with('can_destroy', $canDestroy)
            ->with('datas', $datas)
            ->with('search', $search);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $auditor = Auditor::where('id', $id)
            ->where('is_deleted', 1)
            ->first();
        if (is_null($auditor)) {
            return redirect()
                ->route('auditor.pull-cabinet.index')
                ->with('message', sprintf('auditor with id %s not found', $id));
        }
        
        return view('Auditor.pullcabinet.show')
            ->with('auditor', $auditor);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $auditor = Auditor::where('id', $id)
            ->where('is_deleted', 1)
            ->first();
        if (is_null($auditor)) {
            return redirect()
                ->route('auditor.pull-cabinet.index')
                ->with('message', sprintf('auditor with id %s not found', $id));
        }
        return view('Auditor.pullcabinet.edit')
            ->with('auditor', $auditor);
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
        $validator = Validator::make(
            [
                'id' => $id,
            ],
            [
                'id' => 'required|exists:App\Models\Auditor\Auditor,id,is_deleted,1',
            ]
        );
        if ($validator->fails()) {
            Log::info($validator->errors());
            Log::info($request->all());
            $this->flashMessage('error', 'Update Auditor', $validator->errors()->first());
            return redirect()
                ->route('auditor.pull-cabinet.edit', ['auditor' => $id])
                ->withInput($request->all());
        }
        $auditor = Auditor::where('id', $id)
            ->where('is_deleted', 1)
            ->first();
        try {
            DB::beginTransaction();
            $auditor->id_juragan_mappings = $request->get('id_juragan_mappings', null);
            $auditor->updated_at          = Carbon::now()->timestamp;
            $auditor->updated_by          = auth()->user()->id;
            $auditor->save();
            DB::commit();
            $this->flashMessage('success', 'Update Auditor', "Update Auditor Success");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            $this->flashMessage('error', 'Update Auditor', $e->getMessage());
            return redirect()
                ->route('auditor.pull-cabinet.edit', ['auditor' => $id])
                ->withInput($request->all());
        }
        return redirect()->route('auditor.pull-cabinet.index');
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
                ->route('auditor.pull-cabinet.index')
                ->with('message', $validator->errors());
        }
        $auditor = Auditor::where('id', $id)
            ->where('is_deleted', 1)
            ->first();
        $auditor->is_deleted = 2;
        try {
            $auditor->save();
            $this->flashMessage('success', 'Delete Auditor', "Delete Auditor Success");
        } catch (\Exception $e) {
            Log::error($e);
            return redirect()
                ->route('auditor.pull-cabinet.index')
                ->with('message', sprintf('error deleting auditor %s', $e->getMessage()));
        }
        return redirect()->route('auditor.pull-cabinet.index');
    }

    public function getJuragan() {
        $request   = Request::capture();
        $isShow    = $request->get('is_show', 0);
        $id        = $request->query('id', '');
        $draw      = $request->query('draw', '');
        $start     = $request->query('start', '');
        $pageSize  = $request->query('length', '');
        $page      = ($start > 0) ? ($start / $pageSize + 1) : 1;
        $keyword   = $request->get('search')['value'];
        $getOrder  = $request->get('order', []);
        $listOrder = ['id', 'id', 'id_unilever_owner', 'name', 'email', 'phone', 'created_at'];
        $order     = $listOrder[$getOrder[0]['column']];
        $orderType = $getOrder[0]['dir'] ? $getOrder[0]['dir'] : 'asc';
        $ownedJuragan = [];
        if ($id) {
            $query = Auditor::whereNotNull('id_juragan_mappings');
            if ($isShow == 1) {
                $query->where('id', $id);
            } else {
                $query->where('id', '!=', $id);
            }
            $auditor = $query->get(['id_juragan_mappings']);
            if ($auditor) {
                $ownedJuragan = array_unique(explode(',', $auditor->implode('id_juragan_mappings', ',')));
            }
        }
        
        $query       = Juragan::where('is_deleted', 1)->orderBy($order, $orderType);
        if ($isShow == 1) {
            $query->whereIn('id', $ownedJuragan);
        } else {
            $query->whereNotIn('id', $ownedJuragan);
        }
        $total       = $query->count();
        $totalFilter = $total;
        
        if ($keyword) {
            $query->where(function($query) use ($keyword) {
                $query->WhereRaw("id ilike ?", ["%" . $keyword . "%"])
                ->orWhereRaw("id_unilever_owner ilike ?", ["%" . $keyword . "%"])
                ->orWhereRaw("name ilike ?", ["%" . $keyword . "%"])
                ->orWhereRaw("email ilike ?", ["%" . $keyword . "%"])
                ->orWhereRaw("phone ilike ?", ["%" . $keyword . "%"]);                
            });
            $totalFilter = $query->count();
        }
        $juragan = $query->paginate($pageSize, ['*'], 'page', $page);
        $output['draw'] = $draw;
        $output['recordsTotal'] = $total;
        $output['recordsFiltered'] = $totalFilter;
        $output['data'] = [];
        
        foreach ($juragan as $key => $value) {
            $output['data'][] = [
                '<input type="checkbox" id="val'."$key".'" class="id_juragan" name="id_juragan[]" value='."$value->id".'>&nbsp;',
                $value->id,
                $value->id_unilever_owner,
                $value->name,
                $value->email,
                $value->phone,
                Carbon::parse($value->created_at)->format("Y-m-d H:i:s"),
            ];
        }
        
        return response()->json($output);
    }
}
