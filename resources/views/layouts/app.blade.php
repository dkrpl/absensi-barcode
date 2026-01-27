<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Absensi QRCode')</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700&display=swap" rel="stylesheet">

    <!-- Custom Styles -->
    <style>
        :root {
            --primary: #4e73df;
            --primary-dark: #2e59d9;
            --success: #1cc88a;
            --warning: #f6c23e;
            --danger: #e74a3b;
            --info: #36b9cc;
            --gray-100: #f8f9fc;
            --gray-200: #e3e6f0;
            --gray-600: #6e707e;
            --gray-800: #5a5c69;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Nunito', sans-serif;
            background-color: var(--gray-100);
            color: #333;
            min-height: 100vh;
        }

        /* ========== SIDEBAR ========== */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 250px;
            background: linear-gradient(180deg, var(--primary) 10%, var(--primary-dark) 100%);
            color: white;
            z-index: 1000;
            overflow-y: auto;
            transition: all 0.3s ease;
        }

        .sidebar-brand {
            padding: 1.5rem 1rem;
            text-align: center;
            background: rgba(0, 0, 0, 0.1);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-brand-icon {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
            opacity: 0.9;
        }

        .sidebar-brand-text {
            font-size: 1.3rem;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .sidebar-nav {
            padding: 1.5rem 0.75rem;
        }

        .nav-item {
            margin-bottom: 0.5rem;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.85);
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .nav-link:hover {
            color: white;
            background: rgba(255, 255, 255, 0.15);
            transform: translateX(5px);
        }

        .nav-link.active {
            color: white;
            background: rgba(255, 255, 255, 0.2);
            font-weight: 600;
        }

        .nav-link i {
            width: 20px;
            text-align: center;
            margin-right: 12px;
            font-size: 1.1rem;
        }

        .sidebar-divider {
            margin: 1.5rem 0;
            border-color: rgba(255, 255, 255, 0.15);
            opacity: 0.5;
        }

        /* ========== CONTENT WRAPPER ========== */
        #content-wrapper {
            margin-left: 250px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: margin-left 0.3s ease;
        }

        /* ========== TOPBAR ========== */
        .topbar {
            background: white;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
            position: sticky;
            top: 0;
            z-index: 999;
            border-bottom: 1px solid var(--gray-200);
        }

        .topbar-content {
            padding: 1rem 2rem;
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--gray-800);
            margin: 0;
        }

        .current-date {
            color: var(--gray-600);
            font-size: 0.95rem;
        }

        /* User Dropdown */
        .user-dropdown {
            position: relative;
        }

        .user-dropdown-toggle {
            background: transparent;
            border: none;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
        }

        .user-dropdown-toggle:hover {
            background: var(--gray-100);
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 1.1rem;
        }

        .user-info {
            text-align: right;
            line-height: 1.3;
        }

        .user-name {
            font-weight: 600;
            color: var(--gray-800);
            font-size: 0.95rem;
        }

        .user-role {
            font-size: 0.85rem;
            color: var(--gray-600);
        }

        .dropdown-menu {
            border: none;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
            border-radius: 0.5rem;
            padding: 0.5rem;
            min-width: 200px;
        }

        .dropdown-item {
            padding: 0.75rem 1rem;
            border-radius: 0.375rem;
            display: flex;
            align-items: center;
            color: var(--gray-800);
            transition: all 0.3s ease;
        }

        .dropdown-item i {
            width: 20px;
            color: var(--gray-600);
        }

        .dropdown-item:hover {
            background: var(--gray-100);
            color: var(--primary);
        }

        .dropdown-item:hover i {
            color: var(--primary);
        }

        /* ========== MAIN CONTENT ========== */
        #content {
            flex: 1;
            padding: 2rem;
        }

        /* ========== FOOTER ========== */
        .footer {
            background: white;
            border-top: 1px solid var(--gray-200);
            padding: 1.5rem 2rem;
            margin-top: auto;
        }

        .copyright {
            color: var(--gray-600);
            font-size: 0.9rem;
        }

        /* ========== MOBILE RESPONSIVE ========== */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            #content-wrapper {
                margin-left: 0;
            }

            .topbar-content {
                padding: 1rem;
            }

            #content {
                padding: 1.5rem 1rem;
            }

            .mobile-toggle {
                display: block !important;
            }
        }

        /* Mobile Toggle Button */
        .mobile-toggle {
            display: none;
            position: fixed;
            top: 1rem;
            left: 1rem;
            z-index: 1001;
            width: 40px;
            height: 40px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 0.5rem;
            font-size: 1.2rem;
            cursor: pointer;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        /* ========== ALERTS ========== */
        .alert {
            border: none;
            border-radius: 0.5rem;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
        }

        .alert-success {
            background: rgba(28, 200, 138, 0.1);
            color: var(--success);
            border-left: 4px solid var(--success);
        }

        .alert-danger {
            background: rgba(231, 74, 59, 0.1);
            color: var(--danger);
            border-left: 4px solid var(--danger);
        }

        /* ========== UTILITIES ========== */
        .text-primary { color: var(--primary) !important; }
        .text-success { color: var(--success) !important; }
        .text-warning { color: var(--warning) !important; }
        .text-danger { color: var(--danger) !important; }
        .text-info { color: var(--info) !important; }

        .bg-primary { background-color: var(--primary) !important; }
        .bg-success { background-color: var(--success) !important; }
        .bg-warning { background-color: var(--warning) !important; }
        .bg-danger { background-color: var(--danger) !important; }
        .bg-info { background-color: var(--info) !important; }

        /* ========== SCROLLBAR ========== */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: var(--gray-100);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--gray-200);
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--gray-600);
        }

        /* ========== ANIMATIONS ========== */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .fade-in {
            animation: fadeIn 0.3s ease-out;
        }
    </style>

    @stack('styles')
