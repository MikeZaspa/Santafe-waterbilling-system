<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Verification Code</title>
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
            border: 1px solid #ddd;
            border-radius: 10px;
        }
        .header {
            text-align: center;
            background: #1a73e8;
            color: white;
            padding: 20px;
            border-radius: 10px 10px 0 0;
        }
        .code {
            font-size: 32px;
            font-weight: bold;
            text-align: center;
            letter-spacing: 5px;
            margin: 30px 0;
            color: #1a73e8;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Santa Fe Water Billing System</h1>
            <h2>
                @if($type === 'login')
                    Login Verification Code
                @else
                    Registration Verification Code
                @endif
            </h2>
        </div>
        
        <p>Hello,</p>
        
        <p>
            @if($type === 'login')
                Your login verification code is:
            @else
                Your registration verification code is:
            @endif
        </p>
        
        <div class="code">{{ $code }}</div>
        
        <p>
            @if($type === 'login')
                Enter this code on the login verification page to complete your login.
            @else
                Enter this code on the registration verification page to complete your registration.
            @endif
        </p>
        
        <p><strong>This code will expire in 10 minutes.</strong></p>
        
        <p>If you didn't request this code, please ignore this email.</p>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} Santa Fe Water Billing System. All rights reserved.</p>
        </div>
    </div>
</body>
</html>