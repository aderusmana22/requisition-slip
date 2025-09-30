<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Requisition Approval Request</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
            color: #333;
            line-height: 1.6;
        }

        .email-container {
            max-width: 800px;
            margin: 20px auto;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .email-header {
            background: linear-gradient(135deg, #004a99 0%, #002c5c 100%);
            /* Warna disesuaikan untuk Sample */
            color: white;
            padding: 30px 40px;
            text-align: center;
        }

        .email-title {
            font-size: 28px;
            font-weight: 700;
            margin: 0;
        }

        .email-subtitle {
            font-size: 16px;
            margin: 10px 0 0 0;
            opacity: 0.9;
        }

        .email-content {
            padding: 40px;
        }

        .greeting {
            font-size: 18px;
            color: #2c3e50;
            margin-bottom: 25px;
            padding: 20px;
            background: #e9ecef;
            border-radius: 8px;
            border-left: 4px solid #004a99;
            /* Warna disesuaikan untuk Sample */
        }

        .info-section {
            margin-bottom: 30px;
        }

        .section-title {
            background: linear-gradient(135deg, #004a99 0%, #002c5c 100%);
            /* Warna disesuaikan untuk Sample */
            color: white;
            padding: 12px 20px;
            margin: 0 0 15px 0;
            border-radius: 8px 8px 0 0;
            font-weight: 600;
            font-size: 16px;
        }

        .info-grid {
            width: 100%;
            background: #f8f9fa;
            padding: 20px;
            border-radius: 0 0 8px 8px;
            border: 1px solid #e9ecef;
            border-top: none;
        }

        .info-grid table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-grid td {
            width: 50%;
            vertical-align: top;
            padding: 7px;
        }

        .info-item {
            background: white;
            padding: 15px;
            border-radius: 6px;
            border: 1px solid #e9ecef;
            height: 100%;
        }

        .info-label {
            font-weight: 600;
            color: #495057;
            font-size: 14px;
            margin-bottom: 5px;
        }

        .info-value {
            color: #2c3e50;
            font-size: 15px;
            word-break: break-word;
        }

        .product-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #e9ecef;
        }

        .product-table th {
            background: #343a40;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
        }

        .product-table td {
            padding: 12px;
            border-bottom: 1px solid #e9ecef;
            font-size: 14px;
        }

        .action-section {
            background: #e9ecef;
            padding: 30px;
            border-radius: 12px;
            text-align: center;
            margin: 30px 0;
            border: 1px solid #dee2e6;
        }

        .action-title {
            font-size: 20px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 25px;
        }

        .button-group table {
            margin: 0 auto;
        }

        .button-group td {
            padding: 7px;
        }

        .btn {
            display: inline-block;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            text-align: center;
            color: white !important;
            min-width: 140px;
            transition: all 0.3s ease;
        }

        .btn-approve {
            background: #28a745;
        }

        .btn-review {
            background: #007bff;
        }

        .btn-reject {
            background: #dc3545;
        }

        .email-footer {
            background: #343a40;
            color: white;
            padding: 30px 40px;
            text-align: center;
        }

        .footer-content {
            max-width: 600px;
            margin: 0 auto;
        }

        .company-info {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .company-tagline {
            font-size: 14px;
            opacity: 0.8;
            margin-bottom: 20px;
        }

        .copyright {
            font-size: 12px;
            opacity: 0.7;
            margin-top: 15px;
        }

    </style>
</head>

<body>
    <div class="email-container">
        <div class="email-header">
            <h1 class="email-title">Sample Requisition Approval</h1>
            <p class="email-subtitle">SRS No: <strong>{{ $requisition->no_srs }}</strong></p>
        </div>

        <div class="email-content">
            <div class="greeting">
                <strong>Hello {{ $approver->name }},</strong><br>
                A new sample requisition requires your review and approval.
            </div>

            <div class="info-section">
                <h3 class="section-title">📄 Request Information</h3>
                <div class="info-grid">
                    <table>
                        <tr>
                            <td>
                                <div class="info-item">
                                    <div class="info-label">Request Number</div>
                                    <div class="info-value">{{ $requisition->no_srs }}</div>
                                </div>
                            </td>
                            <td>
                                <div class="info-item">
                                    <div class="info-label">Request Date</div>
                                    <div class="info-value">
                                        {{ \Carbon\Carbon::parse($requisition->request_date)->format('d M Y') }}</div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="info-item">
                                    <div class="info-label">Requester</div>
                                    <div class="info-value">{{ $requisition->requester->name ?? 'N/A' }}</div>
                                </div>
                            </td>
                            <td>
                                <div class="info-item">
                                    <div class="info-label">Sub Category</div>
                                    <div class="info-value">{{ $requisition->sub_category }}</div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="info-section">
                <h3 class="section-title">👤 Customer & Objectives</h3>
                <div class="info-grid">
                    <table>
                        <tr>
                            <td>
                                <div class="info-item">
                                    <div class="info-label">Customer Name</div>
                                    <div class="info-value">{{ $requisition->customer->name ?? 'N/A' }}</div>
                                </div>
                            </td>
                            <td>
                                <div class="info-item">
                                    <div class="info-label">Cost Center</div>
                                    <div class="info-value">{{ $requisition->cost_center ?? 'N/A' }}</div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            {{-- [MODIFIED] Menampilkan Objectives & Estimated Potential --}}
                            <td>
                                <div class="info-item">
                                    <div class="info-label">Objectives</div>
                                    <div class="info-value">{{ $requisition->objectives }}</div>
                                </div>
                            </td>
                            <td>
                                <div class="info-item">
                                    <div class="info-label">Estimated Potential</div>
                                    <div class="info-value">{{ $requisition->estimated_potential }}</div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            @if($requisition->requisitionItems && $requisition->requisitionItems->count() > 0)
            <div class="info-section">
                <h3 class="section-title">📦 Requested Item List</h3>
                <div
                    style="background: #f8f9fa; border-radius: 0 0 8px 8px; border: 1px solid #e9ecef; border-top: none;">
                    <table class="product-table">
                        <thead>
                            <tr>
                                @if($requisition->sub_category == 'Packaging')
                                <th>Material Type</th>
                                @endif
                                <th>Item Code</th>
                                <th>Item Name</th>
                                <th>Unit</th>
                                <th>Qty Required</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($requisition->requisitionItems as $item)
                            <tr>
                                @if($requisition->sub_category == 'Packaging')
                                <td>{{ $item->material_type ?? '-' }}</td>
                                <td>{{ $item->itemDetail->item_detail_code ?? '-' }}</td>
                                <td>{{ $item->itemDetail->item_detail_name ?? '-' }}</td>
                                <td>{{ $item->itemDetail->unit ?? '-' }}</td>
                                @else
                                <td>{{ $item->itemMaster->item_master_code ?? '-' }}</td>
                                <td>{{ $item->itemMaster->item_master_name ?? '-' }}</td>
                                <td>{{ $item->itemMaster->unit ?? '-' }}</td>
                                @endif
                                <td style="text-align: center; font-weight: 600;">{{ $item->quantity_required }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            <div class="action-section">
                <h3 class="action-title">Please Choose an Action</h3>
                <div class="button-group">
                    <table>
                        <tr>
                            <td><a href="{{ $approve_url }}" class="btn btn-approve">Approve</a></td>
                            <td><a href="{{ $review_url }}" class="btn btn-review">Approve with Review</a></td>
                            <td><a href="{{ $reject_url }}" class="btn btn-reject">Reject</a></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="email-footer">
            <div class="footer-content">
                <div class="company-info">Sample Requisition System</div>
                {{-- [MODIFIED] Menampilkan departemen requester secara dinamis --}}
                <div class="company-tagline">{{ $requisition->requester->department->name ?? 'Internal Department' }}
                </div>
                <div class="copyright">
                    © {{ date('Y') }} PT. Sinar Meadow International Indonesia. All rights reserved.<br>
                    This is an automated message, please do not reply.
                </div>
            </div>
        </div>
    </div>
</body>

</html>
