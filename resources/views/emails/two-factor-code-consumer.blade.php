<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification Code</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #d32f2f;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f9f9f9;
            padding: 20px;
            border: 1px solid #ddd;
            border-top: none;
        }
        .code {
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            padding: 15px;
            background-color: #fff;
            border: 2px dashed #d32f2f;
            border-radius: 5px;
            margin: 20px 0;
            letter-spacing: 5px;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Santa Fe Water Billing System</h1>
        <p>Two-Factor Authentication</p>
    </div>
    
    <div class="content">
        <p>Hello,</p>
        <p>You have requested to log in to your Santa Fe Water Billing System account. Please use the verification code below to complete the login process:</p>
        
        <div class="code">{{ $code }}</div>
        
        <p>This code will expire in 10 minutes for security reasons. If you did not request this code, please ignore this email.</p>
        
        <p>Thank you,<br>Santa Fe Water Billing System Team</p>
    </div>
    
    <div class="footer">
        <p>&copy; {{ date('Y') }} Santa Fe Water Billing System. All rights reserved.</p>
    </div>
</body>
</html>