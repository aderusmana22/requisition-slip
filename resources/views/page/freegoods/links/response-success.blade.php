<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approval Response</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');

        body {
            background-color: #f0f2f5;
            font-family: 'Poppins', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .response-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            text-align: center;
            padding: 50px 50px 40px;
            max-width: 650px;
            width: 100%;
            border-top: 8px solid;
        }

        .icon-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            font-size: 36px;
            color: #fff;
        }

        .response-card.success { border-color: #cc982f; }
        .response-card.success .icon-circle { background-color: #cc982f; }
        .response-card.reject { border-color: #dc3545; }
        .response-card.reject .icon-circle { background-color: #dc3545; }

        h3 { font-weight: 600; color: #333; margin-bottom: 15px; }
        .message { color: #6c757d; margin-bottom: 40px; }

        .details-box {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            text-align: left;
            margin-bottom: 35px;
            border: 1px solid #e9ecef;
        }

        .detail-item {
            display: flex; justify-content: space-between; align-items: start;
            padding: 10px 0; border-bottom: 1px dashed #dee2e6;
        }
        .detail-item:last-child { border-bottom: none; }
        .detail-label { font-weight: 600; color: #495057; white-space: nowrap; margin-right: 20px; }
        .detail-value { color: #212529; text-align: right; word-break: break-word; }
        .countdown-text { font-size: 0.9em; color: #6c757d; margin-top: 25px; }
        
        .btn-primary { background-color: #cc982f; border-color: #cc982f; }
        .btn-primary:hover { background-color: #b8871a; border-color: #b8871a; }
    </style>
</head>

<body>
    <div class="response-card {{ session('card_class', 'success') }}">
        <div class="icon-circle">
            <i class="fas {{ session('card_class') === 'reject' ? 'fa-times-circle' : 'fa-check-circle' }}"></i>
        </div>

        <h3>{{ session('title', 'Action Submitted') }}</h3>
        <p class="message">{{ session('message', 'Your response has been recorded.') }}</p>

        {{-- [REVISI] Hanya menampilkan jika session customer_name ada --}}
        @if(session('customer_name')) 
        <div class="details-box">
            {{-- FG No sudah dihapus dari sini --}}
            
            {{-- [REVISI] Label Recipient --}}
            <div class="detail-item">
                <span class="detail-label">Recipient:</span>
                <span class="detail-value">{{ session('customer_name') }}</span>
            </div>
            
            @if(session('action_text') && session('approver_name'))
            <div class="detail-item">
                <span class="detail-label">Approver:</span>
                <span class="detail-value">{{ session('action_text') }} by {{ session('approver_name') }}</span>
            </div>
            @endif
            @if(session('new_status'))
            <div class="detail-item">
                <span class="detail-label">New Status:</span>
                <span class="detail-value">{{ session('new_status') }}</span>
            </div>
            @endif
        </div>
        @endif

        <a href="javascript:window.close();" class="btn btn-primary">Close</a>
        <p class="countdown-text">This page will close automatically in <span id="countdown">5</span> seconds.</p>
    </div>

    <script>
        let seconds = 5;
        const countdownElement = document.getElementById('countdown');
        const interval = setInterval(() => {
            seconds--;
            countdownElement.textContent = seconds;
            if (seconds <= 0) {
                clearInterval(interval);
                window.close();
            }
        }, 1000);
    </script>
</body>
</html>