@extends('layouts.app')

@section('title', 'Data Karyawan')
@section('page-title', 'Data Karyawan')

@section('content')
<div class="container-fluid">
    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Karyawan
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $totalKaryawan = \App\Models\User::where('role', 'karyawan')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Karyawan Aktif
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $aktif = \App\Models\User::where('role', 'karyawan')->whereNull('tanggal_keluar')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Karyawan Kontrak
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $kontrak = \App\Models\User::where('role', 'karyawan')
                                    ->where('status_karyawan', 'kontrak')
                                    ->whereNull('tanggal_keluar')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-contract fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Departemen Terbanyak
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                @php
                                    $topDept = \App\Models\User::where('role', 'karyawan')
                                        ->whereNotNull('departemen')
                                        ->select('departemen', \DB::raw('count(*) as total'))
                                        ->groupBy('departemen')
                                        ->orderByDesc('total')
                                        ->first();
                                @endphp
                                {{ $topDept ? $topDept->departemen : '-' }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-building fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter me-2"></i>Filter Data
            </h6>
            <div>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#tambahKaryawanModal">
                    <i class="fas fa-user-plus me-1"></i>Tambah Karyawan
                </button>
                <button class="btn btn-success btn-sm" onclick="exportData()">
                    <i class="fas fa-file-excel me-1"></i>Export
                </button>
            </div>
        </div>
        <div class="card-body">
            <form id="filterForm" method="GET" action="{{ route('admin.data-karyawan') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ request('status') == 'nonaktif' ? 'selected' : '' }}>Non Aktif</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Departemen</label>
                    <input type="text" name="departemen" class="form-control"
                           list="filterDepartemenList"
                           placeholder="Semua departemen"
                           value="{{ request('departemen') }}">
                    <datalist id="filterDepartemenList">
                        @foreach($departemenList ?? [] as $dept)
                        <option value="{{ $dept }}">{{ $dept }}</option>
                        @endforeach
                    </datalist>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Pencarian</label>
                    <div class="input-group">
                        <input type="text" name="search" class="form-control"
                               placeholder="Cari nama, NIP, departemen..."
                               value="{{ request('search') }}">
                        <button class="btn btn-outline-secondary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Tampilkan</label>
                    <select name="per_page" class="form-select" onchange="this.form.submit()">
                        <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15 Data</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 Data</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 Data</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 Data</option>
                    </select>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-table me-2"></i>Daftar Karyawan
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th width="30">#</th>
                            <th width="60">Foto</th>
                            <th>Nama</th>
                            <th>NIK</th>
                            <th>Jabatan</th>
                            <th>Departemen</th>
                            <th>Status</th>
                            <th>Kontak</th>
                            <th>Bergabung</th>
                            <th width="150">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($karyawan as $index => $k)
                        <tr class="{{ $k->tanggal_keluar ? 'table-danger' : '' }}">
                            <td>{{ ($karyawan->currentPage() - 1) * $karyawan->perPage() + $loop->iteration }}</td>
                            <td>
                                @if($k->foto)
                                <img src="{{ asset('storage/' . $k->foto) }}"
                                     alt="{{ $k->nama }}"
                                     class="rounded-circle"
                                     style="width: 40px; height: 40px; object-fit: cover;">
                                @else
                                <div class="avatar-circle"
                                     style="background-color: {{ '#' . substr(md5($k->nama), 0, 6) }};">
                                    {{ substr($k->nama, 0, 1) }}
                                </div>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold">{{ $k->nama }}</div>
                                <small class="text-muted">
                                    <i class="fas fa-{{ $k->jenis_kelamin == 'L' ? 'mars' : 'venus' }} me-1"></i>
                                    {{ $k->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}
                                    @if($k->tanggal_lahir)
                                    | {{ \Carbon\Carbon::parse($k->tanggal_lahir)->age }} thn
                                    @endif
                                </small>
                            </td>
                            <td>
                                @if($k->nip)
                                <code class="bg-light p-1 rounded">{{ $k->nip }}</code>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($k->jabatan)
                                <span class="badge bg-info text-dark">{{ $k->jabatan }}</span>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($k->departemen)
                                <span class="badge bg-secondary">{{ $k->departemen }}</span>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $k->status_karyawan == 'tetap' ? 'success' : ($k->status_karyawan == 'kontrak' ? 'warning' : 'secondary') }}">
                                    {{ ucfirst($k->status_karyawan) }}
                                </span>
                                @if($k->tanggal_keluar)
                                <br>
                                <small class="text-danger">Keluar: {{ \Carbon\Carbon::parse($k->tanggal_keluar)->format('d/m/Y') }}</small>
                                @endif
                            </td>
                            <td>
                                <div>
                                    @if($k->email)
                                    <small><i class="fas fa-envelope me-1"></i>{{ $k->email }}</small><br>
                                    @endif
                                    @if($k->no_telepon)
                                    <small><i class="fas fa-phone me-1"></i>{{ $k->no_telepon }}</small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div>{{ $k->tanggal_masuk ? \Carbon\Carbon::parse($k->tanggal_masuk)->format('d/m/Y') : '-' }}</div>
                                @if($k->tanggal_masuk)
                                <small class="text-muted">
                                    {{ \Carbon\Carbon::parse($k->tanggal_masuk)->diffForHumans() }}
                                </small>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-info btn-sm"
                                            onclick="showDetail({{ $k->id }})"
                                            title="Detail" data-bs-toggle="tooltip">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button" class="btn btn-warning btn-sm"
                                            onclick="editKaryawan({{ $k->id }})"
                                            title="Edit" data-bs-toggle="tooltip">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    @if(!$k->tanggal_keluar)
                                    <button type="button" class="btn btn-danger btn-sm"
                                            onclick="nonaktifkan({{ $k->id }}, '{{ addslashes($k->nama) }}')"
                                            title="Nonaktifkan" data-bs-toggle="tooltip">
                                        <i class="fas fa-user-slash"></i>
                                    </button>
                                    @else
                                    <button type="button" class="btn btn-success btn-sm"
                                            onclick="aktifkan({{ $k->id }}, '{{ addslashes($k->nama) }}')"
                                            title="Aktifkan Kembali" data-bs-toggle="tooltip">
                                        <i class="fas fa-user-check"></i>
                                    </button>
                                    @endif

                                    <button type="button" class="btn btn-outline-danger btn-sm"
                                            onclick="hapusKaryawan({{ $k->id }}, '{{ addslashes($k->nama) }}')"
                                            title="Hapus Permanen" data-bs-toggle="tooltip">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-4">
                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Belum ada data karyawan</p>
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahKaryawanModal">
                                    <i class="fas fa-user-plus me-2"></i>Tambah Karyawan Pertama
                                </button>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($karyawan->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">
                    Menampilkan {{ $karyawan->firstItem() }} - {{ $karyawan->lastItem() }} dari {{ $karyawan->total() }} karyawan
                </div>
                <nav aria-label="Page navigation">
                    {{ $karyawan->appends(request()->query())->links('pagination::bootstrap-5') }}
                </nav>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Tambah Karyawan -->
<div class="modal fade" id="tambahKaryawanModal" tabindex="-1" aria-labelledby="tambahKaryawanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tambahKaryawanModalLabel">
                    <i class="fas fa-user-plus me-2"></i>Tambah Karyawan Baru
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formTambahKaryawan" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="mb-3 text-primary">Data Pribadi</h6>

                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nama" name="nama" required maxlength="100">
                                <div class="invalid-feedback" id="namaError"></div>
                            </div>

                            <div class="mb-3">
                                <label for="nip" class="form-label">NIK</label>
                                <input type="text" class="form-control" id="nip" name="nip" maxlength="20">
                                <div class="form-text">Nomor Induk Kependudukan</div>
                                <div class="invalid-feedback" id="nipError"></div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                                    <select class="form-select" id="jenis_kelamin" name="jenis_kelamin">
                                        <option value="">Pilih Jenis Kelamin</option>
                                        <option value="L">Laki-laki</option>
                                        <option value="P">Perempuan</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="status_karyawan" class="form-label">Status Karyawan</label>
                                    <select class="form-select" id="status_karyawan" name="status_karyawan">
                                        <option value="probation">Probation</option>
                                        <option value="kontrak">Kontrak</option>
                                        <option value="tetap">Tetap</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="tempat_lahir" class="form-label">Tempat Lahir</label>
                                    <input type="text" class="form-control" id="tempat_lahir" name="tempat_lahir" maxlength="100">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                                    <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="alamat" class="form-label">Alamat</label>
                                <textarea class="form-control" id="alamat" name="alamat" rows="2"></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="no_telepon" class="form-label">No. Telepon</label>
                                <input type="text" class="form-control" id="no_telepon" name="no_telepon" maxlength="20">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h6 class="mb-3 text-primary">Data Login & Pekerjaan</h6>

                            <div class="mb-3">
                                <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="username" name="username" required maxlength="50">
                                <div class="form-text">Hanya huruf, angka, underscore</div>
                                <div class="invalid-feedback" id="usernameError"></div>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" maxlength="100">
                                <div class="invalid-feedback" id="emailError"></div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="password" name="password" required minlength="6">
                                        <button class="btn btn-outline-secondary toggle-password" type="button">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <div class="invalid-feedback" id="passwordError"></div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="password_confirmation" class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                                    <div class="invalid-feedback" id="passwordConfirmationError"></div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="jabatan" class="form-label">Jabatan</label>
                                    <input type="text" class="form-control" id="jabatan" name="jabatan" maxlength="100">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="departemen" class="form-label">Departemen</label>
                                    <input type="text" class="form-control" id="departemen" name="departemen"
                                           list="departemenList" maxlength="100" placeholder="Masukkan departemen">
                                    <datalist id="departemenList">
                                        @foreach($departemenList ?? [] as $dept)
                                        <option value="{{ $dept }}">{{ $dept }}</option>
                                        @endforeach
                                    </datalist>
                                    <div class="form-text">Ketik atau pilih dari daftar departemen yang tersedia</div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="tanggal_masuk" class="form-label">Tanggal Masuk</label>
                                <input type="date" class="form-control" id="tanggal_masuk" name="tanggal_masuk" value="{{ date('Y-m-d') }}">
                            </div>

                            <div class="mb-3">
                                <label for="foto" class="form-label">Foto Profil</label>
                                <input type="file" class="form-control" id="foto" name="foto" accept="image/*">
                                <div class="form-text">Maksimal 2MB (jpg, jpeg, png)</div>
                                <div class="invalid-feedback" id="fotoError"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="submitBtnTambah">
                        <span id="submitBtnText">Simpan</span>
                        <span id="submitBtnLoading" class="d-none">
                            <span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Karyawan -->
<div class="modal fade" id="editKaryawanModal" tabindex="-1" aria-labelledby="editKaryawanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editKaryawanModalLabel">
                    <i class="fas fa-edit me-2"></i>Edit Data Karyawan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditKaryawan" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" value="PUT">
                <input type="hidden" id="edit_id" name="id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="mb-3 text-primary">Data Pribadi</h6>

                            <div class="mb-3">
                                <label for="edit_nama" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_nama" name="nama" required maxlength="100">
                                <div class="invalid-feedback" id="editNamaError"></div>
                            </div>

                            <div class="mb-3">
                                <label for="edit_nip" class="form-label">NIP</label>
                                <input type="text" class="form-control" id="edit_nip" name="nip" maxlength="20">
                                <div class="invalid-feedback" id="editNipError"></div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="edit_jenis_kelamin" class="form-label">Jenis Kelamin</label>
                                    <select class="form-select" id="edit_jenis_kelamin" name="jenis_kelamin">
                                        <option value="">Pilih Jenis Kelamin</option>
                                        <option value="L">Laki-laki</option>
                                        <option value="P">Perempuan</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="edit_status_karyawan" class="form-label">Status Karyawan</label>
                                    <select class="form-select" id="edit_status_karyawan" name="status_karyawan">
                                        <option value="probation">Probation</option>
                                        <option value="kontrak">Kontrak</option>
                                        <option value="tetap">Tetap</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="edit_tempat_lahir" class="form-label">Tempat Lahir</label>
                                    <input type="text" class="form-control" id="edit_tempat_lahir" name="tempat_lahir" maxlength="100">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="edit_tanggal_lahir" class="form-label">Tanggal Lahir</label>
                                    <input type="date" class="form-control" id="edit_tanggal_lahir" name="tanggal_lahir">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="edit_alamat" class="form-label">Alamat</label>
                                <textarea class="form-control" id="edit_alamat" name="alamat" rows="2"></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="edit_no_telepon" class="form-label">No. Telepon</label>
                                <input type="text" class="form-control" id="edit_no_telepon" name="no_telepon" maxlength="20">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h6 class="mb-3 text-primary">Data Login & Pekerjaan</h6>

                            <div class="mb-3">
                                <label for="edit_username" class="form-label">Username <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_username" name="username" required maxlength="50">
                                <div class="invalid-feedback" id="editUsernameError"></div>
                            </div>

                            <div class="mb-3">
                                <label for="edit_email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="edit_email" name="email" maxlength="100">
                                <div class="invalid-feedback" id="editEmailError"></div>
                            </div>

                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Kosongkan password jika tidak ingin mengubah
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="edit_password" class="form-label">Password Baru</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="edit_password" name="password" minlength="6">
                                        <button class="btn btn-outline-secondary toggle-password" type="button">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <div class="invalid-feedback" id="editPasswordError"></div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="edit_password_confirmation" class="form-label">Konfirmasi Password</label>
                                    <input type="password" class="form-control" id="edit_password_confirmation" name="password_confirmation">
                                    <div class="invalid-feedback" id="editPasswordConfirmationError"></div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="edit_jabatan" class="form-label">Jabatan</label>
                                    <input type="text" class="form-control" id="edit_jabatan" name="jabatan" maxlength="100">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="edit_departemen" class="form-label">Departemen</label>
                                    <input type="text" class="form-control" id="edit_departemen" name="departemen"
                                           list="editDepartemenList" maxlength="100">
                                    <datalist id="editDepartemenList">
                                        @foreach($departemenList ?? [] as $dept)
                                        <option value="{{ $dept }}">{{ $dept }}</option>
                                        @endforeach
                                    </datalist>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="edit_tanggal_masuk" class="form-label">Tanggal Masuk</label>
                                    <input type="date" class="form-control" id="edit_tanggal_masuk" name="tanggal_masuk">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="edit_tanggal_keluar" class="form-label">Tanggal Keluar</label>
                                    <input type="date" class="form-control" id="edit_tanggal_keluar" name="tanggal_keluar">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="edit_foto" class="form-label">Foto Profil</label>
                                <input type="file" class="form-control" id="edit_foto" name="foto" accept="image/*">
                                <div class="form-text">Maksimal 2MB (jpg, jpeg, png)</div>
                                <div id="currentFoto" class="mt-2"></div>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" id="hapus_foto" name="hapus_foto" value="1">
                                    <label class="form-check-label" for="hapus_foto">
                                        Hapus foto saat ini
                                    </label>
                                </div>
                                <div class="invalid-feedback" id="editFotoError"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="submitBtnEdit">
                        <span id="submitBtnEditText">Perbarui</span>
                        <span id="submitBtnEditLoading" class="d-none">
                            <span class="spinner-border spinner-border-sm me-1"></span> Memperbarui...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Detail Karyawan -->
<div class="modal fade" id="detailKaryawanModal" tabindex="-1" aria-labelledby="detailKaryawanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailKaryawanModalLabel">
                    <i class="fas fa-user-circle me-2"></i>Detail Karyawan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Loading State -->
                <div class="text-center py-5" id="detailLoading">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Memuat data karyawan...</p>
                </div>

                <!-- Error State -->
                <div class="text-center py-5" id="detailError" style="display: none;">
                    <i class="fas fa-exclamation-circle fa-3x text-danger mb-3"></i>
                    <p class="text-danger" id="detailErrorMessage">Gagal memuat data karyawan</p>
                    <button class="btn btn-primary btn-sm" onclick="retryLoadDetail()">
                        <i class="fas fa-redo me-1"></i>Coba Lagi
                    </button>
                </div>

                <!-- Content -->
                <div id="detailContent" style="display: none;">
                    <div class="row">
                        <!-- Left Column - Photo & Basic Info -->
                        <div class="col-md-4 text-center border-end">
                            <div class="mb-4">
                                <div id="detailFoto" class="mb-3 mx-auto" style="width: 180px; height: 180px;">
                                    <!-- Photo will be loaded here -->
                                </div>
                                <h4 id="detailNama" class="fw-bold mb-1"></h4>
                                <div id="detailNIP" class="text-muted mb-2"></div>
                                <div class="mt-2 mb-3">
                                    <span id="detailStatus" class="badge"></span>
                                </div>
                                <div class="mt-3">
                                    <small class="text-muted">
                                        <i class="fas fa-id-card me-1"></i>
                                        <span id="detailUUID"></span>
                                    </small>
                                </div>
                            </div>

                            <!-- Quick Stats -->
                            <div class="card border-0 shadow-sm mb-3">
                                <div class="card-body py-2">
                                    <div class="row text-center">
                                        <div class="col-6 border-end">
                                            <div class="text-muted small">Usia</div>
                                            <div class="fw-bold" id="detailUsiaBadge">-</div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-muted small">Masa Kerja</div>
                                            <div class="fw-bold" id="detailMasaKerjaBadge">-</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column - Detailed Info -->
                        <div class="col-md-8">
                            <!-- Personal Information -->
                            <div class="card border-0 shadow-sm mb-3">
                                <div class="card-header bg-light py-2">
                                    <h6 class="mb-0 text-primary">
                                        <i class="fas fa-user me-2"></i>Informasi Pribadi
                                    </h6>
                                </div>
                                <div class="card-body p-3">
                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <small class="text-muted d-block">Jenis Kelamin</small>
                                            <span id="detailJenisKelamin" class="fw-semibold">-</span>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <small class="text-muted d-block">Tempat Lahir</small>
                                            <span id="detailTempatLahir" class="fw-semibold">-</span>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <small class="text-muted d-block">Tanggal Lahir</small>
                                            <span id="detailTanggalLahir" class="fw-semibold">-</span>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <small class="text-muted d-block">No. Telepon</small>
                                            <span id="detailNoTelepon" class="fw-semibold">-</span>
                                        </div>
                                        <div class="col-12 mb-2">
                                            <small class="text-muted d-block">Alamat</small>
                                            <span id="detailAlamat" class="fw-semibold">-</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Work Information -->
                            <div class="card border-0 shadow-sm mb-3">
                                <div class="card-header bg-light py-2">
                                    <h6 class="mb-0 text-primary">
                                        <i class="fas fa-briefcase me-2"></i>Informasi Pekerjaan
                                    </h6>
                                </div>
                                <div class="card-body p-3">
                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <small class="text-muted d-block">Jabatan</small>
                                            <span id="detailJabatan" class="fw-semibold">-</span>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <small class="text-muted d-block">Departemen</small>
                                            <span id="detailDepartemen" class="fw-semibold">-</span>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <small class="text-muted d-block">Status Karyawan</small>
                                            <span id="detailStatusKaryawan" class="fw-semibold">-</span>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <small class="text-muted d-block">Tanggal Masuk</small>
                                            <span id="detailTanggalMasuk" class="fw-semibold">-</span>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <small class="text-muted d-block">Tanggal Keluar</small>
                                            <span id="detailTanggalKeluar" class="fw-semibold">-</span>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <small class="text-muted d-block">Username</small>
                                            <span id="detailUsername" class="fw-semibold">-</span>
                                        </div>
                                        <div class="col-12 mb-2">
                                            <small class="text-muted d-block">Email</small>
                                            <span id="detailEmail" class="fw-semibold">-</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- System Information -->
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-light py-2">
                                    <h6 class="mb-0 text-primary">
                                        <i class="fas fa-database me-2"></i>Informasi Sistem
                                    </h6>
                                </div>
                                <div class="card-body p-3">
                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <small class="text-muted d-block">Dibuat Pada</small>
                                            <span id="detailCreatedAt" class="fw-semibold">-</span>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <small class="text-muted d-block">Diperbarui Pada</small>
                                            <span id="detailUpdatedAt" class="fw-semibold">-</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Tutup
                </button>
                <button type="button" class="btn btn-warning" id="btnEditFromDetail">
                    <i class="fas fa-edit me-1"></i>Edit Data
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nonaktifkan Karyawan -->
<div class="modal fade" id="nonaktifkanModal" tabindex="-1" aria-labelledby="nonaktifkanModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="nonaktifkanModalLabel">
                    <i class="fas fa-user-slash me-2"></i>Nonaktifkan Karyawan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formNonaktifkan">
                @csrf
                @method('PUT')
                <input type="hidden" id="nonaktifkan_id" name="id">
                <div class="modal-body">
                    <p id="nonaktifkanText"></p>
                    <div class="mb-3">
                        <label for="tanggal_keluar" class="form-label">Tanggal Keluar <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="tanggal_keluar" name="tanggal_keluar" required value="{{ date('Y-m-d') }}">
                        <div class="invalid-feedback" id="tanggalKeluarError"></div>
                    </div>
                    <div class="mb-3">
                        <label for="alasan" class="form-label">Alasan (Opsional)</label>
                        <textarea class="form-control" id="alasan" name="alasan" rows="2" placeholder="Masukkan alasan nonaktif..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">
                        <span id="nonaktifkanBtnText">Nonaktifkan</span>
                        <span id="nonaktifkanBtnLoading" class="d-none">
                            <span class="spinner-border spinner-border-sm me-1"></span> Memproses...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 16px;
}

