@extends('layouts.app')

@section('title', 'Data Shift')
@section('page-title', 'Data Shift')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Data Shift</h1>
            <p class="text-muted mb-0">Kelola jadwal shift kerja karyawan</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahShiftModal">
            <i class="fas fa-plus me-2"></i>Tambah Shift
        </button>
    </div>

    <!-- Shift Cards -->
    <div class="row">
        @forelse($shifts as $shift)
        <div class="col-xl-6 col-lg-12 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">
                            <i class="fas fa-clock me-2"></i>{{ $shift->nama_shift }}
                        </h5>
                        <small class="text-muted">
                            {{ $shift->jam_mulai }} - {{ $shift->jam_akhir }}
                        </small>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-info me-2">{{ $shift->batas_telat }} menit toleransi</span>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <button class="dropdown-item text-primary" onclick="editShift({{ $shift->id }})">
                                        <i class="fas fa-edit me-2"></i>Edit Shift
                                    </button>
                                </li>
                                <li>
                                    <button class="dropdown-item text-danger" onclick="confirmHapusShift({{ $shift->id }}, '{{ $shift->nama_shift }}')">
                                        <i class="fas fa-trash me-2"></i>Hapus Shift
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-play text-primary me-2"></i>
                                <div>
                                    <small class="text-muted">Jam Mulai</small>
                                    <div class="fw-bold h5 mb-0">{{ $shift->jam_mulai }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-stop text-danger me-2"></i>
                                <div>
                                    <small class="text-muted">Jam Selesai</small>
                                    <div class="fw-bold h5 mb-0">{{ $shift->jam_akhir }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-hourglass-half text-warning me-2"></i>
                                <div>
                                    <small class="text-muted">Durasi Kerja</small>
                                    <div class="fw-bold h5 mb-0">
                                        @php
                                            $jamMulai = \Carbon\Carbon::parse($shift->jam_mulai);
                                            $jamAkhir = \Carbon\Carbon::parse($shift->jam_akhir);
                                            // Handle jika melewati tengah malam
                                            if ($jamAkhir < $jamMulai) {
                                                $jamAkhir->addDay();
                                            }
                                            $durasiJam = $jamMulai->diffInHours($jamAkhir);
                                        @endphp
                                        {{ $durasiJam }} jam
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-calendar-check text-success me-2"></i>
                                <div>
                                    <small class="text-muted">Total Absensi</small>
                                    <div class="fw-bold h5 mb-0">{{ $shift->absensi->count() }} kali</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-light mt-3">
                        <div class="d-flex">
                            <i class="fas fa-info-circle text-info me-3 mt-1"></i>
                            <div>
                                <small class="text-muted">
                                    Karyawan dianggap <strong>terlambat</strong> jika absen melebihi
                                    <strong>{{ $shift->batas_telat }} menit</strong> dari jam mulai shift.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-clock fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">Belum ada data shift</h5>
                    <p class="text-muted">Tambahkan shift baru untuk memulai</p>
                </div>
            </div>
        </div>
        @endforelse
    </div>
</div>

<!-- Modal Tambah Shift -->
<div class="modal fade" id="tambahShiftModal" tabindex="-1" aria-labelledby="tambahShiftModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tambahShiftModalLabel">
                    <i class="fas fa-plus me-2"></i>Tambah Shift Baru
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="tambahShiftForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nama_shift" class="form-label">Nama Shift *</label>
                        <input type="text" name="nama_shift" class="form-control" id="nama_shift"
                               placeholder="Contoh: Shift Pagi, Shift Sore, Shift Malam" required maxlength="50">
                        <div class="form-text">
                            <i class="fas fa-lightbulb me-1"></i>
                            Beri nama yang jelas, contoh: "Shift Pagi", "Shift Siang", "Shift Malam"
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="jam_mulai" class="form-label">Jam Mulai *</label>
                            <input type="time" name="jam_mulai" class="form-control" id="jam_mulai" required>
                            <div class="form-text">Format 24 jam (00:00 - 23:59)</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="jam_akhir" class="form-label">Jam Akhir *</label>
                            <input type="time" name="jam_akhir" class="form-control" id="jam_akhir" required>
                            <div class="form-text">Bisa melewati tengah malam (contoh: 22:00 - 06:00)</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="batas_telat" class="form-label">Batas Keterlambatan *</label>
                        <div class="input-group">
                            <input type="number" name="batas_telat" class="form-control" id="batas_telat"
                                   value="15" min="0" max="120" required>
                            <span class="input-group-text">menit</span>
                        </div>
                        <div class="form-text">
                            <i class="fas fa-info-circle me-1"></i>
                            Karyawan dianggap terlambat jika absen melebihi batas ini (0 = tidak ada toleransi)
                        </div>
                    </div>

                    <!-- Preview Durasi -->
                    <div class="alert alert-info" id="durasiPreview" style="display: none;">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-calculator me-2"></i>
                            <div>
                                <strong>Durasi Shift:</strong>
                                <span id="durasiText">-</span> jam
                                <div class="small text-muted mt-1" id="durasiNote"></div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <small>
                            Pastikan jam akhir lebih besar dari jam mulai. Jika shift melewati tengah malam,
                            sistem akan menghitung durasi dengan benar.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="submitShiftBtn">
                        <span id="submitBtnText">Simpan Shift</span>
                        <span id="submitBtnLoading" class="d-none">
                            <span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Shift -->
<div class="modal fade" id="editShiftModal" tabindex="-1" aria-labelledby="editShiftModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editShiftModalLabel">
                    <i class="fas fa-edit me-2"></i>Edit Shift
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editShiftForm" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_shift_id" name="id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_nama_shift" class="form-label">Nama Shift *</label>
                        <input type="text" name="nama_shift" class="form-control" id="edit_nama_shift"
                               placeholder="Contoh: Shift Pagi, Shift Sore, Shift Malam" required maxlength="50">
                        <div class="form-text">
                            <i class="fas fa-lightbulb me-1"></i>
                            Beri nama yang jelas, contoh: "Shift Pagi", "Shift Siang", "Shift Malam"
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_jam_mulai" class="form-label">Jam Mulai *</label>
                            <input type="time" name="jam_mulai" class="form-control" id="edit_jam_mulai" required>
                            <div class="form-text">Format 24 jam (00:00 - 23:59)</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="edit_jam_akhir" class="form-label">Jam Akhir *</label>
                            <input type="time" name="jam_akhir" class="form-control" id="edit_jam_akhir" required>
                            <div class="form-text">Bisa melewati tengah malam (contoh: 22:00 - 06:00)</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="edit_batas_telat" class="form-label">Batas Keterlambatan *</label>
                        <div class="input-group">
                            <input type="number" name="batas_telat" class="form-control" id="edit_batas_telat"
                                   min="0" max="120" required>
                            <span class="input-group-text">menit</span>
                        </div>
                        <div class="form-text">
                            <i class="fas fa-info-circle me-1"></i>
                            Karyawan dianggap terlambat jika absen melebihi batas ini (0 = tidak ada toleransi)
                        </div>
                    </div>

                    <!-- Preview Durasi Edit -->
                    <div class="alert alert-info" id="editDurasiPreview" style="display: none;">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-calculator me-2"></i>
                            <div>
                                <strong>Durasi Shift:</strong>
                                <span id="editDurasiText">-</span> jam
                                <div class="small text-muted mt-1" id="editDurasiNote"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="updateShiftBtn">
                        <span id="updateBtnText">Perbarui Shift</span>
                        <span id="updateBtnLoading" class="d-none">
                            <span class="spinner-border spinner-border-sm me-1"></span> Memperbarui...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Calculate and preview shift duration for add form
    function calculateDuration() {
        const jamMulai = $('#jam_mulai').val();
        const jamAkhir = $('#jam_akhir').val();

        if (jamMulai && jamAkhir) {
            const mulai = new Date(`2000-01-01T${jamMulai}`);
            let akhir = new Date(`2000-01-01T${jamAkhir}`);

            // Handle jika jam akhir lebih kecil (melewati tengah malam)
            if (akhir < mulai) {
                akhir.setDate(akhir.getDate() + 1);
            }

            const diffMs = akhir - mulai;
            const diffHours = Math.floor(diffMs / (1000 * 60 * 60));
            const diffMinutes = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));

            let durasiText = `${diffHours} jam`;
            if (diffMinutes > 0) {
                durasiText += ` ${diffMinutes} menit`;
            }

            $('#durasiText').text(durasiText);

            // Add note if crossing midnight
            if (jamAkhir < jamMulai) {
                $('#durasiNote').html('<i class="fas fa-moon me-1"></i> Shift ini melewati tengah malam');
            } else {
                $('#durasiNote').html('<i class="fas fa-sun me-1"></i> Shift dalam 1 hari yang sama');
            }

            $('#durasiPreview').fadeIn();
        } else {
            $('#durasiPreview').fadeOut();
        }
    }

    // Calculate duration for edit form
    function calculateEditDuration() {
        const jamMulai = $('#edit_jam_mulai').val();
        const jamAkhir = $('#edit_jam_akhir').val();

        if (jamMulai && jamAkhir) {
            const mulai = new Date(`2000-01-01T${jamMulai}`);
            let akhir = new Date(`2000-01-01T${jamAkhir}`);

            if (akhir < mulai) {
                akhir.setDate(akhir.getDate() + 1);
            }

            const diffMs = akhir - mulai;
            const diffHours = Math.floor(diffMs / (1000 * 60 * 60));
            const diffMinutes = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));

            let durasiText = `${diffHours} jam`;
            if (diffMinutes > 0) {
                durasiText += ` ${diffMinutes} menit`;
            }

            $('#editDurasiText').text(durasiText);

            if (jamAkhir < jamMulai) {
                $('#editDurasiNote').html('<i class="fas fa-moon me-1"></i> Shift ini melewati tengah malam');
            } else {
                $('#editDurasiNote').html('<i class="fas fa-sun me-1"></i> Shift dalam 1 hari yang sama');
            }

            $('#editDurasiPreview').fadeIn();
        } else {
            $('#editDurasiPreview').fadeOut();
        }
    }

    // Event listeners for time changes
    $('#jam_mulai, #jam_akhir').on('change', calculateDuration);
    $('#edit_jam_mulai, #edit_jam_akhir').on('change', calculateEditDuration);

    // Handle tambah shift form submission with AJAX
    $('#tambahShiftForm').on('submit', function(e) {
        e.preventDefault();

        const submitBtn = $('#submitShiftBtn');
        const submitBtnText = $('#submitBtnText');
        const submitBtnLoading = $('#submitBtnLoading');

        // Validate time
        const jamMulai = $('#jam_mulai').val();
        const jamAkhir = $('#jam_akhir').val();

        if (jamMulai && jamAkhir) {
            if (jamMulai === jamAkhir) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Jam tidak valid',
                    text: 'Jam mulai dan jam akhir tidak boleh sama'
                });
                return;
            }
        }

        // Show loading state
        submitBtnText.addClass('d-none');
        submitBtnLoading.removeClass('d-none');
        submitBtn.prop('disabled', true);

        $.ajax({
            url: '{{ route("admin.simpan-shift") }}',
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                // Hide modal
                $('#tambahShiftModal').modal('hide');

                // Show success message
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: response.message || 'Shift berhasil ditambahkan',
                    timer: 2000,
                    showConfirmButton: false
                });

                // Reset form
                $('#tambahShiftForm')[0].reset();
                $('#durasiPreview').hide();

                // Reload page after delay
                setTimeout(function() {
                    window.location.reload();
                }, 1500);
            },
            error: function(xhr) {
                // Reset button state
                submitBtnText.removeClass('d-none');
                submitBtnLoading.addClass('d-none');
                submitBtn.prop('disabled', false);

                let errorMessage = 'Terjadi kesalahan';

                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    errorMessage = '';

                    for (const field in errors) {
                        errorMessage += errors[field][0] + '\n';
                    }
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMessage
                });
            }
        });
    });

    // Function to edit shift
    window.editShift = function(id) {
        // Show loading state
        $('#editShiftModal').modal('show');
        $('#updateBtnText').addClass('d-none');
        $('#updateBtnLoading').removeClass('d-none');
        $('#updateShiftBtn').prop('disabled', true);

        $.ajax({
            url: '{{ route("admin.edit-shift", ":id") }}'.replace(':id', id),
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    const shift = response.data;

                    // Fill form with shift data
                    $('#edit_shift_id').val(shift.id);
                    $('#edit_nama_shift').val(shift.nama_shift);
                    $('#edit_jam_mulai').val(shift.jam_mulai);
                    $('#edit_jam_akhir').val(shift.jam_akhir);
                    $('#edit_batas_telat').val(shift.batas_telat);

                    // Calculate duration
                    calculateEditDuration();

                    // Reset button state
                    $('#updateBtnText').removeClass('d-none');
                    $('#updateBtnLoading').addClass('d-none');
                    $('#updateShiftBtn').prop('disabled', false);
                } else {
                    $('#editShiftModal').modal('hide');
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Gagal memuat data shift'
                    });
                }
            },
            error: function() {
                $('#editShiftModal').modal('hide');
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan saat memuat data shift'
                });
            }
        });
    };

    // Handle edit shift form submission
    $('#editShiftForm').on('submit', function(e) {
        e.preventDefault();

        const id = $('#edit_shift_id').val();
        const updateBtn = $('#updateShiftBtn');
        const updateBtnText = $('#updateBtnText');
        const updateBtnLoading = $('#updateBtnLoading');

        // Validate time
        const jamMulai = $('#edit_jam_mulai').val();
        const jamAkhir = $('#edit_jam_akhir').val();

        if (jamMulai && jamAkhir) {
            if (jamMulai === jamAkhir) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Jam tidak valid',
                    text: 'Jam mulai dan jam akhir tidak boleh sama'
                });
                return;
            }
        }

        // Show loading state
        updateBtnText.addClass('d-none');
        updateBtnLoading.removeClass('d-none');
        updateBtn.prop('disabled', true);

        $.ajax({
            url: '{{ route("admin.update-shift", ":id") }}'.replace(':id', id),
            method: 'PUT',
            data: $(this).serialize(),
            success: function(response) {
                // Hide modal
                $('#editShiftModal').modal('hide');

                // Show success message
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: response.message || 'Shift berhasil diperbarui',
                    timer: 2000,
                    showConfirmButton: false
                });

                // Reload page after delay
                setTimeout(function() {
                    window.location.reload();
                }, 1500);
            },
            error: function(xhr) {
                // Reset button state
                updateBtnText.removeClass('d-none');
                updateBtnLoading.addClass('d-none');
                updateBtn.prop('disabled', false);

                let errorMessage = 'Terjadi kesalahan';

                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    errorMessage = '';

                    for (const field in errors) {
                        errorMessage += errors[field][0] + '\n';
                    }
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMessage
                });
            }
        });
    });

    // Function to delete shift
    window.confirmHapusShift = function(id, nama) {
        Swal.fire({
            title: 'Hapus Shift?',
            html: `<strong>${nama}</strong><br><br>Shift yang sudah digunakan tidak dapat dihapus.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                hapusShift(id);
            }
        });
    };

    function hapusShift(id) {
        $.ajax({
            url: '{{ route("admin.hapus-shift", ":id") }}'.replace(':id', id),
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Terhapus!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    });

                    setTimeout(function() {
                        window.location.reload();
                    }, 1500);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: response.message
                    });
                }
            },
            error: function(xhr) {
                let errorMessage = 'Terjadi kesalahan';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMessage
                });
            }
        });
    }

    // Reset form when modal is closed
    $('#tambahShiftModal').on('hidden.bs.modal', function() {
        $('#tambahShiftForm')[0].reset();
        $('#durasiPreview').hide();
        $('#submitBtnText').removeClass('d-none');
        $('#submitBtnLoading').addClass('d-none');
        $(this).find('button[type="submit"]').prop('disabled', false);
    });

    $('#editShiftModal').on('hidden.bs.modal', function() {
        $('#editShiftForm')[0].reset();
        $('#editDurasiPreview').hide();
        $('#updateBtnText').removeClass('d-none');
        $('#updateBtnLoading').addClass('d-none');
        $(this).find('button[type="submit"]').prop('disabled', false);
    });

    // Auto focus on nama shift when modal opens
    $('#tambahShiftModal').on('shown.bs.modal', function() {
        $('#nama_shift').focus();
    });

    $('#editShiftModal').on('shown.bs.modal', function() {
        $('#edit_nama_shift').focus();
    });
});
</script>
@endpush

<style>
/* Custom styling for better UX */
#durasiPreview, #editDurasiPreview {
    border-left: 4px solid #36b9cc;
}

.form-text i {
    color: #6e707e;
}

.alert-warning {
    border-left: 4px solid #f6c23e;
}

.input-group-text {
    background-color: #f8f9fc;
}

.dropdown-toggle::after {
    display: none;
}

.dropdown-menu {
    min-width: 180px;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    border: 1px solid rgba(0, 0, 0, 0.15);
}

.dropdown-item {
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
}

.dropdown-item:hover {
    background-color: #f8f9fa;
}

.dropdown-item.text-danger:hover {
    background-color: #f8d7da;
    color: #721c24 !important;
}

.dropdown-item.text-primary:hover {
    background-color: #e3f2fd;
    color: #0d6efd !important;
}
</style>
@endsection
