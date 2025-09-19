<!DOCTYPE html>
<html>
<head>
    <title>Status Persetujuan</title>
    <style>
        /* CSS sederhana */
        body { font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background-color: #f4f4f4; }
        .container { text-align: center; padding: 40px; background: white; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        h1 { color: #4CAF50; } /* Hijau untuk sukses, merah untuk invalid */
    </style>
</head>
<body>
    <div class="container">
        <h1>✓</h1>
        <h2>Terima Kasih!</h2>
        <p>{{ $message }}</p>
    </div>
</body>
</html>
