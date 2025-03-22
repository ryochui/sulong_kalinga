<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="{{ asset('css/addUsers.css') }}">
</head>
<body>

    @include('components.userNavbar')
    @include('components.sidebar')
    
    <div class="home-section">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <a href="careManagerProfile" class="btn btn-secondary">
                    <i class="bx bx-arrow-back"></i> Back
                </a>
                <div class="mx-auto text-center" style="flex-grow: 1; font-weight: bold; font-size: 20px;">ADD CARE MANAGER</div>
            </div>
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="row" id="addUserForm">
                <div class="col-12">
                    <!-- <form action="{{ route('addBeneficiary') }}" method="POST"> -->
                    <form action="{{ route('admin.addCareManager.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf <!-- Include CSRF token for security -->
                        <!-- Row 1: Personal Details -->
                        <div class="row mb-1 mt-3">
                            <div class="col-12">
                                <h5 class="text-start">Personal Details</h5> <!-- Row Title -->
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-md-3">
                                <label for="firstName" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="firstName" name="first_name" placeholder="Enter first name" required oninput="validateName(this)" pattern="^[A-Z][a-zA-Z]*(?:-[a-zA-Z]+)?$" title="First letter must be uppercase, and only alphabets are allowed. Hyphen can only be used once per word and not at the end.">
                            </div>
                            <div class="col-md-3">
                                <label for="lastName" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="lastName" name="last_name" placeholder="Enter last name" required oninput="validateName(this)" pattern="^[A-Z][a-zA-Z]*(?:-[a-zA-Z]+)?$" title="First letter must be uppercase, and only alphabets are allowed. Hyphen can only be used once per word and not at the end.">
                            </div>
                            <div class="col-md-3">
                                <label for="birthDate" class="form-label">Birthday</label>
                                <input type="date" class="form-control" id="birthDate" name="birth_date" required onkeydown="return true">
                            </div>
                            <div class="col-md-3 position-relative">
                                <label for="gender" class="form-label">Gender</label>
                                <input type="text" class="form-control" id="genderInput" placeholder="Select gender" autocomplete="off" readonly>
                                <ul class="dropdown-menu w-100" id="genderDropdown">
                                    <li><a class="dropdown-item" data-value="Male">Male</a></li>
                                    <li><a class="dropdown-item" data-value="Female">Female</a></li>
                                    <li><a class="dropdown-item" data-value="Other">Other</a></li>
                                </ul>
                                <input type="hidden" id="gender" name="gender">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-3 position-relative">
                                <label for="civilStatus" class="form-label">Civil Status</label>
                                <input type="text" class="form-control" id="civilStatusInput" placeholder="Select civil status" autocomplete="off" readonly >
                                <ul class="dropdown-menu w-100" id="civilStatusDropdown">
                                    <li><a class="dropdown-item" data-value="Single">Single</a></li>
                                    <li><a class="dropdown-item" data-value="Married">Married</a></li>
                                    <li><a class="dropdown-item" data-value="Widowed">Widowed</a></li>
                                    <li><a class="dropdown-item" data-value="Divorced">Divorced</a></li>
                                </ul>
                                <input type="hidden" id="civilStatus" name="civil_status">
                            </div>
                            <div class="col-md-3">
                                <label for="religion" class="form-label">Religion</label>
                                <input type="text" class="form-control" id="religion" name="religion" placeholder="Enter religion" pattern="^[a-zA-Z\s]*$" title="Only alphabets and spaces are allowed.">
                            </div>
                            <div class="col-md-3">
                                <label for="nationality" class="form-label">Nationality</label>
                                <input type="text" class="form-control" id="nationality" name="nationality" placeholder="Enter nationality" required pattern="^[a-zA-Z\s]*$" title="Only alphabets and spaces are allowed.">
                            </div>
                            <div class="col-md-3 position-relative">
                                <label for="educationalBackground" class="form-label">Educational Background</label>
                                <input type="text" class="form-control" id="educationalBackgroundInput" placeholder="Select Educational Background" autocomplete="off" readonly>
                                <ul class="dropdown-menu w-100" id="educationalBackgroundDropdown">
                                    <li><a class="dropdown-item" data-value="College">College</a></li>
                                    <li><a class="dropdown-item" data-value="Highschool">High School</a></li>
                                    <li><a class="dropdown-item" data-value="Doctorate">Doctorate</a></li>
                                </ul>
                                <input type="hidden" id="educationalBackground" name="educational_background">
                            </div>
                        </div>

                        <hr class="my-4">
                        <!-- Row 2: Address -->
                        <div class="row mb-1">
                            <div class="col-12">
                                <h5 class="text-start">Current Address</h5> <!-- Row Title -->
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="addressDetails" class="form-label">House No., Street, Subdivision, Barangay, City, Province</label>
                                <textarea class="form-control" id="addressDetails" name="address_details" 
                                placeholder="Enter complete current address" 
                                rows="2" 
                                required 
                                pattern="^[a-zA-Z0-9\s,.-]+$" 
                                title="Only alphanumeric characters, spaces, commas, periods, and hyphens are allowed."
                                oninput="validateAddress(this)"></textarea>
                            </div>
                        </div>

                        <hr class="my-4">
                        <!-- Contact Information -->
                        <div class="row mb-1">
                            <div class="col-12">
                                <h5 class="text-start">Contact Information</h5> <!-- Row Title -->
                            </div>
                        </div> 
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="personalEmail" class="form-label">Personal Email Address</label>
                                <input type="email" class="form-control" id="personalEmail" name="personal_email" 
                                       placeholder="Enter personal email" 
                                       required 
                                       pattern="^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$" 
                                       title="Enter a valid email address (e.g., example@domain.com)" 
                                       oninput="validateEmail(this)">
                            </div>
                            <div class="col-md-4">
                                <label for="mobileNumber" class="form-label">Mobile Number</label>
                                <div class="input-group">
                                    <span class="input-group-text">+63</span>
                                    <input type="text" class="form-control" id="mobileNumber" name="mobile_number" placeholder="Enter mobile number" maxlength="11" required oninput="restrictToNumbers(this)" title="Must be 10 or 11digits.">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="landlineNumber" class="form-label">Landline Number</label>
                                <input type="text" class="form-control" id="landlineNumber" name="landline_number" placeholder="Enter Landline number" maxlength="10" required oninput="restrictToNumbers(this)" title="Must be between 7 and 10 digits.">
                            </div>
                        </div>

                        <hr class="my-4">
                        <!-- Documents -->
                        <div class="row mb-1">
                            <div class="col-12">
                                <h5 class="text-start">Documents Upload</h5> <!-- Row Title -->
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-md-4">
                                <label for="caremanagerPhoto" class="form-label">Care Manager Photo</label>
                                <input type="file" class="form-control" id="caremanagerPhoto" name="caremanager_photo" accept="image/png, image/jpeg" capture="user" required>
                            </div>
                            <div class="col-md-4">
                                <label for="governmentID" class="form-label">Government Issued ID</label>
                                <input type="file" class="form-control" id="governmentID" name="government_ID" accept=".jpg,.png" required>
                            </div>
                            <div class="col-md-4">
                                <label for="resume" class="form-label">Resume / CV</label>
                                <input type="file" class="form-control" id="resume" name="resume" accept=".pdf,.doc,.docx" required>
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-md-4">
                                <label for="sssID" class="form-label">SSS ID</label>
                                <input type="text" class="form-control" id="sssID" name="sss_ID" placeholder="Enter SSS ID" maxlength="10" required oninput="restrictToNumbers(this)" title="Must be 10 digits.">
                            </div>
                            <div class="col-md-4">
                                <label for="philhealthID" class="form-label">PhilHealth ID</label>
                                <input type="text" class="form-control" id="philhealthID" name="philhealth_ID" placeholder="Enter PhilHealth ID" maxlength="12" required oninput="restrictToNumbers(this)" title="Must be 12 digits.">
                            </div>
                            <div class="col-md-4">
                                <label for="pagibigID" class="form-label">Pag-Ibig ID</label>
                                <input type="text" class="form-control" id="pagibigID" name="pagibig_ID" placeholder="Enter Pag-Ibig ID" maxlength="12" required oninput="restrictToNumbers(this)" title="Must be 12 digits.">
                            </div>
                        </div>


                        <hr class="my-4">
                        <!-- Account Registration -->
                        <div class="row mb-1">
                            <div class="col-12">
                                <h5 class="text-start">Care Manager Account Registration</h5> <!-- Row Title -->
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-md-4">
                                <label for="email" class="form-label">Work Email Address</label>
                                <input type="email" class="form-control" id="email" name="account[email]" 
                                       placeholder="Enter work email" 
                                       required 
                                       pattern="^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$" 
                                       title="Enter a valid email address (e.g., example@domain.com)" 
                                       oninput="validateEmail(this)">
                            </div>
                            <div class="col-md-4">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="account[password]" placeholder="Enter password" required>
                            </div>
                            <div class="col-md-4">
                                <label for="confirmPassword" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="confirmPassword" name="account[password_confirmation]" placeholder="Confirm password" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 position-relative">
                                <label for="municipality" class="form-label">Municipality</label>
                                <input type="text" class="form-control" id="municipalityInput" placeholder="Select municipality" autocomplete="off" readonly>
                                <ul class="dropdown-menu w-100" id="municipalityDropdown">
                                    <li><a class="dropdown-item" data-value="1">Mondragon</a></li>
                                    <li><a class="dropdown-item" data-value="2">San Roque</a></li>
                                </ul>
                                <input type="hidden" id="municipality" name="municipality">
                            </div>
                        </div>                        
                        <div class="row mt-4">
                            <div class="col-12 d-flex justify-content-center align-items-center">
                                <button type="submit" class="btn btn-success btn-lg d-flex align-items-center">
                                    <i class='bx bx-save me-2' style="font-size: 24px;"></i>
                                    Save Care Manager
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Save Beneficiary Success Modal -->
    <div class="modal fade" id="saveSuccessModal" tabindex="-1" aria-labelledby="saveSuccessModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="saveSuccessModalLabel">Success</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <p>Care Manager has been successfully saved!</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>
    <script src=" {{ asset('js/toggleSideBar.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script>
        document.querySelector('form').addEventListener('submit', function (e) {
            e.preventDefault(); // Prevent the default form submission

            // e.preventDefault(); // Prevent the default form submission

            // Show the success modal
            const successModal = new bootstrap.Modal(document.getElementById('saveSuccessModal'));
            successModal.show();
        });
    </script>
