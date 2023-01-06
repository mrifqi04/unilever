@extends('layouts.app')

@section('content')

    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card-box">
                        <ol class="breadcrumb">
                            <li>
                                Delivery
                            </li>
                            <li class="active">
                                <a href="{{route('delivery.index')}}">Delivery Management</a>
                            </li>
                        </ol>
                        <h4 class="m-t-0 header-title"><b>Data</b></h4>
						
						<button class="btn btn-primary  d-none d-lg-block" type="button" data-toggle="collapse"
                                data-target="#filter_data" aria-expanded="false" aria-controls="collapseExample">
                            <i class="fa fa-arrow-circle-down"></i> Filter
                        </button>

                        @if(Auth::user()->getPermissionByName('delivery.create'))
                            <a href="{{route('delivery.create')}}">
                                <button type="button" class="btn btn-info d-none d-lg-block">
                                    <i class="fa fa-plus-circle"></i> Create New
                                </button>
                            </a>
                        @endif

                        @if(Auth::user()->getPermissionByName('delivery.export-activity'))
                            <a href="#modal-export-survey"
                               data-animation="fadein" data-plugin="custommodal"
                               data-overlayspeed="200" data-overlaycolor="#36404a"
                               class="btn btn-info waves-effect waves-light">
                                Export Driver Activity
                            </a>
                            <!-- Modal -->
                            <div id="modal-export-survey" class="modal-demo">
                                <button type="button" class="close" onclick="Custombox.close();">
                                    <span>&times;</span><span class="sr-only">Close</span>
                                </button>
                                <h4 class="custom-modal-title">Export Driver Activity</h4>
                                <div class="modal-body">
                                    <div class="container-fluid">
                                        <form method="get" action="{{route('delivery.export-activity')}}" role="form"
                                              class="form-horizontal">
                                            <div class="form-group">
                                                <label for="fromDate" class="col-sm-2 control-label">Delivery Date</label>
                                                <div class="col-sm-5">
                                                    <input class="form-control" name="fromDate" id="fromDate"
                                                           value=""
                                                           placeholder="yyyy-mm-dd">
                                                </div>
                                                <div class="col-sm-5">
                                                    <input class="form-control" name="toDate" id="toDate"
                                                           value=""
                                                           placeholder="yyyy-mm-dd">
                                                </div>
                                            </div>
                                            <div class="form-group m-b-0">
                                                <div class="col-sm-12">
                                                    <button class="btn btn-info waves-effect waves-light pull-right">
                                                        <i class="fa fa-search"></i>&nbsp;Export
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif

						<div class="collapse" id="filter_data">
                            <div class="card-box">
                                <div class="card-view">
                                    {{ Form::open(array('url' => route('delivery.index'), 'role' => 'form', 'method' => 'get')) }}
                                    <div class="form-group">
                                        {{ Form::label('search', 'Search', array('class' => 'col-2 col-form-label')) }}
                                        <div class="col-10">
                                            {{ Form::text('search', old('search', app('request')->get('search')), array('class' => 'form-control', 'placeholder'=>'Name, Assigner, Assign To, Vehicle')) }}
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        {{ Form::label('province', 'Provinsi', array('class' => 'col-2 col-form-label')) }}
                                        <div class="col-10">
                                            {{ Form::select('province', $provinces, null, ['placeholder' => 'Pilih Province', 'class' => 'form-control', 'id' => 'province']) }}
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        {{ Form::label('cities', 'City', array('class' => 'col-2 col-form-label')) }}
                                        <div class="col-10">
                                            {{ Form::select('cities', [], null, ['placeholder' => 'Pilih City', 'class' => 'form-control']) }}
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        {{ Form::label('date', 'Date', array('class' => 'col-2 col-form-label')) }}
                                        <div class="col-10">
                                            <div class="input-daterange input-group" id="date-range">
                                                <input type="text" class="form-control" name="start" value="{{app('request')->input('start')}}"/>
                                                <span class="input-group-addon bg-custom b-0 text-white">to</span>
                                                <input type="text" class="form-control" name="end" value="{{app('request')->input('end')}}"/>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        {{ Form::label('journey_plan_type', 'Jenis Transaksi', array('class' => 'col-2 col-form-label')) }}
                                        <div class="col-10">
                                            {{ Form::select('journey_plan_type', ['Deploy' => 'Deploy', 'Tarik' => 'Tarik', 'Tukar' => 'Tukar'], null, ['placeholder' => 'Pilih Jenis Transaksi', 'class' => 'form-control']) }}
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-10">
                                            <button type="submit" class="btn btn-success"><i class="fa fa-check"></i>
                                                Search
                                            </button>
                                        </div>
                                    </div>
                                    {{ Form::close() }}
                                </div>
                            </div>
                        </div>

                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Province, City</th>
                                <th>Assigner</th>
                                <th>Assign To</th>
                                <th>Vehicle</th>
                                <th>Delivery Date</th>
                                <th colspan="3" class="text-center">Action</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach($datas as $data)
                            <tr>
                                <td>{{$data->name}}</td>
                                <td>
                                    {{$data->journeyRoute[0]->outlet->province->name.', '.$data->journeyRoute[0]->outlet->city->name}}                                    
                                    </td>
                                    <td>{{$data->user->name}}</td>
                                    <td>{{$data->driver->name}}</td>
                                    <td>{{$data->vehicle->license_number}}</td>
                                    <td>{{$data->created_at}}</td>
                                    <td class="text-center">
                                        {{ Form::open(array('url' => route('delivery.destroy', ['delivery'=>$data->id]))) }}

                                        @if(Auth::user()->getPermissionByName('delivery.show'))
                                            <a href="{{route('delivery.show', ['delivery'=>$data->id])}}"
                                               class="btn waves-effect waves-light btn-rounded btn-sm btn-success">
                                                Show
                                            </a>
                                        @endif

                                        {{-- Check the permission of user with condition plan status is not canceled and routeplan doesnt have journey route --}}
                                        @if((Auth::user()->getPermissionByName('journey_plan.cancel') && ($data->plan_status != 'canceled')) && ($data->journeyRoute->isNotEmpty()))
                                            <a href="{{url('delivery/cancel', ['route_plan'=>$data->id])}}"
                                               class="btn waves-effect waves-light btn-rounded btn-sm btn-danger">
                                                Cancel
                                            </a>
                                        @endif
                                        <br><br>

                                        @if(Auth::user()->getPermissionByName('delivery.exportPDF'))
                                        <a href="{{route('delivery.exportPDF')}}?id={{$data->id}}&type={{$data->journey_plan_type}}"
                                           class="btn waves-effect waves-light btn-rounded btn-sm btn-success">
                                            Export PDF
                                        </a>
                                        @endif
                                        
                                        @if(Auth::user()->getPermissionByName('delivery.edit') && $data->plan_status != 'canceled')
                                        <a href="{{route('delivery.edit', ['delivery'=>$data->id, 'type' => strtolower($data->journey_plan_type)])}}"
                                        class="btn waves-effect waves-light btn-rounded btn-xs btn-info m-t-5">
                                        Edit
                                        </a>
                                        @endif

                                        @if(Auth::user()->getPermissionByName('delivery.destroy'))
                                            {{ Form::hidden('_method', 'DELETE') }}
                                            <button class="btn waves-effect waves-light btn-rounded btn-xs btn-danger"
                                                    type="submit">Delete
                                            </button>
                                        @endif

                                        {{ Form::close() }}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        {{ $datas->links("pagination::bootstrap-4") }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@include('layouts.alert')

@push('styles')
    <link href="{{asset('assets/plugins/select2/css/select2.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('assets/plugins/bootstrap-daterangepicker/daterangepicker.css')}}" rel="stylesheet">
    <link href="{{asset('assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css')}}" rel="stylesheet">
@endpush

@push('scripts')
    <script src="{{asset('assets/plugins/moment/moment.js')}}"></script>
    <script src="{{asset('assets/plugins/select2/js/select2.min.js')}}"></script>
    <script src="{{asset('assets/plugins/bootstrap-daterangepicker/daterangepicker.js')}}"></script>
    <script src="{{asset('assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>
    <script>
        $('document').ready(function () {
            $('#date-range').datepicker({
                autoclose: true,
                todayHighlight: true,
                format: 'yyyy-mm-dd'
            });

            $("select#province").select2({
                placeholder: "Select Province",
                allowClear: true,
            });
            $("select#cities").select2({
                placeholder: "Select Cities",
            });

            $("select#province").on("change", function (event) {
                $("select#cities").select2({
                    placeholder: "Select Cities",
                    allowClear: true,
                    ajax: {
                        url: url = "{{route("geo.city")}}" + "?province_id=" + this.value,
                        dataType: 'json',
                        type: "GET",
                        data: function (term) {
                            return {
                                term: term
                            };
                        },
                        processResults: function (data) {
                            // Transforms the top-level key of the response object from 'items' to 'results'
                            return {
                                results: $.map(data, function (item) {
                                    return {
                                        text: item.text || item.name,
                                        id: item.id
                                    }
                                })
                            };
                        }
                    }
                });
            });
        });
    </script>
@endpush

@push('styles')
    <link href="{{asset('assets/plugins/custombox/css/custombox.css')}}" rel="stylesheet">
@endpush

@push('scripts')
    <!-- Modal-Effect -->
    <script src="{{asset('assets/plugins/custombox/js/custombox.min.js')}}"></script>
    <script src="{{asset('assets/plugins/custombox/js/legacy.min.js')}}"></script>
@endpush

@push('styles')
    <link href="{{asset('assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css')}}" rel="stylesheet"
          type="text/css">
@endpush

@push('scripts')
    <script src="{{asset('assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $("#fromDate,#toDate").datepicker({"format": "yyyy-mm-dd"});
            $("#fromDate,#toDate").datepicker('update', new Date());
        })
    </script>
@endpush