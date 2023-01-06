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
                            <li class="active">
                                <a href="{{route('outlet.index')}}">Outlet Management</a>
                            </li>
                        </ol>
                        <h4 class="m-t-0 header-title"><b>Data</b></h4>

                        <button class="btn btn-primary  d-none d-lg-block" type="button" data-toggle="collapse"
                                data-target="#filter_data" aria-expanded="false" aria-controls="collapseExample">
                            <i class="fa fa-arrow-circle-down"></i> Filter
                        </button>

                        @if(Auth::user()->getPermissionByName('outlet.create'))
                            <a href="{{route('outlet.create')}}">
                                <button type="button" class="btn btn-info d-none d-lg-block">
                                    <i class="fa fa-plus-circle"></i> Create New
                                </button>
                            </a>
                        @endif

                        @if(Auth::user()->getPermissionByName('outlet.export'))
                            <a href="{{route('outlet.export',['search' => old('search', app('request')->get('search'))])}}"
                               class="btn btn-info waves-effect waves-light">
                                Export Data
                            </a>
                        @endif
                        
                        @if(Auth::user()->getPermissionByName('outlet.export-csdp'))
                        <a href="#modal-export-csdp" id="btn-export-csdp" data-animation="fadein" data-plugin="custommodal" data-overlayspeed="200" data-overlaycolor="#36404a" class="btn btn-info waves-effect waves-light">
                            Download Data CSDP
                        </a>
                        <!-- Modal outlet.export-csdp-->
                        <div id="modal-export-csdp" class="modal-demo">
                            <button type="button" class="close" onclick="Custombox.close();">
                                <span>&times;</span><span class="sr-only">Close</span>
                            </button>
                            <h4 class="custom-modal-title">Download Data CSDP</h4>
                            <div class="modal-body">
                                <div class="container-fluid">
                                    <form method="get" action="{{route('outlet.export-csdp')}}" role="form" class="form-horizontal">
                                        <div class="form-group">
                                            <label for="id_juragan" class="col-sm-2 control-label">Juragan</label>
                                            <div class="col-sm-5">
                                                {{ Form::select('juraganId', [], null, ['id'=>'id_juragan', 'class' => 'form-control']) }}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="fromDate" class="col-sm-2 control-label">Tanggal Submit Outlet</label>
                                            <div class="col-sm-5">
                                                <input class="form-control" name="fromDate" id="fromDate" value="" placeholder="yyyy-mm-dd" required="required">
                                            </div>
                                            <div class="col-sm-5">
                                                <input class="form-control" name="toDate" id="toDate" value="" placeholder="yyyy-mm-dd" required="required">
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


                        <button class="btn btn-info waves-effect waves-light" type="button" data-toggle="collapse" data-target="#import_csdp" aria-expanded="false" aria-controls="collapseExample">
                            <i class="fa fa-upload"></i> Import CSDP
                        </button>

                        @if(Auth::user()->getPermissionByName('outlet.doImport-csdp'))
                        <div class="collapse" id="import_csdp">
                            <div class="card-box">
                                <div class="card-view">
                                    {{ Form::open(array('url' => route('outlet.doImport-csdp'), 'role' => 'form','files'=>'true')) }}
                                    {{ Form::label('import_file_csdp', 'Import CSDP', array('class' => 'col-2 col-form-label')) }}
                                    <div class="row">
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                {{ Form::file('import_file_csdp', array('class'=>"filestyle", 'data-iconname'=>"fa fa-cloud-upload")) }}
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-success"><i class="fa fa-check"></i> Submit
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    {{ Form::close() }}
                                </div>
                            </div>
                        </div>
                        @endif


                        <div class="collapse" id="filter_data">
                            <div class="card-box">
                                <div class="card-view">
                                    {{ Form::open(array('url' => route('outlet.index'), 'role' => 'form', 'method' => 'get')) }}
                                    <div class="form-group">
                                        {{ Form::label('search', 'Search', array('class' => 'col-2 col-form-label')) }}
                                        <div class="col-10">
                                            {{ Form::text('search', old('search', app('request')->get('search')), array('class' => 'form-control', 'placeholder'=>'Unilever ID, Juragan, Owner, or Phone')) }}
                                        </div>
                                    </div>

                                    <div class="row m-t-10">
                                        <div class="form-group col-md-12">
                                            <button class="btn btn-success"><i class="fa fa-check"></i>
                                                Search
                                            </button>
                                            <a href="{{route('outlet.index')}}"
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
                                <th>SERU ID</th>
                                <th>CSDP</th>
                                <th>UNILEVER ID</th>
                                <th>Juragan</th>
                                <th>Owner</th>
                                <th>Phone</th>
                                <th>Created</th>
                                <th colspan="3" class="text-center">Action</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach($datas as $data)
                                <tr>
                                    <td>{{$data->id}}</td>
                                    <td>{{$data->csdp}}</td>
                                    <td>{{$data->id_unilever}}</td>
                                    <td>{{$data->juragan->name}}</td>
                                    <td>{{$data->owner}}</td>
                                    <td>{{$data->phone}}</td>
                                    <td>{{$data->created}}</td>
                                    <td class="text-center">
                                        {{ Form::open(array('url' => route('outlet.destroy', ['outlet'=>$data->id]))) }}

                                        @if(Auth::user()->getPermissionByName('outlet.show'))
                                            <a href="{{route('outlet.show', ['outlet'=>$data->id])}}"
                                               class="btn waves-effect waves-light btn-rounded btn-sm btn-success ion-search">

                                            </a>
                                        @endif

                                        @if(Auth::user()->getPermissionByName('outlet.edit'))
                                            <a href="{{route('outlet.edit', ['outlet'=>$data->id])}}"
                                               class="btn waves-effect waves-light btn-rounded btn-xs btn-info ion-edit">

                                            </a>
                                        @endif

                                        @if(Auth::user()->getPermissionByName('outlet.destroy'))
                                            {{ Form::hidden('_method', 'DELETE') }}
                                            <button class="btn waves-effect waves-light btn-rounded btn-xs btn-danger ion-close"
                                                    type="submit">

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

