<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Halaman Tidak Ditemukan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .error-container {
            background: white;
            padding: 3rem;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 500px;
        }
        .error-code {
            font-size: 5rem;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-code">404</div>
        <h2>Halaman Tidak Ditemukan</h2>
        <p class="mb-4">Halaman yang Anda cari tidak ditemukan atau mungkin telah dipindahkan.</p>
        <a href="{{ url('/') }}" class="btn btn-primary">
            <i class="fas fa-home"></i> Kembali ke Dashboard
        </a>
    </div>

    <script src="https://kit.fontawesome.com/your-fontawesome-kit.js"></script>
</body>
</html>
