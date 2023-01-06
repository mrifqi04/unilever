@extends('layouts.app')

@section('content')

    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card-box">
                        <ol class="breadcrumb">
                            <li>
                                Warehouse Management
                            </li>
                            <li class="active">
                                <a href="{{route('warehouse_management.index')}}">Warehouse Management</a>
                            </li>
                        </ol>
                        <h4 class="m-t-0 header-title"><b>Data</b></h4>
						<div style="display : flex; justify-content : space-between; align-items : center">
                            <div>
                                <button class="btn btn-primary  d-none d-lg-block" type="button" data-toggle="collapse"
                                    data-target="#filter_data" aria-expanded="false" aria-controls="collapseExample">
                                <i class="fa fa-arrow-circle-down"></i> Filter
                                </button>

                                @if(Auth::user()->getPermissionByName('warehouse_management.create'))
                                    <a href="{{route('warehouse_management.create')}}">
                                        <button type="button" class="btn btn-info d-none d-lg-block">
                                            <i class="fa fa-plus-circle"></i> Create New
                                        </button>
                                    </a>
                                @endif
                                </div>
                        </div>

						<div class="collapse" id="filter_data">
                            <div class="card-box">
                                <div class="card-view">
                                    {{ Form::open(array('url' => route('warehouse_management.index'), 'role' => 'form', 'method' => 'get')) }}
                                    <div class="row">
                                            <div class="form-group">
                                                {{ Form::label('search', 'Search', array('class' => 'col-2 col-form-label')) }}
                                                <div class="col-10">
                                                    {{ Form::text('search', old('search', app('request')->get('search')), array('class' => 'form-control', 'placeholder'=>'Warehouse ID, Warehouse Name')) }}
                                                </div>
                                            </div>
                                        </div>
                                    <div class="row m-t-10">
                                        <div class="form-group col-md-12">
                                            <button class="btn btn-success"><i class="fa fa-check"></i>
                                                Search
                                            </button>
                                            <a href="{{route('warehouse_management.index')}}"
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
                                <th>Warehouse ID</th>
                                <th>Warehouse Name</th>
                                <th>Admin Total</th>
                                <th>Created At</th>
                                <th>Latest Updated At</th>
                                <th>Latest Updated By</th>
                                <th colspan="3" class="text-center">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach($datas as $i => $data)
                                @php
                                    $submitter = App\Models\UserManagement\User::where('id', $data->submitted_by)->first();
                                    $updater = App\Models\UserManagement\User::where('id', $data->updated_by)->first();
                                @endphp
                                <tr>
                                    <td>WM{{$data->id}}</td>
                                    <td>{{$data->warehouse_name}}</td>
                                    <td>{{$data->admin_total}}</td>
                                    <td>{{$data->created_at}}</td>
                                    <td>{{$data->updated_at}}</td>
                                    @if($data->updated_by == null)
                                    <td>Latest Updated by {{$submitter->name}}</td>
                                    @else
                                    <td>Latest Updated by {{$updater->name}}</td>
                                    @endif
                                    <td class="text-center">
                                        @if((Auth::user()->getPermissionByName('warehouse_management.edit')))
                                            <a href="{{route('warehouse_management.edit', $data->id)}}"
                                               class="btn waves-effect waves-light btn-rounded btn-sm btn-info">
                                                Edit
                                            </a>
                                        @endif
                                        @if(Auth::user()->getPermissionByName('warehouse_management.show'))
                                            <a href="{{route('warehouse_management.show', $data->id)}}"
                                               class="btn waves-effect waves-light btn-rounded btn-sm btn-default">
                                                Show
                                            </a>
                                        @endif
                                        <br>
                                        @if(Auth::user()->getPermissionByName('warehouse_management.delete'))
                                            <button type="button" class="btn waves-effect waves-light btn-rounded btn-sm btn-danger" data-toggle="modal" data-target="#deleteConfirm{{$data->id}}">Delete</button>
                                            <div class="modal fade" id="deleteConfirm{{$data->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                            <h4 class="modal-title" id="myModalLabel">Delete Confirmation</h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            Are you sure want to delete  this record?
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn waves-effect waves-light btn-rounded btn-sm btn-default" data-dismiss="modal">Close</button>
                                                            {!! Form::open([
                                                                'method' => 'GET',
                                                                'url' => ['/warehouse-management/delete', $data->id],
                                                                'style' => 'display:inline'
                                                            ]) !!}
                                                            {!! Form::button('Delete', array(
                                                                    'type' => 'submit',
                                                                    'class' => 'btn waves-effect waves-light btn-rounded btn-sm btn-danger',
                                                                    'title' => 'Confirm Delete'
                                                            )) !!}
                                                            {!! Form::close() !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        <br>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{-- {{ $datas->links("pagination::bootstrap-4") }} --}}
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

    <script type="text/javascript">
        $("#id_juragan_asal").select2({
                placeholder: "Cari berdasarkan Juragan Asal",
                allowClear: true
            });
    </script>
    <script type="text/javascript">
        $("#id_juragan_tujuan").select2({
                placeholder: "Cari berdasarkan Juragan Tujuan",
                allowClear: true
            });
    </script>
    {{-- <script>
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

            $("#fromDate,#toDate").datepicker({"format": "yyyy-mm-dd"});
            $("#fromDate,#toDate").datepicker('update', new Date());
        });
    </script> --}}
@endpush