@extends('layouts.app')

@section('content')
    <div class="content">
        <div class="container-fluid">

            <!-- Block Juragan -->
            <div class="portlet">
                <!-- Block Header -->
                <div class="portlet-heading bg-primary">
                    <h3 class="portlet-title"> Juragan </h3>

                    <div class="portlet-widgets">
                        <span class="divider"></span>
                        <a data-toggle="collapse" data-parent="#accordion1" href="#bg-juragan"><i class="ion-minus-round"></i></a>
                        <span class="divider"></span>
                    </div>
                    <div class="clearfix"></div>
                </div>

                <div id="bg-juragan" class="panel-collapse collapse in">
                    <div class="portlet-body">
                        <div class="container-fluid">

                            <div class="row">
                                <div class="col-md-6 col-sm-6 col-lg-4">
                                    <div class="card-box widget-box-1 bg-white">
                                        <h4 class="text-dark">Total</h4>
                                        <h2 class="text-primary text-center">
                                            <span id="total_juragan">-</span>
                                        </h2>
                                    </div>
                                </div>

                                <div class="col-md-6 col-sm-6 col-lg-4">
                                    <div class="card-box widget-box-1 bg-white">
                                        <h4 class="text-dark">Total Active</h4>
                                        <h2 class="text-primary text-center">
                                            <span id="total_active_juragan">-</span>
                                        </h2>
                                    </div>
                                </div>

                                <div class="col-md-6 col-sm-6 col-lg-4">
                                    <div class="card-box widget-box-1 bg-white">
                                        <h4 class="text-dark">Total Inactive</h4>
                                        <h2 class="text-primary text-center">
                                            <span id="total_incative_juragan">-</span>
                                        </h2>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <!-- Block Outlet -->
            <div class="portlet">
                <!-- Block Header -->
                <div class="portlet-heading bg-primary">
                    <h3 class="portlet-title"> Outlet </h3>

                    <div class="portlet-widgets">
                        <span class="divider"></span>
                        <a data-toggle="collapse" data-parent="#accordion1" href="#bg-outlet">
                            <i class="ion-minus-round"></i>
                        </a>
                        <span class="divider"></span>
                    </div>
                    <div class="clearfix"></div>
                </div>

                <div id="bg-outlet" class="panel-collapse collapse in">
                    <div class="portlet-body">
                        <div class="container-fluid">

                            <div class="row">
                                <div class="col-md-6 col-sm-6 col-lg-4">
                                    <div class="card-box widget-box-1 bg-white">
                                        <h4 class="text-dark">Total</h4>
                                        <h2 class="text-primary text-center">
                                            <span id="total_outlet">-</span>
                                        </h2>
                                    </div>
                                </div>

                                <div class="col-md-6 col-sm-6 col-lg-4">
                                    <div class="card-box widget-box-1 bg-white">
                                        <h4 class="text-dark">Total Active</h4>
                                        <h2 class="text-primary text-center">
                                            <span id="total_active_outlet">-</span>
                                        </h2>
                                    </div>
                                </div>

                                <div class="col-md-6 col-sm-6 col-lg-4">
                                    <div class="card-box widget-box-1 bg-white">
                                        <h4 class="text-dark">Total Inactive</h4>
                                        <h2 class="text-primary text-center">
                                            <span id="total_inactive_outlet">-</span>
                                        </h2>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <!-- Block Cabinet -->
            <div class="portlet">
                <!-- Block Header -->
                <div class="portlet-heading bg-primary">
                    <h3 class="portlet-title"> Cabinet </h3>

                    <div class="portlet-widgets">
                        <span class="divider"></span>
                        <a data-toggle="collapse" data-parent="#accordion1" href="#bg-cabinet">
                            <i class="ion-minus-round"></i>
                        </a>
                        <span class="divider"></span>
                    </div>
                    <div class="clearfix"></div>
                </div>

                <div id="bg-cabinet" class="panel-collapse collapse in">
                    <div class="portlet-body">
                        <div class="container-fluid">

                            <div class="row">
                                <div class="col-md-6 col-sm-6 col-lg-4">
                                    <div class="card-box widget-box-1 bg-white">
                                        <h4 class="text-dark">Total</h4>
                                        <h2 class="text-primary text-center">
                                            <span id="total_cabinet">-</span>
                                        </h2>
                                    </div>
                                </div>

                                <div class="col-md-6 col-sm-6 col-lg-4">
                                    <div class="card-box widget-box-1 bg-white">
                                        <h4 class="text-dark">Total Used</h4>
                                        <h2 class="text-primary text-center">
                                            <span id="total_used_cabinet">-</span>
                                        </h2>
                                    </div>
                                </div>

                                <div class="col-md-6 col-sm-6 col-lg-4">
                                    <div class="card-box widget-box-1 bg-white">
                                        <h4 class="text-dark">Total Available</h4>
                                        <h2 class="text-primary text-center">
                                            <span id="total_available_cabinet">-</span>
                                        </h2>
                                    </div>
                                </div>
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
    <link href="{{asset('assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css')}}" rel="stylesheet">
    <link href="{{asset('assets/plugins/bootstrap-daterangepicker/daterangepicker.css')}}" rel="stylesheet">
@endpush

@push('scripts')
    <script src="{{asset('assets/plugins/morris/morris.min.js')}}"></script>
    <!-- Counterup  -->
    <script src="{{asset('assets/plugins/waypoints/lib/jquery.waypoints.js')}}"></script>
    <script src="{{asset('assets/plugins/counterup/jquery.counterup.min.js')}}"></script>
    <!-- Datepicker  -->
    <script src="{{asset('assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>
    <script src="{{asset('assets/plugins/bootstrap-daterangepicker/daterangepicker.js')}}"></script>

    <script>
        $("document").ready(function(){
            $.ajax({
                url:"{{route("home.juragan")}}",
                method:"GET",
                success: function (data) {
                    console.log(data);
                    let total = 0;
                    $.each(data.data, function (index, value){
                        total += value.total;
                        if(value.is_deleted == 1){
                            console.log("OK");
                            $("#total_active_juragan").html(value.total);
                        }else{
                            $("#total_incative_juragan").html(value.total);
                        }
                    });
                    $("#total_juragan").html(total);
                }
            });

            $.ajax({
                url:"{{route("home.outlet")}}",
                method:"GET",
                success: function (data) {
                    console.log(data);
                    let total = 0;
                    $.each(data.data, function (index, value){
                        total += value.total;
                        if(value.is_deleted == 1){
                            console.log("OK");
                            $("#total_active_outlet").html(value.total);
                        }else{
                            $("#total_incative_outlet").html(value.total);
                        }
                    });
                    $("#total_outlet").html(total);
                }
            });

            $.ajax({
                url:"{{route("home.cabinet")}}",
                method:"GET",
                success: function (data) {
                    console.log(data);
                    let total, used, available = 0;
                    total = data.data.total;
                    used = data.data.used;
                    available = data.data.unused;
                    $("#total_cabinet").html(total);
                    $("#total_used_cabinet").html(used);
                    $("#total_available_cabinet").html(available);
                }
            });
        })
    </script>
@endpush