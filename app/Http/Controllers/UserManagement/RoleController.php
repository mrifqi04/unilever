<?php

namespace App\Http\Controllers\UserManagement;

/**
 * Description of RoleController
 *
 * @author nuansa.ramadhan
 */

use Illuminate\Http\Request;
use App\Http\Controllers\GenericController;
use App\Models\UserManagement\Role;
use App\Models\UserManagement\Permission;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Input;
use Ramsey\Uuid\Uuid;

class RoleController extends GenericController
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::paginate(30);
        $data = [
            'datas' => $roles
        ];
        return view('UserManagement/Role/index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('UserManagement/Role/create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = array(
            'name' => 'required|min:3|max:255|unique:App\Models\UserManagement\Role,name'
        );

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Redirect::to(route('role.create'))
                ->withErrors($validator)->withInput($request->all());
        } else {
            try {
                $data = new Role();
                $data->id = Uuid::uuid4()->toString();
                $data->name = $request->input('name');
                $data->save();
                $this->flashMessage('success', 'CREATE', 'create data success');
                return Redirect::to(route('role.index'));
            } catch (\Exception $e) {
                Log::error($e);
                $this->flashMessage('error', 'CREATE', 'create data failed');
                return Redirect::to(route('role.create'));
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = Role::find($id);
        if ($data == null) {
            $this->flashMessage('error', 'SHOW', 'Data not found');
            return Redirect::to(route('role.index'));
        }
        $datas = [
            'data' => $data
        ];
        return view('UserManagement/Role/show', $datas);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = Role::find($id);
        if ($data == null) {
            $this->flashMessage('error', 'EDIT', 'Data not found');
            return redirect(route('role.index'));
        }
        $datas = [
            'data' => $data
        ];
        return view('UserManagement/Role/edit', $datas);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $rules = array(
            'name' => 'required|min:3|max:255|unique:App\Models\UserManagement\Role,name'.$id,
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Redirect::to(route('role.edit',['role'=>$id]))
                ->withErrors($validator);
        } else {
            try {
                $data = Role::find($id);
                $data->name = $request->input('name');
                $data->save();
                $this->flashMessage('success', 'EDIT', 'Edit data success');
                return Redirect::to(route('role.index'));
            } catch (\Exception $e) {
                Log::error($e);
                $this->flashMessage('error', 'EDIT', 'create data failed');
                return Redirect::to(route('role.edit',['role'=>$id]));
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            $data = Role::find($id);
            $data->delete();
            $this->flashMessage('success', 'DELETE', 'delete data success');
        }catch (\Exception $e){
            Log::error($e);
            $this->flashMessage('success', 'DELETE', 'delete data failed');
        }

        return Redirect::to(route('role.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function permissions($id)
    {
        $data = Role::with(['permissions' => function($q){
            $q->orderBy('name', 'desc');
        }])->find($id);
        $permissions = Permission::whereDoesntHave('roles', function ($query) use ($id) {
            $query->where('role_id', $id);
        })->OrderBy('name')->get();
        $datas = [
            'data' => $data,
            'permissions' => $permissions,
        ];

        return view('UserManagement/Role/permissions', $datas);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function addpermissions($id)
    {
        $data = Role::find($id);
        if ($data == null) {
            $this->flashMessage('error', 'SHOW', 'data not found');
            return Redirect::to(route('role.show', ['id' => $id]));
        }
        $permissions = Permission::whereDoesntHave('roles', function ($query) use ($id) {
            $query->where('role_id', $id);
        })->OrderBy('name')->get();
        $datas = [
            'data' => $data,
            'permissions' => $permissions,
        ];
        return view('UserManagement/Role/addpermissions', $datas);
    }

    public function storepermissions(Request $request, $id)
    {
        $rules = array(
            'from' => [
                'required'
            ]
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Redirect::to(route('role.addpermissions', ['id' => $id]))
                ->withErrors($validator);
        } else {
            try{
                $data = Role::find($id);
                $data->permissions()->sync($request->input('from', []));
                $this->flashMessage('success', 'Add Permission', 'add data success');
                return Redirect::to(route('role.permissions', ['id' => $id]));
            }catch (\Exception $e){
                Log::error($e);
                $this->flashMessage('error', 'SAVE PERMISSION', $e->getMessage());
                return Redirect::to(route('role.permissions', ['id' => $id]));
            }
        }
    }

    public function destroypermissions(Request $request, $id)
    {
        $rules = array(
            'permission' => [
                'required'
            ]
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Redirect::to(route('role.permissions', ['id' => $id]))
                ->withErrors($validator);
        } else {
            $data = Role::find($id);
            $data->permissions()->detach($request->input('permission', []));
            $this->flashMessage('success', 'Remove Permission', 'remove data success');
            return Redirect::to(route('role.permissions', ['id' => $id]));
        }
    }
}
