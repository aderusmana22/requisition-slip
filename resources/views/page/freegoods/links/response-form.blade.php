<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle }}</title>
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

        /* Gradient Emas */
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

        .text-primary { color: #b8871a !important; }

        .btn-primary { background-color: #cc982f; border-color: #cc982f; color: white; }
        .btn-primary:hover { background-color: #b8871a; border-color: #b8871a; color: white; }

        .btn-success { background-color: #cc982f; border-color: #cc982f; color: white; }
        .btn-success:hover { background-color: #b8871a; border-color: #b8871a; }

        .processing-overlay .spinner-border { color: #cc982f !important; }

        .main-container {
            display: grid;
            grid-template-columns: 2.5fr 1fr;
            gap: 30px;
            max-width: 1400px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .action-card { position: sticky; top: 40px; }
        .left-column>.card { margin-bottom: 30px; }
        .radio-group-horizontal .form-check { margin-right: 15px; }

        @media (max-width: 992px) {
            .main-container { grid-template-columns: 1fr; gap: 20px; padding: 0 15px; margin-top: 20px; margin-bottom: 20px; }
            .action-card { position: static; top: auto; }
            .card-body.p-md-5 { padding: 1.5rem !important; }
            .main-header h4 { font-size: 1.25rem; }
        }
    </style>
</head>

<body>
    <form id="responseForm" action="{{ route('fg.approval.process') }}" method="POST">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="main-container">
            <div class="left-column">
                <div class="card">
                    <div class="card-header main-header">
                        <h4 class="mb-0">Free Goods Requisition Approval</h4>
                        {{-- [REVISI] FG NO Dihapus --}}
                    </div>
                    <div class="card-body p-4 p-md-5">
                        
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
                            
                            {{-- [REVISI] Label Recipient & Data Recipient Name --}}
                            <div class="col-md-4">
                                <div class="info-label">Recipient</div>
                                <div class="info-value">{{ $requisition->recipient_name ?? '-' }}</div>
                            </div>
                            
                            {{-- [REVISI] Data Recipient Address --}}
                            <div class="col-md-8">
                                <div class="info-label">Address</div>
                                <div class="info-value">{{ $requisition->recipient_address ?? '-' }}</div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="info-label">Account / Cost Center</div>
                                <div class="info-value">{{ $requisition->account ?? '-' }} / {{ $requisition->cost_center ?? '-' }}</div>
                            </div>
                            
                            {{-- [REVISI] Estimated Potential Dihapus --}}
                            <div class="col-md-8">
                                <div class="info-label">Objectives</div>
                                <div class="info-value">{{ $requisition->objectives }}</div>
                            </div>
                        </div>

                        {{-- ITEM LIST --}}
                        @if($requisition->requisitionItems->count() > 0)
                        <h5 class="section-title mt-5"><i class="fas fa-cubes"></i> Requested Item List</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 120px;">Item Code</th>
                                        <th>Item Name</th>
                                        <th style="width: 90px;">Unit</th>
                                        <th style="width: 125px;" class="text-center">Qty Required</th>
                                        
                                        {{-- Header Qty Issued (Muncul HANYA jika action = update_qty) --}}
                                        <th class="text-center" style="width: 125px;">
                                            Qty Issued 
                                            @if($action === 'update_qty')
                                                <span class="text-danger">*</span>
                                            @endif
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($requisition->requisitionItems as $item)
                                    <tr>
                                        <td>{{ $item->itemMaster->item_master_code ?? '-' }}</td>
                                        <td>{{ $item->itemMaster->item_master_name ?? '-' }}</td>
                                        <td>{{ $item->itemMaster->unit ?? '-' }}</td>
                                        <td class="text-center">{{ $item->quantity_required }}</td>
                                        <td class="text-center">
                                            @if($action === 'update_qty')
                                                <input type="number" 
                                                    class="form-control form-control-sm text-center fw-bold"
                                                    style="min-width: 80px;"
                                                    name="items[{{ $item->id }}]" 
                                                    value="{{ $item->quantity_issued > 0 ? $item->quantity_issued : '' }}" 
                                                    placeholder="0"
                                                    min="0"
                                                    max="{{ $item->quantity_required }}"
                                                >
                                            @else
                                                {{ $item->quantity_issued > 0 ? $item->quantity_issued : '-' }}
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ACTION COLUMN --}}
            <div class="right-column">
                <div class="card action-card">
                    <div class="card-body p-4">
                        <h5 class="section-title">
                            <i class="fas fa-edit"></i> 
                            @php
                                $actionName = ucwords(str_replace('_', ' ', $action));
                                if($action === 'submit' && $isWarehouseProcess) $actionName = 'Submit';
                            @endphp
                            {{ $actionName }}
                        </h5>

                        @if($action === 'approve' || ($action === 'submit' && $isWarehouseProcess))
                            <input type="hidden" name="action" value="{{ $action }}">
                        @endif

                        @if($isWarehouseProcess)
                            <input type="hidden" name="action" value="{{ $action }}">

                            @if($action === 'submit')
                                <div class="alert alert-success" style="background-color: #d1e7dd; border-color: #badbcc; color: #0f5132;">
                                    <strong>Quick Submit:</strong> You are approving this step without changes.
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-success btn-lg" id="submitBtn">Confirm Submit</button>
                                </div>
                            
                            @elseif ($action === 'review')
                                <div class="alert alert-primary" style="background-color: #cfe2ff; border-color: #b6d4fe; color: #084298;">
                                    <strong>Submit with Notes:</strong> Please provide notes/remarks regarding this step.
                                </div>
                                <div class="mb-3">
                                    <label for="notes" class="form-label"><strong>Notes/Reason: <span class="text-danger">*</span></strong></label>
                                    <div class="alert alert-warning d-flex align-items-start p-2 mb-2" role="alert" style="font-size: 0.8rem;">
                                        <i class="fas fa-exclamation-triangle mt-1 me-2"></i>
                                        <div><strong>Validation:</strong> Please provide a clear sentence.</div>
                                    </div>
                                    <textarea class="form-control" id="notes" name="notes" rows="5" placeholder="Provide notes..." required></textarea>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">Submit with Notes</button>
                                </div>

                            @elseif ($action === 'update_qty')
                                <div class="alert alert-warning border-warning">
                                    <strong>Update Quantity Issued:</strong><br>
                                    Please input the "Qty Issued" directly in the <b>Item List table</b> on the left, then add notes below.
                                </div>
                                <div class="mb-3">
                                    <label for="notes" class="form-label"><strong>Notes: <span class="text-danger">*</span></strong></label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Provide reason..." required></textarea>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">Submit Qty & Notes</button>
                                </div>
                            @endif

                        @else
                            {{-- Managerial Approval --}}
                            <div class="mb-3">
                                <label class="form-label"><strong>Decision:</strong></label>
                                <div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="action" id="action_review"
                                            value="review" @if($action === 'review' && $originalAction !== 'reject') checked @endif>
                                        <label class="form-check-label text-primary" for="action_review"><strong>Approve with Review</strong></label>
                                    </div>
                                    <div class="form-check me-3 mb-1">
                                        <input class="form-check-input" type="radio" name="action" id="action_reject"
                                            value="reject" @if($originalAction === 'reject') checked @endif>
                                        <label class="form-check-label text-danger" for="action_reject"><strong>Reject</strong></label>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="notes" class="form-label"><strong>Notes/Reason:</strong></label>
                                <div class="alert alert-warning d-flex align-items-start p-2 mb-2" role="alert" style="font-size: 0.8rem;">
                                    <i class="fas fa-exclamation-triangle mt-1 me-2"></i>
                                    <div><strong>Validation:</strong> Please provide a clear explanation.</div>
                                </div>
                                <textarea class="form-control" id="notes" name="notes" rows="8" placeholder="Provide notes..."></textarea>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">Submit Notes</button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="processing-overlay" id="processingOverlay">
        <div class="spinner-border" role="status"></div>
        <p class="mt-3">Processing your response...</p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('responseForm');
            const overlay = document.getElementById('processingOverlay');
            const submitBtn = document.getElementById('submitBtn');
            
            const isWarehouseProcess = {{ $isWarehouseProcess ? 'true' : 'false' }};
            const isQuickAction = ('{{ $action }}' === 'approve') || ('{{ $action }}' === 'submit' && isWarehouseProcess);

            if ('{{ $action }}' === 'update_qty') {
                const qtyInputs = document.querySelectorAll('input[name^="items"]');
                qtyInputs.forEach(input => {
                    input.addEventListener('input', function() {
                        let value = this.value.replace(/[^0-9]/g, ''); 
                        if (value.length > 1 && value.startsWith('0')) value = parseInt(value, 10).toString();
                        this.value = value;
                    });
                });
            }

            const validateForm = () => {
                if (isWarehouseProcess) {
                    const notesTextarea = document.getElementById('notes');
                    if (notesTextarea && !(/[a-zA-Z]/.test(notesTextarea.value.trim()))) {
                        Swal.fire({ icon: 'warning', title: 'Catatan Diperlukan', text: 'Mohon berikan catatan yang valid.' });
                        return false;
                    }
                    if ('{{ $action }}' === 'update_qty') {
                        let qtyValid = true;
                        const inputs = document.querySelectorAll('input[name^="items"]');
                        inputs.forEach(input => {
                            if (input.value === '' || parseInt(input.value) < 0) qtyValid = false;
                        });
                        if(!qtyValid) {
                             Swal.fire({ icon: 'warning', title: 'Quantity Invalid', text: 'Qty Issued tidak boleh kosong/negatif.' });
                             return false;
                        }
                    }
                } else {
                    const reviewRadio = document.getElementById('action_review');
                    const rejectRadio = document.getElementById('action_reject');
                    const notesTextarea = document.getElementById('notes');
                    if ((reviewRadio && reviewRadio.checked || rejectRadio && rejectRadio.checked) && !(/[a-zA-Z]/.test(notesTextarea.value.trim()))) {
                        Swal.fire({ icon: 'warning', title: 'Alasan Diperlukan', text: 'Mohon berikan alasan yang valid.' });
                        return false;
                    }
                }
                return true;
            };

            if (isQuickAction) {
                overlay.style.display = 'flex';
                form.submit();
            } 
            else if (form && submitBtn) {
                form.addEventListener('submit', function (event) {
                    event.preventDefault();
                    if (!validateForm()) return;

                    const originalBtnText = submitBtn.innerHTML;
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...`;

                    Swal.fire({
                        title: 'Konfirmasi Pengiriman',
                        text: "Apakah Anda yakin ingin melanjutkan?",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, Lanjutkan!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            overlay.style.display = 'flex';
                            form.submit();
                        } else {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalBtnText;
                        }
                    });
                });
            }
            
            if (!isWarehouseProcess) {
                const reviewRadio = document.getElementById('action_review');
                const rejectRadio = document.getElementById('action_reject');
                const updateSubmitButton = () => {
                    if (reviewRadio && reviewRadio.checked) {
                        submitBtn.textContent = 'Submit Approve';
                        submitBtn.classList.remove('btn-danger'); submitBtn.classList.add('btn-primary');
                    } else if (rejectRadio && rejectRadio.checked) {
                        submitBtn.textContent = 'Submit Reject';
                        submitBtn.classList.remove('btn-primary'); submitBtn.classList.add('btn-danger');
                    }
                };
                if(reviewRadio && rejectRadio){
                    reviewRadio.addEventListener('change', updateSubmitButton);
                    rejectRadio.addEventListener('change', updateSubmitButton);
                    updateSubmitButton();
                }
            }
        });
    </script>
</body>
</html>