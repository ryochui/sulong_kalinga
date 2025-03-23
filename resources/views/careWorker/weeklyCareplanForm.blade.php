<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="{{ asset('css/weeklyCareplan.css') }}">
</head>
<body>

    @include('components.userNavbar')
    @include('components.sidebar')
    
    <div class="home-section">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <a href="weeklyCareplan" class="btn btn-secondary">
                    <i class="bx bx-arrow-back"></i> Back
                </a>
                <div class="mx-auto text-center" style="flex-grow: 1; font-weight: bold; font-size: 20px;">WEEKLY CAREPLAN FORM</div>
            </div>
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="row" id="weeklyCareplanForm">
                <div class="col-12">
                    <!-- <form action="{{ route('addBeneficiary') }}" method="POST"> -->
                    <!-- <form action="" method="POST" enctype="multipart/form-data"> -->
                    <form>
                        @csrf <!-- Include CSRF token for security -->
                        <div class="row mb-1 mt-3">
                            <div class="col-12">
                                <h5 class="text-start">Personal Details</h5> <!-- Row Title -->
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-md-4 col-sm-9 position-relative">
                                <label for="selectBeneficiary" class="form-label">Select Beneficiary</label>
                                <input type="text" class="form-control" id="selectBeneficiaryInput" placeholder="Select Beneficiary" autocomplete="off" readonly >
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
                            <div class="row mb-3">
                                <div class="col-md-6 col-sm-6">
                                    <label for="condition" class="form-label">Condition</label>
                                    <input type="text" class="form-control" id="condition" name="nationality" placeholder="Enter nationality" required>
                                </div>
                                <div class="col-md-6 col-sm-6 position-relative">
                                    <label for="careManager" class="form-label">Care Manager</label>
                                    <input type="text" class="form-control" id="careManager" placeholder="Care Manager" >
                                </div>
                            </div>
                        </div>

                        <hr my-4>
                        <!-- Assessment, Vital Signs -->
                        <div class="row mb-3 mt-2">
                            <div class="col-lg-6 col-md-6 col-sm-12 text-center">
                                <label for="assessment" class="form-label"><h5>Assessment</h5></label>
                                <textarea class="form-control" id="assessment" rows="4"></textarea>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 text-center">
                                <label for="vitalSigns" class="form-label"><h5>Vital Signs</h5></label>
                                <textarea class="form-control" id="vitalSigns" rows="4"></textarea>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <!-- Mobility Section -->
                            <div class="col-lg-6 col-md-12">
                                <div class="row mb-3">
                                    <div class="col-lg-12 col-md-12">
                                        <h5 class="text-center mb-2">Mobility</h5>
                                        <!-- Row for each checkbox, label, and minutes input -->
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
                                        <div class="row mb-2">
                                            <div class="col-md-12">
                                                <label for="mobilityOthers">Others:</label>
                                                <textarea class="form-control" id="mobilityOthers" rows="2"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                    <div class="row mb-3">
                                        <div class="col-lg-12 col-md-12">
                                            <!-- Self-sustainability Section -->
                                            <h5 class="mb-2">Self-Sustainability</h5>
                                            <!-- Row for each checkbox, label, and minutes input -->
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
                                            <div class="row mb-2">
                                                <div class="col-md-12">
                                                    <label for="selfOthers">Others:</label>
                                                    <textarea class="form-control" id="selfOthers" rows="2"></textarea>
                                                </div>
                                            </div>  
                                        </div>
                                    </div>
                                </div>
                            <!-- Cognitive/Communication Section -->
                            <div class="col-lg-6 col-md-12">
                                <h5 class="text-center mb-2">Cognitive/Communication</h5>
                                <!-- Row for each checkbox, label, and minutes input -->
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
                                <div class="row mb-2">
                                    <div class="col-md-12">
                                        <label for="cognitiveOthers">Others:</label>
                                        <textarea class="form-control" id="cognitiveOthers" rows="2"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3"> <!-- 2nd ROW -->
                            <!-- Disease / Therapy Section -->
                            <div class="col-lg-6 col-md-12">
                            <h5 class="mb-2">Disease / Therapy Handling</h5>
                                <!-- Row for each checkbox, label, and minutes input -->
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
                                <div class="row mb-2">
                                    <div class="col-md-12">
                                        <label for="therapyOthers">Others:</label>
                                        <textarea class="form-control" id="therapyOthers" rows="2"></textarea>
                                    </div>
                                </div>
                            </div>                       

                            <!-- Daily life / Social Contact Section -->
                            <div class="col-lg-6 col-md-12">
                                <h5 class="mb-2">Daily Life / Social Contact</h5>
                                <!-- Row for each checkbox, label, and minutes input -->
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
                                <div class="row mb-2">
                                    <div class="col-md-12">
                                        <label for="socialOthers">Others:</label>
                                        <textarea class="form-control" id="socialOthers" rows="2"></textarea>
                                    </div>
                                </div>
                            </div>   
                        </div>

                        <!-- THIRD ROW -->
                        <div class="row mb-2">                    
                            <!-- Outdoor Activities -->
                            <div class="col-lg-6 col-md-12">
                                <h5 class="mb-2">Outdoor Activities</h5>
                                <!-- Row for each checkbox, label, and minutes input -->
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
                                <div class="row mb-2">
                                    <div class="col-md-12">
                                        <label for="outdoorOthers">Others:</label>
                                        <textarea class="form-control" id="outdoorOthers" rows="2"></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Household Keeping -->
                            <div class="col-lg-6 col-md-12">
                                <h5 class="mb-2">Household Keeping</h5>
                                <!-- Row for each checkbox, label, and minutes input -->
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
                                <div class="row mb-2">
                                    <div class="col-md-12">
                                        <label for="householdOthers">Others:</label>
                                        <textarea class="form-control" id="househldOthers" rows="2"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>          
                        
                        <div class="row mb-3 mt-2 justify-content-center">
                            <div class="col-lg-8 col-md-12 col-sm-12 text-center">
                                <label for="assessment" class="form-label"><h5>Recommendations / Evaluations</h5></label>
                                <textarea class="form-control" id="assessment" rows="4"></textarea>
                            </div>
                        </div>

                        <!-- Add more sections as needed -->

                        <!-- Submit Button -->
                        <div class="row mt-4">
                            <div class="col-12 d-flex justify-content-center align-items-center">
                                <button type="submit" class="btn btn-success btn-lg d-flex align-items-center" onclick="submitReport()">
                                    <i class='bx bxs-file-plus' style="font-size: 24px; margin-right: 5px;"></i>
                                    Save Report
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Save Beneficiary Success Modal -->
    <!-- Success Modal for Submitted Report -->
    <div class="modal fade" id="successReportModal" tabindex="-1" aria-labelledby="successReportModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h5 class="modal-title" id="successReportModalLabel">Report Submitted Successfully</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <!-- Modal Body -->
                <div class="modal-body">
                    <div class="text-center">
                        <!-- Success Icon (Optional) -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="green" class="bi bi-check-circle-fill" viewBox="0 0 16 16">
                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                        </svg>
                        <!-- Success Message -->
                        <p class="mt-3">Your report has been submitted successfully!</p>
                    </div>
                </div>
                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src=" {{ asset('js/toggleSideBar.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script>
        document.querySelector('form').addEventListener('submit', function (e) {
            e.preventDefault(); // Prevent the default form submission

            // e.preventDefault(); // Prevent the default form submission

            // Show the success modal
            const successModal = new bootstrap.Modal(document.getElementById('successReportMOdal'));
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
            filterDropdown('selectBeneficiaryInput', 'selectBeneficiaryDropdown');
            filterDropdown('genderInput', 'genderDropdown');
        });

        // document.querySelectorAll('.dropdown-item').forEach(item => {
        //     item.addEventListener('click', function (e) {
        //         e.preventDefault();
        //         const input = this.closest('.position-relative').querySelector('input[type="text"]');
        //         const hiddenInput = this.closest('.position-relative').querySelector('input[type="hidden"]');
        //         input.value = this.textContent;
        //         hiddenInput.value = this.getAttribute('data-value');
        //     });
        // });

        // document.querySelectorAll('input[type="text"]').forEach(input => {
        //     input.addEventListener('input', function () {
        //         this.value = this.value.replace(/[^a-zA-Z0-9\s]/g, ''); // Remove special characters
        //     });
        // });

        // // Validate names to allow only one hyphen per word and not at the end
        // function validateName(input) {
        //     input.value = input.value.replace(/[^a-zA-Z-]/g, ''); // Remove invalid characters
        //     input.value = input.value.replace(/-{2,}/g, '-'); // Prevent multiple consecutive hyphens
        //     input.value = input.value.replace(/^-|-$/g, ''); // Remove hyphen at the start or end
        //     const words = input.value.split(' ');
        //     input.value = words.map(word => word.replace(/-/g, (match, offset) => offset === word.indexOf('-') ? '-' : '')).join(' ');
        // }

        // // Prevent spaces in email fields
        // function preventSpaces(input) {
        //     input.value = input.value.replace(/\s/g, ''); // Remove spaces
        // }

        // // Validate Current Address to allow only alphanumeric characters, spaces, commas, periods, and hyphens
        // function validateAddress(input) {
        //     input.value = input.value.replace(/[^a-zA-Z0-9\s,.-]/g, ''); // Remove invalid characters
        // }

    </script>
</body>
</html>