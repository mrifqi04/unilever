<!DOCTYPE html>
<html>
<head>
    @include('layouts.header')
</head>
<body>
<body class="fixed-left">

<!-- Begin page -->
<div id="wrapper">

    <!-- Top Bar Start -->
    @include('layouts.topbar')
    <!-- Top Bar End -->


    <!-- ========== Left Sidebar Start ========== -->

    @include('layouts.sidebar')
    <!-- Left Sidebar End -->



    <!-- ============================================================== -->
    <!-- Start right Content here -->
    <!-- ============================================================== -->
    <div class="content-page">
        @yield('content')
        <footer class="footer">
            © 2016. All rights reserved.
        </footer>
    </div>

</div>
<!-- END wrapper -->

@include('layouts.footer')

</body>
</html>
