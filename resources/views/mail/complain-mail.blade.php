<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Requisition Approval Request</title>
    <style>
        /* Global styles kept minimal for modern clients, but the critical styles are INLINE */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 0;
            margin: 0;
        }

        /* Class definitions are less effective in old Outlook, hence the inlining */
        .tr-odd {
            background-color: rgb(238, 238, 238) !important;
        }
    </style>
</head>

<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; margin: 0;">
    <table role="presentation" align="center" border="0" cellpadding="0" cellspacing="0" class="email-wrapper"
        style="width: 100%; max-width: 800px; margin: auto; border: 1px solid #ddd; border-collapse: collapse; background-color: white;">

        <thead>
            <tr class="header-style">
                <td colspan="2"
                    style="background-color: #ffc107; padding: 10px 15px; border: 1px solid #ddd; border-bottom: none; font-family: Arial, sans-serif; color: #333333;">
                    <h2 style="margin: 0.3em 0; font-size: 24px;">Requisition approval request</h2>
                    <h5 style="margin: 0.3em 0; font-size: 16px;">{{ $requisition->department ?? 'Sales & Marketing' }}
                        - {{ $requisition->type ?? 'Packaging Replacement Request' }}</h5>
                </td>
            </tr>
            <tr>
                <td colspan="2"
                    style="padding: 15px 0.8rem; border: 1px solid #ddd; border-top: none; font-family: Arial, sans-serif; font-size: 14px; color: #333333;">
                    <p style="margin: 0.3em 0;">Dear <strong style="font-weight: bold;">{{ $approver->name ?? 'Approver'
                            }}</strong>,</p>
                    <p style="margin: 0.3em 0;">
                        You have received a new requisition approval request. Please review the details below and
                        provide your decision.
                    </p>
                </td>
            </tr>
        </thead>

        <tbody>
            <tr>
                <th colspan="2"
                    style="text-align: center; background-color: #e0e0e0; padding: 0.5rem 0.8rem; border: 1px solid #ddd; font-family: Arial, sans-serif; font-size: 14px; color: #333333;">
                    <h5 style="margin: 0.3em 0;">REQUEST DETAILS ({{ $requisition->no_srs ?? 'REQ-XXXXXX' }})</h5>
                </th>
            </tr>

            <tr style="background-color: rgb(238, 238, 238);">
                <td
                    style="width: 20%; font-weight: bold; background-color: #f0f0f0; padding: 0.5rem 0.8rem; border: 1px solid #ddd; font-family: Arial, sans-serif; color: #333333;">
                    Request Number:</td>
                <td
                    style="padding: 0.5rem 0.8rem; border: 1px solid #ddd; font-family: Arial, sans-serif; color: #333333;">
                    <span style="font-weight: bold;">{{ $requisition->no_srs ?? 'N/A' }}</span></td>
            </tr>
            <tr>
                <td
                    style="width: 20%; font-weight: bold; background-color: #f0f0f0; padding: 0.5rem 0.8rem; border: 1px solid #ddd; font-family: Arial, sans-serif; color: #333333;">
                    Request Date:</td>
                <td
                    style="padding: 0.5rem 0.8rem; border: 1px solid #ddd; font-family: Arial, sans-serif; color: #333333;">
                    {{ \Carbon\Carbon::parse($requisition->request_date)->format('d F Y') ?? 'N/A' }}</td>
            </tr>
            <tr style="background-color: rgb(238, 238, 238);">
                <td
                    style="width: 20%; font-weight: bold; background-color: #f0f0f0; padding: 0.5rem 0.8rem; border: 1px solid #ddd; font-family: Arial, sans-serif; color: #333333;">
                    Requester:</td>
                <td
                    style="padding: 0.5rem 0.8rem; border: 1px solid #ddd; font-family: Arial, sans-serif; color: #333333;">
                    {{ $requisition->requester->name ?? 'N/A' }} (Dept: {{ $requisition->requester->department->name ??
                    'N/A' }})</td>
            </tr>
            <tr>
                <td
                    style="width: 20%; font-weight: bold; background-color: #f0f0f0; padding: 0.5rem 0.8rem; border: 1px solid #ddd; font-family: Arial, sans-serif; color: #333333;">
                    Cost Center:</td>
                <td
                    style="padding: 0.5rem 0.8rem; border: 1px solid #ddd; font-family: Arial, sans-serif; color: #333333;">
                    {{ $requisition->cost_center ?? 'N/A' }}</td>
            </tr>

            <tr>
                <th colspan="2"
                    style="text-align: center; background-color: #e0e0e0; padding: 0.5rem 0.8rem; border: 1px solid #ddd; font-family: Arial, sans-serif; font-size: 14px; color: #333333;">
                    <h5 style="margin: 0.3em 0;">CUSTOMER/ACCOUNT INFORMATION</h5>
                </th>
            </tr>
            <tr style="background-color: rgb(238, 238, 238);">
                <td
                    style="width: 20%; font-weight: bold; background-color: #f0f0f0; padding: 0.5rem 0.8rem; border: 1px solid #ddd; font-family: Arial, sans-serif; color: #333333;">
                    Customer Name:</td>
                <td
                    style="padding: 0.5rem 0.8rem; border: 1px solid #ddd; font-family: Arial, sans-serif; color: #333333;">
                    {{ $requisition->customer->name ?? 'N/A (Internal Request)' }}</td>
            </tr>
            <tr>
                <td
                    style="width: 20%; font-weight: bold; background-color: #f0f0f0; padding: 0.5rem 0.8rem; border: 1px solid #ddd; font-family: Arial, sans-serif; color: #333333;">
                    Customer Address:</td>
                <td
                    style="padding: 0.5rem 0.8rem; border: 1px solid #ddd; font-family: Arial, sans-serif; color: #333333;">
                    {{ $requisition->customer->address ?? '-' }}</td>
            </tr>
            <tr style="background-color: rgb(238, 238, 238);">
                <td
                    style="width: 20%; font-weight: bold; background-color: #f0f0f0; padding: 0.5rem 0.8rem; border: 1px solid #ddd; font-family: Arial, sans-serif; color: #333333;">
                    Account/Project:</td>
                <td
                    style="padding: 0.5rem 0.8rem; border: 1px solid #ddd; font-family: Arial, sans-serif; color: #333333;">
                    {{ $requisition->account ?? '-' }}</td>
            </tr>

            <tr>
                <th colspan="2"
                    style="text-align: center; background-color: #e0e0e0; padding: 0.5rem 0.8rem; border: 1px solid #ddd; font-family: Arial, sans-serif; font-size: 14px; color: #333333;">
                    <h5 style="margin: 0.3em 0;">PRODUCT/MATERIAL DETAILS</h5>
                </th>
            </tr>

            <tr>
                <td colspan="2" style="padding: 0; border: none;">
                    <table role="presentation" border="0" cellpadding="0" cellspacing="0"
                        style="width: 100%; border-collapse: collapse; border: none;">
                        <thead>
                            <tr style="background-color: #cccccc;">
                                <th
                                    style="width: 15%; padding: 0.5rem 0.8rem; text-align: left; font-size: 12px; font-weight: bold; border: 1px solid #ddd; font-family: Arial, sans-serif; color: #333333;">
                                    Material Type</th>
                                <th
                                    style="width: 15%; padding: 0.5rem 0.8rem; text-align: left; font-size: 12px; font-weight: bold; border: 1px solid #ddd; font-family: Arial, sans-serif; color: #333333;">
                                    Product Code</th>
                                <th
                                    style="width: 30%; padding: 0.5rem 0.8rem; text-align: left; font-size: 12px; font-weight: bold; border: 1px solid #ddd; font-family: Arial, sans-serif; color: #333333;">
                                    Product Name</th>
                                <th
                                    style="width: 5%; padding: 0.5rem 0.8rem; text-align: center; font-size: 12px; font-weight: bold; border: 1px solid #ddd; font-family: Arial, sans-serif; color: #333333;">
                                    Unit</th>
                                <th
                                    style="width: 10%; padding: 0.5rem 0.8rem; text-align: center; font-size: 12px; font-weight: bold; border: 1px solid #ddd; font-family: Arial, sans-serif; color: #333333;">
                                    QTY Req</th>
                                <th
                                    style="width: 10%; padding: 0.5rem 0.8rem; text-align: center; font-size: 12px; font-weight: bold; border: 1px solid #ddd; font-family: Arial, sans-serif; color: #333333;">
                                    QTY Issued</th>
                                <th
                                    style="width: 15%; padding: 0.5rem 0.8rem; text-align: center; font-size: 12px; font-weight: bold; border: 1px solid #ddd; font-family: Arial, sans-serif; color: #333333;">
                                    Batch Number</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($requisition->requisitionItems as $item)
                            @php
                            $detail = $item->itemMaster->ItemDetails->firstWhere('id', $item->item_detail_id) ??
                            (object)['material_type' => 'N/A', 'item_detail_code' => 'N/A', 'item_detail_name' => 'N/A',
                            'unit' => 'N/A'];
                            @endphp
                            <tr style="background-color: {{ $loop->odd ? 'rgb(238, 238, 238)' : 'white' }};">
                                <td
                                    style="padding: 0.5rem 0.8rem; border: 1px solid #ddd; font-family: Arial, sans-serif; font-size: 13px; color: #333333;">
                                    {{ $detail->material_type }}</td>
                                <td
                                    style="padding: 0.5rem 0.8rem; border: 1px solid #ddd; font-family: Arial, sans-serif; font-size: 13px; color: #333333;">
                                    {{ $detail->item_detail_code }}</td>
                                <td
                                    style="padding: 0.5rem 0.8rem; border: 1px solid #ddd; font-family: Arial, sans-serif; font-size: 13px; color: #333333;">
                                    {{ $detail->item_detail_name }}</td>
                                <td
                                    style="padding: 0.5rem 0.8rem; border: 1px solid #ddd; font-family: Arial, sans-serif; font-size: 13px; color: #333333; text-align: center;">
                                    {{ $detail->unit }}</td>
                                <td
                                    style="padding: 0.5rem 0.8rem; border: 1px solid #ddd; font-family: Arial, sans-serif; font-size: 13px; color: #333333; text-align: center; font-weight: bold;">
                                    {{ $item->quantity_required }}</td>
                                <td
                                    style="padding: 0.5rem 0.8rem; border: 1px solid #ddd; font-family: Arial, sans-serif; font-size: 13px; color: #333333; text-align: center; font-weight: bold;">
                                    {{ $item->quantity_issued }}</td>
                                <td
                                    style="padding: 0.5rem 0.8rem; border: 1px solid #ddd; font-family: Arial, sans-serif; font-size: 13px; color: #333333; text-align: center;">
                                    {{ date('j/n/y', strtotime($item->batch_number)) ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr style="background-color: rgb(238, 238, 238);">
                                <td colspan="7"
                                    style="padding: 0.5rem 0.8rem; text-align: center; border: 1px solid #ddd; font-family: Arial, sans-serif; font-size: 13px; color: #333333;">
                                    No product details available.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </td>
            </tr>

            <tr>
                <th colspan="2"
                    style="text-align: center; background-color: #e0e0e0; padding: 0.5rem 0.8rem; border: 1px solid #ddd; font-family: Arial, sans-serif; font-size: 14px; color: #333333;">
                    <h5 style="margin: 0.3em 0;">REMARKS / OBJECTIVES</h5>
                </th>
            </tr>
            <tr style="background-color: rgb(238, 238, 238);">
                <td
                    style="width: 20%; font-weight: bold; background-color: #f0f0f0; padding: 0.5rem 0.8rem; border: 1px solid #ddd; font-family: Arial, sans-serif; color: #333333;">
                    Alasan / Objectives:</td>
                <td
                    style="width: 80%; padding: 0.5rem 0.8rem; border: 1px solid #ddd; font-family: Arial, sans-serif; color: #333333;">
                    {{ $requisition->objectives ?? $item->remarks ?? 'No specific reason provided.' }}</td>
            </tr>

            <tr>
                <th colspan="2"
                    style="text-align: center; padding: 20px 0; border: 1px solid #ddd; background-color: #ffffff;">

                    <table role="presentation" border="0" cellpadding="0" cellspacing="0" align="center"
                        style="border-collapse: collapse; margin: 0 auto;">
                        <tr>
                            <td style="padding: 0 10px;">
                                <a href="{{ $approveLink }}" target="_blank" style="text-decoration: none; font-size: 18px; font-weight: bold; padding: 10px 20px; display: inline-block; border-radius: 5px;
                                   background-color: #28a745; color: white; border: none; mso-padding-alt: 10px 20px;">
                                    ✅ Quick Approve
                                </a>
                            </td>
                            <td style="padding: 0 10px;">
                                <a href="{{ $approveWithReviewLink }}" target="_blank" style="text-decoration: none; font-size: 18px; font-weight: bold; padding: 10px 20px; display: inline-block; border-radius: 5px;
                                   background-color: #007bff; color: white; border: none; mso-padding-alt: 10px 20px;">
                                    📝 Approve with Review
                                </a>
                            </td>
                            <td style="padding: 0 10px;">
                                <a href="{{ $rejectLink }}" target="_blank" style="text-decoration: none; font-size: 18px; font-weight: bold; padding: 10px 20px; display: inline-block; border-radius: 5px;
                                   background-color: #dc3545; color: white; border: none; mso-padding-alt: 10px 20px;">
                                    ❌ Quick Reject
                                </a>
                            </td>
                        </tr>
                    </table>

                </th>
            </tr>

            <tr>
                <td colspan="2"
                    style="text-align: center; background-color: #f0f0f0; padding: 15px; border: 1px solid #ddd; font-family: Arial, sans-serif; font-size: 14px; color: #333333;">
                    <p style="margin: 0.3em 0;">Kindly approve it at your earliest convenience so we can proceed.</p>
                    <p style="margin: 0.3em 0;">Thank you for your attention.</p>
                    <br>
                    <p style="margin: 0.3em 0; font-weight: bold;">Best regards,</p>
                    <p style="margin: 0.3em 0;">{{ config('app.name') ?? 'PT Sinar Meadow International Indonesia' }}
                    </p>
                </td>
            </tr>
        </tbody>
    </table>
</body>

</html>
