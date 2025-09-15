<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team Invitation - {{ $hackathonName }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f7f7f7;
        }
        .container {
            background-color: #ffffff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #0d9488;
        }
        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #0d9488;
            margin-bottom: 5px;
        }
        .hackathon-name {
            font-size: 20px;
            color: #666;
            font-weight: 500;
        }
        h1 {
            color: #0d9488;
            font-size: 24px;
            margin-bottom: 10px;
        }
        .content {
            margin-bottom: 30px;
        }
        .highlight-box {
            background: linear-gradient(135deg, #f0fdfa, #e6fffa);
            border: 1px solid #5eead4;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
        }
        .hackathon-info {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 15px;
        }
        .hackathon-badge {
            background: linear-gradient(135deg, #0d9488, #14b8a6);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 14px;
        }
        .team-info {
            background-color: #f0fdfa;
            border-left: 4px solid #0d9488;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .team-name {
            font-size: 18px;
            font-weight: bold;
            color: #0d9488;
            margin-bottom: 5px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin: 20px 0;
        }
        .info-item {
            padding: 10px;
            background-color: #f9fafb;
            border-radius: 5px;
        }
        .info-label {
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 3px;
        }
        .info-value {
            font-size: 14px;
            font-weight: 600;
            color: #0d9488;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #0d9488, #14b8a6);
            color: white;
            text-decoration: none;
            border-radius: 25px;
            font-weight: bold;
            margin: 20px 0;
            text-align: center;
        }
        .button:hover {
            opacity: 0.9;
        }
        .steps-list {
            background-color: #f9fafb;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .steps-list ul {
            margin: 0;
            padding-left: 20px;
        }
        .steps-list li {
            margin: 10px 0;
            color: #374151;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e5e5;
            text-align: center;
            font-size: 12px;
            color: #888;
        }
        .footer a {
            color: #0d9488;
            text-decoration: none;
        }
        @media (max-width: 600px) {
            .info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">{{ $appName }}</div>
            <div class="hackathon-name">{{ $hackathonName }} {{ $hackathonYear }}</div>
        </div>

        <h1>Welcome to {{ $hackathonName }}!</h1>

        <div class="content">
            <p>Dear {{ $userName }},</p>

            <p>Great news! You have been invited to join a team for <strong>{{ $hackathonName }} {{ $hackathonYear }}</strong>. We're thrilled to have you as part of this innovative journey where creativity meets technology.</p>

            <div class="highlight-box">
                <div class="hackathon-info">
                    <span class="hackathon-badge">{{ $hackathonName }} {{ $hackathonYear }}</span>
                </div>

                <div class="team-info" style="margin: 0; background-color: white;">
                    <div class="team-name">Team: {{ $teamName }}</div>
                    @if($teamDescription)
                        <p style="margin: 10px 0 0 0; color: #666;">{{ $teamDescription }}</p>
                    @endif
                </div>

                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Hackathon</div>
                        <div class="info-value">{{ $hackathonName }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Year</div>
                        <div class="info-value">{{ $hackathonYear }}</div>
                    </div>
                </div>
            </div>

            <p>Your team is ready to embark on this exciting challenge. To get started and access your team dashboard, please click the button below:</p>

            <div style="text-align: center;">
                <a href="{{ $loginUrl }}" class="button">Access Your Team Dashboard</a>
            </div>

            <div class="steps-list">
                <p style="margin-top: 0;"><strong>What's Next?</strong></p>
                <ul>
                    <li>Log in to your {{ $appName }} account using this email address</li>
                    <li>View your team details and connect with other members</li>
                    <li>Review the {{ $hackathonName }} tracks and challenges</li>
                    <li>Collaborate with your team on your innovative project</li>
                    <li>Submit your groundbreaking ideas before the deadline</li>
                </ul>
            </div>

            <p><strong>Important:</strong> This invitation is specifically for {{ $hackathonName }} {{ $hackathonYear }}. Make sure to join your team as soon as possible to start collaborating on your project.</p>

            <p>If you didn't expect this invitation or have any questions, please contact our support team.</p>

            <p>We can't wait to see what amazing solutions your team will create!</p>

            <p>Best regards,<br>
            The {{ $appName }} Team<br>
            <em>{{ $hackathonName }} {{ $hackathonYear }} Organizing Committee</em></p>
        </div>

        <div class="footer">
            <p>This is an automated message from {{ $appName }} - Hackathon Management System.</p>
            <p><strong>{{ $hackathonName }} {{ $hackathonYear }}</strong></p>
            <p>© {{ date('Y') }} {{ $appName }}. All rights reserved.</p>
            <p><a href="{{ url('/') }}">Visit our website</a> | <a href="{{ url('/hackathons/' . Str::slug($hackathonName)) }}">View Hackathon Details</a></p>
        </div>
    </div>
</body>
</html>