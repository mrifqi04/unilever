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
                                Reset Password
                            </li>
                        </ol>
                        <h4 class="m-t-0 header-title">
                            <b>Data User</b>
                        </h4>
                        {{ Html::ul($errors->all()) }}
                        {{ Form::open(array('url' => route('user.doresetpassword', ['id'=>$data->id]), 'role' => 'form')) }}

                        <div class="form-group">
                            {{ Form::label('name', 'Name', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                {{ $data->name }}
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('password', 'Password', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::password('password', array('class' => 'form-control', 'placeholder'=>'Password', 'required'=>'required')) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-10">
                                <button type="submit" class="btn btn-success m-t-20 pull-right"><i class="fa fa-check"></i> Reset</button>
                                <a class="btn btn-danger m-t-20" href="{{ route('user.index') }}"><i class="fa fa-times"></i> Cancel</a>
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