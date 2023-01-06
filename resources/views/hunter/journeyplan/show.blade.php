@extends('layouts.app')
@section('content')

    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card-box">
                        <ol class="breadcrumb">
                            <li>
                                Journey Plan
                            </li>
                            <li>
                                <a href="{{route('journeyplan.index')}}">Journey Plan Management</a>
                            </li>
                            <li class="active">
                                Show
                            </li>
                        </ol>
                        <h4 class="m-t-0 header-title">
                            <b>Data Journey Plan</b>
                        </h4>
                        {{ Html::ul($errors->all()) }}
                        <div class="form-group">
                            {{ Form::label('id', 'ID', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                {{ $journeyplan->id }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('name', 'Name', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                {{ $journeyplan->name }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('id_juragan', 'Juragan', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                {{ !is_null($journeyplan->juragan) ? $journeyplan->juragan->name : '' }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('assign_to', 'Assign To', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                {{ !is_null($journeyplan->assignTo) ? $journeyplan->assignTo->name : '' }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('province', 'Province', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                {{ !is_null($journeyplan->province) ? $journeyplan->province->name : '' }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('city', 'City', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                {{ !is_null($journeyplan->city) ? $journeyplan->city->name : '' }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('district', 'District', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                {{ !is_null($journeyplan->district) ? $journeyplan->district->name : '' }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('village', 'Village', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                {{!is_null($journeyplan->village) ? $journeyplan->village->name : '' }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('start_date', 'Start Date', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                {{ \Carbon\Carbon::parse($journeyplan->start_date)->format('Y-m-d') }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('end_date', 'End Date', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                {{ \Carbon\Carbon::parse($journeyplan->end_date)->format('Y-m-d') }}
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-2"></div>
                            <div class="col-10">
                                <a class="btn btn-danger m-t-20" href="{{ route('journeyplan.index') }}">
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