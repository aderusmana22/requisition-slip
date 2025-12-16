<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Warehouse Approval Completed</title>
</head>

<body
    style="margin: 0; padding: 0; background-color: #f3f4f6; font-family: Arial, sans-serif; color: #333; line-height: 1.6; height: 100% !important; width: 100% !important; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%;">

    <table border="0" cellpadding="0" cellspacing="0" width="100%"
        style="border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #f3f4f6;">
        <tr>
            <td align="center" style="padding: 20px 0;">

                <table border="0" cellpadding="0" cellspacing="0" width="100%" align="center"
                    style="max-width: 700px; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05); border-collapse: collapse;">

                    <tr>
                        <td align="center" bgcolor="#28a745"
                            style="background: #28a745; /* Fallback for clients that don't support gradients */ background-image: linear-gradient(135deg, #28a745 0%, #20c997 100%); padding: 30px 40px; text-align: center;">
                            <img src="{{ asset('storage/logo.png') }}" alt="{{ config('app.name') }}"
                                style="max-height: 50px; width: auto; margin-bottom: 15px; display: block; margin: 0 auto 15px; border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; -ms-interpolation-mode: bicubic;">
                            <h1
                                style="font-size: 24px; font-weight: 700; margin: 0; color: white; mso-line-height-alt: 30px;">
                                Warehouse Approval Completed</h1>
                            <p style="font-size: 14px; margin: 10px 0 0 0; opacity: 0.9; color: #f9f9f9;">
                                Warehouse Department - Approval Successfully Completed</p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 30px 40px;">

                            <div
                                style="font-size: 15px; color: #2c3e50; margin-bottom: 25px; padding: 15px; background-color: #f8f9fa; border-radius: 4px; border-left: 4px solid #28a745;">
                                <strong>Hello {{ $requester->name ?? 'User' }},</strong><br>
                                Great news! Your requisition approval has been successfully completed. Please review the
                                details below.
                            </div>

                            <table width="100%" cellpadding="0" cellspacing="0" border="0" align="center"
                                style="border-collapse: collapse; margin-bottom: 30px; border: 2px solid #28a745; border-radius: 8px; background: #d4edda; /* Fallback */ background-image: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);">
                                <tr>
                                    <td style="padding: 30px; text-align: center;">
                                        <h3
                                            style="font-size: 20px; font-weight: 700; color: #155724; margin: 0 0 15px 0;">
                                            🎉 Approval Process Complete!</h3>
                                        <p style="color: #155724; margin: 0; font-size: 16px;">Your requisition
                                            <strong>{{ $requisition->no_srs }}</strong> has successfully completed all
                                            warehouse approval processes and is now fully approved.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <div style="margin-bottom: 30px;">
                                <table width="100%" cellpadding="0" cellspacing="0" border="0" align="center"
                                    style="border-collapse: collapse; font-size: 13px;">
                                    <tr>
                                        <td colspan="2" style="padding: 0 0 5px 0;">
                                            <p
                                                style="font-size: 12px; font-weight: 700; color: #4b5563; text-transform: uppercase; border-bottom: 2px solid #e5e7eb; padding-bottom: 5px; margin: 0;">
                                                📄 Request Information</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="50%" style="padding: 7px 7px 7px 0;" valign="top">
                                            <table width="100%" cellpadding="0" cellspacing="0"
                                                style="border-collapse: collapse;">
                                                <tr>
                                                    <td
                                                        style="background: white; padding: 12px; border-radius: 6px; border: 1px solid #e9ecef;">
                                                        <div
                                                            style="font-weight: 600; color: #495057; font-size: 12px; margin-bottom: 5px;">
                                                            No. SRS</div>
                                                        <div
                                                            style="color: #2c3e50; font-size: 14px; font-weight: bold; word-break: break-word;">
                                                            {{ $requisition->no_srs }}</div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                        <td width="50%" style="padding: 7px 0 7px 7px;" valign="top">
                                            <table width="100%" cellpadding="0" cellspacing="0"
                                                style="border-collapse: collapse;">
                                                <tr>
                                                    <td
                                                        style="background: white; padding: 12px; border-radius: 6px; border: 1px solid #e9ecef;">
                                                        <div
                                                            style="font-weight: 600; color: #495057; font-size: 12px; margin-bottom: 5px;">
                                                            Category</div>
                                                        <div
                                                            style="color: #2c3e50; font-size: 14px; word-break: break-word;">
                                                            {{ $requisition->category }}
                                                        </div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="50%" style="padding: 7px 7px 7px 0;" valign="top">
                                            <table width="100%" cellpadding="0" cellspacing="0"
                                                style="border-collapse: collapse;">
                                                <tr>
                                                    <td
                                                        style="background: white; padding: 12px; border-radius: 6px; border: 1px solid #e9ecef;">
                                                        <div
                                                            style="font-weight: 600; color: #495057; font-size: 12px; margin-bottom: 5px;">
                                                            Final Status</div>
                                                        <div
                                                            style="color: #2c3e50; font-size: 14px; word-break: break-word;">
                                                            <span
                                                                style="display: inline-block; background: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 6px 12px; border-radius: 15px; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; mso-padding-alt: 6px 12px;">
                                                                ✅ Approved
                                                            </span>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                        <td width="50%" style="padding: 7px 0 7px 7px;" valign="top">
                                            <table width="100%" cellpadding="0" cellspacing="0"
                                                style="border-collapse: collapse;">
                                                <tr>
                                                    <td
                                                        style="background: white; padding: 12px; border-radius: 6px; border: 1px solid #e9ecef;">
                                                        <div
                                                            style="font-weight: 600; color: #495057; font-size: 12px; margin-bottom: 5px;">
                                                            Request Date</div>
                                                        <div
                                                            style="color: #2c3e50; font-size: 14px; word-break: break-word;">
                                                            {{
                                                            \Carbon\Carbon::parse($requisition->request_date)->format('d M Y') }}
                                                        </div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            @if($requisition->customer)
                            <div style="margin-bottom: 30px;">
                                <table width="100%" cellpadding="0" cellspacing="0" border="0" align="center"
                                    style="border-collapse: collapse; font-size: 13px;">
                                    <tr>
                                        <td colspan="2" style="padding: 0 0 5px 0;">
                                            <p
                                                style="font-size: 12px; font-weight: 700; color: #4b5563; text-transform: uppercase; border-bottom: 2px solid #e5e7eb; padding-bottom: 5px; margin: 0;">
                                                👤 Customer Information</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="50%" style="padding: 7px 7px 7px 0;" valign="top">
                                            <table width="100%" cellpadding="0" cellspacing="0"
                                                style="border-collapse: collapse;">
                                                <tr>
                                                    <td
                                                        style="background: white; padding: 12px; border-radius: 6px; border: 1px solid #e9ecef;">
                                                        <div
                                                            style="font-weight: 600; color: #495057; font-size: 12px; margin-bottom: 5px;">
                                                            Customer Name</div>
                                                        <div
                                                            style="color: #2c3e50; font-size: 14px; word-break: break-word;">
                                                            {{ $requisition->customer->name ?? 'N/A' }}</div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                        <td width="50%" style="padding: 7px 0 7px 7px;" valign="top">
                                            <table width="100%" cellpadding="0" cellspacing="0"
                                                style="border-collapse: collapse;">
                                                <tr>
                                                    <td
                                                        style="background: white; padding: 12px; border-radius: 6px; border: 1px solid #e9ecef;">
                                                        <div
                                                            style="font-weight: 600; color: #495057; font-size: 12px; margin-bottom: 5px;">
                                                            Customer Address</div>
                                                        <div
                                                            style="color: #2c3e50; font-size: 14px; word-break: break-word;">
                                                            {{ $requisition->customer->address ?? 'N/A' }}</div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            @endif

                            @if($requisition->reason_for_replacement)
                            <div style="margin-bottom: 30px;">
                                <table width="100%" cellpadding="0" cellspacing="0" border="0" align="center"
                                    style="border-collapse: collapse;">
                                    <tr>
                                        <td style="padding: 0 0 5px 0;">
                                            <p
                                                style="font-size: 12px; font-weight: 700; color: #4b5563; text-transform: uppercase; border-bottom: 2px solid #e5e7eb; padding-bottom: 5px; margin: 0;">
                                                🎯 Reason for Replacement</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 7px 0;">
                                            <div
                                                style="background: #f8f9fa; padding: 15px; border-radius: 4px; border: 1px solid #e9ecef;">
                                                <p
                                                    style="margin: 0; color: #2c3e50; font-size: 14px; line-height: 1.6;">
                                                    {{ $requisition->reason_for_replacement }}</p>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            @endif

                            <div style="margin-bottom: 30px;">
                                <table width="100%" cellpadding="0" cellspacing="0" border="0" align="center"
                                    style="border-collapse: collapse; font-size: 13px;">
                                    <tr>
                                        <td colspan="2" style="padding: 0 0 5px 0;">
                                            <p
                                                style="font-size: 12px; font-weight: 700; color: #4b5563; text-transform: uppercase; border-bottom: 2px solid #e5e7eb; padding-bottom: 5px; margin: 0;">
                                                ✅ Completion Information</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="50%" style="padding: 7px 7px 7px 0;" valign="top">
                                            <table width="100%" cellpadding="0" cellspacing="0"
                                                style="border-collapse: collapse;">
                                                <tr>
                                                    <td
                                                        style="background: white; padding: 12px; border-radius: 6px; border: 1px solid #e9ecef;">
                                                        <div
                                                            style="font-weight: 600; color: #495057; font-size: 12px; margin-bottom: 5px;">
                                                            Completed Date</div>
                                                        <div
                                                            style="color: #2c3e50; font-size: 14px; word-break: break-word;">
                                                            {{ $formattedCompletionDate ?? 'N/A' }}</div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                        <td width="50%" style="padding: 7px 0 7px 7px;" valign="top">
                                            <table width="100%" cellpadding="0" cellspacing="0"
                                                style="border-collapse: collapse;">
                                                <tr>
                                                    <td
                                                        style="background: white; padding: 12px; border-radius: 6px; border: 1px solid #e9ecef;">
                                                        <div
                                                            style="font-weight: 600; color: #495057; font-size: 12px; margin-bottom: 5px;">
                                                            Process Type</div>
                                                        <div
                                                            style="color: #2c3e50; font-size: 14px; word-break: break-word;">
                                                            Warehouse Approval Process
                                                        </div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        @if($completedBy)
                                        <td width="50%" style="padding: 7px 7px 7px 0;" valign="top">
                                            <table width="100%" cellpadding="0" cellspacing="0"
                                                style="border-collapse: collapse;">
                                                <tr>
                                                    <td
                                                        style="background: white; padding: 12px; border-radius: 6px; border: 1px solid #e9ecef;">
                                                        <div
                                                            style="font-weight: 600; color: #495057; font-size: 12px; margin-bottom: 5px;">
                                                            Final Approver</div>
                                                        <div
                                                            style="color: #2c3e50; font-size: 14px; word-break: break-word;">
                                                            {{ $completedBy->name ?? 'N/A' }}</div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                        @endif
                                        <td width="50%" style="padding: 7px 0 7px 7px;" valign="top"
                                            colspan="{{ $completedBy ? '1' : '2' }}">
                                            <table width="100%" cellpadding="0" cellspacing="0"
                                                style="border-collapse: collapse;">
                                                <tr>
                                                    <td
                                                        style="background: white; padding: 12px; border-radius: 6px; border: 1px solid #e9ecef;">
                                                        <div
                                                            style="font-weight: 600; color: #495057; font-size: 12px; margin-bottom: 5px;">
                                                            Current Route</div>
                                                        <div
                                                            style="color: #2c3e50; font-size: 14px; word-break: break-word;">
                                                            {{ $requisition->route_to ?? 'Completed' }}
                                                        </div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <div style="margin-bottom: 30px;">
                                <table width="100%" cellpadding="0" cellspacing="0" border="0" align="center"
                                    style="border-collapse: collapse; font-size: 13px;">
                                    <tr>
                                        <td colspan="2" style="padding: 0 0 5px 0;">
                                            <p
                                                style="font-size: 12px; font-weight: 700; color: #4b5563; text-transform: uppercase; border-bottom: 2px solid #e5e7eb; padding-bottom: 5px; margin: 0;">
                                                ⏱️ Process Summary</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="50%" style="padding: 7px 7px 7px 0;" valign="top">
                                            <table width="100%" cellpadding="0" cellspacing="0"
                                                style="border-collapse: collapse;">
                                                <tr>
                                                    <td
                                                        style="background: white; padding: 12px; border-radius: 6px; border: 1px solid #e9ecef;">
                                                        <div
                                                            style="font-weight: 600; color: #495057; font-size: 12px; margin-bottom: 5px;">
                                                            Request Date</div>
                                                        <div
                                                            style="color: #2c3e50; font-size: 14px; word-break: break-word;">
                                                            {{
                                                            \Carbon\Carbon::parse($requisition->request_date)->format('d
                                                            M Y') }}
                                                        </div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                        <td width="50%" style="padding: 7px 0 7px 7px;" valign="top">
                                            <table width="100%" cellpadding="0" cellspacing="0"
                                                style="border-collapse: collapse;">
                                                <tr>
                                                    <td
                                                        style="background: white; padding: 12px; border-radius: 6px; border: 1px solid #e9ecef;">
                                                        <div
                                                            style="font-weight: 600; color: #495057; font-size: 12px; margin-bottom: 5px;">
                                                            Completion Date</div>
                                                        <div
                                                            style="color: #2c3e50; font-size: 14px; word-break: break-word;">
                                                            {{
                                                            \Carbon\Carbon::parse($completionDate)->format('d M Y') }}
                                                        </div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" style="padding: 7px 0;" valign="top">
                                            <table width="100%" cellpadding="0" cellspacing="0"
                                                style="border-collapse: collapse;">
                                                <tr>
                                                    <td
                                                        style="background: white; padding: 12px; border-radius: 6px; border: 1px solid #e9ecef;">
                                                        <div
                                                            style="font-weight: 600; color: #495057; font-size: 12px; margin-bottom: 5px;">
                                                            Total Process Time</div>
                                                        <div
                                                            style="color: #2c3e50; font-size: 14px; word-break: break-word;">
                                                            {{
                                                            \Carbon\Carbon::parse($requisition->request_date)->diffInDays(\Carbon\Carbon::parse($completionDate))
                                                            }} days
                                                        </div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td align="center" bgcolor="#2c3e50" style="padding: 20px; font-size: 11px; color: #9ca3af;">
                            <div style="font-size: 14px; font-weight: 600; color: white; margin-bottom: 5px;">
                                {{ config('app.name') }}</div>
                            <div style="font-size: 12px; opacity: 0.8; margin-bottom: 10px;">Warehouse Department</div>
                            <div style="height: 1px; background: rgba(255, 255, 255, 0.2); margin: 10px 0;"></div>
                            <div style="font-size: 12px; opacity: 0.9; line-height: 1.6;">
                                <strong>Need Help?</strong><br>
                                Contact IT Support:
                            </div>
                            <div style="font-size: 10px; opacity: 0.7; margin-top: 10px;">
                                © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.<br>
                                This is an automated message, please do not reply directly to this email.
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
