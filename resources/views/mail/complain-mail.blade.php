<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml"
    xmlns:o="urn:schemas-microsoft-com:office:office" lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="x-apple-disable-message-reformatting" />
    <title>Requisition Approval Request</title>
    <style>
        /* CSS Reset dan Gaya Universal */
        body,
        table,
        td,
        a {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }

        table,
        td {
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }

        img {
            -ms-interpolation-mode: bicubic;
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
        }

        a[x-apple-data-detectors] {
            color: inherit !important;
            text-decoration: none !important;
            font-size: inherit !important;
            font-family: inherit !important;
            font-weight: inherit !important;
            line-height: inherit !important;
        }

        /* Perbaikan untuk background-color pada baris ganjil */
        .tr-odd {
            background-color: #eeeeee !important;
        }

        .bg-f0 {
            background-color: #f0f0f0 !important;
        }
    </style>
</head>

<body style="margin: 0; word-spacing: normal; background-color: #f4f4f4;">
    <div role="article" aria-label="Permintaan Persetujuan Requisition" lang="en"
        style="font-size: 1px; line-height: 1px; mso-line-height-rule: exactly; background-color: #f4f4f4;">

        <table role="presentation" align="center" border="0" cellpadding="0" cellspacing="0" width="100%"
            style="border-collapse: collapse;">
            <tr>
                <td align="center">
                    <table role="presentation" align="center" border="0" cellpadding="0" cellspacing="0" width="600"
                        style="max-width: 800px; width: 100%; margin: auto; border: 1px solid #dddddd; border-collapse: collapse; background-color: #ffffff;">

                        <thead>
                            <tr class="header-style">
                                <td colspan="2"
                                    style="background-color: #ffc107; border: 1px solid #dddddd; border-bottom: none; font-family: Arial, sans-serif; color: #333333; mso-line-height-rule: exactly;">
                                    <h2
                                        style="margin: 0.3em 0; font-size: 24px; line-height: 1.2; font-weight: bold;">
                                        Requisition approval request</h2>
                                    <h5
                                        style="margin: 0.3em 0; font-size: 16px; line-height: 1.2; font-weight: normal;">
                                        {{ $requisition->department ?? 'Sales & Marketing' }}
                                        - {{ $requisition->type ?? 'Packaging Replacement Request' }}</h5>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2"
                                    style="border: 1px solid #dddddd; border-top: none; font-family: Arial, sans-serif; font-size: 14px; color: #333333; mso-line-height-rule: exactly;">
                                    <p style="margin: 0.3em 0; line-height: 1.4;">Dear <strong
                                            style="font-weight: bold;">{{ $approver->name ?? 'Approver' }}</strong>,</p>
                                    <p style="margin: 0.3em 0; line-height: 1.4;">
                                        You have received a new requisition approval request. Please review the details
                                        below and
                                        provide your decision.
                                    </p>
                                </td>
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <th colspan="2"
                                    style="text-align: center; background-color: #e0e0e0; border: 1px solid #dddddd; font-family: Arial, sans-serif; font-size: 14px; color: #333333; mso-line-height-rule: exactly;">
                                    <h5
                                        style="margin: 0.3em 0; font-size: 14px; font-weight: bold;">
                                        REQUEST DETAILS ({{ $requisition->no_srs ?? 'REQ-XXXXXX' }})</h5>
                                </th>
                            </tr>

                            <tr style="background-color: #eeeeee;" class="tr-odd">
                                <td width="20%"
                                    style="font-weight: bold; background-color: #f0f0f0; border: 1px solid #dddddd; font-family: Arial, sans-serif; color: #333333; font-size: 14px;"
                                    class="bg-f0">
                                    <span style="display: block; margin: 8px 15px;">Request Number:</span>
                                </td>
                                <td width="80%"
                                    style="border: 1px solid #dddddd; font-family: Arial, sans-serif; color: #333333; font-size: 14px;">
                                    <span style="font-weight: bold; display: block; margin: 8px 15px;">{{
                                        $requisition->no_srs ?? 'N/A' }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td width="20%"
                                    style="font-weight: bold; background-color: #f0f0f0; border: 1px solid #dddddd; font-family: Arial, sans-serif; color: #333333; font-size: 14px;"
                                    class="bg-f0">
                                    <span style="display: block; margin: 8px 15px;">Request Date:</span>
                                </td>
                                <td width="80%"
                                    style="border: 1px solid #dddddd; font-family: Arial, sans-serif; color: #333333; font-size: 14px;">
                                    <span style="display: block; margin: 8px 15px;">{{
                                        \Carbon\Carbon::parse($requisition->request_date)->format('d F Y') ?? 'N/A'
                                        }}</span>
                                </td>
                            </tr>
                            <tr style="background-color: #eeeeee;" class="tr-odd">
                                <td width="20%"
                                    style="font-weight: bold; background-color: #f0f0f0; border: 1px solid #dddddd; font-family: Arial, sans-serif; color: #333333; font-size: 14px;"
                                    class="bg-f0">
                                    <span style="display: block; margin: 8px 15px;">Requester:</span>
                                </td>
                                <td width="80%"
                                    style="border: 1px solid #dddddd; font-family: Arial, sans-serif; color: #333333; font-size: 14px;">
                                    <span style="display: block; margin: 8px 15px;">{{ $requisition->requester->name ??
                                        'N/A' }} (Dept: {{ $requisition->requester->department->name ?? 'N/A' }})</span>
                                </td>
                            </tr>
                            <tr>
                                <td width="20%"
                                    style="font-weight: bold; background-color: #f0f0f0; border: 1px solid #dddddd; font-family: Arial, sans-serif; color: #333333; font-size: 14px;"
                                    class="bg-f0">
                                    <span style="display: block; margin: 8px 15px;">Cost Center:</span>
                                </td>
                                <td width="80%"
                                    style="border: 1px solid #dddddd; font-family: Arial, sans-serif; color: #333333; font-size: 14px;">
                                    <span style="display: block; margin: 8px 15px;">{{ $requisition->cost_center ??
                                        'N/A' }}</span>
                                </td>
                            </tr>

                            <tr>
                                <th colspan="2"
                                    style="text-align: center; background-color: #e0e0e0; border: 1px solid #dddddd; font-family: Arial, sans-serif; font-size: 14px; color: #333333; mso-line-height-rule: exactly;">
                                    <h5
                                        style="margin: 0.3em 0; font-size: 14px; font-weight: bold;">
                                        CUSTOMER/ACCOUNT INFORMATION</h5>
                                </th>
                            </tr>
                            <tr style="background-color: #eeeeee;" class="tr-odd">
                                <td width="20%"
                                    style="font-weight: bold; background-color: #f0f0f0; border: 1px solid #dddddd; font-family: Arial, sans-serif; color: #333333; font-size: 14px;"
                                    class="bg-f0">
                                    <span style="display: block; margin: 8px 15px;">Customer Name:</span>
                                </td>
                                <td width="80%"
                                    style="border: 1px solid #dddddd; font-family: Arial, sans-serif; color: #333333; font-size: 14px;">
                                    <span style="display: block; margin: 8px 15px;">{{ $requisition->customer->name ??
                                        'N/A (Internal Request)' }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td width="20%"
                                    style="font-weight: bold; background-color: #f0f0f0; border: 1px solid #dddddd; font-family: Arial, sans-serif; color: #333333; font-size: 14px;"
                                    class="bg-f0">
                                    <span style="display: block; margin: 8px 15px;">Customer Address:</span>
                                </td>
                                <td width="80%"
                                    style="border: 1px solid #dddddd; font-family: Arial, sans-serif; color: #333333; font-size: 14px;">
                                    <span style="display: block; margin: 8px 15px;">{{ $requisition->customer->address
                                        ?? '-' }}</span>
                                </td>
                            </tr>
                            <tr style="background-color: #eeeeee;" class="tr-odd">
                                <td width="20%"
                                    style="font-weight: bold; background-color: #f0f0f0; border: 1px solid #dddddd; font-family: Arial, sans-serif; color: #333333; font-size: 14px;"
                                    class="bg-f0">
                                    <span style="display: block; margin: 8px 15px;">Account/Project:</span>
                                </td>
                                <td width="80%"
                                    style="border: 1px solid #dddddd; font-family: Arial, sans-serif; color: #333333; font-size: 14px;">
                                    <span style="display: block; margin: 8px 15px;">{{ $requisition->account ?? '-'
                                        }}</span>
                                </td>
                            </tr>

                            <tr>
                                <th colspan="2"
                                    style="text-align: center; background-color: #e0e0e0; border: 1px solid #dddddd; font-family: Arial, sans-serif; font-size: 14px; color: #333333; mso-line-height-rule: exactly;">
                                    <h5
                                        style="margin: 0.3em 0; font-size: 14px; font-weight: bold;">
                                        PRODUCT/MATERIAL DETAILS</h5>
                                </th>
                            </tr>

                            <tr>
                                <td colspan="2" style="border: none;">
                                    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%"
                                        style="width: 100%; border-collapse: collapse; border: none;">
                                        <thead>
                                            <tr style="background-color: #cccccc;">
                                                <th width="15%"
                                                    style="text-align: left; font-size: 12px; font-weight: bold; border: 1px solid #dddddd; font-family: Arial, sans-serif; color: #333333;">
                                                    <span style="display: block; margin: 8px 15px;">Material Type</span>
                                                </th>
                                                <th width="15%"
                                                    style="text-align: left; font-size: 12px; font-weight: bold; border: 1px solid #dddddd; font-family: Arial, sans-serif; color: #333333;">
                                                    <span style="display: block; margin: 8px 15px;">Product Code</span>
                                                </th>
                                                <th width="30%"
                                                    style="text-align: left; font-size: 12px; font-weight: bold; border: 1px solid #dddddd; font-family: Arial, sans-serif; color: #333333;">
                                                    <span style="display: block; margin: 8px 15px;">Product Name</span>
                                                </th>
                                                <th width="5%"
                                                    style="text-align: center; font-size: 12px; font-weight: bold; border: 1px solid #dddddd; font-family: Arial, sans-serif; color: #333333;">
                                                    <span style="display: block; margin: 8px 15px;">Unit</span>
                                                </th>
                                                <th width="10%"
                                                    style="text-align: center; font-size: 12px; font-weight: bold; border: 1px solid #dddddd; font-family: Arial, sans-serif; color: #333333;">
                                                    <span style="display: block; margin: 8px 15px;">QTY Req</span>
                                                </th>
                                                <th width="10%"
                                                    style="text-align: center; font-size: 12px; font-weight: bold; border: 1px solid #dddddd; font-family: Arial, sans-serif; color: #333333;">
                                                    <span style="display: block; margin: 8px 15px;">QTY Issued</span>
                                                </th>
                                                <th width="15%"
                                                    style="text-align: center; font-size: 12px; font-weight: bold; border: 1px solid #dddddd; font-family: Arial, sans-serif; color: #333333;">
                                                    <span style="display: block; margin: 8px 15px;">Batch Number</span>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($requisition->requisitionItems as $item)
                                            @php
                                            $detail = $item->itemMaster->ItemDetails->firstWhere('id',
                                            $item->item_detail_id) ??
                                            (object)['material_type' => 'N/A', 'item_detail_code' => 'N/A',
                                            'item_detail_name' => 'N/A',
                                            'unit' => 'N/A'];
                                            @endphp
                                            <tr style="background-color: {{ $loop->odd ? '#eeeeee' : '#ffffff' }};"
                                                class="{{ $loop->odd ? 'tr-odd' : '' }}">
                                                <td
                                                    style="border: 1px solid #dddddd; font-family: Arial, sans-serif; font-size: 13px; color: #333333;">
                                                    <span style="display: block; margin: 8px 15px;">{{
                                                        $detail->material_type }}</span>
                                                </td>
                                                <td
                                                    style="border: 1px solid #dddddd; font-family: Arial, sans-serif; font-size: 13px; color: #333333;">
                                                    <span style="display: block; margin: 8px 15px;">{{
                                                        $detail->item_detail_code }}</span>
                                                </td>
                                                <td
                                                    style="border: 1px solid #dddddd; font-family: Arial, sans-serif; font-size: 13px; color: #333333;">
                                                    <span style="display: block; margin: 8px 15px;">{{
                                                        $detail->item_detail_name }}</span>
                                                </td>
                                                <td
                                                    style="border: 1px solid #dddddd; font-family: Arial, sans-serif; font-size: 13px; color: #333333; text-align: center;">
                                                    <span style="display: block; margin: 8px 15px;">{{ $detail->unit
                                                        }}</span>
                                                </td>
                                                <td
                                                    style="border: 1px solid #dddddd; font-family: Arial, sans-serif; font-size: 13px; color: #333333; text-align: center; font-weight: bold;">
                                                    <span style="display: block; margin: 8px 15px;">{{
                                                        $item->quantity_required }}</span>
                                                </td>
                                                <td
                                                    style="border: 1px solid #dddddd; font-family: Arial, sans-serif; font-size: 13px; color: #333333; text-align: center; font-weight: bold;">
                                                    <span style="display: block; margin: 8px 15px;">{{
                                                        $item->quantity_issued }}</span>
                                                </td>
                                                <td
                                                    style="border: 1px solid #dddddd; font-family: Arial, sans-serif; font-size: 13px; color: #333333; text-align: center;">
                                                    <span style="display: block; margin: 8px 15px;">{{ $item->batch_number ?? '-' }}</span>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr style="background-color: #eeeeee;" class="tr-odd">
                                                <td colspan="7"
                                                    style="text-align: center; border: 1px solid #dddddd; font-family: Arial, sans-serif; font-size: 13px; color: #333333;">
                                                    <span style="display: block; margin: 8px 15px;">No product details
                                                        available.</span>
                                                </td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </td>
                            </tr>

                            <tr>
                                <th colspan="2"
                                    style="text-align: center; background-color: #e0e0e0; border: 1px solid #dddddd; font-family: Arial, sans-serif; font-size: 14px; color: #333333; mso-line-height-rule: exactly;">
                                    <h5
                                        style="margin: 0.3em 0; font-size: 14px; font-weight: bold;">
                                        REMARKS / OBJECTIVES</h5>
                                </th>
                            </tr>
                            <tr style="background-color: #eeeeee;" class="tr-odd">
                                <td width="20%"
                                    style="font-weight: bold; background-color: #f0f0f0; border: 1px solid #dddddd; font-family: Arial, sans-serif; color: #333333; font-size: 14px;"
                                    class="bg-f0">
                                    <span style="display: block; margin: 8px 15px;">Alasan / Objectives:</span>
                                </td>
                                <td width="80%"
                                    style="border: 1px solid #dddddd; font-family: Arial, sans-serif; color: #333333; font-size: 14px;">
                                    <span style="display: block; margin: 8px 15px;">{{ $requisition->objectives ??
                                        $item->remarks ?? 'No specific reason provided.' }}</span>
                                </td>
                            </tr>

                            <tr>
                                <th colspan="2"
                                    style="text-align: center; border: 1px solid #dddddd; background-color: #ffffff;">

                                    <table role="presentation" border="0" cellpadding="0" cellspacing="0" align="center"
                                        style="border-collapse: collapse; margin: 0 auto;">
                                        <tr>
                                            <td align="center">
                                                <a href="{{ $approveLink }}" target="_blank"
                                                    style="background-color: #28a745; color: white; text-decoration: none; font-size: 18px; font-family: Arial, sans-serif; font-weight: bold; display: inline-block; border-radius: 5px; mso-hide: all;">
                                                    ✅ Quick Approve
                                                </a>
                                            </td>

                                            <td align="center">
                                                <a href="{{ $approveWithReviewLink }}" target="_blank"
                                                    style="background-color: #007bff; color: white; text-decoration: none; font-size: 18px; font-family: Arial, sans-serif; font-weight: bold; display: inline-block; border-radius: 5px; mso-hide: all;">
                                                    📝 Approve with Review
                                                </a>
                                            </td>

                                            <td align="center">
                                                <a href="{{ $rejectLink }}" target="_blank"
                                                    style="background-color: #dc3545; color: white; text-decoration: none; font-size: 18px; font-family: Arial, sans-serif; font-weight: bold; display: inline-block; border-radius: 5px; mso-hide: all;">
                                                    ❌ Quick Reject
                                                </a>
                                            </td>

                                        </tr>
                                    </table>

                                </th>
                            </tr>

                            <tr>
                                <td colspan="2"
                                    style="text-align: center; background-color: #f0f0f0; border: 1px solid #dddddd; font-family: Arial, sans-serif; font-size: 14px; color: #333333; mso-line-height-rule: exactly;">
                                    <p style="margin: 0.3em 0; line-height: 1.4;">Kindly approve
                                        it at your earliest convenience so we can proceed.</p>
                                    <p style="margin: 0.3em 0; line-height: 1.4;">Thank you for your
                                        attention.</p>
                                    <br>
                                    <p style="margin: 0.3em 0; font-weight: bold; line-height: 1.4;">
                                        Best regards,</p>
                                    <p style="margin: 0.3em 0; line-height: 1.4;">PT Sinar Meadow
                                        International Indonesia
                                    </p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
