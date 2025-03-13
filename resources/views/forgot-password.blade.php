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
                            </div>
                            <button type="submit" class="btn btn-primary w-100" id="forgotSubmit">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col">

            </div>
        </div>
    </div>

    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h1 class="modal-title fs-5" id="exampleModalLabel">Reset Password Request</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            A password reset link has been sent to you email. Please check you email. 
        </div>
        <div class="modal-footer">
            <a href="{{ route('login') }}">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button> </a>
        </div>
        </div>
    </div>
    </div>

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