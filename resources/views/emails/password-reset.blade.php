<!DOCTYPE html>
<html>
<head>
    <title>Password Reset</title>
</head>
<body>
    <h2>Password Reset Request</h2>
    <p>You are receiving this email because we received a password reset request for your account.</p>
    
    <p>Click the button below to reset your password:</p>
    
    <!-- Use a div wrapper for better Gmail compatibility -->
    <div style="margin: 20px 0;">
        <a href="{{ $resetUrl }}" 
           style="background-color: #007bff; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; display: inline-block;">
            Reset Password
        </a>
    </div>
    
    <p><strong>If the button doesn't work, copy and paste this URL into your browser:</strong></p>
    <p style="word-break: break-all; background: #f8f9fa; padding: 10px; border-radius: 5px;">
        {{ $resetUrl }}
    </p>
    
    <p>This password reset link will expire in 60 minutes.</p>
    
    <p>If you did not request a password reset, please ignore this email.</p>
</body>
</html>