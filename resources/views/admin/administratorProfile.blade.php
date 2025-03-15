<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="{{ asset('css/reportsManagement.css') }}">
</head>
<body>

    @include('components.navbar')
    @include('components.sidebar')
    
    <div class="home-section">
        <div class="text-left">ADMINISTRATOR PROFILES</div>
        <div class="container-fluid text-center">
        <div class="row mb-3">
                <div class="col-md-7">
                    <!-- Search Bar -->
                    <div class="input-group">
                    <span class="input-group-text">
                        <i class="bx bx-search-alt"></i>
                    </span>
                    <input type="text" class="form-control" placeholder="Search Administrator profile..." id="searchBar">
                    </div>
                </div>
            
                <div class="col-md-5 text-end">
                    <div class="d-inline-block me-2">
                        <!-- Filter Dropdown -->
                        <div class="input-group">
                        <span class="input-group-text">
                            <i class="bx bx-filter-alt"></i>
                        </span>
                        <select class="form-select" id="filterDropdown">
                            <option value="" selected>Filter by</option>
                            <option value="role">Organization Role</option>
                            <option value="alphabetically">Alphabetically</option>
                        </select>
                        </div>
                    </div>
                    <div class="d-inline-block" style="transform: translateY(-3px);">
                        <!-- Add Report Button -->
                        <button class="btn btn-primary" id="addReportButton">
                            <i class="bx bx-plus"></i> Add Administrator
                        </button>
                    </div>
                </div>
            </div>


            <div class="row" id="recentReports">
                <div class="col-12">
                    <div class="table-responsive">
                        <table class="table table-striped w-100 align-middle">
                            <thead>
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">Fullname</th>
                                    <th scope="col">Organization Role</th>
                                    <th scope="col">Mobile Number</th>
                                    <th scope="col">Email Address</Address></th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @for ($i = 1; $i <= 33; $i++)
                                    <tr>
                                        <th scope="row">{{ $i }}</th>
                                        <td>Mark</td>
                                        <td>Otto</td>
                                        <td>@mdo</td>
                                        <td>Otto</td>
                                        <td>
                                        <select class="form-select" name="status" id="statusSelect{{ $i }}">
                                            <option value="active" selected>Active</option>
                                            <option value="inactive">Inactive</option>
                                            <option value="pending">Pending</option>
                                        </select>
                                        </td>
                                        <td>
                                            <div class="action-icons">
                                                <i class='bx bx-trash'></i>
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
</body>
</html>