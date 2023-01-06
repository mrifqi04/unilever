@extends('layouts.app')
@section('content')

    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card-box">
                        <ol class="breadcrumb">
                            <li>
                                Cabinet
                            </li>
                            <li>
                                <a href="{{route('cabinet.index')}}">Cabinet Management</a>
                            </li>
                            <li class="active">
                                Create
                            </li>
                        </ol>
                        <h4 class="m-t-0 header-title">
                            <b>Data Cabinet</b>
                        </h4>
                        {{ Html::ul($errors->all()) }}
                        {{ Form::open(array('url' => route('cabinet.store'), 'role' => 'form')) }}

                        <div class="form-group">
                            {{ Form::label('brand', 'Brand', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::text('brand', '', array('class' => 'form-control', 'placeholder'=>'Ex. Samsung, Mitsubishi', 'required'=>'required')) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('model', 'Model', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::text('model', '', array('class' => 'form-control', 'placeholder'=>'Ex. B31AMD', 'required'=>'required')) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('qrcode', 'QR Code', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::text('qrcode', '', array('class' => 'form-control', 'placeholder'=>'Ex. Value for QR Code after scan', 'required'=>'required')) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('serialnumber', 'Serial Number', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::text('serialnumber', '', array('class' => 'form-control', 'placeholder'=>'Ex. 12345ABCDE', 'required'=>'required')) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-10">
                                <button type="submit" class="btn btn-success m-t-20 pull-right"><i class="fa fa-check"></i> Save</button>
                                <a class="btn btn-danger m-t-20" href="{{ route('cabinet.index') }}"><i class="fa fa-times"></i> Cancel</a>
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

@endpush