.table-hover tbody tr:hover {
    background-color: rgba(0, 123, 255, 0.05);
}

#currentFoto img {
    max-width: 100px;
    max-height: 100px;
    border-radius: 5px;
    border: 1px solid #dee2e6;
}
/* Detail Modal Styles */
#detailKaryawanModal .modal-content {
    border-radius: 12px;
    overflow: hidden;
}

#detailKaryawanModal .modal-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-bottom: none;
}

#detailKaryawanModal .modal-header .btn-close {
    filter: invert(1);
    opacity: 0.8;
}

#detailKaryawanModal .modal-header .btn-close:hover {
    opacity: 1;
}

#detailKaryawanModal .card {
    border: 1px solid #e9ecef;
    transition: all 0.3s ease;
}

#detailKaryawanModal .card:hover {
    border-color: #667eea;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.1);
}

#detailKaryawanModal .card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
}

#detailKaryawanModal .text-muted {
    color: #6c757d !important;
    font-size: 0.85rem;
}

#detailKaryawanModal .fw-semibold {
    font-weight: 600;
    color: #343a40;
}

#detailKaryawanModal code {
    background-color: #f8f9fa;
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 0.9em;
    color: #e83e8c;
}

#detailKaryawanModal .badge {
    font-size: 0.8em;
    padding: 5px 10px;
    border-radius: 20px;
}

