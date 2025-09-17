<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Workshop Registration</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #0d9488;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        h1 {
            color: #0d9488;
            margin: 0;
            font-size: 24px;
        }
        .workshop-details {
            background: #f9fafb;
            padding: 20px;
            border-radius: 6px;
            margin: 20px 0;
        }
        .detail-row {
            display: flex;
            padding: 8px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: 600;
            color: #6b7280;
            min-width: 120px;
        }
        .detail-value {
            color: #111827;
        }
        .qr-section {
            text-align: center;
            margin: 30px 0;
            padding: 30px;
            background: #fafafa;
            border-radius: 8px;
            border: 2px dashed #d1d5db;
        }
        .qr-code {
            display: inline-block;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .qr-code img {
            max-width: 250px;
            height: auto;
        }
        .instructions {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin: 20px 0;
        }
        .instructions h3 {
            color: #92400e;
            margin-top: 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 14px;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background: #0d9488;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin: 10px 0;
        }
        .button:hover {
            background: #0f766e;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Workshop Registration Confirmation</h1>
            <p style="color: #6b7280; margin: 10px 0 0 0;">Your attendance QR code is ready!</p>
        </div>

        <p>Dear {{ $user->name }},</p>

        <p>You have been registered for the following workshop. Please save this email and present the QR code below at the workshop entrance for quick check-in.</p>

        <div class="workshop-details">
            <h2 style="color: #111827; margin-top: 0;">Workshop Details</h2>
            <div class="detail-row">
                <span class="detail-label">Title:</span>
                <span class="detail-value">{{ $workshop->title }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Date:</span>
                <span class="detail-value">{{ $workshopDate }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Time:</span>
                <span class="detail-value">{{ $workshopTime }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Location:</span>
                <span class="detail-value">{{ $location }}</span>
            </div>
            @if($workshop->format === 'online' && $workshop->location)
            <div class="detail-row">
                <span class="detail-label">Online Link:</span>
                <span class="detail-value"><a href="{{ $workshop->location }}">Join Online</a></span>
            </div>
            @endif
            @if($registrationId)
            <div class="detail-row">
                <span class="detail-label">Registration ID:</span>
                <span class="detail-value">#{{ str_pad($registrationId, 6, '0', STR_PAD_LEFT) }}</span>
            </div>
            @endif
        </div>

        <div class="qr-section">
            <h3 style="color: #111827; margin-top: 0;">Your Personal QR Code</h3>
            <p style="color: #6b7280; margin: 10px 0;">Show this QR code at the workshop entrance for instant check-in</p>
            <div class="qr-code">
                <img src="{{ $qrCode }}" alt="Workshop Registration QR Code" />
            </div>
            <p style="font-size: 12px; color: #9ca3af; margin-top: 10px;">
                This QR code is unique to you and contains your registration information
            </p>
        </div>

        <div class="instructions">
            <h3>Check-in Instructions</h3>
            <ol style="margin: 0; padding-left: 20px;">
                <li>Arrive at the workshop location 10 minutes early</li>
                <li>Show this QR code to the supervisor at the entrance</li>
                <li>Your attendance will be automatically registered</li>
                <li>No need for manual sign-in or paper forms!</li>
            </ol>
        </div>

        @if($workshop->description)
        <div style="margin: 20px 0;">
            <h3 style="color: #111827;">About This Workshop</h3>
            <p style="color: #4b5563;">{{ $workshop->description }}</p>
        </div>
        @endif

        @if($workshop->prerequisites)
        <div style="margin: 20px 0;">
            <h3 style="color: #111827;">Prerequisites</h3>
            <p style="color: #4b5563;">{{ $workshop->prerequisites }}</p>
        </div>
        @endif

        <div class="footer">
            <p>This is an automated email. Please do not reply.</p>
            <p>For questions, contact your track supervisor or hackathon administrator.</p>
            <p style="margin-top: 20px;">
                <small>GuacPanel Hackathon Management System</small>
            </p>
        </div>
    </div>
</body>
</html>