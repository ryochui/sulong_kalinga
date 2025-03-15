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
        <div class="text-left">REPORTS MANAGEMENT</div>
        <div class="container-fluid text-center">
            <div class="row mb-3">
                <div class="col-md-6">
                    <!-- Search Bar -->
                    <div class="input-group">
                    <span class="input-group-text">
                        <i class="bx bx-search-alt"></i>
                    </span>
                    <input type="text" class="form-control" placeholder="Search reports..." id="searchBar">
                    </div>
                </div>
            
                <div class="col-md-6 text-end">
                    <div class="d-inline-block me-2">
                        <!-- Filter Dropdown -->
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
                    <div class="d-inline-block me-2" style="transform: translateY(-3px);">
                        <!-- Export Dropdown -->
                        <div class="dropdown">
                            <button class="btn btn-secondary dropdown-toggle" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bx bx-export"></i> Export
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="exportDropdown">
                                <li><a class="dropdown-item" href="#">Export as PDF</a></li>
                                <li><a class="dropdown-item" href="#">Export as Excel</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="d-inline-block" style="transform: translateY(-3px);">
                        <!-- Add Report Button -->
                        <button class="btn btn-primary" id="addReportButton">
                            <i class="bx bx-plus"></i> Add Report
                        </button>
                    </div>
                </div>
            </div>
            <div class="row" id="recentReports">
                <div class="col-12">
                    <div class="table-responsive">
                        <table class="table table-striped w-100">
                            <thead>
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">Author</th>
                                    <th scope="col">Report Type</th>
                                    <th scope="col">Date Uploaded</th>
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