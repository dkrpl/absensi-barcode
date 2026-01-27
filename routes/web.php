<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\PublicController;

// Public routes
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    // Data Karyawan
    Route::get('/data-karyawan', [AdminController::class, 'dataKaryawan'])->name('admin.data-karyawan');
    Route::post('/tambah-karyawan', [AdminController::class, 'tambahKaryawan'])->name('admin.tambah-karyawan');
    Route::get('/detail-karyawan/{id}', [AdminController::class, 'detailKaryawan'])->name('admin.detail-karyawan');
    Route::get('/edit-karyawan/{id}', [AdminController::class, 'editKaryawan'])->name('admin.edit-karyawan');
    Route::put('/update-karyawan/{id}', [AdminController::class, 'updateKaryawan'])->name('admin.update-karyawan');
    Route::put('/nonaktifkan-karyawan/{id}', [AdminController::class, 'nonaktifkanKaryawan'])->name('admin.nonaktifkan-karyawan');
    Route::put('/aktifkan-karyawan/{id}', [AdminController::class, 'aktifkanKaryawan'])->name('admin.aktifkan-karyawan');
    Route::delete('/hapus-karyawan/{id}', [AdminController::class, 'hapusKaryawan'])->name('admin.hapus-karyawan');
    Route::get('/export-karyawan', [AdminController::class, 'exportKaryawan'])->name('admin.export-karyawan');
    Route::get('/get-departemen-suggestions', [AdminController::class, 'getDepartemenSuggestions'])->name('admin.get-departemen-suggestions');

    // Data Shift
    Route::get('/data-shift', [AdminController::class, 'dataShift'])->name('admin.data-shift');
    Route::post('/simpan-shift', [AdminController::class, 'simpanShift'])->name('admin.simpan-shift');
    Route::get('/shift/{id}/edit', [AdminController::class, 'editShift'])->name('admin.edit-shift');
    Route::put('/shift/{id}/update', [AdminController::class, 'updateShift'])->name('admin.update-shift');
    Route::delete('/shift/{id}/hapus', [AdminController::class, 'hapusShift'])->name('admin.hapus-shift');

    // QR Code
    Route::get('/generate-barcode', [AdminController::class, 'showGenerateBarcode'])->name('admin.generate-barcode');
    Route::post('/generate-barcode', [AdminController::class, 'generateBarcode'])->name('admin.generate-barcode.post');
    Route::get('/barcode-display/{id}', [AdminController::class, 'showBarcode'])->name('admin.barcode-display');

    // Laporan
    Route::get('/laporan-absensi', [AdminController::class, 'laporanAbsensi'])->name('admin.laporan-absensi');
    Route::get('/export-laporan', [AdminController::class, 'exportLaporan'])->name('admin.export-laporan');
    // Tambahkan route ini
    Route::get('admin/monthly-stats', [AdminController::class, 'getMonthlyStats'])
        ->name('admin.monthly-stats');
    Route::get('admin/export-rekap-bulanan', [AdminController::class, 'exportRekapBulanan'])
    ->name('admin.export-rekap-bulanan');

    // Dashboard stats API
    Route::get('/dashboard/stats', [AdminController::class, 'dashboardStats'])->name('admin.dashboard.stats');
});

// Karyawan routes
Route::middleware(['auth', 'karyawan'])->prefix('karyawan')->group(function () {
    Route::get('/dashboard', [KaryawanController::class, 'dashboard'])->name('karyawan.dashboard');
    Route::get('/scan-barcode', [KaryawanController::class, 'scanBarcode'])->name('karyawan.scan-barcode');
    Route::post('/proses-absensi', [KaryawanController::class, 'prosesAbsensi'])->name('karyawan.proses-absensi');
    Route::get('/riwayat-absensi', [KaryawanController::class, 'riwayatAbsensi'])->name('karyawan.riwayat-absensi');
    Route::get('/check-attendance', [KaryawanController::class, 'checkAttendance'])->name('karyawan.check-attendance');
    // Tambahkan route baru untuk info shift
    Route::get('/current-shift-info', [KaryawanController::class, 'getCurrentShiftInfo'])->name('karyawan.current-shift-info');
});

// Public routes untuk QR display (TANPA AUTH)
Route::get('/qr-display', [PublicController::class, 'displayQR'])->name('public.qr-display');
Route::get('/api/qr-active', [PublicController::class, 'getActiveQR'])->name('api.qr-active');
Route::get('/api/next-shift', [PublicController::class, 'getNextShiftInfo'])->name('api.next-shift');
Route::get('/api/health', [PublicController::class, 'healthCheck'])->name('api.health');

// Fallback untuk QR display
Route::get('/qr', function () {
    return redirect()->route('public.qr-display');
});
// Fallback for 404
Route::fallback(function () {
    return view('errors.404');
});
