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
                            Edit
                        </li>
                    </ol>
                    <h4 class="m-t-0 header-title">
                        <b>Data Warehouse Management</b>
                    </h4>
                    {{ Html::ul($errors->all()) }}
                    {{ Form::open(array('url' => route('warehouse_management.update'), 'role' => 'form', 'id' => 'form-create')) }}
                    <div class="form-group">
                        {{ Form::label('warehouseName', 'Warehouse Name', array('class' => 'col-2 col-form-label')) }}
                        <div class="col-10">
                            <input type="text" name="warehouse_name" class="form-control" value="{{$warehouseManagement->warehouse_name}}" required>
                            <input type="hidden" name="id_warehouse_management" value="{{$warehouseManagement->id}}">
                            <small class="form-text text-muted">
                                <span class="text-danger">*</span> Tidak boleh dikosongkan.
                            </small>
                            <input type="hidden" name="id_warehouse_admins" value="{{$warehouseManagement->id_warehouse_admins}}">
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
                        {{ Form::label('listadmin', 'Data Admin Warehouse', array('class' => 'col-2 col-form-label')) }}
                        <div class="col-10">
                            <table class="table table-hover" id="data-table">
                                <thead>
                                    <tr>
                                        <th width="30"><input type="checkbox" id="check-all" name="checked_all" value="1"></th>
                                        <th>Username</th>
                                        <th>Name</th>
                                        <th>Phone</th>
                                        <th>Role</th>
                                        <th>Created</th>
                                    </tr>
                                </thead>
                                <tbody id="listadmin">

                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-10">
                            <button type="button" id="btn-confirm" class="btn btn-sm btn-success m-t-20 pull-right" data-toggle="modal" data-target="#create">Update</button>
                            <div class="modal fade" id="create" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-sm">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            <h4 class="modal-title" id="myModalLabel">Update Confirmation</h4>
                                        </div>
                                        <div class="modal-body">
                                            Confirmation : Are you sure to update this data?
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn waves-effect waves-light btn-rounded btn-sm btn-danger" data-dismiss="modal">Close</button>
                                            {!! Form::button('Submit', array(
                                            'type' => 'submit',
                                            'class' => 'btn waves-effect waves-light btn-rounded btn-sm btn-success',
                                            'title' => 'Confirm to Update data'
                                            )) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <a class="btn btn-danger m-t-20" href="{{ route('warehouse_management.index') }}"><i class="fa fa-times"></i> Cancel</a>
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
<!-- <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script> -->
<script src="{{asset('assets/plugins/multiselect/js/jquery.multi-select.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
<script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap4.min.js"></script>
<script>
    var id_warehouse_admins = "{{$warehouseManagement->id_warehouse_admins}}";
</script>
<!-- //NOTE: Start Datatable -->
<script>
    var selected_data = <?php echo ($warehouseManagement->id_warehouse_admins) ? json_encode(explode(',', $warehouseManagement->id_warehouse_admins)) : '[]' ?>;
    console.log(selected_data);
    $(document).ready(function() {
        console.log(selected_data);
        var data_table = $('#data-table').DataTable({
            "lengthMenu": [10, 25, 50, 100],
            // "bSort": false,
            "columnDefs": [{
                "orderable": false,
                "targets": 0
            }],
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "{{url('/warehouse-management/get-admin-create?id='.$warehouseManagement->id)}}",
                "type": 'GET',
            },
            dataType: 'json',
            "createdRow": function(row, data, index) {
                check_selected_juragan();
                console.log(data);
            },
            success: function(data) {
                let check = $('#check-all');
                console.log(check);
                console.log(data);
            }
        });

    });

    $(document).on('change', '#check-all', function() {
        $('#listadmin input:checkbox').not(this).prop('checked', this.checked);
        update_selected_juragan();
    });

    $(document).on('click', '.id_admin', function() {
        update_selected_juragan();
    });

    function update_selected_juragan() {
        $('.id_admin').each(function(index) {
            if ($(this).prop('checked') == true) {
                if (selected_data.indexOf($(this).val()) < 0) {
                    selected_data.push($(this).val());
                }
            } else {
                if ((index = selected_data.indexOf($(this).val())) > -1) {
                    selected_data.splice(index, 1);
                }
            }
        });

        $('#check-all').prop('checked', false);

        if ($('.id_admin').is(':visible')) {
            if ($('.id_admin:not(:checked)').length == 0) {
                $('#check-all').prop('checked', true);
            }
        }
    }

    function check_selected_juragan() {
        if (selected_data.length > 0) {
            $.each(selected_data, function(i, id) {
                setTimeout(
                    function() {
                        $('.id_admin[value=' + id + ']').attr('checked', 'checked');
                    }, 500);
            });
        }

        setTimeout(
            function() {
                $('#check-all').prop('checked', false);
                if ($('.id_admin').is(':visible')) {
                    if ($('.id_admin:not(:checked)').length == 0) {
                        $('#check-all').prop('checked', true);
                    }
                }
            }, 500);
    }

    $(document).on('click', '.page-link', function() {
        check_selected_juragan();
    });

    $(document).on('click', '#btn-confirm', function() {
        $("[name=id_warehouse_admins]").val(selected_data);
    });
</script>
<!-- //NOTE: End Datatable -->
@endpush