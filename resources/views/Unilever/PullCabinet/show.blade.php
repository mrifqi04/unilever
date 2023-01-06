@extends('layouts.app')
@section('content')

    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card-box">
                        <ol class="breadcrumb">
                            <li>
                                Unilever
                            </li>
                            <li>
                                <a href="{{route('unilever.pull-cabinet.index')}}">Approval Tarik Kabinet</a>
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
                            {{ Form::label('id', 'SERU ID', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                {{ $data->id }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('unilever_id', 'Unilever ID', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                {{ $data->id_unilever }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('juragan', 'Juragan', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                {{ $data->juragan->name }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('name', 'Name', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                {{ $data->name }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('owner', 'Outlet Owner', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                {{ $data->owner }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('phone', 'Phone', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                {{ $data->phone }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('phone2', 'Phone 2', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                {{ $data->phone2 }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('address', 'Address', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                {{ $data->address }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('latitude', 'Latitude', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                {{ $data->latitude }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('longitude', 'Longitude', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                {{ $data->longitude }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('province', 'Province', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                {{ $data->province->name }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('city', 'City', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                {{ $data->city->name }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('district', 'District', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                {{ $data->district->name }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('village', 'Village', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                {{ $data->village->name }}
                            </div>
                        </div>
                        <div class="form-group">

                            <div class="col-12">
                                <a class="btn btn-danger m-t-20" href="{{ route('unilever.pull-cabinet.index') }}">
                                    <i class="fa fa-times"></i>&nbsp;Back
                                </a>
                                @if($canChangeDeliveryDate)
                                    <div class="pull-right">
                                        <a href="#modal-delivery-date"
                                           class="btn btn-success m-t-20"
                                           data-animation="fadein" data-plugin="custommodal"
                                           data-overlayspeed="200" data-overlaycolor="#36404a"
                                           title="Delivery Date">
                                            <i class="ion-calendar"></i>&nbsp;Delivery Date
                                        </a>
                                    </div>
                                    <!-- Modal -->
                                    <div id="modal-delivery-date" class="modal-demo">
                                        <button type="button" class="close" onclick="Custombox.close();">
                                            <span>&times;</span><span class="sr-only">Close</span>
                                        </button>
                                        <h4 class="custom-modal-title">Change Delivery Date</h4>
                                        <div class="modal-body">
                                            <div class="row">
                                                <form action="{{route('unilever.pull-cabinet.change-delivery-date',['unilever' => $outletRetractionProgress->id])}}"
                                                      method="POST" class="form-horizontal">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="form-group">
                                                        <label for="send_date"
                                                               class="col-md-4 control-label">Delivery
                                                            Date</label>
                                                        <div class="col-md-6">
                                                            <input name="send_date"
                                                                   value="{{!is_null($data->send_date) ? \Illuminate\Support\Carbon::parse($data->send_date)->format('Y-m-d'): \Illuminate\Support\Carbon::now()->format('Y-m-d')}}"
                                                                   required
                                                                   class="form-control date">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="col-md-4"></div>
                                                        <div class="col-md-10">
                                                            <button class="btn btn-success m-t-20 pull-right">
                                                                <i class="fa fa-check"></i> Save
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                @if($canReject)
                                    <form action="{{route('unilever.pull-cabinet.reject',['unilever' => $outletRetractionProgress->id])}}"
                                          method="POST" class="pull-right"
                                          name="form-reject-{{$outletRetractionProgress->id}}">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="reject_reason_id" value="{{$outletRetractionProgress->section !== 'uli' ? '' : $outletRetractionProgress->reject_reason_id}}">
                                        <button name="button-reject" class="btn btn-danger m-t-20" title="approve" type="button" data-id="{{$outletRetractionProgress->id}}">
                                            <i class="ion-close"></i>&nbsp;Reject
                                        </button>
                                    </form>
                                @endif
                                @if($canApprove)
                                    <form action="{{route('unilever.pull-cabinet.approve',['unilever' => $outletRetractionProgress->id])}}"
                                          method="POST" class="pull-right"
                                          name="form-approve-{{$outletRetractionProgress->id}}">
                                        @csrf
                                        @method('PUT')
                                        <button name="button-approve" class="btn btn-primary m-t-20" title="approve" type="button" data-id="{{$outletRetractionProgress->id}}">
                                            <i class="ion-checkmark"></i>&nbsp;Approve
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

                                    <div class="row">
                                        <div class="form-horizontal">
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">Jenis Toko</label>
                                                <div class="col-sm-8">
                                                    <p class="form-control-static">{{$survey->id_outlet_type}}</p>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">Status Kepemilikan</label>
                                                <div class="col-sm-8">
                                                    <p class="form-control-static">{{$survey->id_ownership_status}}</p>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">Tipe Jalan</label>
                                                <div class="col-sm-8">
                                                    <p class="form-control-static">{{$survey->id_street_type}}</p>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">Radius 50-100 Dekat dengan</label>
                                                <div class="col-sm-8">
                                                    <p class="form-control-static">{{$survey->area_radius}}</p>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">Sudah berjualan es krim?</label>
                                                <div class="col-sm-8">
                                                    <p class="form-control-static">
                                                        @if($survey->selling->selling == "1")
                                                            Ya,
                                                            @foreach($survey->selling->name as $sn)
                                                                {{$sn->brandName}},
                                                            @endforeach
                                                        @else
                                                            Tidak
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">Ada kulkas dirumah?</label>
                                                <div class="col-sm-8">
                                                    <p class="form-control-static">
                                                        @if($survey->kulkas->exist == "1")
                                                            Ya, {{$survey->kulkas->type}} Pintu
                                                        @else
                                                            Tidak
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">Bersedia Membeli Paket
                                                    Perdana?</label>
                                                <div class="col-sm-8">
                                                    <p class="form-control-static">
                                                        @if($survey->perdana == "1")
                                                            Ya
                                                        @else
                                                            Tidak
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">Tersedia tempat untuk
                                                    cabinet?</label>
                                                <div class="col-sm-8">
                                                    <p class="form-control-static">
                                                        @if($survey->freezer == "1")
                                                            Ya
                                                        @else
                                                            Tidak
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">Kapasitas listrik (VA)</label>
                                                <div class="col-sm-8">
                                                    <p class="form-control-static">{{$survey->electricity_capacity}}</p>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">Frekuensi Mati Listrik dalam 1
                                                    bulan</label>
                                                <div class="col-sm-8">
                                                    <p class="form-control-static">{{$survey->blackout_intensity}}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

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
    <link href="{{asset('assets/plugins/custombox/css/custombox.css')}}" rel="stylesheet">
    <link href="{{asset('assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css')}}" rel="stylesheet"
          type="text/css">
@endpush

@push('scripts')
    <script src="{{asset('assets/plugins/custombox/js/custombox.min.js')}}"></script>
    <script src="{{asset('assets/plugins/custombox/js/legacy.min.js')}}"></script>
    <script src="{{asset('assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>
    <script>
        function initMap() {
            var myLatLng = {lat: {{$data->latitude}}, lng: {{$data->longitude}}};
            var map = new google.maps.Map(
                document.getElementById('map'), {zoom: 16, center: myLatLng});
            var marker = new google.maps.Marker({
                position: myLatLng,
                map: map,
                title: '{{ $data->name }}',
                label: '{{ $data->name }}',
            });
        }
    </script>
    <script async defer
            src="https://maps.googleapis.com/maps/api/js?key=&callback=initMap">
    </script>
    <script type="text/javascript">
        $(document).ready(function () {
            $(".date").datepicker({"format": "yyyy-mm-dd"});
            $("[name='button-approve']").click(function (e) {
                var id = $(this).data('id');
                swal({
                        title: "Outlet Approval",
                        text: "Approve Outlet?",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Approve",
                        cancelButtonText: "Cancel"
                    }).then(function (isConfirm) {
                        if (isConfirm) {
                            $("[name='form-approve-" + id + "']").submit();
                        }
                    }
                );
            });
            
            var listRejectReasons = <?php echo $rejectReasons; ?>;
            $("[name='button-reject']").click(function (e) {
                var id             = $(this).data('id');
                var form           = $(this).closest('[name^=form-reject]');
                var rejectReason   = form.find("[name=reject_reason_id]");
                var rejectReasonId = rejectReason.attr('value');
                swal({
                        title: "Outlet Approval",
                        text: "Reject Outlet?",
                        type: "warning",
                        input: 'select',
                        inputOptions: listRejectReasons,
                        inputValue: rejectReasonId,
                        inputPlaceholder: 'Pilih Alasan',
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Reject",
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
                    }).then(function (result) {
                        if (result) {
                            rejectReason.attr('value', result);
                            form.submit();
                        }
                    }
                );
            });
            let tpl = `
                <div class="col-sm-6 col-lg-6 col-md-6">
                    <div class="card-box">
                    </div>
                </div>
            `;
            let pictureIds = [];
            @foreach($pictureIds as $pictureId)
            pictureIds.push('{!! $pictureId !!}');
                    @endforeach
            let imageRow = $('#image-row');
            for (let i = 0; i < pictureIds.length; i++) {
                setTimeout(function () {
                    let url = '{{url('answer-image/picture/')}}/' + pictureIds[i];
                    let container = $(tpl);
                    let img = $('<img class="thumb-img" alt="' + pictureIds[i] + '"/>').attr('src', url)
                        .on('load', function () {
                            container.append(img);
                            imageRow.append(container);
                        });
                }, 100);
            }
        });
    </script>
@endpush

@include('layouts.alert2')