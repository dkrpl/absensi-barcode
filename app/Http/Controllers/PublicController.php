<?php

namespace App\Http\Controllers;

use App\Models\Barcode;
use App\Models\Shift;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PublicController extends Controller
{
    /**
     * Display QR code page without authentication
     */
    public function displayQR(Request $request)
    {
        // Get current time
        $now = Carbon::now();

        // LOGIC: Cari shift yang QR-nya HARUS AKTIF SEKARANG
        // QR aktif dari (shift_mulai - 1 jam) sampai (shift_mulai)

        $currentShift = null;
        $activeBarcode = null;

        // Cari semua QR aktif saat ini
        $activeBarcode = Barcode::with('shift')
            ->active()
            ->first();

        if ($activeBarcode) {
            // Jika ada QR aktif, gunakan shift-nya
            $currentShift = $activeBarcode->shift;
        } else {
            // Jika tidak ada QR aktif, cari shift berikutnya yang akan memiliki QR
            $shifts = Shift::orderByRaw(
                "TIME(jam_mulai) < TIME(?)",
                [$now->format('H:i:s')]
            )
            ->orderBy('jam_mulai')
            ->get();

            foreach ($shifts as $shift) {
                $shiftStart = Carbon::parse($shift->jam_mulai);
                $qrStartTime = $shiftStart->copy()->subHour();

                // Jika shift ini akan mulai dalam waktu dekat
                if ($shiftStart > $now) {
                    $currentShift = $shift;
                    break;
                }
            }

            // Jika tidak ketemu, ambil shift pertama besok
            if (!$currentShift) {
                $currentShift = Shift::orderBy('jam_mulai')->first();
            }
        }

        // Get all shifts for info
        $shifts = Shift::orderBy('jam_mulai')->get();

        return view('public.qr-display', compact('activeBarcode', 'currentShift', 'shifts'));
    }

    /**
     * API endpoint untuk mendapatkan QR code aktif
     */
    public function getActiveQR()
    {
        // Cari QR aktif
        $activeBarcode = Barcode::with('shift')
            ->active()
            ->first();

        if (!$activeBarcode) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada QR code aktif saat ini',
                'data' => null
            ]);
        }

        // Calculate remaining time
        $now = Carbon::now();
        $expiryTime = Carbon::parse($activeBarcode->waktu_akhir);
        $remainingSeconds = $now->diffInSeconds($expiryTime, false);

        // Format untuk response
        return response()->json([
            'success' => true,
            'message' => 'QR code aktif ditemukan',
            'data' => [
                'id' => $activeBarcode->id,
                'kode_barcode' => $activeBarcode->kode_barcode,
                'waktu_mulai' => $activeBarcode->waktu_mulai->format('H:i:s'),
                'waktu_akhir' => $activeBarcode->waktu_akhir->format('H:i:s'),
                'remaining_seconds' => max(0, $remainingSeconds),
                'remaining_minutes' => max(0, floor($remainingSeconds / 60)),
                'expiry_time' => $expiryTime->format('H:i:s'),
            ],
            'shift' => [
                'id' => $activeBarcode->shift->id,
                'nama_shift' => $activeBarcode->shift->nama_shift,
                'jam_mulai' => $activeBarcode->shift->jam_mulai,
                'jam_akhir' => $activeBarcode->shift->jam_akhir,
            ],
            'timestamp' => now()->format('Y-m-d H:i:s')
        ]);
    }

    /**
     * API untuk mendapatkan info shift berikutnya
     */
    public function getNextShiftInfo()
    {
        $now = Carbon::now();

        // Cari shift berikutnya
        $nextShift = Shift::whereTime('jam_mulai', '>', $now->format('H:i:s'))
            ->orderBy('jam_mulai')
            ->first();

        if (!$nextShift) {
            // Jika tidak ada shift berikutnya hari ini, ambil pertama besok
            $nextShift = Shift::orderBy('jam_mulai')->first();
        }

        // Hitung kapan QR akan muncul (1 jam sebelum shift)
        $shiftStart = Carbon::parse($nextShift->jam_mulai);
        $qrStartTime = $shiftStart->copy()->subHour();
        $secondsUntilQR = $now->diffInSeconds($qrStartTime, false);

        return response()->json([
            'success' => true,
            'data' => [
                'shift' => $nextShift,
                'qr_will_appear_at' => $qrStartTime->format('H:i:s'),
                'seconds_until_qr' => max(0, $secondsUntilQR),
            ]
        ]);
    }

    /**
     * Health check untuk monitoring
     */
    public function healthCheck()
    {
        $activeQR = Barcode::active()->count();
        $totalShifts = Shift::count();
        $nextQR = $this->getNextShiftInfo()->getData();

        return response()->json([
            'status' => 'healthy',
            'timestamp' => now()->format('Y-m-d H:i:s'),
            'data' => [
                'active_qr_codes' => $activeQR,
                'total_shifts' => $totalShifts,
                'next_qr_generation' => $nextQR->data->qr_will_appear_at ?? null,
                'system_time' => now()->format('H:i:s'),
            ]
        ]);
    }
}
