@extends('layouts.app')

@section('content')

    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card-box">
                        <ol class="breadcrumb">
                            <li>
                                Cabinet
                            </li>
                            <li class="active">
                                <a href="{{route('delivery.index')}}">Exchange</a>
                            </li>
                        </ol>
                        <h4 class="m-t-0 header-title"><b>Data</b></h4>

                        <button class="btn btn-primary  d-none d-lg-block" type="button" data-toggle="collapse"
                                data-target="#filter_data" aria-expanded="false" aria-controls="collapseExample">
                            <i class="fa fa-arrow-circle-down"></i> Filter
                        </button>

                        {{--@if(Auth::user()->getPermissionByName('delivery.create'))--}}
                            {{--<a href="{{route('delivery.create')}}">--}}
                                {{--<button type="button" class="btn btn-info d-none d-lg-block">--}}
                                    {{--<i class="fa fa-plus-circle"></i> Create New--}}
                                {{--</button>--}}
                            {{--</a>--}}
                        {{--@endif--}}

                        <div class="collapse" id="filter_data">
                            <div class="card-box">
                                <div class="card-view">
                                    {{ Form::open(array('url' => route('delivery.index'), 'role' => 'form', 'method' => 'get')) }}
                                    <div class="form-group">
                                        {{ Form::label('juragan', 'Juragan', array('class' => 'col-2 col-form-label')) }}
                                        <div class="col-10">
                                            {{ Form::text('juragan', old('juragan', app('request')->get('juragan')), array('class' => 'form-control', 'placeholder'=>'Juragan Code or Name')) }}
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        {{ Form::label('outlet', 'Outlet ID', array('class' => 'col-2 col-form-label')) }}
                                        <div class="col-10">
                                            {{ Form::text('outlet', old('outlet', app('request')->get('outlet')), array('class' => 'form-control', 'placeholder'=>'Outlet ID')) }}
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        {{ Form::label('request_date', 'Request Date', array('class' => 'col-2 col-form-label')) }}
                                        <div class="col-10">
                                            <div class="input-daterange input-group" id="request-date-range">
                                                <input type="text" class="form-control" name="start"
                                                       value="{{app('request')->input('request_start')}}"/>
                                                <span class="input-group-addon bg-custom b-0 text-white">to</span>
                                                <input type="text" class="form-control" name="end"
                                                       value="{{app('request')->input('request_end')}}"/>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        {{ Form::label('withdraw_date', 'Withdraw Date', array('class' => 'col-2 col-form-label')) }}
                                        <div class="col-10">
                                            <div class="input-daterange input-group" id="withdraw-date-range">
                                                <input type="text" class="form-control" name="start"
                                                       value="{{app('request')->input('withdraw_start')}}"/>
                                                <span class="input-group-addon bg-custom b-0 text-white">to</span>
                                                <input type="text" class="form-control" name="end"
                                                       value="{{app('request')->input('withdraw_end')}}"/>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        {{ Form::label('propose_date', 'Propose Date', array('class' => 'col-2 col-form-label')) }}
                                        <div class="col-10">
                                            <div class="input-daterange input-group" id="propose-date-range">
                                                <input type="text" class="form-control" name="start"
                                                       value="{{app('request')->input('propose_start')}}"/>
                                                <span class="input-group-addon bg-custom b-0 text-white">to</span>
                                                <input type="text" class="form-control" name="end"
                                                       value="{{app('request')->input('propose_end')}}"/>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-10">
                                            <button type="submit" class="btn btn-success"><i class="fa fa-check"></i>
                                                Search
                                            </button>
                                        </div>
                                    </div>
                                    {{ Form::close() }}
                                </div>
                            </div>
                        </div>

                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>Juragan Code</th>
                                <th>Juragan Name</th>
                                <th>Outlet ID</th>
                                <th>Request At</th>
                                <th>Withdrawal At</th>
                                <th>Propose Date</th>
                                <th colspan="3" class="text-center">Action</th>
                            </tr>
                            </thead>

                            <tbody>
                            {{--@foreach($datas as $data)--}}
                            <tr>
                                <td>adasdsa</td>
                                <td>asdada</td>
                                <td>asdad</td>
                                <td>sadasda</td>
                                <td>adada</td>
                                <td>asdadasd</td>
                                <td class="text-center">
                                    {{--{{ Form::open(array('url' => route('withdraw_cabinet.destroy', ['withdraw_cabinet'=>1]))) }}--}}
                                    <div class="button-list">
                                        <button class="btn waves-effect waves-light btn-sm btn-default approve" data-id="123">
                                            Approve
                                        </button>
                                        <button class="btn waves-effect waves-light btn-sm btn-danger reject" data-id="123">
                                            Reject
                                        </button>
                                        {{--@if(Auth::user()->getPermissionByName('delivery.show'))--}}
                                        {{--<a href="{{route('delivery.show')}}"--}}
                                        {{--class="btn waves-effect waves-light btn-rounded btn-sm btn-success">--}}
                                        {{--Show--}}
                                        {{--</a>--}}
                                        {{--@endif--}}

                                        {{--@if(Auth::user()->getPermissionByName('delivery.exportPDF'))--}}
                                        {{--<a href="{{route('delivery.exportPDF')}}?id="--}}
                                        {{--class="btn waves-effect waves-light btn-rounded btn-sm btn-success">--}}
                                        {{--Export PDF--}}
                                        {{--</a>--}}
                                        {{--@endif--}}
                                        {{--@if(Auth::user()->getPermissionByName('delivery.exportADR'))--}}
                                        {{--<a href="{{route('delivery.exportADR')}}?id="--}}
                                        {{--class="btn waves-effect waves-light btn-rounded btn-sm btn-success">--}}
                                        {{--Export ADR--}}
                                        {{--</a>--}}
                                        {{--@endif--}}

                                        {{--@if(Auth::user()->getPermissionByName('delivery.destroy'))--}}
                                        {{--                                        {{ Form::hidden('_method', 'DELETE') }}--}}
                                        <div class="btn-group">
                                            <button type="button"
                                                    class="btn btn-success dropdown-toggle waves-effect waves-light"
                                                    data-toggle="dropdown" aria-expanded="false"><i
                                                        class="fa fa-ellipsis-h"></i>
                                            </button>
                                            <ul class="dropdown-menu" role="menu">
                                                <li><a href="#">Show</a></li>
                                                {{--<li><a href="#">Delete</a></li>--}}
                                                <li><a href="{{route('withdraw.exportPDF')}}?id=">
                                                        Export PDF
                                                    </a></li>
                                            </ul>
                                        </div>
                                        {{--@endif--}}
                                    </div>
                                    {{--                                    {{ Form::close() }}--}}
                                </td>
                            </tr>
                            <tr>
                                <td>adasdsa</td>
                                <td>asdada</td>
                                <td>asdad</td>
                                <td>sadasda</td>
                                <td>adada</td>
                                <td>asdadasd</td>
                                <td class="text-center">
                                    {{--{{ Form::open(array('url' => route('withdraw_cabinet.destroy', ['withdraw_cabinet'=>1]))) }}--}}
                                    <div class="button-list">
                                        <button class="btn waves-effect waves-light btn-sm btn-default change" data-id="123">
                                            Change Date
                                        </button>
                                        {{--@if(Auth::user()->getPermissionByName('delivery.show'))--}}
                                        {{--<a href="{{route('delivery.show')}}"--}}
                                        {{--class="btn waves-effect waves-light btn-rounded btn-sm btn-success">--}}
                                        {{--Show--}}
                                        {{--</a>--}}
                                        {{--@endif--}}

                                        {{--@if(Auth::user()->getPermissionByName('delivery.exportPDF'))--}}
                                        {{--<a href="{{route('delivery.exportPDF')}}?id="--}}
                                        {{--class="btn waves-effect waves-light btn-rounded btn-sm btn-success">--}}
                                        {{--Export PDF--}}
                                        {{--</a>--}}
                                        {{--@endif--}}
                                        {{--@if(Auth::user()->getPermissionByName('delivery.exportADR'))--}}
                                        {{--<a href="{{route('delivery.exportADR')}}?id="--}}
                                        {{--class="btn waves-effect waves-light btn-rounded btn-sm btn-success">--}}
                                        {{--Export ADR--}}
                                        {{--</a>--}}
                                        {{--@endif--}}

                                        {{--@if(Auth::user()->getPermissionByName('delivery.destroy'))--}}
                                        {{--                                        {{ Form::hidden('_method', 'DELETE') }}--}}
                                        <div class="btn-group">
                                            <button type="button"
                                                    class="btn btn-success dropdown-toggle waves-effect waves-light"
                                                    data-toggle="dropdown" aria-expanded="false"><i
                                                        class="fa fa-ellipsis-h"></i>
                                            </button>
                                            <ul class="dropdown-menu" role="menu">
                                                <li><a href="#">Show</a></li>
                                                {{--<li><a href="#">Delete</a></li>--}}
                                                <li><a href="{{route('withdraw.exportPDF')}}?id=">
                                                        Export PDF
                                                    </a></li>
                                            </ul>
                                        </div>
                                        {{--@endif--}}
                                    </div>
                                    {{--                                    {{ Form::close() }}--}}
                                </td>
                            </tr>
                            {{--@endforeach--}}
                            </tbody>
                        </table>
                        {{--{{ $datas->links("pagination::bootstrap-4") }}--}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@include('Warehouse.Cabinet.Withdraw.modal')
@include('layouts.alert')

@push('styles')
    <link href="{{asset('assets/plugins/select2/css/select2.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('assets/plugins/bootstrap-daterangepicker/daterangepicker.css')}}" rel="stylesheet">
    <link href="{{asset('assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css')}}" rel="stylesheet">
@endpush

@push('scripts')
    <script src="{{asset('assets/plugins/moment/moment.js')}}"></script>
    <script src="{{asset('assets/plugins/select2/js/select2.min.js')}}"></script>
    <script src="{{asset('assets/plugins/bootstrap-daterangepicker/daterangepicker.js')}}"></script>
    <script src="{{asset('assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>
    <script>
        $('document').ready(function () {
            $("#con-close-modal").on("hidden.bs.modal", function () {
                $("#field").html("");
            });

            $('#request-date-range, #withdraw-date-range, #propose-date-range').datepicker({
                autoclose: true,
                todayHighlight: true,
                format: 'yyyy-mm-dd'
            });

            $('.approve').on('click', function () {
                let id = $(this).data('id');
                let status = 2;
                let approval = 1;
                swal({
                    title: "Are you sure?",
                    text: "You will not be able to recover this data!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Yes",
                    cancelButtonText: "No",
                    closeOnConfirm: false,
                    closeOnCancel: false,
                    showLoaderOnConfirm:true
                }, function (isConfirm) {
                    if (!isConfirm) notif({type:"success",title:"Change Status", message:"You didn't change status"});
                    sendData('/pull_self/status/' + id, "POST", {
                        status: status,
                        approval_status: approval,
                        '_token':'{{ csrf_token() }}'
                    });
                });
            });

            $('.reject').on('click', function () {
                $('#withdraw_id').val($(this).data('id'));
                $('.modal-title').text('Reject status');
                $('#form').find('#field').remove().end();
                $('#form').append('<div class="row" id="field">\n' +
                    '                    <div class="col-md-12">\n' +
                    '                        <div class="form-group no-margin">\n' +
                    '                            <label for="field-7" class="control-label">Reason</label>\n' +
                    '                            <textarea class="form-control autogrow" id="field-7" placeholder="Write something reason" style="overflow: hidden; word-wrap: break-word; resize: horizontal; height: 104px;"></textarea>\n' +
                    '                        </div>\n' +
                    '                    </div>\n' +
                    '                </div>');
                $('#con-close-modal').modal('show');
                $('#submit').on('click', function () {
                    let data = {status: 6, approval_status: 2, reason: $("#field-7").val(), '_token':'{{ csrf_token() }}'};
                    swal({
                        title: "Are you sure?",
                        text: "You will not be able to recover this data!",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonText: "Yes",
                        cancelButtonText: "No",
                        closeOnConfirm: false,
                        closeOnCancel: false,
                        showLoaderOnConfirm:true
                    }, function (isConfirm) {
                        if (!isConfirm) notif({type:"success",title:"Change Status", message:"You didn't change status"});
                        sendData('/pull_self/status/' + $('#withdraw_id').val(), "POST", data);
                    });
                });
            });

            function notif(result) {
                swal({
                    type: result.type,
                    title: result.title,
                    text: result.message,
                    timer: 3000,
                }, function () {
                    if(result.status){
                        location.reload();
                    }
                    $('#con-close-modal').modal('hide');
                });
            }

            function sendData(route, metode, data) {
                let result = {};
                $.ajax({
                    type: metode,
                    url: route,
                    data: data,
                    success: function (resp) {
                        result = resp;
                        notif(result);
                    },
                });
                return result;
            }

            $('.change').on('click', function () {
                $('#withdraw_id').val($(this).data('id'));
                $('.modal-title').text('Change Withdraw Date');
                $('#form').find('#field').remove().end();
                $('#form').append('<div class="row" id="field">\n' +
                    '                    <div class="col-md-12">\n' +
                    '                        <div class="form-group no-margin">\n' +
                    '                            <label for="field-7" class="control-label">Date</label>\n' +
                    '                            <input type="text" class="form-control" placeholder="yyyy-mm-dd" id="datepicker-autoclose">\n' +
                    '                        </div>\n' +
                    '                    </div>\n' +
                    '                </div>');
                $('#status').val('insert');
                $('#con-close-modal').modal('show');
                $('#datepicker-autoclose').datepicker({
                    autoclose: true,
                    todayHighlight: true,
                    format: 'yyyy-mm-dd'
                });

            });
        });
    </script>
@endpush