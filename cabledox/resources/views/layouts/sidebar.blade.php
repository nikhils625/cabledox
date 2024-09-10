<div id="left-sidebar" class="sidebar">
    <a href="javascript:void(0);" class="menu_toggle"><i class="fa fa-angle-left"></i></a>
    <div class="navbar-brand">
        @php
            $logoImage = \Config::get('constants.static.cabeldoxLogo');
        @endphp
        <a href="{{route('dashboard.index')}}"><img src="{{asset($logoImage) }}" alt="Cabledox Logo" class="img-fluid logo">
        <span>{{ config('app.name') }}</span></a>
        <button type="button" class="btn-toggle-offcanvas btn btn-sm float-right"><i class="fa fa-close"></i></button>
    </div>
    <div class="sidebar-scroll">
        <div class="user-account">
            <div class="user_div">
                @php
                    $profileImage = \Config::get('constants.static.staticProfileImage');
                       
                    if(isset(Auth::user()->user_profile) && !empty(Auth::user()->user_profile)) {
                        $profileImage = \Config::get('constants.uploadPaths.viewProfileImage') . Auth::user()->user_profile;
                    }
                @endphp
                <img src="{{asset($profileImage)}}" class="user-photo" alt="User Profile Picture">
            </div>
            <div class="dropdown">
                <a href="javascript:void(0);" class="dropdown-toggle user-name" data-toggle="dropdown"><strong class="auth-name">{{ ucfirst(Auth::user()->first_name) }} {{ ucfirst(Auth::user()->last_name) }}</strong></a>
                <ul class="dropdown-menu dropdown-menu-right account vivify flipInY">
                    <li><a href="{{route('users.my-profile')}}"><i class="fa fa-user"></i>My Profile</a></li>
                    <li><a href="{{route('users.change-password-form')}}"><i class="fa fa-lock"></i>Change Password</a></li>
                    <li class="divider"></li>
                    <li><a href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-forms').submit();"><i class="fa fa-power-off"></i>{{ __('Logout') }}</a>
                        <form id="logout-forms" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                    </li>
                </ul>
            </div>
        </div>  
        <nav id="left-sidebar-nav" class="sidebar-nav">
            <ul id="main-menu" class="metismenu animation-li-delay">
                <li class="header">Main</li>
                <li class="{{ Request::segment(1) === 'dashboard' ? 'active' : 
                null }}"><a href="{{route('dashboard.index')}}"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>               
                <li class="header">Vendors</li>

                @canany(['clients.create', 'clients.list'])
                <li class="{{ Request::segment(1) === 'clients' ? 'active' : null }}">
                    <a href="#clients" class="has-arrow"><i class="fa fa-address-book"></i><span>Clients</span></a>
                    <ul> 
                       @can('clients.create')
                        <li class="{{ Request::segment(2) === 'create' ? 'active' : null }}"><a href="{{route('clients.create')}}">Add Client</a></li>
                       @endcan
                       @can('clients.list')
                        <li class="{{ Request::segment(2) === 'index' ? 'active' : null }}"><a href="{{route('clients.index')}}">List Client</a></li>
                       @endcan
                    </ul>
                </li>
                @endcan

                @canany(['users.create', 'users.list'])
                <li class="{{ Request::segment(1) === 'users' ? 'active' : null }}">
                    <a href="#users" class="has-arrow"><i class="fa fa-address-book"></i><span>Users</span></a>
                    <ul>
                        @can('users.create')
                            <li class="{{ Request::segment(2) === 'create' ? 'active' : null }}"><a href="{{route('users.create')}}">Add</a></li>
                        @endcan
                        @can('users.list')
                            <li class="{{ in_array(Request::segment(2), ['index', null])? 'active' : null }}"><a href="{{route('users.index')}}">List</a></li>
                        @endcan
                    </ul>
                </li>
                @endcan

                @canany(['cable-master.create', 'cable-master.list'])
                <li class="{{ Request::segment(1) === 'cable-masters' ? 'active' : null }}">
                    <a href="#cable-masters" class="has-arrow"><i class="fa fa-plug"></i><span>Cable Master</span></a>
                    <ul>
                        @can('cable-master.create')
                            <li class="{{ Request::segment(2) === 'create' ? 'active' : null }}"><a href="{{route('cable-masters.create')}}">Add</a></li>
                        @endcan
                        @can('cable-master.list')
                            <li class="{{ in_array(Request::segment(2), ['index', null])? 'active' : null }}"><a href="{{route('cable-masters.index')}}">List</a></li>
                        @endcan
                    </ul>
                </li>
                @endcan

                @canany(['final-check-sheet.create', 'final-check-sheet.list'])
                <li class="{{ Request::segment(1) === 'final-check-sheet' ? 'active' : null }}">
                    <a href="#final-check-sheet" class="has-arrow"><i class="fa fa-calendar-check-o"></i><span>Final Check Sheet</span></a>
                    <ul>
                        @can('final-check-sheet.create')
                            <li class="{{ Request::segment(2) === 'create' ? 'active' : null }}"><a href="{{route('final-check-sheet.create')}}">Add</a></li>
                        @endcan
                        @can('final-check-sheet.list')
                            <li class="{{ in_array(Request::segment(2), ['index', null])? 'active' : null }}"><a href="{{route('final-check-sheet.index')}}">List</a></li>
                        @endcan
                    </ul>
                </li>
                @endcan

                @canany(['test-parameters.create', 'test-parameters.list'])
                <li class="{{ Request::segment(1) === 'test-parameters' ? 'active' : null }}">
                    <a href="#test-parameters" class="has-arrow"><i class="icon-check"></i><span>Test Parameters</span></a>
                    <ul>
                        @can('test-parameters.create')
                            <li class="{{ Request::segment(2) === 'create' ? 'active' : null }}"><a href="{{route('test-parameters.create')}}">Add</a></li>
                        @endcan
                        @can('test-parameters.list')
                            <li class="{{ in_array(Request::segment(2), ['index', null])? 'active' : null }}"><a href="{{route('test-parameters.index')}}">List</a></li>
                        @endcan
                    </ul>
                </li>
                @endcan

                @canany(['jobs.create', 'jobs.list'])
                <li class="{{ Request::segment(1) === 'jobs' ? 'active' : null }}">
                    <a href="#jobs" class="has-arrow"><i class="fa fa-tasks"></i><span>Jobs</span></a>
                    <ul>
                        @can('jobs.create')
                            <li class="{{ Request::segment(2) === 'create' ? 'active' : null }}"><a href="{{route('jobs.create')}}">Add</a></li>
                        @endcan
                        @can('jobs.list')
                            <li class="{{ in_array(Request::segment(2), ['index', null])? 'active' : null }}"><a href="{{route('jobs.index')}}">List</a></li>
                        @endcan
                    </ul>
                </li>
                @endcan
            </ul>
        </nav>     
    </div>
</div>