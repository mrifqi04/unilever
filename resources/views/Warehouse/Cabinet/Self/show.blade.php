@extends('layouts.app')
@section('content')

    <!-- Start content -->
    <div class="content">

        <div class="wraper container">

            <!-- Page-Title -->
            <div class="row">
                <div class="col-sm-12">
                    {{--@if(Auth::user()->getPermissionByName('pull_self.status'))--}}
                    @if($data->Status->id == 1)
                        <div class="btn-group pull-right m-t-15">
                            <button class="btn waves-effect waves-light btn-sm btn-default approve"
                                    data-id="{{$data->id}}">
                                Approve
                            </button>
                            <button class="btn waves-effect waves-light btn-sm btn-danger reject"
                                    data-id="{{$data->id}}">
                                Reject
                            </button>
                        </div>
                    @endif
                    @if(Auth::user()->roles[0]->id == '64132dec-322e-474a-a003-4c68ccc510fd')
                        @foreach ($data->DetailMandiri->ShippingMandiri()->get() as $sm)
                            @php($ship['ship'][] = $sm->shipping_type_id)
                            @php($ship['answer'][] = $sm->answer_id)
                        @endforeach
                        @if($data->Status->id == 2 && in_array(1, $ship['ship']) && in_array("", $ship['answer']) && $data->Approval->asm_user_id == "")
                            <div class="btn-group pull-right m-t-15">
                                <button class="btn waves-effect waves-light btn-sm btn-default approve"
                                        data-id="{{$data->id}}">
                                    Approve
                                </button>
                                <button class="btn waves-effect waves-light btn-sm btn-danger reject"
                                        data-id="{{$data->id}}">
                                    Reject
                                </button>
                            </div>
                        @endif
                    @endif
                    {{--@endif--}}

                    <h4 class="page-title">Cabinet</h4>
                    <ol class="breadcrumb">
                        <li><a href="{{route('pull_self.index')}}">Self exchange</a></li>
                        <li class="active">Show</li>
                    </ol>
                </div>
            </div>

            @foreach($data->DetailMandiri->ShippingMandiri()->get() as $sm)
                @if($sm->shipping_type_id == 1)
                    @php($penarikan = date('l, j F Y', strtotime($sm->shipping_date)))
                @endif
                @if($sm->shipping_type_id == 2)
                    @php($pengiriman = date('l, j F Y', strtotime($sm->shipping_date)))
                @endif
            @endforeach

            <div class="row">
                <div class="col-md-4 col-lg-6">
                    <div class="profile-detail card-box">
                        <div>
                            <h4 class="text-uppercase font-600">Data Toko <span
                                        class="font-bold text-danger">Asal</span></h4>
                            {{--<p class="text-muted font-13 m-b-30">--}}
                            {{--Hi I'm Johnathn Deo,has been the industry's standard dummy text ever since the--}}
                            {{--1500s, when an unknown printer took a galley of type.--}}
                            {{--</p>--}}

                            <div class="text-left">
                                <p class="text-muted font-13"><strong>Juragan :</strong> <span
                                            class="m-l-15">{{$data->juragan->name}}</span>
                                </p>

                                <p class="text-muted font-13"><strong>ID Juragan :</strong><span
                                            class="m-l-15">{{$data->juragan->id_unilever_owner}}</span>
                                </p>

                                <p class="text-muted font-13"><strong>ID Seru Toko :</strong> <span
                                            class="m-l-15">{{$data->DetailMandiri->outlet->id}}</span>
                                </p>

                                <p class="text-muted font-13"><strong>Nama Toko :</strong> <span
                                            class="m-l-15">{{$data->DetailMandiri->outlet->name}}</span>
                                </p>

                                <p class="text-muted font-13"><strong>Nama Pemilik :</strong><span
                                            class="m-l-15">{{$data->DetailMandiri->outlet->owner}}</span>
                                </p>

                                <p class="text-muted font-13"><strong>Phone :</strong> <span
                                            class="m-l-15">{{$data->DetailMandiri->outlet->phone}}</span>
                                </p>

                                <p class="text-muted font-13"><strong>Alamat :</strong> <span
                                            class="m-l-15">{{$data->DetailMandiri->outlet->address}}</span>
                                </p>

                                <p class="text-muted font-13"><strong>Kecamatan :</strong> <span
                                            class="m-l-15">{{$data->DetailMandiri->outlet->province->name}}</span>
                                </p>

                                <p class="text-muted font-13"><strong>Kelurahan :</strong> <span
                                            class="m-l-15">{{$data->DetailMandiri->outlet->city->name}}</span>
                                </p>

                                <p class="text-muted font-13"><strong>Kecamatan :</strong> <span
                                            class="m-l-15">{{$data->DetailMandiri->outlet->district->name}}</span>
                                </p>

                                <p class="text-muted font-13"><strong>Kelurahan :</strong> <span
                                            class="m-l-15">{{$data->DetailMandiri->outlet->village->name}}</span>
                                </p>
                            </div>

                            <hr>

                            <h4 class="text-uppercase font-600">Penarikan</h4>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="text-left">
                                        <p class="text-muted font-13"><strong>Tgl. Permintaan :</strong> <span
                                                    class="m-l-15">{{date('l, j F Y', strtotime($data->created_at))}}</span>
                                        </p>

                                        <p class="text-muted font-13"><strong>Tgl. Penarikan :</strong><span
                                                    class="m-l-15">{{$penarikan}}</span>
                                        </p>

                                        <p class="text-muted font-13"><strong>Cabinet (Model Type) :</strong> <span
                                                    class="m-l-15">{{$data->DetailMandiri->Cabinet->brand}}
                                                ({{$data->DetailMandiri->Cabinet->model_type}})
                                                , SN {{$data->DetailMandiri->Cabinet->serialnumber}}
                                                , {{$data->DetailMandiri->Cabinet->qrcode}}
                                                , {{$data->DetailMandiri->Cabinet->model_type}}</span>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="text-left">
                                        @if($data->status_id != 6 || $data->status_id != 1)
                                            @php($by = $data->DetailMandiri->ShippingMandiri()->where('shipping_type_id', 1)->first()->driver_no)
                                            @php($car = $data->DetailMandiri->ShippingMandiri()->where('shipping_type_id', 1)->first()->vahicle_plate_no)
                                        @else
                                            @php($by = "")
                                            @php($car = "")
                                        @endif
                                        <p class="text-muted font-13"><strong>Dilakukan Oleh :</strong><span
                                                    class="m-l-15">{{$by}}</span>
                                        </p>
                                        <p class="text-muted font-13"><strong>Kendaraan :</strong><span
                                                    class="m-l-15">{{$car}}</span>
                                        </p>
                                        <p class="text-muted font-13"><strong>Alasan :</strong> <span
                                                    class="m-l-15">{{$data->DetailMandiri->reason}}</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="col-md-4 col-lg-6">
                    <div class="profile-detail card-box">
                        <h4 class="text-uppercase font-600">Data Toko <span class="font-bold text-success">Tujuan</span>
                        </h4>
                        {{--<p class="text-muted font-13 m-b-30">--}}
                        {{--Hi I'm Johnathn Deo,has been the industry's standard dummy text ever since the--}}
                        {{--1500s, when an unknown printer took a galley of type.--}}
                        {{--</p>--}}

                        <div class="text-left">
                            <p class="text-muted font-13"><strong>Juragan :</strong> <span
                                        class="m-l-15">{{$data->juragan->name}}</span>
                            </p>

                            <p class="text-muted font-13"><strong>ID Juragan :</strong><span
                                        class="m-l-15">{{$data->juragan->id_unilever_owner}}</span>
                            </p>

                            <p class="text-muted font-13"><strong>ID Seru Toko :</strong> <span
                                        class="m-l-15">{{$data->DetailMandiri->destoutlet->id}}</span>
                            </p>

                            <p class="text-muted font-13"><strong>Nama Toko :</strong> <span
                                        class="m-l-15">{{$data->DetailMandiri->destoutlet->name}}</span>
                            </p>

                            <p class="text-muted font-13"><strong>Nama Pemilik :</strong><span
                                        class="m-l-15">{{$data->DetailMandiri->destoutlet->owner}}</span>
                            </p>

                            <p class="text-muted font-13"><strong>Phone :</strong> <span
                                        class="m-l-15">{{$data->DetailMandiri->destoutlet->phone}}</span>
                            </p>

                            <p class="text-muted font-13"><strong>Alamat :</strong> <span
                                        class="m-l-15">{{$data->DetailMandiri->destoutlet->address}}</span>
                            </p>

                            <p class="text-muted font-13"><strong>Kecamatan :</strong> <span
                                        class="m-l-15">{{$data->DetailMandiri->destoutlet->province->name}}</span>
                            </p>

                            <p class="text-muted font-13"><strong>Kelurahan :</strong> <span
                                        class="m-l-15">{{$data->DetailMandiri->destoutlet->city->name}}</span>
                            </p>

                            <p class="text-muted font-13"><strong>Kecamatan :</strong> <span
                                        class="m-l-15">{{$data->DetailMandiri->destoutlet->district->name}}</span>
                            </p>

                            <p class="text-muted font-13"><strong>Kelurahan :</strong> <span
                                        class="m-l-15">{{$data->DetailMandiri->destoutlet->village->name}}</span>
                            </p>

                        </div>

                        <hr>

                        <h4 class="text-uppercase font-600">Pengiriman</h4>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="text-left">
                                    <p class="text-muted font-13"><strong>Tgl. Permintaan :</strong> <span
                                                class="m-l-15">{{date('l, j F Y', strtotime($data->created_at))}}</span>
                                    </p>

                                    <p class="text-muted font-13"><strong>Tgl. Pengiriman :</strong><span
                                                class="m-l-15">{{$pengiriman}}</span>
                                    </p>
                                    <p class="text-muted font-13"><strong>Cabinet (Model Type) :</strong> <span
                                                class="m-l-15">{{$data->DetailMandiri->Cabinet->brand}}
                                            ({{$data->DetailMandiri->Cabinet->model_type}})
                                            , SN {{$data->DetailMandiri->Cabinet->serialnumber}}
                                            , {{$data->DetailMandiri->Cabinet->qrcode}}
                                            </span>
                                    </p>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="text-left">
                                    @if($data->status_id != 6 || $data->status_id != 1)
                                        @php($by = $data->DetailMandiri->ShippingMandiri()->where('shipping_type_id', 1)->first()->driver_no)
                                        @php($car = $data->DetailMandiri->ShippingMandiri()->where('shipping_type_id', 1)->first()->vahicle_plate_no)
                                    @else
                                        @php($by = "")
                                        @php($car = "")
                                    @endif
                                    <p class="text-muted font-13"><strong>Dilakukan Oleh :</strong><span
                                                class="m-l-15">{{$by}}</span>
                                    </p>
                                    <p class="text-muted font-13"><strong>Kendaraan :</strong><span
                                                class="m-l-15">{{$car}}</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 col-lg-6">
                    <div class="profile-detail card-box">
                        <h4 class="text-uppercase font-600">Survey Toko <span
                                    class="font-bold text-danger">Asal</span></h4>
                        @if($survey_tarik != null)
                            @php($st = json_decode($survey_tarik))
                            @php($kunci =($st->unit_kunci_status == '1')?'Ya':'Tidak')
                            @php($scrapper =($st->unit_scrapper_status == '1')?'Ya':'Tidak')
                            @php($cabinet =($st->unit_cabinet_status == '1')?'Ya':'Tidak')
                            @php($lainnya =($st->unit_lainnya_status == '1')?'Ya':'Tidak')
                            @php($lainnya =($st->unit_panduan_status == '1')?'Ya':'Tidak')
                            @php($keranjang =($st->unit_keranjang_status == '1')?'Ya':'Tidak')
                            @php($highlighter =($st->unit_highlighter_status == '1')?'Ya':'Tidak')
                            <div class="text-left">
                                <p class="text-muted font-13"><strong>Kunci :</strong> <span
                                            class="m-l-15">{{$kunci}}, {{$st->unit_kunci_value}}</span>
                                </p>
                                <p class="text-muted font-13"><strong>Scrapper :</strong> <span
                                            class="m-l-15">{{$scrapper}}, {{$st->unit_scrapper_value}}</span>
                                </p>
                                <p class="text-muted font-13"><strong>Cabinet :</strong> <span
                                            class="m-l-15">{{$cabinet}}, {{$st->unit_cabinet_value}}</span>
                                </p>
                                <p class="text-muted font-13"><strong>Lainnya :</strong> <span
                                            class="m-l-15">{{$lainnya}}, {{$st->unit_lainnya_value}}</span>
                                </p>
                                <p class="text-muted font-13"><strong>Panduan :</strong> <span
                                            class="m-l-15">{{$lainnya}}, {{$st->unit_panduan_value}}</span>
                                </p>
                                <p class="text-muted font-13"><strong>Keranjang :</strong> <span
                                            class="m-l-15">{{$keranjang}}, {{$st->unit_keranjang_value}}</span>
                                </p>
                                <p class="text-muted font-13"><strong>Highlighter :</strong> <span
                                            class="m-l-15">{{$kunci}}, {{$st->unit_highlighter_value}}</span>
                                </p>
                            </div>
                        @endif

                    </div>
                </div>

                <div class="col-md-4 col-lg-6">
                    <div class="profile-detail card-box">
                        <h4 class="text-uppercase font-600">Survey Toko <span
                                    class="font-bold text-success">Tujuan</span></h4>
                        @if($survey_kirim != null)
                            @php($sk = json_decode($survey_kirim))
                            @php($kunci =($sk->unit_kunci_status == '1')?'Ya':'Tidak')
                            @php($scrapper =($sk->unit_scrapper_status == '1')?'Ya':'Tidak')
                            @php($cabinet =($sk->unit_cabinet_status == '1')?'Ya':'Tidak')
                            @php($lainnya =($sk->unit_lainnya_status == '1')?'Ya':'Tidak')
                            @php($lainnya =($sk->unit_panduan_status == '1')?'Ya':'Tidak')
                            @php($keranjang =($sk->unit_keranjang_status == '1')?'Ya':'Tidak')
                            @php($highlighter =($sk->unit_highlighter_status == '1')?'Ya':'Tidak')
                            <div class="text-left">
                                <p class="text-muted font-13"><strong>Kunci :</strong> <span
                                            class="m-l-15">{{$kunci}}, {{$sk->unit_kunci_value}}</span>
                                </p>
                                <p class="text-muted font-13"><strong>Scrapper :</strong> <span
                                            class="m-l-15">{{$scrapper}}, {{$sk->unit_scrapper_value}}</span>
                                </p>
                                <p class="text-muted font-13"><strong>Cabinet :</strong> <span
                                            class="m-l-15">{{$cabinet}}, {{$sk->unit_cabinet_value}}</span>
                                </p>
                                <p class="text-muted font-13"><strong>Lainnya :</strong> <span
                                            class="m-l-15">{{$lainnya}}, {{$sk->unit_lainnya_value}}</span>
                                </p>
                                <p class="text-muted font-13"><strong>Panduan :</strong> <span
                                            class="m-l-15">{{$lainnya}}, {{$sk->unit_panduan_value}}</span>
                                </p>
                                <p class="text-muted font-13"><strong>Keranjang :</strong> <span
                                            class="m-l-15">{{$keranjang}}, {{$sk->unit_keranjang_value}}</span>
                                </p>
                                <p class="text-muted font-13"><strong>Highlighter :</strong> <span
                                            class="m-l-15">{{$kunci}}, {{$sk->unit_highlighter_value}}</span>
                                </p>
                            </div>
                        @endif

                    </div>
                </div>

                <div class="col-md-8 col-lg-6">
                    <div class="portlet">
                        <!-- Block Header -->
                        <div class="portlet-heading bg-danger">
                            <h3 class="portlet-title"> Image Toko Asal </h3>

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
                                    <div id="image-row-asal" class="row"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-8 col-lg-6">
                    <div class="portlet">
                        <!-- Block Header -->
                        <div class="portlet-heading bg-success">
                            <h3 class="portlet-title"> Image Toko Tujuan </h3>

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
                                    <div id="image-row-tujuan" class="row"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="col-md-8 col-lg-8">
                    <div class="profile-detail card-box">
                        <div>
                            <h4 class="text-uppercase font-600">Map</h4>

                            <div id="map" class="gmaps"></div>
                        </div>

                    </div>
                </div>

                <div class="col-lg-4 col-md-4">
                    <div class="card-box">
                        <div class="p-20">
                            <h4 class="m-b-20 header-title"><b>Activities</b></h4>
                            <div class="nicescroll p-l-r-10" style="max-height: 555px;">
                                <div class="timeline-2">
                                    @if(count($data->MandiriTimelines) > 0)
                                        @foreach($data->MandiriTimelines as $op)
                                            <div class="time-item">
                                                <div class="item-info">
                                                    <div class="text-muted">
                                                        <small>{{\Carbon\Carbon::parse($op->created_at)->format('M d Y H:i:s')}}</small>
                                                    </div>
                                                    <p>
                                                        Status
                                                        @php($approval = [
                                                                'uli' => ($data->Approval->unilever_user_id != "")?$data->Approval->UliUser->name: "",
                                                                'asm' => ($data->Approval->asm_user_id != "")?$data->Approval->AsmUser->name: ""
                                                            ])
                                                        @switch($op->status_id)
                                                            @case(1)
                                                            @php($style = 'text-info')
                                                            @break

                                                            @case(2)
                                                            @php($style = 'text-success')
                                                            @break

                                                            @case(3)
                                                            @php($style = 'text-warning')
                                                            @break

                                                            @case(4)
                                                            @php($style = 'text-warning')
                                                            @break

                                                            @case(5)
                                                            @php($style = 'text-success')
                                                            @break

                                                            @case(6)
                                                            @php($style = 'text-danger')
                                                            @break
                                                        @endswitch

                                                        @if ($op->status_id == 2 && $data->Approval->asm_user_id != "")
                                                            @php($word = ', by '.$approval['uli'].' & '.$approval['asm'])
                                                        @elseif ($op->status_id == 2 && $data->Approval->asm_user_id == "")
                                                            @php($word = ', by '.$approval['uli'])
                                                        @elseif ($op->status_id == 6 && $data->Approval->asm_user_id != "")
                                                            @php($word = ', by '.$approval['uli'].' & '.$approval['asm'])
                                                        @elseif ($op->status_id == 6 && $data->Approval->asm_user_id == "")
                                                            @php($word = ', by '.$approval['uli'])
                                                        @elseif($op->status_id != 6 || $op->status_id != 2)
                                                            @php($word = '')
                                                        @endif

                                                        <em class="font-bold {{$style}}">{{$op->Status->name}}</em> {{$word ?? ''}}
                                                        @if($op->status_id == 6 && $data->Approval->asm_user_id == "")
                                                            , Reason {{$data->Approval->unilever_approval_notes}}
                                                        @elseif($op->status_id == 6 && $data->Approval->asm_user_id != "")
                                                            , Reason {{$data->Approval->asm_approval_notes}}
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div> <!-- container -->

    </div> <!-- content -->

    @include('Warehouse.Cabinet.Self.modal')
