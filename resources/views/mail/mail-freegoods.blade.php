<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Free Goods Requisition Notification</title>
    <style>
        body { font-family: sans-serif; background-color: #f4f7fc; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); }
        .header { background-color: #008779; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .header h1 { margin: 0; font-size: 24px; }
        .content { padding: 30px; color: #333; line-height: 1.6; }
        .details-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .details-table th, .details-table td { text-align: left; padding: 10px; border-bottom: 1px solid #eee; }
        .details-table th { background-color: #f8f8f8; width: 30%; font-weight: 600; }
        .action-button { display: block; width: 80%; margin: 20px auto; padding: 12px 20px; text-align: center; color: white !important; text-decoration: none; border-radius: 6px; font-weight: bold; }
        .approve-btn { background-color: #28a745; }
        .review-btn { background-color: #008779; }
        .reject-btn { background-color: #dc3545; }
        .footer { text-align: center; padding: 20px; font-size: 12px; color: #777; border-top: 1px solid #eee; }
        .badge { display: inline-block; padding: 5px 10px; border-radius: 4px; font-size: 12px; font-weight: bold; color: white; }
        .badge-info { background-color: #008779; }
        .badge-success { background-color: #28a745; }
        .badge-danger { background-color: #dc3545; }
        .notes-box { background-color: #fff3f3; border-left: 5px solid #dc3545; padding: 15px; margin-top: 20px; color: #721c24; }

        /* Style untuk Completed dan Warehouse Process */
        .process-title { font-size: 18px; font-weight: bold; margin-bottom: 15px; color: #00665c; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            @if(isset($data['mail_type']) && $data['mail_type'] === 'completed_notification')
                <h1>Free Goods Ready!</h1>
            @else
                <h1>Action Required: Free Goods Requisition</h1>
            @endif
        </div>
        
        <div class="content">
            @if(isset($data['mail_type']) && $data['mail_type'] === 'completed_notification')
                <p>Hello <strong>{{ $recipient->name }}</strong>,</p>
                <p>Your **Free Goods Requisition** (**FG No: {{ $requisition->no_srs }}**) has been fully processed and is now **Completed**.</p>
                <p>The requested goods are ready to be dispatched/picked up.</p>
                
            @elseif(isset($data['mail_type']) && $data['mail_type'] === 'warehouse_process')
                <p>Hello <strong>{{ $recipient->name }}</strong>,</p>
                <p>You have a new **Warehouse Process** step assigned to you for **Free Goods Requisition** (**FG No: {{ $requisition->no_srs }}**).</p>
                
                <div class="process-title">Step: {{ $data['process_step'] ?? 'Warehouse Process' }}</div>
                <p>Please click the button below to complete the step and provide any necessary notes.</p>
                
                @php
                    $processUrl = route('fg.approval.response', ['token' => $data['token'], 'action' => 'submit']);
                @endphp
                <a href="{{ $processUrl }}" class="action-button approve-btn">Process Step</a>

            @else 
                {{-- Default: Approval --}}
                <p>Hello <strong>{{ $recipient->name }}</strong>,</p>
                <p>You are the next approver for a **Free Goods Requisition** from **{{ $requisition->requester->name ?? 'N/A' }}**.</p>
                <p>Please review the details below and take action:</p>
                
                @php
                    $reviewUrl = route('fg.approval.response', ['token' => $data['token'], 'action' => 'review']);
                    $approveUrl = route('fg.approval.response', ['token' => $data['token'], 'action' => 'approve']);
                    $rejectUrl = route('fg.approval.response', ['token' => $data['token'], 'action' => 'reject']);
                @endphp

                <a href="{{ $approveUrl }}" class="action-button approve-btn">Quick Approve</a>
                <a href="{{ $rejectUrl }}" class="action-button reject-btn">Quick Reject</a>
                
                <p style="text-align: center; margin-top: 10px;">
                    <a href="{{ $reviewUrl }}" style="color: #008779;">Review/Add Notes before Approving</a>
                </p>
                
            @endif

            {{-- Detail Requisition (selalu tampil) --}}
            <table class="details-table">
                <tr><th>FG No</th><td>{{ $requisition->no_srs }}</td></tr>
                <tr><th>Requester</th><td>{{ $requisition->requester->name ?? 'N/A' }}</td></tr>
                <tr><th>Customer</th><td>{{ $requisition->customer->name ?? 'N/A' }}</td></tr>
                <tr><th>Request Date</th><td>{{ \Carbon\Carbon::parse($requisition->request_date)->format('d F Y') }}</td></tr>
                <tr><th>Current Route</th><td><span class="badge badge-info">{{ $requisition->route_to ?? 'N/A' }}</span></td></tr>
                <tr><th>Objective</th><td>{{ $requisition->objectives }}</td></tr>
                <tr><th>Est. Potential</th><td>{{ $requisition->estimated_potential }}</td></tr>
            </table>

            <p style="font-size: 14px; text-align: center; margin-top: 30px;">
                If the buttons do not work, copy and paste the link below into your browser.
                <br>
                {{ $approveUrl ?? $processUrl ?? '' }}
            </p>

        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Requisition Slip App. All rights reserved.</p>
        </div>
    </div>
</body>
</html>