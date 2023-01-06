@extends('layouts.app')
@section('content')


    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card-box">
                        <ol class="breadcrumb">
                            <li class="active">
                                <a href="{{route('user.profile')}}">Profile</a>
                            </li>
                        </ol>
                        <h4 class="m-t-0 header-title">
                            <b>Data Profile</b>
                        </h4>
                        {{ Html::ul($errors->all()) }}
                        {{ Form::model($data, array('route' => array('user.updateprofile'), 'method' => 'POST', 'role'=>'form')) }}
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
                            {{ Form::label('role', 'Role', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                {{ $data->roles[0]->name }}
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('password', 'Password', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::password('password', array('class' => 'form-control', 'placeholder'=>'Password')) }}
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-12">
                                <button type="submit" class="btn btn-success">
                                    <i class="fa fa-check"></i> Save
                                </button>
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