/* Style untuk tombol aksi */
.btn-group .btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
}

/* Hover effect untuk tombol */
.btn-outline-danger:hover {
    background-color: #dc3545;
    color: white;
}

.btn-outline-danger {
    border-color: #dc3545;
    color: #dc3545;
}

/* Tooltip styling */
.tooltip {
    font-size: 0.875rem;
}

/* Responsive button group */
@media (max-width: 768px) {
    .btn-group {
        flex-wrap: wrap;
        gap: 2px;
    }

    .btn-group .btn-sm {
        margin-bottom: 2px;
    }
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // Toggle password visibility
    $('.toggle-password').on('click', function() {
        const input = $(this).closest('.input-group').find('input');
        const icon = $(this).find('i');
        const type = input.attr('type') === 'password' ? 'text' : 'password';
        input.attr('type', type);
        icon.toggleClass('fa-eye fa-eye-slash');
    });

    // Form Tambah Karyawan
    $('#formTambahKaryawan').on('submit', function(e) {
        e.preventDefault();
        submitForm('tambah');
    });

    // Form Edit Karyawan
    $('#formEditKaryawan').on('submit', function(e) {
        e.preventDefault();
        submitForm('edit');
    });

    // Form Nonaktifkan Karyawan
    $('#formNonaktifkan').on('submit', function(e) {
        e.preventDefault();
        submitNonaktifkanForm();
    });

    // Reset form when modals are closed
    $('.modal').on('hidden.bs.modal', function() {
        $(this).find('form')[0].reset();
        $(this).find('.is-invalid').removeClass('is-invalid');
        $(this).find('.invalid-feedback').text('');
        resetButtonState($(this).find('button[type="submit"]'));
    });
});

