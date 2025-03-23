<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Family Member Profiles</title>
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
        .status-approved {
            color: green;
            font-weight: bold;
        }
        .status-denied {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Sulong Kalinga - Family Member Profiles</h1>
        <p>Export Date: {{ $exportDate }}</p>
    </div>

    @foreach($familyMembers as $index => $family_member)
        <div class="profile">
            <div class="profile-header">
                <h2>{{ $family_member->first_name }} {{ $family_member->last_name }}</h2>
            </div>
            
            <div class="info-section">
                <h3>Personal Information</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Access Status:</span> 
                        <span class="{{ $family_member->status == 'Approved' ? 'status-approved' : 'status-denied' }}">
                            {{ $family_member->status }}
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Mobile:</span> 
                        {{ $family_member->mobile ?? 'Not provided' }}
                    </div>
                    <div class="info-item">
                        <span class="info-label">Email:</span> 
                        {{ $family_member->email ?? 'Not provided' }}
                    </div>
                    <div class="info-item">
                        <span class="info-label">Birthday:</span>
                        {{ $family_member->birthday ? date('F j, Y', strtotime($family_member->birthday)) : 'Not provided' }}
                    </div>
                    <div class="info-item">
                        <span class="info-label">Family Member ID:</span> 
                        {{ $family_member->family_member_id }}
                    </div>
                </div>
            </div>
            
            <div class="info-section">
                <h3>Beneficiary Information</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Beneficiary Name:</span> 
                        {{ $family_member->beneficiary->first_name }} {{ $family_member->beneficiary->last_name }}
                    </div>
                    <div class="info-item">
                        <span class="info-label">Relationship:</span> 
                        {{ $family_member->relationship ?? 'Not specified' }}
                    </div>
                </div>
            </div>
            
            <div class="info-section">
                <h3>Contact Information</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Address:</span> 
                        {{ $family_member->address ?? 'Not provided' }}
                    </div>
                    <div class="info-item">
                        <span class="info-label">Emergency Contact:</span> 
                        {{ $family_member->emergency_contact ?? 'Not provided' }}
                    </div>
                </div>
            </div>
        </div>
        
        @if(!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach
</body>
</html>