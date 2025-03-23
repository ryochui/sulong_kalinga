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
    @include('components.modals.statusChangeAdmin')
    @php use App\Helpers\StringHelper;
    @endphp
   
    <div class="home-section">
        <div class="text-left">ADMINISTRATOR PROFILES</div>
        <div class="container-fluid text-center">
            <div class="row mb-3 align-items-center">
                
                <!-- Search Bar -->
                <div class="col-12 col-md-6 col-lg-6 mb-2">
                    <form action="{{ route('admin.administratorProfile') }}" method="GET">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bx bx-search-alt"></i>
                            </span>
                            <input type="text" class="form-control" name="search" placeholder="Enter administrator name..." id="searchBar" value="{{ request('search') }}">
                            <button type="submit" class="btn btn-primary">Search</button>
                        </div>
                    </form>
                </div>

                <!-- Filter Dropdown -->
                <div class="col-12 col-sm-6 col-md-6 col-lg-2 mb-2">
                    <form action="{{ route('admin.administratorProfile') }}" method="GET" id="filterForm">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bx bx-filter-alt"></i>
                            </span>
                            <select class="form-select" name="filter" id="filterDropdown" onchange="document.getElementById('filterForm').submit()">
                                <option value="" {{ request('filter') ? '' : 'selected' }}>Filter by</option>
                                <option value="status" {{ request('filter') == 'status' ? 'selected' : '' }}>Status</option>
                                <option value="organizationrole" {{ request('filter') == 'organizationrole' ? 'selected' : '' }}>Organization Role</option>
                                <option value="area" {{ request('filter') == 'area' ? 'selected' : '' }}>Area</option>
                            </select>
                        </div>
                    </form>
                </div>

                <!-- Export Dropdown -->
                <div class="col-6 col-md-3 col-lg-2 mb-2">
                    <div class="dropdown-center">
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
                    <a href="{{ route('admin.addAdministrator') }}">
                    <button class="btn btn-primary w-100" id="addButton">
                        <i class="bx bx-plus"></i> Add Admin
                    </button>
                    </a>
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
                                    <th scope="col" class="d-none d-sm-table-cell">Organization Role</th>
                                    <th scope="col" class="d-none d-sm-table-cell">Area</th>
                                    <th scope="col" class="d-none d-sm-table-cell">Mobile Number</th>
                                    <th scope="col" class="d-none d-md-table-cell">Email Address</Address></th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($administrators as $administrator)
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="rowCheckbox" />
                                        </td>
                                        <td>{{ $administrator->first_name }} {{ $administrator->last_name }}</td>
                                        <td>{{ ucwords(str_replace('_', ' ', $administrator->organizationRole->role_name ?? 'N/A')) }}</td>
                                        <td>{{ StringHelper::formatArea($administrator->organizationRole->area ?? 'N/A') }}</td>
                                        <td>{{ $administrator->mobile}}</td>
                                        <td>{{ $administrator->email}}</td>
                                        <td>
                                            <div class="position-relative" 
                                                {{ isset($administrator->organizationRole) && $administrator->organizationRole->role_name == 'executive_director' ? 'data-bs-toggle="tooltip" data-bs-placement="top" title="Executive Director status cannot be changed."' : '' }}>
                                                @if(isset($administrator->organizationRole) && $administrator->organizationRole->role_name == 'executive_director')
                                                    <!-- For executive directors, use a static badge instead of a dropdown -->
                                                    <span class="badge bg-primary">Active</span>
                                                    <!-- Hidden select just to maintain data consistency if needed -->
                                                    <select class="form-select d-none" name="status" id="statusSelect{{ $administrator->id }}" disabled>
                                                        <option value="Active" selected>Active</option>
                                                    </select>
                                                @else
                                                    <!-- For non-executive directors, use the normal dropdown -->
                                                    <select class="form-select" name="status" id="statusSelect{{ $administrator->id }}" 
                                                        onchange="openStatusChangeAdminModal(this, 'Administrator', {{ $administrator->id }}, '{{ $administrator->volunteer_status }}')">
                                                        <option value="Active" {{ $administrator->volunteer_status == 'Active' ? 'selected' : '' }}>Active</option>
                                                        <option value="Inactive" {{ $administrator->volunteer_status == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                                    </select>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="action-icons">
                                            <form action="{{ route('viewAdminDetails') }}" method="POST" style="display:inline;">
                                                @csrf
                                                <input type="hidden" name="administrator_id" value="{{ $administrator->id }}">
                                                <button type="submit" class="btn btn-link text-decoration-none" style="color:black;">
                                                    <i class="fa fa-eye"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('editAdminProfile') }}" method="POST" style="display:inline;">
                                                @csrf
                                                <input type="hidden" name="administrator_id" value="{{ $administrator->id }}">
                                                <button type="submit" class="btn btn-link text-decoration-none" style="color:black;">
                                                    <i class='bx bxs-edit'></i>
                                                </button>
                                            </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
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