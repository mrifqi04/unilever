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
                            <li>
                                <a href="{{route('warehouse_management.index')}}">Warehouse Management</a>
                            </li>
                            <li class="active">
                                Show
                            </li>
                        </ol>
                        <h4 class="m-t-0 header-title">
                            <b>Data Warehouse Management</b>
                        </h4>
                        {{ Html::ul($errors->all()) }}
                        {{ Form::open(array('url' => route('redermarkasi.store'), 'role' => 'form')) }}
                        <div class="form-group">
                            {{ Form::label('warehouseName', 'Warehouse Name', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                <input type="text" name="warehouse_name" class="form-control" value="{{$warehouseManagement->warehouse_name}}" required>
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('warehouseDescription', 'Warehouse Description', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                <textarea name="warehouse_description" class="form-control" required>{{$warehouseManagement->warehouse_description}}</textarea>
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('listAdmin', 'Data Admin Warehouse', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                <table class="table table-hover" id="data-table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Username</th>
                                            <th>Name</th>
                                            <th>Phone</th>
                                            <th>Role</th>
                                            <th>Created</th>
                                        </tr>
                                    </thead>
                                    <tbody id="listadmin">
                                        @foreach($current_admin as $i)
                                            @php
                                                $adminData = App\Models\UserManagement\User::find($i);
                                            @endphp
                                        <tr>
                                            <td><input type="checkbox" id="val{{$adminData->id}}" name="id_submitted_outlets[]" value="{{$adminData->id}}" checked onclick="return false;"/>&nbsp;</td>
                                            <td>{{$adminData->username}}</td>
                                            <td>{{$adminData->name}}</td>
                                            <td>{{$adminData->phone}}</td>
                                            <td>Admin Warehouse</td>
                                            <td>{{$adminData->created_at}}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-10">
                                <a class="btn btn-danger m-t-20" href="{{ route('warehouse_management.index') }}"><i
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