</head>
<body id="page-top">

    @if(Auth::check())
    <!-- Mobile Toggle Button -->
    <button class="mobile-toggle" id="sidebarToggleMobile">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="sidebar-brand-icon">
                <i class="fas fa-qrcode"></i>
            </div>
            <div class="sidebar-brand-text">
                Absensi QRCode
            </div>
        </div>

        <hr class="sidebar-divider">

        <div class="sidebar-nav">
            @if(Auth::user()->role == 'admin')
            <!-- Admin Menu -->
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                       href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-fw fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.data-karyawan') ? 'active' : '' }}"
                       href="{{ route('admin.data-karyawan') }}">
                        <i class="fas fa-fw fa-users"></i>
                        <span>Data Karyawan</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.data-shift') ? 'active' : '' }}"
                       href="{{ route('admin.data-shift') }}">
                        <i class="fas fa-fw fa-clock"></i>
                        <span>Data Shift</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.generate-barcode') ? 'active' : '' }}"
                       href="{{ route('admin.generate-barcode') }}">
                        <i class="fas fa-fw fa-qrcode"></i>
                        <span>Generate QR Code</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.laporan-absensi') ? 'active' : '' }}"
                       href="{{ route('admin.laporan-absensi') }}">
                        <i class="fas fa-fw fa-file-alt"></i>
                        <span>Laporan Absensi</span>
                    </a>
                </li>
            </ul>
            @else
            <!-- Karyawan Menu -->
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('karyawan.dashboard') ? 'active' : '' }}"
                       href="{{ route('karyawan.dashboard') }}">
                        <i class="fas fa-fw fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('karyawan.scan-barcode') ? 'active' : '' }}"
                       href="{{ route('karyawan.scan-barcode') }}">
                        <i class="fas fa-fw fa-camera"></i>
                        <span>Scan QR Code</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('karyawan.riwayat-absensi') ? 'active' : '' }}"
                       href="{{ route('karyawan.riwayat-absensi') }}">
                        <i class="fas fa-fw fa-history"></i>
                        <span>Riwayat Absensi</span>
                    </a>
                </li>
            </ul>
            @endif
        </div>

        <div class="sidebar-footer mt-auto p-3 text-center">
            <small style="opacity: 0.7;">© {{ date('Y') }} Absensi QRCode</small>
        </div>
    </nav>
    @endif

    <!-- Content Wrapper -->
    <div id="content-wrapper">
        @if(Auth::check())
        <!-- Topbar -->
        <nav class="topbar fade-in">
            <div class="topbar-content container-fluid d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <h4 class="page-title mb-0">
                        @yield('page-title', 'Dashboard')
                    </h4>
                </div>

                <div class="d-flex align-items-center gap-3">
                    <span class="current-date d-none d-md-block">
                        <i class="fas fa-calendar-alt me-2"></i>
                        {{ now()->translatedFormat('l, d F Y') }}
                    </span>

                    <!-- User Dropdown -->
                    <div class="user-dropdown">
                        <button class="user-dropdown-toggle"
                                type="button"
                                id="userDropdown"
                                data-bs-toggle="dropdown"
                                aria-expanded="false">
                            <div class="user-info d-none d-md-block">
                                <div class="user-name">{{ Auth::user()->nama }}</div>
                                <div class="user-role">{{ ucfirst(Auth::user()->role) }}</div>
                            </div>
                            <div class="user-avatar">
                                {{ substr(Auth::user()->nama, 0, 1) }}
                            </div>
                        </button>

                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-user fa-sm"></i>
                                    <span class="ms-2">Profile</span>
                                </a>
                            </li>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}" class="mb-0">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="fas fa-sign-out-alt fa-sm"></i>
                                        <span class="ms-2">Logout</span>
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>
        @endif

        <!-- Main Content -->
        <main id="content" class="fade-in">
            <!-- Flash Messages -->
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fas fa-check-circle me-3 fa-lg"></i>
                    <div class="flex-grow-1">{{ session('success') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-circle me-3 fa-lg"></i>
                    <div class="flex-grow-1">{{ session('error') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
            @endif

            <!-- Page Content -->
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="footer">
            <div class="container-fluid">
                <div class="row align-items-center justify-content-between">
                    <div class="col-md-6">
                        <div class="copyright">
                            &copy; {{ date('Y') }} Sistem Absensi QRCode - Laravel 12
                        </div>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <div class="copyright">
                            <small>Versi 1.0.0</small>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- QR Code Scanner -->
    <script src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>

    <!-- QR Code Generator -->
    <script src="https://cdn.jsdelivr.net/npm/qrcode-generator@1.4.4/qrcode.min.js"></script>

    <!-- Main Script -->
    <script>
        $(document).ready(function() {
            // CSRF Token Setup
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Auto dismiss alerts after 5 seconds
            setTimeout(function() {
                $('.alert').alert('close');
            }, 5000);

            // Mobile sidebar toggle
            $('#sidebarToggleMobile').on('click', function() {
                $('#sidebar').toggleClass('show');
            });

            // Close sidebar when clicking outside on mobile
            $(document).on('click', function(event) {
                if ($(window).width() < 768) {
                    if (!$(event.target).closest('#sidebar, #sidebarToggleMobile').length) {
                        $('#sidebar').removeClass('show');
                    }
                }
            });

            // Close sidebar when menu item clicked (mobile)
            $('.nav-link').on('click', function() {
                if ($(window).width() < 768) {
                    $('#sidebar').removeClass('show');
                }
            });

            // Update current date in topbar
            function updateCurrentDate() {
                const now = new Date();
                const options = {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                };
                const dateString = now.toLocaleDateString('id-ID', options);
                $('.current-date').html('<i class="fas fa-calendar-alt me-2"></i>' + dateString);
            }

            // Update date every hour
            setInterval(updateCurrentDate, 3600000);

            // Global error handler for AJAX
            $(document).ajaxError(function(event, jqxhr, settings, thrownError) {
                if (jqxhr.status === 419) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Session Expired',
                        text: 'Sesi Anda telah berakhir. Silakan login kembali.',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.reload();
                    });
                }
            });

            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Prevent form double submission
            $('form').on('submit', function() {
                $(this).find('button[type="submit"]').prop('disabled', true);
            });
        });

        // Window resize handler
        $(window).on('resize', function() {
            if ($(window).width() >= 768) {
                $('#sidebar').removeClass('show');
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
