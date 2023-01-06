@extends('layouts.app')

@section('content')
    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card-box">
                        <ol class="breadcrumb">
                            <li>
                                Juragan
                            </li>
                            <li class="active">
                                <a href="{{ route('juragan.import') }}">Upload Juragan</a>
                            </li>
                        </ol>
                        <h4 class="m-t-0 header-title"><b>Data</b></h4>

                        <button class="btn btn-primary  d-none d-lg-block" type="button" data-toggle="collapse"
                            data-target="#filter_data" aria-expanded="false" aria-controls="collapseExample">
                            <i class="fa fa-arrow-circle-down"></i> Upload Form
                        </button>

                        <a class="btn btn-success d-none d-lg-block"
                            href="{{ asset('assets/templates/juragan-template.csv') }}" download>
                            <i class="fa fa-download"></i> Download Template
                        </a>

                        @if (Auth::user()->getPermissionByName('juragan.import'))
                            <div class="collapse" id="filter_data">
                                <div class="card-box">
                                    <div class="card-view">
                                        {{ Form::open(['url' => route('juragan.import'), 'role' => 'form', 'files' => 'true']) }}

                                        <div class="form-group">
                                            {{ Form::label('import_file', 'File', ['class' => 'col-2 col-form-label']) }}
                                            <div class="col-10">
                                                {{ Form::file('import_file') }}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="col-10">
                                                <button type="submit" class="btn btn-success"><i class="fa fa-check"></i>
                                                    Submit
                                                </button>
                                            </div>
                                        </div>
                                        {{ Form::close() }}
                                    </div>
                                </div>
                            </div>
                        @endif

                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>File Name</th>
                                    <th>Created</th>
                                    <th>Updated</th>
                                    <th>Status</th>
                                    <th>Log</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($datas as $index => $data)
                                    <tr>
                                        <td>{{ $data->file_name_origin }}</td>
                                        <td>{{ $data->created_at }}</td>
                                        <td>{{ $data->updated_at }}</td>
                                        <td>
                                            @if ($data->status->id === 2)
                                                <button type="button"
                                                    class="btn btn-success btn-sm btn-custom btn-rounded waves-effect waves-light">
                                                    {{ $data->status->name }}
                                                </button>
                                            @else
                                                <button type="button"
                                                    class="btn btn-danger btn-sm btn-custom btn-rounded waves-effect waves-light">
                                                    {{ $data->status->name }}
                                                </button>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($data->status->id === 3)
                                                <a href="#modal-delivery-date-{{ $data->id }}"
                                                    data-id="{{ $data->id }}"
                                                    class="btn waves-effect waves-light btn-rounded btn-sm btn-warning ion-search"
                                                    data-animation="fadein" data-plugin="custommodal"
                                                    data-overlayspeed="200" data-overlaycolor="#36404a"
                                                    title="Delivery Date">
                                                </a>
                                                <!-- Modal -->
                                                <div id="modal-delivery-date-{{ $data->id }}" class="modal-demo">
                                                    <button type="button" class="close" onclick="Custombox.close();">
                                                        <span>&times;</span><span class="sr-only">Close</span>
                                                    </button>
                                                    <h4 class="custom-modal-title">Log Error</h4>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <textarea class="form-control" rows="20">
@foreach ($data_messages[$index] as $i => $value)
{{ $i + 1 }}.{{ $value }}. &#13;&#10;
@endforeach
</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{ $datas->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@include('layouts.alert')

@push('styles')
    <link href="{{ asset('assets/plugins/custombox/css/custombox.css') }}" rel="stylesheet">
@endpush

@push('scripts')
    <!-- Modal-Effect -->
    <script src="{{ asset('assets/plugins/custombox/js/custombox.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/custombox/js/legacy.min.js') }}"></script>
@endpush
