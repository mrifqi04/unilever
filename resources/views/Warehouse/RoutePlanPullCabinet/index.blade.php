@extends('layouts.app')

@section('content')

    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card-box">
                        <ol class="breadcrumb">
                            <li>
                                Route Plans
                            </li>
                            <li class="active">
                                <a href="{{route('warehouse.route-plan-pull-cabinet.index')}}">Route Plans Management</a>
                            </li>
                        </ol>
                        <h4 class="m-t-0 header-title"><b>Data</b></h4>
						
						<button class="btn btn-primary  d-none d-lg-block" type="button" data-toggle="collapse"
                                data-target="#filter_data" aria-expanded="false" aria-controls="collapseExample">
                            <i class="fa fa-arrow-circle-down"></i> Filter
                        </button>

                        @if(Auth::user()->getPermissionByName('warehouse.route-plan-pull-cabinet.create'))
                            <a href="{{route('warehouse.route-plan-pull-cabinet.create')}}">
                                <button type="button" class="btn btn-info d-none d-lg-block">
                                    <i class="fa fa-plus-circle"></i> Create New
                                </button>
                            </a>
                        @endif

                        @if(Auth::user()->getPermissionByName('warehouse.route-plan-pull-cabinet.export'))
                            <a href="#modal-export-activity"
                               data-animation="fadein" data-plugin="custommodal"
                               data-overlayspeed="200" data-overlaycolor="#36404a"
                               class="btn btn-info waves-effect waves-light">
                                Download Data Activity
                            </a>
                            <!-- Modal -->
                            <div id="modal-export-activity" class="modal-demo">
                                <button type="button" class="close" onclick="Custombox.close();">
                                    <span>&times;</span><span class="sr-only">Close</span>
                                </button>
                                <h4 class="custom-modal-title">Download Data Activity</h4>
                                <div class="modal-body">
                                    <div class="container-fluid">
                                        <form method="get" action="{{route('warehouse.route-plan-pull-cabinet.export')}}" role="form"
                                              class="form-horizontal">
                                            <div class="form-group">
                                                <label for="fromDate" class="col-sm-2 control-label">Tanggal</label>
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
                                    {{ Form::open(array('url' => route('warehouse.route-plan-pull-cabinet.index'), 'role' => 'form', 'method' => 'get')) }}
                                    <div class="form-group">
                                        {{ Form::label('search', 'Search', array('class' => 'col-2 col-form-label')) }}
                                        <div class="col-10">
                                            {{ Form::text('search', old('adr', app('request')->get('search')), array('class' => 'form-control', 'placeholder'=>'ADR, Outlet Name, Outlet Phone, or Outlet Owner')) }}
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        {{ Form::label('id_outlet', 'Outlet', array('class' => 'col-2 col-form-label')) }}
                                        <div class="col-10">
                                            {{ Form::select('id_outlet', [], null, ['class' => 'form-control', 'placeholder'=>'Outlet']) }}
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

                                    <div class="row m-t-10">
                                        <div class="form-group col-md-12">
                                            <button class="btn btn-success"><i class="fa fa-check"></i>
                                                Search
                                            </button>
                                            <a href="{{route('warehouse.route-plan-pull-cabinet.index')}}"
                                               class="btn btn-white">Clear</a>
                                        </div>
                                    </div>
                                    {{ Form::close() }}
                                </div>
                            </div>
                        </div>

                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>ADR</th>
                                <th>Outlet Name</th>
                                <th>Outlet Addr.</th>
                                <th>Outlet Phone</th>
                                <th>Created At</th>
                                <th colspan="3" class="text-center">Action</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach($datas as $data)
                                <tr>
                                    <td>{{$data->deliveryOrders->adr}}</td>
                                    <td>{{$data->outlet->name}}</td>
                                    <td>
                                        {{$data->outlet->address}}<br/>
                                        {{$data->outlet->province->name.', '.
                                            $data->outlet->city->name.', '.
                                            $data->outlet->district->name.', '.
                                            $data->outlet->village->name}}
                                    </td>
                                    <td>{{$data->outlet->phone}}</td>
                                    <td>{{\Carbon\Carbon::createFromTimestamp($data->created_at)}}</td>
                                    <td class="text-center">
                                        @if(Auth::user()->getPermissionByName('warehouse.route-plan-pull-cabinet.show'))
                                            <a href="{{route('warehouse.route-plan-pull-cabinet.show', ['id'=>$data->id])}}"
                                               class="btn waves-effect waves-light btn-rounded btn-sm btn-success">
                                                Show
                                            </a>
                                        @endif

                                        {{ Form::open(array('url' => route('warehouse.route-plan-pull-cabinet.cancel', ['id'=>$data->id]))) }}
                                        
                                        {{-- Check the permission of user with condition plan status is not canceled and routeplan doesnt have journey route --}}
                                        @if((Auth::user()->getPermissionByName('warehouse.route-plan-pull-cabinet.cancel') && ($data->plan_status != 'canceled')) && ($data->journeyRoute->isEmpty()))
                                            {{ Form::hidden('_method', 'PUT') }}
                                            <button class="btn waves-effect waves-light btn-rounded btn-sm btn-danger m-t-5"
                                                    type="submit">Cancel
                                            </button>
                                        @endif
                                        
                                        {{ Form::close() }}

                                        {{ Form::open(array('url' => route('warehouse.route-plan-pull-cabinet.destroy', ['id'=>$data->id]))) }}

                                        {{-- Check the permission of user with condition plan status is not canceled and routeplan doesnt have journey route --}}
                                        @if((Auth::user()->getPermissionByName('warehouse.route-plan-pull-cabinet.destroy') && ($data->plan_status != 'canceled')) && ($data->journeyRoute->isEmpty()))
                                            {{ Form::hidden('_method', 'DELETE') }}
                                            <button class="btn waves-effect waves-light btn-rounded btn-xs btn-danger m-t-5"
                                                    type="submit">Delete
                                            </button>
                                        @endif

                                        @if((Auth::user()->getPermissionByName('warehouse.route-plan-pull-cabinet.edit') && ($data->plan_status != 'canceled')) && ($data->journeyRoute->isEmpty()))
                                            <a href="{{route('warehouse.route-plan-pull-cabinet.edit', ['id'=>$data->id])}}"
                                            class="btn waves-effect waves-light btn-rounded btn-xs btn-info m-t-5">
                                            Edit
                                            </a>
                                        @endif

                                        {{ Form::close() }}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        {{ $datas->appends(request()->input())->links("pagination::bootstrap-4") }}
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
    <link href="{{asset('assets/plugins/custombox/css/custombox.css')}}" rel="stylesheet">
