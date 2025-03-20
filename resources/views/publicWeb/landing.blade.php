<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sulong Kalinga</title>
    <link rel="stylesheet" href="{{ asset('css/landing.css')}}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
</head>
<body>

    @include('components.navbar')

    <div class="container text-center" id="landing">
        <div class="row">
            <div class="col" style="background-color: red;">
                a
            </div>
            <div class="col-6" style="text-align: left;">
            <h1>Sulong</h1> 
            <h1>Kalinga</h1>
                <p class="lead">Mobile Care Services Platform with Caregiver GPS Tracking and Automated Summary Generation Using Natural Language Processing for the Coalition of Services for the Elderly, Inc.</p>
                <a href="#" class="btn btn-outline-dark">Learn More</a>
                <a href="{{ route('login') }}" class="btn btn-primary">Login</a>
            </div>
            <div class="col-4" style="background-color: yellow;">
                a
            </div>
            <div class="col" style="background-color: red;">
                a
            </div>
            @if (Auth::check())
    <p>Logged-in User ID: {{ Auth::user()->id }}</p>
@else
    <p>No user is logged in.</p>
@endif
        </div>
    </div>
    
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>