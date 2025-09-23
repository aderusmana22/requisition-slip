<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invalid Approval Link - {{ config('app.name') }}</title>
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
                        <h4 class="mb-0">Invalid Approval Link</h4>
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-4">
                            <div class="text-danger">
                                <i class="fas fa-times-circle icon-large"></i>
                                <h5>Invalid or Malformed Link</h5>
                                <p class="text-muted">{{ $message ?? 'The approval link you are trying to access is invalid or malformed.' }}</p>
                            </div>
                        </div>
                        
                        <div class="alert alert-danger">
                            <strong>Error Details:</strong><br>
                            @if(isset($errorType) && $errorType === 'missing_params')
                                <i class="fas fa-exclamation-triangle"></i> Missing required parameters (ID or Token) in the approval link.
                            @elseif(isset($errorType) && $errorType === 'token_not_found')
                                <i class="fas fa-history"></i> The approval token was not found in our system. This link may have been used previously or is invalid.
                                @if(isset($lastActionDate))
                                    <br><strong>Last action performed on:</strong> {{ $lastActionDate }}
                                @endif
                            @else
                                <i class="fas fa-question-circle"></i> Unknown error occurred while processing the approval link.
                            @endif
                        </div>

                        <div class="alert alert-info">
                            <strong>What does this mean?</strong><br>
                            @if(isset($errorType) && $errorType === 'missing_params')
                                The link you clicked is incomplete or corrupted. Essential information (ID or security token) is missing from the URL.
                            @elseif(isset($errorType) && $errorType === 'token_not_found')
                                This approval link has likely been used before or the token has been cleared from our system for security reasons.
                            @else
                                There was an unexpected issue with the approval link.
                            @endif
                        </div>

                        <div class="mt-3">
                            <div class="alert alert-warning" id="auto-close-message">
                                <i class="fas fa-clock"></i> <span id="countdown">This page will automatically close in 5 seconds...</span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <h6><strong>Suggested Actions:</strong></h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-envelope text-primary"></i> Check your email for the correct approval link</li>
                                <li><i class="fas fa-refresh text-success"></i> Request a new approval link if needed</li>
                                <li><i class="fas fa-phone text-info"></i> Contact the sender if the issue persists</li>
                            </ul>
                        </div>

                        <p class="text-muted small">
                            If you continue to experience issues, please contact the system administrator or the person who sent you this approval request.
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