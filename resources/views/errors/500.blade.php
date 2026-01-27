<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Server Error</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
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
            color: #f5576c;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-code">500</div>
        <h2>Terjadi Kesalahan Server</h2>
        <p class="mb-4">Maaf, terjadi kesalahan pada server. Silahkan coba lagi nanti.</p>
        <a href="{{ url()->previous() }}" class="btn btn-primary mr-2">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
        <a href="{{ url('/') }}" class="btn btn-secondary">
            <i class="fas fa-home"></i> Ke Dashboard
        </a>
    </div>

    <script src="https://kit.fontawesome.com/your-fontawesome-kit.js"></script>
</body>
</html>
