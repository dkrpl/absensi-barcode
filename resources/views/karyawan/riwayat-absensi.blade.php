@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Riwayat Absensi</h1>
        <a href="{{ route('karyawan.dashboard') }}" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Riwayat Absensi {{ Auth::user()->nama }}</h6>
            <form class="form-inline" id="filterForm">
                <input type="date" class="form-control form-control-sm mr-2" id="tanggalFilter">
                <button type="button" class="btn btn-sm btn-info" onclick="filterRiwayat()">
                    <i class="fas fa-filter"></i> Filter
                </button>
                <button type="button" class="btn btn-sm btn-secondary ml-2" onclick="resetFilter()">
                    <i class="fas fa-redo"></i> Reset
                </button>
            </form>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="riwayatTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Shift</th>
                            <th>Waktu Absen</th>
                            <th>Status</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($riwayat as $index => $absensi)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $absensi->tanggal_absen->format('d/m/Y') }}</td>
                            <td>{{ $absensi->shift->nama_shift }}</td>
                            <td>{{ $absensi->waktu_absen->format('H:i:s') }}</td>
                            <td>
                                <span class="badge bg-{{ $absensi->status == 'hadir' ? 'success' : 'warning' }}">
                                    {{ $absensi->status == 'hadir' ? 'Hadir' : 'Terlambat' }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $jamMulai = \Carbon\Carbon::parse($absensi->shift->jam_mulai);
                                    $waktuAbsen = \Carbon\Carbon::parse($absensi->waktu_absen->format('H:i:s'));
                                    $selisih = $waktuAbsen->diffInMinutes($jamMulai, false);
                                @endphp
                                @if($absensi->status == 'terlambat')
                                    Terlambat {{ abs($selisih) }} menit
                                @else
                                    Tepat waktu
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $riwayat->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function filterRiwayat() {
    let tanggal = $('#tanggalFilter').val();
    if (tanggal) {
        window.location.href = '{{ route("karyawan.riwayat-absensi") }}?tanggal=' + tanggal;
    }
}

function resetFilter() {
    window.location.href = '{{ route("karyawan.riwayat-absensi") }}';
}

// Export to CSV (optional)
function exportToCSV() {
    let csv = [];
    let rows = document.querySelectorAll("#riwayatTable tr");

    for (let i = 0; i < rows.length; i++) {
        let row = [], cols = rows[i].querySelectorAll("td, th");

        for (let j = 0; j < cols.length; j++) {
            row.push(cols[j].innerText);
        }

        csv.push(row.join(","));
    }

    // Download CSV file
    downloadCSV(csv.join("\n"), 'riwayat_absensi.csv');
}

function downloadCSV(csv, filename) {
    let csvFile = new Blob([csv], {type: "text/csv"});
    let downloadLink = document.createElement("a");

    downloadLink.download = filename;
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.style.display = "none";

    document.body.appendChild(downloadLink);
    downloadLink.click();
}
</script>
@endpush
@endsection
