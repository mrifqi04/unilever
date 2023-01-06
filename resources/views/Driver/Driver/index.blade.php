@extends('layouts.app')

@section('content')

    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card-box">
                        <ol class="breadcrumb">
                            <li>
                                Driver
                            </li>
                            <li class="active">
                                <a href="{{route('driver.index')}}">Driver Management</a>
                            </li>
                        </ol>
                        <h4 class="m-t-0 header-title"><b>Data</b></h4>
						
						<button class="btn btn-primary  d-none d-lg-block" type="button" data-toggle="collapse"
                                data-target="#filter_data" aria-expanded="false" aria-controls="collapseExample">
                            <i class="fa fa-arrow-circle-down"></i> Filter
                        </button>

                        @if(Auth::user()->getPermissionByName('driver.create'))
                            <a href="{{route('driver.create')}}">
                                <button type="button" class="btn btn-info d-none d-lg-block">
                                    <i class="fa fa-plus-circle"></i> Create New
                                </button>
                            </a>
                        @endif

                        @if(Auth::user()->getPermissionByName('driver.export'))
                            <a href="{{route('driver.export',['search' => old('search', app('request')->get('search'))])}}"
                               class="btn btn-info waves-effect waves-light">
                                Export Data
                            </a>
                        @endif

                        <div class="collapse" id="filter_data">
                            <div class="card-box">
                                <div class="card-view">
                                    {{ Form::open(array('url' => route('driver.index'), 'role' => 'form', 'method' => 'get')) }}
                                   
									<div class="form-group">
                                        {{ Form::label('search', 'Search', array('class' => 'col-2 col-form-label')) }}
                                        <div class="col-10">
                                            {{ Form::text('search', old('search', app('request')->get('search')), array('class' => 'form-control', 'placeholder'=>'62811111111, Unilever ID, Name, Email')) }}
                                        </div>
                                    </div>

                                    <div class="row m-t-10">
                                        <div class="form-group col-md-12">
                                            <button class="btn btn-success"><i class="fa fa-check"></i>
                                                Search
                                            </button>
                                            <a href="{{route('driver.index')}}"
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
                                <th>UNILEVER ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th colspan="3" class="text-center">Action</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach($datas as $data)
                                <tr>
                                    <td>{{$data->id_unilever}}</td>
                                    <td>{{$data->name}}</td>
                                    <td>{{$data->email}}</td>
                                    <td>{{$data->phone}}</td>
                                    <td>{{$data->status_active}}</td>
                                    <td>{{$data->created_at}}</td>
                                    <td class="text-center">
                                        {{ Form::open(array('url' => route('driver.destroy', ['driver'=>$data->id]))) }}

                                        @if(Auth::user()->getPermissionByName('driver.show'))
                                            <a href="{{route('driver.show', ['driver'=>$data->id])}}"
                                               class="btn waves-effect waves-light btn-rounded btn-sm btn-success">
                                                Show
                                            </a>
                                        @endif

                                        @if(Auth::user()->getPermissionByName('driver.edit'))
                                            <a href="{{route('driver.edit', ['driver'=>$data->id])}}"
                                               class="btn waves-effect waves-light btn-rounded btn-xs btn-info">
                                                Edit
                                            </a>
                                        @endif

                                        @if(Auth::user()->getPermissionByName('driver.destroy'))
                                            {{ Form::hidden('_method', 'DELETE') }}
                                            <button class="btn waves-effect waves-light btn-rounded btn-xs btn-danger"
                                                    type="submit">Delete
                                            </button>
                                        @endif

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

