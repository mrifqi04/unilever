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
                            <li class="active">
                                <a href="{{route('auditor.pull-cabinet.index')}}">Auditor Tarik Kabinet</a>
                            </li>
                        </ol>
                        <h4 class="m-t-0 header-title"><b>Data</b></h4>

                        <button class="btn btn-primary  d-none d-lg-block" type="button" data-toggle="collapse"
                                data-target="#filter_data" aria-expanded="false" aria-controls="collapseExample">
                            <i class="fa fa-arrow-circle-down"></i> Filter
                        </button>

                        <div class="collapse" id="filter_data">
                            <div class="card-box">
                                <div class="card-view">
                                    {{ Form::open(array('url' => route('auditor.pull-cabinet.index'), 'role' => 'form', 'method' => 'get', 'id'=>'form-filter')) }}
                                    <div class="form-group">
                                        {{ Form::label('search', 'Search', array('class' => 'col-2 col-form-label')) }}
                                        <div class="col-10">
                                            {{ Form::text('search', old('search', app('request')->get('search')), array('class' => 'form-control', 'placeholder'=>'Auditor ID, Unilever ID, Name, Email, Phone')) }}
                                        </div>
                                    </div>
                                    <div class="row m-t-10">
                                        <div class="form-group col-md-12">
                                            <button class="btn btn-success"><i class="fa fa-check"></i>
                                                Search
                                            </button>
                                            <a href="{{route('auditor.pull-cabinet.index')}}"
                                               class="btn btn-white">Clear</a>
                                        </div>
                                    </div>
                                    {{ Form::close() }}
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th>Auditor ID</th>
                                    <th>Unilever ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Created</th>
                                    <th>Total Juragan</th>
                                    <th colspan="3" class="text-center">Action</th>
                                </tr>
                                </thead>

                                <tbody>
                                @foreach($datas as $data)
                                    <tr>
                                        <td>{{$data->id}}</td>
                                        <td>{{$data->id_unilever}}</td>
                                        <td>{{$data->name}}</td>
                                        <td>{{$data->email}}</td>
                                        <td>{{$data->phone}}</td>
                                        <td>{{$data->created_at}}</td>
                                        <td>{{$data->id_juragan_mappings ? count(explode(',', $data->id_juragan_mappings)) : 0}}</td>
                                        <td class="text-center">
                                            <!-- {{ Form::open(array('url' => route('auditor.pull-cabinet.destroy', ['id'=>$data->id]))) }} -->

                                            @if($can_show)
                                                <a href="{{route('auditor.pull-cabinet.show', ['id'=>$data->id])}}"
                                                class="btn waves-effect waves-light btn-rounded btn-sm btn-success">
                                                    Show
                                                </a>
                                            @endif

                                            @if($can_edit)
                                                <a href="{{route('auditor.pull-cabinet.edit', ['id'=>$data->id])}}"
                                                class="btn waves-effect waves-light btn-rounded btn-xs btn-info">
                                                    Edit
                                                </a>
                                            @endif

                                            @if($can_destroy)
                                                <form name="form-delete-{{$data->id}}"
                                                        action="{{route('auditor.pull-cabinet.destroy',['id' => $data->id])}}"
                                                        method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" data-id="{{$data->id}}"
                                                            name="button-delete"
                                                            class="btn waves-effect waves-light btn-rounded btn-sm btn-danger"
                                                            title="delete">Delete
                                                    </button>
                                                </form>
                                            @endif

                                            <!-- {{ Form::close() }} -->
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        {{ $datas->appends(request()->input())->links("pagination::bootstrap-4") }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@include('layouts.alert2')

@push('scripts')
<script type="text/javascript">
        $(document).ready(function () {
            $("[name='button-delete']").click(function (e) {
                var id = $(this).data('id');
                swal({
                        title: "Delete Confirmation",
                        text: "Are you sure want to delete this record?",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        cancelButtonText: "Close",
                        confirmButtonText: "Delete",
                    }).then(function (isConfirm) {
                        if (isConfirm) {
                            console.log
                            $("[name='form-delete-" + id + "']").submit();
                        }
                    }
                );
            });
        });
    </script>
@endpush