<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
</head>
<body>

    @include('components.navbar')
    
    <div class="container text-center" id="login">
        <div class="row justify-content-center">
            <div class="col">
            
            </div>
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body">
                        <h2 class="text-center">Login</h2>
                        <!-- ALERT ANY ERRORS -->
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <form action="#" method="POST">
                            <!-- @csrf (VALIDATION TOKEN) -->
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" placeholder="lastnameFirstname@example.com" required>
                                <!-- EMAIL INPUT ERROR -->
                                <!-- @error('email')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror -->
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" class="form-control" placeholder="Enter your password" required>
                                <!-- PASSWORD INPUT ERROR -->
                                <!-- @error('password')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror -->
                            </div>
                            <div class="mb-3 text-end">
                                <a href="{{ route('forgotPass') }}">Forgot password?</a>
                            </div>
                            <button type="submit" class="btn btn-primary w-100" id="submitLogin">Login</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col">
            
            </div>
        </div>
    </div>
        

    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>
