@extends('layouts.app')

@section('title', 'Generate QR Code')
@section('page-title', 'Generate QR Code')

@section('content')
<div class="row">
    <div class="col-lg-6">
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-qrcode me-2"></i>Buat QR Code Baru</h6>
            </div>
            <div class="card-body">
                <form id="generateForm">
                    @csrf

                    <div class="mb-3">
                        <label for="id_shift" class="form-label">Pilih Shift</label>
                        <select class="form-select" id="id_shift" name="id_shift" required>
                            <option value="">-- Pilih Shift --</option>
                            @foreach($shifts as $shift)
                            <option value="{{ $shift->id }}">
                                {{ $shift->nama_shift }} ({{ $shift->jam_mulai }} - {{ $shift->jam_akhir }})
                            </option>
                            @endforeach
                        </select>
                        <div class="form-text">Pilih shift untuk QR Code ini</div>
                    </div>

                    <div class="mb-3">
                        <label for="durasi_menit" class="form-label">Durasi Aktif (menit)</label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="durasi_menit"
                                   name="durasi_menit" min="1" max="60" value="5" required>
                            <span class="input-group-text">menit</span>
                        </div>
                        <div class="form-text">QR Code akan aktif selama menit yang ditentukan (1-60 menit)</div>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        QR Code akan expired otomatis setelah waktu berakhir dan tidak bisa digunakan lagi.
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg" id="generateBtn">
                            <i class="fas fa-qrcode me-2"></i>Generate QR Code
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-history me-2"></i>QR Code Aktif</h6>
            </div>
            <div class="card-body">
                @php
                    $activeBarcodes = \App\Models\Barcode::with('shift')
                        ->where('status', 'aktif')
                        ->where('waktu_akhir', '>', now())
                        ->orderBy('waktu_akhir', 'asc')
                        ->get();
                @endphp

                @if($activeBarcodes->count() > 0)
                <div class="list-group">
                    @foreach($activeBarcodes as $barcode)
                    <a href="{{ route('admin.barcode-display', $barcode->id) }}"
                       class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1 fw-bold">{{ $barcode->shift->nama_shift }}</h6>
                                <small class="text-muted">
                                    Kode: {{ substr($barcode->kode_barcode, 0, 8) }}...
                                </small>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-success mb-1">Aktif</span>
                                <div class="text-muted small">
                                    Expires: {{ $barcode->waktu_akhir->format('H:i') }}
                                </div>
                            </div>
                        </div>
                        <div class="mt-2">
                            <small class="text-muted">
                                <i class="fas fa-clock me-1"></i>
                                Aktif sampai: {{ $barcode->waktu_akhir->diffForHumans() }}
                            </small>
                        </div>
                    </a>
                    @endforeach
                </div>
                @else
                <div class="text-center py-5">
                    <i class="fas fa-qrcode fa-4x text-muted mb-3"></i>
                    <p class="text-muted">Tidak ada QR Code yang aktif</p>
                    <small class="text-muted">Generate QR Code baru untuk memulai</small>
                </div>
                @endif

                <hr class="my-4">

                <div class="text-center">
                    <small class="text-muted">
                        <i class="fas fa-lightbulb me-1"></i>
                        QR Code yang sudah expired akan otomatis dinonaktifkan
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loadingOverlay" class="position-fixed top-0 start-0 w-100 h-100 bg-dark bg-opacity-75 d-none justify-content-center align-items-center" style="z-index: 9999;">
    <div class="text-center text-white">
        <div class="spinner-border text-primary mb-3" style="width: 3rem; height: 3rem;" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <h4>Generating QR Code...</h4>
        <p>Harap tunggu sebentar</p>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('#generateForm').on('submit', function(e) {
        e.preventDefault();

        // Show loading
        $('#loadingOverlay').removeClass('d-none').addClass('d-flex');
        $('#generateBtn').prop('disabled', true);

        $.ajax({
            url: '{{ route("admin.generate-barcode.post") }}',
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success && response.redirect) {
                    // Redirect to barcode display page
                    window.location.href = response.redirect;
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan saat generate QR Code'
                    });
                }
            },
            error: function(xhr) {
                // Hide loading
                $('#loadingOverlay').addClass('d-none').removeClass('d-flex');
                $('#generateBtn').prop('disabled', false);

                let message = 'Terjadi kesalahan';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    message = '';
                    for (const field in errors) {
                        message += errors[field][0] + '\n';
                    }
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: message
                });
            }
        });
    });
});
</script>
@endpush
@endsection
