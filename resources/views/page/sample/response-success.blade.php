<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approval Response Submitted</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f7fc;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .result-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
            padding: 40px;
            text-align: center;
            max-width: 550px;
            width: 90%;
            border-top: 5px solid;
        }
        .result-card.success { border-color: #28a745; }
        .result-card.review { border-color: #007bff; }
        .result-card.reject { border-color: #dc3545; }

        .result-icon {
            width: 70px; height: 70px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 20px;
            color: white; font-size: 32px;
        }
        .result-icon.success { background-color: #28a745; }
        .result-icon.review { background-color: #007bff; }
        .result-icon.reject { background-color: #dc3545; }

        .result-title { font-size: 2rem; font-weight: 600; margin-bottom: 10px; }
        .result-message { font-size: 1rem; color: #6c757d; margin-bottom: 30px; }

        .info-summary {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 20px;
            text-align: left;
        }
        .info-summary div { margin-bottom: 8px; }
        .info-summary div:last-child { margin-bottom: 0; }
        .info-summary strong { color: #495057; }

        .countdown-text { margin-top: 25px; font-size: 0.9em; color: #6c757d; }
    </style>
</head>
<body>
    <div class="result-card {{ session('card_class') }}">
        <div class="result-icon {{ session('card_class') }}">
            @if(session('action') === 'reject')
                &#10006;
            @else
                &#10004;
            @endif
        </div>

        <h1 class="result-title">{{ session('title') }}</h1>
        <p class="result-message">{{ session('message') }}</p>

        <div class="info-summary">
            <div><strong>SRS Number:</strong> <span>{{ session('no_srs') }}</span></div>
            <div><strong>Customer:</strong> <span>{{ session('customer_name') }}</span></div>
            <div><strong>Action Taken:</strong> <span>{{ session('action_text') }} by {{ session('approver_name') }}</span></div>
            <div><strong>New Status:</strong> <span>{{ session('new_status') }}</span></div>
        </div>

        <p class="countdown-text">Anda dapat menutup halaman ini. Halaman akan tertutup otomatis dalam <span id="countdown">10</span> detik.</p>
        <button class="btn btn-primary" onclick="window.close()">Close</button>
    </div>

    <script>
        let countdown = 10;
        const countdownElement = document.getElementById('countdown');
        const countdownInterval = setInterval(() => {
            countdown--;
            if (countdown > 0) {
                countdownElement.textContent = countdown;
            } else {
                clearInterval(countdownInterval);
                window.close();
            }
        }, 1000);
    </script>
</body>
</html>
