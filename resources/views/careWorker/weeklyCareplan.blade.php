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
</head>
<body>

    @include('components.userNavbar')
    @include('components.sidebar')
    
    <div class="home-section">
        <h4 class="text-center mt-2">WEEKLY CARE PLAN FORM</h4>
            <div class="container-fluid">
                <div class="row mb-1" id="weeklyCareplanForm">
                    <div class="col-12">
                        <div class="row">
                            <div class="col-12">
                                <!-- Breadcrumb Navigation -->
                                <div class="breadcrumb-container">
                                    <nav aria-label="breadcrumb">
                                        <ol class="breadcrumb">
                                            <li class="breadcrumb-item" data-page="1"><a href="#">Personal Details</a></li>
                                            <li class="breadcrumb-item" data-page="2"><a href="#">Mobility</a></li>
                                            <li class="breadcrumb-item" data-page="3"><a href="#">Cognitive and Communication</a></li>
                                            <li class="breadcrumb-item" data-page="4"><a href="#">Self-Sustainability</a></li>
                                            <li class="breadcrumb-item" data-page="5"><a href="#">Disease and Therapy Handling</a></li>
                                            <li class="breadcrumb-item" data-page="6"><a href="#">Daily Life and Social Contact</a></li>
                                            <li class="breadcrumb-item" data-page="7"><a href="#">Outdoor Activities</a></li>
                                            <li class="breadcrumb-item" data-page="8"><a href="#">Household Keeping</a></li>
                                            <li class="breadcrumb-item" data-page="9"><a href="#">Evaluation</a></li>
                                        </ol>
                                    </nav>
                                </div>
                            </div>
                        </div>                        

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
                                                <label for="selectBeneficiary" class="form-label">Select Beneficiary</label>
                                                <input type="text" class="form-control" id="selectBeneficiaryInput" placeholder="Select Beneficiary" autocomplete="off" readonly>
                                                <ul class="dropdown-menu w-100" id="selectBeneficiaryDropdown">
                                                    <li><a class="dropdown-item" data-value="beneficiary1">Beneficiary 1</a></li>
                                                    <li><a class="dropdown-item" data-value="beneficiary2">Beneficiary 2</a></li>
                                                    <li><a class="dropdown-item" data-value="beneficiary3">Beneficiary 3</a></li>
                                                    <li><a class="dropdown-item" data-value="beneficiary4">Beneficiary 4</a></li>
                                                </ul>
                                                <input type="hidden" id="selectBeneficiary" name="select_beneficiary">
                                            </div>
                                            <div class="col-md-2 col-sm-3">
                                                <label for="age" class="form-label">Age</label>
                                                <input type="text" class="form-control" id="age" name="age" required>
                                            </div>
                                            <div class="col-md-3 col-sm-6">
                                                <label for="birthDate" class="form-label">Birthdate</label>
                                                <input type="date" class="form-control" id="birthDate" name="birth_date" required>
                                            </div>
                                            <div class="col-md-3 col-sm-6 position-relative">
                                                <label for="gender" class="form-label">Gender</label>
                                                <input type="text" class="form-control" id="genderInput" placeholder="Select gender" autocomplete="off">
                                                <ul class="dropdown-menu w-100" id="genderDropdown">
                                                    <li><a class="dropdown-item" data-value="Male">Male</a></li>
                                                    <li><a class="dropdown-item" data-value="Female">Female</a></li>
                                                    <li><a class="dropdown-item" data-value="Other">Other</a></li>
                                                </ul>
                                                <input type="hidden" id="gender" name="gender">
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-3 col-sm-4 position-relative">
                                                <label for="civilStatus" class="form-label">Civil Status</label>
                                                <input type="text" class="form-control" id="civilStatus" placeholder="Select civil status">
                                            </div>
                                            <div class="col-md-9 col-sm-8">
                                                <label for="address" class="form-label">Address</label>
                                                <input type="text" class="form-control" id="address" name="address" placeholder="Enter Address" required>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-6 col-sm-6">
                                                <label for="condition" class="form-label">Condition</label>
                                                <input type="text" class="form-control" id="condition" name="nationality" placeholder="Enter nationality" required>
                                            </div>
                                            <div class="col-md-6 col-sm-6 position-relative">
                                                <label for="careManager" class="form-label">Care Manager</label>
                                                <input type="text" class="form-control" id="careManager" placeholder="Care Manager">
                                            </div>
                                        </div>
                                        <hr my-4>
                                        <!-- Assessment, Vital Signs -->
                                        <div class="row mb-3 mt-2">
                                            <div class="col-lg-6 col-md-6 col-sm-12 text-center">
                                                <label for="assessment" class="form-label"><h5>Assessment</h5></label>
                                                <textarea class="form-control" id="assessment" rows="5"></textarea>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-12">
                                                <h5 class="">Vital Signs</h5>
                                                <div class="row mb-1">
                                                    <div class="col-md-6 col-sm-6">
                                                        <label for="bloodPressure" class="form-label">Blood Pressure</label>
                                                        <input type="text" class="form-control" id="bloodPressure" name="bloodPressure" placeholder="Enter Blood pressure" required>
                                                    </div>
                                                    <div class="col-md-6 col-sm-6 position-relative">
                                                        <label for="bodyTemp" class="form-label">Body Temperature</label>
                                                        <input type="text" class="form-control" id="bodyTemp" placeholder="Enter Body temperature">
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6 col-sm-6">
                                                        <label for="pulse" class="form-label">Pulse Rate</label>
                                                        <input type="text" class="form-control" id="pulse" name="pulse" placeholder="Enter Pulse rate" required>
                                                    </div>
                                                    <div class="col-md-6 col-sm-6 position-relative">
                                                        <label for="respiratory" class="form-label">Respiratory Rate</label>
                                                        <input type="text" class="form-control" id="respiratory" placeholder="Enter Respiratory rate">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-4">
                                            <div class="col-12 d-flex justify-content-end">
                                                <button type="button" class="btn btn-primary" onclick="nextPage(2)">Next <i class="bi bi-arrow-right"></i></button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Page 2: Mobility Section -->
                                    <div class="form-page" id="page2">
                                        <h5 class="text-center mb-2">Mobility</h5>
                                        <!-- Add Mobility Section Content Here -->
                                        <div class="row justify-content-center">
                                            <div class="col-lg-8 col-md-10">
                                                <div class="row mb-2">
                                                    <div class="col-md-9">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="mobility1">
                                                            <label class="form-check-label" for="mobility1">Assist/aid in sitting</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="number" class="form-control" placeholder="Mins">
                                                    </div>
                                                </div>
                                                <div class="row mb-2">
                                                    <div class="col-md-9">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="mobility2">
                                                            <label class="form-check-label" for="mobility2">Support/aid in walking and other movements</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="number" class="form-control" placeholder="Mins">
                                                    </div>
                                                </div>
                                                <div class="row mb-2">
                                                    <div class="col-md-9">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="mobility3">
                                                            <label class="form-check-label" for="mobility3">Transfer/move from bed to wheelchair</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="number" class="form-control" placeholder="Mins">
                                                    </div>
                                                </div>
                                                <div class="row mb-2">
                                                    <div class="col-md-9">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="mobility4">
                                                            <label class="form-check-label" for="mobility4">Aide in using assistive device</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="number" class="form-control" placeholder="Mins">
                                                    </div>
                                                </div>
                                                <div class="row mb-2">
                                                    <div class="col-md-9">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="mobility5">
                                                            <label class="form-check-label" for="mobility5">Assist in using the toilet</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="number" class="form-control" placeholder="Mins">
                                                    </div>
                                                </div>
                                                <div class="row mb-2">
                                                    <div class="col-md-9">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="mobility6">
                                                            <label class="form-check-label" for="mobility6">Assistance in getting to health facilities</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="number" class="form-control" placeholder="Mins">
                                                    </div>
                                                </div>
                                                <div class="row mb-2">
                                                    <div class="col-md-9">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="mobility7">
                                                            <label class="form-check-label" for="mobility7">Assist in repositioning in bed</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="number" class="form-control" placeholder="Mins">
                                                    </div>
                                                </div>
                                                <div id="mobilityOthersContainer">
                                                    <!-- Initial "Others" Input -->
                                                    <div class="row mb-2 others-row">
                                                        <div class="col-md-9">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" id="mobilityOthers">
                                                                <label class="form-check-label" for="mobilityOthers">Others</label>
                                                            </div>
                                                            <!-- Additional Input Field -->
                                                            <input type="text" class="form-control mt-2" placeholder="Enter details">
                                                        </div>
                                                        <div class="col-md-3">
                                                            <input type="number" class="form-control" placeholder="Mins">
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Add and Delete Buttons -->
                                                <div class="row mb-2">
                                                    <div class="col-12 d-flex justify-content-end">
                                                        <button type="button" class="btn btn-success btn-sm me-2" onclick="addOthersInput('mobilityOthersContainer')">
                                                            <i class='bx bx-plus-circle'></i> Add Others
                                                        </button>
                                                        <button type="button" class="btn btn-danger btn-sm" onclick="deleteOthersInput('mobilityOthersContainer')">
                                                            <i class='bx bx-trash' ></i> Delete Others
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-4">
                                            <div class="col-12 d-flex justify-content-between">
                                                <button type="button" class="btn btn-secondary" onclick="prevPage(1)"><i class="bi bi-arrow-left"></i> Previous</button>
                                                <button type="button" class="btn btn-primary" onclick="nextPage(3)">Next <i class="bi bi-arrow-right"></i></button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Page 3: Cognitive/Communication Section -->
                                    <div class="form-page" id="page3">
                                        <h5 class="text-center mb-2">Cognitive and Communication</h5>
                                        <!-- Add Cognitive/Communication Section Content Here -->
                                        <div class="row justify-content-center">
                                            <div class="col-lg-8 col-md-10">
                                                <div class="row mb-2">
                                                    <div class="col-md-9">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="cognitive1">
                                                            <label class="form-check-label" for="cognitive1">Communicate using clear and concise language</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="number" class="form-control" placeholder="Mins">
                                                    </div>
                                                </div>
                                                <div class="row mb-2">
                                                    <div class="col-md-9">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="cognitive2">
                                                            <label class="form-check-label" for="cognitive2">Use pictures, symbols, or gestures to support communication</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="number" class="form-control" placeholder="Mins">
                                                    </div>
                                                </div>
                                                <div class="row mb-2">
                                                    <div class="col-md-9">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="cognitive3">
                                                            <label class="form-check-label" for="cognitive3">Repeat important information to reinforce understanding</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="number" class="form-control" placeholder="Mins">
                                                    </div>
                                                </div>
                                                <div class="row mb-2">
                                                    <div class="col-md-9">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="cognitive4">
                                                            <label class="form-check-label" for="cognitive4">Provide prompts or cues to help recall information</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="number" class="form-control" placeholder="Mins">
                                                    </div>
                                                </div>
                                                <div class="row mb-2">
                                                    <div class="col-md-9">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="cognitive5">
                                                            <label class="form-check-label" for="cognitive5">Explore devices like tablets or smartphones with communication apps</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="number" class="form-control" placeholder="Mins">
                                                    </div>
                                                </div>
                                                <div class="row mb-2">
                                                    <div class="col-md-9">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="cognitive6">
                                                            <label class="form-check-label" for="cognitive6">Facilitate playing simple memory games</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="number" class="form-control" placeholder="Mins">
                                                    </div>
                                                </div>
                                                <div class="row mb-2">
                                                    <div class="col-md-9">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="cognitive7">
                                                            <label class="form-check-label" for="cognitive7">Work on puzzles (jigsaw or word) to improve problem-solving skills</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="number" class="form-control" placeholder="Mins">
                                                    </div>
                                                </div>
                                                <div class="row mb-2">
                                                    <div class="col-md-9">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="cognitive8">
                                                            <label class="form-check-label" for="cognitive8">Encourage reading materials that are age-appropriate and interesting (story telling)</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="number" class="form-control" placeholder="Mins">
                                                    </div>
                                                </div>
                                                <div class="row mb-2">
                                                    <div class="col-md-9">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="cognitive9">
                                                            <label class="form-check-label" for="cognitive9">Use music to stimulate memories and evoke emotions.</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="number" class="form-control" placeholder="Mins">
                                                    </div>
                                                </div>
                                                <div class="row mb-2">
                                                    <div class="col-md-9">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="cognitive10">
                                                            <label class="form-check-label" for="cognitive10">Explore creative activities to express thoughts and feelings (Art-therapy) such as drawing and painting</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="number" class="form-control" placeholder="Mins">
                                                    </div>
                                                </div>
                                                <div class="row mb-2">
                                                    <div class="col-md-9">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="cognitive11">
                                                            <label class="form-check-label" for="cognitive11">Encourage to participate in singing groups or sing along to favorite songs.</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="number" class="form-control" placeholder="Mins">
                                                    </div>
                                                </div>
                                                <div class="row mb-2">
                                                    <div class="col-md-9">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="cognitive12">
                                                            <label class="form-check-label" for="cognitive12">Writing: Keeping a journal, writing short stories, or poetry.</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="number" class="form-control" placeholder="Mins">
                                                    </div>
                                                </div>
                                                <div class="row mb-2">
                                                    <div class="col-md-9">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="cognitive13">
                                                            <label class="form-check-label" for="cognitive13">Scrapbooking:Collecting and organizing memories in a scrapbook.</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="number" class="form-control" placeholder="Mins">
                                                    </div>
                                                </div>
                                                <div class="row mb-2">
                                                    <div class="col-md-9">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="cognitive14">
                                                            <label class="form-check-label" for="cognitive14">Knitting and Crocheting: Creating items with yarn.</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="number" class="form-control" placeholder="Mins">
                                                    </div>
                                                </div>
                                                <div class="row mb-2">
                                                    <div class="col-md-9">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="cognitive15">
                                                            <label class="form-check-label" for="cognitive15">Label items in the home with clear and simple labels.</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="number" class="form-control" placeholder="Mins">
                                                    </div>
                                                </div>
                                                <div class="row mb-2">
                                                    <div class="col-md-9">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="cognitive16">
                                                            <label class="form-check-label" for="cognitive16">Maintain a consistent daily routine to help the individual feel more oriented.</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="number" class="form-control" placeholder="Mins">
                                                    </div>
                                                </div>
                                                <div class="row mb-2">
                                                    <div class="col-md-9">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="cognitive17">
                                                            <label class="form-check-label" for="cognitive17">Minimize clutter to reduce confusion and overwhelm.</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="number" class="form-control" placeholder="Mins">
                                                    </div>
                                                </div>
                                                <div class="row mb-2">
                                                    <div class="col-md-9">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="cognitive18">
                                                            <label class="form-check-label" for="cognitive18">Ensure adequate natural light to help orientation and mood.</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="number" class="form-control" placeholder="Mins">
                                                    </div>
                                                </div>
                                                <div id="cognitiveOthersContainer">
                                                    <!-- Initial "Others" Input -->
                                                    <div class="row mb-2 others-row">
                                                        <div class="col-md-9">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" id="cognitiveOthers">
                                                                <label class="form-check-label" for="cognitiveOthers">Others</label>
                                                            </div>
                                                            <!-- Additional Input Field -->
                                                            <input type="text" class="form-control mt-2" placeholder="Enter details">
                                                        </div>
                                                        <div class="col-md-3">
                                                            <input type="number" class="form-control" placeholder="Mins">
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Add and Delete Buttons -->
                                                <div class="row mb-2">
                                                    <div class="col-12 d-flex justify-content-end">
                                                        <button type="button" class="btn btn-success btn-sm me-2" onclick="addOthersInput('cognitiveOthersContainer')">
                                                            <i class='bx bx-plus-circle'></i> Add Others
                                                        </button>
                                                        <button type="button" class="btn btn-danger btn-sm" onclick="deleteOthersInput('cognitiveOthersContainer')">
                                                            <i class='bx bx-trash' ></i> Delete Others
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-4">
                                            <div class="col-12 d-flex justify-content-between">
                                                <button type="button" class="btn btn-secondary" onclick="prevPage(2)"><i class="bi bi-arrow-left"></i> Previous</button>
                                                <button type="button" class="btn btn-primary" onclick="nextPage(4)">Next <i class="bi bi-arrow-right"></i></button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Page 4: Self-sustainability Section -->
                                    <div class="form-page" id="page4">
                                        <h5 class="text-center mb-2">Self-Sustainability</h5>
                                        <!-- Add Self-sustainability Section Content Here -->
                                        <div class="row justify-content-center">
                                            <div class="col-lg-8 col-md-10">
                                                <div class="row mb-2">
                                                    <div class="col-md-9">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="self1">
                                                            <label class="form-check-label" for="self1">Hand washing</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="number" class="form-control" placeholder="Mins">
                                                    </div>
                                                </div>
                                                <div class="row mb-2">
                                                    <div class="col-md-9">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="self2">
                                                            <label class="form-check-label" for="self2">Clombing (Pagnapakhty)</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="number" class="form-control" placeholder="Mins">
                                                    </div>
                                                </div>
                                                <div class="row mb-2">
                                                    <div class="col-md-9">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="self3">
                                                            <label class="form-check-label" for="self3">Tooth brushing</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="number" class="form-control" placeholder="Mins">
                                                    </div>
                                                </div>
                                                <div class="row mb-2">
                                                    <div class="col-md-9">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="self4">
                                                            <label class="form-check-label" for="self4">Nail clipping (Pagbakko sa kalo)</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="number" class="form-control" placeholder="Mins">
                                                    </div>
                                                </div>
                                                <div class="row mb-2">
                                                    <div class="col-md-9">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="self5">
                                                            <label class="form-check-label" for="self5">Changing clothes (Pagnapali ng damit)</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="number" class="form-control" placeholder="Mins">
                                                    </div>
                                                </div>
                                                <div class="row mb-2">
                                                    <div class="col-md-9">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="self6">
                                                            <label class="form-check-label" for="self6">Perineal care</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="number" class="form-control" placeholder="Mins">
                                                    </div>
                                                </div>
                                                <div class="row mb-2">
                                                    <div class="col-md-9">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="self7">
                                                            <label class="form-check-label" for="self7">Bathing (Pagnapaligo)</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="number" class="form-control" placeholder="Mins">
                                                    </div>
                                                </div>
                                                <div class="row mb-2">
                                                    <div class="col-md-9">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="self8">
                                                            <label class="form-check-label" for="self8">Diaper Changing (Pagnapalit ng diaper)</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="number" class="form-control" placeholder="Mins">
                                                    </div>
                                                </div>
                                                <div class="row mb-2">
                                                    <div class="col-md-9">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="self9">
                                                            <label class="form-check-label" for="self9">Feeding (Pagnapakain)</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="number" class="form-control" placeholder="Mins">
                                                    </div>
                                                </div>
                                                <div id="selfOthersContainer">
                                                    <!-- Initial "Others" Input -->
                                                    <div class="row mb-2 others-row">
                                                        <div class="col-md-9">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" id="selfOthers">
                                                                <label class="form-check-label" for="selfOthers">Others</label>
                                                            </div>
                                                            <!-- Additional Input Field -->
                                                            <input type="text" class="form-control mt-2" placeholder="Enter details">
                                                        </div>
                                                        <div class="col-md-3">
                                                            <input type="number" class="form-control" placeholder="Mins">
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Add and Delete Buttons -->
                                                <div class="row mb-2">
                                                    <div class="col-12 d-flex justify-content-end">
                                                        <button type="button" class="btn btn-success btn-sm me-2" onclick="addOthersInput('selfOthersContainer')">
                                                            <i class='bx bx-plus-circle'></i> Add Others
                                                        </button>
                                                        <button type="button" class="btn btn-danger btn-sm" onclick="deleteOthersInput('selfOthersContainer')">
                                                            <i class='bx bx-trash' ></i> Delete Others
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-4">
                                            <div class="col-12 d-flex justify-content-between">
                                                <button type="button" class="btn btn-secondary" onclick="prevPage(3)"><i class="bi bi-arrow-left"></i> Previous</button>
                                                <button type="button" class="btn btn-primary" onclick="nextPage(5)">Next <i class="bi bi-arrow-right"></i></button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Page 5: Disease and Therapy Handling Section -->
                                    <div class="form-page" id="page5">
                                        <h5 class="text-center mb-2">Disease and Therapy Handling</h5>
                                        <!-- Add Disease and Therapy Section Content Here -->
                                        <div class="row justify-content-center">
                                            <div class="col-lg-8 col-md-10">
                                                <div class="row mb-2">
                                                    <div class="col-md-9">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="therapy1">
                                                            <label class="form-check-label" for="therapy1">Ensure that the individual is taking medications as prescribed and understanding their purpose.</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="number" class="form-control" placeholder="Mins">
                                                    </div>
                                                </div>
                                                <div class="row mb-2">
                                                    <div class="col-md-9">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="therapy2">
                                                            <label class="form-check-label" for="therapy2">Use medication reminders, pill organizers, or caregiver assistance to help with medication adherence.</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="number" class="form-control" placeholder="Mins">
                                                    </div>
                                                </div>
                                                <div class="row mb-2">
                                                    <div class="col-md-9">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="therapy3">
                                                            <label class="form-check-label" for="therapy3">Store medications safely and out of reach to prevent accidental overdose or misuse.</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="number" class="form-control" placeholder="Mins">
                                                    </div>
                                                </div>
                                                <div class="row mb-2">
                                                    <div class="col-md-9">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="therapy4">
                                                            <label class="form-check-label" for="therapy4">Back care (light massage).</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="number" class="form-control" placeholder="Mins">
                                                    </div>
                                                </div>
                                                <div class="row mb-2">
                                                    <div class="col-md-9">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="therapy5">
                                                            <label class="form-check-label" for="therapy5">Breating Exercise.</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="number" class="form-control" placeholder="Mins">
                                                    </div>
                                                </div>
                                                <div class="row mb-2">
                                                    <div class="col-md-9">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="therapy6">
                                                            <label class="form-check-label" for="therapy6">Light stretching / exercise.</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="number" class="form-control" placeholder="Mins">
                                                    </div>
                                                </div>
                                                <div id="therapyOthersContainer">
                                                    <!-- Initial "Others" Input -->
                                                    <div class="row mb-2 others-row">
                                                        <div class="col-md-9">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" id="therapyOthers">
                                                                <label class="form-check-label" for="therapyOthers">Others</label>
                                                            </div>
                                                            <!-- Additional Input Field -->
                                                            <input type="text" class="form-control mt-2" placeholder="Enter details">
                                                        </div>
                                                        <div class="col-md-3">
                                                            <input type="number" class="form-control" placeholder="Mins">
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Add and Delete Buttons -->
                                                <div class="row mb-2">
                                                    <div class="col-12 d-flex justify-content-end">
                                                        <button type="button" class="btn btn-success btn-sm me-2" onclick="addOthersInput('therapyOthersContainer')">
                                                            <i class='bx bx-plus-circle'></i> Add Others
                                                        </button>
                                                        <button type="button" class="btn btn-danger btn-sm" onclick="deleteOthersInput('therapyOthersContainer')">
                                                            <i class='bx bx-trash' ></i> Delete Others
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-4">
                                            <div class="col-12 d-flex justify-content-between">
                                                <button type="button" class="btn btn-secondary" onclick="prevPage(4)"><i class="bi bi-arrow-left"></i> Previous</button>
                                                <button type="button" class="btn btn-primary" onclick="nextPage(6)">Next <i class="bi bi-arrow-right"></i></button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Page 6: Daily Life and Social Contact Section -->
                                    <div class="form-page" id="page6">
                                        <h5 class="text-center mb-2">Daily Life and Social Contact</h5>
                                        <!-- Add Daily Life and Social Contact Section Content Here -->
                                        <div class="row justify-content-center">
                                            <div class="col-lg-8 col-md-10">
                                                <div class="row mb-2">
                                                    <div class="col-md-9">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="social1">
                                                            <label class="form-check-label" for="social1">Assist in attending Senior Citizens activities.</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="number" class="form-control" placeholder="Mins">
                                                    </div>
                                                </div>
                                                <div class="row mb-2">
                                                    <div class="col-md-9">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="social2">
                                                            <label class="form-check-label" for="social2">Assist in going to the Senior Citizens Day Center.</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="number" class="form-control" placeholder="Mins">
                                                    </div>
                                                </div>
                                                <div class="row mb-2">
                                                    <div class="col-md-9">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="social3">
                                                            <label class="form-check-label" for="social3">Encourage visits from family, friends, or caregivers to provide companionship and emotional support.</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="number" class="form-control" placeholder="Mins">
                                                    </div>
                                                </div>
                                                <div class="row mb-2">
                                                    <div class="col-md-9">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="social4">
                                                            <label class="form-check-label" for="social4">Arrange regular phone calls or other communication mediums with loved ones to maintain social connections.</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="number" class="form-control" placeholder="Mins">
                                                    </div>
                                                </div>
                                                <div class="row mb-2">
                                                    <div class="col-md-9">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="social5">
                                                            <label class="form-check-label" for="social5">Connect with support groups for individuals with chronic diseases.</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="number" class="form-control" placeholder="Mins">
                                                    </div>
                                                </div>
                                                <div id="socialOthersContainer">
                                                    <!-- Initial "Others" Input -->
                                                    <div class="row mb-2 others-row">
                                                        <div class="col-md-9">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" id="socialOthers">
                                                                <label class="form-check-label" for="socialOthers">Others</label>
                                                            </div>
                                                            <!-- Additional Input Field -->
                                                            <input type="text" class="form-control mt-2" placeholder="Enter details">
                                                        </div>
                                                        <div class="col-md-3">
                                                            <input type="number" class="form-control" placeholder="Mins">
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Add and Delete Buttons -->
                                                <div class="row mb-2">
                                                    <div class="col-12 d-flex justify-content-end">
                                                        <button type="button" class="btn btn-success btn-sm me-2" onclick="addOthersInput('socialOthersContainer')">
                                                            <i class='bx bx-plus-circle'></i> Add Others
                                                        </button>
                                                        <button type="button" class="btn btn-danger btn-sm" onclick="deleteOthersInput('socialOthersContainer')">
                                                            <i class='bx bx-trash' ></i> Delete Others
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-4">
                                            <div class="col-12 d-flex justify-content-between">
                                                <button type="button" class="btn btn-secondary" onclick="prevPage(5)"><i class="bi bi-arrow-left"></i> Previous</button>
                                                <button type="button" class="btn btn-primary" onclick="nextPage(7)">Next <i class="bi bi-arrow-right"></i></button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Page 7: Outdoor Activities Section -->
                                    <div class="form-page" id="page7">
                                        <h5 class="text-center mb-2">Outdoor Activities</h5>
                                        <!-- Add Outdoor Activities Section Content Here -->
                                        <div class="row justify-content-center">
                                            <div class="col-lg-8 col-md-10">
                                                <div class="row mb-2">
                                                    <div class="col-md-9">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="outdoor1">
                                                            <label class="form-check-label" for="outdoor1">Going outside for fresh air and sunlight</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="number" class="form-control" placeholder="Mins">
                                                    </div>
                                                </div>
                                                <div class="row mb-2">
                                                    <div class="col-md-9">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="outdoor2">
                                                            <label class="form-check-label" for="outdoor2">Walking in parks and other areas in the community</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="number" class="form-control" placeholder="Mins">
                                                    </div>
                                                </div>
                                                <div class="row mb-2">
                                                    <div class="col-md-9">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="outdoor3">
                                                            <label class="form-check-label" for="outdoor3">Encourage / Assist in spending time outdoors</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="number" class="form-control" placeholder="Mins">
                                                    </div>
                                                </div>
                                                <div class="row mb-2">
                                                    <div class="col-md-9">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="outdoor4">
                                                            <label class="form-check-label" for="outdoor4">Gardening activities</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="number" class="form-control" placeholder="Mins">
                                                    </div>
                                                </div>
                                                <div id="outdoorOthersContainer">
                                                    <!-- Initial "Others" Input -->
                                                    <div class="row mb-2 others-row">
                                                        <div class="col-md-9">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" id="outdoorOthers=">
                                                                <label class="form-check-label" for="outdoorOthers">Others</label>
                                                            </div>
                                                            <!-- Additional Input Field -->
                                                            <input type="text" class="form-control mt-2" placeholder="Enter details">
                                                        </div>
                                                        <div class="col-md-3">
                                                            <input type="number" class="form-control" placeholder="Mins">
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Add and Delete Buttons -->
                                                <div class="row mb-2">
                                                    <div class="col-12 d-flex justify-content-end">
                                                        <button type="button" class="btn btn-success btn-sm me-2" onclick="addOthersInput('outdoorOthersContainer')">
                                                            <i class='bx bx-plus-circle'></i> Add Others
                                                        </button>
                                                        <button type="button" class="btn btn-danger btn-sm" onclick="deleteOthersInput('outdoorOthersContainer')">
                                                            <i class='bx bx-trash' ></i> Delete Others
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-4">
                                            <div class="col-12 d-flex justify-content-between">
                                                <button type="button" class="btn btn-secondary" onclick="prevPage(6)"><i class="bi bi-arrow-left"></i> Previous</button>
                                                <button type="button" class="btn btn-primary" onclick="nextPage(8)">Next <i class="bi bi-arrow-right"></i></button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Page 8: Household Keeping Section -->
                                    <div class="form-page" id="page8">
                                        <h5 class="text-center mb-2">Household Keeping</h5>
                                        <!-- Household Keeping Section Content Here -->
                                        <div class="row justify-content-center">
                                            <div class="col-lg-8 col-md-10">
                                                <div class="row"><h6>Regular Cleaning:</h6></div>
                                                <div class="row mb-2">
                                                    <div class="col-md-9">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="household1">
                                                            <label class="form-check-label" for="household1">Daily Task: Talk with family members / Facilitate cleaning surfaces, dusting, mopping with the family members.</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="number" class="form-control" placeholder="Mins">
                                                    </div>
                                                </div>
                                                <div class="row mb-2">
                                                    <div class="col-md-9">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="household2">
                                                            <label class="form-check-label" for="household2">Weekly Task: Assist / Facilitate family members in changing bed linens, and cleaning bathrooms, and laundry.</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="number" class="form-control" placeholder="Mins">
                                                    </div>
                                                </div>
                                                <div class="row"><h6>Meal Preparation:</h6></div>
                                                <div class="row mb-2">
                                                    <div class="col-md-9">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="household3">
                                                            <label class="form-check-label" for="household3">Cooking: Preparing meals that are easy to eat and nutritious.</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="number" class="form-control" placeholder="Mins">
                                                    </div>
                                                </div>
                                                <div class="row mb-2">
                                                    <div class="col-md-9">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="household4">
                                                            <label class="form-check-label" for="household4">Dishwashing: Assist family members / Facilitate washing dishes and cleaning up after meals.</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="number" class="form-control" placeholder="Mins">
                                                    </div>
                                                </div>
                                                <div class="row"><h6>Laundry:</h6></div>
                                                <div class="row mb-2">
                                                    <div class="col-md-9">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="household5">
                                                            <label class="form-check-label" for="household5">Washing and drying laundry (towel & sinul-out la nga bado).</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="number" class="form-control" placeholder="Mins">
                                                    </div>
                                                </div>
                                                <div class="row mb-2">
                                                    <div class="col-md-9">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="household6">
                                                            <label class="form-check-label" for="household6">Folding and ironing clothes if needed.</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="number" class="form-control" placeholder="Mins">
                                                    </div>
                                                </div>
                                                <div class="row mb-2">
                                                    <div class="col-md-9">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="household7">
                                                            <label class="form-check-label" for="household7">Organizing clothes in closets or drawers.</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="number" class="form-control" placeholder="Mins">
                                                    </div>
                                                </div>
                                                <div id="householdOthersContainer">
                                                    <!-- Initial "Others" Input -->
                                                    <div class="row mb-2 others-row">
                                                        <div class="col-md-9">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" id="househldOthers">
                                                                <label class="form-check-label" for="householdOthers">Others</label>
                                                            </div>
                                                            <!-- Additional Input Field -->
                                                            <input type="text" class="form-control mt-2" placeholder="Enter details">
                                                        </div>
                                                        <div class="col-md-3">
                                                            <input type="number" class="form-control" placeholder="Mins">
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Add and Delete Buttons -->
                                                <div class="row mb-2">
                                                    <div class="col-12 d-flex justify-content-end">
                                                        <button type="button" class="btn btn-success btn-sm me-2" onclick="addOthersInput('householdOthersContainer')">
                                                            <i class='bx bx-plus-circle'></i> Add Others
                                                        </button>
                                                        <button type="button" class="btn btn-danger btn-sm" onclick="deleteOthersInput('householdOthersContainer')">
                                                            <i class='bx bx-trash' ></i> Delete Others
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-4">
                                            <div class="col-12 d-flex justify-content-between">
                                                <button type="button" class="btn btn-secondary" onclick="prevPage(7)"><i class="bi bi-arrow-left"></i> Previous</button>
                                                <button type="button" class="btn btn-primary" onclick="nextPage(9)">Next <i class="bi bi-arrow-right"></i></button>
                                            </div>
                                        </div>
                                    </div>                        

                                    <!-- Page 9: Evaluation and Submit -->
                                    <div class="form-page" id="page9">
                                        <div class="row mb-3 mt-2 justify-content-center">
                                            <div class="col-lg-8 col-md-12 col-sm-12 text-center">
                                                <label for="assessment" class="form-label"><h5>Recommendations and Evaluations</h5></label>
                                                <textarea class="form-control" id="assessment" rows="6"></textarea>
                                            </div>
                                        </div>
                                        <div class="row mt-4">
                                            <div class="col-12 d-flex justify-content-center align-items-center">
                                                <button type="submit" class="btn btn-success btn-lg d-flex align-items-center" onclick="submitReport()">
                                                    <i class='bx bxs-file-plus' style="font-size: 24px; margin-right: 5px; margin-right: 5px;"></i>
                                                    Save Report
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

    <script src=" {{ asset('js/toggleSideBar.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script>
        let currentPage = 1;
        function showPage(pageNumber) {
            // Hide all pages
            document.querySelectorAll('.form-page').forEach(page => {
                page.classList.remove('active');
            });
            // Show the selected page
            document.getElementById(`page${pageNumber}`).classList.add('active');
            // Update breadcrumb
            document.querySelectorAll('.breadcrumb-item').forEach(item => {
                item.classList.remove('active');
            });
            const activeItem = document.querySelector(`.breadcrumb-item[data-page="${pageNumber}"]`);
            activeItem.classList.add('active');

            // Auto-scroll to the active breadcrumb item
            const breadcrumbContainer = document.querySelector('.breadcrumb-container');
            const breadcrumbItem = activeItem;
            breadcrumbContainer.scrollTo({
                left: breadcrumbItem.offsetLeft - (breadcrumbContainer.offsetWidth / 2) + (breadcrumbItem.offsetWidth / 2),
                behavior: 'smooth' // Smooth scrolling
            });
        }

        function nextPage(next) {
            if (next <= 9) {
                currentPage = next;
                showPage(currentPage);
            }
        }

        function prevPage(prev) {
            if (prev >= 1) {
                currentPage = prev;
                showPage(currentPage);
            }
        }

        // Breadcrumb Navigation
        document.querySelectorAll('.breadcrumb-item').forEach(item => {
            item.addEventListener('click', (e) => {
                e.preventDefault();
                const pageNumber = item.getAttribute('data-page');
                showPage(pageNumber);
            });
        });

        // Initialize first page
        showPage(1);
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