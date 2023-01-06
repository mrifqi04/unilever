@extends('layouts.app')

@section('content')

    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card-box">
                        <ol class="breadcrumb">
                            <li>
                                Call Center
                            </li>
                            <li class="active">
                                <a href="{{route('callcenter.pull-cabinet.index')}}">Tarik Kabinet</a>
                            </li>
                        </ol>
                        <h4 class="m-t-0 header-title"><b>Data</b></h4>
                        <button class="btn btn-primary  d-none d-lg-block" type="button" data-toggle="collapse"
                                data-target="#filter_data" aria-expanded="{{$in_search === true ? 'true' : 'false'}}"
                                aria-controls="collapseExample">
                            <i class="fa fa-arrow-circle-down"></i> Filter
                        </button>
                        @if($canExportOutlet)
                            <a href="{{route('callcenter.pull-cabinet.export-outlet')}}" class="btn btn-primary  d-none d-lg-block">
                                <i class="fa fa-download"></i> Download Outlet
                            </a>
                        @endif
                        <div class="m-t-20">
                            <div class="collapse {{$in_search === true ? 'in' : ''}}"
                                 aria-expanded="{{$in_search === true ? 'true' : 'false'}}" id="filter_data">
                                <div class="card-box">
                                    <div class="card-view">
                                        {{ Form::open(['url' => route('callcenter.pull-cabinet.index'), 'role' => 'form', 'method' => 'get','class'=>'form-inline']) }}
                                        <div class="row">
                                            <div class="form-group col-md-3">
                                                {{ Form::select('id_province', $provinces, old('id_province', $id_province),
                                                    ['class' => 'form-control', 'placeholder'=>'Province']) }}
                                            </div>
                                            <div class="form-group col-md-3">
                                                {{ Form::select('id_city', $cities, old('id_city', $id_city),
                                                    ['class' => 'form-control', 'placeholder'=>'City']) }}
                                            </div>
                                            <div class="form-group col-md-3">
                                                {{ Form::select('id_district', $districts, old('id_district', $id_district),
                                                    ['class' => 'form-control', 'placeholder'=>'District']) }}
                                            </div>
                                            <div class="form-group col-md-3">
                                                {{ Form::select('id_village', $villages, old('id_village', $id_village),
                                                ['class' => 'form-control', 'placeholder'=>'Village']) }}
                                            </div>
                                        </div>
                                        <div class="row m-t-10">
                                            <div class="form-group col-md-3">
                                                {{ Form::select('id_juragan', $juragans, old('id_juragan', $id_juragan),
                                                ['class' => 'form-control', 'placeholder'=>'Outlet']) }}
                                            </div>
                                            <div class="form-group col-md-3">
                                                {{ Form::select('id_outlet', $outlets, old('id_outlet', $id_outlet),
                                                ['class' => 'form-control', 'placeholder'=>'Outlet']) }}
                                            </div>
                                            <div class="form-group col-md-3">
                                                {{ Form::select('id_progress', $progress, old('id_progress', $id_progress),
                                                ['class' => 'form-control', 'placeholder'=>'Progress']) }}
                                            </div>
                                        </div>
                                        <div class="row m-t-10">
                                            <div class="form-group col-md-12">
                                                <button class="btn btn-success"><i class="fa fa-check"></i>
                                                    Search
                                                </button>
                                                <a href="{{route('callcenter.pull-cabinet.index')}}"
                                                   class="btn btn-white">Clear</a>
                                            </div>
                                        </div>
                                        {{ Form::close() }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover table-responsive">
                                <thead>
                                <tr>
                                    <th>Kota</th>
                                    <th>ID Juragan</th>
                                    <th>Juragan</th>
                                    <th>ID Toko</th>
                                    <th>Nama Toko</th>
                                    <th>Pemilik Toko</th>
                                    <th>Alamat</th>
                                    <th>No Telp</th>
                                    <th>Status Date</th>
                                    <th>Status</th>
                                    <th>Tgl Request</th>
                                    <th>Tgl Jadwal Tarik</th>
                                    <th colspan="3" class="text-center">Action</th>
                                </tr>
                                </thead>

                                <tbody>
                                @foreach($data as $outlet)
                                    <tr>
                                        <td>{{$outlet->city}}</td>
                                        <td>{{$outlet->juragan_id}}</td>
                                        <td>{{$outlet->juragan}}</td>
                                        <td>{{$outlet->outlet_id}}</td>
                                        <td>{{$outlet->outlet_name}}</td>
                                        <td>{{$outlet->owner}}</td>
                                        <td>{{$outlet->address}}</td>
                                        <td>{{$outlet->phone}}</td>
                                        <td>{{$outlet->created_date ? \Illuminate\Support\Carbon::parse($outlet->created_date)->format('Y-m-d'): ''}}</td>
                                        <td align="center">
                                            <span class="label label-{{isset($progressClass[$outlet->section.'#'.$outlet->status_progress]) ? $progressClass[$outlet->section.'#'.$outlet->status_progress]: 'inverse'}}"
                                                  style="padding: 5px">
                                                  {{isset($progress[$outlet->section.'#'.$outlet->status_progress]) ? $progress[$outlet->section.'#'.$outlet->status_progress] : ''}}
                                            </span>
                                        </td>
                                        <td>{{($outlet->recommend_date) ? \Illuminate\Support\Carbon::parse($outlet->recommend_date)->format('Y-m-d'): ''}}</td>
                                        <td>{{($outlet->send_date) ? \Illuminate\Support\Carbon::parse($outlet->send_date)->format('Y-m-d'): ''}}</td>
                                        <td class="text-center">
                                            @if($canShow)
                                                <a href="{{route('callcenter.pull-cabinet.show', ['id'=>$outlet->id ? $outlet->id : ''])}}"
                                                   title="show"
                                                   class="btn waves-effect waves-light btn-rounded btn-sm btn-success ion-edit">
                                                </a>
                                            @endif
                                            @if($canApprove)
                                                <form name="form-approve-{{$outlet->id}}"
                                                      action="{{route('callcenter.pull-cabinet.approve',['id' => $outlet->id])}}"
                                                      method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="button" data-id="{{$outlet->id}}"
                                                            name="button-approve"
                                                            class="btn waves-effect waves-light btn-rounded btn-sm btn-primary ion-checkmark"
                                                            title="approve">
                                                    </button>
                                                </form>
                                            @endif
                                            @if($canCancel)
                                                <form name="form-cancel-{{$outlet->id}}"
                                                      action="{{route('callcenter.pull-cabinet.cancel',['id' => $outlet->id])}}"
                                                      method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="reject_reason_id" value="{{$outlet->section !== 'callcenter' ? '' : (($outlet->status_progress == 4) ? $outlet->reject_reason_id : '')}}">
                                                    <button type="button" data-id="{{$outlet->id}}" name="button-cancel"
                                                            class="btn waves-effect waves-light btn-rounded btn-sm btn-danger ion-close"
                                                            title="cancel">
                                                    </button>
                                                </form>
                                            @endif
                                            @if($canPostpone)
                                            <form name="form-postpone-{{$outlet->id}}"
                                                    action="{{route('callcenter.pull-cabinet.postpone',['id' => $outlet->id])}}"
                                                    method="POST">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="reject_reason_id" value="{{$outlet->section !== 'callcenter' ? '' : (($outlet->status_progress == 3) ? $outlet->reject_reason_id : '')}}">
                                                <button type="button" data-id="{{$outlet->id}}" name="button-postpone"
                                                        class="btn waves-effect waves-light btn-rounded btn-sm btn-warning ion-clock"
                                                        title="postpone">
                                                </button>
                                            </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        {{ $data->appends(request()->input())->links("pagination::bootstrap-4") }}
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
    <link href="{{asset('assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css')}}" rel="stylesheet"
          type="text/css">
    <link href="{{asset('assets/plugins/select2/css/select2.css')}}" rel="stylesheet" type="text/css">
@endpush

@push('scripts')
    <script src="{{asset('assets/plugins/sweetalert2/sweetalert2.min.js')}}"></script>
    <!-- Modal-Effect -->
    <script src="{{asset('assets/plugins/custombox/js/custombox.min.js')}}"></script>
    <script src="{{asset('assets/plugins/custombox/js/legacy.min.js')}}"></script>
    <script src="{{asset('assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>
    <script src="{{asset('assets/plugins/select2/js/select2.min.js')}}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $(".date").datepicker({"format": "yyyy-mm-dd"});
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
                placeholder: "Progress"
            });
            $("select[name='id_province']").on("change", function (event) {
                url = "{{route("geo.city")}}" + "?province_id=" + this.value;
                $.ajax({
                    url: url,
                    method: "GET",
                    success: function (data) {
                        var items = $.map(data, function (obj) {
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

            $("select[name='id_city']").on("change", function (event) {
                url = "{{route("geo.district")}}" + "?city_id=" + this.value;
                $.ajax({
                    url: url,
                    method: "GET",
                    success: function (data) {
                        var items = $.map(data, function (obj) {
                            obj.id = obj.id;
                            obj.text = obj.text || obj.name;
                            return obj;
                        });
                        fillSelectData("select[name='id_district']", items);
                        emptySelectData("select[name='id_village']");
                    }
                });
            });

            $("select[name='id_district']").on("change", function (event) {
                url = "{{route("geo.village")}}" + "?district_id=" + this.value;
                $.ajax({
                    url: url,
                    method: "GET",
                    success: function (data) {
                        var items = $.map(data, function (obj) {
                            obj.id = obj.id;
                            obj.text = obj.text || obj.name;

                            return obj;
                        });
                        fillSelectData("select[name='id_village']", items);
                    }
                });
            });

            function fillSelectData(selectorId, datas) {
                obj = $(selectorId);
                obj.html('').select2({data: datas});
                $(selectorId).trigger('change');
            }

            function emptySelectData(selectorId) {
                obj = $(selectorId);
                obj.html('').select2({});
            }

            $("[name='button-approve']").click(function (e) {
                var id = $(this).data('id');
                swal({
                        title: "Outlet Approval",
                        text: "Approve Outlet?",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Approve",
                        cancelButtonText: "Cancel"
                    }).then(function (isConfirm) {
                        if (isConfirm) {
                            $("[name='form-approve-" + id + "']").submit();
                        }
                    }
                );
            });
            
            var listRejectReasons = <?php echo $rejectReasons; ?>;
            $("[name='button-cancel']").click(function (e) {
                var id             = $(this).data('id');
                var form           = $(this).closest('[name^=form-cancel]');
                var rejectReason   = form.find("[name=reject_reason_id]");
                var rejectReasonId = rejectReason.attr('value');
                swal({
                        title: "Outlet Approval",
                        text: "Cancel Outlet?",
                        type: "warning",
                        input: 'select',
                        inputOptions: listRejectReasons,
                        inputValue: rejectReasonId,
                        inputPlaceholder: 'Pilih Alasan',
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes",
                        cancelButtonText: "No",
                        inputValidator: (value) => {
                            return new Promise((resolve, reject) => {
                                if (value != '') {
                                    resolve();
                                } else {
                                    reject('Alasan wajib dipilih!');
                                }
                            });
                        }
                    }).then(function (result) {
                        if (result) {
                            rejectReason.attr('value', result);
                            form.submit();
                        }
                    }
                );
            });
            
            var listRejectReasons = <?php echo $rejectReasons; ?>;
            $("[name='button-postpone']").click(function (e) {
                var id             = $(this).data('id');
                var form           = $(this).closest('[name^=form-postpone]');
                var rejectReason   = form.find("[name=reject_reason_id]");
                var rejectReasonId = rejectReason.attr('value');
                swal({
                        title: "Outlet Approval",
                        text: "Tunda Outlet?",
                        type: "warning",
                        input: 'select',
                        inputOptions: listRejectReasons,
                        inputValue: rejectReasonId,
                        inputPlaceholder: 'Pilih Alasan',
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Tunda",
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
                    }).then(function (result) {
                        if (result) {
                            rejectReason.attr('value', result);
                            form.submit();
                        }
                    }
                );
            });
        });
    </script>
@endpush