@endsection

@include('layouts.alert')

@push('styles')
    <link href="{{asset('assets/plugins/custombox/css/custombox.css')}}" rel="stylesheet">
@endpush

@push('scripts')
    <script src="{{asset('assets/plugins/custombox/js/custombox.min.js')}}"></script>
    <script src="{{asset('assets/plugins/custombox/js/legacy.min.js')}}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            let tpl = `
                <div class="col-sm-6 col-lg-6 col-md-6">
                    <div class="card-box">
                    </div>
                </div>
            `;
            let pictureIds = [];
            @foreach($pictureOrg as $pictureId)
            pictureIds.push('{!! $pictureId !!}');
                    @endforeach
            let imageRow = $('#image-row-asal');
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

            let pictureDest = [];
            @foreach($pictureDest as $pictureId)
            pictureDest.push('{!! $pictureId !!}');
                    @endforeach
            let imageRowDest = $('#image-row-tujuan');
            for (let i = 0; i < pictureDest.length; i++) {
                setTimeout(function () {
                    let url = '{{url('answer-image/picture/')}}/' + pictureDest[i];
                    let container = $(tpl);
                    let img = $('<img class="thumb-img" alt="' + pictureDest[i] + '"/>').attr('src', url)
                        .on('load', function () {
                            container.append(img);
                            imageRowDest.append(container);
                        });
                }, 100);
            }

            $("#con-close-modal").on("hidden.bs.modal", function () {
                $("#field").html("");
            });

            $('.approve').on('click', function () {
                let id = $(this).data('id');
                let status = 2;
                let approval = 1;
                swal({
                    title: "Are you sure?",
                    text: "You will not be able to recover this data!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Yes",
                    cancelButtonText: "No",
                    closeOnConfirm: false,
                    closeOnCancel: false,
                    showLoaderOnConfirm: true
                }, function (isConfirm) {
                    if (!isConfirm) notif({
                        type: "success",
                        title: "Change Status",
                        message: "You didn't change status"
                    });
                    sendData('/pull_self/status/' + id, "POST", {
                        status: status,
                        approval_status: approval,
                        '_token': '{{ csrf_token() }}'
                    });
                });
            });

            $('.reject').on('click', function () {
                $('#withdraw_id').val($(this).data('id'));
                $('.modal-title').text('Reject status');
                $('#form').find('#field').remove().end();
                $('#form').append('<div class="row" id="field">\n' +
                    '                    <div class="col-md-12">\n' +
                    '                        <div class="form-group no-margin">\n' +
                    '                            <label for="field-7" class="control-label">Reason</label>\n' +
                    '                            <textarea class="form-control autogrow" id="field-7" placeholder="Write something reason" style="overflow: hidden; word-wrap: break-word; resize: horizontal; height: 104px;"></textarea>\n' +
                    '                        </div>\n' +
                    '                    </div>\n' +
                    '                </div>');
                $('#con-close-modal').modal('show');
                $('#submit').on('click', function () {
                    let data = {
                        status: 6,
                        approval_status: 2,
                        reason: $("#field-7").val(),
                        '_token': '{{ csrf_token() }}'
                    };
                    swal({
                        title: "Are you sure?",
                        text: "You will not be able to recover this data!",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonText: "Yes",
                        cancelButtonText: "No",
                        closeOnConfirm: false,
                        closeOnCancel: false,
                        showLoaderOnConfirm: true
                    }, function (isConfirm) {
                        if (!isConfirm) notif({
                            type: "success",
                            title: "Change Status",
                            message: "You didn't change status"
                        });
                        sendData('/pull_self/status/' + $('#withdraw_id').val(), "POST", data);
                    });
                });
            });

            function notif(result) {
                swal({
                    type: result.type,
                    title: result.title,
                    text: result.message,
                    timer: 3000,
                }, function () {
                    if (result.status) {
                        location.reload();
                    }
                    $('#con-close-modal').modal('hide');
                });
            }

            function sendData(route, metode, data) {
                let result = {};
                $.ajax({
                    type: metode,
                    url: route,
                    data: data,
                    success: function (resp) {
                        result = resp;
                        notif(result);
                    },
                });
                return result;
            }
        });
    </script>
    <script>
        // Initialize and add the map
        function initMap() {

            var marker;
            var orgDesc = '{{$data->DetailMandiri->outlet->name}}, {{$data->DetailMandiri->outlet->address}}';
            var destDesc = '{{$data->DetailMandiri->destoutlet->name}}, {{$data->DetailMandiri->destoutlet->address}}';
            var destmyLatLng = [
                [orgDesc, {{$data->DetailMandiri->outlet->latitude}},
                    {{$data->DetailMandiri->outlet->longitude}}, 3, "http://maps.google.com/mapfiles/ms/icons/red-dot.png"],
                [destDesc, {{$data->DetailMandiri->destoutlet->latitude}},
                    {{$data->DetailMandiri->destoutlet->longitude}}, 3, "http://maps.google.com/mapfiles/ms/icons/green-dot.png"]
            ];
            var infowindow = new google.maps.InfoWindow();
            var bounds = new google.maps.LatLngBounds();
            var map = new google.maps.Map(
                document.getElementById('map'));
            for (i = 0; i < destmyLatLng.length; i++) {
                marker = new google.maps.Marker({
                    position: new google.maps.LatLng(destmyLatLng[i][1], destmyLatLng[i][2]),
                    map: map,
                    title: destmyLatLng[i][0],
                    icon: {
                        url: destmyLatLng[i][4]
                    }
                });
                bounds.extend(marker.position);

                google.maps.event.addListener(marker, 'click', (function (marker, i) {
                    return function () {
                        infowindow.setContent(destmyLatLng[i][0]);
                        infowindow.open(map, marker);
                    }
                })(marker, i));
            }
            var listener = google.maps.event.addListener(map, "idle", function () {
                map.setZoom(11);
                google.maps.event.removeListener(listener);
            });
            map.fitBounds(bounds);
        }
    </script>
    <script async defer
            src="https://maps.googleapis.com/maps/api/js?key=&callback=initMap">
    </script>
@endpush