@endpush

@push('scripts')
    <script src="{{asset('assets/plugins/moment/moment.js')}}"></script>
    <script src="{{asset('assets/plugins/select2/js/select2.min.js')}}"></script>
    <script src="{{asset('assets/plugins/bootstrap-daterangepicker/daterangepicker.js')}}"></script>
    <script src="{{asset('assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>
    <!-- Modal-Effect -->
    <script src="{{asset('assets/plugins/custombox/js/custombox.min.js')}}"></script>
    <script src="{{asset('assets/plugins/custombox/js/legacy.min.js')}}"></script>
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
                        url: url = "{{route('geo.city')}}" + "?province_id=" + this.value,
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

            $("#fromDate,#toDate").datepicker({"format": "yyyy-mm-dd"});
            $("#fromDate,#toDate").datepicker('update', new Date());
            
            $("select#id_outlet").select2({
                minimumInputLength: 3,
                placeholder: "Select Outlet",
                ajax: {
                    url: "{{route('geo.allOutlet')}}",
                    dataType: 'json',
                    type: "GET",
                    data: function (params) {
                        return {
                            search: params.term,
                        }
                    },
                    processResults: function (data) {
                        // Transforms the top-level key of the response object from 'items' to 'results'
                        return {
                            results: $.map(data, function (item) {
                                return {
                                    text: item.id + ' - ' + item.name,
                                    id: item.id
                                }
                            })
                        };
                    }
                }
            });
        });
    </script>
@endpush