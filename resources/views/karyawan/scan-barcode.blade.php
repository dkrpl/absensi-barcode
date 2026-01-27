@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Scan QR Code Absensi</h1>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-camera"></i> Scanner QR Code
                    </h6>
                </div>
                <div class="card-body text-center">

                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle"></i>
                        Pilih kamera dan arahkan ke QR Code yang tersedia di lokasi absensi.
                        QR Code hanya aktif pada waktu tertentu.
                    </div>

                    <!-- Camera Selection -->
                    <div class="form-group text-left">
                        <label for="cameraSelect">Pilih Kamera:</label>
                        <select id="cameraSelect" class="form-control mb-3">
                            <option value="">Loading kamera...</option>
                        </select>
                    </div>

                    <!-- Scanner Container -->
                    <div class="mb-4">
                        <div id="reader" class="rounded" style="width: 100%; min-height: 300px; margin: 0 auto; border: 1px solid #ddd;"></div>
                    </div>

                    <!-- Manual Input (Fallback) -->
                    <div class="card border-left-warning mb-4">
                        <div class="card-body">
                            <h6 class="card-title text-warning">
                                <i class="fas fa-keyboard"></i> Input Manual
                            </h6>
                            <p class="card-text">Jika scanner tidak bekerja, masukkan kode QR manual:</p>
                            <div class="input-group">
                                <input type="text" id="manualCode" class="form-control" placeholder="Masukkan kode QR (format UUID)">
                                <div class="input-group-append">
                                    <button class="btn btn-warning" onclick="submitManualCode()">
                                        Submit
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Result Display -->
                    <div id="result" class="mt-4" style="display: none;">
                        <div class="alert" id="resultAlert">
                            <h4 id="resultMessage"></h4>
                            <div id="resultDetails"></div>
                        </div>
                        <button onclick="resetScanner()" class="btn btn-primary">
                            <i class="fas fa-redo"></i> Scan Lagi
                        </button>
                    </div>

                    <!-- Loading -->
                    <div id="loading" class="text-center" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p class="mt-2">Memproses absensi...</p>
                    </div>

                </div>
                <div class="card-footer text-muted">
                    <small>
                        <i class="fas fa-exclamation-triangle"></i>
                        Pastikan QR Code berada dalam area kotak scanner dan pencahayaan cukup
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- HTML5 QR Code Library -->
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
let html5QrcodeScanner = null;
let isScanning = false;
let lastScanTime = 0;
const SCAN_COOLDOWN = 3000; // 3 seconds between scans

$(document).ready(function() {
    console.log('Document ready, initializing scanner...');
    initCameraSelection();
});

async function initCameraSelection() {
    console.log('Initializing camera selection...');
    try {
        const devices = await Html5Qrcode.getCameras();
        const cameraSelect = $('#cameraSelect');
        cameraSelect.empty();

        if (devices && devices.length > 0) {
            console.log('Found cameras:', devices.length);

            devices.forEach((device, index) => {
                const label = device.label || `Kamera ${index + 1}`;
                cameraSelect.append(
                    `<option value="${device.id}">${label}</option>`
                );
            });

            // Start scanner with selected camera
            cameraSelect.on('change', function() {
                const cameraId = $(this).val();
                console.log('Camera changed to:', cameraId);
                if (cameraId) {
                    startScanner(cameraId);
                }
            });

            // Start with first camera
            if (devices[0].id) {
                cameraSelect.val(devices[0].id);
                setTimeout(() => {
                    startScanner(devices[0].id);
                }, 500);
            }
        } else {
            console.error('No cameras found');
            cameraSelect.append('<option value="">Kamera tidak ditemukan</option>');
            showCameraError();
        }
    } catch (error) {
        console.error('Error getting cameras:', error);
        showCameraError();
    }
}

function startScanner(cameraId) {
    console.log('Starting scanner with camera:', cameraId);
    if (!cameraId) {
        console.error('No camera ID provided');
        return;
    }

    // Stop existing scanner
    if (html5QrcodeScanner && isScanning) {
        console.log('Stopping existing scanner...');
        html5QrcodeScanner.stop().then(() => {
            console.log('Scanner stopped successfully');
            isScanning = false;
            initNewScanner(cameraId);
        }).catch(err => {
            console.error('Error stopping scanner:', err);
            initNewScanner(cameraId);
        });
    } else {
        initNewScanner(cameraId);
    }
}

