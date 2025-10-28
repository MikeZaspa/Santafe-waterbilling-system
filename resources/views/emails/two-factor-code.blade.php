<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Two-Factor Authentication Code</title>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            width: 170px;
            height: 120px;
        }
        .code-container {
            background-color: #f8f9fa;
            border: 1px solid #dadce0;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin: 30px 0;
        }
        .code {
            font-size: 32px;
            font-weight: 600;
            letter-spacing: 5px;
            color: #1a73e8;
            margin: 10px 0;
        }
        .footer {
            text-align: center;
            font-size: 14px;
            color: #5f6368;
            margin-top: 30px;
        }
        .warning {
            color: #d93025;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ asset('image/santafe.png') }}" class="logo" alt="Santa Fe Water">
        <h1>Santa Fe Water Billing System</h1>
    </div>
    
    <p>Hello,</p>
    
    <p>You have requested to log in to the Santa Fe Water Billing System. For your security, we require a two-factor authentication code to complete the login process.</p>
    
    <div class="code-container">
        <p>Your verification code is:</p>
        <div class="code">{{ $code }}</div>
        <p>This code will expire in 10 minutes.</p>
    </div>
    
    <p>If you did not request this code, please ignore this email or contact our support team immediately.</p>
    
    <div class="footer">
        <p class="warning">Do not share this code with anyone.</p>
        <p>© {{ date('Y') }} Santa Fe Water Billing System. All rights reserved.</p>
    </div>
</body>
</html>