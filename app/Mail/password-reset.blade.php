<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Password Reset | Santa Fe Water Billing System</title>
</head>
<body style="font-family: Arial, sans-serif; background: #f4f4f4; padding: 20px;">
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <table width="500" cellpadding="20" cellspacing="0" style="background: #ffffff; border-radius: 8px;">
                    <tr>
                        <td align="center">
                            <img src="{{ asset('image/santafe.png') }}" alt="Santa Fe Water" width="120" height="80">
                            <h2 style="color:#1a73e8;">Santa Fe Water Billing System</h2>
                            <p style="color:#333;">You requested to reset your password.</p>
                            <p style="color:#555;">Click the button below to set a new password:</p>

                            <a href="{{ $resetUrl }}" 
                               style="display:inline-block;background-color:#1a73e8;color:#fff;padding:12px 24px;
                                      border-radius:6px;text-decoration:none;font-weight:bold;margin-top:10px;">
                                Reset Password
                            </a>

                            <p style="color:#888;margin-top:20px;font-size:0.9rem;">
                                If you didn’t request this, please ignore this email.<br>
                                The link will expire in 1 hour.
                            </p>

                            <hr style="border:none;border-top:1px solid #eee;margin:20px 0;">
                            <p style="font-size:0.8rem;color:#aaa;">© {{ date('Y') }} Santa Fe Water Billing System</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
