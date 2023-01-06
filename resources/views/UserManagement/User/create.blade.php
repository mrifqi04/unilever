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
                                Create
                            </li>
                        </ol>
                        <h4 class="m-t-0 header-title">
                            <b>Data User</b>
                        </h4>
                        {{ Html::ul($errors->all()) }}
                        {{ Form::open(array('url' => route('user.store'), 'role' => 'form')) }}
                        <div class="form-group">
                            {{ Form::label('role', 'Role', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::select('role', $roles, null, ['placeholder' => 'Pilih Role', 'class' => 'form-control', 'required' => 'required']) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Pilih salah satu Role, tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('name', 'Name', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::text('name', '', array('class' => 'form-control', 'placeholder'=>'Name', 'required'=>'required')) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('phone', 'Phone', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::text('phone', '', array('class' => 'form-control', 'placeholder'=>'628111111111', 'required'=>'required')) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('email', 'Email', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::text('email', '', array('class' => 'form-control', 'placeholder'=>'Email', 'required'=>'required')) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Tidak boleh dikosongkan.
                                </small>
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
                            {{ Form::label('confirm_password', 'Confirm Password', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::password('confirm_password', array('class' => 'form-control', 'placeholder'=>'Confirm Password', 'required'=>'required')) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-10">
                                <button type="submit" class="btn btn-success m-t-20 pull-right"><i class="fa fa-check"></i> Save</button>
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