function submitForm(type) {
    const isEdit = type === 'edit';
    const formId = isEdit ? '#formEditKaryawan' : '#formTambahKaryawan';
    const modalId = isEdit ? '#editKaryawanModal' : '#tambahKaryawanModal';
    const url = isEdit
        ? '{{ route("admin.update-karyawan", ["id" => ":id"]) }}'.replace(':id', $('#edit_id').val())
        : '{{ route("admin.tambah-karyawan") }}';
    const method = isEdit ? 'PUT' : 'POST';

    const submitBtn = $(formId).find('button[type="submit"]');
    const submitBtnText = $(formId).find('#submitBtnText, #submitBtnEditText');
    const submitBtnLoading = $(formId).find('#submitBtnLoading, #submitBtnEditLoading');

    // Show loading
    submitBtnText.addClass('d-none');
    submitBtnLoading.removeClass('d-none');
    submitBtn.prop('disabled', true);

    // Remove previous error states
    $(formId).find('.is-invalid').removeClass('is-invalid');
    $(formId).find('.invalid-feedback').text('');

    // Create FormData for file upload
    const formData = new FormData($(formId)[0]);

    // Add _method for PUT request
    if (isEdit) {
        formData.append('_method', 'PUT');
    }

    $.ajax({
        url: url,
        method: 'POST', // Always use POST for FormData with file upload
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                // Hide modal
                $(modalId).modal('hide');

                // Show success message
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: response.message,
                    timer: 2000,
                    showConfirmButton: false
                });

                // Reload page after delay
                setTimeout(function() {
                    window.location.reload();
                }, 1500);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message || 'Terjadi kesalahan'
                });
            }
        },
        error: function(xhr) {
            console.error('Error submitting form:', xhr);

            if (xhr.status === 422) {
                // Validation errors
                const errors = xhr.responseJSON.errors;
                for (const field in errors) {
                    const input = $(formId).find(`[name="${field}"]`);
                    const errorDiv = $(formId).find(`#${field}Error, #edit${field.charAt(0).toUpperCase() + field.slice(1)}Error`);

                    input.addClass('is-invalid');
                    if (errorDiv.length) {
                        errorDiv.text(errors[field][0]);
                    } else {
                        // Create error message if error div doesn't exist
                        const errorHtml = `<div class="invalid-feedback" id="${field}Error">${errors[field][0]}</div>`;
                        input.after(errorHtml);
                        input.addClass('is-invalid');
                    }
                }
            } else {
                // Other errors
                let errorMessage = 'Terjadi kesalahan saat menyimpan data';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMessage
                });
            }
        },
        complete: function() {
            resetButtonState(submitBtn);
            submitBtnText.removeClass('d-none');
            submitBtnLoading.addClass('d-none');
        }
    });
}

