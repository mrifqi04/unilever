@extends('layouts.app')
@section('content')

    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card-box">
                        <ol class="breadcrumb">
                            <li>
                                Auditor
                            </li>
                            <li>
                                <a href="{{route('auditor.journeyplan.index')}}">Journey Plan Management</a>
                            </li>
                            <li class="active">
                                Show
                            </li>
                        </ol>
                        <h4 class="m-t-0 header-title">
                            <b>Data Journey Plan</b>
                        </h4>

                        <div class="form-group">
                            {{ Form::label('id', 'ID', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::text('id', $journeyplan->id, array('class' => 'form-control', 'placeholder'=>'ID', 'disabled'=>'disabled', 'readonly' => 'readonly')) }}
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('name', 'Name', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::text('name', $journeyplan->name, array('class' => 'form-control', 'placeholder'=>'Name', 'disabled'=>'disabled')) }}
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('start_date', 'Start Date', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::text('start_date', \Carbon\Carbon::parse($journeyplan->start_date)->format('Y-m-d') , ['class' => 'form-control', 'disabled'=>'disabled']) }}
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('end_date', 'End Date', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::text('end_date', \Carbon\Carbon::parse($journeyplan->end_date)->format('Y-m-d')  , ['class' => 'form-control', 'disabled'=>'disabled']) }}
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('id_province', 'Province', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::text('id_province', !is_null($journeyplan->province) ? $journeyplan->province->name : '' , ['class' => 'form-control', 'disabled'=>'disabled']) }}
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('id_city', 'City', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::text('id_city', !is_null($journeyplan->city) ? $journeyplan->city->name : '', ['class' => 'form-control', 'disabled'=>'disabled']) }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('id_juragan', 'Juragan', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::text('id_juragan', !is_null($journeyplan->juragan) ? $journeyplan->juragan->name : '', ['class' => 'form-control', 'disabled'=>'disabled']) }}
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('assign_to', 'Assign To', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::text('assign_to', !is_null($journeyplan->assignTo) ? $journeyplan->assignTo->name : '', ['class' => 'form-control', 'disabled'=>'disabled']) }}
                            </div>
                        </div>

                        <div class="form-group m-t-30 m-b-30">
                            <table id="tableAuditPlan" class="table table-hover table-bordered">
                                <thead>
                                <tr>
                                    <th width="5%">&nbsp;</th>
                                    <th style="padding-left: 10px">Outlet</th>
                                    <th width="10%">&nbsp;</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($journeyplan->auditPlans as $auditPlan)
                                    <tr>
                                        <td>{{$auditPlan->id}}</td>
                                        <td>{{$auditPlan->outlet->name}}</td>
                                        <td>{{$auditPlan->id}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="form-group">
                            <div class="col-2"></div>
                            <div class="col-10">
                                <a class="btn btn-danger m-t-20" href="{{ route('auditor.journeyplan.index') }}">
                                    <i class="fa fa-times"></i>&nbsp;Back
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@include('layouts.alert')

@push('styles')
    <!-- DataTables -->
    <link href="{{asset('assets/plugins/datatables/jquery.dataTables.min.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('assets/plugins/datatables/buttons.bootstrap.min.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('assets/plugins/datatables/fixedHeader.bootstrap.min.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('assets/plugins/datatables/responsive.bootstrap.min.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('assets/plugins/datatables/scroller.bootstrap.min.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('assets/plugins/datatables/dataTables.colVis.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('assets/plugins/datatables/dataTables.bootstrap.min.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('assets/plugins/datatables/fixedColumns.dataTables.min.css')}}" rel="stylesheet"
          type="text/css"/>
@endpush

@push('scripts')
    <!-- DataTables -->
    <script src="{{asset('assets/plugins/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('assets/plugins/datatables/dataTables.bootstrap.js')}}"></script>
    <script src="{{asset('assets/plugins/datatables/dataTables.buttons.min.js')}}"></script>
    <script src="{{asset('assets/plugins/datatables/buttons.bootstrap.min.js')}}"></script>
    <script src="{{asset('assets/plugins/datatables/jszip.min.js')}}"></script>
    <script src="{{asset('assets/plugins/datatables/pdfmake.min.js')}}"></script>
    <script src="{{asset('assets/plugins/datatables/vfs_fonts.js')}}"></script>
    <script src="{{asset('assets/plugins/datatables/buttons.html5.min.js')}}"></script>
    <script src="{{asset('assets/plugins/datatables/buttons.print.min.js')}}"></script>
    <script src="{{asset('assets/plugins/datatables/dataTables.fixedHeader.min.js')}}"></script>
    <script src="{{asset('assets/plugins/datatables/dataTables.keyTable.min.js')}}"></script>
    <script src="{{asset('assets/plugins/datatables/dataTables.responsive.min.js')}}"></script>
    <script src="{{asset('assets/plugins/datatables/responsive.bootstrap.min.js')}}"></script>
    <script src="{{asset('assets/plugins/datatables/dataTables.scroller.min.js')}}"></script>
    <script src="{{asset('assets/plugins/datatables/dataTables.colVis.js')}}"></script>
    <script src="{{asset('assets/plugins/datatables/dataTables.fixedColumns.min.js')}}"></script>
@endpush

@push('scripts')
    <script type="text/javascript">
        $('document').ready(function () {
            $('#tableAuditPlan').DataTable({
                "aaSorting": [],
                "select": {
                    "style": "multi"
                },
                "columns": [
                    {"data": "id", "defaultContent": ""},
                    {"data": "name", "defaultContent": ""},
                    {"data": "id", "defaultContent": ""},
                ],
                "deferRender": true,
                "pagingType": "simple_numbers",
                "columnDefs": [
                    {
                        "targets": 0,
                        "orderable": false,
                        "render": function (data, type, row, meta) {
                            return '<i class="fa fa-circle m-l-10 text-muted"></i>';
                        },
                    },
                    {
                        "targets": 1,
                        "orderable": false,
                        "render": function (data, type, row, meta) {
                            return row.name;
                        },
                    },
                    {
                        "targets": 2,
                        "orderable": false,
                        "render": function (data, type, row, meta) {
                            return ``;
                        }
                    }
                ],
                'createdRow': function (row, data, index) {
                    $(row).attr('id', `auditplan-row-${data.id}`)
                },
            });
        });
    </script>
@endpush