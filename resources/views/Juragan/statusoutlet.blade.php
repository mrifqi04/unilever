@extends('layouts.app')

@section('content')

    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card-box">
                        <ol class="breadcrumb">
                            <li>
                                Juragan
                            </li>
                            <li class="active">
                                <a href="{{route('juragan.status_approval_outlet')}}">Status Approval Outlet</a>
                            </li>
                        </ol>
                        <h4 class="m-t-0 header-title"><b>Data</b></h4>

                        <button class="btn btn-primary  d-none d-lg-block" type="button" data-toggle="collapse"
                                data-target="#filter_data" aria-expanded="false" aria-controls="collapseExample">
                            <i class="fa fa-arrow-circle-down"></i> Filter
                        </button>

                        @if(Auth::user()->getPermissionByName('juragan.export-validasi-toko'))
                            <a href="#modal-export-validasi-toko"
                               data-animation="fadein" data-plugin="custommodal"
                               data-overlayspeed="200" data-overlaycolor="#36404a"
                               class="btn btn-info waves-effect waves-light">
                                Download Validasi Toko
                            </a>
                            <!-- Modal -->
                            <div id="modal-export-validasi-toko" class="modal-demo">
                                <button type="button" class="close" onclick="Custombox.close();">
                                    <span>&times;</span><span class="sr-only">Close</span>
                                </button>
                                <h4 class="custom-modal-title">Download Validasi Toko</h4>
                                <div class="modal-body">
                                    <div class="container-fluid">
                                        <form method="get" action="{{route('juragan.export-validasi-toko')}}" role="form"
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

                        @if(Auth::user()->getPermissionByName('juragan.export-kirim-mandiri'))
                            <a href="#modal-export-kirim-mandiri"
                               data-animation="fadein" data-plugin="custommodal"
                               data-overlayspeed="200" data-overlaycolor="#36404a"
                               class="btn btn-info waves-effect waves-light">
                                Download Tambah Mandiri
                            </a>
                            <!-- Modal -->
                            <div id="modal-export-kirim-mandiri" class="modal-demo">
                                <button type="button" class="close" onclick="Custombox.close();">
                                    <span>&times;</span><span class="sr-only">Close</span>
                                </button>
                                <h4 class="custom-modal-title">Download Tambah Mandiri</h4>
                                <div class="modal-body">
                                    <div class="container-fluid">
                                        <form method="get" action="{{route('juragan.export-kirim-mandiri')}}" role="form"
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

                        <div class="collapse" id="filter_data">
                            <div class="card-box">
                                <div class="card-view">
                                    {{ Form::open(array('url' => route('juragan.status_approval_outlet'), 'role' => 'form', 'method' => 'get','autocomplete'=>'off')) }}
                                    <div class="form-group">
                                        {{ Form::label('search', 'Search', array('class' => 'col-2 col-form-label')) }}
                                        <div class="col-10">
                                            {{ Form::text('search', old('name', app('request')->get('search')), array('class' => 'form-control validation-id', 'placeholder'=>'Juragan ID, Outlet ID, Juragan Name, Outlet Name, Owner, Phone')) }}
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        {{ Form::label('provinces', 'Province', array('class' => 'col-2 col-form-label')) }}
                                        <div class="col-10">
                                            {{ Form::select('provinces', [], null, ['class' => 'form-control validation-province_id']) }}
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        {{ Form::label('cities', 'City', array('class' => 'col-2 col-form-label')) }}
                                        <div class="col-10">
                                            {{ Form::select('cities', [], null, ['class' => 'form-control validation-city_id']) }}
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-2">Date</label>
                                        <div class="col-10">
                                            <div class="input-daterange input-group" id="date-range">
                                                <input type="text" class="form-control validation-start"
                                                       name="start_date" placeholder="Start Date">
                                                <span class="input-group-addon bg-custom b-0 text-white">to</span>
                                                <input type="text" class="form-control validation-end" name="end_date"
                                                       placeholder="End Date">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-10">
                                            <button type="submit" class="btn btn-success"><i class="fa fa-check"></i>
                                                Search
                                            </button>
                                            <a href="#" class="btn btn-info outlet-export">
                                                <i class="fa fa-check"></i> Export
                                            </a>
                                        </div>
                                    </div>
                                    {{ Form::close() }}
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover table-responsive">
                                <thead>
                                <tr>
                                    <th>Kota</th>
                                    <th>Juragan</th>
                                    <th>ID Juragan</th>
                                    <th>ID Toko</th>
                                    <th>Nama Toko</th>
                                    <th>Pemilik Toko</th>
                                    <th>Alamat</th>
                                    <th>No Telp</th>
                                    <th>Status</th>
                                    <th>Tgl Request</th>
                                    <th>Tgl Jadwal Kirim</th>
                                    <th colspan="3" class="text-center">Remark</th>
                                </tr>
                                </thead>

                                <tbody>
                                @foreach($datas as $data)
                                    <tr>
                                        <td>{{$data->city}}</td>
                                        <td>{{$data->juragan}}</td>
                                        <td>{{$data->juragan_id}}</td>
                                        <td>{{$data->outlet_id}}</td>
                                        <td>{{$data->outlet_name}}</td>
                                        <td>{{$data->owner}}</td>
                                        <td>{{$data->address}}</td>
                                        <td>{{$data->phone}}</td>
                                        <td>{{$data->section." - ".$data->value}}</td>
                                        <td>{{$data->recommend_date}}</td>
                                        <td>{{$data->send_date}}</td>
                                        <td class="text-center">
                                            @if($data->is_mandiri == 1)
                                                Toko Mandiri
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        {{ $datas->links("pagination::bootstrap-4") }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@include('layouts.alert')

@push('styles')
    <link href="{{asset('assets/plugins/select2/css/select2.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css')}}" rel="stylesheet">
    <link href="{{asset('assets/plugins/bootstrap-daterangepicker/daterangepicker.css')}}" rel="stylesheet"
          type="text/css">
@endpush

@push('scripts')
    <script src="{{asset('assets/plugins/select2/js/select2.min.js')}}"></script>
    <script src="{{asset('assets/plugins/moment/moment.js')}}"></script>
    <script src="{{asset('assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>
    <script src="{{asset('assets/plugins/bootstrap-daterangepicker/daterangepicker.js')}}"></script>
    <script src="{{asset('assets/plugins/timepicker/bootstrap-timepicker.js')}}"></script>
    <script src="{{asset('assets/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js')}}"></script>
    <script src="{{asset('assets/plugins/clockpicker/js/bootstrap-clockpicker.min.js')}}"></script>
    <script src="{{asset('assets/pages/jquery.form-pickers.init.js')}}"></script>
    <script>
        $('document').ready(function () {

            function getProvince() {
                url = "{{route("geo.province")}}";
                $.ajax({
                    url: url,
                    method: "GET",
                    success: function (data) {
                        var $select = $("select#provinces");
                        $select.select2({});
                        var $option = $('<option selected>Select Province</option>').val("");
                        $select.append($option).trigger('change');
                        var items = $.map(data, function (obj) {
                            obj.id = obj.id;
                            obj.text = obj.text || obj.name;

                            return obj;
                        });
                        $select.select2({data: items});
                    }
                });
            }

            getProvince();

            $("select#provinces").on("change", function (event) {
                var $select = $("select#cities");
                $select.html('');
                $select.select2({});
                var $option = $('<option selected>Select City</option>').val("");
                $select.append($option).trigger('change');
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
                        $select.select2({data: items});
                    }
                });
            });

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

@push('styles')
    <link href="{{asset('assets/plugins/custombox/css/custombox.css')}}" rel="stylesheet">
@endpush

@push('scripts')
    <!-- Modal-Effect -->
    <script src="{{asset('assets/plugins/custombox/js/custombox.min.js')}}"></script>
    <script src="{{asset('assets/plugins/custombox/js/legacy.min.js')}}"></script>
@endpush

@push('scripts')
    <script type="text/javascript">
        $(document).ready(function () {
            $("#fromDate,#toDate").datepicker({"format": "yyyy-mm-dd"});
            $("#fromDate,#toDate").datepicker('update', new Date());
        })
    </script>
@endpush