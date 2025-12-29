<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Warehouse Approval Request</title>
</head>

<body
    style="margin: 0; padding: 0; background-color: #f3f4f6; font-family: Arial, sans-serif; color: #333; line-height: 1.6; height: 100% !important; width: 100% !important; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%;">

    <table border="0" cellpadding="0" cellspacing="0" width="100%"
        style="border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
        <tr>
            <td align="center" style="padding: 20px 0;">

                <table border="0" cellpadding="0" cellspacing="0" width="100%" align="center"
                    style="max-width: 700px; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05); border-collapse: collapse;"
                    class="email-container">

                    <tr>
                        <td bgcolor="#b8871a" style="padding: 30px 40px; text-align: center;">
                            <img src="{{ asset('storage/logo.png') }}" alt="{{ config('app.name') }}"
                                style="max-height: 50px; width: auto; margin-bottom: 15px; display: block; margin: 0 auto 15px; border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; -ms-interpolation-mode: bicubic;">
                            <h1
                                style="font-size: 24px; font-weight: 700; margin: 0; color: white; mso-line-height-alt: 30px;">
                                Warehouse Approval Request</h1>
                            <p style="font-size: 14px; margin: 10px 0 0 0; opacity: 0.9; color: #f9f9f9;">
                                {{ $tracking->current_position }} - Approval Needed</p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 30px 40px;">

                            <div
                                style="font-size: 15px; color: #2c3e50; margin-bottom: 25px; padding: 15px; background-color: #f8f9fa; border-radius: 4px; border-left: 4px solid #cc982f;">
                                <strong>Hello {{ $approver->name }},</strong><br>
                                You have received a warehouse approval request. Please review the details below and
                                provide your decision.
                                <div
                                    style="display: inline-block; background-color: #cc982f; color: white; padding: 6px 12px; border-radius: 15px; font-weight: 600; font-size: 12px; margin-top: 10px; mso-padding-alt: 6px 12px;">
                                    📦 Approval Level: {{ $tracking->current_position ?? 'N/A' }}
                                </div>
                            </div>

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
                                                            Position</div>
                                                        <div
                                                            style="color: #2c3e50; font-size: 14px; word-break: break-word;">
                                                            {{ $tracking->current_position ?? 'N/A' }}</div>
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

                            <div style="margin-bottom: 30px;">
                                <table width="100%" cellpadding="0" cellspacing="0" border="0" align="center"
                                    style="border-collapse: collapse; font-size: 13px;">
                                    <tr>
                                        <td colspan="2" style="padding: 0 0 5px 0;">
                                            <p
                                                style="font-size: 12px; font-weight: 700; color: #4b5563; text-transform: uppercase; border-bottom: 2px solid #e5e7eb; padding-bottom: 5px; margin: 0;">
                                                💼 Account Information</p>
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
                                                            Account</div>
                                                        <div
                                                            style="color: #2c3e50; font-size: 14px; word-break: break-word;">
                                                            {{ $requisition->account ?? 'N/A' }}</div>
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
                                                            Requester</div>
                                                        <div
                                                            style="color: #2c3e50; font-size: 14px; word-break: break-word;">
                                                            {{ $requisition->requester->name ?? 'N/A' }}</div>
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
                                                            Print Batch</div>
                                                        <div
                                                            style="color: #2c3e50; font-size: 14px; word-break: break-word;">
                                                            @if($requisition->print_batch == 1)
                                                            <span
                                                                style="display: inline-block; background: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 6px 12px; border-radius: 15px; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; mso-padding-alt: 6px 12px;">
                                                                ✅ Yes
                                                            </span>
                                                            @else
                                                            <span
                                                                style="display: inline-block; background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 6px 12px; border-radius: 15px; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; mso-padding-alt: 6px 12px;">
                                                                ❌ No
                                                            </span>
                                                            @endif
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
                                                            Current Status</div>
                                                        <div
                                                            style="color: #2c3e50; font-size: 14px; word-break: break-word;">
                                                            <span
                                                                style="display: inline-block; background: #cce7ff; color: #0066cc; border: 1px solid #99d6ff; padding: 6px 12px; border-radius: 15px; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; mso-padding-alt: 6px 12px;">
                                                                🔄 {{ $tracking->current_position }}
                                                            </span>
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
                                                🎯 Objectives</p>
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

                            @if($requisition->requisitionItems && $requisition->requisitionItems->count() > 0)
                            <div style="margin-bottom: 30px;">
                                <table width="100%" cellpadding="0" cellspacing="0" border="0" align="center"
                                    style="border-collapse: collapse;">
                                    <tr>
                                        <td style="padding: 0 0 5px 0;">
                                            <p
                                                style="font-size: 12px; font-weight: 700; color: #4b5563; text-transform: uppercase; border-bottom: 2px solid #e5e7eb; padding-bottom: 5px; margin: 0;">
                                                📦 Product Details</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 7px 0;">
                                            <div style="overflow-x: auto;">
                                                <table width="100%" cellpadding="0" cellspacing="0" border="0"
                                                    align="center"
                                                    style="width: 100%; border-collapse: collapse; margin-top: 0; border: 1px solid #e9ecef;">
                                                    <thead>
                                                        <tr>
                                                            <th
                                                                style="background-color: #b8871a; color: white; padding: 10px 8px; text-align: left; font-weight: 600; font-size: 12px; border: 1px solid #b8871a;">
                                                                Item Name</th>
                                                            <th
                                                                style="background-color: #b8871a; color: white; padding: 10px 8px; text-align: left; font-weight: 600; font-size: 12px; border: 1px solid #b8871a;">
                                                                Item Code</th>
                                                            <th
                                                                style="background-color: #b8871a; color: white; padding: 10px 8px; text-align: center; font-weight: 600; font-size: 12px; border: 1px solid #b8871a;">
                                                                Qty Req</th>
                                                            <th
                                                                style="background-color: #b8871a; color: white; padding: 10px 8px; text-align: center; font-weight: 600; font-size: 12px; border: 1px solid #b8871a;">
                                                                Qty Iss</th>
                                                            <th
                                                                style="background-color: #b8871a; color: white; padding: 10px 8px; text-align: center; font-weight: 600; font-size: 12px; border: 1px solid #b8871a;">
                                                                Batch No.</th>
                                                            <th
                                                                style="background-color: #b8871a; color: white; padding: 10px 8px; text-align: center; font-weight: 600; font-size: 12px; border: 1px solid #b8871a;">
                                                                Remarks</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($requisition->requisitionItems as $item)
                                                        @php
                                                        $row_bg = $loop->even ? '#f8f9fa' : 'white';
                                                        @endphp
                                                        <tr style="background-color: {{ $row_bg }};">
                                                            <td
                                                                style="padding: 10px 8px; border: 1px solid #e9ecef; font-size: 12px;">
                                                                {{ $item->itemMaster->item_master_name ?? 'N/A' }}</td>
                                                            <td
                                                                style="padding: 10px 8px; border: 1px solid #e9ecef; font-size: 12px;">
                                                                {{ $item->itemMaster->item_master_code ?? 'N/A' }}</td>
                                                            <td
                                                                style="padding: 10px 8px; border: 1px solid #e9ecef; font-size: 12px; text-align: center; font-weight: 600;">
                                                                {{ $item->quantity_required ?? 0 }}</td>
                                                            <td
                                                                style="padding: 10px 8px; border: 1px solid #e9ecef; font-size: 12px; text-align: center; font-weight: 600;">
                                                                {{ $item->quantity_issued ?? 0 }}</td>
                                                            <td
                                                                style="padding: 10px 8px; border: 1px solid #e9ecef; font-size: 12px; text-align: center;">
                                                                {{ $item->batch_number ?? 'N/A' }}</td>
                                                            <td
                                                                style="padding: 10px 8px; border: 1px solid #e9ecef; font-size: 12px; text-align: center;">
                                                                {{ $item->remarks ?? '-' }}</td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            @endif


                            <table width="100%" cellpadding="0" cellspacing="0" border="0" align="center"
                                style="border-collapse: collapse; margin: 30px 0; border: 1px solid #cc982f; border-radius: 4px; background: #f8f9fa;">
                                <tr>
                                    <td style="padding: 15px; background: #f8f9fa; border-radius: 4px;">

                                        <div style="text-align: center;">
                                            <h3
                                                style="font-size: 18px; font-weight: 700; color: #2c3e50; margin-bottom: 10px;">
                                                ⚡ Take Action</h3>
                                            <p style="font-size: 14px; color: #6c757d; margin-bottom: 20px;">Please
                                                review the request above and choose your action below</p>

                                            <table align="center" border="0" cellpadding="0" cellspacing="0"
                                                style="margin: 0 auto; border-collapse: collapse;">
                                                <tr>
                                                    <td style="padding: 0 5px;">
                                                        <table border="0" cellpadding="0" cellspacing="0"
                                                            style="border-collapse: collapse;">
                                                            <tr>
                                                                <td align="center" bgcolor="#28a745"
                                                                    style="border-radius: 4px;">
                                                                    <a href="{{ $quickOkLink }}"
                                                                        style="display: inline-block; padding: 12px 25px; font-family: Arial, sans-serif; font-size: 14px; color: #ffffff; text-decoration: none; font-weight: bold; border: 1px solid #28a745; border-radius: 4px; mso-padding-alt: 12px 25px;">✅
                                                                        Quick OK</a>
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
                                                                    <a href="{{ $okWithReviewLink }}"
                                                                        style="display: inline-block; padding: 12px 25px; font-family: Arial, sans-serif; font-size: 14px; color: #ffffff; text-decoration: none; font-weight: bold; border: 1px solid #007bff; border-radius: 4px; mso-padding-alt: 12px 25px;">📝
                                                                        OK with Review</a>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                    <td style="padding: 0 5px;">
                                                        <table border="0" cellpadding="0" cellspacing="0"
                                                            style="border-collapse: collapse;">
                                                            <tr>
                                                                <td align="center" bgcolor="#17a2b8"
                                                                    style="border-radius: 4px;">
                                                                    <a href="{{ $okWithUpdateLink }}"
                                                                        style="display: inline-block; padding: 12px 25px; font-family: Arial, sans-serif; font-size: 14px; color: #ffffff; text-decoration: none; font-weight: bold; border: 1px solid #17a2b8; border-radius: 4px; mso-padding-alt: 12px 25px;">📝
                                                                        OK with Update</a>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <div style="text-align: center; margin: 30px 0;">
                                <table align="center" border="0" cellpadding="0" cellspacing="0"
                                    style="border-collapse: collapse;">
                                    <tr>
                                        <td align="center" bgcolor="#17a2b8" style="border-radius: 4px;">
                                            <a href="{{ route('complain.warehouse.report', ['id' => $requisition->id]) }}"
                                                target="_blank"
                                                style="display: inline-block; padding: 12px 24px; background: #17a2b8; color: white; text-decoration: none; border-radius: 4px; font-weight: 600; font-size: 14px; min-width: 200px; mso-padding-alt: 12px 24px; border: 1px solid #17a2b8;">
                                                📄 Download Report
                                            </a>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <table width="100%" cellpadding="0" cellspacing="0" border="0" align="center"
                                style="border-collapse: collapse; margin-top: 20px; border: 1px solid #ffeaa7; border-radius: 4px; background: #fff3cd;">
                                <tr>
                                    <td style="padding: 15px; background: #fff3cd; border-radius: 4px;">

                                        <div style="text-align: center;">
                                            <h4 style="color: #856404; margin: 0 0 8px 0; font-size: 14px;">⚠️ Important
                                                Notice</h4>

                                            <table border="0" cellpadding="0" cellspacing="0" align="center"
                                                style="border-collapse: collapse; max-width: 450px; margin: 0 auto 0 auto; color: #856404; font-size: 13px;">
                                                <tr>
                                                    <td align="left" style="padding: 0;">
                                                        <ul
                                                            style="color: #856404; margin: 0; padding-left: 20px; font-size: 13px; list-style-type: disc; text-align: left;">
                                                            <li style="margin-bottom: 5px;">This warehouse approval is
                                                                part of the requisition process</li>
                                                            <li style="margin-bottom: 5px;">Quick OK will process the
                                                                approval immediately</li>
                                                            <li style="margin-bottom: 5px;">Use "OK with Review" if you
                                                                need to add comments</li>
                                                            <li>Your prompt action helps maintain operational efficiency
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
