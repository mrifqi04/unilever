<div class="left side-menu">
    <div class="sidebar-inner slimscrollleft">
        <!--- Divider -->
        <div id="sidebar-menu">
            <ul>

                <li class="text-muted menu-title">Navigation</li>
                <li class="has_sub">
                    <a href="{{route("home.dashboard")}}" class="waves-effect">
                        <i class="ti-home"></i>Dashboard
                        {{--<span class="menu-arrow"></span>--}}
                    </a>
                    {{--<a href="javascript:void(0);" class="waves-effect">--}}
                        {{--<i class="ti-home"></i> <span> Dashboard </span>--}}
                        {{--<span class="menu-arrow"></span>--}}
                    {{--</a>--}}
                    {{--<ul class="list-unstyled">--}}
                        {{--<li><a href="index.html">Dashboard 1</a></li>--}}
                        {{--<li><a href="dashboard_2.html">Dashboard 2</a></li>--}}
                        {{--<li><a href="dashboard_3.html">Dashboard 3</a></li>--}}
                        {{--<li><a href="dashboard_4.html">Dashboard 4</a></li>--}}
                    {{--</ul>--}}
                </li>

                @if(session('menus') != null)
                    @foreach (session('menus') as $menu)
                        <li class="has_sub">
                            <a href="javascript:void(0);" class="waves-effect">
                                @if($menu->icon != "")
                                    <i class="{{$menu->icon}}"></i>
                                @endif
                                <span> {{$menu->name}} </span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul class="list-unstyled">
                                @foreach ($menu->submenus as $submenu)
                                    <li>
                                        <a href="{{route($submenu->permission->name)}}/">
                                            {{--@if($menu->icon != "")--}}
                                                {{--<i class="{{$submenu->icon}}"></i>--}}
                                            {{--@endif--}}
                                            <span>{{$submenu->name}}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    @endforeach
                @endif
            </ul>
            <div class="clearfix"></div>
        </div>
        <div class="clearfix"></div>
    </div>
</div>