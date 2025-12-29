<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Requisition Rejected</title>
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
                        <td align="center" bgcolor="#dc3545"
                            style="background: #dc3545; /* Fallback for clients that don't support gradients */ background-image: linear-gradient(135deg, #dc3545 0%, #c82333 100%); padding: 30px 40px; text-align: center;">
                            <img src="{{ asset('storage/logo.png') }}" alt="{{ config('app.name') }}"
                                style="max-height: 50px; width: auto; margin-bottom: 15px; display: block; margin: 0 auto 15px; border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; -ms-interpolation-mode: bicubic;">
                            <h1
                                style="font-size: 24px; font-weight: 700; margin: 0; color: white; mso-line-height-alt: 30px;">
                                Requisition Rejected</h1>
                            <p style="font-size: 14px; margin: 10px 0 0 0; opacity: 0.9; color: #f9f9f9;">
                                {{ $rejectionType === 'warehouse' ? 'Warehouse Approval' : 'Standard Approval' }} -
                                Request Rejected</p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 30px 40px;">

                            <div
                                style="font-size: 15px; color: #2c3e50; margin-bottom: 25px; padding: 15px; background-color: #f8f9fa; border-radius: 4px; border-left: 4px solid #dc3545;">
                                <strong>Hello {{ $requester->name ?? 'User' }},</strong><br>
                                Your requisition approval request has been rejected. Please review the details below and
                                take appropriate action.
                            </div>

                            <table width="100%" cellpadding="0" cellspacing="0" border="0" align="center"
                                style="border-collapse: collapse; margin-bottom: 30px; border: 2px solid #dc3545; border-radius: 8px; background: #f8d7da; /* Fallback */ background-image: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);">
                                <tr>
                                    <td style="padding: 30px; text-align: center;">
                                        <h3
                                            style="font-size: 20px; font-weight: 700; color: #721c24; margin: 0 0 15px 0;">
                                            ❌ Requisition Rejected</h3>
                                        <p style="color: #721c24; margin: 0; font-size: 16px;">Your requisition
                                            <strong>{{ $requisition->no_srs }}</strong> has been rejected during the
                                            {{ $rejectionType === 'warehouse' ? 'warehouse approval' : 'approval' }}
                                            process.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <table border="0" cellpadding="0" cellspacing="0" width="100%"
                                style="border-collapse: collapse;">
                                <tr>
                                    <td height="30" style="font-size: 1px; line-height: 1px; padding: 0;">&nbsp;</td>
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
                                                            Current Status</div>
                                                        <div
                                                            style="color: #2c3e50; font-size: 14px; word-break: break-word;">
                                                            <span
                                                                style="display: inline-block; background: #f8d7da; color: #721c24; border: 1px solid #f1b0b7; padding: 6px 12px; border-radius: 15px; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; mso-padding-alt: 6px 12px;">
                                                                ❌ Rejected
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

                            <div style="margin-bottom: 30px;">
                                <table width="100%" cellpadding="0" cellspacing="0" border="0" align="center"
                                    style="border-collapse: collapse; font-size: 13px;">
                                    <tr>
                                        <td colspan="2" style="padding: 0 0 5px 0;">
                                            <p
                                                style="font-size: 12px; font-weight: 700; color: #4b5563; text-transform: uppercase; border-bottom: 2px solid #e5e7eb; padding-bottom: 5px; margin: 0;">
                                                🚫 Rejection Information</p>
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
                                                            Rejected By</div>
                                                        <div
                                                            style="color: #2c3e50; font-size: 14px; word-break: break-word;">
                                                            {{ $rejectedBy->name ?? 'N/A' }}</div>
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
                                                            Rejection Date</div>
                                                        <div
                                                            style="color: #2c3e50; font-size: 14px; word-break: break-word;">
                                                            {{ $formattedRejectionDate ?? 'N/A' }}</div>
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
                                                            Rejection Type</div>
                                                        <div
                                                            style="color: #2c3e50; font-size: 14px; word-break: break-word;">
                                                            {{ $rejectionType === 'warehouse' ? 'Warehouse Approval' :
                                                            'Manager Approval' }}
                                                        </div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            @if($rejectionReason)
                            <div style="margin-bottom: 30px;">
                                <table width="100%" cellpadding="0" cellspacing="0" border="0" align="center"
                                    style="border-collapse: collapse;">
                                    <tr>
                                        <td style="padding: 0 0 5px 0;">
                                            <p
                                                style="font-size: 12px; font-weight: 700; color: #4b5563; text-transform: uppercase; border-bottom: 2px solid #e5e7eb; padding-bottom: 5px; margin: 0;">
                                                💬 Reason for Rejection</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 7px 0;">
                                            <div
                                                style="background: #f8f9fa; padding: 15px; border-radius: 4px; border: 1px solid #e9ecef;">
                                                <p
                                                    style="margin: 0; color: #2c3e50; font-size: 14px; line-height: 1.6; font-style: italic;">
                                                    {{ $rejectionReason }}</p>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            @endif

                            <div style="margin-bottom: 30px;">
                                <table width="100%" cellpadding="0" cellspacing="0" border="0" align="center"
                                    style="border-collapse: collapse;">
                                    <tr>
                                        <td style="padding: 0 0 5px 0;">
                                            <p
                                                style="font-size: 12px; font-weight: 700; color: #4b5563; text-transform: uppercase; border-bottom: 2px solid #e5e7eb; padding-bottom: 5px; margin: 0;">
                                                📢 What happens next?</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 7px 0;">
                                            <div
                                                style="background: #f8f9fa; padding: 15px; border-radius: 4px; border: 1px solid #e9ecef;">
                                                <p
                                                    style="margin: 0 0 15px 0; color: #2c3e50; font-size: 14px; line-height: 1.6;">
                                                    Your requisition has been rejected and the process has been stopped.
                                                    @if($rejectionType === 'warehouse')
                                                    Please review the warehouse requirements and resubmit if necessary.
                                                    @else
                                                    Please review the requirements and resubmit if necessary.
                                                    @endif
                                                </p>
                                                <p
                                                    style="margin: 0; color: #2c3e50; font-size: 14px; line-height: 1.6; font-weight: 600;">
                                                    If you have any questions about this rejection, please contact the
                                                    person who
                                                    rejected your request or your supervisor.
                                                </p>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>


                            <table width="100%" cellpadding="0" cellspacing="0" border="0" align="center"
                                style="border-collapse: collapse; margin: 20px 0; border: 1px solid #f1b0b7; border-radius: 4px; background: #f8d7da;">
                                <tr>
                                    <td style="padding: 15px; background: #f8d7da; border-radius: 4px;">

                                        <div style="text-align: center;">
                                            <h4 style="color: #721c24; margin: 0 0 8px 0; font-size: 14px;">⚠️ Important
                                                Notice</h4>

                                            <table border="0" cellpadding="0" cellspacing="0" align="center"
                                                style="border-collapse: collapse; max-width: 450px; margin: 0 auto 0 auto; color: #721c24; font-size: 13px;">
                                                <tr>
                                                    <td align="left" style="padding: 0;">
                                                        <ul
                                                            style="color: #721c24; margin: 0; padding-left: 20px; font-size: 13px; list-style-type: disc; text-align: left;">
                                                            <li style="margin-bottom: 5px;">This requisition has been
                                                                permanently rejected</li>
                                                            <li style="margin-bottom: 5px;">You will need to create a
                                                                new requisition if you wish to proceed</li>
                                                            <li style="margin-bottom: 5px;">Please address the rejection
                                                                reasons before resubmitting</li>
                                                            <li>Contact the rejector directly if clarification is needed
                                                            </li>
                                                        </ul>
                                                    </td>
                                                </tr>
                                            </table>

                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td align="center" bgcolor="#2c3e50" style="padding: 20px; font-size: 11px; color: #9ca3af;">
                            <div style="font-size: 14px; font-weight: 600; color: white; margin-bottom: 5px;">
                                {{ config('app.name') }}</div>
                            <div style="font-size: 12px; opacity: 0.8; margin-bottom: 10px;">Requisition Management
                                System</div>
                            <div style="height: 1px; background: rgba(255, 255, 255, 0.2); margin: 10px 0;"></div>
                            <div style="font-size: 12px; opacity: 0.9; line-height: 1.6;">
                                <strong>Need Help?</strong><br>
                                Contact IT Support: support@company.com<br>
                                Internal Extension: 1234
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
