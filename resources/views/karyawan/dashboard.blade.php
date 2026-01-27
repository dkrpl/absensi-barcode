@extends('layouts.app')

@section('title', 'Dashboard Karyawan')
@section('page-title', 'Dashboard Karyawan')

@section('content')
<!-- Welcome Header -->
<div class="card bg-gradient-primary text-white mb-4">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h3 class="fw-bold mb-2 text-black">Selamat datang, {{ Auth::user()->nama }}! 👋</h3>
                <p class="mb-0 text-black">
                    @if($absensiHariIni)
                    Anda sudah melakukan absensi hari ini pada pukul {{ $absensiHariIni->waktu_absen->format('H:i') }}.
                    @else
                    Silakan lakukan absensi untuk hari ini.
                    @endif
                </p>
            </div>
            <div class="col-md-4 text-end">
                <div class="display-6 fw-bold">{{ now()->format('H:i') }}</div>
                <small>{{ now()->translatedFormat('l, d F Y') }}</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Status Absensi Hari Ini - SELALU DITAMPILKAN -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fas fa-calendar-check me-2"></i>Status Absensi Hari Ini</h6>
                <span class="badge {{ $absensiHariIni ? 'bg-success' : 'bg-info' }}">
                    {{ $absensiHariIni ? 'SUDAH ABSEN' : 'BELUM ABSEN' }}
                </span>
            </div>
            <div class="card-body" id="statusAbsensiContainer">
                @if($absensiHariIni)
                <!-- Status Sudah Absen -->
                <div class="card border-success mb-3">
                    <div class="card-header bg-success text-white py-2">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check-circle me-2"></i>
                            <h6 class="mb-0">ABSEN MASUK BERHASIL</h6>
                        </div>
                    </div>
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-md-4 text-center mb-3">
                                <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center mx-auto"
                                     style="width: 80px; height: 80px;">
                                    <i class="fas fa-check fa-2x"></i>
                                </div>
                                <div class="mt-2">
                                    <strong>ABSEN MASUK</strong>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <table class="table table-borderless table-sm mb-0">
                                    <tr>
                                        <td width="40%"><strong>Tanggal</strong></td>
                                        <td>:</td>
                                        <td>{{ $absensiHariIni->tanggal_absen->translatedFormat('d F Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Waktu Absen</strong></td>
                                        <td>:</td>
                                        <td class="fw-bold text-primary">{{ $absensiHariIni->waktu_absen->format('H:i:s') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Shift</strong></td>
                                        <td>:</td>
                                        <td>
                                            <span class="badge bg-info">{{ $absensiHariIni->shift->nama_shift }}</span>
                                            ({{ $absensiHariIni->shift->jam_mulai }} - {{ $absensiHariIni->shift->jam_akhir }})
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Status</strong></td>
                                        <td>:</td>
                                        <td>
                                            @if($absensiHariIni->status == 'hadir')
                                            <span class="badge bg-success fs-6">Hadir</span>
                                            @else
                                            <span class="badge bg-warning fs-6">Terlambat</span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        @php
                            $jamMulai = \Carbon\Carbon::parse($absensiHariIni->shift->jam_mulai);
                            $waktuAbsen = \Carbon\Carbon::parse($absensiHariIni->waktu_absen->format('H:i:s'));
                            $selisih = (int) $waktuAbsen->diffInMinutes($jamMulai, false);
                        @endphp

                        @if($absensiHariIni->status == 'terlambat')
                        <div class="alert alert-warning mt-3 mb-0 py-2">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <div>
                                    <strong>Keterangan:</strong> Anda terlambat <strong>{{ abs($selisih) }} menit</strong>
                                    dari jam masuk shift ({{ $absensiHariIni->shift->jam_mulai }})
                                </div>
                            </div>
                        </div>
                        @elseif($selisih <= 0)
                        <div class="alert alert-success mt-3 mb-0 py-2">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-thumbs-up me-2"></i>
                                <div>
                                    <strong>Keterangan:</strong> Anda datang <strong>tepat waktu</strong> atau lebih awal
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="alert alert-info mt-3 mb-0 py-2">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-award me-2"></i>
                                <div>
                                    <strong>Keterangan:</strong> Anda datang <strong>{{ $selisih }} menit lebih awal</strong>
                                    dari jam masuk shift
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Tombol untuk absen pulang jika sudah waktunya -->
                    @if($absensiHariIni && !$absensiHariIni->waktu_absen_pulang)
                        @php
                            $jamAkhirShift = \Carbon\Carbon::parse($absensiHariIni->shift->jam_akhir);
                            $now = \Carbon\Carbon::now();
                            $isTimeToCheckout = $now->gte($jamAkhirShift->subMinutes(30)); // 30 menit sebelum akhir shift
                        @endphp


                    @endif
                </div>
                @else
                <!-- Status Belum Absen -->
                <div class="card border-info">
                    <div class="card-header bg-info text-white py-2">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-clock me-2"></i>
                            <h6 class="mb-0">MENUNGGU ABSENSI MASUK</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-start">
                            <div class="flex-shrink-0">
                                <div class="rounded-circle bg-info text-white d-flex align-items-center justify-content-center me-3"
                                     style="width: 48px; height: 48px;">
                                    <i class="fas fa-clock fa-lg"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="fw-bold mb-1">BELUM MELAKUKAN ABSENSI</h5>
                                <p class="mb-3">Anda belum melakukan absensi masuk untuk hari ini.</p>

                                <div class="alert alert-warning mb-3 py-2">
                                    <i class="fas fa-exclamation-circle me-1"></i>
                                    <strong>Informasi:</strong> Segera lakukan absensi sebelum dianggap tidak masuk
                                </div>

                                <div class="text-center mt-4">
                                    <a href="{{ route('karyawan.scan-barcode') }}" class="btn btn-primary btn-lg">
                                        <i class="fas fa-camera me-2"></i>Absen Sekarang
                                    </a>
                                </div>

                                <div class="mt-3 text-center">
                                    <small class="text-muted">
                                        <i class="fas fa-lightbulb me-1"></i>
                                        Gunakan kamera untuk scan QR Code di lokasi absensi
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Countdown Timer untuk yang belum absen -->
                @if(!$absensiHariIni)
                <div class="card border-warning mt-3">
                    <div class="card-header bg-warning text-dark py-2">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-hourglass-half me-2"></i>
                            <h6 class="mb-0">BATAS WAKTU ABSENSI</h6>
                        </div>
                    </div>
                    <div class="card-body py-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small><strong>Sisa Waktu Absen Masuk:</strong></small>
                            </div>
                            <div class="h5 mb-0 fw-bold text-warning" id="countdownTimer">
                                --:--:--
                            </div>
                        </div>
                        <div class="progress mt-2" style="height: 5px;">
                            <div class="progress-bar bg-warning" id="countdownProgress" role="progressbar" style="width: 100%"></div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Statistik Kehadiran -->
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Statistik Bulan Ini</h6>
            </div>
            <div class="card-body p-3">
                <div class="row text-center">
                    <div class="col-4">
                        <div class="h3 fw-bold text-primary mb-1">
                            {{ \App\Models\Absensi::where('id_user', Auth::id())->whereMonth('tanggal_absen', date('m'))->count() }}
                        </div>
                        <small class="text-muted">Hari Hadir</small>
                    </div>
                    <div class="col-4">
                        <div class="h3 fw-bold text-warning mb-1">
                            {{ \App\Models\Absensi::where('id_user', Auth::id())->where('status', 'terlambat')->whereMonth('tanggal_absen', date('m'))->count() }}
                        </div>
                        <small class="text-muted">Keterlambatan</small>
                    </div>
                    <div class="col-4">
                        @php
                            $totalDays = date('t');
                            $hadir = \App\Models\Absensi::where('id_user', Auth::id())->whereMonth('tanggal_absen', date('m'))->count();
                            $percentage = $totalDays > 0 ? round(($hadir / $totalDays) * 100) : 0;
                        @endphp
                        <div class="h3 fw-bold text-success mb-1">{{ $percentage }}%</div>
                        <small class="text-muted">Kehadiran</small>
                    </div>
                </div>
                <div class="progress mt-2" style="height: 8px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $percentage }}%"></div>
                </div>
                <small class="text-muted">{{ $hadir }} dari {{ $totalDays }} hari kerja</small>
            </div>
        </div>
    </div>

    <!-- Quick Actions & Jam Kerja -->
    <div class="col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-bolt me-2"></i>Aksi Cepat & Informasi</h6>
            </div>
            <div class="card-body">
                <!-- Aksi Utama -->
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <a href="{{ route('karyawan.scan-barcode') }}"
                           class="btn btn-primary w-100 h-100 py-4">
                            <div class="text-center">
                                <i class="fas fa-camera fa-3x mb-3"></i>
                                <h5>Scan QR Code</h5>
                                <small class="text-white">
                                    @if($absensiHariIni)
                                        Absen Pulang
                                    @else
                                        Absen Masuk
                                    @endif
                                </small>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-6">
                        <a href="{{ route('karyawan.riwayat-absensi') }}" class="btn btn-info w-100 h-100 py-4">
                            <div class="text-center">
                                <i class="fas fa-history fa-3x mb-3"></i>
                                <h5>Riwayat Absensi</h5>
                                <small class="text-white">Lihat semua absensi</small>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Jam Kerja Aktif -->
                <div class="card border-primary">
                    <div class="card-header bg-primary text-white py-2">
                        <h6 class="mb-0"><i class="fas fa-clock me-2"></i>Jam Kerja Hari Ini</h6>
                    </div>
                    <div class="card-body">
                        @if($absensiHariIni)
                        <div class="text-center mb-3">
                            <div class="h4 fw-bold text-primary">{{ $absensiHariIni->shift->nama_shift }}</div>
                            <div class="h3 fw-bold">{{ $absensiHariIni->shift->jam_mulai }} - {{ $absensiHariIni->shift->jam_akhir }}</div>
                            <small class="text-muted">Shift aktif Anda hari ini</small>
                        </div>

                        @php
                            $jamMulai = \Carbon\Carbon::parse($absensiHariIni->shift->jam_mulai);
                            $jamAkhir = \Carbon\Carbon::parse($absensiHariIni->shift->jam_akhir);
                            $now = \Carbon\Carbon::now();
                            $progress = 0;

                            if ($now->between($jamMulai, $jamAkhir)) {
                                $totalMinutes = $jamMulai->diffInMinutes($jamAkhir);
                                $elapsedMinutes = $jamMulai->diffInMinutes($now);
                                $progress = min(100, round(($elapsedMinutes / $totalMinutes) * 100));
                            } elseif ($now->greaterThan($jamAkhir)) {
                                $progress = 100;
                            }
                        @endphp

                        <div class="mb-2">
                            <small>Progress Jam Kerja:</small>
                            <small class="float-end fw-bold">{{ $progress }}%</small>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-primary progress-bar-striped progress-bar-animated"
                                 role="progressbar"
                                 style="width: {{ $progress }}%"></div>
                        </div>
                        @else
                        <div class="text-center">
                            <div class="h4 fw-bold text-muted mb-2">--:-- - --:--</div>
                            <small class="text-muted">Shift akan ditampilkan setelah absensi</small>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Informasi Shift -->
                <div class="card border-warning mt-3">
                    <div class="card-header bg-warning text-dark py-2">
                        <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Shift</h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-6">
                                <div class="fw-bold text-primary">Shift Pagi</div>
                                <div class="h6 mb-1">08:00 - 16:00</div>
                            </div>
                            <div class="col-6">
                                <div class="fw-bold text-info">Shift Sore</div>
                                <div class="h6 mb-1">16:00 - 00:00</div>
                            </div>
                        </div>
                        <div class="alert alert-warning mt-2 mb-0 py-2">
                            <small>
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                <strong>Batas Keterlambatan:</strong> 15 menit dari jam masuk shift
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Absensi -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0"><i class="fas fa-history me-2"></i>Riwayat Absensi 10 Hari Terakhir</h6>
        <a href="{{ route('karyawan.riwayat-absensi') }}" class="btn btn-sm btn-primary">
            Lihat Semua
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Shift</th>
                        <th>Waktu Absen</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayatAbsensi as $absensi)
                    <tr>
                        <td>
                            <div class="fw-bold">{{ $absensi->tanggal_absen->translatedFormat('d/m/Y') }}</div>
                            <small class="text-muted">{{ $absensi->tanggal_absen->translatedFormat('l') }}</small>
                        </td>
                        <td>
                            <span class="badge bg-info">{{ $absensi->shift->nama_shift }}</span>
                        </td>
                        <td>
                            <div class="fw-bold">{{ $absensi->waktu_absen->format('H:i:s') }}</div>
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
                                $selisih = (int) $waktuAbsen->diffInMinutes($jamMulai, false);
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
                        <td colspan="5" class="text-center py-4">
                            <i class="fas fa-history fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Belum ada riwayat absensi</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Notifikasi jika belum absen dan sudah lewat jam -->
@if(!$absensiHariIni && now()->format('H') >= 8)
<div class="card border-warning mt-4">
    <div class="card-header bg-warning text-dark py-2">
        <div class="d-flex align-items-center">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <h6 class="mb-0">PERHATIAN - BELUM ABSEN</h6>
        </div>
    </div>
    <div class="card-body py-3">
        <div class="d-flex align-items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle fa-2x text-warning"></i>
            </div>
            <div class="flex-grow-1 ms-3">
                <h5 class="mb-1">Perhatian!</h5>
                <p class="mb-0">Anda belum melakukan absensi masuk hari ini. Segera lakukan absensi sebelum dianggap tidak masuk.</p>
            </div>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
let attendanceCheckInterval = null;
let hasShownNotification = false;

$(document).ready(function() {
    // Update time every second
    function updateTime() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('id-ID', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
        $('.display-6').text(timeString);
    }
    setInterval(updateTime, 1000);
    updateTime();

    // Countdown timer untuk yang belum absen
    @if(!$absensiHariIni)
    function updateCountdown() {
        const now = new Date();
        const targetTime = new Date();
        targetTime.setHours(9, 0, 0, 0); // Batas absen jam 9:00

        if (now >= targetTime) {
            $('#countdownTimer').html('WAKTU HABIS').addClass('text-danger');
            $('#countdownProgress').removeClass('bg-warning').addClass('bg-danger');
            return;
        }

        const diff = targetTime - now;
        const hours = Math.floor(diff / (1000 * 60 * 60));
        const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((diff % (1000 * 60)) / 1000);

        // Update progress bar
        const totalMinutes = 60; // dari jam 8-9 (1 jam)
        const remainingMinutes = (hours * 60) + minutes;
        const progressPercentage = Math.max(0, Math.min(100, ((totalMinutes - remainingMinutes) / totalMinutes) * 100));
        $('#countdownProgress').css('width', progressPercentage + '%');

        $('#countdownTimer').text(
            `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`
        );
    }

    setInterval(updateCountdown, 1000);
    updateCountdown();
    @endif

    // Auto refresh status absensi setiap 30 detik jika belum absen
    @if(!$absensiHariIni)
    // Tunggu 3 detik sebelum mulai checking
    setTimeout(() => {
        console.log('Starting attendance check interval...');
        attendanceCheckInterval = setInterval(checkAttendanceStatus, 30000);
        // Check immediately first time
        checkAttendanceStatus();
    }, 3000);
    @endif
});

function checkAttendanceStatus() {
    // Jangan check jika sudah ada notifikasi atau sudah absen
    if (hasShownNotification) {
        clearInterval(attendanceCheckInterval);
        return;
    }

    console.log('Checking attendance status...');

    $.ajax({
        url: '{{ route("karyawan.check-attendance") }}',
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        success: function(response) {
            console.log('Attendance check response:', response);

            if (response.hasAttended && !hasShownNotification) {
                hasShownNotification = true;
                clearInterval(attendanceCheckInterval);

                console.log('User has attended, updating UI...');

                // Update UI tanpa notifikasi popup
                updateAttendanceUI(response.data);

                // Tampilkan pesan sukses di dalam card
                showSuccessMessage(response.data);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error checking attendance:', error);
        }
    });
}

function showSuccessMessage(data) {
    // Buat elemen pesan sukses
    const successMessage = `
        <div class="alert alert-success alert-dismissible fade show mt-3 mb-0" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle me-2 fa-lg"></i>
                <div>
                    <strong>Absensi Berhasil!</strong> Anda telah melakukan absensi masuk pada pukul ${data.waktu}
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;

    // Tambahkan pesan di atas status absensi
    $('#statusAbsensiContainer').prepend(successMessage);
}

function updateAttendanceUI(data) {
    console.log('Updating UI with attendance data:', data);

    // Update badge di header
    $('.card-header .badge').removeClass('bg-info').addClass('bg-success').text('SUDAH ABSEN');

    // Update tombol scan
    $('.btn-primary .small').text('Absen Pulang');

    // Hapus countdown timer jika ada
    $('#countdownTimer').closest('.card').remove();

    // Hapus alert warning jika ada
    $('#lateWarning').remove();

    // Update status container
    let statusBadge = data.status === 'hadir' ?
        '<span class="badge bg-success fs-6">Hadir</span>' :
        '<span class="badge bg-warning fs-6">Terlambat</span>';

    let keteranganClass = data.status === 'terlambat' ? 'alert-warning' : 'alert-success';
    let keteranganIcon = data.status === 'terlambat' ? 'fa-exclamation-triangle' : 'fa-thumbs-up';
    let keteranganText = data.status === 'terlambat' ? 'Anda terlambat' : 'Anda datang tepat waktu';

    // Ganti konten status absensi
    $('#statusAbsensiContainer').html(`
        <div class="card border-success mb-3">
            <div class="card-header bg-success text-white py-2">
                <div class="d-flex align-items-center">
                    <i class="fas fa-check-circle me-2"></i>
                    <h6 class="mb-0">ABSEN MASUK BERHASIL</h6>
                </div>
            </div>
            <div class="card-body p-3">
                <div class="row">
                    <div class="col-md-4 text-center mb-3">
                        <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center mx-auto"
                             style="width: 80px; height: 80px;">
                            <i class="fas fa-check fa-2x"></i>
                        </div>
                        <div class="mt-2">
                            <strong>ABSEN MASUK</strong>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <table class="table table-borderless table-sm mb-0">
                            <tr>
                                <td width="40%"><strong>Tanggal</strong></td>
                                <td>:</td>
                                <td>${data.tanggal}</td>
                            </tr>
                            <tr>
                                <td><strong>Waktu Absen</strong></td>
                                <td>:</td>
                                <td class="fw-bold text-primary">${data.waktu}</td>
                            </tr>
                            <tr>
                                <td><strong>Shift</strong></td>
                                <td>:</td>
                                <td>
                                    <span class="badge bg-info">${data.shift_nama}</span>
                                    (${data.shift_jam})
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Status</strong></td>
                                <td>:</td>
                                <td>
                                    ${statusBadge}
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="alert ${keteranganClass} mt-3 mb-0 py-2">
                    <div class="d-flex align-items-center">
                        <i class="fas ${keteranganIcon} me-2"></i>
                        <div>
                            <strong>Keterangan:</strong> ${keteranganText}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `);

    // Update welcome message
    const welcomeMessage = $('.card-body p.mb-0.text-black');
    if (welcomeMessage.length) {
        welcomeMessage.text(`Anda sudah melakukan absensi hari ini pada pukul ${data.waktu.split(':')[0]}:${data.waktu.split(':')[1]}.`);
    }
}
</script>

<style>
/* Custom styles for the attendance card */
.card-header h6 {
    font-weight: 600;
}

.progress {
    border-radius: 10px;
}

.badge {
    font-weight: 500;
    padding: 0.4em 0.8em;
}

.table td, .table th {
    vertical-align: middle;
}

/* Progress bar animation */
.progress-bar-animated {
    animation: progress-bar-stripes 1s linear infinite;
}

@keyframes progress-bar-stripes {
    0% { background-position: 1rem 0; }
    100% { background-position: 0 0; }
}

/* Countdown timer animation */
#countdownTimer {
    font-family: 'Courier New', monospace;
    letter-spacing: 1px;
}

/* SweetAlert customization */
.swal2-popup {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    border-radius: 10px;
}

.swal2-confirm {
    background-color: #3085d6 !important;
}

.swal2-cancel {
    background-color: #6c757d !important;
}

/* Card border colors */
.card.border-success {
    border-left: 4px solid #28a745 !important;
}

.card.border-info {
    border-left: 4px solid #17a2b8 !important;
}

.card.border-warning {
    border-left: 4px solid #ffc107 !important;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .card-body .row > [class*="col-"] {
        margin-bottom: 15px;
    }

    .btn-lg {
        padding: 0.75rem 1.5rem;
        font-size: 1rem;
    }

    .h3, .h4 {
        font-size: 1.5rem;
    }

    .display-6 {
        font-size: 2rem;
    }

    .table-responsive {
        font-size: 0.875rem;
    }
}
</style>
@endpush
@endsection
