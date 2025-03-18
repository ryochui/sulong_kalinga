<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Beneficiary</title>
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
                <a href="{{ route('beneficiaryProfile') }}" class="btn btn-secondary">
                    <i class="bx bx-arrow-back"></i> Back
                </a>
                <div class="mx-auto text-center" style="flex-grow: 1; font-weight: bold; font-size: 20px;">ADD BENEFICIARY</div>
            </div>
            <div class="row" id="addUserForm">
                <div class="col-12">
                    <!-- <form action="{{ route('addBeneficiary') }}" method="POST"> -->
                    <form>
                        @csrf
                        <!-- Row 1: Personal Details -->
                        <div class="row mb-1 mt-3">
                            <div class="col-12">
                                <h5 class="text-start">Personal Details</h5>
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-md-3">
                                <label for="firstName" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="firstName" name="first_name" placeholder="Enter first name" required>
                            </div>
                            <div class="col-md-3">
                                <label for="lastName" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="lastName" name="last_name" placeholder="Enter last name" required>
                            </div>
                            <div class="col-md-3 position-relative">
                                <label for="civilStatus" class="form-label">Civil Status</label>
                                <input type="text" class="form-control" id="civilStatusInput" placeholder="Select civil status" autocomplete="off">
                                <ul class="dropdown-menu w-100" id="civilStatusDropdown">
                                    <li><a class="dropdown-item" data-value="single">Single</a></li>
                                    <li><a class="dropdown-item" data-value="married">Married</a></li>
                                    <li><a class="dropdown-item" data-value="widowed">Widowed</a></li>
                                    <li><a class="dropdown-item" data-value="divorced">Divorced</a></li>
                                </ul>
                                <input type="hidden" id="civilStatus" name="civil_status">
                            </div>
                            <div class="col-md-3 position-relative">
                                <label for="gender" class="form-label">Gender</label>
                                <input type="text" class="form-control" id="genderInput" placeholder="Select gender" autocomplete="off">
                                <ul class="dropdown-menu w-100" id="genderDropdown">
                                    <li><a class="dropdown-item" data-value="male">Male</a></li>
                                    <li><a class="dropdown-item" data-value="female">Female</a></li>
                                    <li><a class="dropdown-item" data-value="other">Other</a></li>
                                </ul>
                                <input type="hidden" id="gender" name="gender">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="birthDate" class="form-label">Birthday</label>
                                <input type="date" class="form-control" id="birthDate" name="birth_date" required onkeydown="return true">
                            </div>
                            <div class="col-md-3 position-relative">
                                <label for="primaryCareworker" class="form-label">Primary Caregiver</label>
                                <input type="text" class="form-control" id="primaryCareworkerInput" placeholder="Select Primary Careworker" autocomplete="off">
                                <ul class="dropdown-menu w-100" id="primaryCareworkerDropdown">
                                    <li><a class="dropdown-item" data-value="careworker1">Careworker 1</a></li>
                                    <li><a class="dropdown-item" data-value="careworker2">Careworker 2</a></li>
                                    <li><a class="dropdown-item" data-value="careworker3">Careworker 3</a></li>
                                </ul>
                                <input type="hidden" id="primaryCareworker" name="primaryCareworker">
                            </div>
                            <div class="col-md-3">
                                <label for="mobileNumber" class="form-label">Mobile Number</label>
                                <input type="text" class="form-control" id="mobileNumber" name="mobile_number" placeholder="Enter mobile number" required>
                            </div>
                            <div class="col-md-3">
                                <label for="landlineNumber" class="form-label">Landline Number</label>
                                <input type="text" class="form-control" id="landlineNumber" name="landline_number" placeholder="Enter landline number">
                            </div>
                        </div>

                        <hr class="my-4">
                        <!-- Row 2: Address -->
                        <div class="row mb-1">
                            <div class="col-12">
                                <h5 class="text-start">Current Address</h5> 
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="addressDetails" class="form-label">House No., Street, Subdivision</label>
                                <input type="text" class="form-control" id="addressDetails" name="address_details" placeholder="Enter house no., street, subdivision" required>
                            </div>
                            <div class="col-md-3 position-relative">
                                <label for="barangay" class="form-label">Barangay</label>
                                <input type="text" class="form-control" id="barangayInput" placeholder="Select barangay" autocomplete="off">
                                <ul class="dropdown-menu w-100" id="barangayDropdown">
                                    <li><a class="dropdown-item" data-value="barangay1">Barangay 1</a></li>
                                    <li><a class="dropdown-item" data-value="barangay2">Barangay 2</a></li>
                                    <li><a class="dropdown-item" data-value="barangay3">Barangay 3</a></li>
                                </ul>
                                <input type="hidden" id="barangay" name="barangay">
                            </div>
                            <div class="col-md-3 position-relative">
                                <label for="municipality" class="form-label">Municipality</label>
                                <input type="text" class="form-control" id="municipalityInput" placeholder="Select municipality" autocomplete="off">
                                <ul class="dropdown-menu w-100" id="municipalityDropdown">
                                    <li><a class="dropdown-item" data-value="municipality1">Municipality 1</a></li>
                                    <li><a class="dropdown-item" data-value="municipality2">Municipality 2</a></li>
                                    <li><a class="dropdown-item" data-value="municipality3">Municipality 3</a></li>
                                </ul>
                                <input type="hidden" id="municipality" name="municipality">
                            </div>
                        </div>

                        <hr class="my-4">
                        <!-- Row 3: Medical History -->
                        <div class="row mb-1">
                            <div class="col-12">
                                <h5 class="text-start">Medical History</h5> 
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-md-3">
                                <label for="medicalConditions" class="form-label">Medical Conditions</label>
                                <textarea class="form-control" id="medicalConditions" name="medical_conditions" placeholder="List all medical conditions" rows="3"></textarea>
                            </div>
                            <div class="col-md-3">
                                <label for="medications" class="form-label">Medications</label>
                                <textarea class="form-control" id="medications" name="medications" placeholder="List all medications" rows="3"></textarea>
                            </div>
                            <div class="col-md-3">
                                <label for="allergies" class="form-label">Allergies</label>
                                <textarea class="form-control" id="allergies" name="allergies" placeholder="List all allergies" rows="3"></textarea>
                            </div>
                            <div class="col-md-3">
                                <label for="immunizations" class="form-label">Immunizations</label>
                                <textarea class="form-control" id="immunizations" name="immunizations" placeholder="List all immunizations" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="row mb-3">
                        <div class="col-md-3 position-relative">
                            <label for="category" class="form-label">Category</label>
                            <input type="text" class="form-control" id="categoryInput" placeholder="Select category" autocomplete="off">
                            <ul class="dropdown-menu w-100" id="categoryDropdown">
                                <li><a class="dropdown-item" data-value="frail">Frail</a></li>
                                <li><a class="dropdown-item" data-value="bedridden">Bedridden</a></li>
                                <li><a class="dropdown-item" data-value="dementia">Dementia</a></li>
                            </ul>
                            <input type="hidden" id="category" name="category">
                        </div>
                        </div>

                        <hr class="my-4">
                        <!-- Care Needs -->
                        <div class="row mb-1">
                            <div class="col-md-4">
                                <h5 class="text-start">Care Needs</h5>
                            </div>
                        </div>

                        <!-- Care Needs Rows -->
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-4">
                                <label class="form-label">Mobility</label>
                            </div>
                            <div class="col-md-4">
                                <textarea class="form-control" id="mobilityFrequency" name="frequency[mobility]" placeholder="Frequency" rows="2"></textarea>
                            </div>
                            <div class="col-md-4">
                                <textarea class="form-control" id="mobilityAssistance" name="assistance[mobility]" placeholder="Assistance Required" rows="2"></textarea>
                            </div>
                        </div>
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-4">
                                <label class="form-label">Cognitive / Communication</label>
                            </div>
                            <div class="col-md-4">
                                <textarea class="form-control" id="cognitiveFrequency" name="frequency[cognitive]" placeholder="Frequency" rows="2"></textarea>
                            </div>
                            <div class="col-md-4">
                                <textarea class="form-control" id="cognitiveAssistance" name="assistance[cognitive]" placeholder="Assistance Required" rows="2"></textarea>
                            </div>
                        </div>
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-4">
                                <label class="form-label">Self-sustainability</label>
                            </div>
                            <div class="col-md-4">
                                <textarea class="form-control" id="selfSustainabilityFrequency" name="frequency[self_sustainability]" placeholder="Frequency" rows="2"></textarea>
                            </div>
                            <div class="col-md-4">
                                <textarea class="form-control" id="selfSustainabilityAssistance" name="assistance[self_sustainability]" placeholder="Assistance Required" rows="2"></textarea>
                            </div>
                        </div>
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-4">
                                <label class="form-label">Disease / Therapy Handling</label>
                            </div>
                            <div class="col-md-4">
                                <textarea class="form-control" id="diseaseFrequency" name="frequency[disease]" placeholder="Frequency" rows="2"></textarea>
                            </div>
                            <div class="col-md-4">
                                <textarea class="form-control" id="diseaseAssistance" name="assistance[disease]" placeholder="Assistance Required" rows="2"></textarea>
                            </div>
                        </div>
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-4">
                                <label class="form-label">Daily Life / Social Contact</label>
                            </div>
                            <div class="col-md-4">
                                <textarea class="form-control" id="dailyLifeFrequency" name="frequency[daily_life]" placeholder="Frequency" rows="2"></textarea>
                            </div>
                            <div class="col-md-4">
                                <textarea class="form-control" id="dailyLifeAssistance" name="assistance[daily_life]" placeholder="Assistance Required" rows="2"></textarea>
                            </div>
                        </div>
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-4">
                                <label class="form-label">Outdoor Activities</label>
                            </div>
                            <div class="col-md-4">
                                <textarea class="form-control" id="outdoorFrequency" name="frequency[outdoor]" placeholder="Frequency" rows="2"></textarea>
                            </div>
                            <div class="col-md-4">
                                <textarea class="form-control" id="outdoorAssistance" name="assistance[outdoor]" placeholder="Assistance Required" rows="2"></textarea>
                            </div>
                        </div>
                        <div class="row mb-3 align-items-center">
                            <div class="col-md-4">
                                <label class="form-label">Household Keeping</label>
                            </div>
                            <div class="col-md-4">
                                <textarea class="form-control" id="householdFrequency" name="frequency[household]" placeholder="Frequency" rows="2"></textarea>
                            </div>
                            <div class="col-md-4">
                                <textarea class="form-control" id="householdAssistance" name="assistance[household]" placeholder="Assistance Required" rows="2"></textarea>
                            </div>
                        </div>

                        <hr class="my-4">
                       <!-- Medication Management -->
                        <div class="row mb-1">
                            <div class="col-12">
                                <h5 class="text-start">Medication Management</h5> 
                            </div>
                        </div>
                        <div id="medicationManagement">
                            <div class="row mb-1 align-items-center medication-row">
                                <div class="col-md-3">
                                    <input type="text" class="form-control" name="medication_name[]" placeholder="Medication name" >
                                </div>
                                <div class="col-md-2">
                                    <input type="text" class="form-control" name="dosage[]" placeholder="Dosage" >
                                </div>
                                <div class="col-md-2">
                                    <input type="text" class="form-control" name="frequency[]" placeholder="Frequency" >
                                </div>
                                <div class="col-md-4">
                                    <textarea class="form-control" name="administration_instructions[]" placeholder="Administration Instructions" rows="1" ></textarea>
                                </div>
                                <div class="col-md-1 d-flex text-start">
                                    <button type="button" class="btn btn-danger" onclick="removeMedicationRow(this)">Delete</button>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12 text-start">
                                <button type="button" class="btn btn-primary" onclick="addMedicationRow()">Add Medication</button>
                            </div>
                        </div>

                        <hr class="my-4">
                        <!-- Mobility, Cognitive Function, Emotional Well-being -->
                        <div class="row mb-1">
                            <div class="col-md-4">
                                <h5 class="text-start">Mobility</h5>
                                <div class="mb-1">
                                    <label for="walkingAbility" class="form-label">Walking Ability</label>
                                    <textarea class="form-control" id="walkingAbility" name="mobility[walking_ability]" placeholder="Enter details about walking ability" rows="2"></textarea>
                                </div>
                                <div class="mb-1">
                                    <label for="assistiveDevices" class="form-label">Assistive Devices</label>
                                    <textarea class="form-control" id="assistiveDevices" name="mobility[assistive_devices]" placeholder="Enter details about assistive devices" rows="2"></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="transportationNeeds" class="form-label">Transportation Needs</label>
                                    <textarea class="form-control" id="transportationNeeds" name="mobility[transportation_needs]" placeholder="Enter details about transportation needs" rows="2"></textarea>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <h5 class="text-start">Cognitive Function</h5>
                                <div class="mb-1">
                                    <label for="memory" class="form-label">Memory</label>
                                    <textarea class="form-control" id="memory" name="cognitive[memory]" placeholder="Enter details about memory" rows="2"></textarea>
                                </div>
                                <div class="mb-1">
                                    <label for="thinkingSkills" class="form-label">Thinking Skills</label>
                                    <textarea class="form-control" id="thinkingSkills" name="cognitive[thinking_skills]" placeholder="Enter details about thinking skills" rows="2"></textarea>
                                </div>
                                <div class="mb-1">
                                    <label for="orientation" class="form-label">Orientation</label>
                                    <textarea class="form-control" id="orientation" name="cognitive[orientation]" placeholder="Enter details about orientation" rows="2"></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="behavior" class="form-label">Behavior</label>
                                    <textarea class="form-control" id="behavior" name="cognitive[behavior]" placeholder="Enter details about behavior" rows="2"></textarea>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <h5 class="text-start">Emotional Well-being</h5>
                                <div class="mb-1">
                                    <label for="mood" class="form-label">Mood</label>
                                    <textarea class="form-control" id="mood" name="emotional[mood]" placeholder="Enter details about mood" rows="2"></textarea>
                                </div>
                                <div class="mb-1">
                                    <label for="socialInteractions" class="form-label">Social Interactions</label>
                                    <textarea class="form-control" id="socialInteractions" name="emotional[social_interactions]" placeholder="Enter details about social interactions" rows="2"></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="emotionalSupport" class="form-label">Emotional Support Need</label>
                                    <textarea class="form-control" id="emotionalSupport" name="emotional[emotional_support]" placeholder="Enter details about emotional support need" rows="2"></textarea>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">
                        <!-- Emergency Contact -->
                        <div class="row mb-1">
                            <div class="col-12">
                                <h5 class="text-start">Emergency Contact</h5>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="contactName" class="form-label">Contact Name</label>
                                <input type="text" class="form-control" id="contactName" name="emergency_contact[name]" placeholder="Enter contact name" required>
                            </div>
                            <div class="col-md-3 position-relative">
                                <label for="relation" class="form-label">Relation</label>
                                <input type="text" class="form-control" id="relationInput" placeholder="Select relation" autocomplete="off">
                                <ul class="dropdown-menu w-100" id="relationDropdown">
                                    <li><a class="dropdown-item" data-value="son">Son</a></li>
                                    <li><a class="dropdown-item" data-value="daughter">Daughter</a></li>
                                    <li><a class="dropdown-item" data-value="grandchild">Grandchild</a></li>
                                </ul>
                                <input type="hidden" id="relation" name="relation">
                            </div>
                            <div class="col-md-3">
                                <label for="mobileNumber" class="form-label">Mobile Number</label>
                                <input type="text" class="form-control" id="mobileNumber" name="emergency_contact[mobile]" placeholder="Enter mobile number" required>
                            </div>
                            <div class="col-md-3">
                                <label for="emailAddress" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="emailAddress" name="emergency_contact[email]" placeholder="Enter email address" >
                            </div>
                        </div>

                        <hr class="my-4">
                        <!-- Emergency Plan -->
                        <div class="row mb-1">
                            <div class="col-12">
                                <h5 class="text-start">Emergency Plan</h5> 
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="emergencyProcedures" class="form-label">Emergency Procedures</label>
                                <textarea class="form-control" id="emergencyProcedures" name="emergency_plan[procedures]" placeholder="Enter emergency procedures" rows="3" required></textarea>
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        <!-- Care Worker's Responsibilities -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <h5 class="text-start">Care Worker's Responsibilities</h5> 
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-3 position-relative">
                                <label for="careworkerName" class="form-label">Municipality</label>
                                <input type="text" class="form-control" id="careworkerNameInput" placeholder="Select municipality" autocomplete="off">
                                <ul class="dropdown-menu w-100" id="careworkerNameDropdown">
                                    <li><a class="dropdown-item" data-value="careworker1">Careworker 1</a></li>
                                    <li><a class="dropdown-item" data-value="careworker2">Careworker 2</a></li>
                                    <li><a class="dropdown-item" data-value="careworker3">Careworker 3</a></li>
                                </ul>
                                <input type="hidden" id="careworkerName" name="careworkerName">
                            </div>
                            <div class="col-md-5">
                                <label class="form-label">Tasks and Responsibilities</label>
                                <div id="tasksContainer">
                                    <div class="input-group mb-2">
                                        <input type="text" class="form-control" name="care_worker[tasks][]" placeholder="Enter task or responsibility" required>
                                    </div>
                                    <div class="input-group mb-2">
                                        <input type="text" class="form-control" name="care_worker[tasks][]" placeholder="Enter task or responsibility" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 d-flex flex-column align-items-start">
                                <label class="form-label">Add or Delete Task</label>
                                <button type="button" class="btn btn-primary btn-sm mb-2 w-100" onclick="addTask()">Add Task</button>
                                <button type="button" class="btn btn-danger btn-sm w-100" onclick="removeTask()">Delete Task</button>
                            </div>
                        </div>

                        <!-- General Care Plan and Care Service Agreement File Upload -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="datePicker" class="form-label">Review Date</label>
                                <input type="date" class="form-control" id="datePicker" name="date" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-4">
                                <label for="generalCarePlan" class="form-label">General Care Plan</label>
                                <input type="file" class="form-control" id="generalCarePlan" name="general_care_plan" accept=".pdf,.doc,.docx" required>
                            </div>
                            <div class="col-md-4">
                                <label for="careServiceAgreement" class="form-label">Care Service Agreement</label>
                                <input type="file" class="form-control" id="careServiceAgreement" name="care_service_agreement" accept=".pdf,.doc,.docx" required>
                            </div>
                        </div>

                        <hr class="my-4">
                        <!-- Account Registration -->
                        <div class="row mb-1">
                            <div class="col-12">
                                <h5 class="text-start">Family Portal Account Registration</h5> 
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="accountEmail" class="form-label">Email</label>
                                <input type="email" class="form-control" id="accountEmail" name="account[email]" placeholder="Enter email" required>
                            </div>
                            <div class="col-md-4">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="account[password]" placeholder="Enter password" required>
                            </div>
                            <div class="col-md-4">
                                <label for="confirmPassword" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="confirmPassword" name="account[confirm_password]" placeholder="Confirm password" required>
                            </div>
                        </div>

                        
                        <div class="row mt-4">
                            <div class="col-12 d-flex justify-content-center align-items-center">
                                <button type="submit" class="btn btn-success btn-lg d-flex align-items-center" id="saveBeneficiaryButton">
                                    <i class='bx bx-save me-2' style="font-size: 24px;"></i>
                                    Save Beneficiary
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
                    <p>Beneficiary has been successfully saved!</p>
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
        function addMedicationRow() {
        const container = document.getElementById('medicationManagement');
        const newRow = document.createElement('div');
        newRow.className = 'row mb-2 align-items-center medication-row';
        newRow.innerHTML = `
            <div class="col-md-3">
                <input type="text" class="form-control" name="medication_name[]" placeholder="Enter medication name" required>
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control" name="dosage[]" placeholder="Enter dosage" required>
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control" name="frequency[]" placeholder="Enter frequency" required>
            </div>
            <div class="col-md-4">
                <textarea class="form-control" name="administration_instructions[]" placeholder="Enter administration instructions" rows="1" required></textarea>
            </div>
            <div class="col-md-1 d-flex text-start">
                <button type="button" class="btn btn-danger" onclick="removeMedicationRow(this)">Delete</button>
            </div>
        `;
        container.appendChild(newRow);

        inputGroup.scrollIntoView({ behavior: 'smooth', block: 'center' });

        }

        // Function to remove a medication row
        function removeMedicationRow(button) {
            const row = button.closest('.medication-row');
            row.remove();
        }

    </script>
    <script>
        // Function to add a new task input field
        function addTask() {
            const tasksContainer = document.getElementById('tasksContainer');
            const inputGroup = document.createElement('div');
            inputGroup.className = 'input-group mb-2';

            const input = document.createElement('input');
            input.type = 'text';
            input.className = 'form-control';
            input.name = 'care_worker[tasks][]';
            input.placeholder = 'Enter task or responsibility';
            input.required = true;

            tasksContainer.appendChild(inputGroup);
            inputGroup.appendChild(input);

            inputGroup.scrollIntoView({ behavior: 'smooth', block: 'center' });

        }

        // Function to remove a task input field
        function removeTask(button) {
            const tasksContainer = document.getElementById('tasksContainer');
            if (tasksContainer.children.length > 0) {
                tasksContainer.lastChild.remove();
            }
        }

    </script>
    <script>
        document.querySelector('form').addEventListener('submit', function (e) {
            e.preventDefault();
            const successModal = new bootstrap.Modal(document.getElementById('saveSuccessModal'));
            successModal.show();
        });
    </script>
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
            filterDropdown('primaryCareworkerInput', 'primaryCareworkerDropdown');
            filterDropdown('barangayInput', 'barangayDropdown');
            filterDropdown('municipalityInput', 'municipalityDropdown');
            filterDropdown('categoryInput', 'categoryDropdown');
            filterDropdown('relationInput', 'relationDropdown');
            filterDropdown('careworkerNameInput', 'careworkerNameDropdown');
        });
    </script>

</body>
</html>