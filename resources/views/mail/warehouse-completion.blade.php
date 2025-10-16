<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Warehouse Approval Completed</title>
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
            background: linear-gradient(135deg, #28a745, #20c997);
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
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            border-left: 4px solid #28a745;
            padding: 15px;
            margin-bottom: 25px;
            border-radius: 4px;
        }
        .message h3 {
            margin: 0 0 10px 0;
            color: #155724;
            font-size: 18px;
        }
        .message p {
            margin: 0;
            color: #155724;
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
        .completion-details {
            background-color: #d1ecf1;
            border: 1px solid #bee5eb;
            border-left: 4px solid #17a2b8;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .completion-details h4 {
            margin: 0 0 10px 0;
            color: #0c5460;
            font-size: 16px;
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
        .status-approved {
            background-color: #28a745;
            color: white;
        }
        .highlight-box {
            background: linear-gradient(45deg, #e8f5e8, #d4edda);
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
            border: 2px solid #28a745;
        }
        .highlight-box h3 {
            margin: 0 0 10px 0;
            color: #155724;
            font-size: 20px;
        }
        .highlight-box p {
            margin: 0;
            color: #155724;
            font-size: 16px;
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
            <span class="icon">✅</span>
            <h1>Warehouse Approval Completed</h1>
            <p>All approval processes have been successfully completed</p>
        </div>

        <!-- Body -->
        <div class="email-body">
            <!-- Greeting -->
            <div class="greeting">
                <p>Dear {{ $requester->name ?? 'User' }},</p>
            </div>

            <!-- Main Message -->
            <div class="message">
                <h3>Great news! Your requisition has been fully approved</h3>
                <p>Your requisition <strong>{{ $requisition->no_srs }}</strong> has successfully completed all warehouse approval processes and is now fully approved.</p>
            </div>

            <!-- Highlight Success -->
            <div class="highlight-box">
                <h3>🎉 Approval Process Complete!</h3>
                <p>Your requisition is now ready for processing and fulfillment.</p>
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
                    <span class="info-label">Final Status:</span>
                    <span class="info-value">
                        <span class="status-badge status-approved">Approved</span>
                    </span>
                </div>
                @if($requisition->objectives)
                <div class="info-row">
                    <span class="info-label">Objectives:</span>
                    <span class="info-value">{{ $requisition->objectives }}</span>
                </div>
                @endif
            </div>

            <!-- Completion Details -->
            <div class="completion-details">
                <h4>✅ Completion Information</h4>
                <div class="info-row">
                    <span class="info-label">Completed Date:</span>
                    <span class="info-value">{{ $formattedCompletionDate }}</span>
                </div>
                @if($completedBy)
                <div class="info-row">
                    <span class="info-label">Final Approver:</span>
                    <span class="info-value">{{ $completedBy->name }}</span>
                </div>
                @endif
                <div class="info-row">
                    <span class="info-label">Process Type:</span>
                    <span class="info-value">Warehouse Approval Process</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Current Route:</span>
                    <span class="info-value">{{ $requisition->route_to ?? 'Completed' }}</span>
                </div>
            </div>

            <!-- Next Steps -->
            <div class="info-section">
                <h4>📢 What happens next?</h4>
                <p style="margin: 0; color: #495057;">
                    Your requisition has been fully approved and will now proceed to the fulfillment stage. 
                    The relevant departments will begin processing your request according to the specified requirements. 
                    You will be notified of any further updates or if additional information is required.
                </p>
                <br>
                <p style="margin: 0; color: #495057; font-weight: 600;">
                    Thank you for using our requisition system!
                </p>
            </div>

            <!-- Timeline Summary -->
            <div class="completion-details">
                <h4>⏱️ Process Summary</h4>
                <div class="info-row">
                    <span class="info-label">Request Date:</span>
                    <span class="info-value">{{ \Carbon\Carbon::parse($requisition->request_date)->format('d M Y') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Completion Date:</span>
                    <span class="info-value">{{ \Carbon\Carbon::parse($completionDate)->format('d M Y') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Total Process Time:</span>
                    <span class="info-value">
                        {{ \Carbon\Carbon::parse($requisition->request_date)->diffInDays(\Carbon\Carbon::parse($completionDate)) }} days
                    </span>
                </div>
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