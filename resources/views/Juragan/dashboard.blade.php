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
                                <a href="{{route('juragan.dashboard')}}">Dashboard</a>
                            </li>
                        </ol>

                        <div class="row">
                            <h4 class="m-t-0 header-title">
                                <b>Summary</b>
                            </h4>

                            <button class="btn btn-primary  d-none d-lg-block" type="button" data-toggle="collapse"
                                    data-target="#filter_data" aria-expanded="false" aria-controls="collapseExample">
                                <i class="fa fa-arrow-circle-down"></i> Filter
                            </button>

                            <div class="collapse" id="filter_data">
                                <div class="card-box">
                                    <div class="card-view">
                                        {{ Form::open(array('url' => "#", 'role' => 'form', 'method' => 'get', 'class'=>'summary-form form-horizontal','autocomplete'=>'off')) }}

                                        <div class="form-group">
                                            <label class="control-label col-sm-2">Date</label>
                                            <div class="col-sm-10">
                                                <div class="input-daterange input-group" id="date-range">
                                                    <input type="text" class="form-control summary-start"
                                                           name="start_date" placeholder="Start Date">
                                                    <span class="input-group-addon bg-custom b-0 text-white">to</span>
                                                    <input type="text" class="form-control summary-end" name="end_date"
                                                           placeholder="End Date">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <button type="submit" class="btn btn-success search-summary">
                                                    <i class="fa fa-check"></i> Search
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
                                    <th>Kota</th>
                                    <th>Juragan Aktif</th>
                                    <th>Jumlah Toko Baru</th>
                                    <th>Divalidasi</th>
                                    <th>Toko Approved</th>
                                    <th>Toko Baru Mandiri</th>
                                    <th>Toko Tunda</th>
                                    <th>Request Tarik</th>
                                    <th>Request Tukar Guling</th>
                                </tr>
                                </thead>

                                <tbody id="summary">
                                </tbody>
                            </table>
                        </div>

                        <div class="row">
                            <h4 class="m-t-0 header-title">
                                <b>Status Validasi Toko</b>
                            </h4>

                            <button class="btn btn-primary  d-none d-lg-block" type="button" data-toggle="collapse"
                                    data-target="#filter_data_validation" aria-expanded="false"
                                    aria-controls="collapseExample">
                                <i class="fa fa-arrow-circle-down"></i> Filter
                            </button>

                            <div class="collapse" id="filter_data_validation">
                                <div class="card-box">
                                    <div class="card-view">
                                        {{ Form::open(array('url' => '#', 'role' => 'form', 'method' => 'get', 'class'=>'validation-form','autocomplete'=>'off')) }}
                                        <div class="form-group">
                                            {{ Form::label('provinces', 'Province', array('class' => 'col-2 col-form-label')) }}
                                            <div class="col-10">
                                                {{ Form::select('provinces', [], null, ['placeholder' => 'Pilih Province', 'class' => 'form-control']) }}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {{ Form::label('cities', 'City', array('class' => 'col-2 col-form-label')) }}
                                            <div class="col-10">
                                                {{ Form::select('cities', [], null, ['placeholder' => 'Pilih City', 'class' => 'form-control validation-city_id']) }}
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            {{ Form::label('juragan_id', 'Juragan Name', array('class' => 'col-2 col-form-label')) }}
                                            <div class="col-10">
                                                {{ Form::text('juragan_id', old('name', app('request')->get('juragan_id')), array('class' => 'form-control validation-id', 'placeholder'=>'Juragan Name')) }}
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-2">Date</label>
                                            <div class="col-10">
                                                <div class="input-daterange input-group" id="date-range-validation">
                                                    <input type="text" class="form-control validation-start"
                                                           name="start_date" placeholder="Start Date">
                                                    <span class="input-group-addon bg-custom b-0 text-white">to</span>
                                                    <input type="text" class="form-control validation-end"
                                                           name="end_date" placeholder="End Date">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="col-6">
                                                <button type="submit" class="btn btn-success"><i
                                                            class="fa fa-check"></i> Search
                                                </button>
                                                <a href="#" class="btn btn-info validation-export">
                                                    <i class="fa fa-check"></i> Export
                                                </a>
                                            </div>
                                        </div>
                                        {{ Form::close() }}
                                    </div>
                                </div>
                            </div>

                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th>Kota</th>
                                    <th>Juragan</th>
                                    <th>ID Juragan</th>
                                    <th>Jumlah Toko Baru</th>
                                    <th>Divalidasi</th>
                                    <th>Toko Approved</th>
                                    <th>Toko Baru Mandiri</th>
                                    <th>Toko Tunda</th>
                                    <th>Request Tarik</th>
                                    <th>Request Tukar Guling</th>
                                </tr>
                                </thead>

                                <tbody id="outlet_validation">
                                </tbody>
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
            $('#date-range-validation').datepicker({
                toggleActive: true
            });

            function getProvince() {
                url = "{{route("geo.province")}}";
                $.ajax({
                    url: url,
                    method: "GET",
                    success: function (data) {
                        var items = $.map(data, function (obj) {
                            obj.id = obj.id;
                            obj.text = obj.text || obj.name;

                            return obj;
                        });
                        fillSelectData("select#provinces", items);
                    }
                });
            }

            getProvince();

            $("select#provinces").select2({
                placeholder: "Select Province",
            });
            $("select#cities").select2({
                placeholder: "Select City",
            });

            $("select#provinces").on("change", function (event) {
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
                        fillSelectData("select#cities", items);
                    }
                });
            });

            function fillSelectData(selectorId, datas) {
                obj = $(selectorId);
                obj.html('').select2({data: datas});
                $(selectorId).trigger('change');
            }

            // summary form
            $(".summary-form").on("submit", function (ev) {
                ev.stopPropagation();
                ev.preventDefault();
                startDate = $("input.summary-start").val();
                endDate = $("input.summary-end").val();
                GetDataSummaryJuragan(startDate, endDate);

            });

            function GetDataSummaryJuragan(startDate, endDate) {
                let url;

                url = "{{route("juragan.dashboard_juragan")}}?start_date=" + startDate + "&end_date=" + endDate;
                $.ajax({
                    url: url,
                    method: "GET",
                    success: function (data) {
                        if (data.data.length > 0) {
                            // remove old data
                            $("tbody#summary tr").remove();
                            $.each(data.data, function (key, val) {
                                mainElement = $("tbody#summary");
                                newContent = "<tr>";
                                newContent += '<td class="juragan_city-' + val.id + '">' + val.city_name + '</td>';
                                newContent += '<td class="juragan_total-' + val.id + '">' + val.total + '</td>';
                                newContent += '<td class="outlet-' + val.id + '">' + val.total_outlet + '</td>';
                                newContent += '<td class="outlet_deal-' + val.id + '">' + val.total_outlet_deal + '</td>';
                                newContent += '<td class="outlet_approved-' + val.id + '">' + val.total_outlet_approved + '</td>';
                                newContent += '<td class="outlet_mandiri-' + val.id + '">' + val.total_outlet_mandiri + '</td>';
                                newContent += '<td class="outlet_tunda-' + val.id + '">' + val.total_outlet_tunda + '</td>';
                                newContent += '<td>-</td>';
                                newContent += '<td>-</td>';
                                newContent += "</tr>";
                                $("td.outlet-" + val.id).html(val.total);
                                mainElement.append(newContent);
                            });
                            // load other data
                            // GetDataSummary(startDate, endDate);
                        }
                    }
                });
            }

            {{--function GetDataSummary(startDate, endDate){--}}
            {{--let url;--}}

            {{--url = "{{route("juragan.dashboard_outlet")}}?start_date="+startDate+"&end_date="+endDate;--}}
            {{--$.ajax({--}}
            {{--url:url,--}}
            {{--method:"GET",--}}
            {{--success: function (data) {--}}
            {{--if(data.data.length > 0 ){--}}
            {{--$.each(data.data, function (key, val) {--}}
            {{--$("td.outlet-"+val.id).html(val.total);--}}
            {{--});--}}
            {{--}--}}
            {{--}--}}
            {{--});--}}

            {{--url = "{{route("juragan.dashboard_outlet_mandiri")}}?start_date="+startDate+"&end_date="+endDate;--}}
            {{--$.ajax({--}}
            {{--url:url,--}}
            {{--method:"GET",--}}
            {{--success: function (data) {--}}
            {{--if(data.data.length > 0 ){--}}
            {{--$.each(data.data, function (key, val) {--}}
            {{--$("td.outlet_mandiri-"+val.id).html(val.total);--}}
            {{--});--}}
            {{--}--}}
            {{--}--}}
            {{--});--}}

            {{--url = "{{route("juragan.dashboard_outlet_progress")}}?start_date="+startDate+"&end_date="+endDate;--}}
            {{--$.ajax({--}}
            {{--url:url,--}}
            {{--method:"GET",--}}
            {{--success: function (data) {--}}
            {{--if(data.data.length > 0 ){--}}
            {{--$.each(data.data, function (key, val) {--}}
            {{--if(val.value == "Deal"){--}}
            {{--$("td.outlet_deal-"+val.id).html(val.total);--}}
            {{--}else if(val.value == "Tunda"){--}}
            {{--$("td.outlet_tunda-"+val.id).html(val.total);--}}
            {{--}else{--}}
            {{--$("td.outlet_approved-"+val.id).html(val.total);--}}
            {{--}--}}

            {{--});--}}
            {{--}--}}
            {{--}--}}
            {{--});--}}
            {{--}--}}

            // validation form
            $(".validation-form").on("submit", function (ev) {
                ev.stopPropagation();
                ev.preventDefault();
                startDate = $("input.validation-start").val();
                endDate = $("input.validation-end").val();
                cityId = $("select.validation-city_id").val();
                id = $("input.validation-id").val();
                GetDataValidationOutlet(startDate, endDate, id, cityId);

            });

            function GetDataValidationOutlet(startDate, endDate, id, cityId) {
                let url;
                let idData;
                let cityData;
                if (id != "") {
                    idData = "&id=" + id;
                } else {
                    idData = "";
                }
                if (cityId != "") {
                    cityData = "&city_id=" + cityId;
                } else {
                    cityData = "";
                }

                url = "{{route("juragan.dashboard_status_outlet")}}?start_date=" + startDate + "&end_date=" + endDate + idData + cityData;
                $.ajax({
                    url: url,
                    method: "GET",
                    success: function (data) {
                        if (data.data.length > 0) {
                            // remove old data
                            $("tbody#outlet_validation tr").remove();
                            $.each(data.data, function (key, val) {
                                mainElement = $("tbody#outlet_validation");
                                newContent = "<tr>";
                                newContent += '<td class="juragan_city-' + val.id + '">' + val.city_name + '</td>';
                                newContent += '<td class="juragan_name">' + val.name + '</td>';
                                newContent += '<td class="id_juragan">' + val.id + '</td>';
                                newContent += '<td class="outlet-' + val.id + '">' + val.total_outlet + '</td>';
                                newContent += '<td class="outlet_deal-' + val.id + '">' + val.total_outlet_deal + '</td>';
                                newContent += '<td class="outlet_approved-' + val.id + '">' + val.total_outlet_approved + '</td>';
                                newContent += '<td class="outlet_mandiri-' + val.id + '">' + val.total_outlet_mandiri + '</td>';
                                newContent += '<td class="outlet_tunda-' + val.id + '">' + val.total_outlet_tunda + '</td>';
                                newContent += '<td>-</td>';
                                newContent += '<td>-</td>';
                                newContent += "</tr>";
                                $("td.outlet-" + val.id).html(val.total);
                                mainElement.append(newContent);
                            });
                        }
                    }
                });
            }

            // validation export
            $(".validation-export").on("click", function (ev) {
                ev.stopPropagation();
                ev.preventDefault();
                var url = "{{route('juragan.export_status_outlet')}}?";
                var startDate = $(".validation-form [name='start_date']").val();
                var endDate = $(".validation-form [name='end_date']").val();
                var cityId = $(".validation-form [name='cities']").val();
                var id = $(".validation-form [name='juragan_id']").val();

                // if (startDate != "") {
                //     url += "start_date=" + startDate;
                // }
                // if (endDate != "") {
                //     url += "&end_date=" + endDate;
                // }
                // if (id != "") {
                //     url += "&id=" + id;
                // }
                // if (cityId != "") {
                //     url += "&city_id=" + cityId;
                // }
                url += "start_date=" + startDate + "&end_date=" + endDate + "&id=" + id + "&city_id=" + cityId;
                window.open(url);
            });

        });


    </script>
@endpush