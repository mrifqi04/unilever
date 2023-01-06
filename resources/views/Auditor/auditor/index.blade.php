@extends('layouts.app')

@section('css')
<style type="text/css">
    .preloader {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 9999;
        background-color: rgba(255, 2555, 255, 0.5);
    }

    .loading {
        position: absolute;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
        font: 14px arial;
    }

    .lds-roller {
        display: inline-block;
        position: relative;
        width: 80px;
        height: 80px;
    }

    .lds-roller div {
        animation: lds-roller 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite;
        transform-origin: 40px 40px;
    }

    .lds-roller div:after {
        content: " ";
        display: block;
        position: absolute;
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #000;
        margin: -4px 0 0 -4px;
    }

    .lds-roller div:nth-child(1) {
        animation-delay: -0.036s;
    }

    .lds-roller div:nth-child(1):after {
        top: 63px;
        left: 63px;
    }

    .lds-roller div:nth-child(2) {
        animation-delay: -0.072s;
    }

    .lds-roller div:nth-child(2):after {
        top: 68px;
        left: 56px;
    }

    .lds-roller div:nth-child(3) {
        animation-delay: -0.108s;
    }

    .lds-roller div:nth-child(3):after {
        top: 71px;
        left: 48px;
    }

    .lds-roller div:nth-child(4) {
        animation-delay: -0.144s;
    }

    .lds-roller div:nth-child(4):after {
        top: 72px;
        left: 40px;
    }

    .lds-roller div:nth-child(5) {
        animation-delay: -0.18s;
    }

    .lds-roller div:nth-child(5):after {
        top: 71px;
        left: 32px;
    }

    .lds-roller div:nth-child(6) {
        animation-delay: -0.216s;
    }

    .lds-roller div:nth-child(6):after {
        top: 68px;
        left: 24px;
    }

    .lds-roller div:nth-child(7) {
        animation-delay: -0.252s;
    }

    .lds-roller div:nth-child(7):after {
        top: 63px;
        left: 17px;
    }

    .lds-roller div:nth-child(8) {
        animation-delay: -0.288s;
    }

    .lds-roller div:nth-child(8):after {
        top: 56px;
        left: 12px;
    }

    @keyframes lds-roller {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }
</style>
@endsection

@section('content')
<div class="preloader hidden" id="loading_screen">
    <div class="loading">
        <div class="lds-roller">
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
        </div>
    </div>