function editKaryawan(id) {
    // Reset form
    $('#formEditKaryawan')[0].reset();
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').text('');
    $('#currentFoto').empty();
    $('#hapus_foto').prop('checked', false);

    // Show loading
    const submitBtn = $('#submitBtnEdit');
    submitBtn.prop('disabled', true);
    $('#submitBtnEditText').addClass('d-none');
    $('#submitBtnEditLoading').removeClass('d-none');

    $.ajax({
        url: '{{ route("admin.edit-karyawan", ":id") }}'.replace(':id', id),
        method: 'GET',
        success: function(response) {
            if (response.success) {
                const data = response.data;

                // Fill form fields
                $('#edit_id').val(data.id);
                $('#edit_nama').val(data.nama);
                $('#edit_nip').val(data.nip);
                $('#edit_username').val(data.username);
                $('#edit_email').val(data.email);
                $('#edit_jabatan').val(data.jabatan);
                $('#edit_departemen').val(data.departemen);
                $('#edit_jenis_kelamin').val(data.jenis_kelamin);
                $('#edit_tempat_lahir').val(data.tempat_lahir);

                // Format dates properly
                if (data.tanggal_lahir) {
                    const tanggalLahir = new Date(data.tanggal_lahir);
                    $('#edit_tanggal_lahir').val(tanggalLahir.toISOString().split('T')[0]);
                }

                $('#edit_alamat').val(data.alamat);
                $('#edit_no_telepon').val(data.no_telepon);
                $('#edit_status_karyawan').val(data.status_karyawan);

                if (data.tanggal_masuk) {
                    const tanggalMasuk = new Date(data.tanggal_masuk);
                    $('#edit_tanggal_masuk').val(tanggalMasuk.toISOString().split('T')[0]);
                }

                if (data.tanggal_keluar) {
                    const tanggalKeluar = new Date(data.tanggal_keluar);
                    $('#edit_tanggal_keluar').val(tanggalKeluar.toISOString().split('T')[0]);
                }

                // Show current photo if exists
                if (data.foto) {
                    $('#currentFoto').html(`
                        <div class="mb-2">Foto Saat Ini:</div>
                        <img src="{{ asset('storage') }}/${data.foto}" alt="Foto Profil" class="img-thumbnail" style="max-width: 150px;">
                    `);
                }

                $('#editKaryawanModal').modal('show');
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message || 'Gagal memuat data karyawan'
                });
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading employee data:', error);

            let errorMessage = 'Gagal memuat data karyawan';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            } else if (xhr.status === 404) {
                errorMessage = 'Karyawan tidak ditemukan';
            }

            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: errorMessage
            });
        },
        complete: function() {
            resetButtonState(submitBtn);
            $('#submitBtnEditText').removeClass('d-none');
            $('#submitBtnEditLoading').addClass('d-none');
        }
    });
}

