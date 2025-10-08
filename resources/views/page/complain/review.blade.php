<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Requisition Approval Review - {{ config('app.name') }}</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f7fc;
        }

        .main-container {
            display: grid;
            grid-template-columns: 2.5fr 1fr;
            gap: 30px;
            max-width: 1400px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .card {
            background-color: #ffffff;
            border-radius: 16px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.05);
            border: none;
        }

        .card-header.main-header {
            background: linear-gradient(135deg, #004a99 0%, #002c5c 100%);
            color: white;
            padding: 20px 30px;
            border-radius: 16px 16px 0 0 !important;
            border-bottom: none;
        }

        .section-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: #003b7a;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #eef2f9;
            display: flex;
            align-items: center;
        }

        .section-title i {
            margin-right: 12px;
            font-size: 1.2rem;
        }

        .info-label {
            color: #8a96a3;
            font-size: 0.85em;
            margin-bottom: 2px;
        }

        .info-value {
            color: #212529;
            font-weight: 500;
        }

        .action-card {
            position: sticky;
            top: 40px;
        }

        .processing-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.9);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            display: none;
        }

        .spinner-border {
            width: 3rem;
            height: 3rem;
        }

        .status-badge {
            font-size: 0.9em;
            padding: 0.5em 0.7em;
            font-weight: 700;
        }

        @media (max-width: 1024px) {
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
                    <!-- Judul -->
                    <div class="row mb-4 text-center">
                        <h5><strong>REQUISITION SLIP - COMPLAIN</strong></h5>
                        <p class="mb-0">SALES & MARKETING<br>SAMPLE PRODUCT</p>
                    </div>

                    {{-- Detail Utama --}}
                    <h5 class="section-title"><i class="fas fa-file-invoice"></i> Requisition Details</h5>
                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="info-label">Customer Name</div>
                            <div class="info-value">{{ $requisition->customer->name ?? '-' }}</div>
                        </div>
                        <div class="col-md-8">
                            <div class="info-label">Customer Address</div>
                            <div class="info-value">{{ $requisition->customer->address ?? '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-label">Account</div>
                            <div class="info-value">{{ $requisition->account }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-label">Cost Center</div>
                            <div class="info-value">{{ number_format($requisition->cost_center) }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-label">Nomor RS</div>
                            <div class="info-value">{{ $requisition->no_srs }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-label">Request Date</div>
                            <div class="info-value">{{ $requisition->request_date }}</div>
                        </div>
                        @if($requisition->objectives)
                        <div class="col-md-8">
                            <div class="info-label">Objectives</div>
                            <div class="info-value">{{ $requisition->objectives }}</div>
                        </div>
                        @endif
                    </div>

                    {{-- Detail Item --}}
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

                    <!-- Current Status -->
                    <h5 class="section-title mt-5"><i class="fas fa-info-circle"></i> Current Status</h5>
                    <div class="p-3">
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
                            <label class="form-label"><strong>Decision:</strong></label>
                            <div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="status" id="approve"
                                        value="approve" required>
                                    <label class="form-check-label text-success" for="approve">
                                        <strong>Approve</strong>
                                    </label>
                                </div>
                                <div class="form-check">
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
                            <label for="notes" class="form-label"><strong>Notes/Reason:</strong></label>
                            <textarea class="form-control" id="notes" name="notes" rows="6"
                                placeholder="Please provide your reason for this decision (optional for approval, required for rejection)"></textarea>
                            <div class="form-text">
                                <span id="notes-help-text">Please provide a reason for your decision.</span>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                Submit Review
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Overlay saat proses --}}
    <div class="processing-overlay" id="processingOverlay">
        <div class="spinner-border text-primary" role="status"></div>
        <p class="mt-3">Processing your response...</p>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('reviewForm');
            const approveRadio = document.getElementById('approve');
            const rejectRadio = document.getElementById('reject');
            const notesTextarea = document.getElementById('notes');
            const notesHelpText = document.getElementById('notes-help-text');
            const submitBtn = document.getElementById('submitBtn');

            // Update form behavior based on selected decision
            function updateFormBehavior() {
                if (rejectRadio.checked) {
                    notesTextarea.required = true;
                    notesHelpText.textContent = 'Please provide a reason for rejection (required).';
                    notesHelpText.className = 'form-text text-danger';
                    submitBtn.textContent = 'Submit Rejection';
                    submitBtn.className = 'btn btn-danger';
                } else if (approveRadio.checked) {
                    notesTextarea.required = false;
                    notesHelpText.textContent = 'Optional: Add any notes or comments for this approval.';
                    notesHelpText.className = 'form-text text-muted';
                    submitBtn.textContent = 'Submit Approval';
                    submitBtn.className = 'btn btn-success';
                } else {
                    notesTextarea.required = false;
                    notesHelpText.textContent = 'Please provide a reason for your decision.';
                    notesHelpText.className = 'form-text text-muted';
                    submitBtn.textContent = 'Submit Review';
                    submitBtn.className = 'btn btn-primary';
                }
            }

            // Add event listeners
            approveRadio.addEventListener('change', updateFormBehavior);
            rejectRadio.addEventListener('change', updateFormBehavior);

            // Form submission
            form.addEventListener('submit', function (e) {
                e.preventDefault();

                const formData = new FormData(form);
                const decision = formData.get('status');
                const notes = formData.get('notes');

                // Validation for rejection
                if (decision === 'reject' && !notes.trim()) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Notes Required',
                        text: 'Please provide a reason for rejection.',
                        confirmButtonColor: '#d33'
                    });
                    return;
                }

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
                        // Show loading
                        Swal.fire({
                            title: 'Processing...',
                            text: 'Please wait while we process your decision.',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            allowEnterKey: false,
                            showConfirmButton: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Submit form
                        fetch(form.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        })
                            .then(response => {
                                // Check if response is ok
                                if (!response.ok) {
                                    throw new Error(`HTTP error! status: ${response.status}`);
                                }

                                // Check if response is JSON
                                const contentType = response.headers.get('content-type');
                                if (contentType && contentType.includes('application/json')) {
                                    return response.json();
                                } else {
                                    throw new Error('Server returned non-JSON response');
                                }
                            })
                            .then(data => {
                                if (data.success && data.message) {
                                    // Immediately show the closing message without SweetAlert
                                    if (window.opener) {
                                        // If opened in popup, close it
                                        window.close();
                                    } else {
                                        // If in main window, show closing message immediately
                                        document.body.innerHTML = `
                                        <div style="
                                            display: flex;
                                            justify-content: center;
                                            align-items: center;
                                            height: 100vh;
                                            background: linear-gradient(135deg, #cc982f 0%, #b8871a 100%);
                                            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                                            margin: 0;
                                            color: white;
                                        ">
                                            <div style="
                                                text-align: center;
                                                background: rgba(255,255,255,0.1);
                                                padding: 50px 40px;
                                                border-radius: 20px;
                                                box-shadow: 0 20px 40px rgba(0,0,0,0.3);
                                                backdrop-filter: blur(10px);
                                                border: 1px solid rgba(255,255,255,0.2);
                                                max-width: 500px;
                                                animation: fadeIn 0.5s ease-out;
                                            ">
                                                <div style="font-size: 4em; margin-bottom: 20px; animation: bounce 1s ease-out;">✅</div>
                                                <h1 style="margin: 0 0 20px 0; font-size: 2.5em; font-weight: 300;">Approval Completed</h1>
                                                <p style="margin: 0 0 30px 0; font-size: 1.2em; opacity: 0.9; line-height: 1.6;">
                                                    ${data.message}<br>
                                                    <span id="auto-close-countdown">This page will close in 5 seconds...</span>
                                                </p>
                                                <div style="
                                                    background: rgba(255,255,255,0.2);
                                                    padding: 15px;
                                                    border-radius: 10px;
                                                    margin-top: 20px;
                                                    font-size: 0.9em;
                                                    opacity: 0.8;
                                                ">
                                                    You can safely close this browser tab
                                                </div>
                                            </div>
                                        </div>
                                        <style>
                                            @keyframes fadeIn {
                                                from { opacity: 0; transform: scale(0.8); }
                                                to { opacity: 1; transform: scale(1); }
                                            }
                                            @keyframes bounce {
                                                0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
                                                40% { transform: translateY(-10px); }
                                                60% { transform: translateY(-5px); }
                                            }
                                        </style>
                                    `;
                                        document.title = '✅ Approval Completed - Page Closed';

                                        // Auto close countdown
                                        let countdown = 5;
                                        const countdownElement = document.getElementById('auto-close-countdown');

                                        const countdownTimer = setInterval(() => {
                                            countdown--;
                                            if (countdown > 0) {
                                                countdownElement.textContent = `This page will close in ${countdown} seconds...`;
                                            } else {
                                                countdownElement.textContent = 'Closing now...';
                                                clearInterval(countdownTimer);

                                                // Try to close after showing the message
                                                setTimeout(() => {
                                                    window.close();
                                                }, 5000);
                                            }
                                        }, 500);
                                    }
                                } else {
                                    throw new Error(data.message || 'Invalid response from server');
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                let errorMessage = 'An error occurred while processing your request. Please try again.';

                                // If error contains specific message, use it
                                if (error.message && error.message !== 'Failed to fetch') {
                                    errorMessage = error.message;
                                }

                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: errorMessage,
                                    confirmButtonColor: '#d33'
                                });
                            });
                    }
                });
            });

            // Initialize form behavior
            updateFormBehavior();

            // Auto-close feature - close page after 5 minutes of inactivity
            let inactivityTimer;
            let inactivityCountdown = 300; // 5 minutes in seconds
            let showingInactivityWarning = false;

            function resetInactivityTimer() {
                clearTimeout(inactivityTimer);
                if (!showingInactivityWarning) {
                    inactivityTimer = setTimeout(showInactivityWarning, 240000); // Show warning after 4 minutes
                }
            }

            function showInactivityWarning() {
                showingInactivityWarning = true;
                const warningHtml = `
                    <div class="alert alert-warning mt-3" id="inactivity-warning">
                        <i class="fas fa-exclamation-triangle"></i> 
                        <strong>Inactivity Warning:</strong> This page will close in <span id="inactivity-countdown">60</span> seconds due to inactivity. 
                        Click anywhere to stay on this page.
                    </div>
                `;

                const cardBody = document.querySelector('.card-body');
                cardBody.insertAdjacentHTML('beforeend', warningHtml);

                let countdown = 60;
                const countdownElement = document.getElementById('inactivity-countdown');

                const countdownInterval = setInterval(() => {
                    countdown--;
                    if (countdown > 0) {
                        countdownElement.textContent = countdown;
                    } else {
                        clearInterval(countdownInterval);
                        // Close the page
                        if (window.opener) {
                            window.close();
                        } else {
                            // Show inactivity timeout message
                            document.body.innerHTML = `
                                <div style="
                                    display: flex;
                                    justify-content: center;
                                    align-items: center;
                                    height: 100vh;
                                    background: linear-gradient(135deg, #cc982f 0%, #b8871a 100%);
                                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                                    margin: 0;
                                    color: white;
                                ">
                                    <div style="
                                        text-align: center;
                                        background: rgba(255,255,255,0.1);
                                        padding: 50px 40px;
                                        border-radius: 20px;
                                        box-shadow: 0 20px 40px rgba(0,0,0,0.3);
                                        backdrop-filter: blur(10px);
                                        border: 1px solid rgba(255,255,255,0.2);
                                        max-width: 500px;
                                    ">
                                        <div style="font-size: 4em; margin-bottom: 20px;">⏰</div>
                                        <h1 style="margin: 0 0 20px 0; font-size: 2.5em; font-weight: 300;">Session Expired</h1>
                                        <p style="margin: 0 0 30px 0; font-size: 1.2em; opacity: 0.9; line-height: 1.6;">
                                            This approval page has been closed<br>
                                            due to inactivity timeout.
                                        </p>
                                        <div style="
                                            background: rgba(255,255,255,0.2);
                                            padding: 15px;
                                            border-radius: 10px;
                                            margin-top: 20px;
                                            font-size: 0.9em;
                                            opacity: 0.8;
                                        ">
                                            Please close this browser tab
                                        </div>
                                    </div>
                                </div>
                            `;
                            document.title = '⏰ Session Expired - Page Closed';
                        }
                    }
                }, 1000);

                // Allow user to cancel by clicking anywhere
                document.addEventListener('click', function cancelInactivityClose() {
                    clearInterval(countdownInterval);
                    const warningElement = document.getElementById('inactivity-warning');
                    if (warningElement) {
                        warningElement.remove();
                    }
                    showingInactivityWarning = false;
                    resetInactivityTimer();
                    document.removeEventListener('click', cancelInactivityClose);
                }, { once: true });
            }

            // Start inactivity timer
            resetInactivityTimer();

            // Reset timer on user activity
            ['click', 'mousemove', 'keypress', 'scroll', 'touchstart'].forEach(event => {
                document.addEventListener(event, resetInactivityTimer, true);
            });
        });
    </script>
</body>

</html>