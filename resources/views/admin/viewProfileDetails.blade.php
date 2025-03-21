<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="{{ asset('css/viewProfileDetails.css') }}">
</head>
<body>

    @include('components.userNavbar')
    @include('components.sidebar')
    
    <div class="home-section">
        <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
    <!-- Original Back Button -->
    <a href="beneficiaryProfile" class="btn btn-secondary original-back-btn">
        <i class="bx bx-arrow-back"></i> Back
    </a>

    <div class="mx-auto text-center" style="flex-grow: 1; font-weight: bold; font-size: 20px;">VIEW BENEFICIARY PROFILE DETAILS</div>

    <!-- Edit and Delete Buttons -->
    <div>
        <!-- Hidden Back Button -->
        <a href="beneficiaryProfile" class="btn btn-secondary hidden-back-btn">
            <i class="bx bx-arrow-back"></i> Back
        </a>
        <a href="editProfile" class="btn btn-primary" onclick="editProfile()">
            <i class="bx bxs-edit"></i> Edit
        </a>
        <button class="btn btn-danger" onclick="deleteProfile()">
            <i class="bx bxs-trash"></i> Delete
        </button>
    </div>
</div>
            <div class="row p-lg-3 p-md-2 p-sm-1 justify-content-center" id="profileDetails">
                <div class="row p-lg-3 p-md-2 p-sm-1 justify-content-center">
                    <div class="col-lg-8 col-md-12 col-sm-12" id="profilePic">
                        <div class="row justify-content-center align-items-center text-center text-md-start">
                            <!-- Profile Picture Column -->
                            <div class="col-lg-3 col-md-4 col-sm-12 mb-3 mb-md-0">
                                <img src="{{ asset('images/defaultProfile.png') }}" 
                                    alt="Profile Picture" 
                                    class="img-fluid rounded-circle mx-auto d-block d-md-inline" 
                                    style="width: 150px; height: 150px; border: 1px solid #ced4da;">
                            </div>
                            <!-- Name and Details Column -->
                            <div class="col-lg-9 col-md-8 col-sm-12">
                                <div class="d-flex flex-column flex-md-row align-items-center align-items-md-start">
                                    <!-- Complete Name -->
                                    <h4 class="me-md-3 mb-2 mb-md-0 mt-2">First Name M.I. Last Name</h4>
                                    <!-- Dropdown for Status -->
                                    <div class="form-group mb-0 ms-md-auto">
                                        <select class="form-select d-inline-block w-auto" id="status" name="status">
                                            <option value="active">Active Beneficiary</option>
                                            <option value="inactive">Inactive</option>
                                        </select>
                                    </div>
                                </div>
                                <p class="text-muted mt-2 text-center text-md-start">A Beneficiary since 00-00-0000</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <!-- Personal Details Column -->
                    <div class="col-lg-6 col-md-6 col-sm-12 p-lg-3 p-md-2 p-sm-1">
                        <h5 class="text-center">Personal Details</h5>
                        <table class="table table-striped personal-details">                            
                            <tbody>
                                <tr>
                                    <td style="width:30%;"><strong>Primary Caregiver:</strong></td>
                                    <td><p><!-- Backend data --></p></td>
                                </tr>
                                <tr>
                                    <td style="width:30%;"><strong>Gender:</strong></td>
                                    <td><p><!-- Backend data --></p></td>
                                </tr>
                                <tr>
                                    <td style="width:30%;"><strong>Civil Status:</strong></td>
                                    <td><p><!-- Backend data --></p></td>
                                </tr>
                                <tr>
                                    <td style="width:30%;"><strong>Mobile Number:</strong></td>
                                    <td><p><!-- Backend data --></p></td>
                                </tr>
                                <tr>
                                    <td style="width:30%;"><strong>Landline Number:</strong></td>
                                    <td><p><!-- Backend data --></p></td>
                                </tr>
                                <tr>
                                    <td style="width:30%;"><strong>Current Address:</strong></td>
                                    <td><p>16905 Brooke View, Glendaburgh, Wyoming - 52932, Hungary</p></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- Medical History Column -->
                    <div class="col-lg-6 col-md-6 col-sm-12 p-lg-3 p-md-2 p-sm-1">
                        <h5 class="text-center">Medical History</h5>
                        <table class="table table-striped medical-history">
                            <tbody>
                                <tr>
                                    <td><strong>Medical Conditions:</strong></td>
                                    <td><p><!-- Backend data --></p></td>
                                </tr>
                                <tr>
                                    <td><strong>Medications:</strong></td>
                                    <td><p><!-- Backend data --></p></td>
                                </tr>
                                <tr>
                                    <td><strong>Allergies:</strong></td>
                                    <td><p><!-- Backend data --></p></td>
                                </tr>
                                <tr>
                                    <td><strong>Immunizations:</strong></td>
                                    <td><p><!-- Backend data --></p></td>
                                </tr>
                                <tr>
                                    <td><strong>Category:</strong></td>
                                    <td><p><!-- Backend data --></p></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="row">
                    <!-- Emergency Details Column -->
                    <div class="col-lg-5 col-md-5 col-sm-12 p-lg-3 p-md-2 p-sm-1">
                        <h5 class="text-center">Emergency Details</h5>
                        <table class="table table-striped emergency-details">
                            <tbody>
                                <tr>
                                    <td><strong>Emergency Contact:</strong></td>
                                    <td><p><!-- Backend data --></p></td>
                                </tr>
                                <tr>
                                    <td><strong>Relation:</strong></td>
                                    <td><p><!-- Backend data --></p></td>
                                </tr>
                                <tr>
                                    <td><strong>Civil Status:</strong></td>
                                    <td><p><!-- Backend data --></p></td>
                                </tr>
                                <tr>
                                    <td><strong>Mobile Number:</strong></td>
                                    <td><p><!-- Backend data --></p></td>
                                </tr>
                                <tr>
                                    <td><strong>Email Address:</strong></td>
                                    <td><p><!-- Backend data --></p></td>                                </tr>
                                <tr>
                                    <td><strong>Emergency Procedure:</strong></td>
                                    <td><p><!-- Backend data --></p></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- Medication Management Column -->
                    <div class="col-lg-7 col-md-7 col-sm-12 p-lg-3 p-md-2 p-sm-1">
                        <h5 class="text-center">Medication Management</h5>
                        <table class="table table-striped medication-management">
                            <thead>
                                <th style="width:30%;">Medication Name</th>
                                <th style="width:20%;">Dosage</th>
                                <th style="width:20%;">Frequency</th>
                                <th style="width:30%;">Instructions</th>
                            </thead>
                            <tbody>
                                <!-- LOOP -->
                                <tr>
                                    <td style="width:30%;"><p><!-- Backend data --></p></td>
                                    <td style="width:20%;"><p><!-- Backend data --></p></td>
                                    <td style="width:20%;"><p><!-- Backend data --></p></td>
                                    <td style="width:30%;"><p><!-- Backend data --></p></td>                                
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="row">
                    <!-- Care Needs Column -->
                    <div class="col-lg-8 col-md-8 col-sm-12 p-lg-3 p-md-2 p-sm-1">
                        <h5 class="text-center">Care Needs</h5>
                        <table class="table table-striped care-needs">                            
                            <thead>
                                <th style="width:30%;"></th>
                                <th style="width:20%;">Frequency</th>
                                <th style="width:50%;">Assistance Required</th>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="width:30%;"><strong>Mobility:</strong></td>
                                    <td style="width:20%;"><p><!-- Backend data --></p></td>
                                    <td style="width:50%;"><p><!-- Backend data --></p></td>
                                </tr>
                                <tr>
                                    <td style="width:30%;"><strong>Cognitive / Communication:</strong></td>
                                    <td style="width:20%;"><p><!-- Backend data --></p></td>
                                    <td style="width:50%;"><p><!-- Backend data --></p></td>
                                </tr>
                                <tr>
                                    <td style="width:30%;"><strong>Self-sustainability:</strong></td>
                                    <td style="width:20%;"><p><!-- Backend data --></p></td>
                                    <td style="width:50%;"><p><!-- Backend data --></p></td>
                                </tr>
                                <tr>
                                    <td style="width:30%;"><strong>Disease / Therapy Handling:</strong></td>
                                    <td style="width:20%;"><p><!-- Backend data --></p></td>
                                    <td style="width:50%;"><p><!-- Backend data --></p></td>
                                </tr>
                                <tr>
                                    <td style="width:30%;"><strong>Daily Life / Social Contact:</strong></td>
                                    <td style="width:20%;"><p><!-- Backend data --></p></td>
                                    <td style="width:50%;"><p><!-- Backend data --></p></td>
                                </tr>
                                <tr>
                                    <td style="width:30%;"><strong>Outdoor Activities:</strong></td>
                                    <td style="width:20%;"><p><!-- Backend data --></p></td>
                                    <td style="width:50%;"><p><!-- Backend data --></p></td>
                                </tr>
                                <tr>
                                    <td style="width:30%;"><strong>Household Keeping:</strong></td>
                                    <td style="width:20%;"><p><!-- Backend data --></p></td>
                                    <td style="width:50%;"><p><!-- Backend data --></p></td>
                                </tr>                           
                            </tbody>
                        </table>
                    </div>
                    <!-- Mobility Column -->
                    <div class="col-lg-4 col-md-4 col-sm-12 p-lg-3 p-md-2 p-sm-1">
                        <h5 class="text-center">Mobility</h5>
                        <table class="table table-striped mobility">
                            <tbody>
                                <tr>
                                    <td><strong>Walking Ability:</strong></td>
                                    <td><p><!-- Backend data --></p></td>                                
                                </tr>
                                <tr>
                                    <td><strong>Assistive Devices:</strong></td>
                                    <td><p><!-- Backend data --></p></td>                                
                                </tr>
                                <tr>
                                    <td><strong>Transportation Needs:</strong></td>
                                    <td><p><!-- Backend data --></p></td>                                
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Cognitive Function Column -->
                <div class="row">
                    <div class="col-lg-4 col-md-4 col-sm-12 p-lg-3 p-md-2 p-sm-1">
                        <h5 class="text-center">Cognitive Function</h5>
                        <table class="table table-striped cognitive-function">
                            <tbody>
                                <tr>
                                    <td><strong>Memory:</strong></td>
                                    <td><p><!-- Backend data --></p></td>                                
                                </tr>
                                <tr>
                                    <td><strong>Thinking Skills:</strong></td>
                                    <td><p><!-- Backend data --></p></td>                                
                                </tr>
                                <tr>
                                    <td><strong>Orientation:</strong></td>
                                    <td><p><!-- Backend data --></p></td>                                
                                </tr>
                                <tr>
                                    <td><strong>Behavior:</strong></td>
                                    <td><p><!-- Backend data --></p></td>                                
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- Emotional Well-being Column -->
                    <div class="col-lg-4 col-md-4 col-sm-12 p-lg-3 p-md-2 p-sm-1">
                        <h5 class="text-center">Emotional Well-being</h5>
                        <table class="table table-striped emotional-wellbeing">
                            <tbody>
                                <tr>
                                    <td><strong>Mood:</strong></td>
                                    <td><p><!-- Backend data --></p></td>                                
                                </tr>
                                <tr>
                                    <td><strong>Social Interactions:</strong></td>
                                    <td><p><!-- Backend data --></p></td>                                
                                </tr>
                                <tr>
                                    <td><strong>Emotional Support Need:</strong></td>
                                    <td><p><!-- Backend data --></p></td>                                
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- Assigned Care Worker Column -->
                    <div class="col-lg-4 col-md-4 col-sm-12 p-lg-3 p-md-2 p-sm-1">
                        <h5 class="text-center">Assigned Care Worker</h5>
                        <table class="table table-striped">
                            <tbody>
                                <tr>
                                    <td style="width: 20%;"><strong>Name:</strong></td>
                                    <td style="width: 80%;"><p><!-- Backend data --></p></td>                                
                                </tr>
                                <tr> 
                                    <td style="width: 100%; text-align:center;"><strong>Tasks and Responsibilities</strong></td> 
                                    <td></td>                            
                                </tr>
                                <!-- LOOP -->
                                <tr>
                                    <td style="width: 100%;"><p><!-- Backend data --></p></td> 
                                    <td></td>                            
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-12 p-lg-3 p-md-2 p-sm-1">
                        <h5 class="text-center">Documents</h5>
                        <table class="table table-striped">
                            <tbody>
                                <tr>
                                    <td style="width: 40%;"><strong>Care Service Agreement:</strong></td>
                                    <td style="width: 60%;"><p><!-- Backend data --></p></td>                                
                                </tr>
                                <tr>
                                    <td style="width: 40%;"><strong>General Careplan:</strong></td>
                                    <td style="width: 60%;"><p><!-- Backend data --></p></td>                                
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 p-lg-3 p-md-2 p-sm-1">
                        <h5 class="text-center">Signatures</h5>
                        <table class="table table-striped">
                            <tbody>
                                <tr>
                                    <td style="width: 30%;"><strong>Beneficiary Signature:</strong></td>
                                    <td style="width: 70%;"><img src="" alt="Beneficiary signature"><!-- Backend data --></td>                                
                                </tr>
                                <tr>
                                    <td style="width: 30%;"><strong>Care Worker Signature:</strong></td>
                                    <td style="width: 70%;"><img src="" alt="Care worker signature"><!-- Backend data --></td>                                
                                </tr>
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