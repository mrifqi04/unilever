<?php
namespace App\Http\Controllers\UserManagement;

use App\Http\Controllers\GenericController;
use App\Models\UserManagement\Menu;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class MenuController extends GenericController {

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
        $roles = Menu::with("submenus")->paginate(30);
        $data = [
            'datas' => $roles
        ];
        return view('UserManagement/Menu/index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(){
        return view('UserManagement/Menu/create');
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
            'name' => 'required|min:3|max:255|unique:menus,name'
        );

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Redirect::to(route('menu.create'))
                ->withErrors($validator)->withInput($request->all());
        } else {
            try {
                $data = new Menu();
                $data->name = $request->input('name');
                $data->order_no = 1;
                $data->created_by = Auth::user()->id;
                $data->save();
                $this->flashMessage('success', 'CREATE', 'create data success');
                return Redirect::to(route('menu.index'));
            } catch (\Exception $e) {
                Log::error($e);
                $this->flashMessage('error', 'CREATE', 'create data failed');
                return Redirect::to(route('menu.create'));
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
        $data = Menu::find($id);
        if ($data == null) {
            $this->flashMessage('error', 'SHOW', 'Data not found');
            return Redirect::to(route('menu.index'));
        }
        $datas = [
            'data' => $data
        ];
        return view('UserManagement/Menu/show', $datas);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = Menu::find($id);
        if ($data == null) {
            $this->flashMessage('error', 'EDIT', 'Data not found');
            return redirect(route('menu.index'));
        }
        $datas = [
            'data' => $data
        ];
        return view('UserManagement/Menu/edit', $datas);
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
            'name' => [
                'required',
                'min:3',
                'max:255',
                Rule::unique('roles')->ignore($id)
            ]
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Redirect::to(route('menu.edit'))
                ->withErrors($validator);
        } else {
            try {
                $data = Menu::find($id);
                $data->name = $request->input('name');
                $data->save();
                $this->flashMessage('success', 'EDIT', 'Edit data success');
                return Redirect::to(route('menu.index'));
            } catch (\Exception $e) {
                Log::error($e);
                $this->flashMessage('error', 'EDIT', 'create data failed');
                return Redirect::to(route('menu.edit'));
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
            $data = Menu::find($id);
            $data->delete();
            $this->flashMessage('success', 'DELETE', 'delete data success');
        }catch (\Exception $e){
            Log::error($e);
            $this->flashMessage('success', 'DELETE', 'delete data failed');
        }

        return Redirect::to(route('menu.index'));
    }
}