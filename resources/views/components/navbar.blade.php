<link rel="stylesheet" href="{{ asset('css/navbar.css') }}">

<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="{{ route('landing') }}">
            <img src="{{ asset('images/cose-logo.png') }}" alt="System Logo" width="30" class="me-2">
            <span class="text-dark">SulongKalinga</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link {{ Request::routeIs('aboutUs') ? 'active' : '' }}" href="{{ route('aboutUs') }}">About Us</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ Request::routeIs('highlightsEvents') ? 'active' : '' }}" href="#" id="highlightsDropdown" role="button" data-bs-toggle="dropdown">
                        Highlights & Events
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">Upcoming Events</a></li>
                        <li><a class="dropdown-item" href="#">Past Events</a></li>
                        <li><a class="dropdown-item" href="#">News & Updates</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::routeIs('donor') ? 'active' : '' }}" href="{{ route('donor') }}">Donor</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::routeIs('contactUs') ? 'active' : '' }}" href="{{ route('contactUs') }}">Contact Us</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ Request::routeIs('login') || Request::routeIs('forgotPass') ? 'active' : '' }}" href="{{ route('login') }}">Login</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
