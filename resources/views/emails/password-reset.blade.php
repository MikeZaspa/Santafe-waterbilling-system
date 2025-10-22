<!DOCTYPE html>
<html>
<head>
    <title>Password Reset</title>
    <style>
        body {
            font-family: 'Inter', Arial, sans-serif;
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
        .button {
            display: inline-block;
            background-color: #1a73e8;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .url-box {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #1a73e8;
            word-break: break-all;
            margin: 20px 0;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 14px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Santa Fe Water Billing System</h2>
        <h3>Password Reset Request</h3>
    </div>
    
    <p>Hello,</p>
    
    <p>You are receiving this email because we received a password reset request for your account ({{ $email }}).</p>
    
    <p>Click the button below to reset your password:</p>
    
    <div style="text-align: center;">
        <a href="{{ $resetUrl }}" class="button">
            Reset Password
        </a>
    </div>
    
    <p><strong>If the button doesn't work, copy and paste this URL into your browser:</strong></p>
    <div class="url-box">
    {{ urldecode($resetUrl) }}
   </div>
    
    <p>This password reset link will expire in 60 minutes.</p>
    
    <p>If you did not request a password reset, no further action is required.</p>
    
    <div class="footer">
        <p>Thank you,<br>Santa Fe Water Billing System Team</p>
        <p><small>This is an automated message. Please do not reply to this email.</small></p>
    </div>
</body>
</html>