<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Warehouse Approval Request</title>
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
        
        /* Header */
        .email-header {
            background: linear-gradient(135deg, #6f42c1 0%, #495057 100%);
            color: white;
            padding: 30px 40px;
            text-align: center;
            position: relative;
        }
        .email-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 20"><defs><pattern id="dots" width="20" height="20" patternUnits="userSpaceOnUse"><circle cx="10" cy="10" r="1.5" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="20" fill="url(%23dots)"/></svg>');
            opacity: 0.3;
        }
        .header-content {
            position: relative;
            z-index: 1;
        }
        .company-logo {
            max-height: 50px;
            width: auto;
            margin-bottom: 15px;
        }
        .header-title {
            font-size: 28px;
            font-weight: 700;
            margin: 0;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }
        .header-subtitle {
            font-size: 16px;
            margin: 10px 0 0 0;
            opacity: 0.9;
        }
        .warehouse-icon {
            font-size: 48px;
            margin-bottom: 15px;
            opacity: 0.9;
        }
        
        /* Content */
        .email-content {
            padding: 40px;
        }
        
        /* Greeting */
        .greeting {
            margin-bottom: 30px;
        }
        .greeting h2 {
            color: #6f42c1;
            font-size: 24px;
            margin: 0 0 10px 0;
            font-weight: 600;
        }
        .greeting p {
            margin: 0;
            font-size: 16px;
            color: #666;
        }
        
        /* Info Cards */
        .info-section {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin: 30px 0;
        }
        .info-card {
            flex: 1;
            min-width: 280px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 10px;
            padding: 25px;
            border-left: 5px solid #6f42c1;
        }
        .info-card h3 {
            color: #495057;
            font-size: 18px;
            margin: 0 0 15px 0;
            font-weight: 600;
        }
        .info-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }
        .info-item:last-child {
            margin-bottom: 0;
            border-bottom: none;
        }
        .info-label {
            font-weight: 600;
            color: #495057;
            flex: 0 0 120px;
        }
        .info-value {
            color: #6c757d;
            text-align: right;
            flex: 1;
        }
        
        /* Level Badge */
        .level-badge {
            display: inline-block;
            background: linear-gradient(45deg, #6f42c1, #e83e8c);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 14px;
            margin: 15px 0;
        }
        
        /* Print Batch Badge */
        .print-batch-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 15px;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
        }
        .print-batch-yes {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .print-batch-no {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        /* Items Table */
        .items-section {
            margin: 30px 0;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        .items-table th {
            background: linear-gradient(135deg, #6f42c1 0%, #495057 100%);
            color: white;
            padding: 15px 12px;
            font-weight: 600;
            text-align: left;
            font-size: 14px;
        }
        .items-table td {
            padding: 12px;
            border-bottom: 1px solid #dee2e6;
            font-size: 14px;
        }
        .items-table tr:last-child td {
            border-bottom: none;
        }
        .items-table tr:hover {
            background-color: #f8f9fa;
        }
        
        /* Action Buttons */
        .action-section {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 12px;
            padding: 35px;
            margin: 30px 0;
            text-align: center;
            border: 2px dashed #6f42c1;
        }
        .action-title {
            color: #495057;
            font-size: 20px;
            font-weight: 600;
            margin: 0 0 10px 0;
        }
        .action-subtitle {
            color: #6c757d;
            margin: 0 0 25px 0;
            font-size: 14px;
        }
        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn {
            display: inline-block;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            text-align: center;
            transition: all 0.3s ease;
            min-width: 160px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        .btn-ok {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
        }
        .btn-ok:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
            color: white;
        }
        .btn-review {
            background: linear-gradient(135deg, #17a2b8 0%, #6610f2 100%);
            color: white;
        }
        .btn-review:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(23, 162, 184, 0.4);
            color: white;
        }
        
        /* Urgent Notice */
        .urgent-notice {
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
            text-align: center;
        }
        .urgent-notice h4 {
            color: #856404;
            margin: 0 0 10px 0;
            font-size: 16px;
            font-weight: 600;
        }
        .urgent-notice p {
            color: #856404;
            margin: 0;
            font-size: 14px;
        }
        
        /* Footer */
        .email-footer {
            background: #f8f9fa;
            padding: 30px 40px;
            text-align: center;
            border-top: 1px solid #dee2e6;
        }
        .footer-text {
            color: #6c757d;
            font-size: 14px;
            margin: 0 0 15px 0;
        }
        .footer-links {
            margin: 15px 0;
        }
        .footer-links a {
            color: #6f42c1;
            text-decoration: none;
            margin: 0 15px;
            font-size: 14px;
        }
        .footer-links a:hover {
            text-decoration: underline;
        }
        .company-info {
            color: #adb5bd;
            font-size: 12px;
            margin-top: 20px;
            line-height: 1.4;
        }
        
        /* Responsive */
        @media (max-width: 600px) {
            .email-container {
                margin: 10px;
                border-radius: 8px;
            }
            .email-content,
            .email-header,
            .email-footer {
                padding: 20px;
            }
            .info-section {
                flex-direction: column;
            }
            .info-card {
                min-width: auto;
            }
            .action-buttons {
                flex-direction: column;
                align-items: center;
            }
            .btn {
                width: 100%;
                max-width: 300px;
            }
            .items-table {
                font-size: 12px;
            }
            .items-table th,
            .items-table td {
                padding: 8px 6px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <div class="header-content">
                <div class="warehouse-icon">🏭</div>
                <h1 class="header-title">Warehouse Approval Required</h1>
                <p class="header-subtitle">
                    {{ 
                        $approvalLog->level == 100 ? 'WH Supervisor - Initial Check' :
                        ($approvalLog->level == 101 ? 'Material Supervisor - Material Review' : 'WH Supervisor - Final Approval')
                    }}
                </p>
            </div>
        </div>

        <!-- Content -->
        <div class="email-content">
            <!-- Greeting -->
            <div class="greeting">
                <h2>Hello, {{ $approver->name }}</h2>
                <p>A requisition complain requires your warehouse approval. Please review the details below and take appropriate action.</p>
                <div class="level-badge">
                    📦 Level {{ $approvalLog->level }} Approval
                </div>
            </div>

            <!-- Requisition Information -->
            <div class="info-section">
                <div class="info-card">
                    <h3>📋 Requisition Details</h3>
                    <div class="info-item">
                        <span class="info-label">Number:</span>
                        <span class="info-value"><strong>{{ $requisition->no_srs }}</strong></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Customer:</span>
                        <span class="info-value">{{ $requisition->customer->name ?? 'N/A' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Requester:</span>
                        <span class="info-value">{{ $requisition->requester->name ?? 'N/A' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Request Date:</span>
                        <span class="info-value">{{ \Carbon\Carbon::parse($requisition->request_date)->format('d M Y') }}</span>
                    </div>
                </div>

                <div class="info-card">
                    <h3>💼 Account Information</h3>
                    <div class="info-item">
                        <span class="info-label">Account:</span>
                        <span class="info-value">{{ $requisition->account }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Cost Center:</span>
                        <span class="info-value">{{ $requisition->cost_center }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Category:</span>
                        <span class="info-value"><strong>{{ $requisition->category }}</strong></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Print Batch:</span>
                        <span class="info-value">
                            @if($requisition->print_batch)
                                <span class="print-batch-badge print-batch-yes">✓ Yes</span>
                            @else
                                <span class="print-batch-badge print-batch-no">✗ No</span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            @if($requisition->objectives)
            <div class="info-card" style="margin-top: 20px;">
                <h3>🎯 Objectives</h3>
                <p style="margin: 0; color: #495057; line-height: 1.6;">{{ $requisition->objectives }}</p>
            </div>
            @endif

            <!-- Requisition Items -->
            @if($requisition->requisitionItems && $requisition->requisitionItems->count() > 0)
            <div class="items-section">
                <h3 style="color: #495057; margin-bottom: 15px;">📦 Requisition Items</h3>
                <table class="items-table">
                    <thead>
                        <tr>
                            <th>Item Name</th>
                            <th>Item Code</th>
                            <th>Qty Required</th>
                            <th>Qty Issued</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($requisition->requisitionItems as $item)
                        <tr>
                            <td><strong>{{ $item->itemMaster->item_name ?? 'N/A' }}</strong></td>
                            <td>{{ $item->itemMaster->item_code ?? 'N/A' }}</td>
                            <td style="text-align: center;"><span style="background: #e3f2fd; color: #1976d2; padding: 4px 8px; border-radius: 12px; font-weight: 600;">{{ $item->quantity_required ?? 0 }}</span></td>
                            <td style="text-align: center;"><span style="background: #e8f5e8; color: #2e7d32; padding: 4px 8px; border-radius: 12px; font-weight: 600;">{{ $item->quantity_issued ?? 0 }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif

            <!-- Urgent Notice -->
            <div class="urgent-notice">
                <h4>⚡ Quick Action Required</h4>
                <p>This warehouse approval is part of the requisition process. Your prompt action helps maintain operational efficiency.</p>
            </div>

            <!-- Action Buttons -->
            <div class="action-section">
                <h3 class="action-title">Take Action</h3>
                <p class="action-subtitle">Choose your preferred approval method</p>
                
                <div class="action-buttons">
                    <a href="{{ $quickOkLink }}" class="btn btn-ok">
                        ✅ Quick OK
                    </a>
                    <a href="{{ $okWithReviewLink }}" class="btn btn-review">
                        📝 OK with Review
                    </a>
                </div>
                
                <p style="margin-top: 20px; font-size: 12px; color: #6c757d;">
                    <strong>Quick OK:</strong> Approve immediately without additional notes<br>
                    <strong>OK with Review:</strong> Review details and add notes before approval
                </p>
            </div>
        </div>

        <!-- Footer -->
        <div class="email-footer">
            <p class="footer-text">
                This is an automated email from the Requisition Management System.<br>
                Please do not reply to this email.
            </p>
            
            <div class="footer-links">
                <a href="#">Help Center</a>
                <a href="#">Contact Support</a>
                <a href="#">System Status</a>
            </div>
            
            <div class="company-info">
                © {{ date('Y') }} Your Company Name. All rights reserved.<br>
                Requisition Management System | Warehouse Approval Module
            </div>
        </div>
    </div>
</body>
</html>