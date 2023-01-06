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
                            <li>
                                <a href="{{route('juragan.index')}}">Juragan Management</a>
                            </li>
                            <li class="active">
                                Edit
                            </li>
                        </ol>
                        <h4 class="m-t-0 header-title">
                            <b>Data Juragan</b>
                        </h4>
                        {{ Html::ul($errors->all()) }}
                        {{ Form::model($data, array('route' => array('juragan.update', $data->id), 'method' => 'PUT', 'role'=>'form')) }}
                        <div class="form-group">
                            {{ Form::label('unilever_id', 'Unilever ID', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::text('unilever_id', old('name', $data->id_unilever_owner), array('class' => 'form-control', 'placeholder'=>'Unilever ID', 'required'=>'required')) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

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
                            {{ Form::label('address', 'Address', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::textarea('address', old('name', $data->address), array('rows' =>5, 'class' => 'form-control', 'placeholder'=>'Address', 'required'=>'required')) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('zip_code', 'Zip Code', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::text('zip_code', old('name', $data->zip_code), array('class' => 'form-control', 'placeholder'=>'Zip Code', 'required'=>'required')) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('latitude', 'Latitude', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::text('latitude', old('name', $data->latitude), array('class' => 'form-control', 'placeholder'=>'Latitude', 'required'=>'required')) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('longitude', 'Longitude', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::text('longitude', old('name', $data->longitude), array('class' => 'form-control', 'required'=>'required')) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('radius', 'Radius(meter)', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::text('radius', old('name', $data->radius_default), array('class' => 'form-control', 'required'=>'required')) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('radius_threshold', 'Radius(meter)', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::text('radius_threshold', old('name', $data->radius_threshold), array('class' => 'form-control', 'required'=>'required')) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('provinces', 'Province', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::select('provinces', $provinces, $data->province->id, ['placeholder' => 'Pilih Province', 'class' => 'form-control', 'required' => 'required']) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Pilih salah satu Provinsi, tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('cities', 'City', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::select('cities', $cities, $data->city->id, ['placeholder' => 'Pilih City', 'class' => 'form-control', 'required' => 'required']) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Pilih salah satu, tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('districts', 'District', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::select('districts', $districts, $data->district->id, ['placeholder' => 'Pilih District', 'class' => 'form-control', 'required' => 'required']) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Pilih salah satu, tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('villages', 'Village', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::select('villages', $villages, $data->village->id, ['placeholder' => 'Pilih Village', 'class' => 'form-control', 'required' => 'required']) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Pilih salah satu, tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-10">
                                <button type="submit" class="btn btn-success m-t-20 pull-right"><i class="fa fa-check"></i> Save</button>
                                <a class="btn btn-danger m-t-20" href="{{ route('juragan.index') }}"><i class="fa fa-times"></i> Cancel</a>
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
@endpush

@push('scripts')
    <script src="{{asset('assets/plugins/select2/js/select2.min.js')}}"></script>
    <script>
        $('document').ready(function(){
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
        });
    </script>
@endpush