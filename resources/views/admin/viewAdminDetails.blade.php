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
                <a href="administratorProfile" class="btn btn-secondary original-back-btn">
                    <i class="bx bx-arrow-back"></i> Back
                </a>
                <div class="mx-auto text-center" style="flex-grow: 1; font-weight: bold; font-size: 20px;">VIEW ADMINISTRATOR PROFILE DETAILS</div>
                <div>
                    <!-- Hidden Back Button -->
                    <a href="viewCareworkerProfile" class="btn btn-secondary hidden-back-btn">
                        <i class="bx bx-arrow-back"></i> Back
                    </a>
                    <a href="editAdminProfile" class="btn btn-primary" onclick="editProfile()">
                        <i class="bx bxs-edit"></i> Edit
                    </a>
                    <button class="btn btn-danger" onclick="deleteProfile()">
                        <i class="bx bxs-trash"></i> Delete
                    </button>
                </div>
            </div>
            <div class="row justify-content-center" id="profileDetails">
                <div class="row justify-content-center mb-3">
                    <div class="col-lg-8 col-md-12 col-sm-12 mb-3" id="profilePic">
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
                                            <option value="active">Active Care Manager</option>
                                            <option value="inactive">Inactive Care Manager</option>
                                        </select>
                                    </div>
                                </div>
                                <p class="text-muted mt-2 text-center text-md-start">A (Organization Role) since 00-00-0000</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <!-- Personal Details Column -->
                    <div class="col-lg-8 col-md-12 col-sm-12">
                        <h5 class="text-center">Personal Details</h5>
                        <table class="table table-striped personal-details">                            
                            <tbody>
                                <tr>
                                    <td style="width:30%;"><strong>Educational Background:</strong></td>
                                    <td><p><!-- Backend data --></p></td>
                                </tr>
                                <tr>
                                    <td style="width:30%;"><strong>Birthday:</strong></td>
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
                                    <td style="width:30%;"><strong>Religion:</strong></td>
                                    <td><p><!-- Backend data --></p></td>
                                </tr>
                                <tr>
                                    <td style="width:30%;"><strong>Nationality:</strong></td>
                                    <td><p><!-- Backend data --></p></td>
                                </tr>
                                <tr>
                                    <td style="width:30%;"><strong>Email Address:</strong></td>
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
                    <div class="col-lg-4 col-md-12 col-sm-12">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <h5 class="text-center">Documents</h5>
                                <table class="table table-striped">
                                    <tbody>
                                        <tr>
                                            <td style="width: 40%;"><strong>Government Issued ID:</strong></td>
                                            <td style="width: 60%;"><p><!-- Backend data --></p></td>                                
                                        </tr>
                                        <tr>
                                            <td style="width: 40%;"><strong>Resume / CV:</strong></td>
                                            <td style="width: 60%;"><p><!-- Backend data --></p></td>                                
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <h5 class="text-center">Government ID Numbers</h5>
                                <table class="table table-striped">
                                    <tbody>
                                        <tr>
                                            <td style="width: 40%;"><strong>SSS ID Number:</strong></td>
                                            <td style="width: 60%;"><p><!-- Backend data --></p></td>                                
                                        </tr>
                                        <tr>
                                            <td style="width: 40%;"><strong>PhilHealth ID Number:</strong></td>
                                            <td style="width: 60%;"><p><!-- Backend data --></p></td>                                
                                        </tr>
                                        <tr>
                                            <td style="width: 40%;"><strong>Pag-Ibig ID Number:</strong></td>
                                            <td style="width: 60%;"><p><!-- Backend data --></p></td>                                
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