@extends('layouts.app')

@section('content')

    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card-box">
                        <ol class="breadcrumb">
                            <li>
                                Vehicle
                            </li>
                            <li class="active">
                                <a href="{{route('vehicle.index')}}">Vehicle Management</a>
                            </li>
                        </ol>
                        <h4 class="m-t-0 header-title"><b>Data</b></h4>
						
						<button class="btn btn-primary  d-none d-lg-block" type="button" data-toggle="collapse"
                                data-target="#filter_data" aria-expanded="false" aria-controls="collapseExample">
                            <i class="fa fa-arrow-circle-down"></i> Filter
                        </button>

                        @if(Auth::user()->getPermissionByName('vehicle.create'))
                        <a href="{{route('vehicle.create')}}">
                            <button type="button" class="btn btn-info d-none d-lg-block">
                                <i class="fa fa-plus-circle"></i> Create New
                            </button>
                        </a>
                        @endif

                        @if(Auth::user()->getPermissionByName('vehicle.export'))
                            <a href="{{route('vehicle.export',['search' => old('license_number', app('request')->get('license_number'))])}}"
                               class="btn btn-info waves-effect waves-light">
                                Export Data
                            </a>
                        @endif
						
						<div class="collapse" id="filter_data">
                            <div class="card-box">
                                <div class="card-view">
                                    {{ Form::open(array('url' => route('vehicle.index'), 'role' => 'form', 'method' => 'get')) }}
                                    <div class="form-group">
                                        {{ Form::label('license_number', 'Plate Number', array('class' => 'col-2 col-form-label')) }}
                                        <div class="col-10">
                                            {{ Form::text('license_number', old('license_number', app('request')->get('license_number')), array('class' => 'form-control', 'placeholder'=>'Plate Number')) }}
                                        </div>
                                    </div>

                                    <div class="row m-t-10">
                                        <div class="form-group col-md-12">
                                            <button class="btn btn-success"><i class="fa fa-check"></i>
                                                Search
                                            </button>
                                            <a href="{{route('vehicle.index')}}"
                                               class="btn btn-white">Clear</a>
                                        </div>
                                    </div>
                                    {{ Form::close() }}
                                </div>
                            </div>
                        </div>

                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>Number Plate</th>
                                <th>Created</th>
                                <th colspan="3" class="text-center">Action</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach($datas as $data)
                                <tr>
                                    <td>{{$data->license_number}}</td>
                                    <td>{{$data->created_at}}</td>
                                    <td class="text-center">
                                        {{ Form::open(array('url' => route('vehicle.destroy', ['vehicle'=>$data->id]))) }}

                                        {{--                                        @if(Auth::user()->getPermissionByName('vehicle.show'))--}}
                                        <a href="{{route('vehicle.show', ['vehicle'=>$data->id])}}"
                                           class="btn waves-effect waves-light btn-rounded btn-sm btn-success">
                                            Show
                                        </a>
                                        {{--@endif--}}

                                        {{--                                        @if(Auth::user()->getPermissionByName('vehicle.edit'))--}}
                                        <a href="{{route('vehicle.edit', ['vehicle'=>$data->id])}}"
                                           class="btn waves-effect waves-light btn-rounded btn-xs btn-info">
                                            Edit
                                        </a>
                                        {{--@endif--}}

                                        {{--                                        @if(Auth::user()->getPermissionByName('vehicle.destroy'))--}}
                                        {{ Form::hidden('_method', 'DELETE') }}
                                        <button class="btn waves-effect waves-light btn-rounded btn-xs btn-danger"
                                                type="submit">Delete
                                        </button>
                                        {{--@endif--}}

                                        {{ Form::close() }}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        {{ $datas->links("pagination::bootstrap-4") }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@include('layouts.alert')