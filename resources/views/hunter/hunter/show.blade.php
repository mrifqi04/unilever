@extends('layouts.app')
@section('content')

    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card-box">
                        <ol class="breadcrumb">
                            <li>
                                Hunter
                            </li>
                            <li>
                                <a href="{{route('hunter.index')}}">Hunter Management</a>
                            </li>
                            <li class="active">
                                Show
                            </li>
                        </ol>
                        <h4 class="m-t-0 header-title">
                            <b>Data Hunter</b>
                        </h4>
                        {{ Html::ul($errors->all()) }}
                        <div class="form-group">
                            {{ Form::label('id', 'ID', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                {{ $hunter->id }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('unilever_id', 'Unilever ID', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                {{ $hunter->id_unilever }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('name', 'Name', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                {{ $hunter->name }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('email', 'Email', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                {{ $hunter->email }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('phone', 'Phone', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                {{ $hunter->phone }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('province', 'Province', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                {{$hunter->province->name}}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('city', 'City', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                {{$hunter->city->name}}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('district', 'District', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                {{$hunter->district->name}}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('village', 'Village', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                {{$hunter->village->name}}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('address', 'Address', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                {{ $hunter->address }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('latitude', 'Latitude', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                {{ $hunter->latitude }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('longitude', 'Longitude', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                {{ $hunter->longitude }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('id_user_types', 'User Type', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                {{ $hunter->userType->name }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('start_date', 'Start Date', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                {{ \Carbon\Carbon::parse($hunter->start_date)->format('Y-m-d') }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('end_date', 'End Date', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                {{ \Carbon\Carbon::parse($hunter->end_date)->format('Y-m-d') }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('active', 'Active', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                {{ $hunter->status_active == 1 ? 'Yes' : 'No'}}
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-2"></div>
                            <div class="col-10">
                                <a class="btn btn-danger m-t-20" href="{{ route('hunter.index') }}">
                                    <i class="fa fa-times"></i>Back
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@include('layouts.alert')