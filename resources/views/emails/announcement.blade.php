<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Announcement | Santa Fe Water Billing System</title>
</head>
<body style="margin:0;padding:0;background:#f5f5f5;font-family:Arial, sans-serif;color:#333;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f5f5f5;padding:24px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:8px;overflow:hidden;">
                    <tr>
                        <td style="background:#1a73e8;color:#ffffff;padding:20px 24px;">
                            <h1 style="margin:0;font-size:20px;">Santa Fe Water Billing System</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:24px;">
                            <p style="margin:0 0 12px 0;">{{ $prefix }}</p>
                            <h2 style="margin:0 0 12px 0;font-size:18px;">{{ $announcement->title }}</h2>
                            <p style="margin:0 0 16px 0;white-space:pre-line;">{{ $announcement->message }}</p>
                            <p style="margin:0;">Thank you,<br>Santa Fe Water Billing System Team</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#f3f4f6;color:#6b7280;padding:12px 24px;font-size:12px;text-align:center;">
                            &copy; {{ date('Y') }} Santa Fe Water Billing System. All rights reserved.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