function initNewScanner(cameraId) {
    console.log('Initializing new scanner...');

    // Clear previous scanner
    $('#reader').empty();

    // Create new scanner instance
    html5QrcodeScanner = new Html5Qrcode("reader");

    const config = {
        fps: 10,
        qrbox: { width: 250, height: 250 },
        aspectRatio: 1.333333,
        disableFlip: false
    };

    console.log('Starting scanner with config:', config);

    html5QrcodeScanner.start(
        cameraId,
        config,
        onScanSuccess,
        onScanError
    ).then(() => {
        isScanning = true;
        console.log('Scanner started successfully');
    }).catch(err => {
        console.error('Error starting scanner with config:', err);

        // Try simpler config
        console.log('Trying simpler configuration...');
        const simpleConfig = { fps: 5, qrbox: 200 };

        html5QrcodeScanner.start(
            cameraId,
            simpleConfig,
            onScanSuccess,
            onScanError
        ).then(() => {
            isScanning = true;
            console.log('Scanner started with simple config');
        }).catch(simpleErr => {
            console.error('Simple config also failed:', simpleErr);
            showCameraError();
        });
    });
}

function onScanSuccess(decodedText, decodedResult) {
    console.log('QR Code detected:', decodedText.substring(0, 50) + '...');

    // Prevent multiple scans in quick succession
    const now = Date.now();
    if (now - lastScanTime < SCAN_COOLDOWN) {
        console.log('Scan cooldown active, skipping...');
        return;
    }
    lastScanTime = now;

    // Add visual feedback
    const scannerElement = document.getElementById('reader');
    scannerElement.style.boxShadow = '0 0 0 3px #28a745 inset';
    scannerElement.style.transition = 'box-shadow 0.3s ease';
    setTimeout(() => {
        scannerElement.style.boxShadow = 'none';
    }, 500);

    // Play beep sound
    playBeepSound();

    // Process QR code
    console.log('Processing QR code...');
    processQRCode(decodedText);
}

function onScanError(error) {
    // Ignore common scanning errors
    if (error.includes('NotFoundException') ||
        error.includes('No MultiFormat Readers') ||
        error.includes('No QR code found')) {
        return;
    }
    console.warn('Scan error:', error);
}

function playBeepSound() {
    try {
        // Try Web Audio API first
        const audioContext = new (window.AudioContext || window.webkitAudioContext)();
        const oscillator = audioContext.createOscillator();
        const gainNode = audioContext.createGain();

        oscillator.connect(gainNode);
        gainNode.connect(audioContext.destination);

        oscillator.frequency.value = 800;
        oscillator.type = 'sine';

        gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
        gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.2);

        oscillator.start(audioContext.currentTime);
        oscillator.stop(audioContext.currentTime + 0.2);
    } catch (e) {
        console.log('Web Audio API not supported, trying fallback');
        try {
            // Fallback to HTML5 Audio
            const audio = new Audio('data:audio/wav;base64,UklGRnoAAABXQVZFZm10IBIAAAABAAEAQB8AAEAfAAABAAgAZGF0YQ');
            audio.volume = 0.3;
            audio.play();
        } catch (e2) {
            console.log('Audio not supported');
        }
    }
}

