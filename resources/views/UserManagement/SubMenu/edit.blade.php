@extends('layouts.app')
@section('content')


    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card-box">
                        <ol class="breadcrumb">
                            <li>
                                User Management
                            </li>
                            <li>
                                <a href="{{route('submenu.index')}}">Sub Menu</a>
                            </li>
                            <li class="active">
                                Edit
                            </li>
                        </ol>
                        <h4 class="m-t-0 header-title">
                            <b>Data Menu</b>
                        </h4>
                        {{ Html::ul($errors->all()) }}
                        {{ Form::model($data, array('route' => array('submenu.update', $data->id), 'method' => 'PUT', 'role'=>'form')) }}

                        <div class="form-group">
                            {{ Form::label('menu', 'Group Menu', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::select('menu', $menus, $data->menu->id, ['placeholder' => 'Pilih Menu', 'class' => 'form-control', 'required' => 'required']) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Pilih salah satu Group Menu, tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('name', 'Name', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::text('name', old('name', $data->name), array('class' => 'form-control', 'placeholder'=>'Name', 'required'=>'required')) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('permission', 'Permission', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::select('permission', $permissions, $data->permission->id, ['placeholder' => 'Pilih Permission', 'class' => 'form-control', 'required' => 'required']) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Pilih salah satu Permission, tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-12">
                                <div class="col-6">
                                    <button type="submit" class="btn btn-success pull-right">
                                        <i class="fa fa-check"></i> Save
                                    </button>
                                    <a class="btn btn-danger" href="{{ route('submenu.index') }}">
                                        <i class="fa fa-times"></i>Cancel
                                    </a>
                                </div>
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@include('layouts.alert')