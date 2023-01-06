<?php
namespace App\Http\Controllers\UserManagement;

use App\Http\Controllers\GenericController;
use App\Models\UserManagement\SubMenu;
use App\Models\UserManagement\Menu;
use App\Models\UserManagement\Permission;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class SubMenuController extends GenericController {

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
        $d = SubMenu::join("menus", "menu_id", "=", "menus.id")->select("sub_menus.*", "menus.name as menu_name")->orderBy("menus.order_no", "asc")->orderBy("sub_menus.order_no", "asc");
        $data = [
            'datas' => $d->paginate(30)
        ];
        return view('UserManagement/SubMenu/index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(){
        $menus = Menu::all()->pluck('name', 'id');
        $permissions = Permission::all()->pluck('caption', 'id');
        $datas = [
            'menus' => $menus,
            'permissions' => $permissions,
            'route' => route('user.store')
        ];
        return view('UserManagement/SubMenu/create', $datas);
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
            'name' => 'required|min:3|max:255|unique:sub_menus,name',
            'menu' => 'required',
            'permission' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Redirect::to(route('submenu.create'))
                ->withErrors($validator)->withInput($request->all());
        } else {
            try {
                $data = new SubMenu();
                $data->name = $request->input('name');
                $data->menu_id = $request->input('menu');
                $data->permission_id = $request->input('permission');
                $data->order_no = 1;
                $data->created_by = Auth::user()->id;
                $data->save();
                $this->flashMessage('success', 'CREATE', 'create data success');
                return Redirect::to(route('submenu.index'));
            } catch (\Exception $e) {
                Log::error($e);
                $this->flashMessage('error', 'CREATE', 'create data failed');
                return Redirect::to(route('submenu.create'));
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
        $data = SubMenu::with(["menu","permission"])->find($id);
        if ($data == null) {
            $this->flashMessage('error', 'SHOW', 'Data not found');
            return Redirect::to(route('submenu.index'));
        }
        $datas = [

            'data' => $data
        ];
        return view('UserManagement/SubMenu/show', $datas);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $menus = Menu::all()->pluck('name', 'id');
        $permissions = Permission::all()->pluck('caption', 'id');
        $data = SubMenu::with(["menu","permission"])->find($id);
        if ($data == null) {
            $this->flashMessage('error', 'EDIT', 'Data not found');
            return redirect(route('submenu.index'));
        }
        $datas = [
            'menus' => $menus,
            'permissions' => $permissions,
            'data' => $data
        ];
        return view('UserManagement/SubMenu/edit', $datas);
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
            'name' => 'required|min:3|max:255',
            'menu' => 'required',
            'permission' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Redirect::to(route('submenu.edit'))
                ->withErrors($validator);
        } else {
            try {
                $data = SubMenu::find($id);
                $data->name = $request->input('name');
                $data->menu_id = $request->input('menu');
                $data->permission_id = $request->input('permission');
                $data->updated_by = $data->created_by = Auth::user()->id;
                $data->save();
                $this->flashMessage('success', 'EDIT', 'Edit data success');
                return Redirect::to(route('submenu.index'));
            } catch (\Exception $e) {
                $this->flashMessage('error', 'EDIT', 'create data failed');
                return Redirect::to(route('submenu.edit'));
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
            $data = SubMenu::find($id);
            $data->delete();
            $this->flashMessage('success', 'DELETE', 'delete data success');
        }catch (\Exception $e){
            $this->flashMessage('success', 'DELETE', 'delete data failed');
        }

        return Redirect::to(route('submenu.index'));
    }
}