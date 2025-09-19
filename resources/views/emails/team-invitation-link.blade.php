<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team Invitation - {{ $appName }}</title>
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
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #0d9488;
            margin-bottom: 10px;
        }
        .title {
            font-size: 20px;
            color: #333;
            margin: 20px 0;
        }
        .content {
            margin: 20px 0;
        }
        .team-info {
            background: #f8f9fa;
            border-left: 4px solid #0d9488;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .team-name {
            font-size: 18px;
            font-weight: bold;
            color: #0d9488;
            margin-bottom: 10px;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #0d9488, #14b8a6);
            color: white !important;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            margin: 20px 0;
            text-align: center;
        }
        .btn:hover {
            opacity: 0.9;
        }
        .info-box {
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 5px;
            padding: 10px;
            margin: 20px 0;
        }
        .info-box strong {
            color: #856404;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e5e5;
            text-align: center;
            font-size: 14px;
            color: #666;
        }
        .expiry-notice {
            color: #dc3545;
            font-weight: bold;
            margin: 15px 0;
        }
        .email-highlight {
            background: #e3f2fd;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: monospace;
            color: #1976d2;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">{{ $appName }}</div>
            <div class="title">Team Invitation for {{ $hackathonName }} {{ $hackathonYear }}</div>
        </div>

        <div class="content">
            <p>Hello,</p>

            <p><strong>{{ $teamLeaderName }}</strong> has invited you to join their team for the upcoming hackathon!</p>

            <div class="team-info">
                <div class="team-name">Team: {{ $teamName }}</div>
                @if($teamDescription)
                <p>{{ $teamDescription }}</p>
                @endif
                <p><strong>Hackathon:</strong> {{ $hackathonName }} {{ $hackathonYear }}</p>
            </div>

            <div class="info-box">
                <strong>Important:</strong> This invitation is for the email address
                <span class="email-highlight">{{ $email }}</span>.
                You must register with this email to join the team automatically.
            </div>

            <p>To accept this invitation and join the team, please click the button below to register:</p>

            <div style="text-align: center;">
                <a href="{{ $registrationUrl }}" class="btn">Accept Invitation & Register</a>
            </div>

            <p>Or copy and paste this link into your browser:</p>
            <p style="word-break: break-all; color: #0d9488;">{{ $registrationUrl }}</p>

            <div class="expiry-notice">
                ⚠️ This invitation will expire on {{ $expiresAt->format('F j, Y \a\t g:i A') }}
            </div>

            <h3>What happens next?</h3>
            <ol>
                <li>Click the invitation link above</li>
                <li>Complete the registration form (your email will be pre-filled)</li>
                <li>You'll be automatically added to the team upon successful registration</li>
                <li>Access your team dashboard to start collaborating!</li>
            </ol>

            <p>If you have any questions, please contact your team leader or the hackathon organizers.</p>
        </div>

        <div class="footer">
            <p>This invitation was sent from {{ $appName }} - {{ $hackathonName }} {{ $hackathonYear }}</p>
            <p>© {{ date('Y') }} {{ $appName }}. All rights reserved.</p>
            <p style="font-size: 12px; color: #999;">
                If you did not expect this invitation, please ignore this email.
            </p>
        </div>
    </div>
</body>
</html>