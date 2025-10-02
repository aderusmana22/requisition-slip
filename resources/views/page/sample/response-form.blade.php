<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approval: {{ $requisition->no_srs }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f7fc
        }

        .main-container {
            display: grid;
            grid-template-columns: 2.5fr 1fr;
            gap: 30px;
            max-width: 1400px;
            margin: 40px auto;
            padding: 0 20px
        }

        .card {
            background-color: #fff;
            border-radius: 16px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, .05);
            border: none
        }

        .card-header.main-header {
            background: linear-gradient(135deg, #004a99 0%, #002c5c 100%);
            color: white;
            padding: 20px 30px;
            border-radius: 16px 16px 0 0 !important;
            border-bottom: none
        }

        .section-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: #003b7a;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #eef2f9;
            display: flex;
            align-items: center
        }

        .section-title i {
            margin-right: 12px;
            font-size: 1.2rem
        }

        .info-label {
            color: #8a96a3;
            font-size: .85em;
            margin-bottom: 2px
        }

        .info-value {
            color: #212529;
            font-weight: 500
        }

        .action-card {
            position: sticky;
            top: 40px
        }

        .processing-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, .9);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            display: none
        }

        .spinner-border {
            width: 3rem;
            height: 3rem
        }

        .tracking-list {
            list-style: none;
            padding-left: 0
        }

        .tracking-item {
            position: relative;
            padding: 10px 0 25px 30px;
            border-left: 2px solid #e9ecef
        }

        .tracking-item:last-child {
            border-left: 2px solid transparent
        }

        .tracking-item::before {
            content: '';
            position: absolute;
            left: -9px;
            top: 12px;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background-color: #e9ecef;
            border: 3px solid #f4f7fc
        }

        .tracking-item.approved::before {
            background-color: #28a745
        }

        .tracking-item.pending::before {
            background-color: #ffc107
        }

        .tracking-item.rejected::before {
            background-color: #dc3545
        }

        .tracking-name {
            font-weight: 600
        }

        .tracking-notes {
            font-size: .9em;
            color: #6c757d;
            font-style: italic
        }

        .tracking-date {
            font-size: .8em;
            color: #adb5bd
        }

        @media (max-width:1024px) {
            .main-container {
                grid-template-columns: 1fr
            }
        }

    </style>
</head>

