@extends('layouts.app')
@section('content')

    <!-- Start content -->
    <div class="content">

        <div class="wraper container">

            <!-- Page-Title -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="btn-group pull-right m-t-15">

                        @if($journey_plan_id != '')
                            @if((Auth::user()->getPermissionByName('warehouse.route-plan-pull-cabinet.download-art')))
                            <a href="{{route('warehouse.route-plan-pull-cabinet.download-art', ['id' => $data->id])}}" class="btn btn-primary m-r-5"><i class="fa fa-download"></i> Download ART</a>
                            @endif

                            <button type="button" class="btn btn-default dropdown-toggle waves-effect waves-light"
                                    data-toggle="dropdown" aria-expanded="false">Cetak <span class="m-l-5"><i
                                            class="fa fa-cog"></i></span></button>
                            <ul class="dropdown-menu drop-menu-right" role="menu">
                                <li><a href="{{route('delivery.exportPDF')}}?id={{$journey_plan_id}}&type=Tarik">ADR / BAP</a></li>
                            </ul>
                        @endif
                    </div>

                    <h4 class="page-title">Show Route Plan</h4>
                    <ol class="breadcrumb">
                        <li><a href="#">Route Plan</a></li>
                        <li><a href="{{route('warehouse.route-plan-pull-cabinet.index')}}">Route Plans Management</a></li>
                        <li class="active">Show</li>
                    </ol>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8 col-lg-8">
                    <div class="profile-detail card-box">
                        <div>
                            <h4 class="text-uppercase font-600">Data Toko</h4>

                            <div class="text-left">
                                <p class="text-muted font-13"><strong>Juragan :</strong> <span
                                            class="m-l-15">{{$data->outlet->juragan->name}}</span>
                                </p>

                                <p class="text-muted font-13"><strong>ID Juragan :</strong><span
                                            class="m-l-15">{{$data->outlet->juragan->id}}</span>
                                </p>

                                <p class="text-muted font-13"><strong>ID Seru Toko :</strong> <span
                                            class="m-l-15">{{$data->outlet->id}}</span>
                                </p>

                                <p class="text-muted font-13"><strong>Nama Toko :</strong> <span
                                            class="m-l-15">{{$data->outlet->name}}</span>
                                </p>

                                <p class="text-muted font-13"><strong>Nama Pemilik :</strong><span
                                            class="m-l-15">{{$data->outlet->owner}}</span>
                                </p>

                                <p class="text-muted font-13"><strong>Alamat :</strong> <span
                                            class="m-l-15">{{$data->outlet->address}}</span>
                                </p>

                                <p class="text-muted font-13"><strong>Phone :</strong> <span
                                            class="m-l-15">{{$data->outlet->phone}}</span>
                                </p>

                                <p class="text-muted font-13"><strong>Kecamatan :</strong> <span
                                            class="m-l-15">{{$data->outlet->district->name}}</span>
                                </p>

                                <p class="text-muted font-13"><strong>Kelurahan :</strong> <span
                                            class="m-l-15">{{$data->outlet->village->name}}</span>
                                </p>
                            </div>

                            @if($data->survey != null)

                                <hr>

                                <h4 class="text-uppercase font-600">Survey</h4>

                                <div class="text-left">
                                    <p class="text-muted font-13"><strong>Jenis Toko :</strong> <span class="m-l-15">Johnathan Deo</span>
                                    </p>

                                    <p class="text-muted font-13"><strong>Status Kepemilikan :</strong><span
                                                class="m-l-15">(123) 123 1234</span>
                                    </p>

                                    <p class="text-muted font-13"><strong>Tipe Jalan :</strong> <span class="m-l-15">coderthemes@gmail.com</span>
                                    </p>

                                    <p class="text-muted font-13"><strong>Radius 50 - 100 dekat dengan :</strong> <span
                                                class="m-l-15">Johnathan Deo</span>
                                    </p>

                                    <p class="text-muted font-13"><strong>Sudah berjualan es krim? :</strong><span
                                                class="m-l-15">(123) 123 1234</span>
                                    </p>

                                    <p class="text-muted font-13"><strong>Ada kulkas di rumah? :</strong> <span
                                                class="m-l-15">coderthemes@gmail.com</span>
                                    </p>

                                    <p class="text-muted font-13"><strong>Bersedia membeli Paket Perdana? :</strong>
                                        <span class="m-l-15">Johnathan Deo</span>
                                    </p>

                                    <p class="text-muted font-13"><strong>Tersedia tempat untuk Cabinet :</strong> <span
                                                class="m-l-15">coderthemes@gmail.com</span>
                                    </p>

                                    <p class="text-muted font-13"><strong>Kapasitas Listrik (VA) :</strong> <span
                                                class="m-l-15">Johnathan Deo</span>
                                    </p>

                                    <p class="text-muted font-13"><strong>Frekuensi Mati Listrik dalam 1 bulan
                                            :</strong> <span class="m-l-15">Johnathan Deo</span>
                                    </p>

                                    <p class="text-muted font-13"><strong>Jadwal pengiriman Cabinet :</strong> <span
                                                class="m-l-15">Johnathan Deo</span>
                                    </p>
                                </div>
                            @endif
                        </div>

                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="card-box">
                        <div class="p-20">
                            <h4 class="m-b-20 header-title"><b>Activities</b></h4>
                            <div class="nicescroll p-l-r-10" style="max-height: 555px;">
                                <div class="timeline-2">
                                    @if(count($data->outlet->mapOutlet->OutletRetractionProgress) > 0)
                                        @foreach($data->outlet->mapOutlet->OutletRetractionProgress->sortBy("created_at") as $op)
                                            <div class="time-item">
                                                <div class="item-info">
                                                    <div class="text-muted">
                                                        <small>{{\Carbon\Carbon::parse($op->created_at)->format('M d Y H:i:s')}}</small>
                                                    </div>
                                                    <p><span class="text-capitalize font-bold">{{$op->section == 'callcenter' ? 'Call Center' : $op->section}}</span>
                                                    </p>
                                                    <p>
                                                        Status
                                                        @if($op->section == 'juragan')
                                                            @switch($op->status_progress)
                                                                @case("1")
                                                                <em class="font-bold text-success">Approve</em>
                                                                @break

                                                                @case("4")
                                                                <em class="font-bold text-danger">Cancel</em>
                                                                @break
                                                            @endswitch
                                                        @endif

                                                        @if($op->section == 'partner')
                                                            @switch($op->status_progress)
                                                                @case("1")
                                                                <em class="font-bold text-success">Approve</em>
                                                                @break

                                                                @case("4")
                                                                <em class="font-bold text-danger">Cancel</em>
                                                                @break
                                                            @endswitch
                                                        @endif

                                                        @if($op->section == 'uli')
                                                            @switch($op->status_progress)
                                                                @case("1")
                                                                <em class="font-bold text-success">Approve</em>
                                                                @break

                                                                @case("2")
                                                                <em class="font-bold text-danger">Reject</em>
                                                                @break
                                                            @endswitch
                                                        @endif

                                                        @if($op->section == 'callcenter')
                                                            @switch($op->status_progress)
                                                                @case("1")
                                                                <em class="font-bold text-success">Approve</em>
                                                                @break

                                                                @case("3")
                                                                <em class="font-bold text-primary">Tunda</em>
                                                                @break

                                                                @case("4")
                                                                <em class="font-bold text-danger">Reject</em>
                                                                @break
                                                            @endswitch
                                                        @endif

                                                        @if($op->section == 'driver')
                                                            @switch($op->status_progress)
                                                                @case("3")
                                                                <em class="font-bold text-info">Tunda</em>
                                                                @break

                                                                @case("4")
                                                                <em class="font-bold text-danger">Batal</em>
                                                                @break

                                                                @case("6")
                                                                <em class="font-bold text-success">Terkirim</em>
                                                                @break

                                                                @case("7")
                                                                <em class="font-bold text-success">Sedang Ditarik</em>
                                                                @break
                                                            @endswitch
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

                <div class="col-md-8 col-lg-12">
                    <div class="profile-detail card-box">
                        <div>
                            <h4 class="text-uppercase font-600">Image</h4>

                            <div class="text-left">
                                <div class="container-fluid">
                                    <div id="image-row" class="row"></div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="col-md-8 col-lg-12">
                    <div class="profile-detail card-box">
                        <div>
                            <h4 class="text-uppercase font-600">Map</h4>

                            <div id="map" class="gmaps"></div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <a class="btn btn-danger m-t-20 pull-right" href="{{ route('warehouse.route-plan-pull-cabinet.index') }}"><i
                                class="fa fa-times"></i> Close</a>
                </div>
            </div>

        </div> <!-- container -->

    </div> <!-- content -->

@endsection

@include('layouts.alert')

@push('scripts')
    <script type="text/javascript">
        $(document).ready(function () {
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
    <script>
        // Initialize and add the map
        function initMap() {
            let myLatLng = {lat: {{$data->outlet->latitude}}, lng: {{$data->outlet->longitude}}};
            console.log(myLatLng);
            let map = new google.maps.Map(
                document.getElementById('map'), {zoom: 16, center: myLatLng});
            let marker = new google.maps.Marker({
                position: myLatLng,
                map: map,
                title: '{{ $data->outlet->name }}',
                label: '{{ $data->outlet->name }}',
            });
        }
    </script>
    <script async defer
            src="https://maps.googleapis.com/maps/api/js?key=&callback=initMap">
    </script>
@endpush