</div>
<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="card-box">
                    <ol class="breadcrumb">
                        <li>
                            Auditor
                        </li>
                        <li class="active">
                            <a href="{{ route('auditor.index') }}">Auditor Management</a>
                        </li>
                    </ol>
                    <h4 class="m-t-0 header-title"><b>Data</b></h4>

                    <div class="row">
                        <div class="col-md-6">
                            <button class="btn btn-primary  d-none d-lg-block" type="button" data-toggle="collapse" data-target="#filter_data" aria-expanded="false" aria-controls="collapseExample">
                                <i class="fa fa-arrow-circle-down"></i> Filter
                            </button>

                            @if (Auth::user()->getPermissionByName('auditor.create'))
                            <a href="{{ route('auditor.create') }}">
                                <button type="button" class="btn btn-info d-none d-lg-block">
                                    <i class="fa fa-plus-circle"></i> Create New
                                </button>
                            </a>
                            @endif

                            @if (Auth::user()->getPermissionByName('auditor.export'))
                            <a href="{{ route('auditor.export', ['search' => old('search', app('request')->get('search'))]) }}" class="btn btn-info waves-effect waves-light">
                                Export Data
                            </a>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <div class="pull-right">
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#upload_data_auditor_management">
                                    <i class="fa fa-arrow-circle-down"></i> Upload Data
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="upload_data_auditor_management" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-pjp-upload" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                    <h4 class="modal-title" id="auditPlanLookupModalLabel">Upload Data Auditor</h4>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-5">
                                        <button class="btn btn-primary  d-none d-lg-block" type="button" data-toggle="collapse" data-target="#upload_juragan_data" aria-expanded="false" aria-controls="collapseExample">
                                            <i class="fa fa-arrow-circle-down"></i> Upload Form
                                        </button>

                                        <a class="btn btn-success d-none d-lg-block" href="{{ asset('assets/templates/auditor-management-template.csv') }}" download>
                                            <i class="fa fa-download"></i> Download Template
                                        </a>

                                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#list_provinces_cities_modal" id="button_provinces_cities_modal">
                                            List Provinces & Cities
                                        </button>
                                    </div>

                                    @if ($can_destroy)
                                    <div class="collapse" id="upload_juragan_data">
                                        <div class="card-box">
                                            <div class="card-view">
                                                {{ Form::open(['url' => route('auditor.auditor.import'), 'role' => 'form', 'files' => 'true', 'method' => 'post']) }}
                                                {{ Form::label('import_file_auditor', 'Import Auditor', ['class' => 'col-2 col-form-label']) }}
                                                <div class="row">
                                                    <div class="col-md-5">
                                                        <div class="form-group">
                                                            {{ Form::file('import_file_auditor', ['class' => 'filestyle', 'data-iconname' => 'fa fa-cloud-upload']) }}
                                                        </div>
                                                    </div>

                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <button type="submit" class="btn btn-success" id="submit_import_button"><i class="fa fa-check"></i>
                                                                Submit
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                                {{ Form::close() }}
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    <hr>
                                    <table class="table table-hover m-t-30" id="table-pjp-file">
                                        <thead>
                                            <tr>
                                                <th>File Name</th>
                                                <th>Created</th>
                                                <th>Updated</th>
                                                <th>Status</th>
                                                <th>Log</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            @foreach ($auditor_file_csv as $index => $data)
                                            {{-- {{ dd($data) }} --}}
                                            <tr>
                                                <td>{{ $data->file_name_origin }}</td>
                                                <td>{{ $data->created_at }}</td>
                                                <td>{{ $data->updated_at }}</td>
                                                <td>
                                                    @if ($data->status->id === 1)
                                                    <button type="button" class="btn btn-info btn-sm btn-custom btn-rounded waves-effect waves-light">
                                                        {{ $data->status->name }}
                                                    </button>
                                                    @elseif($data->status->id === 3)
                                                    <button type="button" class="btn btn-danger btn-sm btn-custom btn-rounded waves-effect waves-light">
                                                        {{ $data->status->name }}
                                                    </button>
                                                    @else
                                                    <button type="button" class="btn btn-success btn-sm btn-custom btn-rounded waves-effect waves-light">
                                                        {{ $data->status->name }}
                                                    </button>
                                                    @endif

                                                </td>
                                                <td>
                                                    @if ($data->status->id === 3)
                                                    <a href="#modal-delivery-date-{{ $data->id }}" data-id="{{ $data->id }}" class="btn waves-effect waves-light btn-rounded btn-sm btn-warning ion-search" data-animation="fadein" data-plugin="custommodal" data-overlayspeed="200" data-overlaycolor="#36404a" title="Delivery Date">
                                                    </a>
                                                    <!-- Modal -->
                                                    <div id="modal-delivery-date-{{ $data->id }}" class="modal-demo">
                                                        <button type="button" class="close" onclick="Custombox.close();">
                                                            <span>&times;</span><span class="sr-only">Close</span>
                                                        </button>
                                                        <h4 class="custom-modal-title">Log Error</h4>
                                                        @php
                                                        $data_error_messages = json_decode($data->error_description);
                                                        @endphp
                                                        <div class="modal-body">
                                                            <div class="row">
                                                                <textarea class="form-control" rows="20" id="error_description">@foreach ($data_messages[$index] as $i => $value){{ $i + 1 }}.{{ $value }}. &#13;&#10;@endforeach</textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="list_provinces_cities_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                    <h4 class="modal-title" id="auditPlanLookupModalLabel">List Provinces & Cities
                                    </h4>
                                </div>
                                <div class="modal-body">
                                    <table class="table table-hover m-t-30" id="table-cities-provinces">
                                        <thead>
                                            <tr>
                                                <th>ID Province</th>
                                                <th>Province</th>
                                                <th>ID City</th>
                                                <th>City</th>
                                                <th>ID District</th>
                                                <th>District</th>
                                                <th>ID Village</th>
                                                <th>Village</th>
                                            </tr>
                                        </thead>
                                        <tbody class="tbody">

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="collapse" id="filter_data">
                        <div class="card-box">
                            <div class="card-view">
                                {{ Form::open(['url' => route('auditor.index'), 'role' => 'form', 'method' => 'get', 'id' => 'form-filter']) }}
                                <div class="form-group">
                                    {{ Form::label('search', 'Search', ['class' => 'col-2 col-form-label']) }}
                                    <div class="col-10">
                                        {{ Form::text('search', old('search', app('request')->get('search')), ['class' => 'form-control', 'placeholder' => 'Auditor ID, Unilever ID, Name, Email, Phone']) }}
                                    </div>
                                </div>
                                <div class="row m-t-10">
                                    <div class="form-group col-md-12">
                                        <button class="btn btn-success"><i class="fa fa-check"></i>
                                            Search
                                        </button>
                                        <a href="{{ route('auditor.index') }}" class="btn btn-white">Clear</a>
                                    </div>
                                </div>
                                {{ Form::close() }}
                            </div>
                        </div>
                    </div>

                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Auditor ID</th>
                                <th>Unilever ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Created</th>
                                <th colspan="3" class="text-center">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($datas as $data)
                            <tr>
                                <td>{{ $data->id }}</td>
                                <td>{{ $data->id_unilever }}</td>
                                <td>{{ $data->name }}</td>
                                <td>{{ $data->email }}</td>
                                <td>{{ $data->phone }}</td>
                                <td>{{ $data->created_at }}</td>
                                <td class="text-center">
                                    {{ Form::open(['url' => route('auditor.destroy', ['auditor' => $data->id])]) }}

                                    @if ($can_show)
                                    <a href="{{ route('auditor.show', ['auditor' => $data->id]) }}" class="btn waves-effect waves-light btn-rounded btn-sm btn-success">
                                        Show
                                    </a>
                                    @endif

                                    @if ($can_edit)
                                    <a href="{{ route('auditor.edit', ['auditor' => $data->id]) }}" class="btn waves-effect waves-light btn-rounded btn-xs btn-info">
                                        Edit
                                    </a>
                                    @endif

                                    @if ($can_destroy)
                                    {{ Form::hidden('_method', 'DELETE') }}
                                    <button class="btn waves-effect waves-light btn-rounded btn-xs btn-danger" type="submit">Delete
                                    </button>
                                    @endif

                                    {{ Form::close() }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $datas->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@include('layouts.alert')

@push('styles')
<link href="{{ asset('assets/plugins/custombox/css/custombox.css') }}" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.12.0/css/jquery.dataTables.css">
@endpush

@push('scripts')
<!-- Modal-Effect -->
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.12.0/js/jquery.dataTables.js"></script>
<script src="{{ asset('assets/plugins/custombox/js/custombox.min.js') }}"></script>
<script src="{{ asset('assets/plugins/custombox/js/legacy.min.js') }}"></script>
@endpush

@push('scripts')
<script src="{{ asset('assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#table-pjp-file').DataTable({
            "aaSorting": []
        });


        $.ajax({
            url: "{{ route('auditor.getLocations') }}",
            type: "GET",
            success: function(result) {
                var html = '';
                $.each(result.data, function(key, item) {
                    html += '<tr>';
                    html += '<td>' + item.province_id + '</td>';
                    html += '<td>' + item.province_name + '</td>';
                    html += '<td>' + item.city_id + '</td>';
                    html += '<td>' + item.city_name + '</td>';
                    html += '<td>' + item.district_id + '</td>';
                    html += '<td>' + item.district_name + '</td>';
                    html += '<td>' + item.village_id + '</td>';
                    html += '<td>' + item.village_name + '</td>';
                    html += '</tr>';
                });
                $('.tbody').html(html);
                // Apply DataTable plugin after bind records.
                $("#table-cities-provinces").dataTable({
                    lengthChange: false,
                    processing: true,
                    deferRender: true
                });
            },
        });


        $("#fromDate,#toDate").datepicker({
            "format": "yyyy-mm-dd"
        });
        $("#fromDate,#toDate").datepicker('update', new Date());

    })

    $('#submit_import_button').click(function() {
        $('#loading_screen').removeClass('hidden')
    })

    $('#list_provinces_cities_modal').on('shown.bs.modal', function() {
        $('#upload_data_pjp_modal').modal('hide')
    })

    $('#list_provinces_cities_modal').on('hide.bs.modal', function() {
        $('#upload_data_pjp_modal').modal('show')
    })
</script>
@endpush