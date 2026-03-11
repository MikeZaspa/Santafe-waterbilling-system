<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>New Billing | Santa Fe Water Billing System</title>
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
                            <p style="margin:0 0 12px 0;">Hello {{ $consumer->first_name }} {{ $consumer->last_name }},</p>
                            <p style="margin:0 0 12px 0;">
                                A new billing has been created for your account. Please see the details below:
                            </p>
                            <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;margin:16px 0;">
                                <tr>
                                    <td style="padding:8px;border:1px solid #e5e7eb;"><strong>Billing Month</strong></td>
                                    <td style="padding:8px;border:1px solid #e5e7eb;">{{ \Carbon\Carbon::parse($billing->due_date)->format('F Y') }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:8px;border:1px solid #e5e7eb;"><strong>Due Date</strong></td>
                                    <td style="padding:8px;border:1px solid #e5e7eb;">{{ \Carbon\Carbon::parse($billing->due_date)->format('M d, Y') }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:8px;border:1px solid #e5e7eb;"><strong>Consumption</strong></td>
                                    <td style="padding:8px;border:1px solid #e5e7eb;">{{ number_format($billing->consumption, 2) }} m&sup3;</td>
                                </tr>
                                <tr>
                                    <td style="padding:8px;border:1px solid #e5e7eb;"><strong>Total Amount</strong></td>
                                    <td style="padding:8px;border:1px solid #e5e7eb;">&#8369;{{ number_format($billing->total_amount, 2) }}</td>
                                </tr>
                                @if(!empty($billing->penalty_amount) && $billing->penalty_amount > 0)
                                <tr>
                                    <td style="padding:8px;border:1px solid #e5e7eb;"><strong>Penalty</strong></td>
                                    <td style="padding:8px;border:1px solid #e5e7eb;">&#8369;{{ number_format($billing->penalty_amount, 2) }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td style="padding:8px;border:1px solid #e5e7eb;"><strong>Amount Due</strong></td>
                                    <td style="padding:8px;border:1px solid #e5e7eb;">&#8369;{{ number_format($billing->amount_due ?? ($billing->total_amount + ($billing->penalty_amount ?? 0)), 2) }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:8px;border:1px solid #e5e7eb;"><strong>Status</strong></td>
                                    <td style="padding:8px;border:1px solid #e5e7eb;">{{ ucfirst($billing->status) }}</td>
                                </tr>
                            </table>
                            <div style="margin:16px 0 20px 0;">
                                <a href="{{ url('/consumer-login') }}"
                                   style="display:inline-block;background:#28a745;color:#ffffff;text-decoration:none;padding:10px 16px;border-radius:4px;font-weight:600;">
                                    Pay Now
                                </a>
                            </div>
                            <p style="margin:0 0 12px 0;font-size:12px;color:#6b7280;">
                                If the button does not work, open this link: {{ url('/consumer-login') }}
                            </p>
                            <p style="margin:0 0 12px 0;">You can view and pay your bill by logging in to your account.</p>
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
