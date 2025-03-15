<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/forgotPass.css') }}">
</head>
<body>
    @include('components.navbar')

    <div class="container text-center" id="forgot-password">
        <div class="row justify-content-center">
            <div class="col">
            
            </div>
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body">
                        <h2 class="text-center">Forgot Password</h2>
                        <form id="forgotPasswordForm">
                        <!-- <form action="#" method="POST"> -->
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" placeholder="lastnameFirstname@example.com" required>
                                <!-- EMAIL INPUT ERROR
                                @error('email')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror -->
                            </div>
                            <button type="submit" class="btn btn-primary w-100" id="forgotSubmit">Send Reset Link</button>
                        </form>
                        <div class="backtoLogin">
                        <a href="/login">Back to Login</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">

            </div>
        </div>
    </div>

    @include('components.modals.resetRequestSuccess')

    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script>
        document.getElementById('forgotPasswordForm').addEventListener('submit', function(event) {
            event.preventDefault();
            var myModal = new bootstrap.Modal(document.getElementById('exampleModal'));
            myModal.show();
            this.reset();
        });
    </script>

</body>
</html>