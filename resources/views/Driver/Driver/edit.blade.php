@extends('layouts.app')
@section('content')

    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card-box">
                        <ol class="breadcrumb">
                            <li>
                                Driver
                            </li>
                            <li>
                                <a href="{{route('driver.index')}}">Driver Management</a>
                            </li>
                            <li class="active">
                                Edit
                            </li>
                        </ol>
                        <h4 class="m-t-0 header-title">
                            <b>Data Driver</b>
                        </h4>
                        {{ Html::ul($errors->all()) }}
                        {{ Form::model($data, array('route' => array('driver.update', $data->id), 'method' => 'PUT', 'role'=>'form')) }}
                        <div class="form-group">
                            {{ Form::label('name', 'Name', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::text('name', old('name', $data->name), array('class' => 'form-control', 'placeholder'=>'Name', 'required'=>'required')) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('phone', 'Phone', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::text('phone', old('name', $data->phone), array('class' => 'form-control', 'placeholder'=>'Phone', 'required'=>'required')) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('email', 'Email', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::text('email', old('name', $data->email), array('class' => 'form-control', 'placeholder'=>'Email', 'required'=>'required')) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('password', 'Password', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::password('password', array('class' => 'form-control', 'placeholder'=>'Password')) }}
                                </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('confirm_password', 'Confirm Password', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::password('confirm_password', array('class' => 'form-control', 'placeholder'=>'Confirm Password')) }}
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('user_type', 'User Type', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::select('user_type', $user_type, $data->id_user_types, ['class' => 'form-control', 'id' => 'id_user_types', 'required' => 'required']) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Pilih salah satu Provinsi, tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('provinces', 'Province', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
{{--                                {{ Form::select('provinces', $provinces, $data->province->id, ['placeholder' => 'Pilih Province', 'class' => 'form-control', 'required' => 'required']) }}--}}
                                {{ Form::select('provinces', $provinces, $data->id_province, ['placeholder' => 'Pilih Province', 'class' => 'form-control', 'required' => 'required']) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Pilih salah satu Provinsi, tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('cities', 'City', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
{{--                                {{ Form::select('cities', [], $data->city->id, ['placeholder' => 'Pilih City', 'class' => 'form-control', 'required' => 'required']) }}--}}
                                {{ Form::select('cities', $cities, $data->id_city, ['placeholder' => 'Pilih City', 'class' => 'form-control', 'required' => 'required']) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Pilih salah satu, tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('districts', 'District', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
{{--                                {{ Form::select('districts', [], $data->district->id, ['placeholder' => 'Pilih District', 'class' => 'form-control', 'required' => 'required']) }}--}}
                                {{ Form::select('districts', $districts, $data->id_district, ['placeholder' => 'Pilih District', 'class' => 'form-control', 'required' => 'required']) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Pilih salah satu, tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('villages', 'Village', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
{{--                                {{ Form::select('villages', [], $data->village->id, ['placeholder' => 'Pilih Village', 'class' => 'form-control', 'required' => 'required']) }}--}}
                                {{ Form::select('villages', $villages, $data->id_village, ['placeholder' => 'Pilih Village', 'class' => 'form-control', 'required' => 'required']) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Pilih salah satu, tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('start_date', 'Start Date', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="yyyy-mm-dd" name="start_date" value="{{\Carbon\Carbon::parse(old('start_date', $data->start_date))->format('Y-m-d')}}" id="start_date">
                                    <span class="input-group-addon bg-custom b-0 text-white"><i class="icon-calender"></i></span>
                                </div><!-- input-group -->
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Pilih tanggal, tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('end_date', 'End Date', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="yyyy-mm-dd" name="end_date" value="{{\Carbon\Carbon::parse(old('start_date', $data->end_date))->format('Y-m-d')}}" id="end_date">
                                    <span class="input-group-addon bg-custom b-0 text-white"><i class="icon-calender"></i></span>
                                </div><!-- input-group -->
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Pilih tanggal, tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-10">
                                <button type="submit" class="btn btn-success m-t-20 pull-right"><i class="fa fa-check"></i> Save</button>
                                <a class="btn btn-danger m-t-20" href="{{ route('driver.index') }}"><i class="fa fa-times"></i> Cancel</a>
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
    <link href="{{asset('assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css')}}" rel="stylesheet">
@endpush

@push('scripts')
    <script src="{{asset('assets/plugins/select2/js/select2.min.js')}}"></script>
    <script src="{{asset('assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>
    <script>
        $('document').ready(function(){
            $("input#start_date").datepicker({"format": "yyyy-mm-dd"});
            $("input#end_date").datepicker({"format": "yyyy-mm-dd"});

            $("select#provinces").select2({
                placeholder:"Select Province",
            });
            $("select#cities").select2({
                placeholder:"Select City",
            });
            $("select#districts").select2({
                placeholder:"Select District",
            });
            $("select#villages").select2({
                placeholder:"Select Village",
            });

            $("select#provinces").on("change", function (event) {
                url = "{{route("geo.city")}}"+"?province_id="+this.value;
                $.ajax({
                    url:url,
                    method:"GET",
                    success: function (data) {
                        var items = $.map(data, function (obj) {
                            obj.id = obj.id;
                            obj.text = obj.text || obj.name;

                            return obj;
                        });
                        fillSelectData("select#cities", items);
                        emptySelectData("select#districts");
                        emptySelectData("select#villages");
                    }
                });
            });

            $("select#cities").on("change", function (event) {
                url = "{{route("geo.district")}}"+"?city_id="+this.value;
                $.ajax({
                    url:url,
                    method:"GET",
                    success: function (data) {
                        var items = $.map(data, function (obj) {
                            obj.id = obj.id;
                            obj.text = obj.text || obj.name;

                            return obj;
                        });
                        fillSelectData("select#districts", items);
                        emptySelectData("select#villages");
                    }
                });
            });

            $("select#districts").on("change", function (event) {
                url = "{{route("geo.village")}}"+"?district_id="+this.value;
                $.ajax({
                    url:url,
                    method:"GET",
                    success: function (data) {
                        var items = $.map(data, function (obj) {
                            obj.id = obj.id;
                            obj.text = obj.text || obj.name;

                            return obj;
                        });
                        fillSelectData("select#villages", items);
                    }
                });
            });

            function fillSelectData(selectorId, datas) {
                obj = $(selectorId);
                obj.html('').select2({data:datas});
                $(selectorId).trigger('change');
            }
            function emptySelectData(selectorId) {
                obj = $(selectorId);
                obj.html('').select2({});
            }

            $("select#id_user_types").on("change", function (event) {
                var now = new Date();
                var year = now.getFullYear();
                var month = now.getMonth() + 1;
                var day = now.getDate();
                var then = new Date(year + 1000, month, day);
                var now_str = year.toString() + "-" + (month.toString().length === 1 ? "0" + month.toString() : month.toString()) + "-" + day.toString();
                var then_str = "";
                if (this.value === "1") {
                    then_str = then.getFullYear().toString() + "-" + (then.getMonth().toString().length === 1 ? "0" + then.getMonth().toString() : month.toString()) + "-" + then.getDate().toString();
                    $("input#start_date").attr("readonly", "readonly");
                    $("input#end_date").attr("readonly", "readonly");
                } else {
                    then_str = now_str;
                    $("input#start_date").removeAttr("readonly");
                    $("input#end_date").removeAttr("readonly");
                }
                $("input#start_date").val(now_str);
                $("input#end_date").val(then_str);
            });

            if ($("select#id_user_types").val() === "1") {
                $("input#start_date").attr("readonly", "readonly");
                $("input#end_date").attr("readonly", "readonly");
            } else {
                $("input#start_date").removeAttr("readonly");
                $("input#end_date").removeAttr("readonly");
            }
        });
    </script>
@endpush