function processQRCode(code) {
    console.log('Processing QR Code:', code);

    // Show loading
    $('#loading').show();
    $('#result').hide();

    // Stop scanner temporarily
    if (isScanning) {
        const scannerElement = document.getElementById('reader');
        scannerElement.style.opacity = '0.5';
        scannerElement.style.pointerEvents = 'none';
    }

    // Validate UUID format
    const uuidRegex = /^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i;
    if (!uuidRegex.test(code)) {
        console.error('Invalid UUID format:', code);
        showErrorAlert('Format QR Code tidak valid', 'Pastikan Anda scan QR Code yang benar dari sistem absensi.');
        $('#loading').hide();

        // Re-enable scanner
        if (isScanning) {
            const scannerElement = document.getElementById('reader');
            scannerElement.style.opacity = '1';
            scannerElement.style.pointerEvents = 'auto';
        }
        return;
    }

    // Send to server
    console.log('Sending AJAX request to server...');
    $.ajax({
        url: '{{ route("karyawan.proses-absensi") }}',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            kode_barcode: code
        },
        timeout: 30000, // 30 seconds timeout
        beforeSend: function() {
            console.log('AJAX request sending...');
        },
        success: function(response) {
            console.log('Server response:', response);
            if (response.success) {
                showSuccessAlert(response.message, response.data);
                // Stop scanner after successful scan
                stopScanner();
            } else {
                showErrorAlert('Absensi Gagal', response.message);
                // Re-enable scanner on error
                if (!isScanning) {
                    const cameraId = $('#cameraSelect').val();
                    if (cameraId) {
                        setTimeout(() => startScanner(cameraId), 2000);
                    }
                }
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', {
                status: xhr.status,
                statusText: xhr.statusText,
                error: error,
                responseText: xhr.responseText
            });

            let message = 'Terjadi kesalahan koneksi. Coba lagi.';
            if (xhr.status === 429) {
                message = 'Terlalu banyak percobaan. Tunggu 1 menit.';
            } else if (xhr.status === 422) {
                message = 'Data tidak valid.';
            } else if (xhr.status === 419) {
                message = 'Sesi telah berakhir. Silakan refresh halaman.';
            } else if (xhr.status === 404) {
                message = 'Endpoint tidak ditemukan.';
            } else if (xhr.status === 500) {
                message = 'Terjadi kesalahan server.';
            } else if (xhr.status === 0) {
                message = 'Tidak ada koneksi internet. Periksa jaringan Anda.';
            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            }

            showErrorAlert('Terjadi Kesalahan', message);

            // Re-enable scanner
            if (!isScanning) {
                const cameraId = $('#cameraSelect').val();
                if (cameraId) {
                    setTimeout(() => startScanner(cameraId), 2000);
                }
            }
        },
        complete: function() {
            console.log('AJAX request complete');
            $('#loading').hide();

            // Re-enable scanner UI
            const scannerElement = document.getElementById('reader');
            if (scannerElement) {
                scannerElement.style.opacity = '1';
                scannerElement.style.pointerEvents = 'auto';
            }
        }
    });
}

function showSuccessAlert(message, data) {
    console.log('Showing success alert with data:', data);

    // Determine icon and title based on status
    let icon = 'success';
    let title = 'Absensi Berhasil!';
    let statusText = 'Hadir';
    let statusClass = 'success';
    let terlambatText = '';
    let showLateWarning = false;

    if (data) {
        if (data.status === 'terlambat') {
            icon = 'warning';
            title = 'Absensi Terekam (Terlambat)';
            statusText = 'Terlambat';
            statusClass = 'warning';
            terlambatText = `${data.menit_terlambat} menit`;
            showLateWarning = true;
        } else if (data.status === 'pulang') {
            title = 'Absensi Pulang Berhasil!';
            statusText = 'Pulang';
        }
    }

    // Build HTML content
    let htmlContent = `<div class="text-left">
        <p class="mb-3">${message}</p>`;

    if (data) {
        htmlContent += `
        <div class="bg-light p-3 rounded mb-3">
            <div class="row mb-2">
                <div class="col-6"><strong><i class="fas fa-clock"></i> Waktu:</strong></div>
                <div class="col-6 text-right font-weight-bold">${data.waktu || '-'}</div>
            </div>
            <div class="row mb-2">
                <div class="col-6"><strong><i class="fas fa-tag"></i> Status:</strong></div>
                <div class="col-6 text-right">
                    <span class="badge badge-${statusClass}">${statusText}</span>
                </div>
            </div>`;

        if (showLateWarning) {
            htmlContent += `
            <div class="row">
                <div class="col-6"><strong><i class="fas fa-exclamation-triangle"></i> Keterlambatan:</strong></div>
                <div class="col-6 text-right text-warning font-weight-bold">${terlambatText}</div>
            </div>`;
        }

        if (data.shift) {
            htmlContent += `
            <div class="row">
                <div class="col-6"><strong><i class="fas fa-exchange-alt"></i> Shift:</strong></div>
                <div class="col-6 text-right">${data.shift}</div>
            </div>`;
        }

        htmlContent += `</div>`;
    }

    htmlContent += `<small class="text-muted"><i class="fas fa-info-circle"></i> Scanner akan dimulai ulang dalam 5 detik</small></div>`;

    Swal.fire({
        title: title,
        html: htmlContent,
        icon: icon,
        showConfirmButton: true,
        confirmButtonText: 'OK',
        confirmButtonColor: '#3085d6',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showClass: {
            popup: 'animate__animated animate__fadeInDown'
        },
        hideClass: {
            popup: 'animate__animated animate__fadeOutUp'
        },
        timer: showLateWarning ? 8000 : 5000,
        timerProgressBar: true,
        willClose: () => {
            // Restart scanner after alert closes
            resetScanner();
        }
    }).then((result) => {
        if (result.dismiss === Swal.DismissReason.timer) {
            // Restart scanner when timer runs out
            resetScanner();
        }
    });
}