<!-- <script>
        document.querySelector('form').addEventListener('submit', function (e) {
            e.preventDefault(); // Prevent the default form submission

            // Show the success modal
            const successModal = new bootstrap.Modal(document.getElementById('saveSuccessModal'));
            successModal.show();
        });
    </script> -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Function to filter dropdown items
            function filterDropdown(inputId, dropdownId) {
                const input = document.getElementById(inputId);
                const dropdown = document.getElementById(dropdownId);
                const items = dropdown.querySelectorAll('.dropdown-item');

                input.addEventListener('input', function () {
                    const filter = input.value.toLowerCase();
                    let hasVisibleItems = false;

                    items.forEach(item => {
                        if (item.textContent.toLowerCase().includes(filter)) {
                            item.style.display = 'block';
                            hasVisibleItems = true;
                        } else {
                            item.style.display = 'none';
                        }
                    });
                    dropdown.style.display = hasVisibleItems ? 'block' : 'none';
                });
                input.addEventListener('blur', function () {
                    setTimeout(() => dropdown.style.display = 'none', 200);
                });
                input.addEventListener('focus', function () {
                    dropdown.style.display = 'block';
                });

                // Handle item selection
                items.forEach(item => {
                    item.addEventListener('click', function (e) {
                        e.preventDefault();
                        input.value = item.textContent;
                        document.getElementById(inputId.replace('Input', '')).value = item.getAttribute('data-value');
                        dropdown.style.display = 'none';
                    });
                });
            }

            // Initialize filtering for each dropdown
            filterDropdown('civilStatusInput', 'civilStatusDropdown');
            filterDropdown('genderInput', 'genderDropdown');
            filterDropdown('educationalBackgroundInput', 'educationalBackgroundDropdown');
            // filterDropdown('Organization_RolesInput', 'Organization_RolesDropdown');
            filterDropdown('municipalityInput', 'municipalityDropdown');
        });

        function validateMobileNumber(input) {
            // Remove any non-numeric characters
            input.value = input.value.replace(/[^0-9]/g, '');

            // Limit the input to 11 digits
            if (input.value.length > 11) {
                input.value = input.value.slice(0, 11);
            }
        }

        document.querySelectorAll('.dropdown-item').forEach(item => {
            item.addEventListener('click', function (e) {
                e.preventDefault();
                const input = this.closest('.position-relative').querySelector('input[type="text"]');
                const hiddenInput = this.closest('.position-relative').querySelector('input[type="hidden"]');
                input.value = this.textContent;
                hiddenInput.value = this.getAttribute('data-value');
            });
        });

        document.querySelectorAll('input[type="text"]').forEach(input => {
            input.addEventListener('input', function () {
                this.value = this.value.replace(/[^a-zA-Z0-9\s]/g, ''); // Remove special characters
            });
        });

        document.querySelectorAll('input[type="email"]').forEach(input => {
            input.addEventListener('input', function () {
                // Allow only characters that match the regex
                this.value = this.value.replace(/[^a-zA-Z0-9._%+-@]/g, '');
            });
        });

        function restrictToNumbers(input) {
            input.value = input.value.replace(/[^0-9]/g, '');
        }

        // Restrict input to numbers only
        function restrictToNumbers(input) {
            input.value = input.value.replace(/[^0-9]/g, ''); // Remove non-numeric characters
        }

        // Validate names to allow only one hyphen per word and not at the end
        function validateName(input) {
            // NOT WORKING AS INTENDED
            // input.value = input.value.replace(/[^a-zA-Z-]/g, ''); // Remove invalid characters
            // input.value = input.value.replace(/-{2,}/g, '-'); // Prevent multiple consecutive hyphens
            // input.value = input.value.replace(/^-|-$/g, ''); // Remove hyphen at the start or end
            // const words = input.value.split(' ');
            // input.value = words.map(word => word.replace(/-/g, (match, offset) => offset === word.indexOf('-') ? '-' : '')).join(' ');
        }

        // Prevent spaces in email fields
        function preventSpaces(input) {
            input.value = input.value.replace(/\s/g, ''); // Remove spaces
        }

        // Validate Current Address to allow only alphanumeric characters, spaces, commas, periods, and hyphens
        function validateAddress(input) {
            input.value = input.value.replace(/[^a-zA-Z0-9\s,.-]/g, ''); // Remove invalid characters
        }

        // Validate Email to allow only characters matching the regex
        function validateEmail(input) {
            input.value = input.value.replace(/[^a-zA-Z0-9._%+-@]/g, ''); // Remove invalid characters
        }
        // document.addEventListener('DOMContentLoaded', function () {
        //     const municipalityInput = document.getElementById('municipalityInput');
        //     const municipalityHiddenInput = document.getElementById('municipality');
        //     const municipalityDropdown = document.getElementById('municipalityDropdown');

        //     // Add click event listeners to dropdown items
        //     municipalityDropdown.querySelectorAll('.dropdown-item').forEach(item => {
        //         item.addEventListener('click', function (e) {
        //             e.preventDefault();
        //             municipalityInput.value = this.textContent; // Set the visible input value
        //             municipalityHiddenInput.value = this.getAttribute('data-value'); // Set the hidden input value
        //         });
        //     });
        // }); FOR MUNICIPALITY DROPDOWN IF DYNAMIC WHEN FIXED
        document.addEventListener('DOMContentLoaded', function () {
            const municipalityInput = document.getElementById('municipalityInput');
            const municipalityHiddenInput = document.getElementById('municipality');
            const municipalityDropdown = document.getElementById('municipalityDropdown');

            // Add click event listeners to dropdown items
            municipalityDropdown.querySelectorAll('.dropdown-item').forEach(item => {
                item.addEventListener('click', function (e) {
                    e.preventDefault();
                    municipalityInput.value = this.textContent; // Set the visible input value
                    municipalityHiddenInput.value = this.getAttribute('data-value'); // Set the hidden input value
                });
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const birthDateInput = document.getElementById('birthDate');

            // Calculate the maximum allowable date (14 years ago from today)
            const today = new Date();
            const maxDate = new Date(today.getFullYear() - 14, today.getMonth(), today.getDate());
            const formattedMaxDate = maxDate.toISOString().split('T')[0]; // Format as YYYY-MM-DD

            // Set the max attribute for the birth_date input
            birthDateInput.setAttribute('max', formattedMaxDate);
        });
    </script>
    <?php
    // if ($request->hasFile('caremanager_photo')) {
    //     dd($request->file('caremanager_photo')->getClientMimeType());
    // }
    ?>
</body>
</html>