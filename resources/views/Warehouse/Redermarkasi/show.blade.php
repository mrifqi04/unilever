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
                            <li>
                                <a href="{{route('redermarkasi.index')}}">Redermarkasi Management</a>
                            </li>
                            <li class="active">
                                Show
                            </li>
                        </ol>
                        <h4 class="m-t-0 header-title">
                            <b>Data Redermarkasi</b>
                        </h4>
                        @php
                            $juraganAsal = App\Models\JuraganManagement\Juragan::where('id', $redermarkasi->id_juragan_asal)->first();
                            $juraganTujuan= App\Models\JuraganManagement\Juragan::where('id', $redermarkasi->id_juragan_tujuan)->first();
                        @endphp
                        {{ Html::ul($errors->all()) }}
                        {{ Form::open(array('url' => route('redermarkasi.store'), 'role' => 'form')) }}
                        <div class="form-group">
                            {{ Form::label('juraganAsal', 'Juragan Asal', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                <select class="form-control" id="id_juragan_asal" name="id_juragan_asal" style="width: 100%" required>
                                <option value="{{$juraganAsal->id}}">{{$juraganAsal->name}}</option>
                                </select>
                                <input type="hidden" name="id_redermarkasi" value="{{$redermarkasi->id}}">
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Pilih salah satu, tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-10">
                                <table class="table table-hover" id="data-table">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>ID</th>
                                            <th>Nama Outlet</th>
                                            <th>Alamat</th>
                                        </tr>
                                    </thead>
                                    <tbody id="listoutlet">
                                        @foreach($current_outlet as $i)
                                            @php
                                                $outletData = App\Models\OutletManagement\Outlet::find($i);
                                            @endphp
                                        <tr>
                                            <td><input type="checkbox" id="val{{$outletData->id}}" name="id_submitted_outlets[]" value="{{$outletData->id}}" checked onclick="return false;"/>&nbsp;</td>
                                            <td>{{$outletData->id}}</td>
                                            <td>{{$outletData->name}}</td>
                                            <td>{{$outletData->address}}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('juraganTujuan', 'Juragan Tujuan', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                <select class="form-control" id="id_juragan_tujuan" name="id_juragan_tujuan" style="width: 100%" required>
                                <option value="{{$juraganTujuan->id}}">{{$juraganTujuan->name}}</option>
                                </select>
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Pilih salah satu, tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-10">
                                @if($redermarkasi->latest_status == 'Draft')
                                <button type="button" class="btn waves-effect waves-light btn-rounded btn-sm btn-success m-t-20 pull-right" data-toggle="modal" data-target="#create">Submit for Approval</button>
                                <div class="modal fade" id="create" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-sm">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                <h4 class="modal-title" id="myModalLabel">Send to Approval Confirmation</h4>
                                            </div>
                                            <div class="modal-body">
                                               Confirmation : Are you sure to submit this to approver? These request cannot be edited. Please make sure your submission is correct!
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn waves-effect waves-light btn-rounded btn-sm btn-default" data-dismiss="modal">Close</button>
                                                {!! Form::button('Submit', array(
                                                        'type' => 'submit',
                                                        'class' => 'btn waves-effect waves-light btn-rounded btn-sm btn-success',
                                                        'title' => 'Confirm Send to Approval'
                                                )) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                <a class="btn btn-danger m-t-20" href="{{ route('redermarkasi.index') }}"><i
                                            class="fa fa-times"></i> Cancel</a>
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@include('layouts.alert')

@push('styles')
    <link href="{{asset('assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css')}}" rel="stylesheet">
    <link href="{{asset('assets/css/select2.min.css')}}" rel="stylesheet">
    <link href="{{asset('assets/plugins/multiselect/css/multi-select.css')}}" rel="stylesheet" type="text/css">
	<link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap4.min.css">
@endpush

@push('scripts')
    <script src="{{asset('assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
    <script src="{{asset('assets/plugins/multiselect/js/jquery.multi-select.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap4.min.js"></script>
@endpush