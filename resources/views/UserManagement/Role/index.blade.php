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
                                <a href="{{route('role.index')}}">Role</a>
                            </li>
                        </ol>
                        <h4 class="m-t-0 header-title"><b>Data</b></h4>

                        @if(Auth::user()->getPermissionByName('role.create'))
                        <a href="{{route('role.create')}}">
                            <button type="button" class="btn btn-info d-none d-lg-block">
                                <i class="fa fa-plus-circle"></i> Create New
                            </button>
                        </a>
                        @endif

                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nama</th>
                                    <th>Created</th>
                                    <th>Updated</th>
                                    <th colspan="3" class="text-center">Action</th>
                                </tr>
                            </thead>

                            <tbody>
                            @foreach($datas as $data)
                                <tr>
                                    <td>{{$data->id}}</td>
                                    <td>{{$data->name}}</td>
                                    <td>{{$data->created_at}}</td>
                                    <td>{{$data->updated_at}}</td>
                                    <td class="text-center">
                                        {{ Form::open(array('url' => route('role.destroy', ['role'=>$data->id]))) }}

                                        @if(Auth::user()->getPermissionByName('role.show'))
                                        <a href="{{route('role.show', ['role'=>$data->id])}}" class="btn waves-effect waves-light btn-rounded btn-sm btn-success ion-search">

                                        </a>
                                        @endif

                                        @if(Auth::user()->getPermissionByName('role.permissions'))
                                        <a href="{{route('role.permissions', ['id'=>$data->id])}}" class="btn waves-effect waves-light btn-rounded btn-xs btn-info ion-unlocked">
                                            View Permission
                                        </a>
                                        @endif

                                        @if(Auth::user()->getPermissionByName('role.edit'))
                                        <a href="{{route('role.edit', ['role'=>$data->id])}}" class="btn waves-effect waves-light btn-rounded btn-xs btn-info ion-edit">

                                        </a>
                                        @endif

                                        @if(Auth::user()->getPermissionByName('role.destroy'))
                                        {{ Form::hidden('_method', 'DELETE') }}
                                        <button class="btn waves-effect waves-light btn-rounded btn-xs btn-danger ion-close" type="submit">

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
        </div> <!-- container -->
    </div> <!-- content -->
@endsection