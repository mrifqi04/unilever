@extends('layouts.app')
@section('content')

<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="card-box">
                    <ol class="breadcrumb">
                        <li>
                            Vehicle to Warehouse Management
                        </li>
                        <li>
                            <a href="{{route('vehicle_to_warehouse.index')}}">Vehicle to Warehouse Management</a>
                        </li>
                        <li class="active">
                            Edit
                        </li>
                    </ol>
                    <h4 class="m-t-0 header-title">
                        <b>Data Vehicle to Warehouse Management</b>
                    </h4>
                    {{ Html::ul($errors->all()) }}
                    {{ Form::open(array('url' => route('vehicle_to_warehouse.update'), 'role' => 'form', 'id' => 'form-create')) }}
                    @php
                    $warehouse = App\Models\Warehouse\WarehouseManagement::where('id', $vehicleToWarehouse->id_warehouse_management)->first();
                    @endphp
                    {{ Form::hidden('id_vehicle_mappings', $vehicleToWarehouse->id_vehicle_mappings) }}
                    <div class="form-group">
                        {{ Form::label('warehouseName', 'Juragan Asal', array('class' => 'col-2 col-form-label')) }}
                        <div class="col-10">
                            <select class="form-control" id="id_warehouse_management" name="id_warehouse_management" style="width: 100%">
                                <option value="{{$warehouse->id}}">{{$warehouse->warehouse_name}}</option>
                                @foreach($warehouses as $item)
                                <option value="{{$item->id}}">{{$item->warehouse_name}}</option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">
                                <span class="text-danger">*</span> Pilih salah satu, tidak boleh dikosongkan.
                            </small>
                            <input type="hidden" name="id_vehicle_to_warehouse" value="{{$vehicleToWarehouse->id}}">
                        </div>
                    </div>

                    <div class="form-group">
                        {{ Form::label('warehouseDescription', 'Warehouse Description', array('class' => 'col-2 col-form-label')) }}
                        <div class="col-10">
                            <textarea id="warehouse_description" class="form-control" readonly>{{$warehouse->warehouse_description}}</textarea>
                            <small class="form-text text-muted">
                                <span class="text-danger">*</span> Tidak boleh dikosongkan.
                            </small>
                        </div>
                    </div>

                    <div class="form-group">
                        {{ Form::label('listvehicle', 'Data Juragan Warehouse', array('class' => 'col-2 col-form-label')) }}
                        <div class="col-10">
                            <table class="table table-hover" id="data-table">
                                <thead>
                                    <tr>
                                        <th width="30"><input type="checkbox" id="check-all" name="checked_all" value="1"></th>
                                        <th>Plate Number</th>
                                    </tr>
                                </thead>
                                <tbody id="listvehicle">

                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-10">
                            <button type="button" id="btn-confirm" class="btn btn-success m-t-20 pull-right" data-toggle="modal" data-target="#create"><i class="fa fa-save"></i> Update</button>
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
                            <a class="btn btn-danger m-t-20" href="{{ route('vehicle_to_warehouse.index') }}"><i class="fa fa-times"></i> Cancel</a>
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
    // var data_table = $('#data-table');
    // data_table.DataTable();
    var id_vehicle_mappings = "{{$vehicleToWarehouse->id_vehicle_mappings}}";
</script>
{{-- <script>
        $('#draft').click(function () {
            var form = $('#form-create').attr('action' , "{{ route('redermarkasi.draft') }}")
$(this).attr('type' , 'submit')
$(this).click();
})
</script> --}}
<script>
    $(function() {
        // $(document).ready(function() {
        //     $.ajax({
        //         type: 'GET',
        //         url: "{{url('/vehicle-to-warehouse/')}}/get-warehouse-name/" + $(this).val(),
        //         // url: "{{url('/vehicle-to-warehouse/get-vehicle')}}",
        //         dataType: 'json',
        //         success: function(data) {
        //             $('#check-all').prop('checked', false);
        //             data_table.DataTable().destroy();
        //             $('#listvehicle').html(data);
        //             loop_vehicle_selected = id_vehicle_mappings.split(",");
        //             if (loop_vehicle_selected.length > 0) {
        //                 $.each(loop_vehicle_selected, function(index, value) {
        //                     $('[name="id_vehicle_mappings[]"][value="' + value + '"]').prop('checked', true);
        //                 });
        //             }
        //             data_table.DataTable({
        //                 "paging": false,
        //             });
        //             data_table.DataTable().on('draw', function() {
        //                 if ($('#check-all:checked').val()) {
        //                     $('#listvehicle input:checkbox').not(this).prop('checked', true);
        //                 }
        //             });
        //         }
        //     });
        // });
        // $("#check-all").change(function() {
        //     $('#listvehicle input:checkbox').not(this).prop('checked', this.checked);
        // });
        $("#id_juragan_asal").change();

    });
    // $(document).on('change', '#listvehicle input:checkbox', function() {
    //     if (!this.checked) {
    //         if ($('#check-all:checked').val()) {
    //             $('#check-all').prop('checked', false);
    //             $('#listvehicle input:checkbox').not(this).prop('checked', false);
    //             $(this).prop('checked', true);
    //         }
    //     }
    // });
</script>
<script>
    $("[name='id_warehouse_management']").change(function() {
        $.ajax({
            url: "{{url('/vehicle-to-warehouse/')}}/get-warehouse-name/" + $(this).val(),
            data: {
                tanggal_create: $("#created_at").val()
            },
            type: "GET",
            success: function(res) {
                $("[id='warehouse_description']").val(res.warehouse.warehouse_description)
            }
        })
    })
</script>
<!-- //NOTE: Start Datatable -->
<script>
    var selected_data = <?php echo ($vehicleToWarehouse->id_vehicle_mappings) ? json_encode(explode(',', $vehicleToWarehouse->id_vehicle_mappings)) : '[]' ?>;
    $(document).ready(function() {
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
                "url": "{{url('/vehicle-to-warehouse/get-vehicle')}}",
                "type": 'GET',
            },
            dataType: 'json',
            "createdRow": function(row, data, index) {
                check_selected_juragan();
            },
            success: function(data) {
                let check = $('#check-all');
                console.log(check);
                console.log(data);
            }
        });

    });

    $(document).on('change', '#check-all', function() {
        $('#listvehicle input:checkbox').not(this).prop('checked', this.checked);
        update_selected_juragan();
    });

    $(document).on('click', '.id_vehicle', function() {
        update_selected_juragan();
    });

    function update_selected_juragan() {
        $('.id_vehicle').each(function(index) {
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

        if ($('.id_vehicle').is(':visible')) {
            if ($('.id_vehicle:not(:checked)').length == 0) {
                $('#check-all').prop('checked', true);
            }
        }
    }

    function check_selected_juragan() {
        if (selected_data.length > 0) {
            $.each(selected_data, function(i, id) {
                setTimeout(
                    function() {
                        $('.id_vehicle[value=' + id + ']').attr('checked', 'checked');
                    }, 500);
            });
        }

        setTimeout(
            function() {
                $('#check-all').prop('checked', false);
                if ($('.id_vehicle').is(':visible')) {
                    if ($('.id_vehicle:not(:checked)').length == 0) {
                        $('#check-all').prop('checked', true);
                    }
                }
            }, 500);
    }

    $(document).on('click', '.page-link', function() {
        check_selected_juragan();
    });

    $(document).on('click', '#btn-confirm', function() {
        $("[name=id_vehicle_mappings]").val(selected_data);
    });
</script>
<!-- //NOTE: End Datatable -->
@endpush