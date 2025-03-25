<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="{{ asset('css/viewWeeklyCareplan.css') }}">
</head>
<body>

    @include('components.userNavbar')
    @include('components.sidebar')

    
    <div class="home-section">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <!-- Original Back Button -->
                <a href="familyProfile" class="btn btn-secondary original-back-btn">
                    <i class="bx bx-arrow-back"></i> Back
                </a>

                <div class="mx-auto text-center" style="flex-grow: 1; font-weight: bold; font-size: 20px;">VIEW WEEKLY CAREPLAN DETAILS</div>

                <!-- Edit and Delete Buttons -->
                <div>
                    <!-- Hidden Back Button -->
                    <a href="familyProfile" class="btn btn-secondary hidden-back-btn">
                        <i class="bx bx-arrow-back"></i> Back
                    </a>
                    <!-- Edit Button with Routing -->
                    <form action="" method="POST" style="display:inline;">
                        @csrf
                        <input type="hidden" name="family_member_id" value="">
                        <button type="submit" class="btn btn-primary">
                        <i class="bx bxs-edit"></i> Edit
                        </button>
                    </form>
                    <button class="btn btn-danger">
                        <i class="bx bxs-trash"></i> Delete
                    </button>
                </div>
            </div>
            <div class="row mb-3" id="weeklyCareplanDetails">
                <div class="col-12">
                    <div class="row personal-details">
                        <div class="col-12">
                            <div class="row mb-1 mt-2">
                            <div class="col-12">
                                <h5>Personal Details</h5>
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-md-4 col-sm-9 position-relative">
                                <label for="benficiary" class="form-label">Beneficiary Name</label>
                                <input type="text" class="form-control" id="beneficiary" readonly data-bs-toggle="tooltip" title="Edit in General Care Plan">    
                            </div>
                            <div class="col-md-2 col-sm-3">
                                <label for="age" class="form-label">Age</label>
                                <input type="text" class="form-control" id="age" readonly data-bs-toggle="tooltip" title="Edit in General Care Plan">                                            </div>
                            <div class="col-md-3 col-sm-6">
                                <label for="birthDate" class="form-label">Birthdate</label>
                                <input type="text" class="form-control" id="birthDate" readonly data-bs-toggle="tooltip" title="Edit in General Care Plan">                                            </div>
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
                            <div class="col-md-6 col-sm-8">
                                <label for="address" class="form-label">Address</label>
                                <input type="text" class="form-control" id="address" readonly data-bs-toggle="tooltip" title="Edit in General Care Plan">
                                </div>
                            <div class="col-md-3 col-sm-12">
                                <label for="condition" class="form-label">Medical Conditions</label>
                                <input type="text" class="form-control" id="medicalConditions" readonly data-bs-toggle="tooltip" title="Edit in General Care Plan">
                                </div>
                        </div>
                        </div>
                    </div>

                    <hr class="my-4">
                    <!-- ASSESSMENT AND EVALIATIONS ROW -->
                    <div class="row">
                        <div class="col-md-7 col-sm-12">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <th>Assessment</th>
                                        <th>Vital Signs</th>                                       
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td rowspan="4">
                                                <!-- Assessment Data -->
                                            </td>
                                            <td>
                                                Blood Pressure:
                                            </td> 
                                        </tr>
                                        <tr>
                                            <td>
                                                Body Temperature:
                                            </td>                                            
                                        </tr>
                                        <tr>
                                            <td>
                                                Pulse Rate:
                                            </td>                                           
                                        </tr>
                                        <tr>
                                            <td>
                                                Respiratory Rate:
                                            </td>                                            
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-5 col-sm-12">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead><th>Evaluation and Recommendation</th></thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <!-- Evaluation and Recommendation Data -->
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">
                    <!-- CARE NEEDS AND INTERVENTIONS IMPLEMENTED ROW -->
                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <th class="care-needs-column">Care Needs</th>
                                        <th class="interventions-column">Interventions Impelemented</th>
                                        <th class="minute-column minutes-header"></th>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td rowspan="2" class="care-needs-column"><!-- Adjust rowspan based on loop -->
                                                <h6>MOBILITY</h6>
                                            </td>
                                        </tr>
                                            <tr> <!-- LOOP THIS FOR INTERVENTIONS IN MOBILITY -->
                                                <td class="interventions-column">Aid in Sitting  asd  asd asdasd asda sasd asdasd asd asd asd asd asd asd asdasdasdasd asd asd asd asd asd as</td>
                                                <td class="minute-column"> </td>
                                            </tr>
                                        <tr>
                                            <td rowspan="2" class="care-needs-column"><!-- Adjust rowspan based on loop +1 -->
                                                <h6>COGNITIVE / COMMUNICATION</h6>
                                            </td>
                                        </tr>
                                            <tr> <!-- LOOP THIS FOR INTERVENTIONS IN COGNITIVE -->
                                                <td class="interventions-column">Aid in Sitting asdasd asd  asd asdasd asda sasd asdasd asd asd asd asd asd asd asdasdasdasd asd asd asd asd asd as</td>
                                                <td class="minute-column"> </td>
                                            </tr>                        
                                        <tr>
                                            <td rowspan="2" class="care-needs-column"><!-- Adjust rowspan based on loop +1 -->
                                                <h6>SELF-SUSTAINABILITY</h6>
                                            </td>
                                        </tr>
                                            <tr> <!-- LOOP THIS FOR INTERVENTIONS IN SELF-SUSTAINABILITY -->
                                                <td class="interventions-column">Aid in Sitting</td>
                                                <td class="minute-column"> </td>
                                            </tr>
                                        <tr>
                                            <td rowspan="2" class="care-needs-column"><!-- Adjust rowspan based on loop +1 -->
                                                <h6>DISEASE / THERAPY HANDLING</h6>
                                            </td>
                                        </tr>
                                            <tr> <!-- LOOP THIS FOR INTERVENTIONS IN DISEASE / THERAPY -->
                                                <td class="interventions-column">Aid in Sitting</td>
                                                <td class="minute-column"> </td>
                                            </tr>
                                        <tr>
                                            <td rowspan="2" class="care-needs-column"><!-- Adjust rowspan based on loop +1 -->
                                                <h6>DAILY LIFE / SOCIAL CONTACT</h6>
                                            </td>
                                        </tr>
                                            <tr> <!-- LOOP THIS FOR INTERVENTIONS IN DAILY LIFE / SOCIAL CONTACT -->
                                                <td class="interventions-column">Aid in Sitting</td>
                                                <td class="minute-column"> </td>
                                            </tr>
                                        <tr>
                                            <td rowspan="2" class="care-needs-column"><!-- Adjust rowspan based on loop +1 -->
                                                <h6>OUTDOOR ACTIVITIES</h6>
                                            </td>
                                        </tr>
                                            <tr> <!-- LOOP THIS FOR INTERVENTIONS IN OUTDOOR ACTIVITIES -->
                                                <td class="interventions-column">Aid in Sitting</td>
                                                <td class="minute-column"> </td>
                                            </tr>
                                        <tr>
                                            <td rowspan="2" class="care-needs-column"><!-- Adjust rowspan based on loop +1 -->
                                                <h6>HOUSEHOLD KEEPING</h6>
                                            </td>
                                        </tr>
                                            <tr> <!-- LOOP THIS FOR INTERVENTIONS IN HOUSEHOLD KEEPING -->
                                                <td class="interventions-column">Aid in Sitting</td>
                                                <td class="minute-column"> </td>
                                            </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src=" {{ asset('js/toggleSideBar.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
   
</body>
</html>