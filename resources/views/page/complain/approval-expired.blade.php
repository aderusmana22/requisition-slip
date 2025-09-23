<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approval Link Expired - {{ config('app.name') }}</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card-header {
            background-color: #dc3545;
            color: white;
        }
        .icon-large {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header text-center">
                        <h4 class="mb-0">Approval Link Status</h4>
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-4">
                            <div class="text-warning">
                                <i class="fas fa-exclamation-triangle icon-large"></i>
                                <h5>Approval Link Already Processed</h5>
                                <p class="text-muted">This approval link has already been used and is no longer valid.</p>
                            </div>
                        </div>
                        
                        <div class="alert alert-info">
                            <strong>Requisition Number:</strong> {{ $requisition->no_srs ?? 'N/A' }}<br>
                            <strong>Customer:</strong> {{ $requisition->customer->name ?? 'N/A' }}<br>
                            <strong>Current Status:</strong> 
                            @if($requisition->status == 'Approved')
                                <span class="badge bg-success">Approved</span>
                            @elseif($requisition->status == 'Rejected')
                                <span class="badge bg-danger">Rejected</span>
                            @elseif($requisition->status == 'In Progress')
                                <span class="badge bg-info">In Progress</span>
                            @else
                                <span class="badge bg-secondary">{{ $requisition->status }}</span>
                            @endif
                        </div>

                        @if($approvalLog)
                        <div class="alert alert-warning">
                            <strong>Processing Details:</strong><br>
                            <strong>Action:</strong> 
                            @if($approvalLog->status == 'Approved')
                                <span class="text-success">✓ Approved</span>
                            @elseif($approvalLog->status == 'Rejected')
                                <span class="text-danger">✗ Rejected</span>
                            @else
                                <span class="text-secondary">{{ $approvalLog->status }}</span>
                            @endif
                            <br>
                            <strong>Processed At:</strong> {{ $approvalLog->updated_at ? \Carbon\Carbon::parse($approvalLog->updated_at)->setTimezone('Asia/Jakarta')->format('d M Y, H:i:s') . ' WIB' : 'N/A' }}<br>
                            @if($approvalLog->notes)
                                <strong>Notes:</strong> {{ $approvalLog->notes }}
                            @endif
                        </div>
                        @endif

                        <div class="mt-3">
                            <div class="alert alert-warning" id="auto-close-message">
                                <i class="fas fa-clock"></i> <span id="countdown">This page will automatically close in 5 seconds...</span>
                            </div>
                        </div>

                        <p class="text-muted small">
                            If you believe this is an error, please contact the system administrator or the person who sent you this approval request.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Font Awesome for icons -->
    <script src="https://kit.fontawesome.com/your-kit-id.js" crossorigin="anonymous"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto close page after 5 seconds with countdown
            let countdown = 5;
            const countdownElement = document.getElementById('countdown');
            
            const countdownTimer = setInterval(() => {
                countdown--;
                if (countdown > 0) {
                    countdownElement.innerHTML = `<i class="fas fa-clock"></i> This page will automatically close in ${countdown} seconds...`;
                } else {
                    countdownElement.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Closing now...';
                    clearInterval(countdownTimer);
                    
                    // Try to close the window/tab after a short delay
                    setTimeout(() => {
                        if (window.opener) {
                            // If opened in popup, close it
                            window.close();
                        } else {
                            // If in main window, try to go back or redirect
                            if (window.history.length > 1) {
                                window.history.back();
                            } else {
                                window.location.href = 'about:blank';
                            }
                        }
                    }, 500);
                }
            }, 1000);

            // Optional: Allow user to cancel auto-close
            document.addEventListener('click', function() {
                // If user clicks anywhere, stop the auto-close
                if (countdown > 0) {
                    clearInterval(countdownTimer);
                    const alertElement = document.getElementById('auto-close-message');
                    alertElement.className = 'alert alert-info';
                    countdownElement.innerHTML = '<i class="fas fa-hand-pointer"></i> Auto-close cancelled. You can manually close this page.';
                }
            });

            // Also cancel on any key press
            document.addEventListener('keydown', function() {
                if (countdown > 0) {
                    clearInterval(countdownTimer);
                    const alertElement = document.getElementById('auto-close-message');
                    alertElement.className = 'alert alert-info';
                    countdownElement.innerHTML = '<i class="fas fa-hand-pointer"></i> Auto-close cancelled. You can manually close this page.';
                }
            });
        });
    </script>
</body>
</html>