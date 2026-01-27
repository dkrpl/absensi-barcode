@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Laporan Absensi Kafe</h1>
        <div>
            <button onclick="exportRekapBulanan()" class="btn btn-info me-2">
                <i class="fas fa-chart-bar"></i> Rekap Bulanan
            </button>
            <button onclick="exportLaporan()" class="btn btn-success">
                <i class="fas fa-file-excel"></i> Export CSV
            </button>
        </div>
    </div>

    <!-- Stats Card - Sesuai dengan controller -->
    @if($stats)
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-primary text-uppercase mb-1">
                                Rata-rata Kehadiran
                            </div>
                            <div class="h5 mb-0 fw-bold text-gray-800">
                                {{ number_format($stats['rata_persentase'], 1) }}%
                            </div>
                            <div class="text-xs text-muted mt-1">
                                {{ $periodeLabel }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percentage fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-success text-uppercase mb-1">
                                Total Absensi
                            </div>
                            <div class="h5 mb-0 fw-bold text-gray-800">
                                {{ $stats['total_absensi'] }}
                            </div>
                            <div class="text-xs text-muted mt-1">
                                {{ $stats['total_karyawan'] }} karyawan aktif
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-info text-uppercase mb-1">
                                Karyawan Teladan
                            </div>
                            <div class="h5 mb-0 fw-bold text-gray-800">
                                {{ $stats['top_performers'] }}
                            </div>
                            <div class="text-xs text-muted mt-1">
                                ≥ 90% kehadiran
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-trophy fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-warning text-uppercase mb-1">
                                Keterlambatan
                            </div>
                            <div class="h5 mb-0 fw-bold text-gray-800">
                                {{ $stats['total_terlambat'] }}
                            </div>
                            <div class="text-xs text-muted mt-1">
                                {{ $stats['persentase_terlambat'] }}% dari kehadiran
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Filter Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 fw-bold text-primary">Filter Laporan</h6>
        </div>
        <div class="card-body">
            <form id="filterForm" method="GET" action="{{ route('admin.laporan-absensi') }}">
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Filter Periode</label>
                            <select name="periode_mode" class="form-control" id="periodeMode">
                                <option value="monthly" {{ $periodeMode == 'monthly' ? 'selected' : '' }}>Per Bulan</option>
                                <option value="range" {{ $periodeMode == 'range' ? 'selected' : '' }}>Rentang Tanggal</option>
                            </select>
                        </div>
                    </div>

                    <!-- Filter Bulanan - Terpisah Bulan dan Tahun -->
                    <div class="col-md-2" id="monthFilter" style="{{ $periodeMode == 'range' ? 'display: none;' : '' }}">
                        <div class="form-group">
                            <label>Bulan</label>
                            <select name="bulan_select" class="form-control" id="bulanSelect">
                                @php
                                    // Parse bulan yang dipilih
                                    $selectedMonthValue = request('bulan_select') ?: (request('bulan') ? explode('-', request('bulan'))[1] : date('m'));
                                    $selectedYearValue = request('tahun_select') ?: (request('bulan') ? explode('-', request('bulan'))[0] : date('Y'));
                                @endphp

                                @foreach($bulanList as $key => $namaBulan)
                                    <option value="{{ str_pad($key, 2, '0', STR_PAD_LEFT) }}"
                                            {{ $selectedMonthValue == str_pad($key, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                        {{ $namaBulan }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="hidden" name="bulan" id="bulanHidden" value="{{ request('bulan') ?: date('Y-m') }}">
                        </div>
                    </div>

                    <div class="col-md-2" id="yearFilter" style="{{ $periodeMode == 'range' ? 'display: none;' : '' }}">
                        <div class="form-group">
                            <label>Tahun</label>
                            <select name="tahun_select" class="form-control" id="tahunSelect">
                                @php
                                    // Parse tahun yang dipilih
                                    $selectedYearValue = request('tahun_select') ?: (request('bulan') ? explode('-', request('bulan'))[0] : date('Y'));
                                @endphp

                                @foreach($allYears as $year)
                                    <option value="{{ $year }}"
                                            {{ $selectedYearValue == $year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Filter Rentang Tanggal -->
                    <div class="col-md-3" id="dateFromFilter" style="{{ $periodeMode == 'monthly' ? 'display: none;' : '' }}">
                        <div class="form-group">
                            <label>Tanggal Dari</label>
                            <input type="date" name="tanggal_dari" class="form-control" id="tanggalDari"
                                   value="{{ request('tanggal_dari') }}">
                        </div>
                    </div>

                    <div class="col-md-3" id="dateToFilter" style="{{ $periodeMode == 'monthly' ? 'display: none;' : '' }}">
                        <div class="form-group">
                            <label>Tanggal Sampai</label>
                            <input type="date" name="tanggal_sampai" class="form-control" id="tanggalSampai"
                                   value="{{ request('tanggal_sampai') }}">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Karyawan</label>
                            <select name="karyawan" class="form-control" id="karyawanSelect">
                                <option value="">Semua Karyawan</option>
                                @foreach($karyawanList as $karyawan)
                                <option value="{{ $karyawan->id }}"
                                        {{ request('karyawan') == $karyawan->id ? 'selected' : '' }}>
                                    {{ $karyawan->nama }}
                                    @if($karyawan->departemen)
                                        ({{ $karyawan->departemen }})
                                    @endif
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Departemen</label>
                            <select name="departemen" class="form-control" id="departemenSelect">
                                <option value="">Semua Departemen</option>
                                @foreach($departemenList as $dept)
                                <option value="{{ $dept }}"
                                        {{ request('departemen') == $dept ? 'selected' : '' }}>
                                    {{ $dept }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div class="d-flex">
                                <button type="submit" class="btn btn-primary btn-block me-2">
                                    <i class="fas fa-filter"></i> Terapkan Filter
                                </button>
                                <a href="{{ route('admin.laporan-absensi') }}" class="btn btn-secondary">
                                    <i class="fas fa-redo"></i> Reset
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Info Standar Kafe -->
    @if(!request('karyawan'))
    <div class="alert alert-info mb-4">
        <i class="fas fa-info-circle me-2"></i>
        <strong>Standar Kehadiran Kafe:</strong> 30 hari kerja per bulan.
        <strong>Sangat Baik (≥90%) = ≥27 hari</strong>,
        <strong>Baik (80-89%) = 24-26 hari</strong>,
        <strong>Cukup (70-79%) = 21-23 hari</strong>,
        <strong>Kurang (60-69%) = 18-20 hari</strong>,
        <strong>Sangat Kurang (<60%) = <18 hari</strong>
    </div>
    @endif

    <!-- Tabel Persentase Karyawan Per Bulan -->
    @if(!request('karyawan') && $persentasePerKaryawan && $persentasePerKaryawan->count() > 0)
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <div>
                <h6 class="m-0 fw-bold text-primary">Rekap Kehadiran Bulanan</h6>
                <small class="text-muted">{{ $periodeLabel }} | Standar: {{ $totalHariBulan }} hari kerja</small>
            </div>
            <div>
                <span class="badge bg-success me-2">≥ 90% Sangat Baik</span>
                <span class="badge bg-primary me-2">80-89% Baik</span>
                <span class="badge bg-warning me-2">70-79% Cukup</span>
                <span class="badge bg-danger">≤ 69% Perlu Perhatian</span>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="20%">Nama Karyawan</th>
                            <th width="15%">Departemen/Jabatan</th>
                            <th width="10%">Hadir</th>
                            <th width="10%">Terlambat</th>
                            <th width="8%">Alpha</th>
                            <th width="8%">% Hadir</th>
                            <th width="8%">% Telat</th>
                            <th width="16%">Progress & Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($persentasePerKaryawan as $index => $data)
                        @php
                            $statusClass = match($data['status']) {
                                'sangat-baik' => 'success',
                                'baik' => 'primary',
                                'cukup' => 'warning',
                                'kurang' => 'danger',
                                'sangat-kurang' => 'danger',
                                default => 'secondary'
                            };

                            $statusText = match($data['status']) {
                                'sangat-baik' => 'Sangat Baik',
                                'baik' => 'Baik',
                                'cukup' => 'Cukup',
                                'kurang' => 'Kurang',
                                'sangat-kurang' => 'Sangat Kurang',
                                default => '-'
                            };

                            // Warna teks untuk badge
                            $textClass = match($data['status']) {
                                'sangat-baik' => 'text-bg-success',
                                'baik' => 'text-bg-primary',
                                'cukup' => 'text-bg-warning',
                                'kurang' => 'text-bg-danger',
                                'sangat-kurang' => 'text-bg-danger',
                                default => 'text-bg-secondary'
                            };
                        @endphp
                        <tr>
                            <td class="text-center align-middle">{{ $index + 1 }}</td>
                            <td class="align-middle">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        @if(isset($data['id']))
                                            @php
                                                $karyawan = \App\Models\User::find($data['id']);
                                            @endphp
                                            @if($karyawan && $karyawan->foto)
                                                <img src="{{ asset('storage/' . $karyawan->foto) }}"
                                                     alt="{{ $data['nama'] }}"
                                                     class="rounded-circle"
                                                     style="width: 40px; height: 40px; object-fit: cover;">
                                            @else
                                                <div class="avatar-placeholder rounded-circle d-flex align-items-center justify-content-center"
                                                     style="width: 40px; height: 40px; background-color: #e9ecef;">
                                                    <i class="fas fa-user text-muted"></i>
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                    <div>
                                        <strong>{{ $data['nama'] }}</strong>
                                        @if($data['status'] == 'sangat-baik')
                                            <i class="fas fa-trophy text-warning ms-1" title="Karyawan Teladan"></i>
                                        @endif
                                        <br>
                                        <small class="text-muted">{{ $data['nip'] ?? '-' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="align-middle">
                                <span class="badge text-bg-info mb-1">{{ $data['departemen'] ?? '-' }}</span>
                                <br>
                                <small class="text-muted">{{ $data['jabatan'] ?? '-' }}</small>
                            </td>
                            <td class="text-center align-middle">
                                <span class="fw-bold">{{ $data['total_hadir'] }}/{{ $data['hari_kerja'] }}</span>
                            </td>
                            <td class="text-center align-middle">
                                <span class="badge {{ $data['total_terlambat'] > 0 ? 'text-bg-warning' : 'text-bg-secondary' }} p-2">
                                    {{ $data['total_terlambat'] ?? 0 }}
                                </span>
                            </td>
                            <td class="text-center align-middle">
                                <span class="badge {{ $data['alpha'] > 0 ? 'text-bg-danger' : 'text-bg-success' }} p-2">
                                    {{ $data['alpha'] }}
                                </span>
                            </td>
                            <td class="text-center align-middle">
                                <span class="fw-bold text-{{ $statusClass }}">
                                    {{ $data['persentase'] }}%
                                </span>
                            </td>
                            <td class="text-center align-middle">
                                @php
                                    $telatPercentage = $data['rata_rata_terlambat'] ?? 0;
                                @endphp
                                <span class="{{ $telatPercentage > 20 ? 'text-danger' : ($telatPercentage > 10 ? 'text-warning' : 'text-success') }}">
                                    {{ $telatPercentage }}%
                                </span>
                            </td>
                            <td class="align-middle">
                                <div class="progress" style="height: 25px;">
                                    <div class="progress-bar bg-{{ $statusClass }}"
                                         role="progressbar"
                                         style="width: {{ min($data['persentase'], 100) }}%"
                                         aria-valuenow="{{ $data['persentase'] }}"
                                         aria-valuemin="0"
                                         aria-valuemax="100">
                                        <span class="progress-text">{{ $data['persentase'] }}%</span>
                                    </div>
                                </div>
                                <div class="text-center mt-1">
                                    <span class="badge {{ $textClass }}">
                                        <i class="fas fa-{{ $data['status'] == 'sangat-baik' ? 'trophy' : ($data['status'] == 'cukup' ? 'clock' : ($data['status'] == 'kurang' ? 'exclamation-triangle' : 'info-circle')) }}"></i>
                                        {{ $statusText }}
                                    </span>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Chart Ringkasan -->
            <div class="row mt-4">
                <div class="col-md-3">
                    <div class="card border-success">
                        <div class="card-body text-center">
                            <h5 class="card-title text-success">Sangat Baik (≥ 90%)</h5>
                            <h2 class="text-success">
                                {{ $persentasePerKaryawan->where('persentase', '>=', 90)->count() }}
                            </h2>
                            <p class="text-muted">Karyawan</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-primary">
                        <div class="card-body text-center">
                            <h5 class="card-title text-primary">Baik (80-89%)</h5>
                            <h2 class="text-primary">
                                {{ $persentasePerKaryawan->whereBetween('persentase', [80, 89])->count() }}
                            </h2>
                            <p class="text-muted">Karyawan</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-warning">
                        <div class="card-body text-center">
                            <h5 class="card-title text-warning">Cukup (70-79%)</h5>
                            <h2 class="text-warning">
                                {{ $persentasePerKaryawan->whereBetween('persentase', [70, 79])->count() }}
                            </h2>
                            <p class="text-muted">Karyawan</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-danger">
                        <div class="card-body text-center">
                            <h5 class="card-title text-danger">Perlu Perhatian (≤ 69%)</h5>
                            <h2 class="text-danger">
                                {{ $persentasePerKaryawan->where('persentase', '<', 70)->count() }}
                            </h2>
                            <p class="text-muted">Karyawan</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @elseif(!request('karyawan') && $persentasePerKaryawan && $persentasePerKaryawan->count() == 0 && ($periodeMode == 'monthly' || request('tanggal_dari')))
    <div class="card shadow mb-4">
        <div class="card-body text-center py-5">
            <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">Tidak ada data kehadiran</h5>
            <p class="text-muted mb-4">
                Tidak ada data kehadiran untuk periode {{ $periodeLabel }}
            </p>
        </div>
    </div>
    @endif

    <!-- Laporan Absensi Detail Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 fw-bold text-primary">Data Absensi Detail</h6>
            <div>
                <span class="badge bg-success me-2">Hadir</span>
                <span class="badge bg-warning">Terlambat</span>
            </div>
        </div>
        <div class="card-body">
            @if($absensi->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="12%">Tanggal</th>
                            <th width="20%">Nama Karyawan</th>
                            <th width="15%">Shift</th>
                            <th width="10%">Waktu Absen</th>
                            <th width="10%">Status</th>
                            <th width="15%">Keterangan</th>
                            @if(!request('karyawan') && $persentasePerKaryawan && $persentasePerKaryawan->count() > 0)
                            <th width="13%">% Kehadiran</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($absensi as $index => $item)
                        <tr>
                            <td class="text-center">
                                {{ ($absensi->currentPage() - 1) * $absensi->perPage() + $index + 1 }}
                            </td>
                            <td>
                                <span class="fw-bold">{{ $item->tanggal_absen->format('d/m/Y') }}</span><br>
                                <small class="text-muted">{{ $item->tanggal_absen->translatedFormat('l') }}</small>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($item->user->foto)
                                        <img src="{{ asset('storage/' . $item->user->foto) }}"
                                             alt="{{ $item->user->nama }}"
                                             class="rounded-circle me-2"
                                             style="width: 35px; height: 35px; object-fit: cover;">
                                    @else
                                        <div class="avatar-placeholder rounded-circle me-2 d-flex align-items-center justify-content-center"
                                             style="width: 35px; height: 35px; background-color: #e9ecef;">
                                            <i class="fas fa-user text-muted" style="font-size: 14px;"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <span>{{ $item->user->nama }}</span><br>
                                        <small class="text-muted">{{ $item->user->departemen ?? '-' }} | {{ $item->user->jabatan ?? '-' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge text-bg-secondary">
                                    {{ $item->shift->nama_shift }}<br>
                                    <small>{{ $item->shift->jam_mulai }} - {{ $item->shift->jam_akhir }}</small>
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="fw-bold {{ $item->status == 'terlambat' ? 'text-warning' : 'text-success' }}">
                                    {{ $item->waktu_absen->format('H:i') }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($item->status == 'hadir')
                                    <span class="badge text-bg-success p-2">
                                        <i class="fas fa-check me-1"></i> Hadir
                                    </span>
                                @else
                                    <span class="badge text-bg-warning p-2">
                                        <i class="fas fa-clock me-1"></i> Terlambat
                                    </span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $jamMulai = \Carbon\Carbon::parse($item->shift->jam_mulai);
                                    $waktuAbsen = \Carbon\Carbon::parse($item->waktu_absen->format('H:i:s'));
                                    $selisih = $waktuAbsen->diffInMinutes($jamMulai, false);
                                @endphp
                                @if($item->status == 'terlambat')
                                    <span class="text-warning">
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        Terlambat {{ abs((int)$selisih) }} menit
                                    </span>
                                @else
                                    @if($selisih <= 0)
                                        <span class="text-success">
                                            <i class="fas fa-check-circle me-1"></i>
                                            Tepat waktu
                                        </span>
                                    @else
                                        <span class="text-info">
                                            <i class="fas fa-flag me-1"></i>
                                            Lebih awal {{ (int)$selisih }} menit
                                        </span>
                                    @endif
                                @endif
                            </td>
                            @if(!request('karyawan') && $persentasePerKaryawan && $persentasePerKaryawan->count() > 0)
                            <td>
                                @php
                                    $karyawanPersentase = $persentasePerKaryawan->firstWhere('id', $item->user->id);
                                @endphp
                                @if($karyawanPersentase)
                                @php
                                    $statusClass = match($karyawanPersentase['status']) {
                                        'sangat-baik' => 'success',
                                        'baik' => 'primary',
                                        'cukup' => 'warning',
                                        'kurang' => 'danger',
                                        'sangat-kurang' => 'danger',
                                        default => 'secondary'
                                    };

                                    $textClass = match($karyawanPersentase['status']) {
                                        'sangat-baik' => 'text-bg-success',
                                        'baik' => 'text-bg-primary',
                                        'cukup' => 'text-bg-warning',
                                        'kurang' => 'text-bg-danger',
                                        'sangat-kurang' => 'text-bg-danger',
                                        default => 'text-bg-secondary'
                                    };
                                @endphp
                                <div class="d-flex align-items-center">
                                    <div class="progress flex-grow-1 me-2" style="height: 20px;">
                                        <div class="progress-bar bg-{{ $statusClass }}"
                                             role="progressbar"
                                             style="width: {{ min($karyawanPersentase['persentase'], 100) }}%"
                                             aria-valuenow="{{ $karyawanPersentase['persentase'] }}"
                                             aria-valuemin="0"
                                             aria-valuemax="100">
                                        </div>
                                    </div>
                                    <span class="badge {{ $textClass }}">
                                        {{ $karyawanPersentase['persentase'] }}%
                                    </span>
                                </div>
                                @else
                                <span class="text-muted">
                                    <i class="fas fa-minus"></i>
                                </span>
                                @endif
                            </td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $absensi->links() }}
            </div>
            @else
            <div class="text-center py-5">
                <div class="empty-state">
                    <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Tidak ada data absensi</h5>
                    <p class="text-muted mb-4">
                        @if(request('tanggal_dari') || request('tanggal_sampai') || request('karyawan') || request('bulan'))
                            Data tidak ditemukan dengan filter yang dipilih
                        @else
                            Belum ada data absensi yang tercatat
                        @endif
                    </p>
                    @if(request('tanggal_dari') || request('tanggal_sampai') || request('karyawan') || request('bulan'))
                        <a href="{{ route('admin.laporan-absensi') }}" class="btn btn-primary">
                            <i class="fas fa-redo me-1"></i> Reset Filter
                        </a>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Update hidden bulan field saat halaman dimuat
    updateBulanHidden();

    // Initialize filter mode
    initFilterMode();

    // Event handler untuk perubahan mode filter
    $('#periodeMode').on('change', function() {
        toggleFilterMode();
    });

    // Event handler untuk perubahan bulan
    $('#bulanSelect').on('change', function() {
        updateBulanHidden();
        if ($('#periodeMode').val() === 'monthly') {
            $('#filterForm').submit();
        }
    });

    // Event handler untuk perubahan tahun
    $('#tahunSelect').on('change', function() {
        updateBulanHidden();
        if ($('#periodeMode').val() === 'monthly') {
            $('#filterForm').submit();
        }
    });

    // Event handler untuk perubahan karyawan/departemen
    $('#karyawanSelect, #departemenSelect').on('change', function() {
        $('#filterForm').submit();
    });

    // Event handler untuk tanggal
    $('#tanggalDari, #tanggalSampai').on('change', function() {
        if ($('#periodeMode').val() === 'range') {
            if (validateDateRange()) {
                $('#filterForm').submit();
            }
        }
    });
});

// Fungsi untuk mengupdate hidden bulan field
function updateBulanHidden() {
    let bulan = $('#bulanSelect').val();
    let tahun = $('#tahunSelect').val();

    if (bulan && tahun) {
        $('#bulanHidden').val(tahun + '-' + bulan);
    }
}

// Fungsi untuk inisialisasi mode filter
function initFilterMode() {
    let mode = $('#periodeMode').val();

    if (mode === 'monthly') {
        $('#monthFilter').show();
        $('#yearFilter').show();
        $('#dateFromFilter').hide();
        $('#dateToFilter').hide();

        // Set default bulan dan tahun jika kosong
        if (!$('#bulanSelect').val() || !$('#tahunSelect').val()) {
            let currentDate = new Date();
            let currentMonth = ('0' + (currentDate.getMonth() + 1)).slice(-2);
            let currentYear = currentDate.getFullYear();

            if (!$('#bulanSelect').val()) {
                $('#bulanSelect').val(currentMonth);
            }
            if (!$('#tahunSelect').val()) {
                $('#tahunSelect').val(currentYear);
            }

            updateBulanHidden();
        }
    } else {
        $('#monthFilter').hide();
        $('#yearFilter').hide();
        $('#dateFromFilter').show();
        $('#dateToFilter').show();

        // Set default date range (last 30 days) jika kosong
        let today = new Date();
        let lastMonth = new Date(today);
        lastMonth.setDate(today.getDate() - 30);

        if (!$('#tanggalDari').val()) {
            $('#tanggalDari').val(formatDate(lastMonth));
        }
        if (!$('#tanggalSampai').val()) {
            $('#tanggalSampai').val(formatDate(today));
        }
    }
}

// Fungsi untuk toggle mode filter
function toggleFilterMode() {
    let mode = $('#periodeMode').val();

    if (mode === 'monthly') {
        $('#monthFilter').show();
        $('#yearFilter').show();
        $('#dateFromFilter').hide();
        $('#dateToFilter').hide();

        // Clear date inputs
        $('#tanggalDari').val('');
        $('#tanggalSampai').val('');

        // Set default bulan dan tahun jika kosong
        if (!$('#bulanSelect').val() || !$('#tahunSelect').val()) {
            let currentDate = new Date();
            let currentMonth = ('0' + (currentDate.getMonth() + 1)).slice(-2);
            let currentYear = currentDate.getFullYear();

            if (!$('#bulanSelect').val()) {
                $('#bulanSelect').val(currentMonth);
            }
            if (!$('#tahunSelect').val()) {
                $('#tahunSelect').val(currentYear);
            }

            updateBulanHidden();
        }

        // Submit form
        setTimeout(() => {
            $('#filterForm').submit();
        }, 100);
    } else {
        $('#monthFilter').hide();
        $('#yearFilter').hide();
        $('#dateFromFilter').show();
        $('#dateToFilter').show();

        // Clear bulan inputs
        $('#bulanSelect').val('');
        $('#tahunSelect').val('');
        $('#bulanHidden').val('');

        // Set default date range (last 30 days) jika kosong
        let today = new Date();
        let lastMonth = new Date(today);
        lastMonth.setDate(today.getDate() - 30);

        if (!$('#tanggalDari').val()) {
            $('#tanggalDari').val(formatDate(lastMonth));
        }
        if (!$('#tanggalSampai').val()) {
            $('#tanggalSampai').val(formatDate(today));
        }

        // Submit form
        setTimeout(() => {
            $('#filterForm').submit();
        }, 100);
    }
}

// Fungsi validasi tanggal
function validateDateRange() {
    let tanggalDari = $('#tanggalDari').val();
    let tanggalSampai = $('#tanggalSampai').val();

    if (tanggalDari && tanggalSampai) {
        if (new Date(tanggalDari) > new Date(tanggalSampai)) {
            Swal.fire({
                icon: 'error',
                title: 'Kesalahan',
                text: 'Tanggal mulai tidak boleh lebih besar dari tanggal akhir',
                confirmButtonText: 'OK'
            });
            return false;
        }
    }

    return true;
}

// Fungsi format date untuk input type="date"
function formatDate(date) {
    let d = new Date(date);
    let month = '' + (d.getMonth() + 1);
    let day = '' + d.getDate();
    let year = d.getFullYear();

    if (month.length < 2) month = '0' + month;
    if (day.length < 2) day = '0' + day;

    return [year, month, day].join('-');
}

// Fungsi export Laporan
function exportLaporan() {
    if ($('#periodeMode').val() === 'range') {
        if (!validateDateRange()) {
            return;
        }
    }

    // Update bulan hidden field sebelum export
    updateBulanHidden();

    // Get filter values
    let formData = $('#filterForm').serialize();

    // Build export URL
    let url = '{{ route("admin.export-laporan") }}?' + formData;

    // Show loading
    Swal.fire({
        title: 'Mempersiapkan Export',
        text: 'Sedang memproses data...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Open export URL
    setTimeout(() => {
        window.open(url, '_blank');
        Swal.close();
    }, 1000);
}

// Fungsi export Rekap Bulanan
function exportRekapBulanan() {
    let periodeMode = $('#periodeMode').val();

    if (periodeMode !== 'monthly') {
        Swal.fire({
            icon: 'warning',
            title: 'Perhatian',
            text: 'Rekap Bulanan hanya tersedia untuk filter mode "Per Bulan"',
            confirmButtonText: 'OK'
        });
        return;
    }

    // Update bulan hidden field sebelum export
    updateBulanHidden();

    let bulan = $('#bulanHidden').val();

    if (!bulan) {
        Swal.fire({
            icon: 'error',
            title: 'Kesalahan',
            text: 'Pilih bulan dan tahun terlebih dahulu'
        });
        return;
    }

    // Build export URL for rekap bulanan
    let url = '{{ route("admin.export-rekap-bulanan") }}?bulan=' + bulan;

    // Show loading
    Swal.fire({
        title: 'Mempersiapkan Rekap Bulanan',
        text: 'Sedang memproses data...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Open export URL
    setTimeout(() => {
        window.open(url, '_blank');
        Swal.close();
    }, 1000);
}

// Fungsi untuk handle form submit dengan enter
$('#filterForm').on('keypress', function(e) {
    if (e.which === 13) {
        e.preventDefault();
        $(this).submit();
    }
});
</script>

<style>
/* Existing styles */
.progress {
    background-color: #e9ecef;
    border-radius: 0.375rem;
    overflow: hidden;
    height: 25px;
    position: relative;
}

.progress-bar {
    transition: width 0.6s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: bold;
    position: relative;
}

.progress-bar.bg-success {
    background-color: #198754 !important;
    background: linear-gradient(90deg, #198754, #20c997);
}

.progress-bar.bg-primary {
    background-color: #0d6efd !important;
    background: linear-gradient(90deg, #0d6efd, #3d8bfd);
}

.progress-bar.bg-warning {
    background-color: #ffc107 !important;
    background: linear-gradient(90deg, #ffc107, #ffda6a);
    color: #212529;
}

.progress-bar.bg-danger {
    background-color: #dc3545 !important;
    background: linear-gradient(90deg, #dc3545, #e35d6a);
}

.progress-text {
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
    color: white;
    font-weight: bold;
    font-size: 12px;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
}

.progress-bar.bg-warning .progress-text {
    color: #212529;
}

.avatar-placeholder {
    background-color: #e9ecef;
    color: #6c757d;
    display: flex;
    align-items: center;
    justify-content: center;
}

.empty-state {
    padding: 3rem 0;
}

.empty-state i {
    margin-bottom: 1rem;
}

.badge {
    font-size: 0.85em;
    font-weight: 500;
    padding: 0.35em 0.65em;
}

.table th {
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.8rem;
    letter-spacing: 0.5px;
    border-top: none;
    background-color: #f8f9fc;
}

.table td {
    vertical-align: middle;
}

.card-header {
    border-bottom: 1px solid #e3e6f0;
}

.btn-primary {
    background-color: #4e73df;
    border-color: #4e73df;
}

.btn-primary:hover {
    background-color: #2e59d9;
    border-color: #2653d4;
}

.btn-success {
    background-color: #1cc88a;
    border-color: #1cc88a;
}

.btn-success:hover {
    background-color: #17a673;
    border-color: #169b6b;
}

.btn-info {
    background-color: #36b9cc;
    border-color: #36b9cc;
}

.btn-info:hover {
    background-color: #2c9faf;
    border-color: #2a96a5;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.9rem;
    }

    .progress-text {
        font-size: 10px;
    }

    .badge {
        font-size: 0.8em;
        padding: 0.25em 0.5em;
    }

    .card .row > div {
        margin-bottom: 15px;
    }
}

/* Stats card animation */
.card {
    transition: transform 0.3s;
}

.card:hover {
    transform: translateY(-5px);
}

/* Chart cards */
.card .card-body.text-center h2 {
    font-size: 2.5rem;
    font-weight: bold;
}

/* Custom scrollbar for table */
.table-responsive::-webkit-scrollbar {
    height: 8px;
}

.table-responsive::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.table-responsive::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
}

.table-responsive::-webkit-scrollbar-thumb:hover {
    background: #555;
}

/* Filter form improvements */
.form-group label {
    font-weight: 500;
    font-size: 0.85rem;
    margin-bottom: 0.3rem;
}

.form-control {
    border: 1px solid #d1d3e2;
    border-radius: 0.375rem;
}

.form-control:focus {
    border-color: #4e73df;
    box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.25);
}

/* Grid layout improvements */
.row {
    margin-bottom: 0.5rem;
}

.row:last-child {
    margin-bottom: 0;
}

/* Badge styles for Bootstrap 5 */
.text-bg-success {
    background-color: #198754 !important;
    color: white !important;
}

.text-bg-primary {
    background-color: #0d6efd !important;
    color: white !important;
}

.text-bg-warning {
    background-color: #ffc107 !important;
    color: #212529 !important;
}

.text-bg-danger {
    background-color: #dc3545 !important;
    color: white !important;
}

.text-bg-info {
    background-color: #0dcaf0 !important;
    color: #212529 !important;
}

.text-bg-secondary {
    background-color: #6c757d !important;
    color: white !important;
}

/* Untuk badge dengan background dan text warna kontras */
.badge[class*="text-bg-"] {
    border: none;
}
</style>
@endpush
@endsection
