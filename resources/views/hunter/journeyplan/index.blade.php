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
                            <li class="active">
                                <a href="{{route('journeyplan.index')}}">Hunter Management</a>
                            </li>
                        </ol>
                        <h4 class="m-t-0 header-title"><b>Data</b></h4>

                        <button class="btn btn-primary  d-none d-lg-block" type="button" data-toggle="collapse"
                                data-target="#filter_data" aria-expanded="false" aria-controls="collapseExample">
                            <i class="fa fa-arrow-circle-down"></i> Filter
                        </button>

                        @if(Auth::user()->getPermissionByName('journeyplan.create'))
                            <a href="{{route('journeyplan.create')}}">
                                <button type="button" class="btn btn-info d-none d-lg-block">
                                    <i class="fa fa-plus-circle"></i> Create New
                                </button>
                            </a>
                        @endif

                        <div class="collapse" id="filter_data">
                            <div class="card-box">
                                <div class="card-view">
                                    {{ Form::open(array('url' => route('journeyplan.index'), 'role' => 'form', 'method' => 'get', 'id'=>'form-filter')) }}

                                    <div class="form-group">
                                        <div class="form-group">
                                            {{ Form::label('search', 'Search', array('class' => 'col-2 col-form-label')) }}
                                            <div class="col-10">
                                                {{ Form::text('search', old('search', app('request')->get('search')), array('class' => 'form-control', 'placeholder'=>'ID, Name, Juragan, Assign To')) }}
                                            </div>
                                        </div>
                                        <div class="col-10">
                                            <button type="submit" class="btn btn-success"><i class="fa fa-check"></i>
                                                Search
                                            </button>
                                        </div>
                                    </div>
                                    {{ Form::close() }}
                                </div>
                            </div>
                        </div>

                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Juragan</th>
                                <th>Start</th>
                                <th>End</th>
                                <th>Assign To</th>
                                <th colspan="3" class="text-center">Action</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach($journeyplans as $journeyplan)
                                <tr>
                                    <td>{{$journeyplan->id}}</td>
                                    <td>{{$journeyplan->name}}</td>
                                    <td>{{!is_null($journeyplan->juragan) ? $journeyplan->juragan->name : ''}}</td>
                                    <td>{{\Carbon\Carbon::parse($journeyplan->start_date)->format('Y-m-d')}}</td>
                                    <td>{{\Carbon\Carbon::parse($journeyplan->end_date)->format('Y-m-d')}}</td>
                                    <td>{{!is_null($journeyplan->assignTo) ? $journeyplan->assignTo->name :''}}</td>
                                    <td class="text-center">
                                        {{ Form::open(array('url' => route('journeyplan.destroy', ['journeyplan'=>$journeyplan->id]))) }}

                                        @if($can_show)
                                            <a href="{{route('journeyplan.show', ['journeyplan'=>$journeyplan->id])}}"
                                               class="btn waves-effect waves-light btn-rounded btn-sm btn-success">
                                                Show
                                            </a>
                                        @endif

                                        @if($can_edit)
                                            <a href="{{route('journeyplan.edit', ['journeyplan'=>$journeyplan->id])}}"
                                               class="btn waves-effect waves-light btn-rounded btn-xs btn-info">
                                                Edit
                                            </a>
                                        @endif

                                        @if($can_destroy)
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
                        {{ $journeyplans->links("pagination::bootstrap-4") }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@include('layouts.alert')