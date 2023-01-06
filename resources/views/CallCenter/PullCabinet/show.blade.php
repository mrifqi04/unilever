@extends('layouts.app')
@section('content')
    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card-box">
                        <ol class="breadcrumb">
                            <li>
                                Call Center
                            </li>
                            <li>
                                <a href="{{ route('callcenter.pull-cabinet.index') }}">Tarik Kabinet</a>
                            </li>
                            <li class="active">
                                Show
                            </li>
                        </ol>
                        <h4 class="m-t-0 header-title">
                            <b>Data Outlet</b>
                        </h4>
                        {{ Html::ul($errors->all()) }}
                        <div class="form-group">
                            {{ Form::label('id', 'SERU ID', ['class' => 'col-2 col-form-label']) }}
                            <div class="col-10 form-control">
                                {{ $data->id }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('unilever_id', 'Unilever ID', ['class' => 'col-2 col-form-label']) }}
                            <div class="col-10 form-control">
                                {{ $data->id_unilever }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('juragan', 'Juragan', ['class' => 'col-2 col-form-label']) }}
                            <div class="col-10 form-control">
                                {{ $data->juragan->name }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('name', 'Name', ['class' => 'col-2 col-form-label']) }}
                            <div class="col-10 form-control">
                                {{ $data->name }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('owner', 'Outlet Owner', ['class' => 'col-2 col-form-label']) }}
                            <div class="col-10 form-control">
                                {{ $data->owner }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('phone', 'Phone', ['class' => 'col-2 col-form-label']) }}
                            <div class="col-10 form-control">
                                {{ $data->phone }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('phone2', 'Phone 2', ['class' => 'col-2 col-form-label']) }}
                            <div class="col-10 form-control">
                                {{ $data->phone2 }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('address', 'Address', ['class' => 'col-2 col-form-label']) }}
                            <div class="col-10 form-control">
                                {{ $data->address }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('latitude', 'Latitude', ['class' => 'col-2 col-form-label']) }}
                            <div class="col-10 form-control">
                                {{ $data->latitude }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('longitude', 'Longitude', ['class' => 'col-2 col-form-label']) }}
                            <div class="col-10 form-control">
                                {{ $data->longitude }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('province', 'Province', ['class' => 'col-2 col-form-label']) }}
                            <div class="col-10 form-control">
                                {{ $data->province->name }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('city', 'City', ['class' => 'col-2 col-form-label']) }}
                            <div class="col-10 form-control">
                                {{ $data->city->name }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('district', 'District', ['class' => 'col-2 col-form-label']) }}
                            <div class="col-10 form-control">
                                {{ $data->district->name }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('village', 'Village', ['class' => 'col-2 col-form-label']) }}
                            <div class="col-10 form-control">
                                {{ $data->village->name }}
                            </div>
                        </div>
                        {{ Form::model($data, ['route' => ['callcenter.pull-cabinet.update', $outletRetractionProgress->id], 'method' => 'PUT', 'role' => 'form', 'name' => 'form-update-' . $outletRetractionProgress->id]) }}
                        <div class="form-group">
                            {{ Form::label('address_by_cc', 'Address (Added by Call Center)', ['class' => 'col-2 col-form-label']) }}
                            <div class="col-10">
                                {{ Form::text('address_by_cc', old('address_by_cc', $data->address_by_cc), ['class' => 'form-control', 'required' => 'required']) }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('phone_by_cc', 'Phone (Added by Call Center)', ['class' => 'col-2 col-form-label']) }}
                            <div class="col-10">
                                {{ Form::text('phone_by_cc', old('phone_by_cc', $data->phone_by_cc), ['class' => 'form-control', 'required' => 'required']) }}
                            </div>
                        </div>
                        {{ Form::close() }}
                        <div class="form-group">

                            <div class="col-12">
                                <a class="btn btn-danger m-t-20" href="{{ route('callcenter.pull-cabinet.index') }}">
                                    <i class="fa fa-times"></i>&nbsp;Back
                                </a>
                                @if ($canUpdate)
                                    <button name="button-save" class="btn btn-success m-t-20 pull-right" title="save"
                                        type="button" data-id="{{ $outletRetractionProgress->id }}">
                                        <i class="fa fa-save"></i>&nbsp;Save
                                    </button>
                                @endif
                                @if ($canCancel)
                                    <form
                                        action="{{ route('callcenter.pull-cabinet.cancel', ['id' => $outletRetractionProgress->id]) }}"
                                        method="POST" class="pull-right"
                                        name="form-cancel-{{ $outletRetractionProgress->id }}">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="reject_reason_id"
                                            value="{{ $outletRetractionProgress->section !== 'callcenter' ? '' : $outletRetractionProgress->reject_reason_id }}">
                                        <button name="button-cancel" class="btn btn-danger m-t-20" title="cancel"
                                            type="button" data-id="{{ $outletRetractionProgress->id }}">
                                            <i class="ion-close"></i>&nbsp;Cancel
                                        </button>
                                    </form>
                                @endif
                                @if ($canApprove)
                                    <form
                                        action="{{ route('callcenter.pull-cabinet.approve', ['id' => $outletRetractionProgress->id]) }}"
                                        method="POST" class="pull-right"
                                        name="form-approve-{{ $outletRetractionProgress->id }}">
                                        @csrf
                                        @method('PUT')
                                        <button name="button-approve" class="btn btn-primary m-t-20" title="approve"
                                            type="button" data-id="{{ $outletRetractionProgress->id }}">
                                            <i class="ion-checkmark"></i>&nbsp;Approve
                                        </button>
                                    </form>
                                @endif
                                @if ($canPostpone)
                                    <form
                                        action="{{ route('callcenter.pull-cabinet.postpone', ['id' => $outletRetractionProgress->id]) }}"
                                        method="POST" class="pull-right"
                                        name="form-postpone-{{ $outletRetractionProgress->id }}">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="reject_reason_id"
                                            value="{{ $outletRetractionProgress->section !== 'callcenter' ? '' : $outletRetractionProgress->reject_reason_id }}">
                                        <button name="button-postpone" class="btn btn-warning m-t-20" title="tunda"
                                            type="button" data-id="{{ $outletRetractionProgress->id }}">
                                            <i class="ion-clock"></i>&nbsp;Tunda
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="card-box">
                        <h4 class="m-t-0 m-b-20 header-title"><b>Map</b></h4>
                        <div id="map" class="gmaps"></div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="portlet">
                        <!-- Block Header -->
                        <div class="portlet-heading bg-primary">
                            <h3 class="portlet-title"> Survey </h3>

                            <div class="portlet-widgets">
                                <span class="divider"></span>
                                <a data-toggle="collapse" data-parent="#accordion1" href="#bg-survey"><i
                                        class="ion-minus-round"></i></a>
                                <span class="divider"></span>
                            </div>
                            <div class="clearfix"></div>
                        </div>

                        <div id="bg-survey" class="panel-collapse collapse in">
                            <div class="portlet-body">
                                <div class="container-fluid">

                                    @if ($survey)
                                        <div class="row">
                                            <div class="form-horizontal">
                                                <div class="form-group">
                                                    <label class="col-sm-6 control-label">Unit Cabinet</label>
                                                    <div class="col-sm-6">
                                                        <p class="form-control-static">
                                                            @if ((int) $survey->unit_cabinet_value > 0)
                                                                Ya, {{ $survey->unit_cabinet_value }}
                                                            @else
                                                                Tidak
                                                            @endif
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-6 control-label">Unit Highlighter</label>
                                                    <div class="col-sm-6">
                                                        <p class="form-control-static">
                                                            @if ((int) $survey->unit_highlighter_value > 0)
                                                                Ya, {{ $survey->unit_highlighter_value }}
                                                            @else
                                                                Tidak
                                                            @endif
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-6 control-label">Unit Keranjang</label>
                                                    <div class="col-sm-6">
                                                        <p class="form-control-static">
                                                            @if ((int) $survey->unit_keranjang_value > 0)
                                                                Ya, {{ $survey->unit_keranjang_value }}
                                                            @else
                                                                Tidak
                                                            @endif
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-6 control-label">Unit Buku Panduan</label>
                                                    <div class="col-sm-6">
                                                        <p class="form-control-static">
                                                            @if ((int) $survey->unit_panduan_value > 0)
                                                                Ya, {{ $survey->unit_panduan_value }}
                                                            @else
                                                                Tidak
                                                            @endif
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-6 control-label">Unit Kunci</label>
                                                    <div class="col-sm-6">
                                                        <p class="form-control-static">
                                                            @if ((int) $survey->unit_kunci_value > 0)
                                                                Ya, {{ $survey->unit_kunci_value }}
                                                            @else
                                                                Tidak
                                                            @endif
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-6 control-label">Unit Scrapper</label>
                                                    <div class="col-sm-6">
                                                        <p class="form-control-static">
                                                            @if ((int) $survey->unit_scrapper_value > 0)
                                                                Ya, {{ $survey->unit_scrapper_value }}
                                                            @else
                                                                Tidak
                                                            @endif
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-6 control-label">Lain - lain</label>
                                                    <div class="col-sm-6">
                                                        <p class="form-control-static">
                                                            @if ((int) $survey->unit_lainnya_value > 0)
                                                                Ya, {{ $survey->unit_lainnya_value }}
                                                            @else
                                                                Tidak
                                                            @endif
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-6 control-label">Pihak Penerima toko sesuai antara
                                                        KTP
                                                        dan asli</label>
                                                    <div class="col-sm-6">
                                                        <p class="form-control-static">
                                                            @if ($survey->penerima_sesuai == true)
                                                                Ya
                                                            @else
                                                                Tidak
                                                            @endif
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-6 control-label">POSM (highlighter) terpasang
                                                        semua</label>
                                                    <div class="col-sm-6">
                                                        <p class="form-control-static">
                                                            @if ($survey->posm == true)
                                                                Ya
                                                            @else
                                                                Tidak
                                                            @endif
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-6 control-label">Kardus dan Styrofoam terlepas
                                                        dengan
                                                        baik dan bersih</label>
                                                    <div class="col-sm-6">
                                                        <p class="form-control-static">
                                                            @if ($survey->kardus_bersih == true)
                                                                Ya
                                                            @else
                                                                Tidak
                                                            @endif
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-6 control-label">Instruksi penanganan cabinet
                                                        sudah
                                                        diberikan</label>
                                                    <div class="col-sm-6">
                                                        <p class="form-control-static">
                                                            @if ($survey->instruksi_diberikan == true)
                                                                Ya
                                                            @else
                                                                Tidak
                                                            @endif
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="portlet">
                        <!-- Block Header -->


                        <div class="portlet-heading bg-primary">
                            <h3 class="portlet-title"> IMAGE </h3>

                            <div class="portlet-widgets">
                                <span class="divider"></span>
                                <a data-toggle="collapse" data-parent="#accordion1" href="#bg-image"><i
                                        class="ion-minus-round"></i></a>
                                <span class="divider"></span>
                            </div>
                            <div class="clearfix"></div>
                        </div>

                        <div id="bg-image" class="panel-collapse collapse in">
                            <div class="portlet-body">
                                <div class="container-fluid">
                                    <div id="image-row" class="row"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('styles')
    <link href="{{ asset('assets/plugins/custombox/css/custombox.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet"
        type="text/css">
