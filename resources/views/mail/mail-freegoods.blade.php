<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Free Goods Requisition Notification</title>
    <style>
        /* BASE STYLES */
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 0; background-color: #f8f9fa; color: #333; line-height: 1.6; }
        .email-container { max-width: 800px; margin: 20px auto; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1); }
        
        /* HEADER: WARNA EMAS/COKLAT */
        .email-header { background: linear-gradient(135deg, #cc982f 0%, #b8871a 100%); color: white; padding: 25px 40px; }
        .header-content .email-title { font-size: 24px; font-weight: 600; margin: 0 0 5px 0; }
        .header-content .email-subtitle { font-size: 16px; font-weight: 400; margin: 0; opacity: 0.9; }
        
        /* CONTENT */
        .email-content { padding: 40px; }
        
        /* GREETING BOX */
        .greeting { font-size: 16px; color: #2c3e50; margin-bottom: 25px; padding: 20px; background: #fef8e7; border-radius: 8px; border-left: 4px solid #cc982f; }
        
        /* INFO SECTION */
        .info-section { margin-bottom: 30px; }
        .section-title { background: linear-gradient(135deg, #cc982f 0%, #b8871a 100%); color: white; padding: 12px 20px; margin: 0 0 15px 0; border-radius: 8px 8px 0 0; font-weight: 600; font-size: 16px; }
        
        /* INFO GRID TABLE */
        .info-grid { width: 100%; background: #f8f9fa; padding: 10px; border-radius: 0 0 8px 8px; border: 1px solid #e9ecef; border-top: none; }
        .info-grid table { width: 100%; border-collapse: separate; border-spacing: 10px; }
        .info-grid td { width: 50%; vertical-align: top; padding: 0; }
        
        /* INFO ITEM CARD */
        .info-item { background: white; padding: 15px; border-radius: 6px; border: 1px solid #e9ecef; height: 100%; box-sizing: border-box; }
        .info-label { font-weight: 600; color: #888; font-size: 12px; text-transform: uppercase; margin-bottom: 5px; letter-spacing: 0.5px; }
        .info-value { color: #2c3e50; font-size: 15px; font-weight: 500; word-break: break-word; }
        .info-value-italic { font-style: italic; color: #555; }
        
        /* STATUS BADGES */
        .status-badge { display: inline-block; padding: 5px 12px; border-radius: 20px; font-weight: 700; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; }
        .status-pending { background: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
        .status-processing { background: #cce7ff; color: #004085; border: 1px solid #b8daff; }
        .status-approved { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .status-rejected { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        
        /* TABLE ITEMS */
        .item-table-section { margin-top: 30px; }
        .item-table { width: 100%; border-collapse: collapse; margin-top: 0; background: white; border: 1px solid #e9ecef; border-radius: 0 0 8px 8px; overflow: hidden; }
        .item-table th { background-color: #eee; color: #444; font-weight: 700; text-transform: uppercase; font-size: 12px; padding: 12px 15px; text-align: left; border-bottom: 2px solid #ddd; }
        .item-table td { padding: 12px 15px; border-bottom: 1px solid #eee; font-size: 14px; color: #333; }
        .item-table tr:last-child td { border-bottom: none; }
        
        /* ACTION SECTION */
        .action-section { background: #fef8e7; padding: 30px; border-radius: 12px; text-align: center; margin: 40px 0 20px 0; border: 1px dashed #cc982f; }
        .action-title { font-size: 18px; font-weight: 700; color: #2c3e50; margin-bottom: 10px; }
        .action-subtitle { color: #6c757d; margin-bottom: 20px; font-size: 14px; }
        .btn { display: inline-block; padding: 12px 30px; text-decoration: none; border-radius: 50px; font-weight: 600; font-size: 14px; text-align: center; color: white !important; margin: 5px; transition: all .3s ease; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .btn-approve { background: #28a745; }
        .btn-reject { background: #dc3545; }
        .btn-review { background: #cc982f; }
        .btn-process { background: #007bff; }
        
        /* FOOTER */
        .email-footer { background: #343a40; color: #adb5bd; padding: 30px; text-align: center; font-size: 12px; }
    </style>
</head>
<body>
    <div class="email-container">
        
        <div class="email-header">
            <div class="header-content">
                <h1 class="email-title">Free Goods Requisition</h1>
                <p class="email-subtitle">{{ $requisition->sub_category ?? 'Notification' }}</p>         
           </div>
        </div>

<body
    style="margin: 0; padding: 0; background-color: #f3f4f6; font-family: Arial, sans-serif; color: #333; line-height: 1.6; height: 100% !important; width: 100% !important;">

    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse;">
        <tr>
            <td align="center" style="padding: 20px 0;">

                <table border="0" cellpadding="0" cellspacing="0" width="100%" align="center"
                    style="max-width: 700px; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05); border-collapse: collapse;">

                    <tr>
                        <td bgcolor="#b8871a" style="padding: 30px 40px; text-align: center;">
                            <img src="{{ asset('assets/images/logo/logohitam.png') }}" alt="{{ config('app.name') }}"
                                style="max-height: 50px; width: auto; margin-bottom: 15px; display: block; margin: 0 auto 15px; border: 0;">
                            <h1 style="font-size: 24px; font-weight: 700; margin: 0; color: white;">Free Goods Approval
                                Request</h1>
                            <p style="font-size: 14px; margin: 10px 0 0 0; opacity: 0.9; color: #f9f9f9;">
                                {{ $requisition->no_srs }} - {{ $requisition->sub_category }}
                            </p>
                        </td>
                    </tr>

        <div class="email-content">
            
            <div class="greeting">
                <strong>Dear {{ $recipient->name ?? 'User' }},</strong><br><br>
                @if(isset($mail_type) && $mail_type === 'completed_notification')
                    Requisition <strong>{{ $requisition->no_srs }}</strong> has been fully processed and is now <strong>COMPLETED</strong>.
                @elseif(isset($mail_type) && $mail_type === 'warehouse_process')
                    Requisition <strong>{{ $requisition->no_srs }}</strong> requires your action for stage: <strong>{{ $process_step ?? 'Processing' }}</strong>.
                @elseif(isset($mail_type) && $mail_type === 'rejection_notification')
                    Your Requisition <strong>{{ $requisition->no_srs }}</strong> has been <strong>REJECTED</strong>.
                @else
                    A new Free Goods requisition requires your review. Please check the details below.
                @endif
            </div>
                    <tr>
                        <td style="padding: 30px 40px;">

                            <div
                                style="font-size: 15px; color: #2c3e50; margin-bottom: 25px; padding: 15px; background-color: #f8f9fa; border-radius: 4px; border-left: 4px solid #cc982f;">
                                <strong>Hello {{ $recipient->name }},</strong><br>
                                @if($mail_type === 'completed_notification')
                                Requisition <strong>{{ $requisition->no_srs }}</strong> has been fully processed and is
                                now <strong>Completed</strong>.
                                @elseif($mail_type === 'warehouse_process')
                                Requisition <strong>{{ $requisition->no_srs }}</strong> has been fully approved and now
                                requires action for: <strong>{{ $process_step }}</strong>.
                                @else
                                A new Free Goods requisition requires your review and approval.
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
                                    <div class="info-value">{{ \Carbon\Carbon::parse($requisition->request_date)->format('d M Y') }}</div>
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
                                    <div class="info-label">Address</div>
                                    <div class="info-value">{{ $requisition->recipient_address ?? '-' }}</div>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <div class="info-item">
                                    <div class="info-label">Account / Cost Center</div>
                                    <div class="info-value">{{ $requisition->account ?? 'N/A' }} / {{ $requisition->cost_center ?? '-' }}</div>
                                </div>
                            </td>
                            <td>
                                <div class="info-item">
                                    <div class="info-label">Current Status</div>
                                    <div class="info-value">
                                        @php $status = $requisition->status; @endphp
                                        @if(in_array($status, ['Pending', 'Submitted']))
                                            <span class="status-badge status-pending">{{ $status }}</span>
                                        @elseif(in_array($status, ['In Progress', 'Processing']))
                                            <span class="status-badge status-processing">{{ $status }}</span>
                                        @elseif(in_array($status, ['Approved', 'Completed']))
                                            <span class="status-badge status-approved">{{ $status }}</span>
                                        @elseif(in_array($status, ['Rejected', 'Cancelled']))
                                            <span class="status-badge status-rejected">{{ $status }}</span>
                                        @else
                                            <span class="status-badge">{{ $status }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="2">
                                <div class="info-item">
                                    <div class="info-label">Objectives</div>
                                    <div class="info-value info-value-italic">"{{ $requisition->objectives ?? '-' }}"</div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            
            @if(isset($requisition->requisitionItems) && $requisition->requisitionItems->isNotEmpty())
            <div class="item-table-section">
                <div class="section-title">📦 Requested Item List</div>
                <table class="item-table" cellpadding="0" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Item Code</th>
                            <th>Item Name</th>
                            <th style="text-align: center;">Unit</th>
                            <th style="text-align: center; width: 15%;">Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($requisition->requisitionItems as $item)
                        <tr>
                            <td>{{ $item->itemMaster->item_master_code ?? '-' }}</td>
                            <td>{{ $item->itemMaster->item_master_name ?? '-' }}</td>
                            <td style="text-align: center;">{{ $item->itemMaster->unit ?? '-' }}</td>
                            <td style="text-align: center; font-weight: bold;">{{ $item->quantity_required }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
                            <div style="margin-bottom: 30px;">
                                <table width="100%" cellpadding="0" cellspacing="0" border="0"
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
                                            <div
                                                style="background: white; padding: 12px; border-radius: 6px; border: 1px solid #e9ecef;">
                                                <div
                                                    style="font-weight: 600; color: #495057; font-size: 11px; margin-bottom: 5px; text-transform: uppercase;">
                                                    Request Number</div>
                                                <div style="color: #2c3e50; font-size: 14px; font-weight: bold;">{{
                                                    $requisition->no_srs }}</div>
                                            </div>
                                        </td>
                                        <td width="50%" style="padding: 7px 0 7px 7px;" valign="top">
                                            <div
                                                style="background: white; padding: 12px; border-radius: 6px; border: 1px solid #e9ecef;">
                                                <div
                                                    style="font-weight: 600; color: #495057; font-size: 11px; margin-bottom: 5px; text-transform: uppercase;">
                                                    Request Date</div>
                                                <div style="color: #2c3e50; font-size: 14px;">{{
                                                    \Carbon\Carbon::parse($requisition->request_date)->format('d M Y')
                                                    }}</div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="50%" style="padding: 7px 7px 7px 0;" valign="top">
                                            <div
                                                style="background: white; padding: 12px; border-radius: 6px; border: 1px solid #e9ecef;">
                                                <div
                                                    style="font-weight: 600; color: #495057; font-size: 11px; margin-bottom: 5px; text-transform: uppercase;">
                                                    Requester</div>
                                                <div style="color: #2c3e50; font-size: 14px;">{{
                                                    $requisition->requester->name ?? 'N/A' }}</div>
                                            </div>
                                        </td>
                                        <td width="50%" style="padding: 7px 0 7px 7px;" valign="top">
                                            <div
                                                style="background: white; padding: 12px; border-radius: 6px; border: 1px solid #e9ecef;">
                                                <div
                                                    style="font-weight: 600; color: #495057; font-size: 11px; margin-bottom: 5px; text-transform: uppercase;">
                                                    Customer</div>
                                                <div style="color: #2c3e50; font-size: 14px;">{{
                                                    $requisition->customer->name ?? 'N/A' }}</div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" style="padding: 7px 0;">
                                            <div
                                                style="background: white; padding: 12px; border-radius: 6px; border: 1px solid #e9ecef;">
                                                <div
                                                    style="font-weight: 600; color: #495057; font-size: 11px; margin-bottom: 5px; text-transform: uppercase;">
                                                    Account / Cost Center</div>
                                                <div style="color: #2c3e50; font-size: 14px;">{{ $requisition->account
                                                    ?? 'N/A' }} / {{ $requisition->cost_center ?? '-' }}</div>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            @if($requisition->requisitionItems->isNotEmpty())
                            <div style="margin-bottom: 30px;">
                                <p
                                    style="font-size: 12px; font-weight: 700; color: #4b5563; text-transform: uppercase; border-bottom: 2px solid #e5e7eb; padding-bottom: 5px; margin: 0 0 7px 0;">
                                    📦 Requested Items</p>
                                <table width="100%" cellpadding="0" cellspacing="0" border="0"
                                    style="border-collapse: collapse; border: 1px solid #e9ecef;">
                                    <thead>
                                        <tr bgcolor="#b8871a">
                                            <th
                                                style="color: white; padding: 10px 8px; text-align: left; font-size: 11px;">
                                                Item Code</th>
                                            <th
                                                style="color: white; padding: 10px 8px; text-align: left; font-size: 11px;">
                                                Item Name</th>
                                            <th
                                                style="color: white; padding: 10px 8px; text-align: center; font-size: 11px;">
                                                Unit</th>
                                            <th
                                                style="color: white; padding: 10px 8px; text-align: center; font-size: 11px;">
                                                Qty</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($requisition->requisitionItems as $item)
                                        <tr bgcolor="{{ $loop->even ? '#f8f9fa' : '#ffffff' }}">
                                            <td style="padding: 10px 8px; border: 1px solid #e9ecef; font-size: 12px;">
                                                {{ $item->itemMaster->item_master_code ?? '-' }}</td>
                                            <td style="padding: 10px 8px; border: 1px solid #e9ecef; font-size: 12px;">
                                                {{ $item->itemMaster->item_master_name ?? '-' }}</td>
                                            <td
                                                style="padding: 10px 8px; border: 1px solid #e9ecef; font-size: 12px; text-align: center;">
                                                {{ $item->itemMaster->unit ?? '-' }}</td>
                                            <td
                                                style="padding: 10px 8px; border: 1px solid #e9ecef; font-size: 12px; text-align: center; font-weight: bold;">
                                                {{ $item->quantity_required }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @endif

            @if(isset($mail_type) && ($mail_type == 'approval' || $mail_type == 'warehouse_process'))
            <div class="action-section">
                @if($mail_type === 'warehouse_process')
                    <h3 class="action-title">Process Required</h3>
                    <p class="action-subtitle">Please proceed with the <strong>{{ $process_step }}</strong> step.</p> 
                    <div>
                        <a href="{{ $submit_url ?? '#' }}" class="btn btn-process">Processing Form</a>
                    </div>
                @else
                    <h3 class="action-title">Approval Action Required</h3>
                    <p class="action-subtitle">Please review and select an action below.</p>
                    <div>
                        @if(isset($approve_url))
                            <a href="{{ $approve_url }}" class="btn btn-approve">Approve</a>
                            {{-- [MODIFIED] Tombol Review diganti textnya --}}
                            <a href="{{ $review_url }}" class="btn btn-review">Review with Note</a>
                            <a href="{{ $reject_url }}" class="btn btn-reject">Reject</a>
                        @else
                            <a href="{{ route('freegoods-form.approval') }}" class="btn btn-review">Go to Approval Page</a>
                        @endif
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
                            <table align="center" border="0" cellpadding="0" cellspacing="0"
                                style="margin: 0 auto; border-collapse: collapse;">
                                <tr>
                                    <td style="padding: 25px; text-align: center;">
                                        @if($mail_type === 'completed_notification')
                                        <p style="color: #856404; margin: 0; font-weight: bold;">✅ Requisition Completed
                                        </p>
                                        @elseif($mail_type === 'warehouse_process')
                                        <h3 style="color: #2c3e50; margin: 0 0 15px 0; font-size: 18px;">📦 Warehouse
                                            Action Required</h3>
                                        <table align="center" border="0" cellpadding="0" cellspacing="0"
                                            style="margin: 0 auto; border-collapse: collapse;">
                                            <tr>
                                                <td style="padding: 0 5px;">
                                                    <table border="0" cellpadding="0" cellspacing="0"
                                                        style="border-collapse: collapse;">
                                                        <tr>
                                                            <td align="center" bgcolor="#007bff"
                                                                style="border-radius: 4px;">
                                                                <a href="{{ $submit_url ?? $review_url }}"
                                                                    style="display: inline-block; padding: 12px 25px; font-family: Arial, sans-serif; font-size: 14px; color: #ffffff; text-decoration: none; font-weight: bold; border: 1px solid #007bff; border-radius: 4px; mso-padding-alt: 12px 25px;">✅
                                                                    Submit Process</a>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                        @else
                                        <h3 style="color: #2c3e50; margin: 0 0 15px 0; font-size: 18px;">⚡ Take Action
                                        </h3>
                                        <table align="center" border="0" cellpadding="0" cellspacing="0"
                                            style="margin: 0 auto; border-collapse: collapse;">
                                            <tr>
                                                <td style="padding: 0 5px;">
                                                    <table border="0" cellpadding="0" cellspacing="0"
                                                        style="border-collapse: collapse;">
                                                        <tr>
                                                            <td align="center" bgcolor="#28a745"
                                                                style="border-radius: 4px;">
                                                                <a href="{{ $approve_url }}"
                                                                    style="display: inline-block; padding: 12px 25px; font-family: Arial, sans-serif; font-size: 14px; color: #ffffff; text-decoration: none; font-weight: bold; border: 1px solid #28a745; border-radius: 4px; mso-padding-alt: 12px 25px;">✅
                                                                    Approve</a>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                                <td style="padding: 0 5px;">
                                                    <table border="0" cellpadding="0" cellspacing="0"
                                                        style="border-collapse: collapse;">
                                                        <tr>
                                                            <td align="center" bgcolor="#dc3545"
                                                                style="border-radius: 4px;">
                                                                <a href="{{ $reject_url }}"
                                                                    style="display: inline-block; padding: 12px 25px; font-family: Arial, sans-serif; font-size: 14px; color: #ffffff; text-decoration: none; font-weight: bold; border: 1px solid #dc3545; border-radius: 4px; mso-padding-alt: 12px 25px;">❌
                                                                    Reject</a>
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
                                                                <a href="{{ $review_url }}"
                                                                    style="display: inline-block; padding: 12px 25px; font-family: Arial, sans-serif; font-size: 14px; color: #ffffff; text-decoration: none; font-weight: bold; border: 1px solid #007bff; border-radius: 4px; mso-padding-alt: 12px 25px;">📝
                                                                    Review</a>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                        @endif
                                    </td>
                                </tr>
                            </table>

                            <table width="100%" border="0" cellpadding="0" cellspacing="0"
                                style="border-collapse: collapse; margin: 10px 0;">
                                <tr>
                                    <td align="center" bgcolor="#f8f9fa"
                                        style="padding: 15px; border-radius: 8px; border: 1px solid #e9ecef;">
                                        <table align="center" border="0" cellpadding="0" cellspacing="0"
                                            style="margin: 0 auto; border-collapse: collapse;">
                                            <tr>
                                                <td style="padding: 0 5px;">
                                                    <table border="0" cellpadding="0" cellspacing="0"
                                                        style="border-collapse: collapse;">
                                                        <tr>
                                                            <td align="center" bgcolor="#343a40"
                                                                style="border-radius: 4px;">
                                                                <a href="{{ $download_url ?? '#' }}"
                                                                    style="display: inline-block; padding: 12px 25px; font-family: Arial, sans-serif; font-size: 14px; color: #ffffff; text-decoration: none; font-weight: bold; border: 1px solid #343a40; border-radius: 4px; mso-padding-alt: 12px 25px;">⬇️
                                                                    Download PDF</a>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>

                    <tr>
                        <td align="center" bgcolor="#2c3e50" style="padding: 30px; font-size: 11px; color: #9ca3af;">
                            <div style="font-size: 14px; font-weight: 600; color: white; margin-bottom: 5px;">{{
                                config('app.name') }}</div>
                            <div style="font-size: 12px; opacity: 0.8; margin-bottom: 10px;">Automated Requisition
                                System</div>
                            <div style="height: 1px; background: rgba(255, 255, 255, 0.2); margin: 10px 0;"></div>
                            <div style="font-size: 11px; opacity: 0.7;">
                                © {{ date('Y') }} PT. Sinar Meadow International Indonesia. All rights reserved.<br>
                                Please do not reply directly to this email.
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
