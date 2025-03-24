<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports Management</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="{{ asset('css/weeklyCareplan.css') }}">

    <style>
    .breadcrumb-container {
        overflow-x: auto;
        white-space: nowrap;
        scrollbar-width: thin;
        -ms-overflow-style: none;
        margin-bottom: 20px;
        padding-bottom: 5px;
    }
    
    .breadcrumb-container::-webkit-scrollbar {
        height: 5px;
    }
    
    .breadcrumb-container::-webkit-scrollbar-thumb {
        background-color: rgba(0,0,0,0.2);
        border-radius: 5px;
    }
    
    .breadcrumb {
        flex-wrap: nowrap;
        margin-bottom: 0;
    }
    
    .breadcrumb-item {
        float: none;
        display: inline-block;
    }
    
    .breadcrumb-item.active a {
        font-weight: bold;
        color: #0d6efd;
        padding: 5px 10px;
        background-color: rgba(13, 110, 253, 0.1);
        border-radius: 5px;
    }
    
    .form-page {
        display: none;
    }
    
    .form-page.active {
        display: block;
    }
</style>
</head>
<body>

    @include('components.userNavbar')
    @include('components.sidebar')
    
    <div class="home-section">
        <h4 class="text-center mt-2">WEEKLY CARE PLAN FORM</h4>
        
        <!-- Error and Success Messages Display -->
        @if ($errors->any())
        <div class="alert alert-danger">
            <h5>Please fix the following errors:</h5>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
        @endif
        
            <div class="container-fluid">
                <div class="row mb-1" id="weeklyCareplanForm">
                    <div class="col-12">
                        <div class="row">
                            <div class="col-12">
                                <!-- Breadcrumb Navigation -->
                                <div class="breadcrumb-container">
                                    <nav aria-label="breadcrumb">
                                        <ol class="breadcrumb">
                                            <li class="breadcrumb-item active" data-page="1"><a href="javascript:void(0)">Personal Details</a></li>
                                            <li class="breadcrumb-item" data-page="2"><a href="javascript:void(0)">Mobility</a></li>
                                            <li class="breadcrumb-item" data-page="3"><a href="javascript:void(0)">Cognitive/Communication</a></li>
                                            <li class="breadcrumb-item" data-page="4"><a href="javascript:void(0)">Self-Sustainability</a></li>
                                            <li class="breadcrumb-item" data-page="5"><a href="javascript:void(0)">Disease/Therapy</a></li>
                                            <li class="breadcrumb-item" data-page="6"><a href="javascript:void(0)">Social Contact</a></li>
                                            <li class="breadcrumb-item" data-page="7"><a href="javascript:void(0)">Outdoor Activities</a></li>
                                            <li class="breadcrumb-item" data-page="8"><a href="javascript:void(0)">Household Keeping</a></li>
                                            <li class="breadcrumb-item" data-page="9"><a href="javascript:void(0)">Evaluation</a></li>
                                        </ol>
                                    </nav>
                                </div>
                            </div>
                        </div>
                        
                        <form id="weeklyCarePlanForm" action="{{ route('weeklycareplans.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-12">
                                    <!-- Multi-Paged Form -->
                                    <form id="multiPageForm">
                                        <!-- Page 1: Personal Details and Assessment, Vital Signs -->
                                        <div class="form-page active" id="page1">
                                            <div class="row mb-1">
                                                <div class="col-12">
                                                    <h5>Personal Details</h5>
                                                </div>
                                            </div>
                                            <div class="row mb-1">
                                                <div class="col-md-4 col-sm-9 position-relative">
                                                    <label for="beneficiary_id" class="form-label">Select Beneficiary</label>
                                                    <select class="form-select" id="beneficiary_id" name="beneficiary_id" required>
                                                            <option value="">-- Select Beneficiary --</option>
                                                            @foreach($beneficiaries as $beneficiary)
                                                                <option value="{{ $beneficiary->beneficiary_id }}">
                                                                    {{ $beneficiary->last_name }}, {{ $beneficiary->first_name }} {{ $beneficiary->middle_name }}
                                                                </option>
                                                            @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-2 col-sm-3">
                                                    <label for="age" class="form-label">Age</label>
                                                    <input type="text" class="form-control" id="age" readonly data-bs-toggle="tooltip" title="Edit in General Care Plan">                                            </div>
                                                <div class="col-md-3 col-sm-6">
                                                    <label for="birthDate" class="form-label">Birthdate</label>
                                                    <input type="date" class="form-control" id="birthDate" readonly data-bs-toggle="tooltip" title="Edit in General Care Plan">                                            </div>
                                                <div class="col-md-3 col-sm-6 position-relative">
                                                    <label for="gender" class="form-label">Gender</label>
                                                    <input type="text" class="form-control" id="gender" readonly data-bs-toggle="tooltip" title="Edit in General Care Plan">
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-md-3 col-sm-4 position-relative">
                                                    <label for="civilStatus" class="form-label">Civil Status</label>
                                                    <input type="text" class="form-control" id="civilStatus" readonly data-bs-toggle="tooltip" title="Edit in General Care Plan">
                                                    </div>
                                                <div class="col-md-9 col-sm-8">
                                                    <label for="address" class="form-label">Address</label>
                                                    <input type="text" class="form-control" id="address" readonly data-bs-toggle="tooltip" title="Edit in General Care Plan">
                                                    </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-md-6 col-sm-6">
                                                    <label for="condition" class="form-label">Medical Conditions</label>
                                                    <input type="text" class="form-control" id="medicalConditions" readonly data-bs-toggle="tooltip" title="Edit in General Care Plan">
                                                    </div>
                                            </div>
                                            <hr my-4>
                                            <!-- Assessment, Vital Signs -->
                                            <div class="row mb-3 mt-2">
                                                <div class="col-lg-6 col-md-6 col-sm-12 text-center">
                                                    <label for="assessment" class="form-label"><h5>Assessment</h5></label>
                                                    <textarea class="form-control @error('assessment') is-invalid @enderror" 
                                                            id="assessment" name="assessment" rows="5" required>{{ old('assessment') }}</textarea>
                                                    @error('assessment')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-12">
                                                    <h5>Vital Signs</h5>
                                                    <div class="row">
                                                        <div class="col-md-6 col-sm-6">
                                                            <label for="blood_pressure" class="form-label">Blood Pressure (mmHg)</label>
                                                            <input type="text" class="form-control @error('blood_pressure') is-invalid @enderror" 
                                                                id="blood_pressure" name="blood_pressure" required 
                                                                placeholder="e.g. 120/80" value="{{ old('blood_pressure') }}"
                                                                pattern="^\d{2,3}\/\d{2,3}$"
                                                                title="Format: systolic/diastolic (e.g. 120/80)">
                                                            <small class="form-text text-muted">Format: systolic/diastolic (e.g. 120/80)</small>
                                                            @error('blood_pressure')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="col-md-6 col-sm-6 position-relative">
                                                            <label for="body_temperature" class="form-label">Body Temperature (°C)</label>
                                                            <input type="number" class="form-control @error('body_temperature') is-invalid @enderror" 
                                                                id="body_temperature" name="body_temperature" required 
                                                                placeholder="e.g. 36.5" value="{{ old('body_temperature') }}"
                                                                min="35" max="42" step="0.1">
                                                            <small class="form-text text-muted">Enter a number between 35-42°C</small>
                                                            @error('body_temperature')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                            </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6 col-sm-6">
                                                            <label for="pulse_rate" class="form-label">Pulse Rate (bpm)</label>
                                                            <input type="number" class="form-control @error('pulse_rate') is-invalid @enderror" 
                                                                id="pulse_rate" name="pulse_rate" required 
                                                                placeholder="e.g. 72" value="{{ old('pulse_rate') }}"
                                                                min="40" max="200" step="1">
                                                            <small class="form-text text-muted">Enter a whole number (beats per minute)</small>
                                                            @error('pulse_rate')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="col-md-6 col-sm-6 position-relative">
                                                            <label for="respiratory_rate" class="form-label">Respiratory Rate (bpm)</label>
                                                            <input type="number" class="form-control @error('respiratory_rate') is-invalid @enderror" 
                                                                id="respiratory_rate" name="respiratory_rate" required 
                                                                placeholder="e.g. 16" value="{{ old('respiratory_rate') }}"
                                                                min="8" max="40" step="1">
                                                            <small class="form-text text-muted">Enter a whole number (breaths per minute)</small>
                                                            @error('respiratory_rate')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mt-4">
                                                <div class="col-12 d-flex justify-content-end">
                                                <button type="button" class="btn btn-primary" onclick="goToPage(2)">Next <i class="fa fa-arrow-right"></i></button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- PAGE 2-8: Interventions by Category -->
                                    @foreach($careCategories as $index => $category)
                                        <div class="form-page" id="page{{ $index + 2 }}" 
                                            style="{{ ($index + 2) > 8 ? 'display:none;' : '' }}">
                                            <div class="card mb-4">
                                                <div class="card-header">
                                                    <h5>{{ $category->care_category_name }}</h5>
                                                </div>
                                                <div class="card-body">
                                                    @foreach($category->interventions as $intervention)
                                                        <div class="row intervention-row">
                                                            <div class="col-md-8">
                                                                <div class="form-check">
                                                                    <input class="form-check-input intervention-checkbox" 
                                                                        type="checkbox" 
                                                                        name="selected_interventions[]" 
                                                                        id="intervention_{{ $intervention->intervention_id }}"
                                                                        value="{{ $intervention->intervention_id }}">
                                                                    <label class="form-check-label" for="intervention_{{ $intervention->intervention_id }}">
                                                                        {{ $intervention->intervention_description }}
                                                                    </label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="input-group duration-input">
                                                                    <input type="number" 
                                                                        class="form-control intervention-duration" 
                                                                        name="duration_minutes[{{ $intervention->intervention_id }}]" 
                                                                        placeholder="Minutes" 
                                                                        min="0.01" 
                                                                        max="999.99" 
                                                                        step="0.01"
                                                                        disabled>
                                                                    <span class="input-group-text">min</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                    
                                                    <!-- Custom intervention for this category -->
                                                    <div class="mt-4">
                                                        <h6>Add Custom {{ $category->care_category_name }} Intervention</h6>
                                                        <div class="custom-intervention-container" data-category="{{ $category->care_category_id }}">
                                                            <!-- Custom interventions will be added here -->
                                                        </div>
                                                        <button type="button" class="btn btn-sm btn-outline-primary add-custom-intervention"
                                                                data-category="{{ $category->care_category_id }}">
                                                            <i class="fa fa-plus"></i> Add Custom Intervention
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="d-flex justify-content-between">
                                                <button type="button" class="btn btn-secondary" onclick="goToPage({{ $index + 1 }})">
                                                    <i class="fa fa-arrow-left"></i> Previous
                                                </button>
                                                <button type="button" class="btn btn-primary" onclick="goToPage({{ $index + 3 }})">
                                                    Next <i class="fa fa-arrow-right"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach                       

                                        <!-- Page 9: Evaluation and Submit -->
                                        <div class="form-page" id="page9">
                                            <div class="row mb-3 mt-2 justify-content-center">
                                                <div class="col-lg-8 col-md-12 col-sm-12 text-center">
                                                    <label for="evaluation_recommendations" class="form-label"><h5>Recommendations and Evaluations</h5></label>
                                                    <textarea class="form-control @error('evaluation_recommendations') is-invalid @enderror" 
                                                            id="evaluation_recommendations" name="evaluation_recommendations" 
                                                            rows="6" required>{{ old('evaluation_recommendations') }}</textarea>
                                                    @error('evaluation_recommendations')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="row mt-4">
                                                <div class="col-12 d-flex justify-content-between align-items-center">
                                                    <button type="button" class="btn btn-secondary" onclick="goToPage(8)">
                                                        <i class="fa fa-arrow-left"></i> Previous
                                                    </button>
                                                    <button type="submit" class="btn btn-success">
                                                        <i class="fa fa-save"></i> Save Weekly Care Plan
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Template for custom intervention row -->
    <template id="custom-intervention-template">
        <div class="row custom-intervention-row mb-2">
            <div class="col-md-8">
                <input type="text" class="form-control" name="custom_description[]" placeholder="Describe intervention">
                <input type="hidden" name="custom_category[]" value="">
            </div>
            <div class="col-md-3">
                <div class="input-group">
                    <input type="number" class="form-control" name="custom_duration[]" placeholder="Minutes" min="0.01" max="999.99" step="0.01">
                    <span class="input-group-text">min</span>
                </div>
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-sm btn-danger remove-custom-row">
                    <i class="fa fa-times"></i>
                </button>
            </div>
        </div>
    </template>

    <!-- Client Side Validation -->

    <script>
    // Client-side validation for vital signs
    document.addEventListener('DOMContentLoaded', function() {
        // Blood pressure validation
        const bpInput = document.getElementById('blood_pressure');
        bpInput.addEventListener('input', function() {
            const bpRegex = /^\d{2,3}\/\d{2,3}$/;
            if (!bpRegex.test(this.value) && this.value !== '') {
                this.classList.add('is-invalid');
                if (!this.nextElementSibling.classList.contains('invalid-feedback')) {
                    const feedback = document.createElement('div');
                    feedback.classList.add('invalid-feedback');
                    feedback.textContent = 'Blood pressure must be in format 120/80';
                    this.parentNode.insertBefore(feedback, this.nextSibling);
                }
            } else {
                this.classList.remove('is-invalid');
                const feedback = this.nextElementSibling;
                if (feedback && feedback.classList.contains('invalid-feedback')) {
                    feedback.remove();
                }
            }
        });
        
        // Form submission validation for vital signs
        document.getElementById('weeklyCarePlanForm').addEventListener('submit', function(e) {
            // Existing validation code...
            
            // Additional validation for vital signs
            const bodyTemp = document.getElementById('body_temperature').value;
            const pulseRate = document.getElementById('pulse_rate').value;
            const respRate = document.getElementById('respiratory_rate').value;
            const bloodPressure = document.getElementById('blood_pressure').value;
            
            // Check if blood pressure is in the correct format
            const bpRegex = /^\d{2,3}\/\d{2,3}$/;
            if (!bpRegex.test(bloodPressure)) {
                e.preventDefault();
                alert('Blood pressure must be in format 120/80');
                goToPage(1); // Go back to the vital signs page
                return;
            }
            
            // Check if numeric values are within realistic ranges
            if (bodyTemp < 35 || bodyTemp > 42) {
                e.preventDefault();
                alert('Body temperature must be between 35°C and 42°C');
                goToPage(1);
                return;
            }
            
            if (pulseRate < 40 || pulseRate > 200) {
                e.preventDefault();
                alert('Pulse rate must be between 40 and 200 bpm');
                goToPage(1);
                return;
            }
            
            if (respRate < 8 || respRate > 40) {
                e.preventDefault();
                alert('Respiratory rate must be between 8 and 40 bpm');
                goToPage(1);
                return;
            }
        });
    });
    </script>

    <script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/toggleSideBar.js') }}"></script>
    
    <script>
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        // Form navigation
        function goToPage(pageNumber) {
            // Hide all pages
            document.querySelectorAll('.form-page').forEach(page => {
                page.classList.remove('active');
            });
            
            // Show the selected page
            document.getElementById('page' + pageNumber).classList.add('active');
            
            // Update breadcrumb active state
            document.querySelectorAll('.breadcrumb-item').forEach(item => {
                item.classList.remove('active');
                if (item.dataset.page == pageNumber) {
                    item.classList.add('active');
                    
                    // Scroll the active breadcrumb item into view
                    setTimeout(() => {
                        const breadcrumbContainer = document.querySelector('.breadcrumb-container');
                        const activeItem = item;
                        
                        // Calculate scroll position to center the active item
                        const containerWidth = breadcrumbContainer.offsetWidth;
                        const itemLeft = activeItem.offsetLeft;
                        const itemWidth = activeItem.offsetWidth;
                        const scrollPosition = itemLeft - (containerWidth / 2) + (itemWidth / 2);
                        
                        // Smooth scroll to the position
                        breadcrumbContainer.scrollTo({
                            left: Math.max(0, scrollPosition),
                            behavior: 'smooth'
                        });
                    }, 100);
                }
            });
            
            // Scroll to top of content
            window.scrollTo(0, 0);
        }
        
        // Breadcrumb navigation
        document.querySelectorAll('.breadcrumb-item').forEach(item => {
            item.addEventListener('click', function() {
                goToPage(this.dataset.page);
            });
        });
        
        // Handle beneficiary selection and auto-fill
        document.getElementById('beneficiary_id').addEventListener('change', function() {
            const beneficiaryId = this.value;
            
            if (!beneficiaryId) {
                // Clear all fields if no beneficiary selected
                document.getElementById('age').value = '';
                document.getElementById('birthDate').value = '';
                document.getElementById('gender').value = '';
                document.getElementById('civilStatus').value = '';
                document.getElementById('address').value = '';
                document.getElementById('medicalConditions').value = '';
                return;
            }
            
            // Fetch beneficiary details via AJAX
            fetch(`/weekly-care-plans/beneficiary/${beneficiaryId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const beneficiary = data.data;
                        
                        // Calculate age
                        const birthDate = new Date(beneficiary.birthday);
                        const today = new Date();
                        let age = today.getFullYear() - birthDate.getFullYear();
                        const monthDiff = today.getMonth() - birthDate.getMonth();
                        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
                            age--;
                        }
                        
                        // Populate form fields
                        document.getElementById('age').value = age;
                        document.getElementById('birthDate').value = beneficiary.birthday;
                        document.getElementById('gender').value = beneficiary.gender;
                        document.getElementById('civilStatus').value = beneficiary.civil_status;
                        document.getElementById('address').value = beneficiary.street_address;
                        
                        // Set medical conditions if available
                        let medicalConditions = 'No medical conditions recorded';
                        
                        if (beneficiary.general_care_plan && beneficiary.general_care_plan.health_history) {
                            medicalConditions = beneficiary.general_care_plan.health_history.medical_conditions || 'None';
                        }
                        
                        document.getElementById('medicalConditions').value = medicalConditions;
                    } else {
                        alert('Failed to load beneficiary details');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while fetching beneficiary data');
                });
        });
        
        // Handle intervention checkboxes
        document.querySelectorAll('.intervention-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const durationInput = this.closest('.row').querySelector('.intervention-duration');
                
                if (this.checked) {
                    durationInput.disabled = false;
                    durationInput.required = true;
                } else {
                    durationInput.disabled = true;
                    durationInput.required = false;
                    durationInput.value = '';
                }
            });
        });
        
        // Add custom intervention
        document.querySelectorAll('.add-custom-intervention').forEach(button => {
            button.addEventListener('click', function() {
                const categoryId = this.dataset.category;
                const container = this.previousElementSibling;
                
                // Clone the template
                const template = document.getElementById('custom-intervention-template');
                const clone = document.importNode(template.content, true);
                
                // Set the category ID in the hidden input
                clone.querySelector('input[name="custom_category[]"]').value = categoryId;
                
                // Add remove button functionality
                clone.querySelector('.remove-custom-row').addEventListener('click', function() {
                    this.closest('.custom-intervention-row').remove();
                });
                
                // Add to container
                container.appendChild(clone);
            });
        });
        
        // Form validation before submission
        document.getElementById('weeklyCarePlanForm').addEventListener('submit', function(e) {
            let isValid = true;
            let message = '';
            
            // Check if beneficiary is selected
            if (!document.getElementById('beneficiary_id').value) {
                isValid = false;
                message = 'Please select a beneficiary';
            }
            
            // Check if at least one intervention is selected
            const checkedInterventions = document.querySelectorAll('.intervention-checkbox:checked');
            const customInterventions = document.querySelectorAll('input[name="custom_description[]"]');
            
            if (checkedInterventions.length === 0 && customInterventions.length === 0) {
                isValid = false;
                message = 'Please select at least one intervention';
            }
            
            // Check if all selected interventions have duration
            checkedInterventions.forEach(checkbox => {
                const durationInput = checkbox.closest('.row').querySelector('.intervention-duration');
                if (!durationInput.value) {
                    isValid = false;
                    message = 'Please enter duration for all selected interventions';
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert(message);
            }
        });
    </script>

    <script>
    // Function to add a new "Others" input
    function addOthersInput(containerId) {
        const othersContainer = document.getElementById(containerId);
        const newOthersRow = document.createElement('div');
        newOthersRow.classList.add('row', 'mb-2', 'others-row');
        newOthersRow.innerHTML = `
            <div class="col-md-9">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="${containerId}_others${othersContainer.children.length}">
                    <label class="form-check-label" for="${containerId}_others${othersContainer.children.length}">Others</label>
                </div>
                <!-- Additional Input Field -->
                <input type="text" class="form-control mt-2" placeholder="Enter details">
            </div>
            <div class="col-md-3">
                <input type="number" class="form-control" placeholder="Mins">
            </div>
        `;
        othersContainer.appendChild(newOthersRow);
    }

    // Function to delete the last "Others" input
    function deleteOthersInput(containerId) {
        const othersContainer = document.getElementById(containerId);
        const othersRows = othersContainer.querySelectorAll('.others-row');
        if (othersRows.length > 1) { // Ensure at least one "Others" input remains
            othersContainer.removeChild(othersRows[othersRows.length - 1]);
        } else {
            alert("At least one 'Others' input is required.");
        }
    }
</script>
</body>
</html>