@endpush

@push('scripts')
    <script src="{{ asset('assets/plugins/custombox/js/custombox.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/custombox/js/legacy.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script>
        function initMap() {
            var myLatLng = {
                lat: {{ $data->latitude }},
                lng: {{ $data->longitude }}
            };
            var map = new google.maps.Map(
                document.getElementById('map'), {
                    zoom: 16,
                    center: myLatLng
                });
            var marker = new google.maps.Marker({
                position: myLatLng,
                map: map,
                title: '{{ $data->name }}',
                label: '{{ $data->name }}',
            });
        }
    </script>
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=&callback=initMap"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $(".date").datepicker({
                "format": "yyyy-mm-dd"
            });
            $("[name='button-approve']").click(function(e) {
                var id = $(this).data('id');
                swal({
                    title: "Outlet Approval",
                    text: "Approve Outlet?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Approve",
                    cancelButtonText: "Cancel"
                }).then(function(isConfirm) {
                    if (isConfirm) {
                        $("[name='form-approve-" + id + "']").submit();
                    }
                });
            });

            var listRejectReasons = <?php echo $rejectReasons; ?>;
            var statusProgress = <?php echo $outletRetractionProgress->status_progress; ?>;
            $("[name='button-cancel']").click(function(e) {
                var id = $(this).data('id');
                var form = $(this).closest('[name^=form-cancel]');
                var rejectReason = form.find("[name=reject_reason_id]");
                var rejectReasonId = statusProgress == 4 ? rejectReason.attr('value') : '';
                swal({
                    title: "Outlet Approval",
                    text: "Cancel Outlet?",
                    type: "warning",
                    input: 'select',
                    inputOptions: listRejectReasons,
                    inputValue: rejectReasonId,
                    inputPlaceholder: 'Pilih Alasan',
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes",
                    cancelButtonText: "No",
                    inputValidator: (value) => {
                        return new Promise((resolve, reject) => {
                            if (value != '') {
                                resolve();
                            } else {
                                reject('Alasan wajib dipilih!');
                            }
                        });
                    }
                }).then(function(result) {
                    if (result) {
                        rejectReason.attr('value', result);
                        form.submit();
                    }
                });
            });

            $("[name='button-postpone']").click(function(e) {
                var id = $(this).data('id');
                var form = $(this).closest('[name^=form-postpone]');
                var rejectReason = form.find("[name=reject_reason_id]");
                var rejectReasonId = statusProgress == 3 ? rejectReason.attr('value') : '';
                swal({
                    title: "Outlet Approval",
                    text: "Tunda Outlet?",
                    type: "warning",
                    input: 'select',
                    inputOptions: listRejectReasons,
                    inputValue: rejectReasonId,
                    inputPlaceholder: 'Pilih Alasan',
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Tunda",
                    cancelButtonText: "Cancel",
                    inputValidator: (value) => {
                        return new Promise((resolve, reject) => {
                            if (value != '') {
                                resolve();
                            } else {
                                reject('Alasan wajib dipilih!');
                            }
                        });
                    }
                }).then(function(result) {
                    if (result) {
                        rejectReason.attr('value', result);
                        form.submit();
                    }
                });
            });

            $("[name='button-save']").click(function(e) {
                var id = $(this).data('id');
                swal({
                    title: "Outlet Approval",
                    text: "Save Outlet?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Save",
                    cancelButtonText: "Cancel"
                }).then(function(isConfirm) {
                    if (isConfirm) {
                        $("[name='form-update-" + id + "']").submit();
                    }
                });
            });

            let tpl = `
                <div class="col-sm-6 col-lg-6 col-md-6">
                    <div class="card-box">
                    </div>
                </div>
            `;
            let pictureIds = [];
            @foreach ($pictureIds as $pictureId)
                pictureIds.push('{!! $pictureId !!}');
            @endforeach
            let imageRow = $('#image-row');
            for (let i = 0; i < pictureIds.length; i++) {
                setTimeout(function() {
                    let url = '{{ url('answer-image/picture/') }}/' + pictureIds[i];
                    let container = $(tpl);
                    let img = $('<img class="thumb-img" alt="' + pictureIds[i] + '"/>').attr('src', url)
                        .on('load', function() {
                            container.append(img);
                            imageRow.append(container);
                        });
                }, 100);
            }
        });
    </script>
@endpush

@include('layouts.alert2')
