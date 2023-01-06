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
                            <li class="active">
                                <a href="{{route('user.index')}}">User</a>
                            </li>
                        </ol>
                        <h4 class="m-t-0 header-title"><b>Data</b></h4>

                        <button class="btn btn-primary  d-none d-lg-block" type="button" data-toggle="collapse" data-target="#filter_data" aria-expanded="false" aria-controls="collapseExample">
                            <i class="fa fa-arrow-circle-down"></i> Filter
                        </button>

                        @if(Auth::user()->getPermissionByName('user.create'))
                        <a href="{{route('user.create')}}">
                            <button type="button" class="btn btn-info d-none d-lg-block">
                                <i class="fa fa-plus-circle"></i> Create New
                            </button>
                        </a>
                        @endif

                        <div class="collapse" id="filter_data">
                            <div class="card-box">
                                <div class="card-view">
                                    {{ Form::open(array('url' => route('user.index'), 'role' => 'form', 'method' => 'get')) }}
                                    <div class="form-group">
                                        {{ Form::label('username', 'Username', array('class' => 'col-2 col-form-label')) }}
                                        <div class="col-10">
                                            {{ Form::text('username', old('name', app('request')->get('username')), array('class' => 'form-control', 'placeholder'=>'Username@email.com')) }}
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        {{ Form::label('phone', 'Phone', array('class' => 'col-2 col-form-label')) }}
                                        <div class="col-10">
                                            {{ Form::text('phone', old('name', app('request')->get('phone')), array('class' => 'form-control', 'placeholder'=>'628111111111')) }}
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-10">
                                            <button type="submit" class="btn btn-success"><i class="fa fa-check"></i> Search</button>
                                        </div>
                                    </div>
                                    {{ Form::close() }}
                                </div>
                            </div>
                        </div>


                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>Username</th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Role</th>
                                <th>Created</th>
                                <th colspan="3" class="text-center">Action</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach($datas as $data)
                                <tr>
                                    <td>{{$data->username}}</td>
                                    <td>{{$data->name}}</td>
                                    <td>{{$data->phone}}</td>
                                    <td>{{$data->roles[0]->name}}</td>
                                    <td>{{$data->created_at}}</td>
                                    <td class="text-center">
                                        {{ Form::open(array('url' => route('user.destroy', ['user'=>$data->id]))) }}

                                        @if(Auth::user()->getPermissionByName('user.show'))
                                        <a href="{{route('user.show', ['user'=>$data->id])}}" class="btn waves-effect waves-light btn-rounded btn-md btn-success ion-search">
                                            {{--Show--}}
                                        </a>
                                        @endif

                                        @if(Auth::user()->getPermissionByName('user.edit'))
                                        <a href="{{route('user.edit', ['user'=>$data->id])}}" class="btn waves-effect waves-light btn-rounded btn-md btn-info ion-edit">
                                            {{--Edit--}}
                                        </a>
                                        @endif

                                        @if(Auth::user()->getPermissionByName('user.resetpassword'))
                                        <a href="{{route('user.resetpassword', ['id'=>$data->id])}}" class="btn waves-effect waves-light btn-rounded btn-md btn-warning ion-unlocked">
                                            {{--Reset Password--}}
                                        </a>
                                        @endif

                                        @if(Auth::user()->getPermissionByName('user.destroy'))
                                            {{ Form::hidden('_method', 'DELETE') }}
                                            <button class="btn waves-effect waves-light btn-rounded btn-md btn-danger ion-close" type="submit">
                                                {{--Delete--}}
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