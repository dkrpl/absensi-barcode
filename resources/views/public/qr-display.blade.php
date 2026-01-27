<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Absensi - Sistem Kafe</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    <style>
        :root {
            --primary: #1e3a8a;
            --primary-light: #3b82f6;
            --success: #10b981;
            --warning: #f59e0b;
        }

        * {
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            margin: 0;
            display: flex;
            flex-direction: column;
        }

        .outer-card {
            background: white;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .main-header {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            padding: 20px 30px;
            color: white;
            flex-shrink: 0;
        }

        .inner-card {
            background: #f8fafc;
            border-radius: 16px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(0, 0, 0, 0.05);
            overflow: hidden;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .card-header {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            padding: 15px 20px;
            color: white;
            flex-shrink: 0;
        }

        .card-body {
            padding: 20px;
            background: white;
            flex: 1;
            overflow-y: auto;
        }

        .status-badge {
            padding: 8px 20px;
            border-radius: 50px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            white-space: nowrap;
        }

        .status-active {
            background: linear-gradient(135deg, #10b981, #34d399);
            color: white;
            animation: pulse 2s infinite;
            box-shadow: 0 4px 20px rgba(16, 185, 129, 0.3);
        }

        .status-waiting {
            background: linear-gradient(135deg, #f59e0b, #fbbf24);
            color: white;
            box-shadow: 0 4px 20px rgba(245, 158, 11, 0.3);
        }

        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.7; }
            100% { opacity: 1; }
        }

        .timer {
            font-family: 'Courier New', monospace;
            font-size: 2.5rem;
            font-weight: 700;
            color: #1e3a8a;
            text-shadow: 0 2px 10px rgba(30, 58, 138, 0.2);
        }

        .qr-frame {
            padding: 15px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(30, 58, 138, 0.1);
            border: 2px solid rgba(30, 58, 138, 0.1);
            margin: 0 auto;
        }

        .info-item {
            background: #f8fafc;
            border-radius: 12px;
            padding: 12px;
            border-left: 4px solid #3b82f6;
            margin-bottom: 10px;
        }

        .refresh-indicator {
            width: 14px;
            height: 14px;
            border: 2px solid #10b981;
            border-top-color: transparent;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        #qrcode img, #qrcode canvas {
            border-radius: 10px;
            padding: 10px;
            background: white !important;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            max-width: 100%;
            height: auto;
            border: 10px solid white !important;
        }

        .main-content {
            padding: 20px;
            background: #f0f4ff;
            flex: 1;
            overflow-y: auto;
            max-height: calc(100vh - 120px);
        }

        /* Scrollbar styling */
        .main-content::-webkit-scrollbar,
        .card-body::-webkit-scrollbar {
            width: 6px;
        }

        .main-content::-webkit-scrollbar-track,
        .card-body::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .main-content::-webkit-scrollbar-thumb,
        .card-body::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 10px;
        }

        .main-content::-webkit-scrollbar-thumb:hover,
        .card-body::-webkit-scrollbar-thumb:hover {
            background: #a1a1a1;
        }

        /* Responsive adjustments */
        @media (max-height: 800px) {
            .main-header {
                padding: 15px 20px;
            }

            .main-content {
                padding: 15px;
            }

            .card-body {
                padding: 15px;
            }

            .timer {
                font-size: 2rem;
            }

            #qrcode img, #qrcode canvas {
                max-width: 200px;
                max-height: 200px;
            }
        }

        @media (max-height: 700px) {
            .main-header h1 {
                font-size: 1.5rem;
            }

            .main-header p {
                font-size: 0.9rem;
            }

            .card-header h2 {
                font-size: 1.1rem;
            }

            .timer {
                font-size: 1.8rem;
            }

            #qrcode img, #qrcode canvas {
                max-width: 180px;
                max-height: 180px;
            }

            /* Compact mode for small screens */
            .qr-top-section {
                margin-bottom: 10px;
            }

            .qr-top-section .inner-card {
                margin-bottom: 10px;
            }
        }

        /* NEW: Optimized layout for QR visibility */
        .qr-top-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
            align-items: stretch;
        }

        .qr-container-large {
            grid-column: span 1;
            height: 100%;
        }

        .timer-container {
            grid-column: span 1;
            height: 100%;
        }

        .info-grid-section {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .bottom-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        /* Ensure QR is always visible */
        .qr-priority {
            position: relative;
            z-index: 10;
        }

        /* Adjust QR size based on screen */
        .qr-responsive {
            max-width: 300px;
            margin: 0 auto;
        }

        @media (max-width: 1024px) {
            .qr-top-section,
            .info-grid-section,
            .bottom-section {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .qr-responsive {
                max-width: 250px;
            }
        }

        @media (max-height: 600px) {
            .main-content {
                padding: 10px;
            }

            .qr-top-section {
                gap: 10px;
                margin-bottom: 10px;
            }

            .card-header {
                padding: 12px 15px;
            }

            .card-body {
                padding: 15px;
            }

            .qr-responsive {
                max-width: 200px;
            }
        }

        .compact-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 8px;
        }

        .shift-item {
            padding: 12px;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            background: #f9fafb;
            font-size: 0.85rem;
        }

        .shift-item.active {
            border-color: #3b82f6;
            background: #eff6ff;
        }

        /* QR Code Optimization */
        .qr-optimized {
            background: white !important;
            padding: 15px !important;
            border: 15px solid white !important;
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="font-['Inter']">
    <!-- OUTER CARD (Card Utama) -->
    <div class="outer-card">
        <!-- Header Outer Card -->
        <div class="main-header">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex-1 min-w-0">
                    <h1 class="text-2xl font-bold text-white mb-2 truncate">
                        <i class="fas fa-qrcode mr-2"></i>SISTEM ABSENSI KAFE
                    </h1>
                    <p class="text-blue-100 text-sm truncate">
                        QR Code digenerate otomatis 1 jam sebelum shift dimulai
                    </p>
                </div>
                <div class="flex items-center gap-3 flex-shrink-0">
                    <div class="text-right">
                        <div class="text-blue-100 text-xs">Update Terakhir</div>
                        <div id="lastUpdate" class="font-bold text-white text-lg">{{ now()->format('H:i:s') }}</div>
                    </div>
                    <div class="flex flex-col items-center">
                        <div class="refresh-indicator"></div>
                        <span class="text-blue-100 text-xs mt-1">Auto</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Area - OPTIMIZED FOR QR VISIBILITY -->
        <div class="main-content">
            <!-- TOP SECTION: QR Code & Timer (ALWAYS VISIBLE) -->
            <div class="qr-top-section qr-priority">
                <!-- QR Code Card -->
                <div class="qr-container-large">
                    <div class="inner-card h-full">
                        <div class="card-header">
                            <h2 class="text-lg font-bold text-white">
                                <i class="fas fa-qrcode mr-2"></i>QR CODE ABSENSI
                            </h2>
                        </div>
                        <div class="card-body">
                            <div id="qrContainer" class="text-center h-full flex flex-col">
                                @if($activeBarcode)
                                    <!-- QR Code Active -->
                                    <div class="mb-4 flex-1 flex items-center justify-center">
                                        <div class="qr-frame qr-responsive">
                                            <div id="qrcode" class="qr-optimized"></div>
                                        </div>
                                    </div>

                                    <div class="space-y-3 mt-auto">
                                        <div class="grid grid-cols-2 gap-3">
                                            <div class="bg-blue-50 p-3 rounded-lg border-l-3 border-blue-500">
                                                <div class="flex items-center gap-2">
                                                    <i class="fas fa-play text-blue-600 text-sm"></i>
                                                    <div class="text-left">
                                                        <div class="text-xs text-gray-600">Mulai</div>
                                                        <div class="font-bold text-blue-700 text-sm" id="qrStartTime">
                                                            {{ $activeBarcode->waktu_mulai->format('H:i:s') }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="bg-red-50 p-3 rounded-lg border-l-3 border-red-500">
                                                <div class="flex items-center gap-2">
                                                    <i class="fas fa-stop text-red-600 text-sm"></i>
                                                    <div class="text-left">
                                                        <div class="text-xs text-gray-600">Kadaluarsa</div>
                                                        <div class="font-bold text-red-700 text-sm" id="qrExpiryTime">
                                                            {{ $activeBarcode->waktu_akhir->format('H:i:s') }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="bg-gray-50 p-3 rounded-lg">
                                            <div class="flex items-center gap-2 mb-2">
                                                <i class="fas fa-key text-gray-600 text-sm"></i>
                                                <div class="text-xs font-medium text-gray-700">Kode QR</div>
                                            </div>
                                            <div class="font-mono text-xs text-gray-800 bg-white p-2 rounded border truncate">
                                                {{ substr($activeBarcode->kode_barcode, 0, 30) }}...
                                            </div>
                                        </div>

                                        <!-- QR Code Test Buttons -->
                                        <div class="mt-4 space-y-2">
                                            <!-- Test Button -->
                                            <button onclick="testQRCode()"
                                                    class="w-full bg-yellow-100 hover:bg-yellow-200 text-yellow-800 font-medium py-2 px-4 rounded-lg transition text-sm">
                                                <i class="fas fa-vial mr-2"></i>Test QR Code
                                            </button>

                                            <!-- Copy Button -->
                                            <button onclick="copyQRData()"
                                                    class="w-full bg-blue-100 hover:bg-blue-200 text-blue-800 font-medium py-2 px-4 rounded-lg transition text-sm">
                                                <i class="fas fa-copy mr-2"></i>Salin Kode QR
                                            </button>

                                            <!-- QR Code Tips -->
                                            <div class="text-center mt-3">
                                                <div class="inline-flex items-center gap-1 bg-green-50 text-green-700 text-xs px-3 py-1 rounded-full">
                                                    <i class="fas fa-lightbulb"></i>
                                                    <span>Tips: QR Code hitam-putih untuk scanning optimal</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <!-- No QR Code -->
                                    <div class="py-4 px-4 flex flex-col items-center justify-center h-full">
                                        <div class="text-5xl mb-4 text-gray-300">
                                            <i class="fas fa-clock"></i>
                                        </div>
                                        <h4 class="text-xl font-bold text-gray-700 mb-3 text-center">
                                            QR Code Belum Tersedia
                                        </h4>
                                        <p class="text-gray-600 mb-4 text-center text-sm">
                                            QR Code akan muncul otomatis<br>
                                            1 jam sebelum shift dimulai
                                        </p>

                                        @if($currentShift)
                                            @php
                                                $shiftStart = \Carbon\Carbon::parse($currentShift->jam_mulai);
                                                $qrStartTime = $shiftStart->copy()->subHour();
                                            @endphp

                                            @if(now() < $qrStartTime)
                                                <div class="bg-blue-50 p-4 rounded-xl border-2 border-blue-200">
                                                    <div class="text-center">
                                                        <div class="text-xs text-gray-600 mb-2">QR akan muncul dalam:</div>
                                                        <div class="timer text-2xl text-blue-700 mb-2" id="timeUntilQR">
                                                            --:--:--
                                                        </div>
                                                        <div class="text-xs text-gray-500">
                                                            <i class="fas fa-calendar-alt mr-1"></i>
                                                            Pukul {{ $qrStartTime->format('H:i') }}
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Timer & Status Card -->
                <div class="timer-container">
                    <div class="inner-card h-full">
                        <div class="card-header">
                            <h2 class="text-lg font-bold text-white">
                                <i class="fas fa-hourglass-half mr-2"></i>STATUS & TIMER
                            </h2>
                        </div>
                        <div class="card-body flex flex-col">
                            <!-- Shift Info -->
                            <div class="mb-4">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="bg-blue-50 px-4 py-2 rounded-lg">
                                        <div class="text-xs text-gray-600">Shift</div>
                                        <div class="font-bold text-gray-800 truncate">
                                            {{ $currentShift->nama_shift ?? '--' }}
                                        </div>
                                    </div>
                                    <div id="statusBadge" class="status-badge {{ $activeBarcode ? 'status-active' : 'status-waiting' }}">
                                        @if($activeBarcode)
                                            <i class="fas fa-check-circle text-sm"></i>QR AKTIF
                                        @else
                                            <i class="fas fa-clock text-sm"></i>MENUNGGU
                                        @endif
                                    </div>
                                </div>

                                <div class="text-center">
                                    <div class="text-xs text-gray-600 mb-2">WAKTU SHIFT</div>
                                    <div class="font-bold text-gray-800">
                                        {{ $currentShift->jam_mulai ?? '--:--' }} - {{ $currentShift->jam_akhir ?? '--:--' }}
                                    </div>
                                </div>
                            </div>

                            <!-- Timer -->
                            <div class="flex-1 flex flex-col justify-center">
                                <div class="text-center py-4">
                                    @if($activeBarcode)
                                        <div class="timer mb-4" id="countdownTimer">
                                            00:00:00
                                        </div>
                                        <div class="text-gray-600 text-sm">
                                            <i class="fas fa-exclamation-triangle text-yellow-500 mr-1"></i>
                                            QR akan kadaluarsa otomatis
                                        </div>
                                    @else
                                        <div class="py-4">
                                            <div class="text-4xl text-gray-300 mb-4">
                                                <i class="fas fa-clock"></i>
                                            </div>
                                            <div class="text-gray-500 text-sm">
                                                Timer akan muncul saat QR Code aktif
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Current Time & Stats -->
                            <div class="mt-4 pt-4 border-t border-gray-100">
                                <div class="space-y-3">
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <div class="text-xs text-gray-500">WAKTU SEKARANG</div>
                                            <div id="currentTime" class="text-xl font-bold text-gray-800">
                                                00:00:00
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-xs text-gray-500">TANGGAL</div>
                                            <div id="currentDate" class="font-bold text-gray-800">
                                                {{ now()->format('d/m/Y') }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-3">
                                        <div class="text-center bg-blue-50 p-2 rounded-lg">
                                            <div class="text-lg font-bold text-blue-700" id="totalShifts">
                                                {{ $shifts->count() }}
                                            </div>
                                            <div class="text-xs text-gray-600">Total Shift</div>
                                        </div>
                                        <div class="text-center bg-green-50 p-2 rounded-lg">
                                            <div class="text-lg font-bold text-green-700" id="activeQR">
                                                {{ $activeBarcode ? 1 : 0 }}
                                            </div>
                                            <div class="text-xs text-gray-600">QR Aktif</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- MIDDLE SECTION: Instructions & System Info -->
            <div class="info-grid-section">
                <!-- Instructions Card -->
                <div>
                    <div class="inner-card">
                        <div class="card-header">
                            <h2 class="text-lg font-bold text-white">
                                <i class="fas fa-graduation-cap mr-2"></i>PETUNJUK PENGGUNAAN
                            </h2>
                        </div>
                        <div class="card-body">
                            <div class="space-y-2">
                                <div class="info-item">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-sm">
                                            1
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-gray-800 text-sm mb-1">Buka Kamera/Scanner</h4>
                                            <p class="text-gray-600 text-xs">Gunakan aplikasi kamera atau QR scanner</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="info-item">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-sm">
                                            2
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-gray-800 text-sm mb-1">Scan QR Code</h4>
                                            <p class="text-gray-600 text-xs">Arahkan kamera ke QR Code di layar</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="info-item">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-sm">
                                            3
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-gray-800 text-sm mb-1">Konfirmasi Absensi</h4>
                                            <p class="text-gray-600 text-xs">Ikuti petunjuk untuk konfirmasi</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="info-item">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-sm">
                                            4
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-gray-800 text-sm mb-1">Selesai</h4>
                                            <p class="text-gray-600 text-xs">Absensi telah tercatat di sistem</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- System Info Card -->
                <div>
                    <div class="inner-card">
                        <div class="card-header">
                            <h2 class="text-lg font-bold text-white">
                                <i class="fas fa-info-circle mr-2"></i>INFORMASI SISTEM
                            </h2>
                        </div>
                        <div class="card-body">
                            <div class="space-y-3">
                                <div class="flex items-center gap-3 p-3 bg-blue-50 rounded-lg">
                                    <i class="fas fa-sync-alt text-blue-600 text-lg"></i>
                                    <div class="flex-1">
                                        <div class="font-medium text-gray-800">Auto Generate</div>
                                        <div class="text-sm text-gray-600">QR digenerate otomatis</div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-3 p-3 bg-green-50 rounded-lg">
                                    <i class="fas fa-shield-alt text-green-600 text-lg"></i>
                                    <div class="flex-1">
                                        <div class="font-medium text-gray-800">Sistem Aman</div>
                                        <div class="text-sm text-gray-600">QR berlaku 1 jam sebelum shift</div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-3 p-3 bg-yellow-50 rounded-lg">
                                    <i class="fas fa-mobile-alt text-yellow-600 text-lg"></i>
                                    <div class="flex-1">
                                        <div class="font-medium text-gray-800">Multi Device</div>
                                        <div class="text-sm text-gray-600">Support semua perangkat</div>
                                    </div>
                                </div>

                                <div class="mt-4 pt-4 border-t border-gray-100">
                                    <div class="text-center">
                                        <div class="text-sm text-gray-600 mb-1">Status Koneksi:</div>
                                        <div id="connectionStatus" class="flex items-center justify-center gap-2">
                                            <i class="fas fa-circle text-green-500"></i>
                                            <span class="text-green-600 font-medium">Online</span>
                                        </div>
                                        <div class="text-xs text-gray-500 mt-2">
                                            <i class="fas fa-question-circle mr-1"></i>
                                            Bantuan: Hubungi Admin/Tim IT
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- BOTTOM SECTION: Shift Schedule -->
            <div class="bottom-section">
                <!-- Shift Schedule Card -->
                <div>
                    <div class="inner-card">
                        <div class="card-header">
                            <h2 class="text-lg font-bold text-white">
                                <i class="fas fa-calendar-alt mr-2"></i>JADWAL SHIFT
                            </h2>
                        </div>
                        <div class="card-body">
                            <div class="compact-grid">
                                @foreach($shifts as $shift)
                                    @php
                                        $shiftStart = \Carbon\Carbon::parse($shift->jam_mulai);
                                        $qrStartTime = $shiftStart->copy()->subHour();
                                        $isActive = $activeBarcode && $activeBarcode->id_shift == $shift->id;
                                    @endphp
                                    <div class="shift-item {{ $isActive ? 'active' : '' }}">
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="font-bold text-gray-800 text-sm truncate">{{ $shift->nama_shift }}</div>
                                            @if($isActive)
                                                <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">
                                                    Aktif
                                                </span>
                                            @endif
                                        </div>
                                        <div class="space-y-1">
                                            <div class="flex justify-between text-xs">
                                                <span class="text-gray-600">Jam:</span>
                                                <span class="font-medium">{{ substr($shift->jam_mulai, 0, 5) }} - {{ substr($shift->jam_akhir, 0, 5) }}</span>
                                            </div>
                                            <div class="flex justify-between text-xs">
                                                <span class="text-gray-600">QR Aktif:</span>
                                                <span class="font-medium text-blue-600">{{ $qrStartTime->format('H:i') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Info Card -->
                <div>
                    <div class="inner-card">
                        <div class="card-body flex flex-col justify-between h-full">
                            <div>
                                <div class="text-center mb-4">
                                    <div class="text-3xl text-blue-600 mb-2">
                                        <i class="fas fa-qrcode"></i>
                                    </div>
                                    <h3 class="font-bold text-gray-800 mb-2">Sistem Absensi Kafe</h3>
                                    <p class="text-gray-600 text-sm">
                                        Sistem absensi digital dengan QR Code untuk efisiensi dan keamanan
                                    </p>
                                </div>

                                <div class="space-y-2">
                                    <div class="flex items-center gap-2 p-2 bg-gray-50 rounded-lg">
                                        <i class="fas fa-code text-gray-600"></i>
                                        <div class="flex-1">
                                            <div class="text-xs font-medium text-gray-700">Versi Sistem</div>
                                            <div class="text-sm text-blue-600 font-bold">v2.0</div>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2 p-2 bg-gray-50 rounded-lg">
                                        <i class="fas fa-calendar text-gray-600"></i>
                                        <div class="flex-1">
                                            <div class="text-xs font-medium text-gray-700">Tahun</div>
                                            <div class="text-sm text-gray-800">{{ date('Y') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 pt-4 border-t border-gray-100 text-center">
                                <p class="text-gray-500 text-xs">
                                    <i class="fas fa-sync-alt mr-1"></i>Auto-refresh setiap 10 detik
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentQR = null;
        let refreshInterval = null;
        let countdownInterval = null;
        let timeUntilInterval = null;

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize based on server data
            @if($activeBarcode)
                generateQRCode('{{ $activeBarcode->kode_barcode }}');
                startCountdown('{{ $activeBarcode->waktu_akhir->format("Y-m-d H:i:s") }}');
                currentQR = '{{ $activeBarcode->kode_barcode }}';
            @elseif($currentShift)
                @php
                    $shiftStart = \Carbon\Carbon::parse($currentShift->jam_mulai);
                    $qrStartTime = $shiftStart->copy()->subHour();
                @endphp

                @if(now() < $qrStartTime)
                    startTimeUntilQR('{{ $qrStartTime->format("Y-m-d H:i:s") }}');
                @endif
            @endif

            // Start auto-refresh every 10 seconds
            startAutoRefresh();
            updateCurrentTime();

            // Adjust QR size based on screen height
            adjustLayout();
            window.addEventListener('resize', adjustLayout);

            // Ensure QR is visible on load
            ensureQRVisibility();
        });

        // Function to ensure QR Code is visible
        function ensureQRVisibility() {
            const qrContainer = document.getElementById('qrContainer');
            if (qrContainer) {
                // Scroll QR into view if needed
                qrContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });

                // Highlight QR section temporarily
                qrContainer.style.boxShadow = '0 0 0 3px rgba(59, 130, 246, 0.3)';
                setTimeout(() => {
                    qrContainer.style.boxShadow = '';
                }, 1500);
            }
        }

        // Adjust layout based on screen size
        function adjustLayout() {
            const screenHeight = window.innerHeight;
            const qrContainer = document.getElementById('qrcode');

            if (qrContainer) {
                let qrSize = 256; // Standard size for optimal scanning

                if (screenHeight < 700) {
                    qrSize = 200;
                } else if (screenHeight < 800) {
                    qrSize = 220;
                }

                // Update QR code size if it exists
                const qrElement = qrContainer.querySelector('img') || qrContainer.querySelector('canvas');
                if (qrElement) {
                    qrElement.style.width = qrSize + 'px';
                    qrElement.style.height = qrSize + 'px';
                }
            }

            // Adjust main content height
            const mainContent = document.querySelector('.main-content');
            const headerHeight = document.querySelector('.main-header').offsetHeight;
            if (mainContent) {
                mainContent.style.maxHeight = `calc(100vh - ${headerHeight}px)`;
            }
        }

        // ============================================
        // PERBAIKAN UTAMA: FUNGSI GENERATE QR CODE
        // ============================================
        function generateQRCode(qrData) {
            const container = document.getElementById('qrcode');
            if (!container) return;

            // Clear previous QR
            container.innerHTML = '';

            try {
                // STANDARD QR CODE SIZE untuk scanning yang optimal
                const qrSize = 256; // 256x256 pixels - ukuran optimal untuk scanning

                // Pastikan data QR adalah string dan tidak kosong
                if (!qrData || typeof qrData !== 'string') {
                    throw new Error('Data QR tidak valid');
                }

                console.log('Generating QR Code for:', qrData.substring(0, 50) + '...');

                // PERBAIKAN 1: Gunakan parameter yang optimal untuk scanning
                new QRCode(container, {
                    text: qrData,
                    width: qrSize,
                    height: qrSize,
                    // PERBAIKAN 2: Warna kontras tinggi untuk scanning yang mudah
                    colorDark: "#000000",  // HITAM PEKAT (bukan biru)
                    colorLight: "#ffffff", // PUTIH BERSIH
                    // PERBAIKAN 3: Error correction level yang lebih tinggi
                    correctLevel: QRCode.CorrectLevel.Q, // Q = 25% error correction
                    // PERBAIKAN 4: Tambah margin untuk scanning yang lebih baik
                    useSVG: false, // Gunakan canvas (lebih kompatibel)
                    margin: 4,     // Margin 4 modules (optimal untuk scanning)
                    // PERBAIKAN 5: Pastikan version otomatis
                    version: -1    // Auto version
                });

                // PERBAIKAN 6: Tambah styling untuk kontras yang lebih baik
                const qrImg = container.querySelector('img') || container.querySelector('canvas');
                if (qrImg) {
                    qrImg.style.border = '10px solid white'; // Padding putih
                    qrImg.style.boxShadow = '0 4px 12px rgba(0,0,0,0.15)'; // Bayangan untuk kontras
                    qrImg.style.borderRadius = '8px'; // Sedikit rounded corner
                    qrImg.style.maxWidth = '100%';
                    qrImg.style.height = 'auto';
                    qrImg.style.background = '#ffffff'; // Background putih
                }

                // PERBAIKAN 7: Log untuk debugging
                console.log('QR Code generated successfully');

                // PERBAIKAN 8: Highlight QR code untuk visibility
                setTimeout(() => {
                    if (qrImg) {
                        qrImg.style.outline = '3px solid #10b981';
                        qrImg.style.outlineOffset = '5px';
                        setTimeout(() => {
                            qrImg.style.outline = 'none';
                        }, 2000);
                    }
                }, 500);

            } catch (error) {
                console.error('Error generating QR:', error);
                container.innerHTML = `
                    <div class="text-center p-6 bg-gray-100 rounded-xl border-2 border-gray-300">
                        <div class="inline-block bg-red-100 p-3 rounded-full mb-3">
                            <i class="fas fa-exclamation-triangle text-red-600 text-3xl"></i>
                        </div>
                        <h4 class="text-red-700 font-bold text-lg mb-2">Gagal membuat QR Code</h4>
                        <div class="bg-white p-3 rounded-lg border mb-3">
                            <div class="text-xs text-gray-600 mb-1">Data QR:</div>
                            <div class="font-mono text-sm break-all text-gray-800">${qrData || 'Tidak ada data'}</div>
                        </div>
                        <button onclick="location.reload()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                            <i class="fas fa-redo mr-2"></i>Refresh Halaman
                        </button>
                    </div>
                `;
            }
        }

        // ============================================
        // FUNGSI TEST QR CODE
        // ============================================
        function testQRCode() {
            // Test dengan UUID valid
            const testUUID = '123e4567-e89b-12d3-a456-426614174000';
            console.log('Testing QR Code with UUID:', testUUID);

            // Clear current QR
            const container = document.getElementById('qrcode');
            if (container) container.innerHTML = '';

            // Generate test QR
            generateQRCode(testUUID);

            // Show test notification
            setTimeout(() => {
                alert('QR Code test berhasil digenerate. Coba scan dengan kamera.');
            }, 1000);
        }

        // ============================================
        // FUNGSI COPY QR DATA
        // ============================================
        function copyQRData() {
            @if($activeBarcode)
                const qrData = '{{ $activeBarcode->kode_barcode }}';
                navigator.clipboard.writeText(qrData).then(() => {
                    alert('Kode QR berhasil disalin: ' + qrData.substring(0, 20) + '...');
                }).catch(err => {
                    console.error('Copy failed:', err);
                });
            @endif
        }

        // Countdown for active QR
        function startCountdown(expiryTime) {
            clearInterval(countdownInterval);

            const timerElement = document.getElementById('countdownTimer');
            if (!timerElement) return;

            function update() {
                const now = new Date();
                const expiry = new Date(expiryTime);
                const diffMs = expiry - now;

                if (diffMs <= 0) {
                    timerElement.textContent = '00:00:00';
                    timerElement.classList.add('text-red-600');

                    // QR expired, refresh page after 2 seconds
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                    clearInterval(countdownInterval);
                    return;
                }

                const hours = Math.floor(diffMs / (1000 * 60 * 60));
                const minutes = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((diffMs % (1000 * 60)) / 1000);

                timerElement.textContent =
                    `${hours.toString().padStart(2, '0')}:` +
                    `${minutes.toString().padStart(2, '0')}:` +
                    `${seconds.toString().padStart(2, '0')}`;

                // Color warning when less than 5 minutes
                if (hours === 0 && minutes < 5) {
                    timerElement.classList.add('text-red-600');
                    timerElement.classList.remove('text-blue-700');
                } else {
                    timerElement.classList.remove('text-red-600');
                    timerElement.classList.add('text-blue-700');
                }
            }

            update();
            countdownInterval = setInterval(update, 1000);
        }

        // Time until QR appears
        function startTimeUntilQR(qrStartTime) {
            clearInterval(timeUntilInterval);

            const timerElement = document.getElementById('timeUntilQR');
            if (!timerElement) return;

            function update() {
                const now = new Date();
                const start = new Date(qrStartTime);
                const diffMs = start - now;

                if (diffMs <= 0) {
                    timerElement.textContent = '00:00:00';
                    // Time for QR, refresh page
                    setTimeout(() => location.reload(), 1000);
                    clearInterval(timeUntilInterval);
                    return;
                }

                const hours = Math.floor(diffMs / (1000 * 60 * 60));
                const minutes = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((diffMs % (1000 * 60)) / 1000);

                timerElement.textContent =
                    `${hours.toString().padStart(2, '0')}:` +
                    `${minutes.toString().padStart(2, '0')}:` +
                    `${seconds.toString().padStart(2, '0')}`;
            }

            update();
            timeUntilInterval = setInterval(update, 1000);
        }

        // Auto-refresh system
        function startAutoRefresh() {
            clearInterval(refreshInterval);

            refreshInterval = setInterval(async () => {
                try {
                    const response = await fetch('/api/qr-active');
                    const data = await response.json();

                    // Update last update time
                    const now = new Date();
                    const lastUpdateElement = document.getElementById('lastUpdate');
                    if (lastUpdateElement) {
                        lastUpdateElement.textContent =
                            now.toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit', second:'2-digit'});
                    }

                    if (data.success) {
                        // New QR code detected
                        if (currentQR !== data.data.kode_barcode) {
                            currentQR = data.data.kode_barcode;

                            // Update QR display
                            generateQRCode(data.data.kode_barcode);

                            // Update countdown
                            startCountdown(data.data.waktu_akhir);

                            // Update status badge
                            const statusBadge = document.getElementById('statusBadge');
                            if (statusBadge) {
                                statusBadge.className = 'status-badge status-active';
                                statusBadge.innerHTML = `
                                    <i class="fas fa-check-circle"></i>QR AKTIF
                                `;
                            }

                            // Update stats
                            const activeQRElement = document.getElementById('activeQR');
                            if (activeQRElement) {
                                activeQRElement.textContent = '1';
                            }

                            // Update connection status
                            const connectionElement = document.getElementById('connectionStatus');
                            if (connectionElement) {
                                connectionElement.innerHTML = `
                                    <i class="fas fa-circle text-green-500"></i>
                                    <span class="text-green-600 font-medium">Online</span>
                                `;
                            }
                        }
                    } else {
                        // No active QR
                        if (currentQR !== null) {
                            currentQR = null;

                            // Update status badge
                            const statusBadge = document.getElementById('statusBadge');
                            if (statusBadge) {
                                statusBadge.className = 'status-badge status-waiting';
                                statusBadge.innerHTML = `
                                    <i class="fas fa-clock"></i>MENUNGGU
                                `;
                            }

                            // Update stats
                            const activeQRElement = document.getElementById('activeQR');
                            if (activeQRElement) {
                                activeQRElement.textContent = '0';
                            }

                            // Stop countdown
                            clearInterval(countdownInterval);
                        }
                    }

                } catch (error) {
                    const connectionElement = document.getElementById('connectionStatus');
                    if (connectionElement) {
                        connectionElement.innerHTML = `
                            <i class="fas fa-circle text-red-500"></i>
                            <span class="text-red-600 font-medium">Offline</span>
                        `;
                    }
                }
            }, 10000); // 10 seconds
        }

        // Update current time
        function updateCurrentTime() {
            const now = new Date();

            // Format time
            const timeString = now.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false
            });

            // Format date
            const dateString = now.toLocaleDateString('id-ID', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });

            // Update time display
            const timeElement = document.getElementById('currentTime');
            const dateElement = document.getElementById('currentDate');

            if (timeElement) timeElement.textContent = timeString;
            if (dateElement) dateElement.textContent = dateString;

            setTimeout(updateCurrentTime, 1000);
        }

        // Handle page visibility change
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) {
                // Refresh immediately when page becomes visible
                if (refreshInterval) {
                    clearInterval(refreshInterval);
                }
                startAutoRefresh();
                updateCurrentTime();
                adjustLayout();
                ensureQRVisibility();
            }
        });
    </script>
</body>
</html>
