@extends('layouts.app')
@section('content')

    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card-box">
                        <ol class="breadcrumb">
                            <li>
                                Outlet
                            </li>
                            <li>
                                <a href="{{route('outlet.index')}}">Outlet Management</a>
                            </li>
                            <li class="active">
                                Show
                            </li>
                        </ol>
                        <h4 class="m-t-0 header-title">
                            <b>Data Outlet</b>
                        </h4>
                        {{ Html::ul($errors->all()) }}
                        <div class="form-group">
                            {{ Form::label('id', 'SERU ID', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                {{ $data->id }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('unilever_id', 'Unilever ID', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                {{ $data->id_unilever }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('juragan', 'Juragan', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                {{ $data->juragan->name }}
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('type', 'Outet Status Type', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                {{ is_null($data->StatusType) ? '-' :  $data->StatusType->name }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('ownership', 'Ownership Status', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                {{ is_null($data->OwnershipStatus) ? '-' :  $data->OwnershipStatus->name }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('street', 'Street Type', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                {{ is_null($data->StreetType) ? '-' :  $data->StreetType->name }}
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('name', 'Name', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                {{ $data->name }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('owner', 'Outlet Owner', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                {{ $data->owner }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('phone', 'Phone', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                {{ $data->phone }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('phone2', 'Phone 2', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                {{ $data->phone2 }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('address', 'Address', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                {{ $data->address }}
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
                            {{ Form::label('province', 'Province', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                @if($data->id_province == "")
                                    -
                                @else
                                    {{ $data->province->name }}
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('city', 'City', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                @if($data->id_city == "")
                                    -
                                @else
                                    {{ $data->city->name }}
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('district', 'District', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                @if($data->id_district == "")
                                    -
                                @else
                                @if ($data->district)
                                    {{ $data->district->name }}
                                @endif
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('village', 'Village', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                @if($data->id_village == "")
                                    -
                                @else
                                    {{ $data->village->name }}
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('descriptions', 'Descriptions', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                {{ $data->descriptions }}
                            </div>
                        </div>

                        <div class="card-box">
                            <h4 class="m-t-0 header-title">
                                <b>Data Cabinet</b>
                            </h4>
                            <div class="card-view">
                                @foreach($data->cabinets as $cabinet)
                                    <div class="form-group">
                                        {{ Form::label('serialnumber', 'Serial Number', array('class' => 'col-2 col-form-label')) }}
                                        <div class="col-10 form-control">
                                            {{ $cabinet->serialnumber }}
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        {{ Form::label('qrcode', 'QR Code', array('class' => 'col-2 col-form-label')) }}
                                        <div class="col-10 form-control">
                                            {{ $cabinet->qrcode }}
                                        </div>
                                    </div>
                                @endforeach

                            </div>
                        </div>


                        <div class="form-group">
                            <div class="col-2"></div>
                            <div class="col-10">
                                <a class="btn btn-danger m-t-20" href="{{ route('outlet.index') }}">
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