@extends('layouts.app')
@section('content')
    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card-box">
                        <ol class="breadcrumb">
                            <li>
                                Delivery
                            </li>
                            <li>
                                <a href="{{ route('delivery.index') }}">Delivery Management</a>
                            </li>
                            <li class="active">
                                Create
                            </li>
                        </ol>
                        <h4 class="m-t-0 header-title">
                            <b>Data Delivery</b>
                        </h4>
                        {{ Html::ul($errors->all()) }}
                        {{ Form::open(['url' => route('delivery.store'), 'role' => 'form']) }}

                        <div class="form-group">
                            {{ Form::label('date', 'Date', ['class' => 'col-2 col-form-label']) }}
                            <div class="col-10">
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="yyyy-mm-dd" name="date"
                                        id="datepicker-autoclose">
                                    <span class="input-group-addon bg-custom b-0 text-white"><i
                                            class="icon-calender"></i></span>
                                </div><!-- input-group -->
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Pilih tanggal, tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('name', 'Name', ['class' => 'col-2 col-form-label']) }}
                            <div class="col-10">
                                {{ Form::text('name', '', ['class' => 'form-control', 'placeholder' => 'Name', 'required' => 'required']) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('provinces', 'Province', ['class' => 'col-2 col-form-label']) }}
                            <div class="col-10">
                                {{ Form::select('provinces', $provinces, null, ['placeholder' => 'Pilih Province', 'class' => 'form-control', 'required' => 'required']) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Pilih salah satu Provinsi, tidak boleh
                                    dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('cities', 'City', ['class' => 'col-2 col-form-label']) }}
                            <div class="col-10">
                                {{ Form::select('cities', [], null, ['placeholder' => 'Pilih City', 'class' => 'form-control', 'required' => 'required']) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Pilih salah satu, tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('journey_plan_type', 'Jenis Transaksi', ['class' => 'col-2 col-form-label']) }}
                            <div class="col-10">
                                {{ Form::select('journey_plan_type', ['Deploy' => 'Deploy', 'Tarik' => 'Tarik'], null, ['placeholder' => 'Pilih Jenis Transaksi', 'class' => 'form-control', 'required' => 'required']) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Pilih salah satu, tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('route_plan', 'Route Plan', ['class' => 'col-2 col-form-label']) }}
                            <div class="col-10">
                                <select multiple="multiple" class="multi-select" id="route_plan" name="route_plan[]">
                                    {{-- <optgroup label="NFC EAST"> --}}
                                    {{-- <option>Dallas Cowboys</option> --}}
                                    {{-- </optgroup> --}}
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('driver', 'Driver', ['class' => 'col-2 col-form-label']) }}
                            <div class="col-10">
                                {{ Form::select('driver', [], null, ['placeholder' => 'Pilih Driver', 'class' => 'form-control', 'required' => 'required']) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Pilih salah satu, tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('vehicle', 'Vehicle', ['class' => 'col-2 col-form-label']) }}
                            <div class="col-10">
                                {{ Form::select('vehicle', [], null, ['placeholder' => 'Pilih Kendaraan', 'class' => 'form-control', 'required' => 'required']) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Pilih salah satu, tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-10">
                                <button type="submit" onclick="confirm('Are you sure?')"
                                    class="btn btn-success m-t-20 pull-right"><i class="fa fa-check"></i> Save
                                </button>
                                <a class="btn btn-danger m-t-20" href="{{ route('delivery.index') }}"><i
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
    <link href="{{ asset('assets/plugins/select2/css/select2.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/plugins/multiselect/css/multi-select.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
@endpush

@push('scripts')
    <script src="{{ asset('assets/plugins/moment/moment.js') }}"></script>
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/multiselect/js/jquery.multi-select.js') }}"></script>
    <script src="{{ asset('assets/plugins/jquery-quicksearch/jquery.quicksearch.js') }}"></script>
    <script src="{{ asset('assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script>
        $('document').ready(function() {
            // $("select#cities").on("change", function (event) {
            $("select#journey_plan_type, select#cities").on("change", function(event) {
                $('#route_plan').empty().multiSelect('refresh');
                url = "{{ route('geo.route_plan') }}" + "?city_id=" + $('select#cities').val() + "&date=" +
                    $("#datepicker-autoclose").val() + "&type=" + $("#journey_plan_type").val();
                $.ajax({
                    url: url,
                    method: "GET",
                    success: function(data) {
                        var type = $("#journey_plan_type").val().toLowerCase() ? $(
                            "#journey_plan_type").val().toLowerCase() : 'deploy';
                        var listPrefix = {
                            deploy: 'ADR',
                            tarik: 'ART',
                            tukar: 'RPC'
                        };
                        var prefix = listPrefix[type];
                        var _nested = {};
                        var uniqueGroups = data.map((item) => {
                            return prefix + '00' + item.delivery_orders.adr + ' ' + $(
                                "#datepicker-autoclose").val();
                        }).filter((v, i, a) => a.indexOf(v) === i);

                        $.each(uniqueGroups, function(i, group) {
                            $('#route_plan').append('<optgroup label="' + group +
                                '"></optgroup>');
                        });
                        $.each(data, function(i, item) {
                            var _nested = prefix + '00' + item.delivery_orders.adr +
                                ' ' + $("#datepicker-autoclose").val();
                            // $.each(item.route_plan, function (a, rt) {
                            $('#route_plan').multiSelect('addOption', {
                                value: item.id + '_' + item.outlet.id,
                                text: item.outlet.name + ' - ' + item
                                    .id_cabinet,
                                index: 0,
                                nested: _nested
                            });
                            // });
                        });
                    }
                });
            });

            $('#route_plan').multiSelect({
                selectableOptgroup: true,
                selectableHeader: "<input type='text' class='form-control search-input' autocomplete='off' placeholder='search...'>",
                selectionHeader: "<input type='text' class='form-control search-input' autocomplete='off' placeholder='search...'>",
                afterInit: function(ms) {
                    var that = this,
                        $selectableSearch = that.$selectableUl.prev(),
                        $selectionSearch = that.$selectionUl.prev(),
                        selectableSearchString = '#' + that.$container.attr('id') +
                        ' .ms-elem-selectable:not(.ms-selected)',
                        selectionSearchString = '#' + that.$container.attr('id') +
                        ' .ms-elem-selection.ms-selected';

                    that.qs1 = $selectableSearch.quicksearch(selectableSearchString)
                        .on('keydown', function(e) {
                            if (e.which === 40) {
                                that.$selectableUl.focus();
                                return false;
                            }
                        });

                    that.qs2 = $selectionSearch.quicksearch(selectionSearchString)
                        .on('keydown', function(e) {
                            if (e.which == 40) {
                                that.$selectionUl.focus();
                                return false;
                            }
                        });
                }
            });

            $("select#provinces").select2({
                placeholder: "Select Province",
            });
            $("select#cities").select2({
                placeholder: "Select Cities",
            });

            $("select#driver").select2({
                placeholder: "Select Driver",
            });
            $("select#vehicle").select2({
                placeholder: "Select Vehicle",
            });

            $("select#provinces").on("change", function(event) {
                url = "{{ route('geo.city') }}" + "?province_id=" + this.value;
                $.ajax({
                    url: url,
                    method: "GET",
                    success: function(data) {
                        var items = $.map(data, function(obj) {
                            obj.id = obj.id;
                            obj.text = obj.text || obj.name;

                            return obj;
                        });
                        fillSelectData("select#cities", items);
                    }
                });
            });

            $("select#cities").on("change", function(event) {
                url = "{{ route('geo.driver') }}" + "?city_id=" + this.value;
                $.ajax({
                    url: url,
                    method: "GET",
                    success: function(data) {
                        var items = $.map(data, function(obj) {
                            obj.id = obj.id;
                            obj.text = obj.text || obj.name;

                            return obj;
                        });
                        fillSelectData("select#driver", items);
                    }
                });
            });

            $("select#cities").on("change", function(event) {
                url = "{{ route('geo.vehicle') }}" + "?city_id=" + this.value;
                $.ajax({
                    url: url,
                    method: "GET",
                    success: function(data) {
                        var items = $.map(data, function(obj) {
                            obj.id = obj.id;
                            obj.text = obj.text || obj.license_number;

                            return obj;
                        });
                        fillSelectData("select#vehicle", items);
                    }
                });
            });

            function fillSelectData(selectorId, datas) {
                obj = $(selectorId);
                obj.html('').select2({
                    data: datas
                });
                $(selectorId).trigger('change');
            }

            $("#datepicker-autoclose").datepicker({
                // startDate: '0d',
                autoclose: true,
                todayHighlight: true,
                format: 'yyyy-mm-dd'
            });
        });
    </script>
@endpush
