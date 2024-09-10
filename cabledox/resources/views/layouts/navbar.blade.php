<nav class="navbar navbar-fixed-top">
        <div class="container-fluid">
            <div class="navbar-left">
                <div class="navbar-btn">
                    @php
                        $logoImage = \Config::get('constants.static.cabeldoxLogo');
                    @endphp
                    <a href="{{route('dashboard.index')}}"><img src="{{asset($logoImage) }}" alt="Mooli Logo" class="img-fluid logo"></a>
                    <button type="button" class="btn-toggle-offcanvas"><i class="fa fa-align-left"></i></button>
                </div>
            </div>
            <div class="navbar-right">
                <div id="navbar-menu">
                    <ul class="nav navbar-nav">
                        <li class="hidden-xs"><a href="javascript:void(0);" id="btnFullscreen" class="icon-menu"><i class="fa fa-arrows-alt"></i></a></li>
                        <li>
                            <a class="icon-menu" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">{{ Auth::user()->name }}<i class="fa fa-power-off"></i></a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>                                
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>