<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Payment Submitted | Santa Fe Water Billing System</title>
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
                            <p style="margin:0 0 12px 0;">A consumer has submitted a payment for verification.</p>
                            <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;margin:16px 0;">
                                <tr>
                                    <td style="padding:8px;border:1px solid #e5e7eb;"><strong>Consumer</strong></td>
                                    <td style="padding:8px;border:1px solid #e5e7eb;">
                                        {{ trim(($bill->consumer->first_name ?? '') . ' ' . ($bill->consumer->last_name ?? '')) ?: 'N/A' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:8px;border:1px solid #e5e7eb;"><strong>Meter No</strong></td>
                                    <td style="padding:8px;border:1px solid #e5e7eb;">{{ $bill->consumer->meter_no ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:8px;border:1px solid #e5e7eb;"><strong>Amount</strong></td>
                                    <td style="padding:8px;border:1px solid #e5e7eb;">PHP {{ number_format($payment->amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:8px;border:1px solid #e5e7eb;"><strong>Payment Method</strong></td>
                                    <td style="padding:8px;border:1px solid #e5e7eb;">{{ strtoupper($payment->payment_method) }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:8px;border:1px solid #e5e7eb;"><strong>Reference No</strong></td>
                                    <td style="padding:8px;border:1px solid #e5e7eb;">{{ $payment->reference_number }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:8px;border:1px solid #e5e7eb;"><strong>Submitted At</strong></td>
                                    <td style="padding:8px;border:1px solid #e5e7eb;">
                                        {{ optional($payment->created_at)->format('M d, Y h:i A') }}
                                    </td>
                                </tr>
                            </table>
                            <p style="margin:0;">Please review and verify this payment in the admin panel.</p>
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
