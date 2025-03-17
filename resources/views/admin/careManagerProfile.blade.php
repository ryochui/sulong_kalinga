<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="{{ asset('css/reportsManagement.css') }}">
</head>
<body>

    @include('components.userNavbar')
    @include('components.sidebar')
    @include('components.modals.statusChange')
    
    <div class="home-section">
        <div class="text-left">CARE MANAGER PROFILES</div>
        <div class="container-fluid text-center">
            <div class="row mb-3 align-items-center">
                <!-- Search Bar -->
                <div class="col-12 col-md-6 col-lg-6 mb-2">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bx bx-search-alt"></i>
                        </span>
                        <input type="text" class="form-control" placeholder="Search reports..." id="searchBar">
                    </div>
                </div>
                <!-- Filter Dropdown -->
                <div class="col-12 col-sm-6 col-md-6 col-lg-2 mb-2">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bx bx-filter-alt"></i>
                        </span>
                        <select class="form-select" id="filterDropdown">
                            <option value="" selected>Filter by</option>
                            <option value="author">Author</option>
                            <option value="type">Report Type</option>
                            <option value="date">Date Uploaded</option>
                        </select>
                    </div>
                </div>
                <!-- Export Dropdown -->
                <div class="col-6 col-md-3 col-lg-2 mb-2">
                    <div class="dropdown">
                        <button class="btn btn-secondary dropdown-toggle w-100" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bx bx-export"></i> Export
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="exportDropdown">
                            <li><a class="dropdown-item" href="#">Export as PDF</a></li>
                            <li><a class="dropdown-item" href="#">Export as Excel</a></li>
                        </ul>
                    </div>
                </div>
                <!-- Add Report Button -->
                <div class="col-6 col-md-3 col-lg-2 mb-2">
                    <button class="btn btn-primary w-100" id="addButton">
                        <i class="bx bx-plus"></i> Add Manager
                    </button>
                </div>
            </div>


            <div class="row" id="recentReports">
                <div class="col-12">
                    <div class="table-responsive">
                        <table class="table table-striped w-100 align-middle">
                            <thead>
                                <tr>
                                    <th scope="col">
                                        <input type="checkbox" id="selectAll" /> <!-- Checkbox to select all rows -->
                                    </th>   
                                    <th scope="col">Fullname</th>
                                    <th scope="col">Municipality</th>
                                    <th scope="col">Barangay</th>
                                    <th scope="col">Mobile Number</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @for ($i = 1; $i <= 33; $i++)
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="rowCheckbox" />
                                        </td>
                                        <td>Mark</td>
                                        <td>Otto</td>
                                        <td>@mdo</td>
                                        <td>Otto</td>
                                        <td>
                                        <select class="form-select" name="status" id="statusSelect{{ $i }}" onchange="openStatusChangeModal(this, 'Care Manager')">
                                            <option value="active" selected>Active</option>
                                            <option value="inactive">Inactive</option>
                                        </select>
                                        </td>
                                        <td>
                                            <div class="action-icons">
                                                <i class="fa fa-eye"></i>
                                                <i class='bx bxs-edit'></i>
                                            </div>
                                        </td>
                                    </tr>
                                @endfor
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src=" {{ asset('js/toggleSideBar.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/forCheckbox.js') }}"></script>
</body>
</html>