<header>
    <div class="main-header inner-header">
        <div class="container">
            <div class="header_cls">
                <div class="logo">
                    <a href="{{route('web.home')}}"><img src="{{asset('web/images/logo.png')}}"></a>
                </div>
                <div class="menu">
                    <nav class="navbar navbar-expand-lg p-0">
                        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                            <img src="{{asset('web/images/menu.png')}}" alt="">
                        </button>
                        <div class="collapse navbar-collapse" id="navbarSupportedContent">
                            <ul class="navbar-nav mr-auto">
                                <div class="remove_bg"><button type="button" class="btn_cross"><img src="{{asset('web/images/cross.png')}}"></button></div>
                                <li class="nav-item">
                                    <a href="" class="nav-link">About US</a>
                                </li>
                                <li class="nav-item">
                                    <a href="" class="nav-link">Upcoming Matches</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{route('web.court_list')}}" class="nav-link">Courts</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{route('web.facility')}}" class="nav-link">Facilities</a>
                                </li>
                                <li class="nav-item">
                                    <a href="" class="nav-link">Contact us</a>
                                </li>
                            </ul>
                        </div>
                    </nav>
                </div>
                <div class="account_sec dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="head-user-img"><img src="{{asset('web/images/user.png')}}"></span> <span class="user-name-cls">Peter Parker</span></button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item" href="{{route('web.my_account')}}">My Account</a>
                        <a class="dropdown-item" href="{{route('web.challenges')}}">Challenges</a>
                        <a class="dropdown-item" href="{{route('web.upcoming_booking')}}">Upcoming Bookings</a>
                        <a class="dropdown-item" href="{{route('web.completed_booking')}}">Completed Bookings</a>
                        <a class="dropdown-item" href="{{route('web.change_password')}}">Change Password</a>
                        <a class="dropdown-item" href="{{route('web.home')}}">Log Out</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>