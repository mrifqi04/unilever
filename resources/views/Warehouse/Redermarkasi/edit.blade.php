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
                                Edit
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
                        {{ Form::open(array('url' => route('redermarkasi.update'), 'role' => 'form', 'id' => 'form-update')) }}
                        {{ Form::hidden('id_submitted_outlets', $redermarkasi->id_submitted_outlets) }}
                        <div class="form-group">
                            {{ Form::label('juraganAsal', 'Juragan Asal', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                <select class="form-control" id="id_juragan_asal" name="id_juragan_asal" style="width: 100%">
                                <option value="{{$juraganAsal->id}}">{{$juraganAsal->name}}</option>
                                @foreach($juragans as $item)
                                    <option value="{{$item->id}}">{{$item->name}}</option>
                                @endforeach
                                </select>
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Pilih salah satu, tidak boleh dikosongkan.
                                </small>
                                <input type="hidden" name="id_redermarkasi" value="{{$redermarkasi->id}}">
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-10">
                                <table class="table table-hover" id="data-table">
                                    <thead>
                                        <tr>
                                            <th width="30"><input type="checkbox" id="check-all"></th>
                                            <th>ID</th>
                                            <th>Nama Outlet</th>
                                            <th>Alamat</th>
                                        </tr>
                                    </thead>
                                    <tbody id="listoutlet">
                                        
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('juraganTujuan', 'Juragan Tujuan', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                <select class="form-control" id="id_juragan_tujuan" name="id_juragan_tujuan" style="width: 100%">
                                @foreach($juragans as $item)
                                    <option value="{{$item->id}}" {{ ($redermarkasi->id_juragan_tujuan == $item->id)? "selected":"" }}>{{$item->name}}</option>
                                @endforeach
                                </select>
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Pilih salah satu, tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-10">
                                <button type="submit" onclick="confirm('Are you sure?')" class="btn btn-success m-t-20 pull-right"><i
                                            class="fa fa-check"></i> Save
                                </button>
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
    <script>
        var data_table = $('#data-table').DataTable();
        var id_submitted_outlets = "{{$redermarkasi->id_submitted_outlets}}";
    </script>
    <script type="text/javascript">
        $("#id_juragan_asal").select2({
                placeholder: "Pilih Juragan Asal",
                allowClear: true
            });
    </script>
    <script type="text/javascript">
        $("#id_juragan_tujuan").select2({
                placeholder: "Pilih Juragan Tujuan",
                allowClear: true
            });
    </script>

    <script>
        $(function () {
            $("#id_juragan_asal").change(function () {
                data_table.destroy();
                data_table = $('#data-table').DataTable({
                    "lengthMenu": [10 , 25, 50, 100],
                    "columnDefs": [
                        { "orderable": false, "targets": 0 }
                    ],
                    "processing" : true,
                    "serverSide" : true,
                    "ajax" : {
                        "url": "{{url('/redermarkasi-edit-data-kabinet/get-outlet-edit')."/"}}" + $("#id_juragan_asal").val(),
                        "type": 'GET',
                    },
                    "createdRow": function ( row, data, index ) {
                        check_selected();
                    }
                });
            });
            $("#id_juragan_asal").change();
        });

        var selected_data = <?php echo ($redermarkasi->id_submitted_outlets) ? json_encode(explode(',', $redermarkasi->id_submitted_outlets)) : '[]' ?>;
        $( document ).on('change', '#check-all', function () {
            $('#listoutlet input:checkbox').not(this).prop('checked', this.checked);
            update_selected();
        });

        $( document ).on('click', '.id_outlet', function () {
            update_selected();
        });

        function update_selected() {
            $('.id_outlet').each(function() {
                if ( $(this).prop('checked') == true ) {
                    if (selected_data.indexOf( $( this ).val() ) < 0) {
                        selected_data.push( $( this ).val() ); 
                    }
                }
                else {
                    if ((index = selected_data.indexOf( $( this ).val() )) > -1) {
                        selected_data.splice(index, 1);
                    }
                }
            });

            $('#check-all').prop('checked', false);

            if ( $('.id_outlet').is(':visible') ) {
                if ( $('.id_outlet:not(:checked)').length == 0 ) {
                    $('#check-all').prop('checked', true);
                }
            }
        }
        
        function check_selected() {
            if ( selected_data.length > 0 ) {
                $.each(selected_data, function(i, id) {
                    setTimeout(
                        function() {
                            $('.id_outlet[value='+id+']').attr('checked', 'checked');
                    }, 500);
                } );
            }
    
            setTimeout(
                function() {
                    $('#check-all').prop('checked', false);
                    if ( $('.id_outlet').is(':visible') ) {
                        if ( $('.id_outlet:not(:checked)').length == 0 ) {
                            $('#check-all').prop('checked', true);
                        }
                    }
            }, 500);
        }

        $(document).on('click', '.page-link', function(){
            check_selected();
        });
        $('#form-update').submit(function(){
            $("[name=id_submitted_outlets]").val(selected_data);
        });
    </script>
@endpush