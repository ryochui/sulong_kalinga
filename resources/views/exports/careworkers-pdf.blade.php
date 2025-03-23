<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Care Worker Profiles</title>
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
        .beneficiaries-list {
            margin-top: 5px;
        }
        .beneficiary-item {
            padding: 3px 0;
            border-bottom: 1px dotted #eee;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Sulong Kalinga - Care Worker Profiles</h1>
        <p>Export Date: {{ $exportDate }}</p>
    </div>

    @foreach($careworkers as $index => $careworker)
        <div class="profile">
            <div class="profile-header">
                <h2>{{ $careworker->first_name }} {{ $careworker->last_name }}</h2>
            </div>
            
            <div class="info-section">
                <h3>Personal Information</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Status:</span> 
                        <span class="{{ $careworker->volunteer_status == 'Active' ? 'status-active' : 'status-inactive' }}">
                            {{ $careworker->volunteer_status }}
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Mobile:</span> 
                        {{ $careworker->mobile ?? 'Not provided' }}
                    </div>
                    <div class="info-item">
                        <span class="info-label">Email:</span> 
                        {{ $careworker->email ?? 'Not provided' }}
                    </div>
                    <div class="info-item">
                        <span class="info-label">Municipality:</span> 
                        {{ $careworker->municipality->municipality_name ?? 'Not assigned' }}
                    </div>
                    <div class="info-item">
                        <span class="info-label">Care Worker ID:</span> 
                        {{ $careworker->id }}
                    </div>
                </div>
            </div>
            
            <div class="info-section">
                <h3>Assigned Beneficiaries</h3>
                @if(count($careworker->assignedBeneficiaries) > 0)
                    <div class="beneficiaries-list">
                        @foreach($careworker->assignedBeneficiaries as $beneficiary)
                            <div class="beneficiary-item">
                                <span class="info-label">{{ $loop->iteration }}.</span> 
                                {{ $beneficiary->first_name }} {{ $beneficiary->last_name }}
                                @if($beneficiary->status)
                                    <span class="{{ $beneficiary->status->status_name == 'Active' ? 'status-active' : 'status-inactive' }}">
                                        ({{ $beneficiary->status->status_name }})
                                    </span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <p>No beneficiaries assigned</p>
                @endif
            </div>
            
            @if(isset($careworker->address) || isset($careworker->emergency_contact))
            <div class="info-section">
                <h3>Contact Information</h3>
                <div class="info-grid">
                    @if(isset($careworker->address))
                    <div class="info-item">
                        <span class="info-label">Address:</span> 
                        {{ $careworker->address }}
                    </div>
                    @endif
                    @if(isset($careworker->emergency_contact))
                    <div class="info-item">
                        <span class="info-label">Emergency Contact:</span> 
                        {{ $careworker->emergency_contact }}
                    </div>
                    @endif
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