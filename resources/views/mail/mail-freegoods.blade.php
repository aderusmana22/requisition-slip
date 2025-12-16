<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Free Goods Requisition Notification</title>
</head>

<body
    style="margin: 0; padding: 0; background-color: #f3f4f6; font-family: Arial, sans-serif; color: #333; line-height: 1.6; height: 100% !important; width: 100% !important;">

    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse;">
        <tr>
            <td align="center" style="padding: 20px 0;">

                <table border="0" cellpadding="0" cellspacing="0" width="100%" align="center"
                    style="max-width: 700px; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05); border-collapse: collapse;">

                    <tr>
                        <td bgcolor="#b8871a" style="padding: 30px 40px; text-align: center;">
                            <img src="{{ asset('assets/images/logo/logohitam.png') }}" alt="{{ config('app.name') }}"
                                style="max-height: 50px; width: auto; margin-bottom: 15px; display: block; margin: 0 auto 15px; border: 0;">
                            <h1 style="font-size: 24px; font-weight: 700; margin: 0; color: white;">Free Goods Approval
                                Request</h1>
                            <p style="font-size: 14px; margin: 10px 0 0 0; opacity: 0.9; color: #f9f9f9;">
                                {{ $requisition->no_srs }} - {{ $requisition->sub_category }}
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 30px 40px;">

                            <div
                                style="font-size: 15px; color: #2c3e50; margin-bottom: 25px; padding: 15px; background-color: #f8f9fa; border-radius: 4px; border-left: 4px solid #cc982f;">
                                <strong>Hello {{ $recipient->name }},</strong><br>
                                @if($mail_type === 'completed_notification')
                                Requisition <strong>{{ $requisition->no_srs }}</strong> has been fully processed and is
                                now <strong>Completed</strong>.
                                @elseif($mail_type === 'warehouse_process')
                                Requisition <strong>{{ $requisition->no_srs }}</strong> has been fully approved and now
                                requires action for: <strong>{{ $process_step }}</strong>.
                                @else
                                A new Free Goods requisition requires your review and approval.
                                @endif
                            </div>

                            <div style="margin-bottom: 30px;">
                                <table width="100%" cellpadding="0" cellspacing="0" border="0"
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
                                            <div
                                                style="background: white; padding: 12px; border-radius: 6px; border: 1px solid #e9ecef;">
                                                <div
                                                    style="font-weight: 600; color: #495057; font-size: 11px; margin-bottom: 5px; text-transform: uppercase;">
                                                    Request Number</div>
                                                <div style="color: #2c3e50; font-size: 14px; font-weight: bold;">{{
                                                    $requisition->no_srs }}</div>
                                            </div>
                                        </td>
                                        <td width="50%" style="padding: 7px 0 7px 7px;" valign="top">
                                            <div
                                                style="background: white; padding: 12px; border-radius: 6px; border: 1px solid #e9ecef;">
                                                <div
                                                    style="font-weight: 600; color: #495057; font-size: 11px; margin-bottom: 5px; text-transform: uppercase;">
                                                    Request Date</div>
                                                <div style="color: #2c3e50; font-size: 14px;">{{
                                                    \Carbon\Carbon::parse($requisition->request_date)->format('d M Y')
                                                    }}</div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="50%" style="padding: 7px 7px 7px 0;" valign="top">
                                            <div
                                                style="background: white; padding: 12px; border-radius: 6px; border: 1px solid #e9ecef;">
                                                <div
                                                    style="font-weight: 600; color: #495057; font-size: 11px; margin-bottom: 5px; text-transform: uppercase;">
                                                    Requester</div>
                                                <div style="color: #2c3e50; font-size: 14px;">{{
                                                    $requisition->requester->name ?? 'N/A' }}</div>
                                            </div>
                                        </td>
                                        <td width="50%" style="padding: 7px 0 7px 7px;" valign="top">
                                            <div
                                                style="background: white; padding: 12px; border-radius: 6px; border: 1px solid #e9ecef;">
                                                <div
                                                    style="font-weight: 600; color: #495057; font-size: 11px; margin-bottom: 5px; text-transform: uppercase;">
                                                    Customer</div>
                                                <div style="color: #2c3e50; font-size: 14px;">{{
                                                    $requisition->customer->name ?? 'N/A' }}</div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" style="padding: 7px 0;">
                                            <div
                                                style="background: white; padding: 12px; border-radius: 6px; border: 1px solid #e9ecef;">
                                                <div
                                                    style="font-weight: 600; color: #495057; font-size: 11px; margin-bottom: 5px; text-transform: uppercase;">
                                                    Account / Cost Center</div>
                                                <div style="color: #2c3e50; font-size: 14px;">{{ $requisition->account
                                                    ?? 'N/A' }} / {{ $requisition->cost_center ?? '-' }}</div>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            @if($requisition->requisitionItems->isNotEmpty())
                            <div style="margin-bottom: 30px;">
                                <p
                                    style="font-size: 12px; font-weight: 700; color: #4b5563; text-transform: uppercase; border-bottom: 2px solid #e5e7eb; padding-bottom: 5px; margin: 0 0 7px 0;">
                                    📦 Requested Items</p>
                                <table width="100%" cellpadding="0" cellspacing="0" border="0"
                                    style="border-collapse: collapse; border: 1px solid #e9ecef;">
                                    <thead>
                                        <tr bgcolor="#b8871a">
                                            <th
                                                style="color: white; padding: 10px 8px; text-align: left; font-size: 11px;">
                                                Item Code</th>
                                            <th
                                                style="color: white; padding: 10px 8px; text-align: left; font-size: 11px;">
                                                Item Name</th>
                                            <th
                                                style="color: white; padding: 10px 8px; text-align: center; font-size: 11px;">
                                                Unit</th>
                                            <th
                                                style="color: white; padding: 10px 8px; text-align: center; font-size: 11px;">
                                                Qty</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($requisition->requisitionItems as $item)
                                        <tr bgcolor="{{ $loop->even ? '#f8f9fa' : '#ffffff' }}">
                                            <td style="padding: 10px 8px; border: 1px solid #e9ecef; font-size: 12px;">
                                                {{ $item->itemMaster->item_master_code ?? '-' }}</td>
                                            <td style="padding: 10px 8px; border: 1px solid #e9ecef; font-size: 12px;">
                                                {{ $item->itemMaster->item_master_name ?? '-' }}</td>
                                            <td
                                                style="padding: 10px 8px; border: 1px solid #e9ecef; font-size: 12px; text-align: center;">
                                                {{ $item->itemMaster->unit ?? '-' }}</td>
                                            <td
                                                style="padding: 10px 8px; border: 1px solid #e9ecef; font-size: 12px; text-align: center; font-weight: bold;">
                                                {{ $item->quantity_required }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @endif

                            <table align="center" border="0" cellpadding="0" cellspacing="0"
                                style="margin: 0 auto; border-collapse: collapse;">
                                <tr>
                                    <td style="padding: 25px; text-align: center;">
                                        @if($mail_type === 'completed_notification')
                                        <p style="color: #856404; margin: 0; font-weight: bold;">✅ Requisition Completed
                                        </p>
                                        @elseif($mail_type === 'warehouse_process')
                                        <h3 style="color: #2c3e50; margin: 0 0 15px 0; font-size: 18px;">📦 Warehouse
                                            Action Required</h3>
                                        <table align="center" border="0" cellpadding="0" cellspacing="0"
                                            style="margin: 0 auto; border-collapse: collapse;">
                                            <tr>
                                                <td style="padding: 0 5px;">
                                                    <table border="0" cellpadding="0" cellspacing="0"
                                                        style="border-collapse: collapse;">
                                                        <tr>
                                                            <td align="center" bgcolor="#007bff"
                                                                style="border-radius: 4px;">
                                                                <a href="{{ $submit_url ?? $review_url }}"
                                                                    style="display: inline-block; padding: 12px 25px; font-family: Arial, sans-serif; font-size: 14px; color: #ffffff; text-decoration: none; font-weight: bold; border: 1px solid #007bff; border-radius: 4px; mso-padding-alt: 12px 25px;">✅
                                                                    Submit Process</a>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                        @else
                                        <h3 style="color: #2c3e50; margin: 0 0 15px 0; font-size: 18px;">⚡ Take Action
                                        </h3>
                                        <table align="center" border="0" cellpadding="0" cellspacing="0"
                                            style="margin: 0 auto; border-collapse: collapse;">
                                            <tr>
                                                <td style="padding: 0 5px;">
                                                    <table border="0" cellpadding="0" cellspacing="0"
                                                        style="border-collapse: collapse;">
                                                        <tr>
                                                            <td align="center" bgcolor="#28a745"
                                                                style="border-radius: 4px;">
                                                                <a href="{{ $approve_url }}"
                                                                    style="display: inline-block; padding: 12px 25px; font-family: Arial, sans-serif; font-size: 14px; color: #ffffff; text-decoration: none; font-weight: bold; border: 1px solid #28a745; border-radius: 4px; mso-padding-alt: 12px 25px;">✅
                                                                    Approve</a>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                                <td style="padding: 0 5px;">
                                                    <table border="0" cellpadding="0" cellspacing="0"
                                                        style="border-collapse: collapse;">
                                                        <tr>
                                                            <td align="center" bgcolor="#dc3545"
                                                                style="border-radius: 4px;">
                                                                <a href="{{ $reject_url }}"
                                                                    style="display: inline-block; padding: 12px 25px; font-family: Arial, sans-serif; font-size: 14px; color: #ffffff; text-decoration: none; font-weight: bold; border: 1px solid #dc3545; border-radius: 4px; mso-padding-alt: 12px 25px;">❌
                                                                    Reject</a>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                                <td style="padding: 0 5px;">
                                                    <table border="0" cellpadding="0" cellspacing="0"
                                                        style="border-collapse: collapse;">
                                                        <tr>
                                                            <td align="center" bgcolor="#007bff"
                                                                style="border-radius: 4px;">
                                                                <a href="{{ $review_url }}"
                                                                    style="display: inline-block; padding: 12px 25px; font-family: Arial, sans-serif; font-size: 14px; color: #ffffff; text-decoration: none; font-weight: bold; border: 1px solid #007bff; border-radius: 4px; mso-padding-alt: 12px 25px;">📝
                                                                    Review</a>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                        @endif
                                    </td>
                                </tr>
                            </table>

                            <table width="100%" border="0" cellpadding="0" cellspacing="0"
                                style="border-collapse: collapse; margin: 10px 0;">
                                <tr>
                                    <td align="center" bgcolor="#f8f9fa"
                                        style="padding: 15px; border-radius: 8px; border: 1px solid #e9ecef;">
                                        <table align="center" border="0" cellpadding="0" cellspacing="0"
                                            style="margin: 0 auto; border-collapse: collapse;">
                                            <tr>
                                                <td style="padding: 0 5px;">
                                                    <table border="0" cellpadding="0" cellspacing="0"
                                                        style="border-collapse: collapse;">
                                                        <tr>
                                                            <td align="center" bgcolor="#343a40"
                                                                style="border-radius: 4px;">
                                                                <a href="{{ $download_url ?? '#' }}"
                                                                    style="display: inline-block; padding: 12px 25px; font-family: Arial, sans-serif; font-size: 14px; color: #ffffff; text-decoration: none; font-weight: bold; border: 1px solid #343a40; border-radius: 4px; mso-padding-alt: 12px 25px;">⬇️
                                                                    Download PDF</a>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>

                    <tr>
                        <td align="center" bgcolor="#2c3e50" style="padding: 30px; font-size: 11px; color: #9ca3af;">
                            <div style="font-size: 14px; font-weight: 600; color: white; margin-bottom: 5px;">{{
                                config('app.name') }}</div>
                            <div style="font-size: 12px; opacity: 0.8; margin-bottom: 10px;">Automated Requisition
                                System</div>
                            <div style="height: 1px; background: rgba(255, 255, 255, 0.2); margin: 10px 0;"></div>
                            <div style="font-size: 11px; opacity: 0.7;">
                                © {{ date('Y') }} PT. Sinar Meadow International Indonesia. All rights reserved.<br>
                                Please do not reply directly to this email.
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
