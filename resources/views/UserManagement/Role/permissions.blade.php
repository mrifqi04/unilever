@extends('layouts.app')
@section('content')
    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card-box">
                        <ol class="breadcrumb">
                            <li>
                                User Management
                            </li>
                            <li>
                                <a href="{{route('role.index')}}">Role</a>
                            </li>
                            <li class="active">
                                Permission
                            </li>
                        </ol>
                        <h4 class="m-t-0 header-title">
                            <b>Role</b>
                        </h4>
                        {{ Html::ul($errors->all()) }}
                        <div class="form-group">
                            {{ Form::label('name', 'Name', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                {{ $data->name }}
                            </div>
                        </div>

                        <div class="form-group">
                            <h4 class="m-t-0 header-title">
                                <b>Data Permission</b>
                            </h4>
                            {{ Form::open(array('url' => route('role.storepermissions', ['id'=>$data->id]), 'class' => 'form govisform')) }}
                            <div class="row">
                                <div class="col-xs-5">
                                    <select name="from[]" id="search" class="form-control" size="8" multiple="multiple">
                                        @foreach($data->permissions as $d)
                                            <option value="{{$d->id}}">{{$d->name}} - {{$d->caption}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-xs-2">
                                    <button type="button" id="search_rightAll" class="btn btn-block"><i class="glyphicon glyphicon-forward"></i></button>
                                    <button type="button" id="search_rightSelected" class="btn btn-block"><i class="glyphicon glyphicon-chevron-right"></i></button>
                                    <button type="button" id="search_leftSelected" class="btn btn-block"><i class="glyphicon glyphicon-chevron-left"></i></button>
                                    <button type="button" id="search_leftAll" class="btn btn-block"><i class="glyphicon glyphicon-backward"></i></button>
                                </div>

                                <div class="col-xs-5">
                                    <select name="to[]" id="search_to" class="form-control" size="8" multiple="multiple">
                                        @foreach($permissions as $nd)
                                            <option value="{{$nd->id}}">{{$nd->name}} - {{$nd->caption}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-12">
                                    <button id="btn-add" type="submit" class="btn btn-info btn-block d-lg pull-left">
                                        <i class="fa fa-check"></i>Save
                                    </button>
                                </div>
                            </div>
                            {{ Form::close() }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{asset('assets/js/crlcu_multiselect.min.js')}}"></script>
    <script>
        $('document').ready(function(){
            $('#search').multiselect({
                search: {
                    left: '<input type="text" name="q" class="form-control" placeholder="Selected Permission" />',
                    right: '<input type="text" name="q" class="form-control" placeholder="Search Permission" />',
                },
                fireSearch: function(value) {
                    return value.length >= 3;
                }
            });


            $('.govisform').on('submit', function(event){
                event.preventDefault();
                this.submit();
                // var checked = $('input[type=checkbox]:checked').length;
                // if(checked > 0 ){
                //     this.submit();
                // }else{
                //     swal({
                //         type: "error",
                //         title: "Delete Permission",
                //         text: "Please Select permission first before submitting",
                //         timer: 3000,
                //     });
                // }
            });
        });
    </script>
@endpush

@include('layouts.alert')