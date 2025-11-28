<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Requisition Complain Notification</title>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <div class="header-content">
                <img src="{{ asset('assets/images/logo/logohitam.png') }}" alt="{{ config('app.name') }}" class="company-logo">
                <h1 class="email-title">Requisition Complain Request</h1>
                <p class="email-subtitle">{{ $requisition->no_srs }} - Complain Request</p>
            </div>
        </div>

        <div class="email-content">
            {{-- ===================== BAGIAN GREETING ===================== --}}
            <div class="greeting">
                <strong>Hello {{ $approver->name }},</strong><br>
                Ada pengajuan <strong>Requisition Complain</strong> yang memerlukan persetujuan Anda. Silakan tinjau detail di bawah ini dan berikan keputusan Anda.
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
                                    <div class="info-value">{{ \Carbon\Carbon::parse($requisition->created_at)->format('d M Y') }}</div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="info-item">
                                    <div class="info-label">Requester</div>
                                    <div class="info-value">{{ $requisition->user->name ?? 'N/A' }}</div>
                                </div>
                            </td>
                            <td>
                                <div class="info-item">
                                    <div class="info-label">Department</div>
                                    <div class="info-value">{{ $requisition->user->department->name ?? 'N/A' }}</div>
                                </div>
                            </td>
                        </tr>
                        @if($requisition->complain_note)
                        <tr>
                            <td colspan="2">
                                <div class="info-item">
                                    <div class="info-label">Complain Note</div>
                                    <div class="info-value">{{ $requisition->complain_note }}</div>
                                </div>
                            </td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

            <div class="action-section">
                <h3 class="action-title">⚡ Take Action</h3>
                <p class="action-subtitle">Please review the complain request above and choose your action below</p>
                <table class="button-group" role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                    <tr>
                        <td class="button-cell">
                            <a href="{{ $approveLink }}" class="btn btn-approve">✅ Quick Approve</a>
                        </td>
                        <td class="button-cell">
                            <a href="{{ $approveWithReviewLink }}" class="btn btn-review">📝 Approve with Notes</a>
                        </td>
                        <td class="button-cell">
                            <a href="{{ $rejectLink }}" class="btn btn-reject">❌ Quick Reject</a>
                        </td>
                    </tr>
                </table>
            </div>

            <table border="0" cellpadding="20" cellspacing="0" width="100%" style="border: 2px solid #000;">
                <tr>
                    <td>
                        <h4 style="margin: 0 0 10px 0;">⚠️ Important Notice</h4>
                        <ul style="margin: 0; padding-left: 20px;">
                            <li style="line-height: 1.5;">This complain approval request is time-sensitive</li>
                            <li style="line-height: 1.5;">Quick actions (Approve/Reject) will be processed immediately</li>
                            <li style="line-height: 1.5;">Use "Approve with Notes" if you need to add comments</li>
                            @if($approver->hasRole('head-QA'))
                            <li style="line-height: 1.5;">Complain images have been attached to this email</li>
                            @else
                            <li style="line-height: 1.5;">Click the action buttons to view complain images and details</li>
                            @endif
                        </ul>
                    </td>
                </tr>
            </table>
        </div>

        <div class="email-footer">
            <div class="copyright">
                © {{ date('Y') }} PT. Sinar Meadow International Indonesia. All rights reserved.<br>
                This is an automated message, please do not reply.
            </div>
        </div>
    </div>
</body>
</html>
