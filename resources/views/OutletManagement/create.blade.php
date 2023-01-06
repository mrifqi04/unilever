@extends('layouts.app')
@section('content')

    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card-box">
                        <ol class="breadcrumb">
                            <li>
                                Outlet
                            </li>
                            <li>
                                <a href="{{route('outlet.index')}}">Outlet Management</a>
                            </li>
                            <li class="active">
                                Create
                            </li>
                        </ol>
                        <h4 class="m-t-0 header-title">
                            <b>Data Outlet</b>
                        </h4>
                        {{ Html::ul($errors->all()) }}
                        {{ Form::open(array('url' => route('outlet.store'), 'role' => 'form')) }}

                        <div class="form-group">
                            {{ Form::label('unilever_id', 'Unilever ID', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::text('unilever_id', '', array('class' => 'form-control', 'placeholder'=>'Unilever ID', 'required'=>'required')) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('juragan_id', 'Juragan', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::select('juragan_id', $juragans, null, ['placeholder' => 'Pilih Juragan', 'class' => 'form-control', 'required' => 'required']) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Pilih salah satu, tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('outlet_type_id', 'Outet Status Type', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::select('outlet_type_id', $statusTypes, null, ['placeholder' => 'Pilih', 'class' => 'form-control', 'required' => 'required']) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Pilih salah satu, tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('ownership_status_id', 'Ownership Status', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::select('ownership_status_id', $ownershipStatus, null, ['placeholder' => 'Pilih', 'class' => 'form-control', 'required' => 'required']) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Pilih salah satu, tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('street_type_id', 'Street Type', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::select('street_type_id', $streetTypes, null, ['placeholder' => 'Pilih', 'class' => 'form-control', 'required' => 'required']) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Pilih salah satu, tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('name', 'Outlet Name', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::text('name', '', array('class' => 'form-control', 'placeholder'=>'Outlet Name', 'required'=>'required')) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('owner', 'Outlet Owner', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::text('owner', '', array('class' => 'form-control', 'placeholder'=>'Outlet Owner', 'required'=>'required')) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('phone', 'Phone', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::text('phone', '', array('class' => 'form-control', 'placeholder'=>'Phone', 'required'=>'required')) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('phone2', 'Phone 2', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::text('phone2', '', array('class' => 'form-control', 'placeholder'=>'Phone 2', 'required'=>'required')) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('address', 'Address', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::textarea('address', '', array('rows' =>5, 'class' => 'form-control', 'placeholder'=>'Address', 'required'=>'required')) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('latitude', 'Latitude', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::text('latitude', '', array('class' => 'form-control', 'placeholder'=>'Latitude', 'required'=>'required')) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('longitude', 'Longitude', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::text('longitude', '', array('class' => 'form-control', 'placeholder'=>'Longitude', 'required'=>'required')) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('provinces', 'Province', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::select('provinces', $provinces, null, ['placeholder' => 'Pilih Province', 'class' => 'form-control', 'required' => 'required']) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Pilih salah satu Provinsi, tidak boleh dikosongkan.
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
                            {{ Form::label('districts', 'District', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::select('districts', [], null, ['placeholder' => 'Pilih District', 'class' => 'form-control', 'required' => 'required']) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Pilih salah satu, tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('villages', 'Village', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::select('villages', [], null, ['placeholder' => 'Pilih Village', 'class' => 'form-control', 'required' => 'required']) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Pilih salah satu, tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('cabinets', 'Cabinet', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::select('cabinets', $cabinets, null, ['placeholder' => 'Pilih Cabinet', 'class' => 'form-control', 'required' => 'required']) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Pilih salah satu, tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('descriptions', 'Descriptions', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::textarea('descriptions', '', array('rows' =>5, 'class' => 'form-control', 'placeholder'=>'Address', 'required'=>'required')) }}
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-10">
                                <button type="submit" class="btn btn-success m-t-20 pull-right"><i class="fa fa-check"></i> Save</button>
                                <a class="btn btn-danger m-t-20" href="{{ route('outlet.index') }}"><i class="fa fa-times"></i> Cancel</a>
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
            $("select#juragan_id").select2({
                placeholder:"Select Juragan",
            });

            $("select#provinces").select2({
                placeholder:"Select Province",
            });
            $("select#cities").select2({
                placeholder:"Select Citie",
            });
            $("select#districts").select2({
                placeholder:"Select District",
            });
            $("select#villages").select2({
                placeholder:"Select Village",
            });

            $("select#cabinets").select2({
                placeholder:"Select Cabinet",
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
        });
    </script>
@endpush