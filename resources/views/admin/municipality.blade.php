<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Municipality Management</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="{{ asset('css/municipality.css') }}">
</head>
<body>
    @include('components.userNavbar')
    @include('components.sidebar')
    @include('components.modals.confirmDelete')
    
    <div class="home-section">
        <div class="text-left">MUNICIPALITY</div>
        <div class="container-fluid text-center">
            <div class="row mb-3 align-items-center">
                <!-- Search Bar -->
                <div class="col-12 col-md-6 col-lg-5 mb-2">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bx bx-search-alt"></i>
                        </span>
                        <input type="text" class="form-control" placeholder="Search barangay..." id="searchBar">
                    </div>
                </div>

                <!-- Filter Dropdown -->
                <div class="col-12 col-md-6 col-lg-4 mb-2">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bx bx-filter-alt"></i>
                        </span>
                        <select class="form-select" id="filterDropdown">
                            <option value="">All Municipalities</option>
                            @foreach($municipalities as $municipality)
                                <option value="{{ $municipality->municipality_id }}">{{ $municipality->municipality_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Add Municipality Button -->
                <div class="col-12 col-md-6 col-lg-3 mb-2">
                    <button class="btn btn-primary w-100" id="addButton" data-bs-toggle="modal" data-bs-target="#">
                        <i class="bx bx-plus"></i> Add Municipality
                    </button>
                </div>
            </div>

            <div class="row" id="recentReports">
                <div class="col-12">
                    <div class="table-responsive">
                        <table class="table table-striped w-100 align-middle">
                            <thead>
                                <tr>
                                    <th scope="col">Municipality</th>
                                    <th scope="col">Barangay</th>
                                    <th scope="col">Beneficiaries</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($barangays as $barangay)
                                    <tr class="municipality-row" data-municipality="{{ $barangay->municipality->municipality_id }}">
                                        <td>{{ $barangay->municipality->municipality_name }}</td>
                                        <td>{{ $barangay->barangay_name }}</td>
                                        <td>{{ $barangay->beneficiaries_count }}</td>
                                        <td>
                                            <div class="action-icons">
                                            <i class='bx bx-trash' data-bs-toggle="modal" data-bs-target="#deleteModal" 
                                                data-id="{{ $barangay->barangay_id }}" 
                                                data-name="{{ $barangay->barangay_name }}"
                                                onclick="setDeleteTarget(this, '{{ route('admin.deleteBarangay', $barangay->barangay_id) }}')"></i>
                                                <i class='bx bxs-edit' data-bs-toggle="modal" data-bs-target="#"
                                                    data-id="{{ $barangay->barangay_id }}"
                                                    data-name="{{ $barangay->barangay_name }}"
                                                    data-municipality="{{ $barangay->municipality_id }}"
                                                    onclick="prepareEdit(this)"></i>
                                            </div>          
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No barangays found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Add Municipality Modal --}}
    {{-- @include('components.modals.addMunicipality') --}}
    
    {{-- Edit Barangay Modal --}}
    {{-- @include('components.modals.editBarangay') --}}

    <script src="{{ asset('js/toggleSideBar.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/deleteModal.js') }}"></script>
    
    <script>
        // Search functionality
        document.getElementById('searchBar').addEventListener('input', function() {
            const searchText = this.value.toLowerCase();
            const rows = document.querySelectorAll('.municipality-row');
            
            rows.forEach(row => {
                const barangayName = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                const municipalityName = row.querySelector('td:first-child').textContent.toLowerCase();
                
                if (barangayName.includes(searchText) || municipalityName.includes(searchText)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
        
        // Filter functionality
        document.getElementById('filterDropdown').addEventListener('change', function() {
            const municipalityId = this.value;
            const rows = document.querySelectorAll('.municipality-row');
            
            if (!municipalityId) {
                // Show all rows if "All Municipalities" is selected
                rows.forEach(row => {
                    row.style.display = '';
                });
                return;
            }
            
            rows.forEach(row => {
                if (row.dataset.municipality === municipalityId) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
        
        // Set up delete target
        function setDeleteTarget(element) {
            const id = element.dataset.id;
            const name = element.dataset.name;
            
            document.getElementById('deleteForm').action = `/admin/delete-barangay/${id}`;
            document.getElementById('deleteItemName').textContent = name;
        }
        
        // Prepare edit modal
        function prepareEdit(element) {
            const id = element.dataset.id;
            const name = element.dataset.name;
            const municipalityId = element.dataset.municipality;
            
            document.getElementById('editBarangayId').value = id;
            document.getElementById('editBarangayName').value = name;
            document.getElementById('editMunicipalityId').value = municipalityId;
        }
    </script>
</body>
</html>