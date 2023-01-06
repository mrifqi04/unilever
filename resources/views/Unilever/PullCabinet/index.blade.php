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
                            <a href="{{route('unilever.pull-cabinet.index')}}">Approval Tarik Kabinet</a>
                        </li>
                    </ol>
                    <h4 class="m-t-0 header-title"><b>Data</b></h4>
                    <button class="btn btn-primary  d-none d-lg-block" type="button" data-toggle="collapse" data-target="#filter_data" aria-expanded="{{$in_search === true ? 'true' : 'false'}}" aria-controls="collapseExample">
                        <i class="fa fa-arrow-circle-down"></i> Filter
                    </button>
                    @if($canExportOutlet)
                    <a href="{{route('unilever.pull-cabinet.export-outlet')}}" class="btn btn-primary d-lg-block">
                        <i class="fa fa-download"></i> Download Outlet
                    </a>
                    @endif
                    @if($canApproveBulk)
                    <button id="approve-bulk" class="btn btn-success d-lg-block">
                        <i class="ion-checkmark"></i> Approve Checked Rows
                    </button>
                    @endif
                    @if($canRejectBulk)
                    <button id="reject-bulk" class="btn btn-danger d-lg-block">
                        <i class="ion-close"></i> Reject Checked Rows
                    </button>
                    @endif
                    <div class="m-t-20">
                        <div class="collapse {{$in_search === true ? 'in' : ''}}" aria-expanded="{{$in_search === true ? 'true' : 'false'}}" id="filter_data">
                            <div class="card-box">
                                <div class="card-view">
                                    {{ Form::open(['url' => route('unilever.pull-cabinet.index'), 'role' => 'form', 'method' => 'get','class'=>'form-inline', 'id' => 'form-filter']) }}
                                    <div class="row">
                                        <div class="form-group col-md-3">
                                            {{ Form::select('id_province', $provinces, null,
                                                    ['class' => 'form-control', 'placeholder'=>'Province']) }}
                                        </div>
                                        <div class="form-group col-md-3">
                                            {{ Form::select('id_city', [], null,
                                                    ['class' => 'form-control', 'placeholder'=>'City']) }}
                                        </div>
                                        <div class="form-group col-md-3">
                                            {{ Form::select('id_district', [], null,
                                                    ['class' => 'form-control', 'placeholder'=>'District']) }}
                                        </div>
                                        <div class="form-group col-md-3">
                                            {{ Form::select('id_village', [], null,
                                                ['class' => 'form-control', 'placeholder'=>'Village']) }}
                                        </div>
                                    </div>
                                    <div class="row m-t-10">
                                        <div class="form-group col-md-3">
                                            {{ Form::select('id_juragan', $juragans, null,
                                                ['class' => 'form-control', 'placeholder'=>'Outlet']) }}
                                        </div>
                                        <div class="form-group col-md-3">
                                            {{ Form::select('id_outlet', $outlets, null,
                                                ['class' => 'form-control', 'placeholder'=>'Outlet']) }}
                                        </div>
                                        <div class="form-group col-md-3">
                                            {{ Form::select('id_progress', $progress, null,
                                                ['class' => 'form-control', 'placeholder'=>'Status']) }}
                                        </div>
                                        <div class="form-group col-md-3">
                                            {{ Form::text('csdp', null,
                                                ['class' => 'form-control', 'placeholder'=>'CSDP']) }}
                                        </div>
                                    </div>
                                    <div class="row m-t-10">
                                        <div class="form-group col-md-3">
                                            <div class="col-10">
                                                <input class="form-control date" name="send_date" id="send_date" value="{{app('request')->input('send_date')}}" placeholder="Tgl Jadwal Tarik">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row m-t-10">
                                        <div class="form-group col-md-12">
                                            <button class="btn btn-success" type="button" id="submit-filter"><i class="fa fa-check"></i>
                                                Search
                                            </button>
                                            <button class="btn btn-white" type="button" id="reset-filter">Clear</a>
                                        </div>
                                    </div>
                                    {{ Form::close() }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover table-responsive" id="data-table">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="check-all"></th>
                                    <th>Kota</th>
                                    <th>ID Juragan</th>
                                    <th>Juragan</th>
                                    <th>ID Toko</th>
                                    <th>CSDP</th>
                                    <th>Nama Toko</th>
                                    <th>Pemilik Toko</th>
                                    <th>Alamat</th>
                                    <th>No Telp</th>
                                    <th>Status</th>
                                    <th>Tgl Status Progress</th>
                                    <th>Tgl Jadwal Tarik</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@include('layouts.alert')

@push('styles')
<link href="{{asset('assets/plugins/sweetalert2/sweetalert2.min.css')}}" rel="stylesheet" type="text/css">
<link href="{{asset('assets/plugins/custombox/css/custombox.css')}}" rel="stylesheet">
<link href="{{asset('assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css')}}" rel="stylesheet" type="text/css">
<link href="{{asset('assets/plugins/select2/css/select2.css')}}" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap4.min.css">
@endpush

@push('scripts')
<script src="{{asset('assets/plugins/sweetalert2/sweetalert2.min.js')}}"></script>
<!-- Modal-Effect -->
<script src="{{asset('assets/plugins/custombox/js/custombox.min.js')}}"></script>
<script src="{{asset('assets/plugins/custombox/js/legacy.min.js')}}"></script>
<script src="{{asset('assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>
<script src="{{asset('assets/plugins/select2/js/select2.min.js')}}"></script>
<script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap4.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        var data_table;
        loadDataTable();

        function loadDataTable() {
            data_table = $('#data-table').DataTable({
                "lengthMenu": [10, 25, 50, 100, 200, 300],
                // "bSort": false,
                "searching": false,
                "columnDefs": [{
                        "orderable": false,
                        "targets": 0
                    },
                    {
                        "orderable": false,
                        "targets": 13
                    },
                    {
                        "defaultContent": "-",
                        "targets": "_all"
                    }
                ],
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "{{url('unilever/pull-cabinet')}}?get_records=1&" + $('#form-filter').serialize(),
                    "type": 'GET',
                },
                "createdRow": function(row, data, index) {
                    var td = [];
                    td[0] = null;
                    if (data['section'] == 'partner' && data['status_progress'] == '1') {
                        td[0] = `<input type="checkbox" class="check_rows" value="` + data['id'] + `">`;
                    }
                    td[1] = data['city'];
                    td[2] = data['id_leveredge'];
                    td[3] = data['juragan'];
                    td[4] = data['outlet_id'];
                    td[5] = data['csdp'];
                    td[6] = data['outlet_name'];
                    td[7] = data['owner'];
                    td[8] = data['address'];
                    td[9] = data['phone'];
                    td[10] = `<span class="label label-` + data['status_class'] + `"style="padding: 5px">` + data['status'] + `</span>`;
                    td[11] = data['created_date_format'];
                    td[12] = data['send_date_format'];
                    td[13] = '';
                    if ("{{$canShow}}") {
                        td[13] += `<a href="{{url('unilever/pull-cabinet')}}/` + data['id'] + `"
                                title="show"
                                class="btn waves-effect waves-light btn-rounded btn-sm btn-success ion-search">
                            </a>`;
                    }

                    if ("{{$canApprove}}") {
                        td[13] += `<form name="form-approve-` + data['id'] + `"
                                    action="{{url('unilever/pull-cabinet/approve')}}/` + data['id'] + `"
                                    method="POST">
                                {{ csrf_field() }}
                                {{ method_field('PUT') }}
                                <button type="button" data-id="` + data['id'] + `"
                                        name="button-approve"
                                        class="btn waves-effect waves-light btn-rounded btn-sm btn-primary ion-checkmark"
                                        title="approve">
                                </button>
                            </form>`;
                    }

                    if ("{{$canReject}}") {
                        td[13] += `<form name="form-reject-` + data['id'] + `"
                                    action="{{url('unilever/pull-cabinet/reject')}}/` + data['id'] + `"
                                    method="POST">
                                {{ csrf_field() }}
                                {{ method_field('PUT') }}
                                <input type="hidden" name="reject_reason_id" value="` + (data['section'] !== 'uli' ? '' : data['reject_reason_id']) + `">
                                <button type="button" data-id="` + data['id'] + `" name="button-reject"
                                        class="btn waves-effect waves-light btn-rounded btn-sm btn-danger ion-close"
                                        title="reject">
                                </button>
                            </form>`
                    }

                    if ("{{$canChangeDeliveryDate}}") {
                        td[13] += `<a href="#" data-id="` + data['id'] + `"
                                class="btn waves-effect waves-light btn-rounded btn-sm btn-warning ion-calendar"
                                onclick="Custombox.open({
                                        target: '#modal-delivery-date-` + data['id'] + `',
                                        effect: 'fadein',
                                        overlaySpeed: 200,
                                        overlayColor: '#36404a'
                                });"
                                title="Delivery Date">
                            </a>
                            <!-- Modal -->
                            <div id="modal-delivery-date-` + data['id'] + `" class="modal-demo">
                                <button type="button" class="close" onclick="Custombox.close();">
                                    <span>&times;</span><span class="sr-only">Close</span>
                                </button>
                                <h4 class="custom-modal-title">Change Delivery Date</h4>
                                <div class="modal-body">
                                    <div class="row">
                                        <form name="form-delivery-date-` + data['id'] + `"
                                            action="{{url('unilever/pull-cabinet/change-delivery-date')}}/` + data['id'] + `"
                                                method="POST" class="form-horizontal">
                                            {{ csrf_field() }}
                                            {{ method_field('PUT') }}
                                            <div class="form-group">
                                                <label for="send_date"
                                                        class="col-md-4 control-label">Delivery
                                                    Date</label>
                                                <div class="col-md-6">
                                                    <input name="send_date"
                                                            value="` + (data['send_date_format'] ? data['send_date_format'] : new Date().toISOString().split('T')[0]) + `"
                                                            required
                                                            class="form-control date">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-md-4"></div>
                                                <div class="col-md-10">
                                                    <button class="btn btn-success m-t-20 pull-right">
                                                        <i class="fa fa-check"></i> Save
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>`;
                    }

                    $.each(td, function(i) {
                        if (i == 10) {
                            $(row).find('td').eq(i).attr('align', 'center');
                        }
                        $(row).find('td').eq(i).html(td[i]);
                    });
                    check_selected();
                }
            });
        }
        $('#submit-filter').click(function() {
            data_table.destroy();
            loadDataTable();
        });

        $('#reset-filter').click(function() {
            $("#form-filter").trigger('reset');
            $('select').val('').change();
            data_table.destroy();
            loadDataTable();
        });
    });
