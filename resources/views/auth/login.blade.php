<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Sistem Absensi QRCode</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700&display=swap" rel="stylesheet">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Nunito', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            width: 100%;
            max-width: 600px;
            animation: fadeIn 0.6s ease-out;
        }

        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .login-header {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            padding: 30px 20px;
            text-align: center;
            color: white;
        }

        .login-header-icon {
            font-size: 4rem;
            /* margin-bottom: 20px; */
            animation: bounce 2s infinite;
        }

        .login-header h1 {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .login-header p {
            opacity: 0.9;
            font-size: 1rem;
        }

        .login-body {
            padding: 40px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
        }

        .form-label i {
            margin-right: 10px;
            color: #4e73df;
        }

        .input-group {
            border-radius: 10px;
            overflow: hidden;
            border: 2px solid #e3e6f0;
            transition: all 0.3s;
        }

        .input-group:focus-within {
            border-color: #4e73df;
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }

        .input-group-text {
            background: #f8f9fc;
            border: none;
            padding: 15px;
            color: #6e707e;
        }

        .form-control {
            border: none;
            padding: 15px;
            font-size: 1rem;
        }

        .form-control:focus {
            box-shadow: none;
            outline: none;
        }

        .btn-login {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            color: white;
            border: none;
            padding: 15px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 10px;
            width: 100%;
            transition: all 0.3s;
            margin-top: 10px;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(78, 115, 223, 0.3);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .btn-login:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .login-footer {
            text-align: center;
            padding: 25px;
            background: #f8f9fc;
            border-top: 1px solid #e3e6f0;
        }

        .credential-info {
            background: #fff3cd;
            border: 1px solid #ffecb5;
            border-radius: 10px;
            padding: 15px;
            margin-top: 20px;
        }

        .credential-info h6 {
            color: #856404;
            margin-bottom: 10px;
        }

        .badge-login {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            margin: 3px;
            font-weight: 600;
        }

        .badge-admin {
            background: #4e73df;
            color: white;
        }

        .badge-karyawan {
            background: #1cc88a;
            color: white;
        }

        .copyright {
            color: #6e707e;
            font-size: 0.9rem;
            margin-top: 5px;
        }

        .alert {
            border-radius: 10px;
            border: none;
            padding: 15px;
            margin-bottom: 20px;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid #e74a3b;
        }

        .alert-danger i {
            color: #e74a3b;
            margin-right: 10px;
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        /* Responsive */
        @media (max-width: 576px) {
            .login-container {
                padding: 10px;
            }

            .login-body {
                padding: 30px 20px;
            }

            .login-header {
                padding: 30px 20px;
            }

            .login-header h1 {
                font-size: 1.8rem;
            }
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <!-- Header -->
            <div class="login-header">
                <div class="login-header-icon">
                    <i class="fas fa-qrcode"></i>
                </div>
                <h1>Absensi QRCode</h1>
                <p>Sistem Absensi dengan QR Code Scanning</p>
            </div>

            <!-- Body -->
            <div class="login-body">
                @if($errors->any() || session('error'))
                <div class="alert alert-danger" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-circle fa-lg"></i>
                        <div class="ms-3">
                            <strong>Login Gagal!</strong><br>
                            {{ $errors->first('username') ?: $errors->first('password') ?: session('error') ?: 'Username atau password salah' }}
                        </div>
                    </div>
                </div>
                @endif

                <form method="POST" action="{{ route('login.post') }}" id="loginForm">
                    @csrf

                    <div class="form-group">
                        <label for="username" class="form-label">
                            <i class="fas fa-user"></i>Username
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-user"></i>
                            </span>
                            <input type="text"
                                   class="form-control @error('username') is-invalid @enderror"
                                   id="username"
                                   name="username"
                                   placeholder="Masukkan username"
                                   value="{{ old('username') }}"
                                   required
                                   autofocus>
                        </div>
                        @error('username')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock"></i>Password
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input type="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   id="password"
                                   name="password"
                                   placeholder="Masukkan password"
                                   required>
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('password')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <button type="submit" class="btn-login" id="loginButton">
                        <span id="loginText">
                            <i class="fas fa-sign-in-alt me-2"></i>Login
                        </span>
                        <span id="loginLoading" class="d-none">
                            <span class="spinner-border spinner-border-sm me-2"></span>
                            Memproses...
                        </span>
                    </button>
                </form>

                <!-- Credential Info -->
                {{-- <div class="credential-info mt-4">
                    <h6><i class="fas fa-key me-2"></i>Kredensial Testing:</h6>
                    <div class="text-center">
                        <span class="badge-login badge-admin">
                            <i class="fas fa-user-shield me-1"></i>Admin: admin / admin123
                        </span>
                        <span class="badge-login badge-karyawan">
                            <i class="fas fa-user-tie me-1"></i>Karyawan: budi / karyawan123
                        </span>
                        <span class="badge-login badge-karyawan">
                            <i class="fas fa-user-tie me-1"></i>Karyawan: siti / karyawan123
                        </span>
                    </div>
                </div>
            </div> --}}

            <!-- Footer -->
            <div class="login-footer">
                <div class="copyright">
                    <i class="fas fa-copyright me-1"></i>
                    {{ date('Y') }} Sistem Absensi QRCode - Laravel 12
                    <div class="mt-2">
                        <small>Versi 1.0.0</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function() {
            // CSRF Token Setup
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Toggle password visibility
            $('#togglePassword').on('click', function() {
                const passwordInput = $('#password');
                const icon = $(this).find('i');

                if (passwordInput.attr('type') === 'password') {
                    passwordInput.attr('type', 'text');
                    icon.removeClass('fa-eye').addClass('fa-eye-slash');
                    $(this).addClass('active');
                } else {
                    passwordInput.attr('type', 'password');
                    icon.removeClass('fa-eye-slash').addClass('fa-eye');
                    $(this).removeClass('active');
                }
            });

            // Auto focus on username
            $('#username').focus();

            // Handle form submission
            $('#loginForm').on('submit', function(e) {
                const loginButton = $('#loginButton');
                const loginText = $('#loginText');
                const loginLoading = $('#loginLoading');

                // Show loading state
                loginText.addClass('d-none');
                loginLoading.removeClass('d-none');
                loginButton.prop('disabled', true);

                // Hide any existing error alerts
                $('.alert').fadeOut();
            });

            // Auto hide error alert after 5 seconds
            setTimeout(function() {
                $('.alert').fadeOut();
            }, 5000);

            // Enter key to submit form
            $(document).on('keypress', function(e) {
                if (e.which === 13) { // Enter key
                    if ($('#username').is(':focus') || $('#password').is(':focus')) {
                        $('#loginForm').submit();
                    }
                }
            });

            // Show welcome animation
            setTimeout(function() {
                $('.login-header-icon').css('animation', 'bounce 2s infinite');
            }, 1000);

            // Check if there's an error and shake the form
            @if($errors->any() || session('error'))
            setTimeout(function() {
                $('.login-card').css('animation', 'shake 0.5s');
            }, 500);

            // Add shake animation
            const style = document.createElement('style');
            style.textContent = `
                @keyframes shake {
                    0%, 100% { transform: translateX(0); }
                    10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
                    20%, 40%, 60%, 80% { transform: translateX(5px); }
                }
            `;
            document.head.appendChild(style);
            @endif
        });

        // If there's a session error from redirect (like unauthorized access)
        @if(session('error'))
        $(document).ready(function() {
            Swal.fire({
                icon: 'warning',
                title: 'Akses Ditolak',
                text: '{{ session("error") }}',
                timer: 3000,
                showConfirmButton: false
            });
        });
        @endif

        // Prevent resubmission on page refresh
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
</body>
</html>
