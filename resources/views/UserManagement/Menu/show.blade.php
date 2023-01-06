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
                                <a href="{{route('menu.index')}}">User</a>
                            </li>
                            <li class="active">
                                Show
                            </li>
                        </ol>
                        <h4 class="m-t-0 header-title">
                            <b>Data Menu</b>
                        </h4>
                        {{ Html::ul($errors->all()) }}
                        <div class="form-group">
                            {{ Form::label('id', 'ID', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                {{ $data->id }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('name', 'Name', array('class' => 'col-2 col-form-label')) }}
                            <div class="col-10 form-control">
                                {{ $data->name }}
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-2"></div>
                            <div class="col-10">
                                <a class="btn btn-danger m-t-20" href="{{ route('menu.index') }}">
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