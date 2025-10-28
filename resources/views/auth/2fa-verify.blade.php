<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>2FA Code</title>
</head>
<body>
    <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
        <h2 style="color: #1a73e8;">Santa Fe Water Billing System</h2>
        <h3>Two-Factor Authentication Code</h3>
        
        <p>Hello,</p>
        
        <p>Your two-factor authentication code is:</p>
        
        <div style="background: #f8f9fa; padding: 20px; text-align: center; font-size: 32px; 
                    font-weight: bold; letter-spacing: 10px; color: #1a73e8; margin: 20px 0;">
            {{ $code }}
        </div>
        
        <p>This code will expire in 10 minutes.</p>
        
        <p>If you didn't request this code, please ignore this email or contact support.</p>
        
        <hr style="border: none; border-top: 1px solid #eee; margin: 20px 0;">
        
        <p style="color: #666; font-size: 12px;">
            This is an automated message from Santa Fe Water Billing System.
        </p>
    </div>
</body>
</html>