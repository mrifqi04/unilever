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
                                <a href="{{route('delivery.index')}}">Delivery Management</a>
                            </li>
                            <li class="active">
                                Edit
                            </li>
                        </ol>
                        <h4 class="m-t-0 header-title">
                            <b>Data Delivery</b>
                        </h4>
                        {{ Html::ul($errors->all()) }}
                        {{ Form::open(array('url' => route('delivery.update', ['delivery' => $data->id, 'type' => $type]), 'method' => 'PUT', 'role' => 'form')) }}

                        <div class="form-group">
                            {{ Form::label('date', 'Date', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="yyyy-mm-dd" name="date" id="datepicker-autoclose" value="{{ \Carbon\Carbon::parse($data->start_date)->format('Y-m-d') }}">
                                    <span class="input-group-addon bg-custom b-0 text-white"><i class="icon-calender"></i></span>
                                </div><!-- input-group -->
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Pilih tanggal, tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('name', 'Name', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::text('name', $data->name, array('class' => 'form-control', 'placeholder'=>'Name', 'required'=>'required')) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('provinces', 'Province', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::select('provinces', $provinces, $data->id_province, ['placeholder' => 'Pilih Province', 'class' => 'form-control', 'required' => 'required']) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Pilih salah satu Provinsi, tidak boleh
                                    dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('cities', 'City', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::select('cities', [], $data->id_city, ['data-id' => $data->id_city, 'placeholder' => 'Pilih City', 'class' => 'form-control', 'required' => 'required']) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Pilih salah satu, tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            {{ Form::label('journey_plan_type', 'Jenis Transaksi', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::select('journey_plan_type', ['Deploy' => 'Deploy', 'Tarik' => 'Tarik', 'Tukar' => 'Tukar'], ucfirst($type), ['placeholder' => 'Pilih Jenis Transaksi', 'class' => 'form-control', 'required' => 'required', 'disabled' => 'disabled']) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Pilih salah satu, tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('route_plan', 'Route Plan', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                <select multiple="multiple" class="multi-select" id="route_plan" name="route_plan[]">
                                    {{--<optgroup label="NFC EAST">--}}
                                        {{--<option>Dallas Cowboys</option>--}}
                                    {{--</optgroup>--}}
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('driver', 'Driver', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::select('driver', [], $data->assign_to, ['placeholder' => 'Pilih Driver', 'class' => 'form-control', 'required' => 'required']) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Pilih salah satu, tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('vehicle', 'Vehicle', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::select('vehicle', [], $data->id_vehicle, ['placeholder' => 'Pilih Kendaraan', 'class' => 'form-control', 'required' => 'required']) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Pilih salah satu, tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-10">
                                <button type="submit" onclick="confirm('Are you sure?')" class="btn btn-success m-t-20 pull-right"><i
                                            class="fa fa-check"></i> Save
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
    <link href="{{asset('assets/plugins/select2/css/select2.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('assets/plugins/multiselect/css/multi-select.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css')}}" rel="stylesheet">
@endpush

@push('scripts')
    <script src="{{asset('assets/plugins/moment/moment.js')}}"></script>
    <script src="{{asset('assets/plugins/select2/js/select2.min.js')}}"></script>
    <script src="{{asset('assets/plugins/multiselect/js/jquery.multi-select.js')}}"></script>
    <script src="{{asset('assets/plugins/jquery-quicksearch/jquery.quicksearch.js')}}"></script>
    <script src="{{asset('assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>
    <script>

        $('document').ready(function () {
            $("select#provinces").change();
            // $("select#cities").on("change", function (event) {

            $('#route_plan').multiSelect({
                selectableOptgroup: true,
                selectableHeader: "<input type='text' class='form-control search-input' autocomplete='off' placeholder='search...'>",
                selectionHeader: "<input type='text' class='form-control search-input' autocomplete='off' placeholder='search...'>",
                afterInit: function (ms) {
                    var that = this,
                        $selectableSearch = that.$selectableUl.prev(),
                        $selectionSearch = that.$selectionUl.prev(),
                        selectableSearchString = '#' + that.$container.attr('id') + ' .ms-elem-selectable:not(.ms-selected)',
                        selectionSearchString = '#' + that.$container.attr('id') + ' .ms-elem-selection.ms-selected';

                    that.qs1 = $selectableSearch.quicksearch(selectableSearchString)
                        .on('keydown', function (e) {
                            if (e.which === 40) {
                                that.$selectableUl.focus();
                                return false;
                            }
                        });

                    that.qs2 = $selectionSearch.quicksearch(selectionSearchString)
                        .on('keydown', function (e) {
                            if (e.which == 40) {
                                that.$selectionUl.focus();
                                return false;
                            }
                        });
                }
            });

            $('select#journey_plan_type, select#cities').on("change", function (event) {
                $('#route_plan').empty().multiSelect('refresh');
                url = "{{route("geo.route_plan")}}" + "?city_id=" 
                    + $('select#cities').val()
                    +"&date="+$("#datepicker-autoclose").val()
                    +"&type="+$("#journey_plan_type").val()
                    +"&id_journey_plan={{$data->id}}";
                $.ajax({
                    url: url,
                    method: "GET",
                    success: function (data) {
                        var listPrefix = {deploy: 'ADR', tarik: 'ART', tukar: 'RPC'}; 
                        var prefix = listPrefix["{{$type}}"];
                        $.each(data, function (i, item) {
                            $('#route_plan').append('<optgroup label="'+prefix+'00' + item.delivery_orders.adr+' '+$("#datepicker-autoclose").val()+'"></optgroup>');
                            var _nested = prefix+'00' + item.delivery_orders.adr+' '+$("#datepicker-autoclose").val();

                            // $.each(item.route_plan, function (a, rt) {
                                $('#route_plan').multiSelect('addOption',
                                    {
                                        value: item.id+'_'+item.outlet.id,
                                        text: item.outlet.name + ' - ' + item.id_cabinet,
                                        index: 0,
                                        nested: _nested
                                    }
                                );
                        });
                        if ($("#journey_plan_type").val() == '{{ ucfirst($type) }}' && $('select#cities').val() == "{{ $data->id_city }}") {
                            var selectedRoutePlan = <?php echo $data->journeyRoute->pluck('route_plan'); ?>;
                            $('#route_plan').multiSelect('select', selectedRoutePlan);
                            $('#route_plan').multiSelect('refresh');
                        }
                    }
                });
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

           

            $("select#cities").on("change", function (event) {
                url = "{{route("geo.driver")}}" + "?city_id=" + this.value;
                val = this.value;
                $.ajax({
                    url: url,
                    method: "GET",
                    success: function (data) {
                        var items = $.map(data, function (obj) {
                            obj.id = obj.id;
                            obj.text = obj.text || obj.name;

                            return obj;
                        });
                        if (val == "{{ $data->id_city }}") {
                            fillSelectData("select#driver", items, 1);
                            $('select#driver').val("{{ $data->assign_to }}").change();
                        } else {
                            fillSelectData("select#driver", items);
                        }
                    }
                });
            });

            $("select#cities").on("change", function (event) {
                url = "{{route("geo.vehicle")}}" + "?city_id=" + this.value;
                val = this.value;
                $.ajax({
                    url: url,
                    method: "GET",
                    success: function (data) {
                        var items = $.map(data, function (obj) {
                            obj.id = obj.id;
                            obj.text = obj.text || obj.license_number;

                            return obj;
                        });
                        if (val == "{{ $data->id_city }}") {
                            fillSelectData("select#vehicle", items, 1);
                            $('select#vehicle').val("{{ $data->id_vehicle }}").change();
                        } else {
                            fillSelectData("select#vehicle", items);
                        }
                    }
                });
            });


            $("#datepicker-autoclose").datepicker({
                // startDate: '0d',
                autoclose: true,
                todayHighlight: true,
                format: 'yyyy-mm-dd'
            });
        });
        
        function fillSelectData(selectorId, datas, isNotTrigger) {
            isNotTrigger = (isNotTrigger) ? 1 : 0;
            obj = $(selectorId);
            obj.html('').select2({data: datas});
            if (isNotTrigger == 0) {
                $(selectorId).trigger('change');
            }
        }
        $(document).on("change", "select#provinces", function (event) {
            url = "{{route("geo.city")}}" + "?province_id=" + this.value;
            val = this.value;
            $.ajax({
                url: url,
                method: "GET",
                success: function (data) {
                    var items = $.map(data, function (obj) {
                        obj.id = obj.id;
                        obj.text = obj.text || obj.name;

                        return obj;
                    });
                    if (val ==  "{{ $data->id_province }}") {
                        fillSelectData("select#cities", items, 1);
                        $('select#cities').val("{{ $data->id_city }}").change();
                    } else {
                        fillSelectData("select#cities", items);
                    }
                }
            });
        });
    </script>
@endpush