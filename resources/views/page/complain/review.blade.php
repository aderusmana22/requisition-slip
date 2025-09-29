<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Requisition Approval Review - {{ config('app.name') }}</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card-header {
            background-color: #cc982f;
            color: white;
        }
        .logo {
            max-height: 60px;
            width: auto;
        }
        .status-badge {
            font-size: 0.9em;
            padding: 0.5em 0.7em;
            font-weight: 700;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow">
                    <div class="card-header text-center">
                        <h4 class="mb-0">Requisition Approval Review</h4>
                    </div>
                    <div class="card-body">
                        <!-- Header dengan logo -->
                        <header class="row mb-4 align-items-center">
                            <div class="col-10">
                                <img src="{{ asset('storage/logo.png') }}" alt="Sinar Meadow Logo" class="logo">
                            </div>
                            <div class="col-2">
                                <p class="small text-end mb-0">
                                    FORM NO: FA-INV-05<br>
                                    REVISION: 3<br>
                                    DATE: 18 FEBRUARY 2021
                                </p>
                            </div>
                        </header>

                        <!-- Judul -->
                        <div class="row mb-4 text-center">
                            <h5><strong>REQUISITION SLIP - COMPLAIN</strong></h5>
                            <p class="mb-0">SALES & MARKETING<br>SAMPLE PRODUCT</p>
                        </div>

                        <!-- Informasi Requisition -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label"><strong>Customer Name:</strong></label>
                                    <input type="text" class="form-control" value="{{ $requisition->customer->name ?? '-' }}" readonly>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label"><strong>Customer Address:</strong></label>
                                    <textarea class="form-control" rows="2" readonly>{{ $requisition->customer->address ?? '-' }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label"><strong>Account:</strong></label>
                                    <input type="text" class="form-control" value="{{ $requisition->account }}" readonly>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label"><strong>Cost Center:</strong></label>
                                    <input type="text" class="form-control" value="{{ number_format($requisition->cost_center) }}" readonly>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label"><strong>Nomor RS:</strong></label>
                                    <input type="text" class="form-control" value="{{ $requisition->no_srs }}" readonly>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label"><strong>Request Date:</strong></label>
                                    <input type="text" class="form-control" value="{{ $requisition->request_date }}" readonly>
                                </div>
                            </div>
                        </div>

                        <!-- Objectives -->
                        @if($requisition->objectives)
                        <div class="mb-4">
                            <label class="form-label"><strong>Objectives:</strong></label>
                            <textarea class="form-control" rows="3" readonly>{{ $requisition->objectives }}</textarea>
                        </div>
                        @endif

                        <!-- Product Details -->
                        @if($requisition->requisitionItems && $requisition->requisitionItems->count() > 0)
                        <div class="mb-4">
                            <h6><strong>Product Details:</strong></h6>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Material Type</th>
                                            <th>Product Code</th>
                                            <th>Product Name</th>
                                            <th>Unit</th>
                                            <th>QTY Required</th>
                                            <th>QTY Issued</th>
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
                                                <td>{{ $item->quantity_required }}</td>
                                                <td>{{ $item->quantity_issued }}</td>
                                            </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endif

                        <!-- Current Status -->
                        <div class="mb-4">
                            <label class="form-label"><strong>Current Status:</strong></label>
                            <div>
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

                        <!-- Review Form -->
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Review & Decision</h6>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('complain.approval.process') }}" method="POST" id="reviewForm">
                                    @csrf
                                    <input type="hidden" name="token" value="{{ $token }}">
                                    <input type="hidden" name="id" value="{{ $requisition->id }}">
                                    
                                    <!-- Decision -->
                                    <div class="mb-3">
                                        <label class="form-label"><strong>Decision:</strong></label>
                                        <div class="d-flex gap-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="status" id="approve" value="approve" required>
                                                <label class="form-check-label text-success" for="approve">
                                                    <strong>Approve</strong>
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="status" id="reject" value="reject" required>
                                                <label class="form-check-label text-danger" for="reject">
                                                    <strong>Reject</strong>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Notes/Reason -->
                                    <div class="mb-3">
                                        <label for="notes" class="form-label"><strong>Notes/Reason:</strong></label>
                                        <textarea class="form-control" id="notes" name="notes" rows="4" 
                                                  placeholder="Please provide your reason for this decision (optional for approval, required for rejection)"></textarea>
                                        <div class="form-text">
                                            <span id="notes-help-text">Please provide a reason for your decision.</span>
                                        </div>
                                    </div>

                                    <!-- Submit Button -->
                                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                        <button type="submit" class="btn btn-primary" id="submitBtn">
                                            Submit Review
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
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
            form.addEventListener('submit', function(e) {
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
