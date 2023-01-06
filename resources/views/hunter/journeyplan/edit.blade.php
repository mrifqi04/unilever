@extends('layouts.app')
@section('content')

    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card-box">
                        <ol class="breadcrumb">
                            <li>
                                Journey Plan
                            </li>
                            <li>
                                <a href="{{route('journeyplan.index')}}">Journey Plan Management</a>
                            </li>
                            <li class="active">
                                Edit
                            </li>
                        </ol>
                        <h4 class="m-t-0 header-title">
                            <b>Data Journey Plan</b>
                        </h4>
                        {{ Html::ul($errors->all()) }}
                        {{ Form::model($journeyplan, array('route' => array('journeyplan.update', $journeyplan->id), 'method' => 'PUT', 'role'=>'form')) }}
                        <div class="form-group">
                            {{ Form::label('id', 'ID', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::text('id', old('id', $journeyplan->id), array('class' => 'form-control', 'placeholder'=>'ID', 'required'=>'required', 'readonly' => 'readonly')) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('name', 'Name', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::text('name', old('name', $journeyplan->name), array('class' => 'form-control', 'placeholder'=>'Name', 'required'=>'required')) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('id_juragan', 'Juragan', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::select('id_juragan', $juragans, old('id_juragan', $journeyplan->id_juragan), ['placeholder' => 'Pilih Juragan', 'class' => 'form-control', 'required' => 'required']) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Pilih salah satu Juragan, tidak boleh
                                    dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('assign_to', 'Assign To', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::select('assign_to', $hunters, old('assign_to', $journeyplan->assign_to), ['placeholder' => 'Pilih Hunter', 'class' => 'form-control', 'required' => 'required']) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Pilih salah satu Hunter, tidak boleh
                                    dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('id_province', 'Province', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::select('id_province', $provinces, old('id_province', $journeyplan->id_province), ['placeholder' => 'Pilih Province', 'class' => 'form-control', 'required' => 'required']) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Pilih salah satu Provinsi, tidak boleh
                                    dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('id_city', 'City', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::select('id_city', $cities, old('id_city', $journeyplan->id_city), ['placeholder' => 'Pilih City', 'class' => 'form-control', 'required' => 'required']) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Pilih salah satu, tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('id_district', 'District', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::select('id_district', $districts, old('id_district', $journeyplan->id_district), ['placeholder' => 'Pilih District', 'class' => 'form-control', 'required' => 'required']) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Pilih salah satu, tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('id_village', 'Village', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::select('id_village', $villages, old('id_village', $journeyplan->id_village), ['placeholder' => 'Pilih Village', 'class' => 'form-control', 'required' => 'required']) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Pilih salah satu, tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('start_date', 'Start Date', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::text('start_date', \Carbon\Carbon::parse(old('start_date', $journeyplan->start_date))->format('Y-m-d') , ['class' => 'form-control', 'required'=>'required']) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('end_date', 'End Date', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::text('end_date', \Carbon\Carbon::parse(old('end_date', $journeyplan->end_date))->format('Y-m-d')  , ['class' => 'form-control', 'required'=>'required']) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-10">
                                <button type="submit" class="btn btn-success m-t-20 pull-right"><i
                                            class="fa fa-check"></i> Save
                                </button>
                                <a class="btn btn-danger m-t-20" href="{{ route('journeyplan.index') }}"><i
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
        });
    </script>
@endpush