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
                                <a href="{{route('pull_self.index')}}">Self exchange</a>
                            </li>
                        </ol>
                        <h4 class="m-t-0 header-title"><b>Data</b></h4>

                        <button class="btn btn-primary  d-none d-lg-block" type="button" data-toggle="collapse"
                                data-target="#filter_data" aria-expanded="false" aria-controls="collapseExample">
                            <i class="fa fa-arrow-circle-down"></i> Filter
                        </button>

                        @if(Auth::user()->getPermissionByName('pull_self.exportTukarMandiri'))
                            {{--<a href="{{route('pull_self.exportTukarMandiri',['search' => old('search', app('request')->get('search'))])}}"--}}
                               {{--class="btn btn-info waves-effect waves-light">--}}
                                {{--Export Data--}}
                            {{--</a>--}}
                            <a href="#modal-export-survey"
                               data-animation="fadein" data-plugin="custommodal"
                               data-overlayspeed="200" data-overlaycolor="#36404a"
                               class="btn btn-info waves-effect waves-light">
                                Download Data Survey
                            </a>
                            <!-- Modal -->
                            <div id="modal-export-survey" class="modal-demo">
                                <button type="button" class="close" onclick="Custombox.close();">
                                    <span>&times;</span><span class="sr-only">Close</span>
                                </button>
                                <h4 class="custom-modal-title">Download Data Survey</h4>
                                <div class="modal-body">
                                    <div class="container-fluid">
                                        <form method="get" action="{{route('pull_self.exportTukarMandiri')}}" role="form"
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

                        @if(Auth::user()->getPermissionByName('pull_self.exportActivity'))
                            <a href="#modal-export-activity"
                               data-animation="fadein" data-plugin="custommodal"
                               data-overlayspeed="200" data-overlaycolor="#36404a"
                               class="btn btn-info waves-effect waves-light">
                                Download Data Activity
                            </a>
                            <!-- Modal -->
                            <div id="modal-export-activity" class="modal-demo">
                                <button type="button" class="close" onclick="Custombox.close();">
                                    <span>&times;</span><span class="sr-only">Close</span>
                                </button>
                                <h4 class="custom-modal-title">Download Data Activity</h4>
                                <div class="modal-body">
                                    <div class="container-fluid">
                                        <form method="get" action="{{route('pull_self.exportActivity')}}" role="form"
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
                                    {{ Form::open(array('url' => route('pull_self.index'), 'role' => 'form', 'method' => 'get','autocomplete'=>'off')) }}
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
                                    <div class="row m-t-10">
                                        <div class="form-group col-md-12">
                                            <button class="btn btn-success"><i class="fa fa-check"></i>
                                                Search
                                            </button>
                                            <a href="{{route('pull_self.index')}}"
                                               class="btn btn-white">Clear</a>
                                        </div>
                                    </div>
                                    {{--<div class="form-group">--}}
                                        {{--<div class="col-10">--}}
                                            {{--<a href="#" class="btn btn-info outlet-export">--}}
                                                {{--<i class="fa fa-check"></i> Export--}}
                                            {{--</a>--}}
                                        {{--</div>--}}
                                    {{--</div>--}}
                                    {{ Form::close() }}
                                </div>
                            </div>
                        </div>

                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>Juragan Code</th>
                                <th>Juragan Name</th>
                                <th>Org. Outlet Name</th>
                                <th></th>
                                <th>Dest. Outlet Name</th>
                                <th>Status</th>
                                <th>Request At</th>
                                <th>Withdrawal At</th>
                                <th>Exchange At</th>
                                <th colspan="3" class="text-center">Action</th>
                            </tr>
                            </thead>

                            <tbody>
                            @if($trx->count() > 0)
                                @foreach($trx as $data)
                                    <tr>
                                        <td>{{$data->juragan->id_unilever_owner}}</td>
                                        <td>{{$data->juragan->name}}</td>
                                        <td>{{$data->DetailMandiri->outlet->name}} </td>
                                        <td><i class="fa fa-arrow-right"></i></td>
                                        <td>{{$data->DetailMandiri->destoutlet->name}}</td>
                                        @switch($data->status_id)
                                            @case(1)
                                            @php($style = 'text-info')
                                            @break

                                            @case(2)
                                            @php($style = 'text-success')
                                            @break

                                            @case(3)
                                            @php($style = 'text-warning')
                                            @break

                                            @case(4)
                                            @php($style = 'text-warning')
                                            @break

                                            @case(5)
                                            @php($style = 'text-success')
                                            @break

                                            @case(6)
                                            @php($style = 'text-danger')
                                            @break
                                        @endswitch
                                        <td><em class="font-bold {{$style}}">{{$data->Status->name}}</em></td>
                                        <td>{{date('l, j F Y H:i', strtotime($data->created_at))}}</td>
                                        @foreach($data->DetailMandiri->ShippingMandiri()->get() as $sm)
                                            @if($sm->shipping_type_id == 1)
                                                <td>{{date('l, j F Y H:i', strtotime($sm->shipping_date))}}</td>
                                            @endif
                                            @if($sm->shipping_type_id == 2)
                                                <td>{{date('l, j F Y H:i', strtotime($sm->shipping_date))}}</td>
                                            @endif
                                            @php($ship['ship'][] = $sm->shipping_type_id)
                                            @php($ship['answer'][] = $sm->answer_id)
                                        @endforeach
                                        <td class="text-center">
                                            {{--{{ Form::open(array('url' => route('withdraw_cabinet.destroy', ['withdraw_cabinet'=>1]))) }}--}}
                                            <div class="button-list">
                                                @if($data->Status->id == 1)
                                                    <button class="btn waves-effect waves-light btn-sm btn-default approve"
                                                            data-id="{{$data->id}}">
                                                        Approve
                                                    </button>
                                                    <button class="btn waves-effect waves-light btn-sm btn-danger reject"
                                                            data-id="{{$data->id}}">
                                                        Reject
                                                    </button>
                                                @endif
                                                @if(Auth::user()->roles[0]->id == '64132dec-322e-474a-a003-4c68ccc510fd')
                                                    @if($data->Status->id == 2 && in_array(1, $ship['ship']) && in_array("", $ship['answer']) && $data->Approval->asm_user_id == "")
                                                        <button class="btn waves-effect waves-light btn-sm btn-default approve"
                                                                data-id="{{$data->id}}">
                                                            Approve
                                                        </button>
                                                        <button class="btn waves-effect waves-light btn-sm btn-danger reject"
                                                                data-id="{{$data->id}}">
                                                            Reject
                                                        </button>
                                                    @endif
                                                @endif

                                                {{--@if(Auth::user()->getPermissionByName('delivery.destroy'))--}}
                                                {{--{{ Form::hidden('_method', 'DELETE') }}--}}
                                                <div class="btn-group">
                                                    <button type="button"
                                                            class="btn btn-success btn-sm dropdown-toggle waves-effect waves-light"
                                                            data-toggle="dropdown" aria-expanded="false"><i
                                                                class="fa fa-ellipsis-h"></i>
                                                    </button>
                                                    <ul class="dropdown-menu" role="menu">
                                                        <li>
                                                            <a href="{{route('pull_self.show', ['pull_self' => $data->id])}}">Show</a>
                                                        </li>
                                                        {{--<li><a href="#">Delete</a></li>--}}
                                                    </ul>
                                                </div>
                                                {{--@endif--}}
                                            </div>
                                            {{--                                    {{ Form::close() }}--}}
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                        {{ $trx->links("pagination::bootstrap-4") }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('Warehouse.Cabinet.Self.modal')
@endsection

@include('layouts.alert')

@push('styles')
    <link href="{{asset('assets/plugins/select2/css/select2.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('assets/plugins/bootstrap-daterangepicker/daterangepicker.css')}}" rel="stylesheet">
    <link href="{{asset('assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css')}}" rel="stylesheet">
    <link href="{{asset('assets/plugins/custombox/css/custombox.css')}}" rel="stylesheet">
@endpush

@push('scripts')
    <script src="{{asset('assets/plugins/moment/moment.js')}}"></script>
    <script src="{{asset('assets/plugins/select2/js/select2.min.js')}}"></script>
    <script src="{{asset('assets/plugins/bootstrap-daterangepicker/daterangepicker.js')}}"></script>
    <script src="{{asset('assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>
    <!-- Modal-Effect -->
    <script src="{{asset('assets/plugins/custombox/js/custombox.min.js')}}"></script>
    <script src="{{asset('assets/plugins/custombox/js/legacy.min.js')}}"></script>
    <script>
        $('document').ready(function () {
            $("#con-close-modal").on("hidden.bs.modal", function () {
                $("#field").html("");
            });

            $("#fromDate,#toDate").datepicker({"format": "yyyy-mm-dd"});
            $("#fromDate,#toDate").datepicker('update', new Date());

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
                    showLoaderOnConfirm: true
                }, function (isConfirm) {
                    if (!isConfirm) notif({
                        type: "success",
                        title: "Change Status",
                        message: "You didn't change status"
                    });
                    sendData('/pull_self/status/' + id, "POST", {
                        status: status,
                        approval_status: approval,
                        '_token': '{{ csrf_token() }}'
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
                    let data = {
                        status: 6,
                        approval_status: 2,
                        reason: $("#field-7").val(),
                        '_token': '{{ csrf_token() }}'
                    };
                    swal({
                        title: "Are you sure?",
                        text: "You will not be able to recover this data!",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonText: "Yes",
                        cancelButtonText: "No",
                        closeOnConfirm: false,
                        closeOnCancel: false,
                        showLoaderOnConfirm: true
                    }, function (isConfirm) {
                        if (!isConfirm) notif({
                            type: "success",
                            title: "Change Status",
                            message: "You didn't change status"
                        });
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
                    if (result.status) {
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

            $(".outlet-export").on("click", function (ev) {
                ev.stopPropagation();
                ev.preventDefault();
                url = "{{route('juragan.status_approval_outlet')}}?";
                startDate = $("input.validation-start").val();
                endDate = $("input.validation-end").val();
                provinceId = $("select.validation-province_id").val();
                cityId = $("select.validation-city_id").val();
                id = $("input.validation-id").val();
                juragan = $("input.validation-name").val();
                outletName = $("input.validation-outlet").val();

                url += "export=true";
                if (startDate != "") {
                    url += "&start_date=" + startDate;
                }
                if (endDate != "") {
                    url += "&end_date=" + endDate;
                }
                if (id != "") {
                    url += "&juragan_id=" + id;
                }
                if (name != "") {
                    url += "&juragan_name=" + juragan;
                }
                if (outletName != "") {
                    url += "&outlet_name=" + outletName;
                }
                if (provinceId != "") {
                    url += "&provinces=" + provinceId;
                }
                if (cityId != "") {
                    url += "&cities=" + cityId;
                }
                window.open(url);
            });
        });
    </script>
@endpush