<body>
    <div class="main-container">
        <div class="left-column">
            {{-- (Konten kolom kiri: detail requisition, dll. tidak perlu diubah) --}}
            <div class="card">
                <div class="card-header main-header">
                    <h4 class="mb-0">Sample Requisition Approval</h4>
                    <p class="mb-0 opacity-75">SRS No: {{ $requisition->no_srs }}</p>
                </div>
                <div class="card-body p-4 p-md-5">
                    {{-- Detail Utama --}}
                    <h5 class="section-title"><i class="fas fa-file-invoice"></i> Requisition Details</h5>
                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="info-label">Request Date</div>
                            <div class="info-value">
                                {{ \Carbon\Carbon::parse($requisition->request_date)->format('d F Y') }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-label">Requester</div>
                            <div class="info-value">{{ $requisition->requester->name ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-label">Department</div>
                            <div class="info-value">{{ $requisition->requester->department->name ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-label">Customer Name</div>
                            <div class="info-value">{{ $requisition->customer->name ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-8">
                            <div class="info-label">Address</div>
                            <div class="info-value">{{ $requisition->customer->address ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-label">Cost Center</div>
                            <div class="info-value">{{ $requisition->cost_center ?? '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-label">Objectives</div>
                            <div class="info-value">{{ $requisition->objectives }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-label">Estimated Potential</div>
                            <div class="info-value">{{ $requisition->estimated_potential }}</div>
                        </div>
                    </div>

                    {{-- Detail Item --}}
                    @if($requisition->requisitionItems->count() > 0)
                    <h5 class="section-title mt-5"><i class="fas fa-cubes"></i> Requested Item List</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="table-light">
                                <tr>
                                    @if($requisition->sub_category == 'Packaging')
                                    <th class="material-type-column">Material Type</th>
                                    @endif
                                    <th>Item Code</th>
                                    <th>Item Name</th>
                                    <th>Unit</th>
                                    <th class="text-center">Qty Required</th>
                                    <th class="text-center">Qty Issued</th>
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
                                    <td class="text-center">{{ $item->quantity_required }}</td>
                                    <td class="text-center">{{ $item->quantity_issued ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif

                    <h5 class="section-title mt-5"><i class="fas fa-shoe-prints"></i> Approval & Process Tracking</h5>
                    <div class="p-3">
                        <ul class="tracking-list">
                            @foreach($requisition->approvalLogs->sortBy('level') as $log)
                            <li class="tracking-item {{ strtolower($log->status) }}">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="tracking-name">{{ $log->approver->name ?? 'N/A' }} (Level
                                        {{ $log->level }})</span>
                                    <span
                                        class="tracking-date">{{ \Carbon\Carbon::parse($log->updated_at)->format('d M Y, H:i') }}</span>
                                </div>
                                @if($log->notes)
                                <p class="tracking-notes mb-0">"{{ $log->notes }}"</p>
                                @endif
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="right-column">
            <div class="card action-card">
                <div class="card-body p-4">
                    {{-- [DIPERBAIKI] Judul form dinamis --}}
                    <h5 class="section-title"><i class="fas fa-check-to-slot"></i> {{ $pageTitle }}</h5>
                    <form id="approvalForm" action="{{ route('approval.process') }}" method="POST">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">

                        @if($action === 'approve')
                        <div class="alert alert-success text-center">
                            <h5 class="alert-heading">Confirm Action</h5>
                            <p class="mb-0">This request will be processed immediately. Please wait...</p>
                        </div>
                        <input type="hidden" name="action" value="approve">
                        @else
                        <div class="mb-3">
                            <label class="form-label"><strong>Decision:</strong></label>
                            <div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="action" id="action_review"
                                        value="review" @if($action==='review' ) checked @endif>
                                    {{-- [DIPERBAIKI] Teks radio button dinamis --}}
                                    <label class="form-check-label text-primary"
                                        for="action_review"><strong>{{ $reviewRadioText }}</strong></label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="action" id="action_reject"
                                        value="reject" @if($action==='reject' ) checked @endif>
                                    <label class="form-check-label text-danger"
                                        for="action_reject"><strong>Reject</strong></label>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="notes" class="form-label"><strong>Notes/Reason:</strong></label>
                            <textarea class="form-control" id="notes" name="notes" rows="6"
                                placeholder="Provide notes for your decision..."></textarea>
                            <div class="form-text" id="notes-help-text">Notes are required for rejection.</div>
                        </div>
                        <div class="d-grid">
                            {{-- [DIPERBAIKI] Teks tombol submit dinamis --}}
                            <button type="submit" class="btn btn-primary btn-lg"
                                id="submitBtn">{{ $submitButtonText }}</button>
                        </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{-- (Overlay dan JavaScript tidak perlu diubah) --}}
    <div class="processing-overlay" id="processingOverlay">
        <div class="spinner-border text-primary" role="status"></div>
        <p class="mt-3">Processing your response...</p>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('approvalForm');
            const overlay = document.getElementById('processingOverlay');
            const reviewRadio = document.getElementById('action_review');
            const rejectRadio = document.getElementById('action_reject');
            const notesTextarea = document.getElementById('notes');
            const notesHelpText = document.getElementById('notes-help-text');
            const submitBtn = document.getElementById('submitBtn');

            function updateFormBehavior() {
                if (rejectRadio && rejectRadio.checked) {
                    notesTextarea.required = true;
                    notesHelpText.textContent = 'Please provide a reason for rejection (required).';
                    notesHelpText.classList.add('text-danger');
                    submitBtn.textContent = 'Submit Rejection';
                    submitBtn.classList.remove('btn-primary');
                    submitBtn.classList.add('btn-danger');
                } else if (reviewRadio && reviewRadio.checked) {
                    notesTextarea.required = false;
                    notesHelpText.textContent = 'Optional: Add notes for this approval.';
                    notesHelpText.classList.remove('text-danger');
                    // Teks tombol diambil dari Blade, jadi tidak perlu diubah di sini
                    submitBtn.classList.remove('btn-danger');
                    submitBtn.classList.add('btn-primary');
                }
            }

            if (reviewRadio && rejectRadio) {
                reviewRadio.addEventListener('change', updateFormBehavior);
                rejectRadio.addEventListener('change', updateFormBehavior);
                updateFormBehavior();
            }

            if ('{{ $action }}' === 'approve') {
                overlay.style.display = 'flex';
                // Form akan otomatis di-submit oleh JavaScript jika action-nya 'approve' (atau 'submit')
                form.submit();
            }

            form.addEventListener('submit', function (event) {
                if (rejectRadio && rejectRadio.checked && !notesTextarea.value.trim()) {
                    alert('Notes are required for rejection.');
                    event.preventDefault();
                    return;
                }
                overlay.style.display = 'flex';
            });
        });

    </script>
</body>

</html>
