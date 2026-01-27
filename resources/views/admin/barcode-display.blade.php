@extends('layouts.app')

@section('title', 'QR Code Display')
@section('page-title', 'QR Code Display')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-qrcode me-2"></i>QR Code Absensi
                    </h5>
                    <a href="{{ route('admin.generate-barcode') }}" class="btn btn-sm btn-light">
                        <i class="fas fa-arrow-left me-1"></i>Kembali
                    </a>
                </div>
            </div>
            <div class="card-body text-center">

                <!-- Countdown Alert -->
                <div class="alert alert-info mb-4">
                    <div class="d-flex align-items-center justify-content-center">
                        <i class="fas fa-clock fa-2x me-3"></i>
                        <div>
                            <h6 class="mb-1">QR Code aktif sampai:</h6>
                            <div class="d-flex align-items-center">
                                <span class="fw-bold fs-5 me-2">{{ $barcode->waktu_akhir->format('H:i:s') }}</span>
                                <span class="badge bg-primary fs-6" id="countdown"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- QR Code Display -->
                <div class="mb-4 p-4 border rounded bg-white">
                    <div id="qrcode" class="mb-3"></div>
                    <div class="text-muted small">
                        Scan QR Code ini menggunakan aplikasi scanner
                    </div>
                </div>

                <!-- Information Card -->
                <div class="card mb-4">
                    <div class="card-body text-start">
                        <h6 class="card-title mb-3">
                            <i class="fas fa-info-circle me-2"></i>Informasi QR Code
                        </h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="text-muted">Shift</div>
                                <div class="fw-bold">{{ $barcode->shift->nama_shift }}</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="text-muted">Jam Shift</div>
                                <div class="fw-bold">{{ $barcode->shift->jam_mulai }} - {{ $barcode->shift->jam_akhir }}</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="text-muted">Batas Telat</div>
                                <div class="fw-bold">{{ $barcode->shift->batas_telat }} menit</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="text-muted">Waktu Mulai</div>
                                <div class="fw-bold">{{ $barcode->waktu_mulai->format('d/m/Y H:i:s') }}</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="text-muted">Waktu Akhir</div>
                                <div class="fw-bold">{{ $barcode->waktu_akhir->format('d/m/Y H:i:s') }}</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="text-muted">Status</div>
                                <div>
                                    @if($barcode->isActive())
                                    <span class="badge bg-success">AKTIF</span>
                                    @else
                                    <span class="badge bg-danger">NONAKTIF</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="text-muted">Kode Unik</div>
                                <div class="fw-bold">
                                    <code>{{ $barcode->kode_barcode }}</code>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Instructions -->
                <div class="alert alert-warning">
                    <div class="d-flex">
                        <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
                        <div>
                            <h6 class="alert-heading">Petunjuk Penggunaan</h6>
                            <ul class="mb-0 ps-3">
                                <li>Letakkan QR Code ini di lokasi yang mudah di-scan oleh karyawan</li>
                                <li>QR Code akan otomatis non-aktif setelah waktu berakhir</li>
                                <li>Satu QR Code hanya untuk satu shift tertentu</li>
                                <li>Karyawan hanya bisa scan sekali per hari per shift</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-grid gap-2 d-md-flex justify-content-md-center mt-4">
                    <button onclick="printQRCode()" class="btn btn-primary me-md-2">
                        <i class="fas fa-print me-2"></i>Print QR Code
                    </button>
                    <button onclick="refreshPage()" class="btn btn-secondary">
                        <i class="fas fa-redo me-2"></i>Refresh
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/qrcode-generator@1.4.4/qrcode.min.js"></script>
<script>
$(document).ready(function() {
    // Generate QR Code
    generateQRCode('{{ $barcode->kode_barcode }}');

    // Start countdown
    startCountdown();
});

function generateQRCode(text) {
    $('#qrcode').empty();

    try {
        var qr = qrcode(0, 'M');
        qr.addData(text);
        qr.make();

        var img = document.createElement('img');
        img.src = qr.createDataURL(10, 0);
        img.className = 'img-fluid';
        img.style.maxWidth = '300px';
        img.style.border = '10px solid white';
        img.style.boxShadow = '0 0 20px rgba(0,0,0,0.1)';

        document.getElementById('qrcode').appendChild(img);
    } catch (error) {
        console.error('Error generating QR Code:', error);
        $('#qrcode').html(`
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Gagal generate QR Code. Error: ${error.message}
            </div>
        `);
    }
}

