@extends('layouts.app')
@section('content')

    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card-box">
                        <ol class="breadcrumb">
                            <li>
                                Auditor
                            </li>
                            <li>
                                <a href="{{route('auditor.pull-cabinet.index')}}">Auditor Tarik Kabinet</a>
                            </li>
                            <li class="active">
                                Edit
                            </li>
                        </ol>
                        <h4 class="m-t-0 header-title">
                            <b>Data Auditor</b>
                        </h4>
                        {{ Html::ul($errors->all()) }}
                        {{ Form::model($auditor, array('route' => array('auditor.pull-cabinet.update', $auditor->id), 'method' => 'PUT', 'role'=>'form')) }}
                        {{ Form::hidden('id', $auditor->id) }}
                        {{ Form::hidden('id_juragan_mappings', $auditor->id_juragan_mappings) }}
                        <div class="form-group">
                            {{ Form::label('id', 'Auditor ID', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                {{ $auditor->id }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('id_unilever', 'Unilever ID', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::text('id_unilever', old('id_unilever', $auditor->id_unilever), array('class' => 'form-control', 'placeholder'=>'Unilever ID', 'required'=>'required', 'readonly'=>'readonly')) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('name', 'Name', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::text('name', old('name', $auditor->name), array('class' => 'form-control', 'placeholder'=>'Name', 'required'=>'required', 'readonly'=>'readonly')) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('email', 'Email', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::email('email', old('email', $auditor->email), array('class' => 'form-control', 'placeholder'=>'Email', 'required'=>'required', 'readonly'=>'readonly')) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>


                        <div class="form-group">
                            {{ Form::label('phone', 'Phone', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                {{ Form::text('phone', old('phone', $auditor->phone), array('class' => 'form-control', 'placeholder'=>'Phone', 'required'=>'required', 'readonly'=>'readonly')) }}
                                <small class="form-text text-muted">
                                    <span class="text-danger">*</span> Tidak boleh dikosongkan.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('listJuragan', 'Data Juragan', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10">
                                <table class="table table-hover" id="data-table">
                                    <thead>
                                        <tr>
                                            <th width="30"><input type="checkbox" id="check-all" name="checked_all" value="1"></th>
                                            <th>SERU ID</th>
                                            <th>UNILEVER ID</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Created</th>
                                        </tr>
                                    </thead>
                                    <tbody id="listJuragan">
                                        
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-10">
                                <button type="button" id="btn-save" class="btn btn-success m-t-20 pull-right"><i
                                            class="fa fa-check"></i> Save
                                </button>
                                <a class="btn btn-danger m-t-20" href="{{ route('auditor.pull-cabinet.index') }}"><i
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
	<link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap4.min.css">
@endpush

@push('scripts')
    <script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap4.min.js"></script>
    <script>
        var selected_data = <?php echo ($auditor->id_juragan_mappings) ? json_encode(explode(',', $auditor->id_juragan_mappings)) : '[]' ?>;
        $(document).ready(function() {
            var data_table = $('#data-table').DataTable({
                "lengthMenu": [10 , 25, 50, 100],
                "columnDefs": [
                    { "orderable": false, "targets": 0 }
                ],
                "processing" : true,
                "serverSide" : true,
                "ajax" : {
                    "url" : "{{url('/auditor/pull-cabinet/get-juragan/?id='.$auditor->id)}}",
                    "type": 'GET',
                },
                "createdRow": function ( row, data, index ) {
                    check_selected_juragan();
                }
            });
        });

        $( document ).on('change', '#check-all', function () {
            $('#listJuragan input:checkbox').not(this).prop('checked', this.checked);
            update_selected_juragan();
        });

        $( document ).on('click', '.id_juragan', function () {
            update_selected_juragan();
        });

        function update_selected_juragan() {
            $('.id_juragan').each(function() {
                if ( $(this).prop('checked') == true ) {
                    if (selected_data.indexOf( $( this ).val() ) < 0) {
                        selected_data.push( $( this ).val() ); 
                    }
                }
                else {
                    if (selected_data.indexOf( $( this ).val() ) > -1) {
                        selected_data.splice(index, 1);
                    }
                }
            });

            $('#check-all').prop('checked', false);

            if ( $('.id_juragan').is(':visible') ) {
                if ( $('.id_juragan:not(:checked)').length == 0 ) {
                    $('#check-all').prop('checked', true);
                }
            }
        }
        
        function check_selected_juragan() {
            if ( selected_data.length > 0 ) {
                $.each(selected_data, function(i, id) {
                    setTimeout(
                        function() {
                            $('.id_juragan[value='+id+']').attr('checked', 'checked');
                    }, 500);
                } );
            }
    
            setTimeout(
                function() {
                    $('#check-all').prop('checked', false);
                    if ( $('.id_juragan').is(':visible') ) {
                        if ( $('.id_juragan:not(:checked)').length == 0 ) {
                            $('#check-all').prop('checked', true);
                        }
                    }
            }, 500);
        }

        $(document).on('click', '.page-link', function(){
            check_selected_juragan();
        });
        $(document).on('click', '#btn-save', function(){
            $("[name=id_juragan_mappings]").val(selected_data);
            $(this).closest('form').submit();
        });
    </script>
@endpush