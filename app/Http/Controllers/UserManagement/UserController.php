<?php

namespace App\Http\Controllers\UserManagement;


use Illuminate\Http\Request;
use App\Http\Controllers\GenericController;
use App\Models\UserManagement\User;
use App\Models\UserManagement\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

/**
 * Description of UserController
 *
 * @author nuansa.ramadhan
 */
class UserController extends GenericController {

    //put your code here
    public function __construct() {
        parent::__construct();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $users = new User;
        if($request->query("usernmae") != ""){
            $users = $users->where("username", $request->query("username"));
        }
        if($request->query("phone") != ""){
            $users = $users->where("phone", $request->query("phone"));
        }

        $data = $users->paginate(30);
        $data = [
            'datas' => $data
        ];

        return view('UserManagement/User/index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        $roles = Role::all()->pluck('name', 'id');
        $datas = [
            'roles' => $roles,
            'route' => route('user.store')
        ];
        return view('UserManagement/User/create', $datas);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $rules = array(
            'name' => 'required|min:3|max:255',
            'phone' => 'required|min:8|max:20|unique:users,phone',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|max:255',
            'confirm_password' => 'required|same:password|min:6|max:255',
            'role' => 'required|exists:roles,id',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Redirect::to(route('user.create'))
                            ->withErrors($validator)->withInput($request->all());
        } else {
            DB::beginTransaction();
            try {
                $data = new User;
                $data->id = Uuid::uuid4()->toString();
                $data->username = $request->input('email');
                $data->name = $request->input('name');
                $data->email = $request->input('email');
                $data->phone = $request->input('phone');
                $data->password = bcrypt($request->input('password'));
                $data->created_by = \auth()->user()->id;
                $data->save();
                $data->roles()->attach($request->input('role'));
                DB::commit();
                $this->flashMessage('success', 'CREATE', 'create data success');
                return Redirect::to(route('user.index'));
            } catch (\Exception $e) {
                DB::rollback();
                Log::error($e);
                $this->flashMessage('error', 'CREATE', $e->getMessage());
                return Redirect::to(route('user.create'));
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
        $data = User::find($id);
        if ($data == null) {
            $this->flashMessage('error', 'SHOW', 'Data not found');
            return Redirect::to(route('user.index'));
        }
        $datas = [
            'data' => $data
        ];
        return view('UserManagement/User/show', $datas);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $roles = Role::all()->pluck('name', 'id');
        $data = User::find($id);

        if ($data == null) {
            $this->flashMessage('error', 'EDIT', 'Data not found');
            return redirect(route('user.index'));
        }
        $datas = [
            'data' => $data,
            'roles' => $roles
        ];
        return view('UserManagement/User/edit', $datas);
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
                'max:255'
            ],
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($id)
            ],
            'phone' => [
                'required',
                'min:8',
                'max:20',
                Rule::unique('users')->ignore($id)
            ],
            'role' => 'required|exists:roles,id'
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Redirect::to(route('user.edit', ['user'=>$id]))
                            ->withErrors($validator)->withInput($request->all());
        } else {
            DB::beginTransaction();
            try{
                $data = User::find($id);
                $data->name = $request->input('name');
                $data->email = $request->input('email');
                $data->username = $request->input('email');
                $data->phone = $request->input("phone");
                $data->updated_by = \Illuminate\Support\Facades\Auth::user()->id;
                $data->save();
                $data->roles()->sync($request->input('role'));
                DB::commit();
                $this->flashMessage('success', 'EDIT', 'Edit data success');
                return Redirect::to(route('user.index'));
            } catch (\Exception $ex) {
                DB::rollback();
                Log::error($e);
                $this->flashMessage('error', 'EDIT', $ex->getMessage());
                return Redirect::to(route('user.edit', ['user'=>$id]));
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function resetpassword($id) {
        $data = User::find($id);
        if ($data == null) {
            $this->flashMessage('error', 'RESET PASSWORD', 'Data not found');
            return redirect(route('user.index'));
        }
        $datas = [
            'data' => $data
        ];
        return view('UserManagement/User/resetpassword', $datas);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function doresetpassword(Request $request, $id) {
        $rules = array(
            'password' => [
                'required',
                'min:6',
                'max:255'
            ],
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Redirect::to(route('user.edit', ['id'=>$id]))
                ->withErrors($validator)->withInput($request->all());
        } else {
            DB::beginTransaction();
            try{
                $data = User::find($id);
                $data->password = bcrypt($request->input('password'));
                $data->updated_by = \Illuminate\Support\Facades\Auth::user()->id;
                $data->save();
                DB::commit();
                $this->flashMessage('success', 'RESET PASSWORD', 'Reset Password success');
                return Redirect::to(route('user.index'));
            } catch (\Exception $ex) {
                DB::rollback();
                Log::error($e);
                $this->flashMessage('error', 'RESET PASSWORD', $ex->getMessage());
                return Redirect::to(route('user.resetpassword', ['id'=>$id]));
            }
        }
    }

    public function profile() {
        $data = User::find(Auth::user()->id);
        if ($data == null) {
            $this->flashMessage('error', 'PROFILE', 'Data not found');
            return redirect(route('home.index'));
        }
        $datas = [
            'data' => $data
        ];
        return view('UserManagement/User/profile', $datas);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateprofile(Request $request) {
        $rules = array(
            'name' => [
                'required',
                'min:3',
                'max:255'
            ],
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore(Auth::user()->id)
            ],
            'password' => [
                'nullable',
                'min:6',
                'max:255'
            ],
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            dd(Input::all());
            return Redirect::to(route('user.profile'))
                ->withErrors($validator)->withInput($request->all());
        } else {
            DB::beginTransaction();
            try{
                $data = User::find(Auth::user()->id);
                $data->name = $request->input('name');
                $data->email = $request->input('email');
                if(Input::get('password') != null){
                    $data->password = bcrypt(Input::get('password'));
                }
                $data->updated_by = Auth::user()->id;
                $data->save();
                DB::commit();
                $this->flashMessage('success', 'UPDATE PROFILE', 'Edit data success');
                return Redirect::to(route('user.profile'));
            } catch (\Exception $ex) {
                DB::rollback();
                Log::error($e);
                $this->flashMessage('error', 'UPDATE PROFILE', $ex->getMessage());
                return Redirect::to(route('user.profile'));
            }
        }
    }

}