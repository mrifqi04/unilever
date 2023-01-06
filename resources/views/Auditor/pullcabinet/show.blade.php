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
                                Show
                            </li>
                        </ol>
                        <h4 class="m-t-0 header-title">
                            <b>Data Auditor</b>
                        </h4>
                        {{ Html::ul($errors->all()) }}
                        <div class="form-group">
                            {{ Form::label('id', 'Auditor ID', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                {{ $auditor->id }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('unilever_id', 'Unilever ID', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                {{ $auditor->id_unilever }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('name', 'Name', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                {{ $auditor->name }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('email', 'Email', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                {{ $auditor->email }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('phone', 'Phone', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                {{ $auditor->phone }}
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
                            <div class="col-2"></div>
                            <div class="col-10">
                                <a class="btn btn-danger m-t-20" href="{{ route('auditor.pull-cabinet.index') }}">
                                    <i class="fa fa-times"></i>Back
                                </a>
                            </div>
                        </div>
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
                // "bSort": false,
                "columnDefs": [
                    { "orderable": false, "targets": 0 }
                ],
                "processing" : true,
                "serverSide" : true,
                "ajax" : {
                    "url" : "{{url('/auditor/pull-cabinet/get-juragan/?id='.$auditor->id)}}&is_show=1",
                    "type": 'GET',
                },
                "createdRow": function ( row, data, index ) {
                    check_selected_juragan();
                }
            });
        });
        
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
    </script>
@endpush