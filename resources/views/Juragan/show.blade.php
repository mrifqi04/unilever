@extends('layouts.app')
@section('content')

    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card-box">
                        <ol class="breadcrumb">
                            <li>
                                Juragan
                            </li>
                            <li>
                                <a href="{{route('juragan.index')}}">Juragan Management</a>
                            </li>
                            <li class="active">
                                Show
                            </li>
                        </ol>
                        <h4 class="m-t-0 header-title">
                            <b>Data Juragan</b>
                        </h4>
                        {{ Html::ul($errors->all()) }}
                        <div class="form-group">
                            {{ Form::label('id', 'ID', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                {{ $data->id }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('unilever_id', 'Unilever ID', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                {{ $data->id_unilever_owner }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('name', 'Name', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                {{ $data->name }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('email', 'Email', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                {{ $data->email }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('phone', 'Phone', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                {{ $data->phone }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('address', 'Address', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                {{ $data->address }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('zip_code', 'Zip Code', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                {{ $data->zip_code }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('latitude', 'Latitude', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                {{ $data->latitude }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('longitude', 'Longitude', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                {{ $data->longitude }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('radius', 'Radius(Meter)', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                {{ $data->radius_default }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('radius_threshold', 'Radius Threshold(Meter)', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                {{ $data->radius_threshold }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('province', 'Province', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                {{ $data->province->name }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('city', 'City', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                {{ $data->city->name }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('district', 'District', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                {{ $data->district->name }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('village', 'Village', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                {{ $data->village->name }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('created_at', 'Created', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                {{ $data->created_at }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('created_by', 'Created By', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                {{ $data->creator->name }}
                            </div>
                        </div>
                        @if ($data->updater != null)
                            <div class="form-group">
                                {{ Form::label('updated_at', 'Updated', array('class' => 'col-2 col-form-label')) }}
                                <div class="col-10 form-control">
                                    {{ $data->updated_at }}
                                </div>
                            </div>
                            <div class="form-group">
                                {{ Form::label('updated_by', 'Updated By', array('class' => 'col-2 col-form-label')) }}
                                <div class="col-10 form-control">
                                    {{ $data->updater->name }}
                                </div>
                            </div>
                        @endif

                        <div class="form-group">
                            <div class="col-2"></div>
                            <div class="col-10">
                                <a class="btn btn-danger m-t-20" href="{{ route('juragan.index') }}">
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