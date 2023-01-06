@extends('layouts.app')

@section('css')
    <style type="text/css">
        .preloader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 9999;
            background-color: rgba(255, 2555, 255, 0.5);
        }

        .loading {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            font: 14px arial;
        }

        .lds-roller {
            display: inline-block;
            position: relative;
            width: 80px;
            height: 80px;
        }

        .lds-roller div {
            animation: lds-roller 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite;
            transform-origin: 40px 40px;
        }

        .lds-roller div:after {
            content: " ";
            display: block;
            position: absolute;
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #000;
            margin: -4px 0 0 -4px;
        }

        .lds-roller div:nth-child(1) {
            animation-delay: -0.036s;
        }

        .lds-roller div:nth-child(1):after {
            top: 63px;
            left: 63px;
        }

        .lds-roller div:nth-child(2) {
            animation-delay: -0.072s;
        }

        .lds-roller div:nth-child(2):after {
            top: 68px;
            left: 56px;
        }

        .lds-roller div:nth-child(3) {
            animation-delay: -0.108s;
        }

        .lds-roller div:nth-child(3):after {
            top: 71px;
            left: 48px;
        }

        .lds-roller div:nth-child(4) {
            animation-delay: -0.144s;
        }

        .lds-roller div:nth-child(4):after {
            top: 72px;
            left: 40px;
        }

        .lds-roller div:nth-child(5) {
            animation-delay: -0.18s;
        }

        .lds-roller div:nth-child(5):after {
            top: 71px;
            left: 32px;
        }

        .lds-roller div:nth-child(6) {
            animation-delay: -0.216s;
        }

        .lds-roller div:nth-child(6):after {
            top: 68px;
            left: 24px;
        }

        .lds-roller div:nth-child(7) {
            animation-delay: -0.252s;
        }

        .lds-roller div:nth-child(7):after {
            top: 63px;
            left: 17px;
        }

        .lds-roller div:nth-child(8) {
            animation-delay: -0.288s;
        }

        .lds-roller div:nth-child(8):after {
            top: 56px;
            left: 12px;
        }

        @keyframes lds-roller {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
@endsection

@section('content')
    <div class="preloader hidden" id="loading_screen">
        <div class="loading">
            <div class="lds-roller">
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card-box">
                        <ol class="breadcrumb">
                            <li>
                                Hunter
                            </li>
                            <li class="active">
                                <a href="{{ route('hunter.import') }}">Upload Hunter</a>
                            </li>
                        </ol>
                        <h4 class="m-t-0 header-title"><b>Data</b></h4>

                        <button class="btn btn-primary  d-none d-lg-block" type="button" data-toggle="collapse"
                            data-target="#filter_data" aria-expanded="false" aria-controls="collapseExample">
                            <i class="fa fa-arrow-circle-down"></i> Upload Form
                        </button>

                        <a class="btn btn-success d-none d-lg-block"
                            href="{{ asset('assets/templates/hunter-template.csv') }}" download>
                            <i class="fa fa-download"></i> Download Template
                        </a>

                        @if (Auth::user()->getPermissionByName('hunter.import'))
                            <div class="collapse" id="filter_data">
                                <div class="card-box">
                                    <div class="card-view">
                                        {{ Form::open(['url' => route('hunter.doImport'), 'role' => 'form', 'files' => 'true']) }}

                                        <div class="form-group">
                                            {{ Form::label('import_file', 'File', ['class' => 'col-2 col-form-label']) }}
                                            <div class="col-10">
                                                {{ Form::file('import_file') }}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="col-10">
                                                <button class="btn btn-success" id="submit_import_button"><i
                                                        class="fa fa-check"></i> Submit
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
                                @foreach ($data as $index => $job)
                                    <tr>
                                        <td>{{ $job->file_name_origin }}</td>
                                        <td>{{ $job->created_at }}</td>
                                        <td>{{ $job->updated_at }}</td>
                                        <td>
                                            @if ($job->status->id === 2)
                                                <button type="button"
                                                    class="btn btn-success btn-sm btn-custom btn-rounded waves-effect waves-light">
                                                    {{ $job->status->name }}
                                                </button>
                                            @else
                                                <button type="button"
                                                    class="btn btn-danger btn-sm btn-custom btn-rounded waves-effect waves-light">
                                                    {{ $job->status->name }}
                                                </button>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($job->status->id === 3)
                                                <a href="#modal-delivery-date-{{ $job->id }}"
                                                    data-id="{{ $job->id }}"
                                                    class="btn waves-effect waves-light btn-rounded btn-sm btn-warning ion-search"
                                                    data-animation="fadein" data-plugin="custommodal"
                                                    data-overlayspeed="200" data-overlaycolor="#36404a"
                                                    title="Delivery Date">
                                                </a>
                                                <!-- Modal -->
                                                <div id="modal-delivery-date-{{ $job->id }}" class="modal-demo">
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
                        {{ $data->links('pagination::bootstrap-4') }}
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
    <script>
        $(document).ready(function() {
            $('#submit_import_button').click(function() {
                $('#loading_screen').removeClass('hidden')
            })
        })
    </script>
@endpush
