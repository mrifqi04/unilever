@extends('layouts.app')

@section('content')
    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="portlet">
                        <div class="portlet-heading">
                            <h4 class="page-title">Auditor Dashboard</h4>
                            <hr>
                            <h3 class="portlet-title text-dark text-uppercase">
                                Auditor Daily Monitoring
                            </h3>
                            <div class="portlet-widgets">
                                <a data-toggle="collapse" data-parent="#accordion1" href="#portlet2"><i
                                            class="ion-minus-round"></i></a>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div id="portlet2" class="panel-collapse collapse in">
                            <div class="row" style="margin-bottom: 30px;">
                                <div class="col-md-6 col-sm-12">
                                    <form class="form-horizontal" action="{{route('dailymonitoring.index')}}"
                                          role="form">
                                        <div class="form-group">
                                            <label for="hunter_id" class="col-sm-3 control-label">ID Hunter</label>
                                            <div class="col-sm-9">
                                                <input class="form-control" name="hunter_id" id="hunter_id"
                                                       value="{{old('hunter_id', $hunter_id)}}"
                                                       placeholder="ID Hunter">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="hunter_name" class="col-sm-3 control-label">Nama
                                                Hunter</label>
                                            <div class="col-sm-9">
                                                <input class="form-control" name="hunter_name" id="hunter_name"
                                                       value="{{old('hunter_name', $hunter_name)}}"
                                                       placeholder="Nama Hunter">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="date" class="col-sm-3 control-label">Tanggal</label>
                                            <div class="col-sm-9">
                                                <input class="form-control" name="date" id="date"
                                                       value="{{old('date', $date)}}"
                                                       placeholder="yyyy-mm-dd">
                                            </div>
                                        </div>
                                        <div class="form-group m-b-0">
                                            <div class="col-sm-offset-3 col-sm-9">
                                                <button class="btn btn-info waves-effect waves-light">
                                                    <i class="fa fa-search"></i>&nbsp;Cari
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-md-6 col-sm-12">&nbsp;</div>
                            </div>
                            <div class="portlet-heading">
                                <h3 class="portlet-title text-dark text-uppercase">
                                    Detail Toko
                                </h3>
                            </div>
                            <div class="portlet-body">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th>Kota/Area</th>
                                        <th>Juragan</th>
                                        <th>Hunter</th>
                                        <th>Toko</th>
                                        <th>Check In</th>
                                        <th>Check Out</th>
                                        <th>Durasi (Menit)</th>
                                        <th>Status</th>
                                        <th>Koordinat Peta</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($outlets as $outlet)
                                        <tr>
                                            <td>{{$outlet->city_name}}</td>
                                            <td>{{$outlet->juragan_name}}</td>
                                            <td>{{$outlet->hunter_name}}</td>
                                            <td>{{$outlet->outlet_name}}</td>
                                            <td>{{strval($outlet->checkin) == '' ? '00:00:00' : \Carbon\Carbon::parse($outlet->checkin)->format('H:i:s')}}</td>
                                            <td>{{strval($outlet->checkout) == '' ? '00:00:00' : \Carbon\Carbon::parse($outlet->checkout)->format('H:i:s')}}</td>
                                            <td>{{$outlet->duration}}</td>
                                            <td>{{$outlet->status_name}}</td>
                                            <td>
                                                <a target="_blank"
                                                   href="https://maps.google.com/?q={{$outlet->latitude}},{{$outlet->longitude}}">
                                                    Klik Disini
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                {{ $outlets->links("pagination::bootstrap-4") }}
                                <div class="pull-right">
                                    <a href="{{route('dailymonitoring.export',['hunter_id' => $hunter_id, 'hunter_name' => $hunter_name, 'date' => $date])}}"
                                       class="btn btn-info waves-effect waves-light">
                                        Export Data
                                    </a>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link href="{{asset('assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css')}}" rel="stylesheet"
          type="text/css">
@endpush

@push('scripts')
    <script src="{{asset('assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $("#date").datepicker({"format": "yyyy-mm-dd"});
        })
    </script>
@endpush

@include('layouts.alert')