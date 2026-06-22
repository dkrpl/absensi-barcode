<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Barcode;
use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class KaryawanController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $absensiHariIni = Absensi::with('shift')
            ->where('id_user', $user->id)
            ->whereDate('tanggal_absen', today())
            ->first();

        $riwayatAbsensi = Absensi::with('shift')
            ->where('id_user', $user->id)
            ->orderBy('tanggal_absen', 'desc')
            ->limit(10)
            ->get();

        return view('karyawan.dashboard', compact('absensiHariIni', 'riwayatAbsensi'));
    }

    public function scanBarcode()
    {
        return view('karyawan.scan-barcode');
    }

    public function prosesAbsensi(Request $request)
{
    Log::info('Proses absensi dimulai', [
        'user_id' => Auth::id(),
        'kode_barcode' => $request->kode_barcode,
        'time' => now()
    ]);

    $request->validate([
        'kode_barcode' => 'required|string'
    ]);

    // Rate limiting based on user ID
    $user = Auth::user();
    $key = 'absensi:' . $user->id;
    $attempts = cache()->get($key, 0);

    if ($attempts >= 3) {
        Log::warning('Rate limit exceeded', ['user_id' => $user->id]);
        return response()->json([
            'success' => false,
            'message' => 'Terlalu banyak percobaan. Coba lagi dalam 1 menit.'
        ], 429);
    }

    cache()->put($key, $attempts + 1, now()->addMinutes(1));

    $kode = $request->kode_barcode;

    // Decrypt the token
    try {
        $decrypted = \Illuminate\Support\Facades\Crypt::decryptString($kode);
        $payload = json_decode($decrypted, true);
        
        if (isset($payload['expires_at']) && time() > $payload['expires_at']) {
            Log::warning('QR Code expired (dynamic token)', ['payload' => $payload]);
            return response()->json([
                'success' => false,
                'message' => 'QR Code sudah kadaluarsa (berubah). Silakan scan QR Code terbaru di layar.'
            ], 400);
        }
        
        if (isset($payload['kode_barcode'])) {
            $kode = $payload['kode_barcode'];
        } else {
            throw new \Exception('Invalid payload structure');
        }
    } catch (\Exception $e) {
        // Fallback untuk backward compatibility jika QR Code statis (lama)
        // atau lempar error jika ingin enforce token dinamis
        Log::warning('Gagal decrypt token QR, menggunakan raw kode', [
            'error' => $e->getMessage(),
            'kode' => $kode
        ]);
        
        // Uncomment baris di bawah ini jika ingin menolak QR statis (lama)
        // return response()->json(['success' => false, 'message' => 'QR Code tidak valid atau format salah.'], 400);
    }

    // Cari barcode yang aktif
    $barcode = Barcode::where('kode_barcode', $kode)
        ->where('status', 'aktif')
        ->first();

    if (!$barcode) {
        Log::warning('Barcode tidak ditemukan atau tidak aktif', ['kode' => $kode]);
        return response()->json([
            'success' => false,
            'message' => 'QR Code tidak valid atau tidak aktif'
        ], 400);
    }

    // Cek waktu barcode
    $now = now();
    if ($now < $barcode->waktu_mulai) {
        Log::warning('Barcode belum aktif', [
            'waktu_mulai' => $barcode->waktu_mulai,
            'sekarang' => $now
        ]);
        return response()->json([
            'success' => false,
            'message' => 'QR Code belum aktif. Aktif pada: ' . $barcode->waktu_mulai->format('H:i:s')
        ], 400);
    }

    if ($now > $barcode->waktu_akhir) {
        Log::warning('Barcode sudah expired', [
            'waktu_akhir' => $barcode->waktu_akhir,
            'sekarang' => $now
        ]);
        return response()->json([
            'success' => false,
            'message' => 'QR Code sudah expired'
        ], 400);
    }

    // Cek apakah sudah absen hari ini untuk shift ini
    $sudahAbsen = Absensi::where('id_user', $user->id)
        ->where('id_shift', $barcode->id_shift)
        ->whereDate('tanggal_absen', today())
        ->exists();

    if ($sudahAbsen) {
        Log::info('User sudah absen hari ini', [
            'user_id' => $user->id,
            'shift_id' => $barcode->id_shift
        ]);
        return response()->json([
            'success' => false,
            'message' => 'Anda sudah absen untuk shift ini hari ini'
        ], 400);
    }

    // Tentukan status (hadir/terlambat) - LOGIKA DIPERBAHARUI
    $shift = Shift::find($barcode->id_shift);

    if (!$shift) {
        Log::error('Shift tidak ditemukan', ['shift_id' => $barcode->id_shift]);
        return response()->json([
            'success' => false,
            'message' => 'Shift tidak ditemukan'
        ], 400);
    }

    $jamMasuk = $shift->jam_mulai;
    $batasTelat = $shift->batas_telat;

    $jamSekarang = $now;
    $jamTarget = today()->setTimeFrom($jamMasuk);

    $menitTerlambat = (int) round($jamSekarang->diffInMinutes($jamTarget, false));

    // LOGIKA BARU: Sesuai dengan AdminController
    // Jika absen sebelum atau pada jam masuk: HADIR
    // Jika absen setelah jam masuk: cek batas telat
    $status = 'hadir'; // Default

    if ($menitTerlambat > 0) {
        // Absen setelah jam masuk
        if ($menitTerlambat > $batasTelat) {
            $status = 'terlambat';
            Log::info('Absensi terlambat', [
                'menit_terlambat' => $menitTerlambat,
                'batas_telat' => $batasTelat
            ]);
        } else {
            // Masih dalam batas toleransi
            $status = 'hadir';
            Log::info('Absensi tepat waktu (dalam toleransi)', [
                'menit_terlambat' => $menitTerlambat,
                'batas_telat' => $batasTelat
            ]);
        }
    } else {
        // Absen sebelum jam masuk (lebih awal)
        $status = 'hadir';
        Log::info('Absensi lebih awal', [
            'menit_lebih_awal' => abs($menitTerlambat)
        ]);
    }

    // Simpan absensi
    try {
        $absensi = Absensi::create([
            'id_user' => $user->id,
            'id_shift' => $barcode->id_shift,
            'id_barcode' => $barcode->id,
            'tanggal_absen' => today(),
            'waktu_absen' => $jamSekarang,
            'status' => $status
        ]);

        Log::info('Absensi berhasil disimpan', [
            'absensi_id' => $absensi->id,
            'status' => $status,
            'waktu' => $jamSekarang->format('H:i:s'),
            'menit_dari_jam_masuk' => $menitTerlambat
        ]);

        // Clear rate limiting cache on success
        cache()->forget($key);

        // Pesan yang lebih informatif
        $message = 'Absensi berhasil! ';
        if ($status == 'terlambat') {
            $message .= 'Status: Terlambat (' . $menitTerlambat . ' menit)';
        } elseif ($menitTerlambat > 0) {
            $message .= 'Status: Hadir (dalam toleransi, terlambat ' . $menitTerlambat . ' menit)';
        } else {
            $message .= 'Status: Hadir ' . ($menitTerlambat < 0 ? '(lebih awal ' . abs($menitTerlambat) . ' menit)' : '(tepat waktu)');
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => [
                'waktu' => $absensi->waktu_absen->format('H:i:s'),
                'status' => $absensi->status,
                'shift' => $shift->nama_shift,
                'jam_mulai' => $shift->jam_mulai,
                'menit_terlambat' => $menitTerlambat > 0 ? $menitTerlambat : 0,
                'menit_lebih_awal' => $menitTerlambat < 0 ? abs($menitTerlambat) : 0
            ]
        ]);

    } catch (\Exception $e) {
        Log::error('Error saat menyimpan absensi: ' . $e->getMessage(), [
            'user_id' => $user->id,
            'error' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan saat menyimpan absensi'
        ], 500);
    }
}

    public function riwayatAbsensi(Request $request)
    {
        $user = Auth::user();

        $query = Absensi::with('shift')
            ->where('id_user', $user->id);

        if ($request->has('tanggal') && $request->tanggal) {
            $query->whereDate('tanggal_absen', $request->tanggal);
        }

        $riwayat = $query->orderBy('tanggal_absen', 'desc')
            ->orderBy('waktu_absen', 'desc')
            ->paginate(20);

        return view('karyawan.riwayat-absensi', compact('riwayat'));
    }

    public function checkAttendance()
    {
        $user = Auth::user();

        $hasAttended = Absensi::where('id_user', $user->id)
            ->whereDate('tanggal_absen', today())
            ->exists();

        return response()->json([
            'hasAttended' => $hasAttended
        ]);
    }
}
