@extends('layouts.app')

@section('content')

    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card-box">
                        <ol class="breadcrumb">
                            <li>
                                Unilever
                            </li>
                            <li class="active">
                                <a href="{{route('unilever.import-outlet')}}">Upload Outlet</a>
                            </li>
                        </ol>
                        <h4 class="m-t-0 header-title"><b>Data</b></h4>

                        <button class="btn btn-primary  d-none d-lg-block" type="button" data-toggle="collapse"
                                data-target="#filter_data" aria-expanded="false" aria-controls="collapseExample">
                            <i class="fa fa-arrow-circle-down"></i> Upload Form
                        </button>

                        @if(Auth::user()->getPermissionByName('unilever.import-outlet'))
                            <div class="collapse" id="filter_data">
                                <div class="card-box">
                                    <div class="card-view">
                                        {{ Form::open(array('url' => route('unilever.doImport-outlet'), 'role' => 'form','files'=>'true')) }}

                                        <div class="form-group">
                                            {{ Form::label('import_file', 'File', array('class' => 'col-2 col-form-label')) }}
                                            <div class="col-10">
                                                {{ Form::file('import_file') }}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="col-10">
                                                <button class="btn btn-success"><i class="fa fa-check"></i> Submit
                                                </button>
                                            </div>
                                        </div>
                                        {{ Form::close() }}
                                    </div>
                                </div>
                            </div>
                        @endif

                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>File Name</th>
                                <th>Created</th>
                                <th>Updated</th>
                                <th>Status</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach($data as $job)
                                <tr>
                                    <td>{{$job->file_name}}</td>
                                    <td>{{$job->created_at}}</td>
                                    <td>{{$job->updated_at}}</td>
                                    <td>
                                        @if ($job->status->id === 1)
                                            <button type="button"
                                                    class="btn btn-info btn-sm btn-custom btn-rounded waves-effect waves-light">
                                                {{$job->status->name}}
                                            </button>
                                        @else
                                            <button type="button"
                                                    class="btn btn-success btn-sm btn-custom btn-rounded waves-effect waves-light">
                                                {{$job->status->name}}
                                            </button>
                                        @endif

                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        {{ $data->links("pagination::bootstrap-4") }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@include('layouts.alert')