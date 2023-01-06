@extends('layouts.app')
@section('content')

    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card-box">
                        <ol class="breadcrumb">
                            <li>
                                Auditor
                            </li>
                            <li>
                                <a href="{{route('auditor.index')}}">Auditor Management</a>
                            </li>
                            <li class="active">
                                Edit
                            </li>
                        </ol>
                        <h4 class="m-t-0 header-title">
                            <b>Data Auditor</b>
                        </h4>
                        {{ Html::ul($errors->all()) }}
                        {{ Form::model($hunter, array('route' => array('auditor.update', $hunter->id), 'method' => 'PUT', 'role'=>'form')) }}
                        {{ Form::hidden('id', $hunter->id) }}
                        <div class="form-group">
                            {{ Form::label('id', 'ID', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                {{ $hunter->id }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('id_unilever', 'Unilever ID', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::text('id_unilever', old('id_unilever', $hunter->id_unilever), array('class' => 'form-control', 'placeholder'=>'Unilever ID', 'required'=>'required')) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('name', 'Name', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::text('name', old('name', $hunter->name), array('class' => 'form-control', 'placeholder'=>'Name', 'required'=>'required')) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('email', 'Email', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::email('email', old('email', $hunter->email), array('class' => 'form-control', 'placeholder'=>'Email', 'required'=>'required')) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>


                        <div class="form-group">
                            {{ Form::label('phone', 'Phone', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::text('phone', old('phone', $hunter->phone), array('class' => 'form-control', 'placeholder'=>'Phone', 'required'=>'required', 'readonly'=>'readonly')) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('id_province', 'Province', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::select('id_province', $provinces, old('id_province',$hunter->id_province), ['placeholder' => 'Pilih Province', 'class' => 'form-control', 'required' => 'required']) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Pilih salah satu Provinsi, tidak boleh
                                    dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('id_city', 'City', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::select('id_city', $cities, old('id_city',$hunter->id_city), ['placeholder' => 'Pilih City', 'class' => 'form-control', 'required' => 'required']) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Pilih salah satu, tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('id_district', 'District', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::select('id_district', $districts, old('id_district',$hunter->id_district), ['placeholder' => 'Pilih District', 'class' => 'form-control', 'required' => 'required']) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Pilih salah satu, tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('id_village', 'Village', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::select('id_village', $villages, old('id_village',$hunter->id_village), ['placeholder' => 'Pilih Village', 'class' => 'form-control', 'required' => 'required']) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Pilih salah satu, tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('address', 'Address', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::textarea('address', old('address', $hunter->address), array('rows' =>5, 'class' => 'form-control', 'placeholder'=>'Address', 'required'=>'required')) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('latitude', 'Latitude', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::text('latitude', old('latitude', $hunter->latitude), array('class' => 'form-control', 'placeholder'=>'Latitude', 'required'=>'required')) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('longitude', 'Longitude', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::text('longitude', old('longitude', $hunter->longitude), ['class' => 'form-control', 'required'=>'required']) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('id_user_types', 'User Type', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::select('id_user_types', $user_types, old('id_user_types',$hunter->id_user_types), ['placeholder' => 'Pilih User Type', 'class' => 'form-control', 'required' => 'required']) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Pilih salah satu, tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('start_date', 'Start Date', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::text('start_date', \Carbon\Carbon::parse(old('start_date', $hunter->start_date))->format('Y-m-d') , ['class' => 'form-control', 'required'=>'required']) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('end_date', 'End Date', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::text('end_date', \Carbon\Carbon::parse(old('end_date', $hunter->end_date))->format('Y-m-d')  , ['class' => 'form-control', 'required'=>'required']) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('status_active', 'Active', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::select('status_active', $actives, old('status_active',$hunter->status_active), ['placeholder' => 'Pilih Active', 'class' => 'form-control', 'required' => 'required']) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Pilih salah satu, tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-10">
                                <button type="submit" class="btn btn-success m-t-20 pull-right"><i
                                            class="fa fa-check"></i> Save
                                </button>
                                <a class="btn btn-danger m-t-20" href="{{ route('auditor.index') }}"><i
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
    <link href="{{asset('assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css')}}" rel="stylesheet"
          type="text/css">
@endpush

@push('scripts')
    <script src="{{asset('assets/plugins/select2/js/select2.min.js')}}"></script>
    <script src="{{asset('assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>
    <script>
        $('document').ready(function () {
            $("select#id_province").select2({
                placeholder: "Select Province",
            });
            $("select#id_city").select2({
                placeholder: "Select City",
            });
            $("select#id_district").select2({
                placeholder: "Select District",
            });
            $("select#id_village").select2({
                placeholder: "Select Village",
            });

            $("input#start_date").datepicker({"format": "yyyy-mm-dd"});
            $("input#end_date").datepicker({"format": "yyyy-mm-dd"});

            $("select#id_province").on("change", function (event) {
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
                        fillSelectData("select#id_city", items);
                        emptySelectData("select#id_district");
                        emptySelectData("select#id_village");
                    }
                });
            });

            $("select#id_city").on("change", function (event) {
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
                        fillSelectData("select#id_district", items);
                        emptySelectData("select#id_village");
                    }
                });
            });

            $("select#id_district").on("change", function (event) {
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
                        fillSelectData("select#id_village", items);
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

@include('layouts.alert')