@push('styles')
    <link href="{{asset('assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css')}}" rel="stylesheet"
          type="text/css">
    <link href="{{asset('assets/plugins/custombox/css/custombox.css')}}" rel="stylesheet">
    <link href="{{asset('assets/plugins/select2/css/select2.css')}}" rel="stylesheet" type="text/css">
@endpush

@push('scripts')
    <!-- Modal-Effect -->
    <script src="{{asset('assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>
    <script src="{{asset('assets/plugins/custombox/js/custombox.min.js')}}"></script>
    <script src="{{asset('assets/plugins/custombox/js/legacy.min.js')}}"></script>
    <script src="{{asset('assets/plugins/select2/js/select2.min.js')}}"></script>
    <script src="{{asset('assets/plugins/bootstrap-filestyle/js/bootstrap-filestyle.min.js')}}" type="text/javascript"></script>
@endpush

@push('scripts')
    <script type="text/javascript">
        $(document).ready(function () {
            let selectJuragan = $("select#id_juragan");
            const errorCallback = function (jqXHR) {
                $.Notification.autoHideNotify('error', 'top right', 'Error Load Data', jqXHR.responseJSON.message);
            };
            $.fn.modal.Constructor.prototype.enforceFocus = function() {

            };
            $("#id_juragan").select2({
                dropdownParent: $('#modal-export-csdp')
            });
            let url = "{{route('juragan.list')}}";
            $.ajax({
                method: 'GET',
                url: url,
                contentType: 'application/json',
                process: false,
            }).done(function (data) {
                console.log(selectJuragan);
                let o = new Option("==== Semua Juragan ====", '');
                $("#id_juragan").append(o);
                $.each(data, function (key, val) {
                    let o = new Option(val.name, val.id);
                    $("#id_juragan").append(o);
                });

            }).fail(function (jqXHR, textStatus, errorThrown) {
                errorCallback(jqXHR);
            });
            $("#fromDate,#toDate").datepicker({"format": "yyyy-mm-dd"});
            $("#fromDate,#toDate").datepicker('update', new Date());
        })
    </script>
@endpush

@include('layouts.alert')