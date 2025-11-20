<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Requisition Approval Request</title>

    <style type="text/css">
        /* CSS Minimalis */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
            color: #333;
            line-height: 1.6;
        }

        table {
            border-collapse: collapse;
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }

        ul {
            margin: 0;
            padding-left: 20px;
        }

        li {
            line-height: 1.5;
        }
    </style>
</head>

<body
    style="margin: 0; padding: 0; background-color: #f8f9fa; color: #333; line-height: 1.6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">

    <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" role="presentation"
        style="max-width: 800px; width: 100%; margin: 0 auto; background-color: #f8f9fa;">
        <tr>
            <td align="center" valign="top" style="padding: 20px 0;">

                <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%"
                    style="max-width: 800px; background-color: white; border-collapse: collapse;">

                    <tr>
                        <td style="padding: 0;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%"
                                style="background-color: #b8871a; color: white; text-align: center;">
                                <tr>
                                    <td style="padding: 30px 40px;">
                                        <img src="{{ asset('storage/logo.png') }}" alt="{{ config('app.name') }}"
                                            style="max-height: 50px; height: auto; margin-bottom: 15px; border: 0;">
                                        <h1 style="font-size: 28px; font-weight: 700; margin: 0; line-height: 32px;">
                                            Requisition Approval Request</h1>
                                        <p
                                            style="font-size: 16px; margin: 10px 0 0 0; opacity: 0.9; line-height: 24px;">
                                            Sales & Marketing - Packaging Replacement Request</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 40px;">

                            <table border="0" cellpadding="20" cellspacing="0" width="100%"
                                style="font-size: 18px; color: #2c3e50; background-color: #f8f9fa; border-left: 4px solid #cc982f;">
                                <tr>
                                    <td>
                                        <strong>Hello {{ $approver->name }},</strong><br>
                                        You have received a new requisition approval request. Please review the details
                                        below and provide your decision.
                                    </td>
                                </tr>
                            </table>

                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td height="25" style="font-size: 1px; line-height: 25px;">&nbsp;</td>
                                </tr>
                            </table>


                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td>
                                        <table border="0" cellpadding="12" cellspacing="0" width="100%"
                                            style="background-color: #b8871a; color: white; font-weight: 600; font-size: 16px; margin: 0;">
                                            <tr>
                                                <td>📄 Request Information</td>
                                            </tr>
                                        </table>

                                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                            <tr>
                                                <td height="15" style="font-size: 1px; line-height: 15px;">&nbsp;</td>
                                            </tr>
                                        </table>

                                        <table border="0" cellpadding="0" cellspacing="0" width="100%"
                                            style="background-color: #f8f9fa; border: 1px solid #e9ecef;">
                                            <tr>
                                                <td width="50%" style="width: 50%; vertical-align: top; padding: 7px;">
                                                    <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                                        <tr>
                                                            <td
                                                                style="background-color: white; padding: 15px; border: 1px solid #e9ecef;">
                                                                <p
                                                                    style="font-weight: 600; color: #495057; font-size: 14px; margin: 0 0 5px 0;">
                                                                    Request Number</p>
                                                                <p
                                                                    style="color: #2c3e50; font-size: 15px; word-break: break-word; margin: 0;">
                                                                    {{ $requisition->no_srs }}</p>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                                <td width="50%" style="width: 50%; vertical-align: top; padding: 7px;">
                                                    <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                                        <tr>
                                                            <td
                                                                style="background-color: white; padding: 15px; border: 1px solid #e9ecef;">
                                                                <p
                                                                    style="font-weight: 600; color: #495057; font-size: 14px; margin: 0 0 5px 0;">
                                                                    Request Date</p>
                                                                <p
                                                                    style="color: #2c3e50; font-size: 15px; word-break: break-word; margin: 0;">
                                                                    {{
                                                                    \Carbon\Carbon::parse($requisition->request_date)->format('d
                                                                    M Y') }}</p>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width="50%" style="width: 50%; vertical-align: top; padding: 7px;">
                                                    <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                                        <tr>
                                                            <td
                                                                style="background-color: white; padding: 15px; border: 1px solid #e9ecef;">
                                                                <p
                                                                    style="font-weight: 600; color: #495057; font-size: 14px; margin: 0 0 5px 0;">
                                                                    Requester</p>
                                                                <p
                                                                    style="color: #2c3e50; font-size: 15px; word-break: break-word; margin: 0;">
                                                                    {{ $requisition->requester->name ?? 'N/A' }}</p>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                                <td width="50%" style="width: 50%; vertical-align: top; padding: 7px;">
                                                    <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                                        <tr>
                                                            <td
                                                                style="background-color: white; padding: 15px; border: 1px solid #e9ecef;">
                                                                <p
                                                                    style="font-weight: 600; color: #495057; font-size: 14px; margin: 0 0 5px 0;">
                                                                    Current Status</p>
                                                                <p
                                                                    style="color: #2c3e50; font-size: 15px; word-break: break-word; margin: 0;">
                                                                    @if($requisition->status == 'Pending')
                                                                    <span
                                                                        style="display: inline-block; padding: 8px 15px; font-weight: 600; font-size: 14px; text-transform: uppercase; background-color: #fff3cd; color: #856404; border: 1px solid #ffeaa7;">⏳
                                                                        Pending</span>
                                                                    @elseif($requisition->status == 'In Progress')
                                                                    <span
                                                                        style="display: inline-block; padding: 8px 15px; font-weight: 600; font-size: 14px; text-transform: uppercase; background-color: #cce7ff; color: #0066cc; border: 1px solid #99d6ff;">🔄
                                                                        In Progress</span>
                                                                    @elseif($requisition->status == 'Approved')
                                                                    <span
                                                                        style="display: inline-block; padding: 8px 15px; font-weight: 600; font-size: 14px; text-transform: uppercase; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb;">✅
                                                                        Approved</span>
                                                                    @elseif($requisition->status == 'Rejected')
                                                                    <span
                                                                        style="display: inline-block; padding: 8px 15px; font-weight: 600; font-size: 14px; text-transform: uppercase; background-color: #f8d7da; color: #721c24; border: 1px solid #f1b0b7;">❌
                                                                        Rejected</span>
                                                                    @else
                                                                    <span
                                                                        style="display: inline-block; padding: 8px 15px; font-weight: 600; font-size: 14px; text-transform: uppercase; background-color: #e9ecef; color: #333; border: 1px solid #ccc;">{{
                                                                        $requisition->status }}</span>
                                                                    @endif
                                                                </p>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td height="30" style="font-size: 1px; line-height: 30px;">&nbsp;</td>
                                </tr>
                            </table>

                            @if($requisition->customer)
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td>
                                        <table border="0" cellpadding="12" cellspacing="0" width="100%"
                                            style="background-color: #b8871a; color: white; font-weight: 600; font-size: 16px; margin: 0;">
                                            <tr>
                                                <td>👤 Customer Information</td>
                                            </tr>
                                        </table>

                                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                            <tr>
                                                <td height="15" style="font-size: 1px; line-height: 15px;">&nbsp;</td>
                                            </tr>
                                        </table>

                                        <table border="0" cellpadding="0" cellspacing="0" width="100%"
                                            style="background-color: #f8f9fa; border: 1px solid #e9ecef;">
                                            <tr>
                                                <td width="50%" style="width: 50%; vertical-align: top; padding: 7px;">
                                                    <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                                        <tr>
                                                            <td
                                                                style="background-color: white; padding: 15px; border: 1px solid #e9ecef;">
                                                                <p
                                                                    style="font-weight: 600; color: #495057; font-size: 14px; margin: 0 0 5px 0;">
                                                                    Customer Name</p>
                                                                <p
                                                                    style="color: #2c3e50; font-size: 15px; word-break: break-word; margin: 0;">
                                                                    {{ $requisition->customer->name }}</p>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                                <td width="50%" style="width: 50%; vertical-align: top; padding: 7px;">
                                                    <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                                        <tr>
                                                            <td
                                                                style="background-color: white; padding: 15px; border: 1px solid #e9ecef;">
                                                                <p
                                                                    style="font-weight: 600; color: #495057; font-size: 14px; margin: 0 0 5px 0;">
                                                                    Customer Address</p>
                                                                <p
                                                                    style="color: #2c3e50; font-size: 15px; word-break: break-word; margin: 0;">
                                                                    {{ $requisition->customer->address ?? 'N/A' }}</p>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width="50%" style="width: 50%; vertical-align: top; padding: 7px;">
                                                    <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                                        <tr>
                                                            <td
                                                                style="background-color: white; padding: 15px; border: 1px solid #e9ecef;">
                                                                <p
                                                                    style="font-weight: 600; color: #495057; font-size: 14px; margin: 0 0 5px 0;">
                                                                    Account</p>
                                                                <p
                                                                    style="color: #2c3e50; font-size: 15px; word-break: break-word; margin: 0;">
                                                                    {{ $requisition->account ?? 'N/A' }}</p>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                                <td width="50%" style="width: 50%; vertical-align: top; padding: 7px;">
                                                    <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                                        <tr>
                                                            <td
                                                                style="background-color: white; padding: 15px; border: 1px solid #e9ecef;">
                                                                <p
                                                                    style="font-weight: 600; color: #495057; font-size: 14px; margin: 0 0 5px 0;">
                                                                    Cost Center</p>
                                                                <p
                                                                    style="color: #2c3e50; font-size: 15px; word-break: break-word; margin: 0;">
                                                                    {{ $requisition->cost_center }}</p>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td height="30" style="font-size: 1px; line-height: 30px;">&nbsp;</td>
                                </tr>
                            </table>
                            @endif

                            @if($requisition->objectives)
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td>
                                        <table border="0" cellpadding="12" cellspacing="0" width="100%"
                                            style="background-color: #b8871a; color: white; font-weight: 600; font-size: 16px; margin: 0;">
                                            <tr>
                                                <td>🎯 Objectives</td>
                                            </tr>
                                        </table>

                                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                            <tr>
                                                <td height="15" style="font-size: 1px; line-height: 15px;">&nbsp;</td>
                                            </tr>
                                        </table>

                                        <table border="0" cellpadding="20" cellspacing="0" width="100%"
                                            style="background-color: #f8f9fa; border: 1px solid #e9ecef;">
                                            <tr>
                                                <td>
                                                    <p style="margin: 0; color: #2c3e50; line-height: 1.6;">{{
                                                        $requisition->objectives }}</p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td height="30" style="font-size: 1px; line-height: 30px;">&nbsp;</td>
                                </tr>
                            </table>
                            @endif

                            @if($requisition->requisitionItems && $requisition->requisitionItems->count() > 0)
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td>
                                        <table border="0" cellpadding="12" cellspacing="0" width="100%"
                                            style="background-color: #b8871a; color: white; font-weight: 600; font-size: 16px; margin: 0;">
                                            <tr>
                                                <td>📦 Product Details</td>
                                            </tr>
                                        </table>

                                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                            <tr>
                                                <td height="15" style="font-size: 1px; line-height: 15px;">&nbsp;</td>
                                            </tr>
                                        </table>

                                        <table border="0" cellpadding="20" cellspacing="0" width="100%"
                                            style="background-color: #f8f9fa; border: 1px solid #e9ecef;">
                                            <tr>
                                                <td style="padding: 0;">
                                                    <table border="0" cellpadding="10" cellspacing="0" width="100%"
                                                        style="border-collapse: collapse; border: 1px solid #e9ecef;">
                                                        <thead>
                                                            <tr>
                                                                <th
                                                                    style="background-color: #b8871a; color: white; padding: 12px; text-align: left; font-weight: 600; font-size: 14px;">
                                                                    Material Type</th>
                                                                <th
                                                                    style="background-color: #b8871a; color: white; padding: 12px; text-align: left; font-weight: 600; font-size: 14px;">
                                                                    Product Code</th>
                                                                <th
                                                                    style="background-color: #b8871a; color: white; padding: 12px; text-align: left; font-weight: 600; font-size: 14px;">
                                                                    Product Name</th>
                                                                <th
                                                                    style="background-color: #b8871a; color: white; padding: 12px; text-align: left; font-weight: 600; font-size: 14px;">
                                                                    Unit</th>
                                                                <th
                                                                    style="background-color: #b8871a; color: white; padding: 12px; text-align: center; font-weight: 600; font-size: 14px;">
                                                                    QTY Req</th>
                                                                <th
                                                                    style="background-color: #b8871a; color: white; padding: 12px; text-align: center; font-weight: 600; font-size: 14px;">
                                                                    QTY Iss</th>
                                                                <th
                                                                    style="background-color: #b8871a; color: white; padding: 12px; text-align: center; font-weight: 600; font-size: 14px;">
                                                                    Batch No</th>
                                                                <th
                                                                    style="background-color: #b8871a; color: white; padding: 12px; text-align: left; font-weight: 600; font-size: 14px;">
                                                                    Remarks</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($requisition->requisitionItems as $item)
                                                            @php
                                                            $detail = $item->itemMaster->ItemDetails->firstWhere('id',
                                                            $item->item_detail_id);
                                                            $bgColor = $loop->even ? '#f8f9fa' : 'white';
                                                            @endphp
                                                            @if($detail)
                                                            <tr style="background-color: {{ $bgColor }};">
                                                                <td
                                                                    style="padding: 12px; border-bottom: 1px solid #e9ecef; font-size: 14px;">
                                                                    {{ $detail->material_type ?? '-' }}</td>
                                                                <td
                                                                    style="padding: 12px; border-bottom: 1px solid #e9ecef; font-size: 14px;">
                                                                    {{ $detail->item_detail_code ?? '-' }}</td>
                                                                <td
                                                                    style="padding: 12px; border-bottom: 1px solid #e9ecef; font-size: 14px;">
                                                                    {{ $detail->item_detail_name ?? '-' }}</td>
                                                                <td
                                                                    style="padding: 12px; border-bottom: 1px solid #e9ecef; font-size: 14px;">
                                                                    {{ $detail->unit ?? '-' }}</td>
                                                                <td
                                                                    style="padding: 12px; border-bottom: 1px solid #e9ecef; font-size: 14px; text-align: center; font-weight: 600;">
                                                                    {{ $item->quantity_required }}</td>
                                                                <td
                                                                    style="padding: 12px; border-bottom: 1px solid #e9ecef; font-size: 14px; text-align: center; font-weight: 600;">
                                                                    {{ $item->quantity_issued }}</td>
                                                                <td
                                                                    style="padding: 12px; border-bottom: 1px solid #e9ecef; font-size: 14px; text-align: center; font-weight: 600;">
                                                                    {{ date('j/n/y', strtotime($item->batch_number)) ??
                                                                    '-' }}
                                                                </td>
                                                                <td
                                                                    style="padding: 12px; border-bottom: 1px solid #e9ecef; font-size: 14px; text-align: left;">
                                                                    {{ $item->remarks ?? '-' }}</td>
                                                            </tr>
                                                            @endif
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td height="30" style="font-size: 1px; line-height: 30px;">&nbsp;</td>
                                </tr>
                            </table>
                            @endif

                            <table border="0" cellpadding="0" cellspacing="0" width="100%"
                                style="background-color: #f8f9fa; text-align: center; border: 2px solid #cc982f;">
                                <tr>
                                    <td align="center" style="padding: 30px;">
                                        <h3
                                            style="font-size: 20px; font-weight: 700; color: #2c3e50; margin: 0 0 15px 0;">
                                            ⚡ Take Action</h3>
                                        <p style="color: #6c757d; margin: 0 0 25px 0; font-size: 16px;">Please review
                                            the request above and choose your action below</p>

                                        <table border="0" cellpadding="0" cellspacing="0"
                                            style="margin: 0 auto; border-collapse: collapse;">
                                            <tr>
                                                <td style="padding: 7px;">
                                                    <a href="{{ $approveLink }}"
                                                        style="display: inline-block; padding: 12px 24px; text-decoration: none; font-weight: 600; font-size: 16px; text-align: center; min-width: 140px; background-color: #28a745; color: white;">
                                                        ✅ Quick Approve
                                                    </a>
                                                </td>
                                                <td style="padding: 7px;">
                                                    <a href="{{ $rejectLink }}"
                                                        style="display: inline-block; padding: 12px 24px; text-decoration: none; font-weight: 600; font-size: 16px; text-align: center; min-width: 140px; background-color: #dc3545; color: white;">
                                                        ❌ Quick Reject
                                                    </a>
                                                </td>
                                                <td style="padding: 7px;">
                                                    <a href="{{ $approveWithReviewLink }}"
                                                        style="display: inline-block; padding: 12px 24px; text-decoration: none; font-weight: 600; font-size: 16px; text-align: center; min-width: 140px; background-color: #007bff; color: white;">
                                                        📝 Review with Notes
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td height="20" style="font-size: 1px; line-height: 20px;">&nbsp;</td>
                                </tr>
                            </table>

                            <table border="0" cellpadding="20" cellspacing="0" width="100%"
                                style="background-color: #fff3cd; border: 1px solid #ffeaa7;">
                                <tr>
                                    <td>
                                        <h4 style="color: #856404; margin: 0 0 10px 0;">⚠️ Important Notice</h4>
                                        <ul style="color: #856404; margin: 0; padding-left: 20px;">
                                            <li style="line-height: 1.5;">This approval request is time-sensitive</li>
                                            <li style="line-height: 1.5;">Quick actions (Approve/Reject) will be
                                                processed
                                                immediately</li>
                                            <li style="line-height: 1.5;">Use "Review with Notes" if you need to add
                                                comments</li>
                                            <li style="line-height: 1.5;">Contact the requester directly if you need
                                                additional information</li>
                                        </ul>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 0;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%"
                                style="background-color: #2c3e50; color: white; text-align: center;">
                                <tr>
                                    <td style="padding: 30px 40px;" align="center">
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%"
                                            style="max-width: 600px; margin: 0 auto;">
                                            <tr>
                                                <td>
                                                    <p style="font-size: 18px; font-weight: 600; margin: 0 0 10px 0;">{{
                                                        config('app.name') }}</p>
                                                    <p style="font-size: 14px; opacity: 0.8; margin: 0 0 20px 0;">Sales
                                                        &
                                                        Marketing Department</p>
                                                    <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                                        <tr>
                                                            <td
                                                                style="height: 1px; background: rgba(255, 255, 255, 0.2); margin: 20px 0;">
                                                            </td>
                                                        </tr>
                                                    </table>
                                                    <p
                                                        style="font-size: 14px; opacity: 0.9; line-height: 1.8; margin-top: 20px;">
                                                        <strong>Need Help?</strong><br>
                                                        Contact IT Support: support@company.com<br>
                                                        Internal Extension: 1234
                                                    </p>
                                                    <p style="font-size: 12px; opacity: 0.7; margin-top: 15px;">
                                                        &copy; {{ date('Y') }} {{ config('app.name') }}. All rights
                                                        reserved.<br>
                                                        This is an automated message, please do not reply directly to
                                                        this email.
                                                    </p>
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
    </table>
</body>

</html>
