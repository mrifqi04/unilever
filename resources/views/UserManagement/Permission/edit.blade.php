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
                                <a href="{{route('permission.index')}}">Permission</a>
                            </li>
                            <li class="active">
                                Edit
                            </li>
                        </ol>
                        <h4 class="m-t-0 header-title">
                            <b>Data Permission</b>
                        </h4>
                        {{ Html::ul($errors->all()) }}
                        {{ Form::model($data, array('route' => array('permission.update', $data->id), 'method' => 'PUT', 'role'=>'form')) }}
                        <div class="form-group">
                            {{ Form::label('caption', 'Caption', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::text('caption', old('caption', $data->caption), array('class' => 'form-control', 'placeholder'=>'Permission Caption', 'required'=>'required')) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('name', 'Name', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::text('name', old('name', $data->name), array('class' => 'form-control', 'placeholder'=>'Permission Name', 'required'=>'required')) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-12">
                                <div class="col-6">
                                    <button type="submit" class="btn btn-success pull-right">
                                        <i class="fa fa-check"></i> Save
                                    </button>
                                    <a class="btn btn-danger" href="{{ route('permission.index') }}">
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