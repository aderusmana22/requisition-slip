<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Requisition Approval Review - {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f7fc;
        }

        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, .05);
        }

        .card-header.main-header {
            background: linear-gradient(135deg, #cc982f 0%, #b8871a 100%);
            color: white;
            padding: 20px 30px;
            border-radius: 16px 16px 0 0 !important;
        }

        .section-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #b8871a;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #eef2f9;
            display: flex;
            align-items: center;
        }

        .section-title i {
            margin-right: 12px;
            font-size: 1.1rem;
            width: 20px;
            text-align: center;
        }

        .info-label {
            color: #8a96a3;
            font-size: .85em;
            margin-bottom: 2px;
        }

        .info-value {
            color: #212529;
            font-weight: 500;
        }

        .processing-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, .9);
            display: none;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        .form-control:disabled,
        .form-control[readonly] {
            background-color: #e9ecef;
            opacity: 1;
        }

        .text-primary {
            color: #b8871a !important;
        }

        .btn-primary {
            background-color: #cc982f;
            border-color: #cc982f;
        }

        .btn-primary:hover {
            background-color: #b8871a;
            border-color: #b8871a;
        }

        .processing-overlay .spinner-border {
            color: #cc982f !important;
        }

        /* Layout Grid 2 Kolom yang konsisten */
        .main-container {
            display: grid;
            grid-template-columns: 2.5fr 1fr;
            gap: 30px;
            max-width: 1400px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .action-card {
            position: sticky;
            top: 40px;
        }

        .left-column>.card {
            margin-bottom: 30px;
        }

        .status-badge {
            font-size: 0.9em;
            padding: 0.5em 0.7em;
            font-weight: 700;
        }

        @media (max-width:1024px) {
            .main-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <div class="main-container">
        <div class="left-column">
            <div class="card">
                <div class="card-header main-header">
                    <h4 class="mb-0">Complain Requisition Approval</h4>
                    <p class="mb-0 opacity-75">SRS No: {{ $requisition->no_srs }}</p>
                </div>
                <div class="card-body p-4 p-md-5">
                    {{-- BAGIAN 1: DETAIL REQUISITION --}}
                    <h5 class="section-title"><i class="fas fa-file-invoice"></i> Requisition Details</h5>
                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="info-label">Request Date</div>
                            <div class="info-value">{{ \Carbon\Carbon::parse($requisition->request_date)->format('d F Y') }}</div>
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
                            <div class="info-label">Customer Address</div>
                            <div class="info-value">{{ $requisition->customer->address ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-label">Account</div>
                            <div class="info-value">{{ $requisition->account ?? '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-label">Cost Center</div>
                            <div class="info-value">{{ $requisition->cost_center ?? '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-label">Objectives</div>
                            <div class="info-value">{{ $requisition->objectives ?? '-' }}</div>
                        </div>
                    </div>

                    {{-- BAGIAN 2: DETAIL ITEM --}}
                    @if($requisition->requisitionItems && $requisition->requisitionItems->count() > 0)
                    <h5 class="section-title mt-5"><i class="fas fa-cubes"></i> Product Details</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Material Type</th>
                                    <th>Product Code</th>
                                    <th>Product Name</th>
                                    <th>Unit</th>
                                    <th class="text-center">QTY Required</th>
                                    <th class="text-center">QTY Issued</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($requisition->requisitionItems as $item)
                                @php
                                $detail = $item->itemMaster->ItemDetails->firstWhere('id', $item->item_detail_id);
                                @endphp
                                @if($detail)
                                <tr>
                                    <td>{{ $detail->material_type ?? '-' }}</td>
                                    <td>{{ $detail->item_detail_code ?? '-' }}</td>
                                    <td>{{ $detail->item_detail_name ?? '-' }}</td>
                                    <td>{{ $detail->unit ?? '-' }}</td>
                                    <td class="text-center">{{ $item->quantity_required }}</td>
                                    <td class="text-center">{{ $item->quantity_issued }}</td>
                                </tr>
                                @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif

                    {{-- BAGIAN 3: CURRENT STATUS --}}
                    <h5 class="section-title mt-5"><i class="fas fa-info-circle"></i> Current Status</h5>
                    <div class="row g-4">
                        <div class="col-md-12">
                            <div class="info-label">Status</div>
                            <div class="info-value">
                                @if($requisition->status == 'Pending')
                                <span class="badge status-badge bg-warning text-dark">Pending</span>
                                @elseif($requisition->status == 'In Progress')
                                <span class="badge status-badge bg-info text-white">In Progress</span>
                                @elseif($requisition->status == 'Approved')
                                <span class="badge status-badge bg-success">Approved</span>
                                @elseif($requisition->status == 'Rejected')
                                <span class="badge status-badge bg-danger">Rejected</span>
                                @else
                                <span class="badge status-badge bg-secondary">{{ $requisition->status }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kolom Kanan: Form Aksi --}}
        <div class="right-column">
            <div class="card action-card">
                <div class="card-body p-4">
                    <h5 class="section-title"><i class="fas fa-check-to-slot"></i> Review & Decision</h5>
                    <form action="{{ route('complain.approval.process') }}" method="POST" id="reviewForm">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">
                        <input type="hidden" name="id" value="{{ $requisition->id }}">

                        <!-- Decision -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Decision:</label>
                            <div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="status" id="approve"
                                        value="approve" required>
                                    <label class="form-check-label text-success" for="approve">
                                        <strong>Approve</strong>
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="status" id="reject"
                                        value="reject" required>
                                    <label class="form-check-label text-danger" for="reject">
                                        <strong>Reject</strong>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Notes/Reason -->
                        <div class="mb-3">
                            <label for="notes" class="form-label fw-semibold">Notes/Reason:</label>
                            <textarea class="form-control" id="notes" name="notes" rows="8"
                                placeholder="Provide notes for your decision..."></textarea>
                            <div class="form-text" id="notes-help-text">Notes are required for rejection.</div>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                Submit Decision
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="processing-overlay" id="processingOverlay">
        <div class="spinner-border" role="status"></div>
        <p class="mt-3">Processing your response...</p>
    </div>

    <!-- Javascript -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('reviewForm');
            const overlay = document.getElementById('processingOverlay');
            const submitBtn = document.getElementById('submitBtn');
            const approveRadio = document.getElementById('approve');
            const rejectRadio = document.getElementById('reject');
            const notesTextarea = document.getElementById('notes');
            const notesHelpText = document.getElementById('notes-help-text');

            // --- FUNGSI VALIDASI ---
            const validateForm = () => {
                const reviewRadio = document.getElementById('reject');
                const notesTextarea = document.getElementById('notes');
                if (reviewRadio.checked && !(/[a-zA-Z]/.test(notesTextarea.value.trim()))) {
                    Swal.fire({ 
                        icon: 'warning', 
                        title: 'Alasan Diperlukan', 
                        text: 'Mohon berikan alasan yang valid untuk penolakan.' 
                    });
                    return false;
                }
                return true; // Jika semua validasi lolos
            };

            // Update form behavior based on selected decision
            function updateFormBehavior() {
                if (rejectRadio.checked) {
                    notesTextarea.required = true;
                    notesHelpText.textContent = 'Please provide a reason for rejection (required).';
                    notesHelpText.className = 'form-text text-danger';
                    submitBtn.textContent = 'Submit Rejection';
                    submitBtn.className = 'btn btn-danger btn-lg';
                } else if (approveRadio.checked) {
                    notesTextarea.required = false;
                    notesHelpText.textContent = 'Optional: Add any notes or comments for this approval.';
                    notesHelpText.className = 'form-text text-muted';
                    submitBtn.textContent = 'Submit Approval';
                    submitBtn.className = 'btn btn-success btn-lg';
                } else {
                    notesTextarea.required = false;
                    notesHelpText.textContent = 'Please provide a reason for your decision.';
                    notesHelpText.className = 'form-text text-muted';
                    submitBtn.textContent = 'Submit Decision';
                    submitBtn.className = 'btn btn-primary btn-lg';
                }
            }

            // Add event listeners
            approveRadio.addEventListener('change', updateFormBehavior);
            rejectRadio.addEventListener('change', updateFormBehavior);

            // --- LOGIKA SUBMIT MANUAL ---
            if (form && submitBtn) {
                form.addEventListener('submit', function (event) {
                    event.preventDefault(); // Selalu hentikan submit default terlebih dahulu

                    // Jika validasi gagal, hentikan proses
                    if (!validateForm()) {
                        return;
                    }

                    // Simpan teks asli tombol
                    const originalBtnText = submitBtn.innerHTML;

                    // Langsung nonaktifkan tombol dan tampilkan status loading
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...`;

                    const formData = new FormData(form);
                    const decision = formData.get('status');
                    const notes = formData.get('notes');

                    // Confirmation dialog
                    const title = decision === 'approve' ? 'Confirm Approval' : 'Confirm Rejection';
                    const text = decision === 'approve'
                        ? 'Are you sure you want to approve this requisition?'
                        : 'Are you sure you want to reject this requisition?';
                    const confirmButtonColor = decision === 'approve' ? '#28a745' : '#dc3545';

                    Swal.fire({
                        title: title,
                        text: text,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: confirmButtonColor,
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: decision === 'approve' ? 'Yes, Approve' : 'Yes, Reject',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Jika dikonfirmasi, tampilkan overlay dan submit form
                            overlay.style.display = 'flex';
                            form.submit();
                        } else {
                            // Jika dibatalkan, aktifkan kembali tombolnya
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalBtnText;
                        }
                    });
                });
            }

            // Initialize form behavior
            updateFormBehavior();
        });
    </script>
</body>

</html>