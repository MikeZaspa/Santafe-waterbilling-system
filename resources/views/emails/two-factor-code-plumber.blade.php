<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Two Factor Authentication Code</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: #0d9488;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 0 0 8px 8px;
        }
        .code {
            font-size: 32px;
            font-weight: bold;
            text-align: center;
            letter-spacing: 5px;
            color: #0d9488;
            margin: 20px 0;
            padding: 15px;
            background: white;
            border: 2px dashed #0d9488;
            border-radius: 8px;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Santa Fe Water Billing System</h1>
            <h2>Two-Factor Authentication</h2>
        </div>
        <div class="content">
            @if($name)
                <p>Hello {{ $name }},</p>
            @else
                <p>Hello,</p>
            @endif
            
            <p>Your two-factor authentication code for the Plumber Portal is:</p>
            
            <div class="code">{{ $code }}</div>
            
            <p>This code will expire in 10 minutes.</p>
            
            <p>If you didn't request this code, please ignore this email or contact support if you have concerns.</p>
            
            <p>Best regards,<br>Santa Fe Water System Team</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Santa Fe Water Billing System. All rights reserved.</p>
        </div>
    </div>
</body>
</html>