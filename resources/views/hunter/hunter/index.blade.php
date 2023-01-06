@extends('layouts.app')

@section('content')
    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card-box">
                        <ol class="breadcrumb">
                            <li>
                                Hunter
                            </li>
                            <li class="active">
                                <a href="{{route('hunter.index')}}">Hunter Management</a>
                            </li>
                        </ol>
                        <h4 class="m-t-0 header-title"><b>Data</b></h4>

                        <button class="btn btn-primary  d-none d-lg-block" type="button" data-toggle="collapse"
                                data-target="#filter_data" aria-expanded="{{$in_search === true ? 'true' : 'false'}}"
                                aria-controls="collapseExample">
                            <i class="fa fa-arrow-circle-down"></i> Filter
                        </button>

                        @if(Auth::user()->getPermissionByName('hunter.create'))
                            <a href="{{route('hunter.create')}}">
                                <button type="button" class="btn btn-info d-none d-lg-block">
                                    <i class="fa fa-plus-circle"></i> Create New
                                </button>
                            </a>
                        @endif

                        @if(Auth::user()->getPermissionByName('hunter.export'))
                            <a href="{{route('hunter.export',['search' => old('search', app('request')->get('search'))])}}"
                               class="btn btn-info waves-effect waves-light">
                                Export Data
                            </a>
                        @endif

                        @if(Auth::user()->getPermissionByName('hunter.export-survey'))
                            <a href="#modal-export-survey"
                               data-animation="fadein" data-plugin="custommodal"
                               data-overlayspeed="200" data-overlaycolor="#36404a"
                               class="btn btn-info waves-effect waves-light">
                                Export Data Survey
                            </a>
                            <!-- Modal -->
                            <div id="modal-export-survey" class="modal-demo">
                                <button type="button" class="close" onclick="Custombox.close();">
                                    <span>&times;</span><span class="sr-only">Close</span>
                                </button>
                                <h4 class="custom-modal-title">Export Data Survey</h4>
                                <div class="modal-body">
                                    <div class="container-fluid">
                                        <form method="get" action="{{route('hunter.export-survey')}}" role="form"
                                              class="form-horizontal">
                                            <div class="form-group">
                                                <label for="fromDate" class="col-sm-2 control-label">Tanggal</label>
                                                <div class="col-sm-5">
                                                    <input class="form-control" name="fromDate" id="fromDate"
                                                           value=""
                                                           placeholder="yyyy-mm-dd">
                                                </div>
                                                <div class="col-sm-5">
                                                    <input class="form-control" name="toDate" id="toDate"
                                                           value=""
                                                           placeholder="yyyy-mm-dd">
                                                </div>
                                            </div>
                                            <div class="form-group m-b-0">
                                                <div class="col-sm-12">
                                                    <button class="btn btn-info waves-effect waves-light pull-right">
                                                        <i class="fa fa-search"></i>&nbsp;Export
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="m-t-20">
                            <div class="collapse {{$in_search === true ? 'in' : ''}}"
                                 aria-expanded="{{$in_search === true ? 'true' : 'false'}}" id="filter_data">
                                <div class="card-box">
                                    <div class="card-view">
                                        {{ Form::open(['url' => route('hunter.index'), 'role' => 'form', 'method' => 'get']) }}
                                        <div class="row">
                                            <div class="form-group">
                                                {{ Form::label('search', 'Search', array('class' => 'col-2 col-form-label')) }}
                                                <div class="col-10">
                                                    {{ Form::text('search', old('search', app('request')->get('search')), array('class' => 'form-control', 'placeholder'=>'62811111111, Unilever ID, Name, Email')) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row m-t-10">
                                            <div class="form-group col-md-12">
                                                <button class="btn btn-success"><i class="fa fa-check"></i>
                                                    Search
                                                </button>
                                                <a href="{{route('hunter.index')}}"
                                                   class="btn btn-white">Clear</a>
                                            </div>
                                        </div>
                                        {{ Form::close() }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>Hunter ID</th>
                                <th>Unilever ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Created</th>
                                <th colspan="3" class="text-center">Action</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach($hunters as $hunter)
                                <tr>
                                    <td>{{$hunter->id}}</td>
                                    <td>{{$hunter->id_unilever}}</td>
                                    <td>{{$hunter->name}}</td>
                                    <td>{{$hunter->email}}</td>
                                    <td>{{$hunter->phone}}</td>
                                    <td>{{$hunter->created_at}}</td>
                                    <td class="text-center">
                                        {{ Form::open(array('url' => route('hunter.destroy', ['hunter'=>$hunter->id]))) }}
                                        @if($can_show)
                                            <a href="{{route('hunter.show', ['hunter'=>$hunter->id])}}"
                                               class="btn btn-success waves-effect waves-light btn-rounded btn-xs">
                                                Show
                                            </a>
                                        @endif
                                        @if($can_edit)
                                            <a href="{{route('hunter.edit', ['hunter'=>$hunter->id])}}"
                                               class="btn btn-info waves-effect waves-light btn-rounded btn-xs">
                                                Edit
                                            </a>
                                        @endif
                                        @if($can_destroy)
                                            {{ Form::hidden('_method', 'DELETE') }}
                                            <button class="btn btn-danger waves-effect waves-light btn-rounded btn-xs">
                                                Delete
                                            </button>
                                        @endif
                                        {{ Form::close() }}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        {{ $hunters->links("pagination::bootstrap-4") }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@include('layouts.alert')

@push('styles')
    <link href="{{asset('assets/plugins/custombox/css/custombox.css')}}" rel="stylesheet">
@endpush

@push('scripts')
    <!-- Modal-Effect -->
    <script src="{{asset('assets/plugins/custombox/js/custombox.min.js')}}"></script>
    <script src="{{asset('assets/plugins/custombox/js/legacy.min.js')}}"></script>
@endpush

@push('styles')
    <link href="{{asset('assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css')}}" rel="stylesheet"
          type="text/css">
@endpush

@push('scripts')
    <script src="{{asset('assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $("#fromDate,#toDate").datepicker({"format": "yyyy-mm-dd"});
            $("#fromDate,#toDate").datepicker('update', new Date());
        })
    </script>
@endpush