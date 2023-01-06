<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="{{config('app.name')}}">
<meta name="author" content="{{config('app.name')}}">

<link rel="shortcut icon" href="{{asset('assets/images/seru_logo.png')}}">
{{--<link rel="shortcut icon" href="{{asset('assets/images/favicon_1.ico')}}">--}}

<title>{{config('app.name')}}</title>

<!--Morris Chart CSS -->
<link rel="stylesheet" href="{{asset('assets/plugins/morris/morris.css')}}">
@stack('styles')
<link href="{{asset('assets/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css"/>
<link href="{{asset('assets/css/core.css')}}" rel="stylesheet" type="text/css"/>
<link href="{{asset('assets/css/components.css')}}" rel="stylesheet" type="text/css"/>
<link href="{{asset('assets/css/icons.css')}}" rel="stylesheet" type="text/css"/>
<link href="{{asset('assets/css/pages.css')}}" rel="stylesheet" type="text/css"/>
<link href="{{asset('assets/css/responsive.css')}}" rel="stylesheet" type="text/css"/>

<!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
<script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
<![endif]-->

<script src="{{asset('assets/js/modernizr.min.js')}}"></script>

