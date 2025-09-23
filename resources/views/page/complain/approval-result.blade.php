<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approval Confirmation - {{ config('app.name') }}</title>
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
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header text-center">
                        <h4 class="mb-0">Approval Confirmation</h4>
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-4">
                            @if($status === 'approve')
                                <div class="text-success">
                                    <i class="fas fa-check-circle fa-3x mb-3"></i>
                                    <h5>Approval Successful!</h5>
                                    <p class="text-muted">The requisition has been approved successfully.</p>
                                </div>
                            @else
                                <div class="text-danger">
                                    <i class="fas fa-times-circle fa-3x mb-3"></i>
                                    <h5>Rejection Successful!</h5>
                                    <p class="text-muted">The requisition has been rejected successfully.</p>
                                </div>
                            @endif
                        </div>
                        
                        <div class="alert alert-info">
                            <strong>Requisition Number:</strong> {{ $requisition->no_srs ?? 'N/A' }}<br>
                            <strong>Customer:</strong> {{ $requisition->customer->name ?? 'N/A' }}<br>
                            <strong>Status:</strong> 
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

                        <p class="text-muted small">
                            This action has been recorded in the system. No further action is required from you.
                        </p>

                        <div class="mt-3">
                            <div class="alert alert-warning" id="auto-close-message">
                                <i class="fas fa-clock"></i> <span id="countdown">This page will automatically close in 5 seconds...</span>
                            </div>
                        </div>
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