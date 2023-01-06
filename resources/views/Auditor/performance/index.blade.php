@extends('layouts.app')

@section('content')
    <div class="content">
        <div class="container">
            <form action="{{route('performance.index')}}" role="form">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="portlet">
                            <div class="portlet-heading">
                                <h4 class="page-title">Hunter Dashboard</h4>
                                <hr>
                                <h3 class="portlet-title text-dark text-uppercase">
                                    Summary Toko Hasil Survey
                                </h3>
                                <div class="portlet-widgets">
                                    <a data-toggle="collapse" data-parent="#accordion1" href="#portlet1"><i
                                                class="ion-minus-round"></i></a>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div id="portlet1" class="panel-collapse collapse in">
                                <div class="row" style="margin-bottom: 30px;">
                                    <div class="col-md-12 col-sm-12">
                                        <div class="form-horizontal">
                                            <div class="form-group">
                                                <label for="summary_from_date"
                                                       class="col-sm-1 control-label">Periode</label>
                                                <div class="col-sm-2">
                                                    <input class="form-control date" name="summary_from_date"
                                                           id="summary_from_date"
                                                           value="{{old('summary_from_date', $summary_from_date)}}"
                                                           placeholder="yyyy-mm-dd">
                                                </div>
                                                <div class="col-sm-2">
                                                    <input class="form-control date" name="summary_to_date"
                                                           id="summary_to_date"
                                                           value="{{old('summary_to_date', $summary_to_date)}}"
                                                           placeholder="yyyy-mm-dd">
                                                </div>
                                                <div class="col-sm-2">
                                                    <button class="btn btn-info waves-effect waves-light">
                                                        <i class="fa fa-search"></i>&nbsp;Cari
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="portlet-body">
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th>Kota/Area</th>
                                            <th>Hunter Aktif</th>
                                            <th>Toko Visit</th>
                                            <th>Toko Deal</th>
                                            <th>Toko Tunda</th>
                                            <th>Toko No Deal</th>
                                            <th>Conversion Rate</th>
                                            <th>EC Rate</th>
                                            <th>AVG Visit Per Hari</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($summaries as $summary)
                                            <tr>
                                                <td>{{$summary->city_name}}</td>
                                                <td>{{$summary->hunter_active}}</td>
                                                <td>{{$summary->outlet_visit}}</td>
                                                <td>{{$summary->outlet_deal}}</td>
                                                <td>{{$summary->outlet_tunda}}</td>
                                                <td>{{$summary->outlet_no_deal}}</td>
                                                <td>{{$summary->coversion_rate}}</td>
                                                <td>{{$summary->ec_rate}}</td>
                                                <td>{{$summary->avg_visit}}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                    {{ $summaries->links("pagination::bootstrap-4") }}
                                    <hr>
                                </div>
                            </div>
                            <div class="portlet-heading">
                                <h3 class="portlet-title text-dark text-uppercase">
                                    Performance By Hunter
                                </h3>
                                <div class="portlet-widgets">
                                    <a data-toggle="collapse" data-parent="#accordion2" href="#portlet2"><i
                                                class="ion-minus-round"></i></a>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div id="portlet2" class="panel-collapse collapse in">
                                <div class="row" style="margin-bottom: 30px;">
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-horizontal">
                                            <div class="form-group">
                                                <label for="performance_juragan_name" class="col-sm-3 control-label">Nama
                                                    Juragan</label>
                                                <div class="col-sm-9">
                                                    <input class="form-control" name="performance_juragan_name"
                                                           id="performance_juragan_name"
                                                           value="{{old('performance_juragan_name', $performance_juragan_name)}}"
                                                           placeholder="Nama Juragan">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="performance_hunter_id" class="col-sm-3 control-label">ID
                                                    Hunter</label>
                                                <div class="col-sm-9">
                                                    <input class="form-control" name="performance_hunter_id"
                                                           id="performance_hunter_id"
                                                           value="{{old('performance_hunter_id', $performance_hunter_id)}}"
                                                           placeholder="ID Hunter">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="performance_hunter_name" class="col-sm-3 control-label">Nama
                                                    Hunter</label>
                                                <div class="col-sm-9">
                                                    <input class="form-control" name="performance_hunter_name"
                                                           id="performance_hunter_name"
                                                           value="{{old('performance_hunter_name', $performance_hunter_name)}}"
                                                           placeholder="Nama Hunter">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="performance_from_date" class="col-sm-3 control-label">Periode</label>
                                                <div class="col-sm-4">
                                                    <input class="form-control date" name="performance_from_date"
                                                           id="performance_from_date"
                                                           value="{{old('performance_from_date', $performance_from_date)}}"
                                                           placeholder="yyyy-mm-dd">
                                                </div>
                                                <div class="col-sm-4">
                                                    <input class="form-control date" name="performance_to_date"
                                                           id="performance_to_date"
                                                           value="{{old('performance_to_date', $performance_to_date)}}"
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
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-12">&nbsp;</div>
                                </div>
                                <div class="portlet-body">
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th>Kota/Area</th>
                                            <th>Juragan</th>
                                            <th>Nama Hunter</th>
                                            <th>ID Hunter</th>
                                            <th>Status</th>
                                            <th>Last Check Out</th>
                                            <th>Toko Visit</th>
                                            <th>Toko Deal</th>
                                            <th>Toko Tunda</th>
                                            <th>Toko No Deal</th>
                                            <th>EC</th>
                                            <th>AVG Visit Per Hari</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($performances as $performance)
                                            <tr>
                                                <td>{{$performance->city_name}}</td>
                                                <td>{{$performance->juragan_name}}</td>
                                                <td>{{$performance->hunter_name}}</td>
                                                <td>{{$performance->hunter_id}}</td>
                                                <td>{{$performance->hunter_active}}</td>
                                                <td>
                                                    {{strval($performance->last_checkout) ? \Illuminate\Support\Carbon::parse($performance->last_checkout)->format('Y-m-d') : ''}}
                                                </td>
                                                <td>{{$performance->outlet_visit}}</td>
                                                <td>{{$performance->outlet_deal}}</td>
                                                <td>{{$performance->outlet_tunda}}</td>
                                                <td>{{$performance->outlet_no_deal}}</td>
                                                <td>{{$performance->ec_rate}}</td>
                                                <td>{{$performance->avg_visit}}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                    {{--                                {{ $performances->links("pagination::bootstrap-4") }}--}}
                                    <div class="pull-right">
                                        <a href="{{route('dailymonitoring.export',['performance_hunter_id' => $performance_hunter_id, 'performance_hunter_name' => $performance_hunter_name, 'date' => $summary_to_date])}}"
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
            </form>
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
            $(".date").datepicker({"format": "yyyy-mm-dd"});
        })
    </script>
@endpush

@include('layouts.alert')