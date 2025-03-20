<link rel="stylesheet" href="{{ asset('css/navbar.css') }}">

<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('landing') }}">
            <img src="{{ asset('images/cose-logo.png') }}" alt="System Logo" width="30" class="me-2">
            <span class="text-dark">SulongKalinga</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-end bg-light" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link {{ Request::routeIs('messages') ? 'active' : '' }}" href="#">Messages</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::routeIs('notifications') ? 'active' : '' }}" href="#">Notifications</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ Request::routeIs('account') ? 'active' : '' }}" href="#" id="highlightsDropdown" role="button" data-bs-toggle="dropdown">
                        Account
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item {{ Request::routeIs('account') ? 'active' : '' }}" href="#">Account Profile</a></li>
                        <li><form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item">Logout</button>
                        </form></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

