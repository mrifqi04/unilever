@extends('layouts.app')

@section('content')

    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card-box">
                        <ol class="breadcrumb">
                            <li>
                                Redermarkasi
                            </li>
                            <li class="active">
                                <a href="{{route('redermarkasi.index')}}">Redermarkasi Management</a>
                            </li>
                        </ol>
                        <h4 class="m-t-0 header-title"><b>Data</b></h4>
						<div style="display : flex; justify-content : space-between; align-items : center">
                            <div>
                                <button class="btn btn-primary  d-none d-lg-block" type="button" data-toggle="collapse"
                                    data-target="#filter_data" aria-expanded="false" aria-controls="collapseExample">
                                <i class="fa fa-arrow-circle-down"></i> Filter
                                </button>

                                @if(Auth::user()->getPermissionByName('redermarkasi.create'))
                                    <a href="{{route('redermarkasi.create')}}">
                                        <button type="button" class="btn btn-info d-none d-lg-block">
                                            <i class="fa fa-plus-circle"></i> Create New
                                        </button>
                                    </a>
                                @endif
                                </div>
                            <div>
                                {{ Form::open(array('url' => route('redermarkasi.index'), 'role' => 'form', 'method' => 'get')) }}
                                <input type="text" name="outlet_name" class="form-control"  placeholder="Nama Outlet" value="{{request('outlet_name')}}">
                                <button class="btn btn-success"><i class="fa fa-search"></i>
                                                Search
                                </button>
                                {{ Form::close() }}
                            </div>
                        </div>

						<div class="collapse" id="filter_data">
                            <div class="card-box">
                                <div class="card-view">
                                    {{ Form::open(array('url' => route('redermarkasi.index'), 'role' => 'form', 'method' => 'get')) }}
                                    <div class="form-group">
                                        <select class="form-control" id="id_juragan_asal" name="id_juragan_asal" style="width: 100%">
                                            <option></option>
                                            @foreach($juragans as $item)
                                                <option value="{{$item->id}}">{{$item->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <select class="form-control" id="id_juragan_tujuan" name="id_juragan_tujuan" style="width: 100%">
                                            <option></option>
                                            @foreach($juragans as $item)
                                                <option value="{{$item->id}}">{{$item->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="row m-t-10">
                                        <div class="form-group col-md-12">
                                            <button class="btn btn-success"><i class="fa fa-check"></i>
                                                Search
                                            </button>
                                            <a href="{{route('redermarkasi.index')}}"
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
                                <th>Route Redermarkasi ID</th>
                                <th>Juragan Asal</th>
                                <th>Juragan Tujuan</th>
                                <th>Total Outlet</th>
                                <th>Submitted By</th>
                                <th>Submitted At</th>
                                <th>Latest Status</th>
                                <th>Latest Status Date</th>
                                <th colspan="3" class="text-center">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach($datas as $i => $data)
                                @php
                                    $juraganAsal = App\Models\JuraganManagement\Juragan::where('id', $data->id_juragan_asal)->first();
                                    $juraganTujuan= App\Models\JuraganManagement\Juragan::where('id', $data->id_juragan_tujuan)->first();
                                    $submitter = App\Models\UserManagement\User::where('id', $data->submitted_by)->first();
                                @endphp
                                <tr>
                                    <td>RD{{$data->id}}</td>
                                    <td>{{$juraganAsal->name}}</td>
                                    <td>{{$juraganTujuan->name}}</td>
                                    <td>{{$data->outlet_total}}</td>
                                    <td>Submitted by {{$submitter->name}}</td>
                                    <td>{{$data->created_at}}</td>
                                    @if($data->latest_status == 'Rejected')
                                    <td>{{$data->latest_status}} : Reason
                                        {{$data->rejected_reason}}
                                    </td>
                                    @else
                                    <td>{{$data->latest_status}}</td>
                                    @endif
                                    <td>{{$data->updated_at}}</td>
                                    <td class="text-center">
                                        @if((Auth::user()->getPermissionByName('redermarkasi.edit')) && ($data->latest_status == 'Draft'))
                                            <a href="{{route('redermarkasi.edit', $data->id)}}"
                                               class="btn waves-effect waves-light btn-rounded btn-sm btn-info">
                                                Edit
                                            </a>
                                        @endif
                                        @if(Auth::user()->getPermissionByName('redermarkasi.show'))
                                            <a href="{{route('redermarkasi.show', $data->id)}}"
                                               class="btn waves-effect waves-light btn-rounded btn-sm btn-default">
                                                Show
                                            </a>
                                        @endif
                                        <br>
                                        @if((Auth::user()->getPermissionByName('redermarkasi.approve')) && (($data->latest_status == 'Submitted')))
                                            <button type="button" class="btn waves-effect waves-light btn-rounded btn-sm btn-success" data-toggle="modal" data-target="#approveConfirm{{$data->id}}">Approve</button>
                                            <div class="modal fade" id="approveConfirm{{$data->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                            <h4 class="modal-title" id="myModalLabel">Approve Confirmation</h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            Are you sure want to approve this redermarkasi?
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn waves-effect waves-light btn-rounded btn-sm btn-danger" data-dismiss="modal">Close</button>
                                                            {!! Form::open([
                                                                'method' => 'GET',
                                                                'url' => ['/redermarkasi-edit-data-kabinet/redermarkasi/approve', $data->id],
                                                                'style' => 'display:inline'
                                                            ]) !!}
                                                            {!! Form::button('Approve', array(
                                                                    'type' => 'submit',
                                                                    'class' => 'btn waves-effect waves-light btn-rounded btn-sm btn-success',
                                                                    'title' => 'Confirm Approve'
                                                            )) !!}
                                                            {!! Form::close() !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        <br>
                                        @if((Auth::user()->getPermissionByName('redermarkasi.reject')) && (($data->latest_status == 'Submitted')))
                                            <button data-toggle="modal" data-target="#reject-{{$data->id}}"
                                               class="btn waves-effect waves-light btn-rounded btn-sm btn-danger">
                                                Reject
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                                <div class="modal fade" id="reject-{{$data->id}}" role="document">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <!-- Modal body -->
                                            <div class="modal-body style-add-modal">
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                <h4 class="modal-title mb-3">Reject Redermarkasi</h4>
                                                <br><br>
                                                <form method="POST" action="{{route('redermarkasi.reject')}}" accept-charset="UTF-8" class="form-horizontal">
                                                    {{ csrf_field() }}
                                                    <div class="form-group">
                                                        <input type="hidden" name="id_redermarkasi" value="{{$data->id}}">
                                                        <label for="rejected_reason" class="control-label">Reject Reason</label>
                                                        <select name="rejected_reason" id="" class="form-control">
                                                            <option value="Incorrect Input">Incorrect Input</option>
                                                            <option value="Cancel Redermarkasi">Cancel Redermarkasi</option>
                                                            <option value="Rejected By Juragan">Rejected By Juragan</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <button class="btn btn-danger ctm-border-radius text-white float-right button-1" type="submit">Reject</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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