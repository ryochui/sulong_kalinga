<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Administrator Profiles</title>
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
        <h1>Sulong Kalinga - Administrator Profiles</h1>
        <p>Export Date: {{ $exportDate }}</p>
    </div>

    @foreach($administrators as $index => $administrator)
        <div class="profile">
            <div class="profile-header">
                <h2>{{ $administrator->first_name }} {{ $administrator->last_name }}</h2>
            </div>
            
            <div class="info-section">
                <h3>Personal Information</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Status:</span> 
                        @if(isset($administrator->organizationRole) && $administrator->organizationRole->role_name == 'executive_director')
                            <span class="status-active">Active (Executive Director)</span>
                        @else
                            <span class="{{ $administrator->volunteer_status == 'Active' ? 'status-active' : 'status-inactive' }}">
                                {{ $administrator->volunteer_status }}
                            </span>
                        @endif
                    </div>
                    <div class="info-item">
                        <span class="info-label">Organization Role:</span> 
                        {{ ucwords(str_replace('_', ' ', $administrator->organizationRole->role_name ?? 'N/A')) }}
                    </div>
                    <div class="info-item">
                        <span class="info-label">Area:</span> 
                        {{ isset($administrator->organizationRole) ? ucwords(str_replace('_', ' ', $administrator->organizationRole->area ?? 'N/A')) : 'N/A' }}
                    </div>
                    <div class="info-item">
                        <span class="info-label">Mobile:</span> 
                        {{ $administrator->mobile ?? 'Not provided' }}
                    </div>
                    <div class="info-item">
                        <span class="info-label">Email:</span> 
                        {{ $administrator->email ?? 'Not provided' }}
                    </div>
                    <div class="info-item">
                        <span class="info-label">Administrator ID:</span> 
                        {{ $administrator->id }}
                    </div>
                </div>
            </div>
            
            @if(isset($administrator->address) || isset($administrator->emergency_contact))
            <div class="info-section">
                <h3>Contact Information</h3>
                <div class="info-grid">
                    @if(isset($administrator->address))
                    <div class="info-item">
                        <span class="info-label">Address:</span> 
                        {{ $administrator->address }}
                    </div>
                    @endif
                    @if(isset($administrator->emergency_contact))
                    <div class="info-item">
                        <span class="info-label">Emergency Contact:</span> 
                        {{ $administrator->emergency_contact }}
                    </div>
                    @endif
                </div>
            </div>
            @endif
            
            @if(isset($administrator->created_at))
            <div class="info-section">
                <h3>Account Information</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Account Created:</span> 
                        {{ $administrator->created_at->format('F j, Y') }}
                    </div>
                    @if(isset($administrator->updated_at))
                    <div class="info-item">
                        <span class="info-label">Last Updated:</span> 
                        {{ $administrator->updated_at->format('F j, Y') }}
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