function nonaktifkan(id, nama) {
    $('#nonaktifkan_id').val(id);
    $('#nonaktifkanText').html(`Yakin ingin menonaktifkan <strong>${nama}</strong>?`);
    $('#nonaktifkanModal').modal('show');
}

function submitNonaktifkanForm() {
    const formId = '#formNonaktifkan';
    const id = $('#nonaktifkan_id').val();
    const url = '{{ route("admin.nonaktifkan-karyawan", ["id" => ":id"]) }}'.replace(':id', id);

    const submitBtn = $(formId).find('button[type="submit"]');
    const submitBtnText = $(formId).find('#nonaktifkanBtnText');
    const submitBtnLoading = $(formId).find('#nonaktifkanBtnLoading');

    // Show loading
    submitBtnText.addClass('d-none');
    submitBtnLoading.removeClass('d-none');
    submitBtn.prop('disabled', true);

    // Remove previous error states
    $(formId).find('.is-invalid').removeClass('is-invalid');
    $(formId).find('.invalid-feedback').text('');

    const formData = new FormData($(formId)[0]);

    $.ajax({
        url: url,
        method: 'PUT',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                // Hide modal
                $('#nonaktifkanModal').modal('hide');

                // Show success message
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: response.message,
                    timer: 2000,
                    showConfirmButton: false
                });

                // Reload page after delay
                setTimeout(function() {
                    window.location.reload();
                }, 1500);
            }
        },
        error: function(xhr) {
            if (xhr.status === 422) {
                // Validation errors
                const errors = xhr.responseJSON.errors;
                for (const field in errors) {
                    const input = $(formId).find(`[name="${field}"]`);
                    const errorDiv = $(formId).find(`#${field}Error`);

                    input.addClass('is-invalid');
                    if (errorDiv.length) {
                        errorDiv.text(errors[field][0]);
                    }
                }
            } else {
                // Other errors
                let errorMessage = 'Gagal menonaktifkan karyawan';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMessage
                });
            }
        },
        complete: function() {
            resetButtonState(submitBtn);
            submitBtnText.removeClass('d-none');
            submitBtnLoading.addClass('d-none');
        }
    });
}

// Variabel untuk menyimpan ID karyawan yang sedang dilihat
let currentDetailId = null;

function showDetail(id) {
    currentDetailId = id;

    // Reset states
    $('#detailLoading').show();
    $('#detailError').hide();
    $('#detailContent').hide();

    // Show modal
    $('#detailKaryawanModal').modal('show');

    // Load data
    loadDetailData(id);
}

function loadDetailData(id) {
    $.ajax({
        url: '{{ route("admin.detail-karyawan", ":id") }}'.replace(':id', id),
        method: 'GET',
        beforeSend: function() {
            $('#detailLoading').show();
            $('#detailError').hide();
            $('#detailContent').hide();
        },
        success: function(response) {
            $('#detailLoading').hide();

            if (response.success) {
                const data = response.data;
                displayDetailData(data);
            } else {
                showDetailError(response.message || 'Gagal memuat data karyawan');
            }
        },
        error: function(xhr, status, error) {
            $('#detailLoading').hide();

            let errorMessage = 'Gagal memuat data karyawan';
            if (xhr.status === 404) {
                errorMessage = 'Karyawan tidak ditemukan';
            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }

            showDetailError(errorMessage);
        }
    });
}

function displayDetailData(data) {
    // Display photo
    if (data.foto) {
        const fotoUrl = '{{ asset("storage") }}/' + data.foto;
        $('#detailFoto').html(`
            <img src="${fotoUrl}" alt="${data.nama}"
                 class="img-fluid rounded-circle shadow"
                 style="width: 180px; height: 180px; object-fit: cover;">
        `);
    } else {
        const initial = data.nama ? data.nama.charAt(0).toUpperCase() : '?';
        const color = getColorFromString(data.nama || 'user');
        $('#detailFoto').html(`
            <div class="d-flex align-items-center justify-content-center rounded-circle shadow mx-auto"
                 style="width: 180px; height: 180px; background-color: ${color}; color: white;">
                <span style="font-size: 64px; font-weight: bold;">${initial}</span>
            </div>
        `);
    }

    // Basic info
    $('#detailNama').text(data.nama || '-');
    $('#detailNIP').html(data.nip ? `<code class="bg-light p-1 rounded">${data.nip}</code>` : '<span class="text-muted">-</span>');
    $('#detailUUID').text(data.uuid_user || '-');

    // Status badge
    let statusText = '-';
    let statusClass = 'secondary';
    let statusKaryawanText = '-';

    if (data.tanggal_keluar) {
        statusText = 'Nonaktif';
        statusClass = 'danger';
    } else {
        switch(data.status_karyawan) {
            case 'tetap':
                statusText = 'Aktif (Tetap)';
                statusClass = 'success';
                statusKaryawanText = 'Tetap';
                break;
            case 'kontrak':
                statusText = 'Aktif (Kontrak)';
                statusClass = 'warning';
                statusKaryawanText = 'Kontrak';
                break;
            case 'probation':
                statusText = 'Aktif (Probation)';
                statusClass = 'info';
                statusKaryawanText = 'Probation';
                break;
            default:
                statusText = 'Aktif';
                statusClass = 'success';
        }
    }

    $('#detailStatus').html(`<span class="badge bg-${statusClass}">${statusText}</span>`);
    $('#detailStatusKaryawan').text(statusKaryawanText);

    // Quick stats
    $('#detailUsiaBadge').text(data.usia ? `${data.usia} tahun` : '-');
    $('#detailMasaKerjaBadge').text(data.masa_kerja || '-');

    // Personal information
    $('#detailJenisKelamin').text(data.jenis_kelamin === 'L' ? 'Laki-laki' : (data.jenis_kelamin === 'P' ? 'Perempuan' : '-'));
    $('#detailTempatLahir').text(data.tempat_lahir || '-');
    $('#detailTanggalLahir').text(data.tanggal_lahir ? formatDate(data.tanggal_lahir) : '-');
    $('#detailNoTelepon').text(data.no_telepon || '-');
    $('#detailAlamat').text(data.alamat || '-');

    // Work information
    $('#detailJabatan').text(data.jabatan || '-');
    $('#detailDepartemen').text(data.departemen || '-');
    $('#detailTanggalMasuk').text(data.tanggal_masuk ? formatDate(data.tanggal_masuk) : '-');
    $('#detailTanggalKeluar').text(data.tanggal_keluar ? formatDate(data.tanggal_keluar) : '-');
    $('#detailUsername').html(data.username ? `<code>${data.username}</code>` : '-');
    $('#detailEmail').text(data.email || '-');

    // System information
    $('#detailCreatedAt').text(data.created_at || '-');
    $('#detailUpdatedAt').text(data.updated_at || '-');

    // Show content
    $('#detailContent').show();

    // Setup edit button
    $('#btnEditFromDetail').off('click').on('click', function() {
        $('#detailKaryawanModal').modal('hide');
        setTimeout(() => {
            editKaryawan(currentDetailId);
        }, 300);
    });
}