function showErrorAlert(title, message) {
    console.log('Showing error alert:', title, message);

    Swal.fire({
        title: title,
        text: message,
        icon: 'error',
        confirmButtonText: 'Coba Lagi',
        confirmButtonColor: '#d33',
        showCancelButton: true,
        cancelButtonText: 'Tutup',
        showClass: {
            popup: 'animate__animated animate__shakeX'
        },
        allowOutsideClick: false
    }).then((result) => {
        if (result.isConfirmed) {
            // Reset scanner on retry
            resetScanner();
        }
    });
}

function stopScanner() {
    console.log('Stopping scanner...');
    if (html5QrcodeScanner && isScanning) {
        html5QrcodeScanner.stop().then(() => {
            isScanning = false;
            console.log('Scanner stopped successfully');
        }).catch(err => {
            console.error('Error stopping scanner:', err);
        });
    }
}

function showResult(success, message, data = null) {
    console.log('Showing result:', { success, message, data });

    const resultDiv = $('#result');
    const resultAlert = $('#resultAlert');
    const messageDiv = $('#resultMessage');
    const detailsDiv = $('#resultDetails');

    // Reset classes
    resultAlert.removeClass('alert-success alert-danger alert-warning alert-info');

    if (success) {
        resultAlert.addClass('alert-success');
        messageDiv.html(`<i class="fas fa-check-circle"></i> ${message}`);

        if (data) {
            const statusClass = data.status === 'hadir' ? 'badge-success' : 'badge-warning';
            const statusText = data.status === 'hadir' ? 'Hadir' : 'Terlambat';
            const terlambatText = data.menit_terlambat > 0 ? ` (${data.menit_terlambat} menit terlambat)` : '';

            detailsDiv.html(`
                <div class="mt-3 p-3 bg-light rounded">
                    <p class="mb-2">
                        <strong><i class="fas fa-clock"></i> Waktu Absensi:</strong>
                        <span class="float-right">${data.waktu}</span>
                    </p>
                    <p class="mb-2">
                        <strong><i class="fas fa-tag"></i> Status:</strong>
                        <span class="float-right">
                            <span class="badge ${statusClass}">${statusText}${terlambatText}</span>
                        </span>
                    </p>
                    <p class="mb-0">
                        <strong><i class="fas fa-exchange-alt"></i> Shift:</strong>
                        <span class="float-right">${data.shift}</span>
                    </p>
                </div>
            `);
        }
    } else {
        resultAlert.addClass('alert-danger');
        messageDiv.html(`<i class="fas fa-exclamation-circle"></i> ${message}`);
        detailsDiv.empty();
    }

    resultDiv.show();
}

function submitManualCode() {
    const code = $('#manualCode').val().trim();
    console.log('Manual code submission:', code);

    if (code) {
        processQRCode(code);
        $('#manualCode').val('');
    } else {
        Swal.fire({
            icon: 'warning',
            title: 'Kode Kosong',
            text: 'Masukkan kode QR terlebih dahulu',
            confirmButtonText: 'OK'
        });
    }
}

function resetScanner() {
    console.log('Resetting scanner...');
    $('#result').hide();
    $('#manualCode').val('');

    const cameraId = $('#cameraSelect').val();
    if (cameraId) {
        // Add small delay before restarting
        setTimeout(() => {
            startScanner(cameraId);
        }, 100);
    }
}

function showCameraError() {
    console.error('Showing camera error');
    $('#reader').html(`
        <div class="alert alert-danger text-left">
            <h5><i class="fas fa-video-slash fa-lg mb-2"></i> Kamera Tidak Dapat Diakses</h5>
            <p class="mb-3">Beberapa kemungkinan penyebab dan solusi:</p>
            <ol class="pl-3 mb-0">
                <li class="mb-2">Berikan izin akses kamera di browser Anda</li>
                <li class="mb-2">Pastikan kamera tidak sedang digunakan aplikasi lain</li>
                <li class="mb-2">Coba gunakan browser lain (Chrome/Firefox rekomendasi)</li>
                <li class="mb-2">Periksa pengaturan privasi browser Anda</li>
                <li class="mb-2">Pastikan situs ini menggunakan HTTPS (diperlukan untuk akses kamera)</li>
                <li>Gunakan fitur input manual di bawah</li>
            </ol>
        </div>
    `);
}

