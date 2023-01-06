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
                                <a href="{{route('user.index')}}">User</a>
                            </li>
                            <li class="active">
                                Edit
                            </li>
                        </ol>
                        <h4 class="m-t-0 header-title">
                            <b>Data User</b>
                        </h4>
                        {{ Html::ul($errors->all()) }}
                        {{ Form::model($data, array('route' => array('user.update', $data->id), 'method' => 'PUT', 'role'=>'form')) }}
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
                            {{ Form::label('email', 'Email', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::text('email', old('email', $data->email), array('class' => 'form-control', 'placeholder'=>'Email', 'required'=>'required')) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('phone', 'Phone', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::text('phone', old('phone', $data->phone), array('class' => 'form-control', 'placeholder'=>'Phone', 'required'=>'required')) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('role', 'Role', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::select('role', $roles, $data->roles[0]->id, ['placeholder' => 'Pilih Role', 'class' => 'form-control', 'required' => 'required']) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Pilih salah satu Role, tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-12">
                                <div class="col-6">
                                    <button type="submit" class="btn btn-success pull-right">
                                        <i class="fa fa-check"></i> Save
                                    </button>
                                    <a class="btn btn-danger" href="{{ route('user.index') }}">
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