function showDetailError(message) {
    $('#detailErrorMessage').text(message);
    $('#detailError').show();
    $('#detailContent').hide();
}

function retryLoadDetail() {
    if (currentDetailId) {
        loadDetailData(currentDetailId);
    }
}

function hapusKaryawan(id, nama) {
    Swal.fire({
        title: 'Hapus Permanen?',
        html: `<div class="text-start">
                <p>Yakin ingin menghapus <strong>${nama}</strong> secara permanen?</p>
                <div class="alert alert-danger py-2 my-2">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>PERINGATAN!</strong> Tindakan ini:
                    <ul class="mb-0 mt-1">
                        <li>Tidak dapat dibatalkan</li>
                        <li>Semua data akan dihapus permanen</li>
                        <li>Termasuk data absensi terkait</li>
                    </ul>
                </div>
                <p class="text-muted small">Masukkan konfirmasi di bawah ini:</p>
                <div class="mb-3">
                    <input type="text" id="confirmDelete" class="form-control"
                           placeholder="Ketik 'HAPUS ${nama.toUpperCase()}'">
                    <small class="text-muted">Ketik: HAPUS ${nama.toUpperCase()}</small>
                </div>
              </div>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus Permanen',
        cancelButtonText: 'Batal',
        reverseButtons: true,
        allowOutsideClick: false,
        showLoaderOnConfirm: true,
        preConfirm: () => {
            const confirmText = document.getElementById('confirmDelete').value;
            const expectedText = `HAPUS ${nama.toUpperCase()}`;

            if (confirmText !== expectedText) {
                Swal.showValidationMessage('Konfirmasi tidak sesuai!');
                return false;
            }

            return true;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: 'Menghapus...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: '{{ route("admin.hapus-karyawan", ["id" => ":id"]) }}'.replace(':id', id),
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        });

                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Gagal menghapus karyawan'
                        });
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Gagal menghapus karyawan';
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
    });
}

// Helper functions
function formatDate(dateString) {
    if (!dateString) return '-';

    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    });
}

function getColorFromString(str) {
    // Generate consistent color from string
    let hash = 0;
    for (let i = 0; i < str.length; i++) {
        hash = str.charCodeAt(i) + ((hash << 5) - hash);
    }

    const colors = [
        '#FF6B6B', '#4ECDC4', '#FFD166', '#06D6A0', '#118AB2',
        '#EF476F', '#7209B7', '#3A86FF', '#FB5607', '#8338EC'
    ];

    return colors[Math.abs(hash) % colors.length];
}

// Initialize modal events
$(document).ready(function() {
    // Reset modal when closed
    $('#detailKaryawanModal').on('hidden.bs.modal', function() {
        currentDetailId = null;
        $('#detailLoading').show();
        $('#detailError').hide();
        $('#detailContent').hide();
    });

    // Handle escape key
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape' && $('#detailKaryawanModal').hasClass('show')) {
            $('#detailKaryawanModal').modal('hide');
        }
    });
});

function aktifkan(id, nama) {
    Swal.fire({
        title: 'Aktifkan Kembali?',
        html: `Yakin ingin mengaktifkan kembali <strong>${nama}</strong>?`,
        icon: 'info',
        showCancelButton: true,
        confirmButtonColor: '#198754',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Aktifkan',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ route("admin.aktifkan-karyawan", ["id" => ":id"]) }}'.replace(':id', id),
                method: 'PUT',
                data: {
                    _token: '{{ csrf_token() }}',
                    tanggal_keluar: null
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                        setTimeout(() => location.reload(), 1500);
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Gagal mengaktifkan karyawan';
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
    });
}

function resetButtonState(button) {
    button.prop('disabled', false);
}



// Simple MD5 function for avatar color
function md5(inputString) {
    var hc="0123456789abcdef";
    function rh(n) {var j,s="";for(j=0;j<=3;j++) s+=hc.charAt((n>>(j*8+4))&0x0F)+hc.charAt((n>>(j*8))&0x0F);return s;}
    function ad(x,y) {var l=(x&0xFFFF)+(y&0xFFFF);var m=(x>>16)+(y>>16)+(l>>16);return (m<<16)|(l&0xFFFF);}
    function rl(n,c) {return (n<<c)|(n>>>(32-c));}
    function cm(q,a,b,x,s,t) {return ad(rl(ad(ad(a,q),ad(x,t)),s),b);}
    function ff(a,b,c,d,x,s,t) {return cm((b&c)|((~b)&d),a,b,x,s,t);}
    function gg(a,b,c,d,x,s,t) {return cm((b&d)|(c&(~d)),a,b,x,s,t);}
    function hh(a,b,c,d,x,s,t) {return cm(b^c^d,a,b,x,s,t);}
    function ii(a,b,c,d,x,s,t) {return cm(c^(b|(~d)),a,b,x,s,t);}
    function sb(x) {
        var i;var nblk=((x.length+8)>>6)+1;var blks=new Array(nblk*16);for(i=0;i<nblk*16;i++) blks[i]=0;
        for(i=0;i<x.length;i++) blks[i>>2]|=x.charCodeAt(i)<<((i%4)*8);
        blks[i>>2]|=0x80<<((i%4)*8);blks[nblk*16-2]=x.length*8;return blks;
    }
    var i,x=sb(inputString),a=1732584193,b=-271733879,c=-1732584194,d=271733878,olda,oldb,oldc,oldd;
    for(i=0;i<x.length;i+=16) {olda=a;oldb=b;oldc=c;oldd=d;
        a=ff(a,b,c,d,x[i+ 0], 7, -680876936);d=ff(d,a,b,c,x[i+ 1],12, -389564586);c=ff(c,d,a,b,x[i+ 2],17,  606105819);
        b=ff(b,c,d,a,x[i+ 3],22, -1044525330);a=ff(a,b,c,d,x[i+ 4], 7, -176418897);d=ff(d,a,b,c,x[i+ 5],12,  1200080426);
        c=ff(c,d,a,b,x[i+ 6],17, -1473231341);b=ff(b,c,d,a,x[i+ 7],22, -45705983);a=ff(a,b,c,d,x[i+ 8], 7,  1770035416);
        d=ff(d,a,b,c,x[i+ 9],12, -1958414417);c=ff(c,d,a,b,x[i+10],17, -42063);b=ff(b,c,d,a,x[i+11],22, -1990404162);
        a=ff(a,b,c,d,x[i+12], 7,  1804603682);d=ff(d,a,b,c,x[i+13],12, -40341101);c=ff(c,d,a,b,x[i+14],17, -1502002290);
        b=ff(b,c,d,a,x[i+15],22,  1236535329);a=gg(a,b,c,d,x[i+ 1], 5, -165796510);d=gg(d,a,b,c,x[i+ 6], 9, -1069501632);
        c=gg(c,d,a,b,x[i+11],14,  643717713);b=gg(b,c,d,a,x[i+ 0],20, -373897302);a=gg(a,b,c,d,x[i+ 5], 5, -701558691);
        d=gg(d,a,b,c,x[i+10], 9,  38016083);c=gg(c,d,a,b,x[i+15],14, -660478335);b=gg(b,c,d,a,x[i+ 4],20, -405537848);
        a=gg(a,b,c,d,x[i+ 9], 5,  568446438);d=gg(d,a,b,c,x[i+14], 9, -1019803690);c=gg(c,d,a,b,x[i+ 3],14, -187363961);
        b=gg(b,c,d,a,x[i+ 8],20,  1163531501);a=gg(a,b,c,d,x[i+13], 5, -1444681467);d=gg(d,a,b,c,x[i+ 2], 9, -51403784);
        c=gg(c,d,a,b,x[i+ 7],14,  1735328473);b=gg(b,c,d,a,x[i+12],20, -1926607734);a=hh(a,b,c,d,x[i+ 5], 4, -378558);
        d=hh(d,a,b,c,x[i+ 8],11, -2022574463);c=hh(c,d,a,b,x[i+11],16,  1839030562);b=hh(b,c,d,a,x[i+14],23, -35309556);
        a=hh(a,b,c,d,x[i+ 1], 4, -1530992060);d=hh(d,a,b,c,x[i+ 4],11,  1272893353);c=hh(c,d,a,b,x[i+ 7],16, -155497632);
        b=hh(b,c,d,a,x[i+10],23, -1094730640);a=hh(a,b,c,d,x[i+13], 4,  681279174);d=hh(d,a,b,c,x[i+ 0],11, -358537222);
        c=hh(c,d,a,b,x[i+ 3],16, -722521979);b=hh(b,c,d,a,x[i+ 6],23,  76029189);a=hh(a,b,c,d,x[i+ 9], 4, -640364487);
        d=hh(d,a,b,c,x[i+12],11, -421815835);c=hh(c,d,a,b,x[i+15],16,  530742520);b=hh(b,c,d,a,x[i+ 2],23, -995338651);
        a=ii(a,b,c,d,x[i+ 0], 6, -198630844);d=ii(d,a,b,c,x[i+ 7],10,  1126891415);c=ii(c,d,a,b,x[i+14],15, -1416354905);
        b=ii(b,c,d,a,x[i+ 5],21, -57434055);a=ii(a,b,c,d,x[i+12], 6,  1700485571);d=ii(d,a,b,c,x[i+ 3],10, -1894986606);
        c=ii(c,d,a,b,x[i+10],15, -1051523);b=ii(b,c,d,a,x[i+ 1],21, -2054922799);a=ii(a,b,c,d,x[i+ 8], 6,  1873313359);
        d=ii(d,a,b,c,x[i+15],10, -30611744);c=ii(c,d,a,b,x[i+ 6],15, -1560198380);b=ii(b,c,d,a,x[i+13],21,  1309151649);
        a=ii(a,b,c,d,x[i+ 4], 6, -145523070);d=ii(d,a,b,c,x[i+11],10, -1120210379);c=ii(c,d,a,b,x[i+ 2],15,  718787259);
        b=ii(b,c,d,a,x[i+ 9],21, -343485551);a=ad(a,olda);b=ad(b,oldb);c=ad(c,oldc);d=ad(d,oldd);
    }
    return rh(a)+rh(b)+rh(c)+rh(d);
}
</script>
@endpush
@endsection
