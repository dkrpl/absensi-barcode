@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard Admin')

@section('content')
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Dashboard Admin</h1>
        <p class="text-muted mb-0">Selamat datang, {{ Auth::user()->nama }}!</p>
    </div>
    <div class="text-end">
        <span class="badge bg-primary">
            <i class="fas fa-calendar me-1"></i>
            {{ now()->translatedFormat('l, d F Y') }}
        </span>
        <span class="badge bg-success ms-2">
            <i class="fas fa-clock me-1"></i>
            <span id="currentTime">{{ now()->format('H:i:s') }}</span>
        </span>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs fw-bold text-primary text-uppercase mb-1">
                            Total Karyawan
                        </div>
                        <div class="h5 mb-0 fw-bold text-gray-800">{{ $totalKaryawan }}</div>
                        <div class="mt-2">
                            <a href="{{ route('admin.data-karyawan') }}" class="small text-primary">
                                Lihat semua <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-primary opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs fw-bold text-success text-uppercase mb-1">
                            Hadir Hari Ini
                        </div>
                        <div class="h5 mb-0 fw-bold text-gray-800">{{ $totalHadir }}</div>
                        <div class="mt-2">
                            <a href="{{ route('admin.laporan-absensi') }}" class="small text-success">
                                Lihat detail <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-success opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs fw-bold text-warning text-uppercase mb-1">
                            Terlambat Hari Ini
                        </div>
                        <div class="h5 mb-0 fw-bold text-gray-800">{{ $totalTerlambat }}</div>
                        <div class="mt-2">
                            <a href="{{ route('admin.laporan-absensi') }}" class="small text-warning">
                                Lihat detail <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clock fa-2x text-warning opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs fw-bold text-info text-uppercase mb-1">
                            QR Code Aktif
                        </div>
                        <div class="h5 mb-0 fw-bold text-gray-800">
                            {{ \App\Models\Barcode::where('status', 'aktif')->where('waktu_akhir', '>', now())->count() }}
                        </div>
                        <div class="mt-2">
                            <a href="{{ route('admin.generate-barcode') }}" class="small text-info">
                                Buat baru <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-qrcode fa-2x text-info opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-bolt me-2"></i>Aksi Cepat</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <a href="{{ route('admin.generate-barcode') }}" class="btn btn-primary w-100 h-100 py-3">
                            <i class="fas fa-qrcode fa-2x mb-2"></i>
                            <div class="fw-bold">Generate QR Code</div>
                            <small class="d-block">Buat QR Code absensi baru</small>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('admin.data-karyawan') }}" class="btn btn-success w-100 h-100 py-3">
                            <i class="fas fa-user-plus fa-2x mb-2"></i>
                            <div class="fw-bold">Tambah Karyawan</div>
                            <small class="d-block">Tambah karyawan baru</small>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('admin.laporan-absensi') }}" class="btn btn-info w-100 h-100 py-3">
                            <i class="fas fa-file-export fa-2x mb-2"></i>
                            <div class="fw-bold">Export Laporan</div>
                            <small class="d-block">Export data absensi</small>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-warning w-100 h-100 py-3" onclick="window.location.reload()">
                            <i class="fas fa-sync-alt fa-2x mb-2"></i>
                            <div class="fw-bold">Refresh Data</div>
                            <small class="d-block">Perbarui data terbaru</small>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Absensi -->
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fas fa-history me-2"></i>Absensi Hari Ini</h6>
                <a href="{{ route('admin.laporan-absensi') }}" class="btn btn-sm btn-primary">
                    Lihat Semua
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama Karyawan</th>
                                <th>Shift</th>
                                <th>Waktu Absen</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($absensiHariIni as $index => $absensi)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2"
                                             style="width: 32px; height: 32px;">
                                            {{ substr($absensi->user->nama, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold">{{ $absensi->user->nama }}</div>
                                            <small class="text-muted">{{ $absensi->user->username }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $absensi->shift->nama_shift }}</span><br>
                                    <small class="text-muted">{{ $absensi->shift->jam_mulai }} - {{ $absensi->shift->jam_akhir }}</small>
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $absensi->waktu_absen->format('H:i:s') }}</div>
                                    <small class="text-muted">{{ $absensi->waktu_absen->format('d/m/Y') }}</small>
                                </td>
                                <td>
                                    @if($absensi->status == 'hadir')
                                    <span class="badge bg-success">Hadir</span>
                                    @else
                                    <span class="badge bg-warning">Terlambat</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $jamMulai = \Carbon\Carbon::parse($absensi->shift->jam_mulai);
                                        $waktuAbsen = \Carbon\Carbon::parse($absensi->waktu_absen->format('H:i:s'));
                                        $selisih = $waktuAbsen->diffInMinutes($jamMulai, false);
                                    @endphp
                                    @if($absensi->status == 'terlambat')
                                        <span class="text-warning">
                                            <i class="fas fa-exclamation-triangle me-1"></i>
                                            Terlambat {{ abs($selisih) }} menit
                                        </span>
                                    @elseif($selisih <= 0)
                                        <span class="text-success">
                                            <i class="fas fa-check me-1"></i>
                                            Tepat waktu
                                        </span>
                                    @else
                                        <span class="text-info">
                                            <i class="fas fa-arrow-up me-1"></i>
                                            Lebih awal {{ $selisih }} menit
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Belum ada absensi hari ini</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Statistik Bulan Ini</h6>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <div class="display-4 fw-bold text-primary">
                        {{ round(($totalHadir / max($totalKaryawan, 1)) * 100) }}%
                    </div>
                    <p class="text-muted">Tingkat Kehadiran</p>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Kehadiran</span>
                        <span class="fw-bold">{{ $totalHadir }}/{{ $totalKaryawan }}</span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-success"
                             style="width: {{ ($totalHadir / max($totalKaryawan, 1)) * 100 }}%">
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Keterlambatan</span>
                        <span class="fw-bold">{{ $totalTerlambat }}</span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-warning"
                             style="width: {{ ($totalTerlambat / max($totalKaryawan, 1)) * 100 }}%">
                        </div>
                    </div>
                </div>

                <hr>

                <div class="text-center">
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Data diperbarui secara real-time
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // Area Chart - 7 Hari Terakhir
    var ctx = document.getElementById("absensiChart").getContext('2d');

    // Data dari controller
    var labels = @json($dates);
    var hadirData = @json($hadirData);
    var terlambatData = @json($terlambatData);

    var absensiChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Hadir',
                data: hadirData,
                backgroundColor: 'rgba(78, 115, 223, 0.05)',
                borderColor: 'rgba(78, 115, 223, 1)',
                pointRadius: 3,
                pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                pointBorderColor: 'rgba(78, 115, 223, 1)',
                pointHoverRadius: 3,
                pointHoverBackgroundColor: 'rgba(78, 115, 223, 1)',
                pointHoverBorderColor: 'rgba(78, 115, 223, 1)',
                pointHitRadius: 10,
                pointBorderWidth: 2,
                fill: true,
                tension: 0.3
            },
            {
                label: 'Terlambat',
                data: terlambatData,
                backgroundColor: 'rgba(246, 194, 62, 0.05)',
                borderColor: 'rgba(246, 194, 62, 1)',
                pointRadius: 3,
                pointBackgroundColor: 'rgba(246, 194, 62, 1)',
                pointBorderColor: 'rgba(246, 194, 62, 1)',
                pointHoverRadius: 3,
                pointHoverBackgroundColor: 'rgba(246, 194, 62, 1)',
                pointHoverBorderColor: 'rgba(246, 194, 62, 1)',
                pointHitRadius: 10,
                pointBorderWidth: 2,
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                }
            },
            scales: {
                x: {
                    display: true,
                    title: {
                        display: true,
                        text: 'Tanggal'
                    }
                },
                y: {
                    display: true,
                    title: {
                        display: true,
                        text: 'Jumlah Karyawan'
                    },
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });

    // Pie Chart - Status Hari Ini
    var ctxPie = document.getElementById("statusPieChart");
    var belumAbsen = {{ $totalKaryawan - $totalHadir - $totalTerlambat }};

    var statusPieChart = new Chart(ctxPie, {
        type: 'doughnut',
        data: {
            labels: ["Hadir", "Terlambat", "Belum Absen"],
            datasets: [{
                data: [{{ $totalHadir }}, {{ $totalTerlambat }}, belumAbsen],
                backgroundColor: ['#1cc88a', '#f6c23e', '#e74a3b'],
                hoverBackgroundColor: ['#17a673', '#dda20a', '#be2617'],
                hoverBorderColor: "rgba(234, 236, 244, 1)",
            }],
        },
        options: {
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            var label = context.label || '';
                            if (label) {
                                label += ': ';
                            }
                            var total = context.dataset.data.reduce((a, b) => a + b, 0);
                            var percentage = Math.round((context.raw / total) * 100);
                            label += context.raw + ' (' + percentage + '%)';
                            return label;
                        }
                    }
                }
            },
            cutout: '70%',
        },
    });

    // Update current time
    function updateCurrentTime() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('id-ID', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
        $('#currentTime').text(timeString);
    }

    setInterval(updateCurrentTime, 1000);

    // Auto refresh data every 30 seconds
    setInterval(function() {
        $.ajax({
            url: '{{ route("admin.dashboard.stats") }}',
            method: 'GET',
            success: function(response) {
                // Update card numbers
                $('.card-body .h5').eq(0).text(response.totalKaryawan);
                $('.card-body .h5').eq(1).text(response.totalHadir);
                $('.card-body .h5').eq(2).text(response.totalTerlambat);
                $('.card-body .h5').eq(3).text(response.qrAktif);

                // Show notification if new attendance
                if (response.newAbsensi > 0) {
                    showNotification('info', 'Ada ' + response.newAbsensi + ' absensi baru');
                }
            }
        });
    }, 30000);

    function showNotification(type, message) {
        const alert = `
            <div class="alert alert-${type} alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-bell me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;

        // Remove existing alerts
        $('.alert-dismissible').alert('close');

        // Add new alert
        setTimeout(function() {
            $('.container-fluid').prepend(alert);

            // Auto remove after 5 seconds
            setTimeout(function() {
                $('.alert').alert('close');
            }, 5000);
        }, 100);
    }
});
</script>
@endpush
@endsection