// Handle page visibility change
document.addEventListener('visibilitychange', function() {
    console.log('Visibility changed:', document.visibilityState);

    if (document.visibilityState === 'visible') {
        // Restart scanner when page becomes visible
        console.log('Page visible, restarting scanner...');
        const cameraId = $('#cameraSelect').val();
        if (cameraId && !isScanning) {
            setTimeout(() => {
                console.log('Delayed scanner restart...');
                startScanner(cameraId);
            }, 1000);
        }
    } else {
        // Stop scanner when page is not visible
        console.log('Page not visible, stopping scanner...');
        stopScanner();
    }
});

// Handle page unload
window.addEventListener('beforeunload', function() {
    console.log('Page unloading, cleaning up...');
    stopScanner();
});

// Handle window resize
let resizeTimer;
window.addEventListener('resize', function() {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(function() {
        if (isScanning) {
            console.log('Window resized, restarting scanner...');
            const cameraId = $('#cameraSelect').val();
            if (cameraId) {
                startScanner(cameraId);
            }
        }
    }, 250);
});
</script>

<style>
/* Add animate.css for animations (optional) */
@import url('https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css');

#reader {
    position: relative;
    overflow: hidden;
    background: #000;
    margin-bottom: 20px;
    border-radius: 8px;
    transition: all 0.3s ease;
}

#reader video {
    width: 100%;
    height: auto;
    display: block;
}

/* SweetAlert2 Customizations */
.swal2-popup {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    border-radius: 12px !important;
}

.swal2-title {
    font-size: 1.5rem !important;
    font-weight: 600 !important;
}

.swal2-icon.swal2-success {
    border-color: #28a745 !important;
    color: #28a745 !important;
}

.swal2-icon.swal2-warning {
    border-color: #ffc107 !important;
    color: #ffc107 !important;
}

.swal2-icon.swal2-error {
    border-color: #dc3545 !important;
    color: #dc3545 !important;
}

/* HTML5 QR Code Scanner custom styles */
.html5-qrcode-element {
    display: none !important;
}

#html5-qrcode-anchor-scan-type-change {
    display: none !important;
}

#reader__scan_region {
    position: relative;
}

#reader__scan_region img {
    display: none !important;
}

#reader__dashboard {
    padding: 10px;
    background: #f8f9fa;
    border-top: 1px solid #dee2e6;
}

#reader__camera_permission_button {
    background: #4e73df;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 4px;
    cursor: pointer;
    font-weight: 500;
    transition: background 0.3s;
}

#reader__camera_permission_button:hover {
    background: #2e59d9;
    text-decoration: none;
}

/* Loading animation */
.spinner-border {
    width: 3rem;
    height: 3rem;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    #reader {
        min-height: 250px;
    }

    .card-body {
        padding: 15px;
    }

    .swal2-popup {
        width: 90% !important;
        font-size: 0.9rem !important;
    }
}

@media (max-width: 576px) {
    #reader {
        min-height: 200px;
    }

    .input-group {
        flex-direction: column;
    }

    .input-group-append {
        margin-top: 10px;
        width: 100%;
    }

    .input-group-append .btn {
        width: 100%;
    }
}

/* Animation for scanner */
@keyframes scanLine {
    0% { top: 0%; }
    100% { top: 100%; }
}

.scan-line {
    position: absolute;
    left: 10%;
    width: 80%;
    height: 2px;
    background: rgba(46, 204, 113, 0.8);
    animation: scanLine 2s ease-in-out infinite;
    z-index: 10;
}

/* Corner borders */
.corner-border {
    position: absolute;
    width: 30px;
    height: 30px;
    border-color: #28a745;
    border-style: solid;
    z-index: 10;
}

.corner-top-left {
    top: 10px;
    left: 10px;
    border-width: 3px 0 0 3px;
}

.corner-top-right {
    top: 10px;
    right: 10px;
    border-width: 3px 3px 0 0;
}

.corner-bottom-left {
    bottom: 10px;
    left: 10px;
    border-width: 0 0 3px 3px;
}

.corner-bottom-right {
    bottom: 10px;
    right: 10px;
    border-width: 0 3px 3px 0;
}

/* Result animation */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

#result {
    animation: fadeIn 0.5s ease-out;
}

/* Alert customization */
.alert {
    border-radius: 8px;
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.alert-success {
    background-color: #d4edda;
    border-left: 4px solid #28a745;
    color: #155724;
}

.alert-danger {
    background-color: #f8d7da;
    border-left: 4px solid #dc3545;
    color: #721c24;
}

/* Badge customization */
.badge {
    padding: 5px 10px;
    border-radius: 20px;
    font-weight: 500;
}

.badge-success {
    background-color: #28a745;
}

.badge-warning {
    background-color: #ffc107;
    color: #212529;
}
</style>
@endpush
@endsection
