<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Care Manager Profiles</title>
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
        .care-workers-list {
            margin-top: 5px;
        }
        .care-worker-item {
            padding: 3px 0;
            border-bottom: 1px dotted #eee;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Sulong Kalinga - Care Manager Profiles</h1>
        <p>Export Date: {{ $exportDate }}</p>
    </div>

    @foreach($caremanagers as $index => $caremanager)
        <div class="profile">
            <div class="profile-header">
                <h2>{{ $caremanager->first_name }} {{ $caremanager->last_name }}</h2>
            </div>
            
            <div class="info-section">
                <h3>Personal Information</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Status:</span> 
                        <span class="{{ $caremanager->volunteer_status == 'Active' ? 'status-active' : 'status-inactive' }}">
                            {{ $caremanager->volunteer_status }}
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Mobile:</span> 
                        {{ $caremanager->mobile ?? 'Not provided' }}
                    </div>
                    <div class="info-item">
                        <span class="info-label">Email:</span> 
                        {{ $caremanager->email ?? 'Not provided' }}
                    </div>
                    <div class="info-item">
                        <span class="info-label">Municipality:</span> 
                        {{ $caremanager->municipality->municipality_name ?? 'Not assigned' }}
                    </div>
                    <div class="info-item">
                        <span class="info-label">Care Manager ID:</span> 
                        {{ $caremanager->id }}
                    </div>
                </div>
            </div>
            
            <div class="info-section">
                <h3>Assigned Care Workers</h3>
                @if(count($caremanager->assignedCareWorkers) > 0)
                    <div class="care-workers-list">
                        @foreach($caremanager->assignedCareWorkers as $careWorker)
                            <div class="care-worker-item">
                                <span class="info-label">{{ $loop->iteration }}.</span> 
                                {{ $careWorker->first_name }} {{ $careWorker->last_name }}
                                <span class="{{ $careWorker->volunteer_status == 'Active' ? 'status-active' : 'status-inactive' }}">
                                    ({{ $careWorker->volunteer_status }})
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p>No care workers assigned</p>
                @endif
            </div>
            
            @if(isset($caremanager->address) || isset($caremanager->emergency_contact))
            <div class="info-section">
                <h3>Contact Information</h3>
                <div class="info-grid">
                    @if(isset($caremanager->address))
                    <div class="info-item">
                        <span class="info-label">Address:</span> 
                        {{ $caremanager->address }}
                    </div>
                    @endif
                    @if(isset($caremanager->emergency_contact))
                    <div class="info-item">
                        <span class="info-label">Emergency Contact:</span> 
                        {{ $caremanager->emergency_contact }}
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