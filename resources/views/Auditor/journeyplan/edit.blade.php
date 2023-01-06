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
                            <li>
                                <a href="{{route('auditor.journeyplan.index')}}">Journey Plan Management</a>
                            </li>
                            <li class="active">
                                Edit
                            </li>
                        </ol>
                        <h4 class="m-t-0 header-title">
                            <b>Data Journey Plan</b>
                        </h4>
                        {{ Html::ul($errors->all()) }}
                        {{ Form::model($journeyplan, array('route' => array('auditor.journeyplan.update', $journeyplan->id), 'method' => 'PUT', 'role'=>'form')) }}
                        <div class="form-group">
                            {{ Form::label('id', 'ID', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::text('id', old('id', $journeyplan->id), array('class' => 'form-control', 'placeholder'=>'ID', 'required'=>'required', 'readonly' => 'readonly')) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('name', 'Name', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::text('name', old('name', $journeyplan->name), array('class' => 'form-control', 'placeholder'=>'Name', 'required'=>'required')) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('start_date', 'Start Date', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::text('start_date', \Carbon\Carbon::parse(old('start_date', $journeyplan->start_date))->format('Y-m-d') , ['class' => 'form-control', 'required'=>'required', 'readonly' => 'readonly', 'disabled' => 'disabled']) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('end_date', 'End Date', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::text('end_date', \Carbon\Carbon::parse(old('end_date', $journeyplan->end_date))->format('Y-m-d')  , ['class' => 'form-control', 'required'=>'required']) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('id_province', 'Province', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::select('id_province', $provinces, old('id_province', $journeyplan->id_province), ['placeholder' => 'Pilih Province', 'class' => 'form-control', 'required' => 'required', 'disabled' => 'disabled']) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Pilih salah satu Provinsi, tidak boleh
                                    dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('id_city', 'City', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::select('id_city', $cities, old('id_city', $journeyplan->id_city), ['placeholder' => 'Pilih City', 'class' => 'form-control', 'required' => 'required', 'disabled' => 'disabled']) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Pilih salah satu, tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('id_juragan', 'Juragan', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::select('id_juragan', $juragans, old('id_juragan', $journeyplan->id_juragan), ['placeholder' => 'Pilih Juragan', 'class' => 'form-control', 'required' => 'required', 'disabled' => 'disabled']) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Pilih salah satu Juragan, tidak boleh
                                    dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('assign_to', 'Assign To', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::select('assign_to', $auditors, old('assign_to', $journeyplan->assign_to), ['placeholder' => 'Pilih Auditor', 'class' => 'form-control', 'required' => 'required', 'readonly' => 'readonly', 'disabled' => 'disabled']) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Pilih salah satu Auditor, tidak boleh
                                    dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group m-t-30 m-b-30">
                            <div class="clearfix m-b-15">
                                <div class="pull-right">
                                    <button type="button"
                                            id="storeAuditPlanLookupOutlet"
                                            name="storeAuditPlanLookupOutlet"
                                            class="btn btn-sm btn-primary waves-effect waves-light">
                                        <i class="fa fa-plus"></i>&nbsp;Add Outlet
                                    </button>
                                </div>
                            </div>

                            <table id="tableAuditPlan" class="table table-hover table-bordered">
                                <thead>
                                <tr>
                                    <th width="5%">&nbsp;</th>
                                    <th width="10%">Id</th>
                                    <th style="padding-left: 10px">Outlet</th>
                                    <th width="10%">&nbsp;</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($journeyplan->auditPlans as $auditPlan)
                                    <tr>
                                        <td>{{$auditPlan->id}}</td>
                                        <td>{{$auditPlan->outlet->id}}</td>
                                        <td>{{$auditPlan->outlet->name}}</td>
                                        <td>{{$auditPlan->id}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="form-group">
                            <div class="col-10">
                                <button type="submit" class="btn btn-success m-t-20 pull-right"><i
                                            class="fa fa-check"></i> Save
                                </button>
                                <a class="btn btn-danger m-t-20" href="{{ route('auditor.journeyplan.index') }}"><i
                                            class="fa fa-times"></i> Cancel</a>
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- audit plan modal content -->
    <div id="auditPlanLookupModal" class="modal fade" tabindex="-1" role="dialog"
         aria-labelledby="auditPlanLookupModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="auditPlanLookupModalLabel">Add Outlet</h4>
                </div>
                <div class="modal-body">
                    <table id="tableLookupOutlet" class="table table-hover table-bordered">
                        <thead>
                        <tr>
                            <th width="5%"><input type="checkbox" id="check-all" name="checked_all" value="1"></th>
                            <th width="10%">Id</th>
                            <th>Outlet</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" id="buttonSubmitLookupOutlet" class="btn btn-success">Submit</button>
                </div>
            </div>
        </div>
    </div><!-- /.modal -->
@endsection

@include('layouts.alert')

@push('styles')
    <link href="{{asset('assets/plugins/select2/css/select2.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css')}}" rel="stylesheet"
          type="text/css">
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
    <script src="{{asset('assets/plugins/select2/js/select2.min.js')}}"></script>
    <script src="{{asset('assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>
    <script src="{{asset('assets/plugins/notifyjs/js/notify.js')}}"></script>
    <script src="{{asset('assets/plugins/notifications/notify-metro.js')}}"></script>
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
    <script>
        let textIdJourneyPlan = $("input#id");
        let selectProvince = $("select#id_province");
        let selectCity = $("select#id_city");
        let selectJuragan = $("select#id_juragan");
        let selectAuditor = $("select#assign_to");
        let datePickerStart = $("input#start_date");
        let datePickerEnd = $("input#end_date");
        let buttonAuditPlanLookupOutlet = $("button#storeAuditPlanLookupOutlet");
        let modalAuditPlanLookupOutlet = $("#auditPlanLookupModal");
        let buttonSubmitLookupOutlet = $("#buttonSubmitLookupOutlet");
        let tableLookupOutlet = $('#tableLookupOutlet').DataTable();
        let tableAuditPlan = null;
        let checkAllLookupOutlet = $(`
            <div class="checkbox checkbox-primary m-t-5">
                <input id="auditPlanLookupCheckAll" type="checkbox">
                <label for="auditPlanLookupCheckAll">&nbsp;</label>
            </div>`);

        // todo start new
        var selected_outlets = [];
        $(document).on('change', '#check-all', function() {
            $('#tableLookupOutlet input:checkbox').not(this).prop('checked', this.checked);
            update_selected_outlets();
        });

        $(document).on('click', '.id_outlets', function() {
            update_selected_outlets();
        });

        function update_selected_outlets() {
            $('input.id_outlets').each(function() {
                if ($(this).prop('checked') == true) {
                    if (selected_outlets.indexOf($(this).val()) == -1) {
                        selected_outlets.push($(this).val());
                    }
                } else {
                    if ((index = selected_outlets.indexOf($(this).val())) > -1) {
                        selected_outlets.splice(index, 1);
                    }
                }
            });

            $('#check-all').prop('checked', false);

            if ($('input.id_outlets').is(':visible')) {
                if ($('.id_outlets:not(:checked)').length == 0) {
                    $('#check-all').prop('checked', true);
                }
            }
        }

        function check_selected_outlets() {
            if (selected_outlets.length > 0) {
                $.each(selected_outlets, function(i, id) {
                    setTimeout(
                        function() {
                            $('.id_outlets[value=' + id + ']').attr('checked', 'checked');
                        }, 500);
                });
            }

            setTimeout(
                function() {
                    $('#check-all').prop('checked', false);
                    if ($('input.id_outlets').is(':visible')) {
                        if ($('.id_outlets:not(:checked)').length == 0) {
                            $('#check-all').prop('checked', true);
                        }
                    }
                }, 500);
        }

        $(document).on('click', '#tableLookupOutlet_paginate .paginate_button', function() {
            check_selected_outlets();
        });
        // todo end new
        function selectSetData(obj, data) {
            obj.html('').select2({data: data});
        }

        function selectClearData(obj) {
            obj.html('');
        }

        function storeJourneyPlanLookupCity(idProvince, successCallback, errorCallback) {
            const url = "{{route('geo.city')}}";
            const data = {
                province_id: idProvince
            };
            $.ajax({
                method: 'GET',
                url: url,
                contentType: 'application/json',
                data: data,
                process: false,
            }).done(function (data) {
                successCallback(data);
            }).fail(function (jqXHR, textStatus, errorThrown) {
                errorCallback(jqXHR);
            });
        }

        function storeJourneyPlanLookupJuragan(idCity, successCallback, errorCallback) {
            const url = "{{route('juragan.list')}}";
            const data = {
                city_id: idCity
            };
            $.ajax({
                method: 'GET',
                url: url,
                contentType: 'application/json',
                data: data,
                process: false,
            }).done(function (data) {
                successCallback(data);
            }).fail(function (jqXHR, textStatus, errorThrown) {
                errorCallback(jqXHR);
            });
        }

        function storeJourneyPlanLookupAuditor(idCity, successCallback, errorCallback) {
            const url = "{{route('auditor.ajax.list')}}";
            const data = {
                city_id: idCity
            };
            $.ajax({
                method: 'GET',
                url: url,
                contentType: 'application/json',
                data: data,
                process: false,
            }).done(function (data) {
                successCallback(data);
            }).fail(function (jqXHR, textStatus, errorThrown) {
                errorCallback(jqXHR);
            });
        }

        function storeAuditPlanGet(id, successCallback, errorCallback) {
            const url = "{{url('auditor/audit-plan')}}/" + id;
            const data = {};
            $.ajax({
                method: 'GET',
                url: url,
                contentType: 'application/json',
                data: data,
                process: false,
            }).done(function (data) {
                successCallback(data);
            }).fail(function (jqXHR, textStatus, errorThrown) {
                errorCallback(jqXHR);
            });
        }

        function storeAuditPlanCreate(idJourneyPlan, idOutlet, successCallback, errorCallback) {
            let data = {
                _token: '{{csrf_token()}}',
                _method: 'POST',
                id_journey_plan: idJourneyPlan,
                id_outlet: idOutlet,
                created_by: '{{\auth()->user()->id}}'
            };
            const url = '{{url('auditor/audit-plan')}}';
            $.ajax({
                method: 'POST',
                url: url,
                contentType: 'application/json',
                dataType: 'json',
                data: JSON.stringify(data),
                process: false,
            }).done(function (data) {
                selected_outlets = [];
                successCallback(data);
            }).fail(function (jqXHR, textStatus, errorThrown) {
                errorCallback(jqXHR);
            });
        }

        function storeAuditPlanDelete(id, successCallback, errorCallback) {
            let data = {
                _token: '{{csrf_token()}}',
                _method: 'DELETE',
                deleted_by: '{{\auth()->user()->id}}'
            };
            const url = '{{url('auditor/audit-plan')}}/' + id;
            $.ajax({
                method: 'POST',
                url: url,
                contentType: 'application/json',
                dataType: 'json',
                data: JSON.stringify(data),
                process: false,
            }).done(function (data) {
                successCallback(data);
            }).fail(function (jqXHR, textStatus, errorThrown) {
                errorCallback(data);
            });
        }

        function storeAuditPlanDeleteAll(id, successCallback, errorCallback) {
            let data = {
                _token: '{{csrf_token()}}',
                _method: 'DELETE',
                deleted_by: '{{\auth()->user()->id}}'
            };
            const url = '{{url('auditor/audit-plan/')}}/' + id + '/delete-all';
            $.ajax({
                method: 'POST',
                url: url,
                contentType: 'application/json',
                dataType: 'json',
                data: JSON.stringify(data),
                process: false,
            }).done(function (data) {
                successCallback(data);
            }).fail(function (jqXHR, textStatus, errorThrown) {
                errorCallback(jqXHR);
            });
        }

        function buttonAuditPlanRemoveClick(id) {
            if (confirm("Remove Selected Outlet?")) {
                const successCallback = function (data) {
                    $.Notification.autoHideNotify('success', 'top right', 'Information', "Selected Outlet Removed");
                    tableAuditPlan.row(`#auditplan-row-${id}`).remove().draw(false);
                };
                const errorCallback = function (jqXHR) {
                    $.Notification.autoHideNotify('error', 'Remove Selected Outlet', jqXHR.responseJSON.message);
                };
                if ((index = selected_outlets.indexOf(id)) > -1) {
                    selected_outlets.splice(index, 1);
                }
                storeAuditPlanDelete(id, successCallback, errorCallback)
            }
        }

        $('document').ready(function () {
            tableAuditPlan = $('#tableAuditPlan').DataTable({
                "aaSorting": [],
                "select": {
                    "style": "multi"
                },
                "columns": [
                    {"data": "id", "defaultContent": ""},
                    {"data": "outlet_id", "defaultContent": ""},
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
                        "targets": 2,
                        "orderable": false,
                        "render": function (data, type, row, meta) {
                            return row.name;
                        },
                    },
                    {
                        "targets": 3,
                        "orderable": false,
                        "render": function (data, type, row, meta) {
                            return `<div class="pull-right">
                                        <button type="button"
                                            class="btn btn-sm btn-danger waves-effect waves-light"
                                            onclick="buttonAuditPlanRemoveClick('${row.id}')">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </div>`;
                        }
                    }
                ],
                'createdRow': function (row, data, index) {
                    $(row).attr('id', `auditplan-row-${data.id}`)
                },
            });

            selectProvince.select2({
                placeholder: "Select Province",
            });

            selectCity.select2({
                placeholder: "Select City",
            });

            selectJuragan.select2({
                placeholder: "Select Juragan",
            });

            selectAuditor.select2({
                placeholder: "Select Auditor",
            });

            datePickerStart.datepicker({"format": "yyyy-mm-dd"});
            datePickerEnd.datepicker({"format": "yyyy-mm-dd"});

            selectProvince.on("select2:selecting", function (e) {
                const successCallback = function (data) {
                    let items = $.map(data, function (obj) {
                        let item = {
                            id: obj.id,
                            text: obj.text || obj.name
                        };
                        return item;
                    });
                    selectSetData(selectCity, items);
                    selectCity.trigger('select2:selecting', {sender: 'selectProvince'});
                };
                const errorCallback = function (jqXHR) {
                    $.Notification.autoHideNotify('error', 'top right', 'Error Load City', jqXHR.responseJSON.message);
                };
                if (confirm('Are You Sure Want To Change Province All Added Outlet Will Be Remove?')) {
                    storeJourneyPlanLookupCity(e.params.args.data.id, successCallback, errorCallback);
                } else {
                    e.preventDefault();
                }
            });

            selectCity.on("select2:selecting", function (e, d) {
                const successCallbackJuragan = function (data) {
                    let items = $.map(data, function (obj) {
                        let item = {
                            id: obj.id,
                            text: obj.text || obj.name
                        };
                        return item;
                    });
                    selectSetData(selectJuragan, items);
                    selectJuragan.trigger('select2:selecting', {sender: 'selectCity'});
                };
                const errorCallbackJuragan = function (jqXHR) {
                    $.Notification.autoHideNotify('error', 'top right', 'Error Load Juragan', jqXHR.responseJSON.message);
                };
                const successCallbackAuditor = function (data) {
                    let items = $.map(data, function (obj) {
                        let item = {
                            id: obj.id,
                            text: obj.text || obj.name
                        };
                        return item;
                    });
                    selectSetData(selectAuditor, items);
                    selectAuditor.trigger('select2:selecting', {sender: 'selectCity'});
                };
                const errorCallbackAuditor = function (jqXHR) {
                    $.Notification.autoHideNotify('error', 'top right', 'Error Load Auditor', jqXHR.responseJSON.message);
                };
                if (d !== undefined && d.sender === "selectProvince") {
                    storeJourneyPlanLookupJuragan(this.value, successCallbackJuragan, errorCallbackJuragan);
                    storeJourneyPlanLookupAuditor(this.value, successCallbackAuditor, errorCallbackAuditor);
                } else {
                    if (confirm('Are You Sure Want To Change City All Added Outlet Will Be Remove?')) {
                        storeJourneyPlanLookupJuragan(e.params.args.data.id, successCallbackJuragan, errorCallbackJuragan);
                        storeJourneyPlanLookupAuditor(e.params.args.data.id, successCallbackAuditor, errorCallbackAuditor);
                    } else {
                        e.preventDefault();
                    }
                }
            });

            selectJuragan.on('select2:selecting', function (e, d) {
                const successCallback = function (data) {
                    tableAuditPlan.clear().draw();
                };
                const errorCallback = function (jqXHR) {
                    $.Notification.autoHideNotify('error', 'top right', 'Error Remove All Outlet', jqXHR.responseJSON.message);
                };
                if (d !== undefined && d.sender === "selectCity") {
                    storeAuditPlanDeleteAll(textIdJourneyPlan.val(), successCallback, errorCallback);
                } else {
                    if (confirm('Are You Sure Want To Change Juragan All Added Outlet Will Be Remove?')) {
                        storeAuditPlanDeleteAll(textIdJourneyPlan.val(), successCallback, errorCallback);
                    } else {
                        e.preventDefault();
                    }
                }
            });

            buttonSubmitLookupOutlet.on("click", function (e) {
                let row = null;
                let checkbox = null;
                let collection = [];
                const successCallback = function (response) {
                    const successCallback = function (response) {
                        let items = $.map(response.data, function (obj) {
                            let item = {
                                id: obj.id,
                                outlet_id: obj.outlet.id,
                                name: obj.outlet.name,
                                final_transaction_in_one_month: obj.outlet.final_transaction_in_one_month,
                                number_of_purchases_in_one_month: obj.outlet.number_of_purchases_in_one_month
                            };
                            return item;
                        });
                        tableAuditPlan.rows.add(items).draw();
                    };
                    const errorCallback = function (jqXHR) {
                        swal({
                            title: "Error Load Outlet",
                            text: jqXHR.responseJSON.message,
                            type: "error",
                        });
                    };
                    modalAuditPlanLookupOutlet.modal('hide');
                    tableAuditPlan.clear();
                    storeAuditPlanGet(textIdJourneyPlan.val(), successCallback, errorCallback)
                };
                const errorCallback = function (jqXHR) {
                    swal({
                        title: "Error Add Outlet",
                        text: jqXHR.responseJSON.message,
                        type: "error",
                    });
                };

                if (selected_outlets.length < 1) {
                    $.Notification.autoHideNotify('warning', 'top right', 'Warning', 'Please Select Outlet');
                } else {
                    storeAuditPlanCreate(textIdJourneyPlan.val(), selected_outlets, successCallback, errorCallback);
                }
            });

            buttonAuditPlanLookupOutlet.on("click", function (e) {
                modalAuditPlanLookupOutlet.modal("show");
                tableLookupOutlet.destroy();
                tableLookupOutlet = $('#tableLookupOutlet').DataTable({
                    "lengthMenu": [10, 25, 50, 100, 200, 300],
                    // "bSort": false,
                    "columnDefs": [{
                        "orderable": false,
                        "targets": 0
                    }],
                    "processing": true,
                    "serverSide": true,
                    "ajax": {
                        "url": "{{url('auditor/audit-plan/outlet/?id_juragan=')}}" + selectJuragan.val() + '&id_journey_plan=' + textIdJourneyPlan.val(),
                        "type": 'GET',
                    },
                    "createdRow": function(row, data, index) {
                        check_selected_outlets();
                    }
                });
            });
        });
    </script>
@endpush