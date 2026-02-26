<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Free Goods Requisition Notification</title>
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
            background: linear-gradient(135deg, #cc982f 0%, #b8871a 100%);
            color: white;
            padding: 25px 40px;
        }

        .header-content .email-title {
            font-size: 24px;
            font-weight: 600;
            margin: 0 0 5px 0;
        }

        .header-content .email-subtitle {
            font-size: 16px;
            font-weight: 400;
            margin: 0;
            opacity: 0.9;
        }

        .email-content {
            padding: 40px;
        }

        .greeting {
            font-size: 16px;
            color: #2c3e50;
            margin-bottom: 25px;
            padding: 20px;
            background: #fef8e7;
            border-radius: 8px;
            border-left: 4px solid #cc982f;
        }

        .info-section {
            margin-bottom: 30px;
        }

        .section-title {
            background: linear-gradient(135deg, #cc982f 0%, #b8871a 100%);
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
            padding: 10px;
            border-radius: 0 0 8px 8px;
            border: 1px solid #e9ecef;
            border-top: none;
        }

        .info-grid table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 10px;
        }

        .info-grid td {
            width: 50%;
            vertical-align: top;
            padding: 0;
        }

        .info-item {
            background: white;
            padding: 15px;
            border-radius: 6px;
            border: 1px solid #e9ecef;
            height: 100%;
            box-sizing: border-box;
        }

        .info-label {
            font-weight: 600;
            color: #888;
            font-size: 12px;
            text-transform: uppercase;
            margin-bottom: 5px;
            letter-spacing: 0.5px;
        }

        .info-value {
            color: #2c3e50;
            font-size: 15px;
            font-weight: 500;
            word-break: break-word;
        }

        .info-value-italic {
            font-style: italic;
            color: #555;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }

        .status-processing {
            background: #cce7ff;
            color: #004085;
            border: 1px solid #b8daff;
        }

        .status-approved {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .status-rejected {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .item-table-section {
            margin-top: 30px;
        }

        .item-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0;
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 0 0 8px 8px;
            overflow: hidden;
        }

        .item-table th {
            background-color: #eee;
            color: #444;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 12px;
            padding: 12px 15px;
            text-align: left;
            border-bottom: 2px solid #ddd;
        }

        .item-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
            color: #333;
        }

        .action-section {
            background: #fef8e7;
            padding: 30px;
            border-radius: 12px;
            text-align: center;
            margin: 40px 0 20px 0;
            border: 1px dashed #cc982f;
        }

        .action-title {
            font-size: 18px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .action-subtitle {
            color: #6c757d;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .btn {
            display: inline-block;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            font-size: 14px;
            text-align: center;
            color: white !important;
            margin: 5px;
            transition: all .3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .btn-approve {
            background: #28a745;
            border: 1px solid #28a745;
        }

        .btn-reject {
            background: #dc3545;
            border: 1px solid #dc3545;
        }

        .btn-review {
            background: #cc982f;
            border: 1px solid #cc982f;
        }

        .btn-process {
            background: #007bff;
            border: 1px solid #007bff;
        }

        .email-footer {
            background: #343a40;
            color: #adb5bd;
            padding: 30px;
            text-align: center;
            font-size: 12px;
            border-top: 4px solid #cc982f;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="email-header">
            <div class="header-content">
                <h1 class="email-title">Free Goods Requisition</h1>
                {{-- PERBAIKAN: Menggunakan sub_category dari database (SnM Request / General Request) --}}
                <p class="email-subtitle">{{ $requisition->sub_category ?? 'General Request' }} Notification</p>
            </div>
        </div>

        <div class="email-content">
            <div class="greeting">
                <strong>Dear {{ $recipient->name ?? 'User' }},</strong><br><br>
                @if (isset($mail_type) && $mail_type === 'completed_notification')
                    Your <strong>{{ $requisition->sub_category ?? 'Requisition' }}</strong> has been fully processed and
                    is now <strong>COMPLETED</strong>.
                @elseif(isset($mail_type) && $mail_type === 'warehouse_process')
                    Requisition requires your action for stage: <strong>{{ $process_step ?? 'Processing' }}</strong>.
                @elseif(isset($mail_type) && $mail_type === 'rejection_notification')
                    Your requisition has been <strong>REJECTED</strong>.
                @else
                    A new <strong>{{ $requisition->sub_category ?? 'Free Goods Request' }}</strong> requires your
                    review. Please check the details below.
                @endif
            </div>

            <div class="info-section">
                <div class="section-title">📄 Request Information</div>
                <div class="info-grid">
                    <table>
                        <tr>
                            <td>
                                <div class="info-item">
                                    <div class="info-label">Requester</div>
                                    <div class="info-value">{{ $requisition->requester->name ?? 'N/A' }}</div>
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
                                    <div class="info-label">Recipient</div>
                                    <div class="info-value">{{ $requisition->recipient_name ?? '-' }}</div>
                                </div>
                            </td>
                            <td>
                                <div class="info-item">
                                    <div class="info-label">Current Status</div>
                                    <div class="info-value">
                                        @php $status = $requisition->status; @endphp
                                        <span
                                            class="status-badge {{ 'status-' . strtolower($status) }}">{{ $status }}</span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            @if (isset($requisition->requisitionItems) && $requisition->requisitionItems->isNotEmpty())
                <div class="item-table-section">
                    <div class="section-title">📦 Requested Item List</div>
                    <table class="item-table" cellpadding="0" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Item Code</th>
                                <th>Item Name</th>
                                <th style="text-align: center;">Unit</th>
                                <th style="text-align: center; width: 15%;">Qty Req</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($requisition->requisitionItems as $item)
                                <tr>
                                    <td>{{ $item->itemMaster->item_master_code ?? '-' }}</td>
                                    <td>{{ $item->itemMaster->item_master_name ?? '-' }}</td>
                                    <td style="text-align: center;">{{ $item->itemMaster->unit ?? '-' }}</td>
                                    <td style="text-align: center; font-weight: bold;">{{ $item->quantity_required }}
                                    </td>
                                    <td style="text-align: center; font-weight: bold;">
                                        {{ $item->quantity_issued ?? '0' }}</td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            @if (isset($mail_type) && ($mail_type == 'approval' || $mail_type == 'warehouse_process'))
                <div class="action-section">
                    @if ($mail_type === 'warehouse_process')
                        <h3 class="action-title">Process Required</h3>
                        <p class="action-subtitle">Please proceed with the <strong>{{ $process_step }}</strong> step.
                        </p>
                        <div>
                            <a href="{{ $update_qty_url ?? '#' }}" class="btn btn-process">Update Qty Issue</a>
                        </div>
                    @else
                        <h3 class="action-title">Approval Action Required</h3>
                        <p class="action-subtitle">Please review and select an action below.</p>
                        <div>
                            <a href="{{ $approve_url ?? '#' }}" class="btn btn-approve">Approve</a>
                            <a href="{{ $review_url ?? '#' }}" class="btn btn-review">Review with Note</a>
                            <a href="{{ $reject_url ?? '#' }}" class="btn btn-reject">Reject</a>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <div class="email-footer">
            &copy; {{ date('Y') }} PT. Sinar Meadow International Indonesia.<br>
            All rights reserved. This is an automated message.
        </div>
    </div>
</body>

</html>
