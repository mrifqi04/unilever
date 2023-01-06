<div class="topbar">

    <!-- LOGO -->
    <div class="topbar-left">
        <div class="text-center">
            {{--<a href="index.html" class="logo"><i class="icon-magnet icon-c-logo"></i><span>Ub<i class="md md-album"></i>ld</span></a>--}}
            <a href="{{route('home.dashboard')}}" class="logo">
                <i class="icon-c-logo">
                    <img src="{{asset('assets/images/seru_logo.png')}}" height="42"/>
                </i>
                <span><img src="{{asset('assets/images/seru_logo.png')}}" height="32"/> {{config('app.name')}}</span>
            </a>
            <!-- Image Logo here -->
            <!--<a href="index.html" class="logo">-->
            <!--<i class="icon-c-logo"> <img src="assets/images/logo_sm.png" height="42"/> </i>-->
            <!--<span><img src="assets/images/logo_light.png" height="20"/></span>-->
            <!--</a>-->
        </div>
    </div>

    <!-- Button mobile view to collapse sidebar menu -->
    <div class="navbar navbar-default" role="navigation">
        <div class="container">
            <div class="">
                <div class="pull-left">
                    <button class="button-menu-mobile open-left waves-effect waves-light">
                        <i class="md md-menu"></i>
                    </button>
                    <span class="clearfix"></span>
                </div>

                <ul class="nav navbar-nav navbar-right pull-right">

                    <li class="dropdown top-menu-item-xs">
                        <a href="" class="dropdown-toggle profile waves-effect waves-light" data-toggle="dropdown" aria-expanded="true">
                            {{{ isset(Auth::user()->name) ? Auth::user()->name : Auth::user()->email }}}
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="{{route('user.profile')}}">
                                    <i class="ti-user m-r-10 text-custom"></i> Profile
                                </a>
                            </li>
                            <li class="divider"></li>
                            <li>
                                <a href="{{route('auth.logout')}}">
                                    <i class="ti-power-off m-r-10 text-danger"></i> Logout
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
            <!--/.nav-collapse -->
        </div>
    </div>
</div>