function startCountdown() {
    var endTime = new Date('{{ $barcode->waktu_akhir }}').getTime();

    function update() {
        var now = new Date().getTime();
        var distance = endTime - now;

        if (distance < 0) {
            $('#countdown').html('<span class="text-danger">EXPIRED</span>');

            // Auto refresh page when expired
            setTimeout(function() {
                window.location.reload();
            }, 5000);

            return;
        }

        var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        var seconds = Math.floor((distance % (1000 * 60)) / 1000);

        $('#countdown').text(
            hours.toString().padStart(2, '0') + ":" +
            minutes.toString().padStart(2, '0') + ":" +
            seconds.toString().padStart(2, '0')
        );

        setTimeout(update, 1000);
    }

    update();
}

function printQRCode() {
    var printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>QR Code Absensi - {{ $barcode->shift->nama_shift }}</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    text-align: center;
                    padding: 20px;
                    margin: 0;
                }
                .header {
                    margin-bottom: 30px;
                }
                .header h1 {
                    color: #4e73df;
                    margin-bottom: 10px;
                }
                .qr-container {
                    margin: 20px 0;
                    padding: 20px;
                    border: 2px solid #333;
                    display: inline-block;
                }
                .info {
                    text-align: left;
                    margin: 30px auto;
                    max-width: 500px;
                    padding: 20px;
                    border: 1px solid #ddd;
                    border-radius: 5px;
                }
                .info h3 {
                    color: #333;
                    border-bottom: 2px solid #4e73df;
                    padding-bottom: 10px;
                    margin-bottom: 20px;
                }
                .info-item {
                    margin-bottom: 10px;
                    display: flex;
                }
                .label {
                    font-weight: bold;
                    min-width: 150px;
                }
                .footer {
                    margin-top: 30px;
                    font-size: 12px;
                    color: #666;
                    border-top: 1px solid #ddd;
                    padding-top: 10px;
                }
                @media print {
                    @page { margin: 0; }
                    body { padding: 10px; }
                    .no-print { display: none; }
                }
            </style>
            <script src="https://cdn.jsdelivr.net/npm/qrcode-generator@1.4.4/qrcode.min.js"><\/script>
        </head>
        <body>
            <div class="header">
                <h1>QR Code Absensi</h1>
                <h2>Shift: {{ $barcode->shift->nama_shift }}</h2>
                <p>Tanggal: {{ $barcode->waktu_mulai->format('d/m/Y') }}</p>
            </div>

            <div class="qr-container">
                <div id="printQr"></div>
            </div>

            <div class="info">
                <h3>Informasi QR Code</h3>
                <div class="info-item">
                    <span class="label">Shift:</span>
                    <span>{{ $barcode->shift->nama_shift }}</span>
                </div>
                <div class="info-item">
                    <span class="label">Jam Shift:</span>
                    <span>{{ $barcode->shift->jam_mulai }} - {{ $barcode->shift->jam_akhir }}</span>
                </div>
                <div class="info-item">
                    <span class="label">Batas Telat:</span>
                    <span>{{ $barcode->shift->batas_telat }} menit</span>
                </div>
                <div class="info-item">
                    <span class="label">Waktu Aktif:</span>
                    <span>{{ $barcode->waktu_mulai->format('H:i') }} - {{ $barcode->waktu_akhir->format('H:i') }}</span>
                </div>
                <div class="info-item">
                    <span class="label">Kode Unik:</span>
                    <span>{{ $barcode->kode_barcode }}</span>
                </div>
            </div>

            <div class="footer">
                <p>Scan QR code ini untuk melakukan absensi</p>
                <p>QR Code akan expired pada: {{ $barcode->waktu_akhir->format('d/m/Y H:i') }}</p>
                <p>&copy; {{ date('Y') }} Sistem Absensi QRCode</p>
            </div>

            <div class="no-print" style="margin-top: 30px;">
                <button onclick="window.print()" class="btn-print" style="padding: 10px 20px; background: #4e73df; color: white; border: none; border-radius: 5px; cursor: pointer;">
                    Print QR Code
                </button>
                <button onclick="window.close()" class="btn-close" style="padding: 10px 20px; background: #666; color: white; border: none; border-radius: 5px; cursor: pointer; margin-left: 10px;">
                    Tutup
                </button>
            </div>

            <script>
                // Generate QR Code for print
                var qr = qrcode(0, 'M');
                qr.addData('{{ $barcode->kode_barcode }}');
                qr.make();
                document.getElementById('printQr').innerHTML = '<img src="' + qr.createDataURL(10, 0) + '" style="max-width: 300px;">';

                // Auto print
                window.onload = function() {
                    window.print();
                };
            <\/script>
        </body>
        </html>
    `);
    printWindow.document.close();
}

function refreshPage() {
    window.location.reload();
}

// Auto refresh every 30 seconds to update countdown
setInterval(function() {
    var distance = new Date('{{ $barcode->waktu_akhir }}').getTime() - new Date().getTime();
    if (distance > 0) {
        // Only refresh if still active
        location.reload();
    }
}, 30000);
</script>
@endpush
@endsection
