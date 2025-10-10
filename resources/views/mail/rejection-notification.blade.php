<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Requisition Rejected</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .email-header {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .email-header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .email-header .icon {
            font-size: 48px;
            margin-bottom: 10px;
            display: block;
        }
        .email-body {
            padding: 30px 20px;
        }
        .greeting {
            font-size: 16px;
            margin-bottom: 20px;
            color: #333;
        }
        .message {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-left: 4px solid #dc3545;
            padding: 15px;
            margin-bottom: 25px;
            border-radius: 4px;
        }
        .message h3 {
            margin: 0 0 10px 0;
            color: #721c24;
            font-size: 18px;
        }
        .message p {
            margin: 0;
            color: #856404;
        }
        .info-section {
            background-color: #f8f9fa;
            border-radius: 6px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .info-section h4 {
            margin: 0 0 15px 0;
            color: #495057;
            font-size: 16px;
            border-bottom: 2px solid #dee2e6;
            padding-bottom: 8px;
        }
        .info-row {
            display: flex;
            margin-bottom: 10px;
        }
        .info-row:last-child {
            margin-bottom: 0;
        }
        .info-label {
            font-weight: 600;
            color: #495057;
            min-width: 140px;
            flex-shrink: 0;
        }
        .info-value {
            color: #212529;
            flex-grow: 1;
        }
        .rejection-details {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            border-left: 4px solid #dc3545;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .rejection-details h4 {
            margin: 0 0 10px 0;
            color: #721c24;
            font-size: 16px;
        }
        .rejection-details .rejection-reason {
            background-color: #ffffff;
            padding: 10px;
            border-radius: 4px;
            border: 1px solid #f5c6cb;
            font-style: italic;
            color: #495057;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #dee2e6;
            color: #6c757d;
            font-size: 14px;
        }
        .footer p {
            margin: 5px 0;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .status-rejected {
            background-color: #dc3545;
            color: white;
        }
        @media (max-width: 600px) {
            .email-container {
                margin: 10px;
                border-radius: 0;
            }
            .email-body {
                padding: 20px 15px;
            }
            .info-section {
                padding: 15px;
            }
            .info-row {
                flex-direction: column;
            }
            .info-label {
                min-width: auto;
                margin-bottom: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <span class="icon">❌</span>
            <h1>Requisition Rejected</h1>
            <p>{{ $rejectionType === 'warehouse' ? 'Warehouse Approval' : 'Standard Approval' }} Process</p>
        </div>

        <!-- Body -->
        <div class="email-body">
            <!-- Greeting -->
            <div class="greeting">
                <p>Dear {{ $requester->name ?? 'User' }},</p>
            </div>

            <!-- Main Message -->
            <div class="message">
                <h3>Your requisition has been rejected</h3>
                <p>Your requisition <strong>{{ $requisition->no_srs }}</strong> has been rejected during the {{ $rejectionType === 'warehouse' ? 'warehouse approval' : 'approval' }} process.</p>
            </div>

            <!-- Requisition Information -->
            <div class="info-section">
                <h4>📋 Requisition Details</h4>
                <div class="info-row">
                    <span class="info-label">No. SRS:</span>
                    <span class="info-value">{{ $requisition->no_srs }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Customer:</span>
                    <span class="info-value">{{ $requisition->customer->name ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Category:</span>
                    <span class="info-value">{{ $requisition->category }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Current Status:</span>
                    <span class="info-value">
                        <span class="status-badge status-rejected">Rejected</span>
                    </span>
                </div>
            </div>

            <!-- Rejection Details -->
            <div class="rejection-details">
                <h4>🚫 Rejection Information</h4>
                <div class="info-row">
                    <span class="info-label">Rejected By:</span>
                    <span class="info-value">{{ $rejectedBy->name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Rejection Date:</span>
                    <span class="info-value">{{ $formattedRejectionDate }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Rejection Type:</span>
                    <span class="info-value">{{ $rejectionType === 'warehouse' ? 'Warehouse Approval' : 'Manager Approval' }}</span>
                </div>
                
                @if($rejectionReason)
                <div style="margin-top: 15px;">
                    <span class="info-label" style="display: block; margin-bottom: 8px;">Reason for Rejection:</span>
                    <div class="rejection-reason">
                        {{ $rejectionReason }}
                    </div>
                </div>
                @else
                <div style="margin-top: 15px;">
                    <span class="info-label" style="display: block; margin-bottom: 8px;">Reason for Rejection:</span>
                    <div class="rejection-reason">
                        <em>No specific reason provided</em>
                    </div>
                </div>
                @endif
            </div>

            <!-- Next Steps -->
            <div class="info-section">
                <h4>📢 What happens next?</h4>
                <p style="margin: 0; color: #495057;">
                    Your requisition has been rejected and the process has been stopped. 
                    @if($rejectionType === 'warehouse')
                        Please review the warehouse requirements and resubmit if necessary.
                    @else
                        Please review the requirements and resubmit if necessary.
                    @endif
                    If you have any questions about this rejection, please contact the person who rejected your request or your supervisor.
                </p>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>{{ config('app.name') }}</strong> - Requisition Management System</p>
            <p>This is an automated email notification. Please do not reply to this email.</p>
            <p style="font-size: 12px; color: #999;">
                Email sent on {{ now()->setTimezone('Asia/Jakarta')->format('d M Y, H:i:s') }} WIB
            </p>
        </div>
    </div>
</body>
</html>