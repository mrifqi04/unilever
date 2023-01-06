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
                                <a href="{{route('permission.index')}}">Permission</a>
                            </li>
                        </ol>
                        <h4 class="m-t-0 header-title"><b>Data</b></h4>

                        @if(Auth::user()->getPermissionByName('permission.create'))
                        <a href="{{route('permission.create')}}">
                            <button type="button" class="btn btn-info d-none d-lg-block">
                                <i class="fa fa-plus-circle"></i> Create New
                            </button>
                        </a>
                        @endif

                        <table class="table table-hover">
                            <thead>
                            <tr>
                                {{--<th>ID</th>--}}
                                <th>Caption</th>
                                <th>Route Name</th>
                                <th>Created</th>
                                <th>Updated</th>
                                <th colspan="3" class="text-center">Action</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach($datas as $data)
                                <tr>
                                    {{--<td>{{$data->id}}</td>--}}
                                    <td>{{$data->caption}}</td>
                                    <td>{{$data->name}}</td>
                                    <td>{{$data->created_at}}</td>
                                    <td>{{$data->updated_at}}</td>
                                    <td class="text-center">
                                        {{ Form::open(array('url' => route('permission.destroy', ['permission'=>$data->id]))) }}

                                        @if(Auth::user()->getPermissionByName('permission.show'))
                                        <a href="{{route('permission.show', ['permission'=>$data->id])}}" class="btn waves-effect waves-light btn-rounded btn-sm btn-success ion-search">

                                        </a>
                                        @endif

                                        @if(Auth::user()->getPermissionByName('permission.edit'))
                                        <a href="{{route('permission.edit', ['permission'=>$data->id])}}" class="btn waves-effect waves-light btn-rounded btn-xs btn-info ion-edit">

                                        </a>
                                        @endif

                                        @if(Auth::user()->getPermissionByName('permission.destroy'))
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
        </div>
    </div>

@endsection

@include('layouts.alert')