<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Beneficiary Profiles</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
        }
        .page-break {
            page-break-after: always;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            color: #2a5885;
            font-size: 20px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .profile {
            margin-bottom: 30px;
        }
        .profile-header {
            background-color: #f5f5f5;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        .profile-header h2 {
            margin: 0;
            font-size: 16px;
            color: #2a5885;
        }
        .info-section {
            margin-bottom: 15px;
        }
        .info-section h3 {
            font-size: 14px;
            margin: 0 0 5px 0;
            border-bottom: 1px solid #eee;
            padding-bottom: 3px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }
        .info-item {
            margin-bottom: 5px;
        }
        .info-label {
            font-weight: bold;
            color: #555;
        }
        .status-active {
            color: green;
            font-weight: bold;
        }
        .status-inactive {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Sulong Kalinga - Beneficiary Profiles</h1>
        <p>Export Date: {{ $exportDate }}</p>
    </div>

    @foreach($beneficiaries as $index => $beneficiary)
        <div class="profile">
            <div class="profile-header">
                <h2>{{ $beneficiary->first_name }} {{ $beneficiary->last_name }}</h2>
            </div>
            
            <div class="info-section">
                <h3>Personal Information</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Category:</span> 
                        {{ $beneficiary->category->category_name }}
                    </div>
                    <div class="info-item">
                        <span class="info-label">Status:</span> 
                        <span class="{{ $beneficiary->status->status_name == 'Active' ? 'status-active' : 'status-inactive' }}">
                            {{ $beneficiary->status->status_name }}
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Mobile:</span> 
                        {{ $beneficiary->mobile }}
                    </div>
                    <div class="info-item">
                        <span class="info-label">Date of Birth:</span> 
                        {{ $beneficiary->birthday ? date('F j, Y', strtotime($beneficiary->birthday)) : 'Not provided' }}
                    </div>
                    <div class="info-item">
                        <span class="info-label">Gender:</span> 
                        {{ $beneficiary->gender ?? 'Not provided' }}
                    </div>
                    <div class="info-item">
                        <span class="info-label">Beneficiary ID:</span> 
                        {{ $beneficiary->beneficiary_id }}
                    </div>
                </div>
            </div>
            
            <div class="info-section">
                <h3>Address Information</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Street Address:</span> 
                        {{ $beneficiary->street_address ?? 'Not provided' }}
                    </div>
                    <div class="info-item">
                        <span class="info-label">Barangay:</span> 
                        {{ $beneficiary->barangay->barangay_name }}
                    </div>
                    <div class="info-item">
                        <span class="info-label">Municipality:</span> 
                        {{ $beneficiary->municipality->municipality_name }}
                    </div>
                </div>
            </div>
            
            @if($beneficiary->generalCarePlan)
            <div class="info-section">
                <h3>Care Plan Information</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Mobility:</span> 
                        {{ $beneficiary->generalCarePlan->mobility->mobility_type ?? 'Not specified' }}
                    </div>
                    <div class="info-item">
                        <span class="info-label">Cognitive Function:</span> 
                        {{ $beneficiary->generalCarePlan->cognitiveFunction->cognitive_function_type ?? 'Not specified' }}
                    </div>
                </div>
            </div>
            @endif
        </div>
        
        @if(!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach
</body>
</html>