<!DOCTYPE html>
<html>
<head>
    <title>Tolak Permintaan</title>
     <style>
        /* ... CSS yang sama ... */
        textarea { width: 100%; padding: 10px; border-radius: 4px; border: 1px solid #ccc; margin-top: 20px; }
        button { background: #f44336; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Tolak Requisition</h1>
        <p>Mohon berikan alasan penolakan pada kolom di bawah ini.</p>
        <form action="{{ route('requisition.reject.submit', ['token' => $token]) }}" method="POST">
            @csrf
            <textarea name="notes" rows="4" placeholder="Alasan penolakan..." required></textarea>
            <br><br>
            <button type="submit">Submit Penolakan</button>
        </form>
    </div>
</body>
</html>
