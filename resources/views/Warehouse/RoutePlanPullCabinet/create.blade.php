@extends('layouts.app')
@section('content')

    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card-box">
                        <ol class="breadcrumb">
                            <li>
                                Route Plan
                            </li>
                            <li>
                                <a href="{{route('warehouse.route-plan-pull-cabinet.index')}}">Route Plan Management</a>
                            </li>
                            <li class="active">
                                Create
                            </li>
                        </ol>
                        <h4 class="m-t-0 header-title">
                            <b>Data Route Plan</b>
                        </h4>
                        {{ Html::ul($errors->all()) }}
                        {{ Form::open(array('url' => route('warehouse.route-plan-pull-cabinet.store'), 'role' => 'form')) }}

                        <div class="form-group">
                            {{ Form::label('provinces', 'Province', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::select('provinces', $provinces, null, ['placeholder' => 'Pilih Province', 'class' => 'form-control', 'required' => 'required']) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Pilih salah satu Provinsi, tidak boleh
                                    dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('cities', 'City', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::select('cities', [], null, ['placeholder' => 'Pilih City', 'class' => 'form-control', 'required' => 'required']) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Pilih salah satu, tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('delivery_date', 'Tanggal Penarikan', array('class' => 'col-2 col-form-label')) }}
                            <small class="form-text text-muted">
                                <span class="text-danger">*</span>
                            </small>
                            <div class="col-10 m-t-10">
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="yyyy-mm-dd"
                                            name="date-tarik" id="date-tarik" required>
                                    <span class="input-group-addon bg-custom b-0 text-white"><i
                                                class="icon-calender"></i></span>
                                </div><!-- input-group -->
                            </div>
                        </div> <!-- form-group -->

                        <!-- <div class="form-group">
                            {{ Form::label('date', 'Date', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                <div class="input-daterange input-group" id="date-range">
                                    <input type="text" class="form-control" name="start"/>
                                    <span class="input-group-addon bg-custom b-0 text-white">to</span>
                                    <input type="text" class="form-control" name="end"/>
                                </div>
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Pilih tanggal, tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div> -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    {{ Form::label('outlet', 'Outlet', array('class' => 'col-2 col-form-label')) }}
                                    <small class="form-text text-muted">
                                        <span class="text-danger">*</span> Isi salah satu atau semua, tidak boleh
                                        dikosongkan.
                                    </small>
                                    <div class="col-5 m-t-10">
                                        {{ Form::select('outlet[]', [], null, ['placeholder' => 'Pilih City', 'class' => 'form-control outlet', 'required' => 'required']) }}
                                    </div>
                                </div> <!-- form-group -->
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    {{ Form::label('cabinet', 'Cabinet', array('class' => 'col-2 col-form-label')) }}
                                    <small class="form-text text-muted">
                                        <span class="text-danger">*</span> Isi sesuai jumlah outlet, tidak boleh
                                        dikosongkan.
                                    </small>
                                    <div class="col-5 m-t-10">
                                        {{ Form::select('cabinet[]', [], null, ['placeholder' => 'Pilih City', 'class' => 'form-control cabinet', 'required' => 'required']) }}
                                        <small><i><b>Masukkan QR Code untuk tambah baru</b></i></small>
                                    </div>
                                </div> <!-- form-group -->
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    {{ Form::label('delivery_date', 'Tanggal Penarikan Akhir', array('class' => 'col-2 col-form-label')) }}
                                    <small class="form-text text-muted">
                                        <span class="text-danger">*</span> Isi sesuai jumlah outlet, tidak boleh
                                        dikosongkan.
                                    </small>
                                    <div class="col-10 m-t-10">
                                        <div class="input-group">
                                            <input type="text" class="form-control" placeholder="yyyy-mm-dd"
                                                name="reportrange[]" required>
                                            <span class="input-group-addon bg-custom b-0 text-white"><i
                                                        class="icon-calender"></i></span>
                                        </div><!-- input-group -->
                                    </div>
                                </div> <!-- form-group -->
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-10">
                                <button type="submit" class="btn btn-success m-t-20 pull-right"><i
                                            class="fa fa-check"></i> Save
                                </button>
                                <a class="btn btn-danger m-t-20" href="{{ route('warehouse.route-plan-pull-cabinet.index') }}"><i
                                            class="fa fa-times"></i> Cancel</a>
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

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
            $('[name="reportrange[]"]').datepicker({
                startDate: '0d',
                autoclose: true,
                todayHighlight: true,
                format: 'yyyy-mm-dd'
            });

            $('[name="date-tarik"]').datepicker({
                startDate: '0d',
                autoclose: true,
                todayHighlight: true,
                format: 'yyyy-mm-dd'
            });

            $("select#provinces").select2({
                placeholder: "Select Province",
            });

            $("select#cities").select2({
                placeholder: "Select Cities",
            });

            $("select.outlet").select2({
                placeholder: "Select Outlet",
            });

            $("select.cabinet").select2({
                placeholder: "Select Cabinet",
            });

            $('select.outlet').on("change", function () {
                let $optionwork = $('<option selected>Select Cabinet</option>').val("");
                $("select.cabinet").html('');
                $("select.cabinet").select2({});
                $("select.cabinet").select2({placeholder: "Please wait a moment ..."});
                $("select.cabinet").append($optionwork).trigger('change');
                url = "{{route("geo.cabinetByOutlet")}}" + "?outlet_id=" + $('select.outlet').val();
                $.ajax({
                    url: url,
                    method: "GET",
                    success: function (data) {
                        var items = $.map(data, function (obj) {
                            obj.id = obj.id.toString();
                            obj.text = obj.qrcode_by_auditor + obj.brand;

                            return obj;
                        });
                        $("select.cabinet").select2({
                            data: items,
                            tags: true,
                            createTag: function (params) {
                                var term = $.trim(params.term);
                                if (term === '') {
                                    return null;
                                }

                                return {
                                    id: term,
                                    text: term,
                                    newTag: true // add additional parameters
                                }
                            }
                        });
                    }
                });
            });

            $("select#provinces").on("change", function (event) {
                $("select#cities").select2({placeholder: "Please wait a moment ..."});
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
                        fillSelectData("select#cities", items, 'Select Cities');
                    }
                });
            });

            // $('#date-range').datepicker({
            //     autoclose: true,
            //     todayHighlight: true,
            //     format: 'yyyy-mm-dd'
            $('select#cities').on("change", function () {
                let $optionwork = $('<option selected>Select Outlet</option>').val("");
                $("select.outlet").html('');
                $("select.outlet").select2({});
                $("select.outlet").select2({placeholder: "Please wait a moment ..."});
                $("select.outlet").append($optionwork).trigger('change');
                // url = "{{route("geo.outletRetraction")}}" + "?city_id=" + $('select#cities').val() + "&start=" + $('#date-range [name="start"]').val() + "&end=" + $('#date-range [name="end"]').val();
            });
            $('#date-tarik').on("change", function () {
                let $optionwork = $('<option selected>Select Outlet</option>').val("");
                $("select.outlet").html('');
                $("select.outlet").select2({});
                $("select.outlet").select2({placeholder: "Please wait a moment ..."});
                $("select.outlet").append($optionwork).trigger('change');
                // url = "{{route("geo.outletRetraction")}}" + "?city_id=" + $('select#cities').val() + "&start=" + $('#date-range [name="start"]').val() + "&end=" + $('#date-range [name="end"]').val();
                url = '{{route("geo.outletRetraction")}}' + "?city_id=" + $('select#cities').val()+ "&date_tarik=" + $('#date-tarik').val();
                $.ajax({
                    url: url,
                    method: "GET",
                    success: function (data) {
                        var items = $.map(data, function (obj) {
                            obj.id = obj.id;
                            obj.text = obj.text || obj.name;

                            return obj;
                        });
                        $("select.outlet").select2({data: items});
                    }
                });
            });

            function fillSelectData(selectorId, datas, placeholder) {
                obj = $(selectorId);
                obj.html('').select2({placeholder: placeholder, data: datas});
                $(selectorId).trigger('change');
            }
        });
    </script>
@endpush