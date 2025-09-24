<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Requisition Approval Response</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap');

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            padding: 20px;
            color: #333;
        }

        #mainContainer {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
            max-width: 1400px;
            margin: 0 auto;
        }

        .card {
            background-color: #ffffff;
            border-radius: 16px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.05);
            border: 1px solid #e9ecef;
        }

        @media (max-width: 1024px) {
            #mainContainer {
                grid-template-columns: 1fr;
            }
        }

        .processing-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background-color: rgba(255, 255, 255, 0.85);
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            z-index: 9999; opacity: 0; visibility: hidden; transition: opacity 0.3s, visibility 0.3s;
        }
        .processing-overlay.show { opacity: 1; visibility: visible; }
        .spinner { border: 4px solid #f3f3f3; border-top: 4px solid #004a99; border-radius: 50%; width: 50px; height: 50px; animation: spin 1s linear infinite; }
        .processing-overlay p { margin-top: 20px; font-weight: 500; font-size: 1.1rem; color: #333; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }

        .header {
            background-color: #004a99;
            color: #ffffff;
            padding: 25px;
            text-align: center;
            border-radius: 16px 16px 0 0;
        }

        .header h1 { margin: 0; font-size: 24px; font-weight: 600; }
        .content { padding: 30px; }

        .section-title {
            font-size: 1rem;
            font-weight: 600;
            color: #004a99;
            margin-bottom: 15px;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px 20px;
            margin-bottom: 25px;
        }

        .info-item .label { font-size: 0.8rem; color: #6c757d; margin-bottom: 2px; }
        .info-item .value { font-size: 0.95rem; font-weight: 500; color: #212529; }
        .info-item.full-width { grid-column: 1 / -1; }

        .table-container {
            border: 1px solid #dee2e6;
            border-radius: 12px;
            overflow: hidden;
            margin-top: 15px;
        }
        .item-table { width: 100%; border-collapse: collapse; }
        .item-table th, .item-table td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #e9ecef; }
        .item-table tbody tr:last-child td { border-bottom: none; }
        .item-table thead th { background-color: #f8f9fa; font-size: 0.85rem; font-weight: 600; color: #495057; }

        .timeline { list-style: none; padding-left: 0; }
        .timeline-item { display: flex; align-items: flex-start; position: relative; padding-bottom: 25px; }
        .timeline-item:not(:last-child)::before { content: ''; position: absolute; width: 2px; background-color: #e9ecef; left: 19px; top: 40px; bottom: 0; }
        .timeline-icon { flex-shrink: 0; display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 50%; margin-right: 20px; }
        .timeline-icon svg { color: white; width: 20px; height: 20px; }
        .timeline-icon.approved { background-color: #28a745; }
        .timeline-icon.rejected { background-color: #dc3545; }
        .timeline-icon.pending { background-color: #ffc107; }

        .timeline-content { padding-top: 5px; }
        .timeline-content .status-text { font-weight: 600; margin-bottom: 2px; }
        .timeline-content .date-text { font-size: 0.85rem; color: #6c757d; margin-bottom: 5px; }
        .timeline-content .notes-text { font-size: 0.9rem; color: #495057; font-style: italic; }

        .action-box { border: 2px dashed #e0e0e0; border-radius: 12px; padding: 25px; text-align: center; }
        .action-title { font-size: 1.2rem; font-weight: 600; margin-bottom: 8px; display: flex; align-items: center; justify-content: center; }
        .action-title svg { width: 24px; height: 24px; margin-right: 10px; }
        .instruction-text { font-size: 0.9rem; color: #6c757d; margin-bottom: 20px; }

        textarea { width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #ced4da; margin-top: 5px; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        textarea:focus { border-color: #004a99; outline: none; box-shadow: 0 0 0 2px rgba(0, 74, 153, 0.25); }

        .button-container { text-align: center; margin-top: 20px; }
        .button { padding: 12px 30px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; color: white; font-size: 1rem; transition: all 0.3s; }
        .btn-review { background-color: #007bff; }
        .btn-review:hover { background-color: #0056b3; transform: translateY(-2px); box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3); }
        .btn-reject { background-color: #dc3545; }
        .btn-reject:hover { background-color: #b02a37; transform: translateY(-2px); box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3); }
        .btn-back { background-color: #004a99; margin-top: 20px; }
        .btn-back:hover { background-color: #003b7a; }

        /* === CSS BARU UNTUK KARTU SUKSES DINAMIS === */
        .success-card { max-width: 600px; margin: 40px auto; display: none; }
        .message-box { padding: 40px 30px; border-radius: 8px; text-align: center; }

        .success { background-color: #e9f7ef; color: #155724; border: 1px solid #c3e6cb; }
        .success .success-icon { box-shadow: 0 4px 10px rgba(40, 167, 69, 0.1); }
        .success .success-icon svg { color: #28a745; }
        .success h2, .success strong { color: #155724; }
        .success p { color: #155724; }
        .success .info-summary { border-top-color: #c3e6cb; }
        .success .info-summary div { border-color: #c3e6cb; }

        .review { background-color: #fff8e1; color: #856404; border: 1px solid #ffecb3; }
        .review .success-icon { box-shadow: 0 4px 10px rgba(255, 193, 7, 0.2); }
        .review .success-icon svg { color: #ffc107; }
        .review h2, .review strong { color: #856404; }
        .review p { color: #856404; }
        .review .info-summary { border-top-color: #ffecb3; }
        .review .info-summary div { border-color: #ffecb3; }

        .reject { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .reject .success-icon { box-shadow: 0 4px 10px rgba(220, 53, 69, 0.2); }
        .reject .success-icon svg { color: #dc3545; }
        .reject h2, .reject strong { color: #721c24; }
        .reject p { color: #721c24; }
        .reject .info-summary { border-top-color: #f5c6cb; }
        .reject .info-summary div { border-color: #f5c6cb; }

        .success-icon { width: 60px; height: 60px; background-color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; }
        .success-icon svg { width: 30px; height: 30px; }
        .message-box h2 { font-size: 1.5rem; font-weight: 600; margin-bottom: 10px; }
        .message-box p { margin-bottom: 25px; }
        .info-summary { border-top: 1px solid; padding-top: 20px; margin-top: 20px; display: grid; grid-template-columns: 1fr; text-align: left; gap: 10px; }
        .info-summary div { background-color: #fff; padding: 10px 15px; border-radius: 8px; border: 1px solid; }
        .info-summary strong { font-weight: 600; display: block; font-size: .8rem; opacity: .8; }
        .info-summary span { font-weight: 500; }
    </style>
</head>
<body>
    <div id="mainContainer">
        {{-- KONTEN KIRI: DETAIL REQUISITION --}}
        <div class="card">
            <div class="header">
                <h1>Requisition Details</h1>
            </div>
            <div class="content">
                <h2 class="section-title">General Information</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="label">SRS Number</div>
                        <div class="value">{{ $requisition->no_srs }}</div>
                    </div>
                    <div class="info-item">
                        <div class="label">Request Date</div>
                        <div class="value">{{ \Carbon\Carbon::parse($requisition->request_date)->format('d F Y') }}</div>
                    </div>
                    <div class="info-item">
                        <div class="label">Category</div>
                        <div class="value">{{ $requisition->category ?? 'N/A' }} / {{ $requisition->sub_category }}</div>
                    </div>
                     <div class="info-item">
                        <div class="label">Requester</div>
                        <div class="value">{{ $requisition->requester->name ?? 'N/A' }}</div>
                    </div>
                </div>

                <h2 class="section-title">Customer Information</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="label">Customer Name</div>
                        <div class="value">{{ $requisition->customer->name ?? 'N/A' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="label">Address</div>
                        <div class="value">{{ $requisition->customer->address ?? '-' }}</div>
                    </div>
                    <div class="info-item   ">
                        <div class="label">Objectives</div>
                        <div class="value">{{ $requisition->objectives ?? '-' }}</div>
                    </div>
                    <div class="info-item   ">
                        <div class="label">Estimated Potential</div>
                        <div class="value">{{ $requisition->estimated_potential ?? '-' }}</div>
                    </div>
                </div>

                @if($requisition->sub_category === 'Special Order' && $requisition->requisitionSpecial)
                <h2 class="section-title">Special Order Details (from Marketing)</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="label">Sample Completion Date</div>
                        <div class="value">{{ \Carbon\Carbon::parse($requisition->requisitionSpecial->requested_date)->format('d F Y') }}</div>
                    </div>
                    <div class="info-item">
                        <div class="label">Sample Weight</div>
                        <div class="value">{{ $requisition->requisitionSpecial->weight_selection ?? '-' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="label">Sample Packaging</div>
                        <div class="value">{{ $requisition->requisitionSpecial->packaging_selection ?? '-' }}</div>
                    </div>
                </div>
                @endif

                <h2 class="section-title">Item List</h2>
                <div class="table-container">
                    <table class="item-table">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Unit</th>
                                <th>Qty Required</th>
                                <th>Qty Issued</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($requisition->requisitionItems as $item)
                            <tr>
                                <td>{{ $item->itemMaster->item_master_code ?? ($item->itemDetail->item_detail_code ?? '-') }}</td>
                                <td>{{ $item->itemMaster->item_master_name ?? ($item->itemDetail->item_detail_name ?? '-') }}</td>
                                <td>{{ $item->itemMaster->unit ?? ($item->itemDetail->unit ?? '-') }}</td>
                                <td>{{ $item->quantity_required }}</td>
                                <td>{{ $item->quantity_issued }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" style="text-align: center;">No items found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- KONTEN KANAN: FORM AKSI & TRACKING --}}
        <div id="rightColumn">
             <div class="card">
                <div class="content">
                    <div id="mainContent">
                        <form id="approvalForm" action="{{ route('approval.process') }}" method="POST">
                            @csrf
                            <input type="hidden" name="token" value="{{ $token }}">
                            <input type="hidden" name="action" id="actionValue">

                            @if($action === 'approve')
                                <div class="action-box">
                                    <h3 class="action-title" style="color: #28a745;"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>Confirm Approval</h3>
                                    <p class="instruction-text">This requisition will be automatically approved. Please wait while we process your request.</p>
                                </div>
                            @else
                                @if($action === 'review')
                                    <h3 class="action-title" style="color: #007bff;"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg>Approve with Review</h3>
                                    <center><p class="instruction-text">This request can be approved, but please provide the necessary notes or revisions. **This field is required.**</p></center>
                                @else
                                    <h3 class="action-title" style="color: #dc3545;"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" /></svg>Reject Requisition</h3>
                                    <center><p class="instruction-text">Please explain in detail why this request is being rejected. **This field is required.**</p></center>
                                @endif

                                <textarea id="notes" name="notes" rows="4" required placeholder="Write your notes here..."></textarea>

                                {{-- ============ PERUBAHAN PADA TOMBOL ============ --}}
                                <div class="button-container">
                                    @if($action === 'review')
                                        <button type="submit" class="button btn-review" data-action="review">Submit with Review</button>
                                    @else
                                        <button type="submit" class="button btn-reject" data-action="reject">Submit Rejection</button>
                                    @endif
                                </div>
                            @endif
                        </form>
                    </div>

                    @if($requisition->approvalLogs->isNotEmpty())
                    <div class="mt-4 pt-4 border-top">
                        <h2 class="section-title">Tracking History</h2>
                        <ul class="timeline">
                             @foreach($requisition->approvalLogs->sortBy('created_at') as $log)
                            <li class="timeline-item">
                                <div class="timeline-icon {{ strtolower($log->status) }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                        @if($log->status == 'Approved') <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                        @elseif($log->status == 'Rejected') <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                        @else <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        @endif
                                    </svg>
                                </div>
                                <div class="timeline-content">
                                    <p class="status-text">{{ $log->status }} by {{ $log->approver->name ?? 'System' }}</p>
                                    <p class="date-text">{{ \Carbon\Carbon::parse($log->responded_at ?? $log->created_at)->format('d F Y, H:i') }}</p>
                                    @if($log->notes)
                                    <p class="notes-text">"{{ $log->notes }}"</p>
                                    @endif
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="processing-overlay" id="processingOverlay">
        <div class="spinner"></div>
        <p>Processing your response...</p>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $(document).ready(function() {

            // --- PERUBAHAN PADA EVENT HANDLER ---
            // Listener untuk semua tombol submit di dalam form
            $('#approvalForm button[type="submit"]').on('click', function(e) {
                e.preventDefault(); // Mencegah form submit secara default
                const action = $(this).data('action'); // Ambil action dari tombol yang diklik
                $('#actionValue').val(action); // Set nilai action ke input tersembunyi
                submitApprovalForm(); // Panggil fungsi submit
            });

            // Fungsi untuk 'approve' otomatis
            if ('{{ $action }}' === 'approve') {
                $('#actionValue').val('approve');
                submitApprovalForm();
            }

            function submitApprovalForm() {
                var form = $('#approvalForm');
                $('#processingOverlay').addClass('show');

                $.ajax({
                    type: "POST",
                    url: form.attr('action'),
                    data: form.serialize(),
                    success: function(response) {
                        // Tambahkan console.log untuk debugging
                        console.log("Server Response:", response);

                        if (response.success) {
                            // --- LOGIKA PESAN SUKSES DINAMIS ---
                            let cardClass = 'success';
                            let title = 'Approval Successful';
                            let message = 'The requisition has been processed. You may now close this page.';
                            let iconSvg = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>';
                            let actionText = 'Approved';

                            if(response.action === 'review') {
                                cardClass = 'review';
                                title = 'Approved with Review';
                                message = 'The requisition has been approved with notes. The process will continue.';
                                iconSvg = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg>';
                                actionText = 'Approved with Review';
                            } else if(response.action === 'reject') {
                                cardClass = 'reject';
                                title = 'Requisition Rejected';
                                message = 'The requisition has been rejected. The requester will be notified.';
                                iconSvg = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>';
                                actionText = 'Rejected';
                            }

                            var successCardHtml = `
                                <div class="card success-card">
                                    <div class="content">
                                        <div class="message-box ${cardClass}" style="display: block;">
                                            <div class="success-icon">${iconSvg}</div>
                                            <h2>${title}</h2>
                                            <p>${message}</p>
                                            <div class="info-summary">
                                                <div><strong>SRS Number:</strong> <span>${response.no_srs}</span></div>
                                                <div><strong>Customer:</strong> <span>${response.customer_name}</span></div>
                                                <div><strong>Action Taken:</strong> <span>${actionText} by ${response.approver_name}</span></div>
                                                <div><strong>New Status:</strong> <span>${response.new_status}</span></div>
                                            </div>
                                            <div class="button-container">
                                                <button id="closeTabBtn" class="button btn-back">Close Tab</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;

                            setTimeout(function() {
                                $('#mainContainer').remove();
                                $('#processingOverlay').remove();
                                $('body').append(successCardHtml);
                                $('body .success-card').fadeIn(400);
                            }, 1500);
                        }
                    },
                    error: function(xhr) {
                        $('#processingOverlay').removeClass('show');
                        var errorMsg = xhr.responseJSON ? xhr.responseJSON.message : 'An error occurred.';
                        alert('Error: ' + errorMsg);
                    }
                });
            }

            $('body').on('click', '#closeTabBtn', function() {
                window.close();
            });
        });
    </script>
</body>
</html>
