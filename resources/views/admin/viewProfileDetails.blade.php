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
                <a href="" class="btn btn-secondary">
                    <i class="bx bx-arrow-back"></i> Back
                </a>
                <div class="mx-auto text-center" style="flex-grow: 1; font-weight: bold; font-size: 20px;">VIEW PROFILE DETAILS</div>
                <div>
                    <button class="btn btn-primary me-2" onclick="editProfile()">
                        <i class="bx bxs-edit"></i> Edit
                    </button>
                    <button class="btn btn-danger" onclick="deleteProfile()">
                        <i class="bx bxs-trash"></i> Delete
                    </button>
                </div>
            </div>
            <div class="container text-center">
                <div class="row" id="profileDetails">
                    <div class="col-12">
                        
                        <div class="row">
                            <div class="col">
                            Column
                            </div>
                            <div class="col">
                            Column
                            </div>
                            <div class="col">
                            Column
                            </div>
                        </div>
                        </div>
                </div>
            </div>
        </div>
    </div>

    <script src=" {{ asset('js/toggleSideBar.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <!-- <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Function to filter dropdown items
        function filterDropdown(inputId, dropdownId) {
            const input = document.getElementById(inputId);
            const dropdown = document.getElementById(dropdownId);
            const items = dropdown.querySelectorAll('.dropdown-item');

            // Filter dropdown items based on user input
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

            // Hide dropdown when input loses focus
            input.addEventListener('blur', function () {
                setTimeout(() => dropdown.style.display = 'none', 200);
            });

            // Show dropdown when input gains focus
            input.addEventListener('focus', function () {
                dropdown.style.display = 'block';
            });

            // Handle item selection
            items.forEach(item => {
                item.addEventListener('click', function (e) {
                    e.preventDefault();
                    input.value = item.textContent; // Update the input field with the selected item's text
                    const hiddenInputId = inputId.replace('Input', ''); // Derive the hidden input ID
                    const hiddenInput = document.getElementById(hiddenInputId);
                    if (hiddenInput) {
                        hiddenInput.value = item.getAttribute('data-value'); // Update the hidden input with the selected item's value
                    }
                    dropdown.style.display = 'none'; // Hide the dropdown
                });
            });
        }

        // Initialize filtering for each dropdown
        filterDropdown('genderInput', 'genderDropdown');
        filterDropdown('relatedBeneficiaryInput', 'relatedBeneficiaryDropdown');
    });
</script> -->

</body>
</html>