</script>
<script type="text/javascript">
    var selectedIds = [];
    $(document).ready(function() {
        $(".date").datepicker({
            "format": "yyyy-mm-dd"
        });
        $("select[name='id_province']").select2({
            placeholder: "Province"
        });
        $("select[name='id_city']").select2({
            placeholder: "City"
        });
        $("select[name='id_district']").select2({
            placeholder: "District"
        });
        $("select[name='id_village']").select2({
            placeholder: "Village"
        });
        $("select[name='id_juragan']").select2({
            placeholder: "Juragan"
        });
        $("select[name='id_outlet']").select2({
            placeholder: "Outlet"
        });
        $("select[name='id_progress']").select2({
            placeholder: "Status"
        });
        $("select[name='id_province']").on("change", function(event) {
            url = "{{route('geo.city')}}" + "?province_id=" + this.value;
            $.ajax({
                url: url,
                method: "GET",
                success: function(data) {
                    var items = $.map(data, function(obj) {
                        obj.id = obj.id;
                        obj.text = obj.text || obj.name;

                        return obj;
                    });
                    fillSelectData("select[name='id_city']", items);
                    emptySelectData("select[name='id_district']");
                    emptySelectData("select[name='id_village']");
                }
            });
        });

        $("select[name='id_city']").on("change", function(event) {
            url = "{{route('geo.district')}}" + "?city_id=" + this.value;
            $.ajax({
                url: url,
                method: "GET",
                success: function(data) {
                    var items = $.map(data, function(obj) {
                        obj.id = obj.id;
                        obj.text = obj.text || obj.name;
                        return obj;
                    });
                    fillSelectData("select[name='id_district']", items);
                    emptySelectData("select[name='id_village']");
                }
            });
        });

        $("select[name='id_district']").on("change", function(event) {
            url = "{{route('geo.village')}}" + "?district_id=" + this.value;
            $.ajax({
                url: url,
                method: "GET",
                success: function(data) {
                    var items = $.map(data, function(obj) {
                        obj.id = obj.id;
                        obj.text = obj.text || obj.name;

                        return obj;
                    });
                    fillSelectData("select[name='id_village']", items);
                }
            });
        });

    });

    function fillSelectData(selectorId, datas) {
        obj = $(selectorId);
        obj.html('').select2({
            data: datas
        });
        $(selectorId).trigger('change');
    }

    function emptySelectData(selectorId) {
        obj = $(selectorId);
        obj.html('').select2({});
    }

    $(document).on('click', "[name='button-approve']", function(e) {
        var id = $(this).data('id');
        swal({
            title: "Outlet Approval",
            text: "Approve Outlet?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Approve",
            cancelButtonText: "Cancel"
        }).then(function(isConfirm) {
            if (isConfirm) {
                $("[name='form-approve-" + id + "']").submit();
            }
        });
    });

    var listRejectReasons = <?php echo $rejectReasons; ?>;
    $(document).on('click', "[name='button-reject']", function(e) {
        var id = $(this).data('id');
        var form = $(this).closest('[name^=form-reject]');
        var rejectReason = form.find("[name=reject_reason_id]");
        var rejectReasonId = rejectReason.attr('value');
        swal({
            title: "Outlet Approval",
            text: "Reject Outlet?",
            type: "warning",
            input: 'select',
            inputOptions: listRejectReasons,
            inputValue: rejectReasonId,
            inputPlaceholder: 'Pilih Alasan',
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Reject",
            cancelButtonText: "Cancel",
            inputValidator: (value) => {
                return new Promise((resolve, reject) => {
                    if (value != '') {
                        resolve();
                    } else {
                        reject('Alasan wajib dipilih!');
                    }
                });
            }
        }).then(function(result) {
            if (result) {
                rejectReason.attr('value', result);
                form.submit();
            }
        });
    });

    $(document).on('change', '#check-all', function() {
        $('.check_rows').prop('checked', false);
        if ($(this).is(':checked') == true) {
            $('.check_rows').prop('checked', true);
        }
        update_selected();
    });

    $(document).on('change', '.check_rows', function() {
        update_selected();
    });

    $(document).on('click', "#approve-bulk", function() {
        if (selectedIds.length > 0) {
            swal({
                title: "Outlet Approval",
                text: "Approve Outlet?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Approve",
                cancelButtonText: "Cancel"
            }).then(function(isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        method: 'put',
                        url: "{{route('unilever.pull-cabinet.approve-bulk')}}",
                        dataType: 'json',
                        data: {
                            ids: selectedIds,
                            _token: '{{csrf_token()}}',
                        },
                        success: function(res) {
                            if (res.status === true) {
                                swal({
                                    title: "Outlet Approval",
                                    text: res.message,
                                    type: "success",
                                });
                                location.reload();
                            } else {
                                swal({
                                    title: "Outlet Approval",
                                    text: res.message,
                                    type: "error",
                                });
                            }
                        }
                    });
                }
            });
        } else {
            swal({
                title: "Approve Checked Rows",
                text: 'Belum ada data yang dipilih',
                type: "error",
            });
        }
    });

    $(document).on('click', "#reject-bulk", function() {
        if (selectedIds.length > 0) {
            swal({
                title: "Outlet Approval",
                text: "Reject Outlet?",
                type: "warning",
                input: 'select',
                inputOptions: listRejectReasons,
                inputPlaceholder: 'Pilih Alasan',
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Reject",
                cancelButtonText: "Cancel",
                inputValidator: (value) => {
                    return new Promise((resolve, reject) => {
                        if (value != '') {
                            resolve();
                        } else {
                            reject('Alasan wajib dipilih!');
                        }
                    });
                }
            }).then(function(result) {
                if (result) {
                    $.ajax({
                        method: 'put',
                        url: "{{route('unilever.pull-cabinet.reject-bulk')}}",
                        dataType: 'json',
                        data: {
                            ids: selectedIds,
                            reject_reason_id: result,
                            _token: '{{csrf_token()}}',
                        },
                        success: function(res) {
                            if (res.status === true) {
                                swal({
                                    title: "Outlet Approval",
                                    text: res.message,
                                    type: "success",
                                });
                                location.reload();
                            } else {
                                swal({
                                    title: "Outlet Approval",
                                    text: res.message,
                                    type: "error",
                                });
                            }
                        }
                    });
                }
            });
        } else {
            swal({
                title: "Reject Checked Rows",
                text: 'Belum ada data yang dipilih',
                type: "error",
            });
        }
    });

    function update_selected() {
        $('.check_rows').each(function() {
            if ($(this).prop('checked') == true) {
                if (selectedIds.indexOf($(this).val()) < 0) {
                    selectedIds.push($(this).val());
                }
            } else {
                if ((index = selectedIds.indexOf($(this).val())) > -1) {
                    selectedIds.splice(index, 1);
                }
            }
        });

        $('#check-all').prop('checked', false);

        if ($('.check_rows').is(':visible')) {
            if ($('.check_rows:not(:checked)').length == 0) {
                $('#check-all').prop('checked', true);
            }
        }
    }

    function check_selected() {
        if (selectedIds.length > 0) {
            $.each(selectedIds, function(i, id) {
                setTimeout(
                    function() {
                        $('.check_rows[value=' + id + ']').attr('checked', 'checked');
                    }, 500);
            });
        }

        setTimeout(
            function() {
                $('#check-all').prop('checked', false);
                if ($('.check_rows').is(':visible')) {
                    if ($('.check_rows:not(:checked)').length == 0) {
                        $('#check-all').prop('checked', true);
                    }
                }
            }, 500);
    }

    $(document).on('click', '.page-link', function() {
        check_selected();
    });
</script>
@endpush