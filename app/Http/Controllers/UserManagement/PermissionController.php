<?php

namespace App\Http\Controllers\UserManagement;

/**
 * Description of PermissionController
 *
 * @author nuansa.ramadhan
 */
use Illuminate\Http\Request;
use App\Http\Controllers\GenericController;
use App\Models\UserManagement\Permission;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Ramsey\Uuid\Uuid;

class PermissionController extends GenericController {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $data = Permission::orderBy('name')->paginate(30);
        $datas = [
            'datas'=>$data
        ];
        return view('UserManagement/Permission/index', $datas);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return view('UserManagement/Permission/create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $rules = array(
            'name' => 'required|min:3|max:100|unique:permissions,name',
            'caption' => 'required|min:3|max:100|unique:permissions,caption'
        );

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Redirect::to(route('permission.create'))
                ->withErrors($validator);
        } else {
            $data = new Permission;
            $data->id = Uuid::uuid4()->toString();
            $data->name = $request->input('name');
            $data->caption = $request->input('caption');
            $data->created_by = auth()->user()->id;
            if ($data->save()) {
                $this->flashMessage('success', 'CREATE', 'create data success');
                return Redirect::to(route('permission.create'));
//                return Redirect::to(route('permission.index'));
            } else {
                Log::error($e);
                $this->flashMessage('error', 'CREATE', 'create data failed');
                return Redirect::to(route('permission.create'));
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $data = Permission::find($id);
        if ($data == null) {
            $this->flashMessage('error', 'SHOW', 'data not found');
            return Redirect::to(route('permission.index'));
        }
        $datas = [
            'data' => $data
        ];
        return view('UserManagement/Permission/show', $datas);
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $data = Permission::find($id);
        if ($data == null) {
            $this->flashMessage('error', 'EDIT', 'data not found');
            return redirect(route('permission.index'));
        }
        $datas = [
            'data' => $data
        ];
        return view('UserManagement/Permission/edit', $datas);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $rules = array(
            'name' => [
                'required',
                'min:3',
                'max:255',
                Rule::unique('permissions')->ignore($id)
            ],
            'caption' => [
                'required',
                'min:3',
                'max:255'
            ]
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Redirect::to(route('permission.edit',['permission'=>$id]))
                ->withErrors($validator);
        } else {
            $data = Permission::find($id);
            $data->name = $request->input('name');
            $data->caption = $request->input('caption');
            if ($data->save()) {
                $this->flashMessage('success', 'EDIT', 'edit data success');
                return Redirect::to(route('permission.index'));
            } else {
                $this->flashMessage('error', 'EDIT', 'create data failed');
                return redirect(route('permission.edit',['id'=>$id]));
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        try{
            $data = Permission::find($id);
            $data->delete();
            $this->flashMessage('error', 'EDIT', 'delete data failed');
        }catch (\Exception $e){
            $this->flashMessage('error', 'EDIT', 'delete data failed');
        }
        return Redirect::to(route('permission.index'));
    }

}
