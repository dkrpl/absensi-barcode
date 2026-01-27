<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\Barcode;
use App\Models\Shift;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        // ==================== TASK 1: GENERATE QR OTOMATIS ====================
        $schedule->call(function () {
            Log::info('🚀 Scheduler: Memulai proses generate QR otomatis');

            $now = Carbon::now();
            $generatedCount = 0;

            // Ambil semua shift
            $shifts = Shift::all();

            foreach ($shifts as $shift) {
                $shiftStart = Carbon::parse($shift->jam_mulai);
                $qrStartTime = $shiftStart->copy()->subHour(); // 1 jam sebelum shift

                // Debug log
                Log::info("Shift {$shift->nama_shift}: Start={$shiftStart->format('H:i')}, QR Start={$qrStartTime->format('H:i')}, Now={$now->format('H:i')}");

                // Cek apakah sekarang waktunya generate QR (tepat 1 jam sebelum shift)
                // Kita beri toleransi 1 menit (±1 menit dari waktu exact)
                if ($now->between(
                    $qrStartTime->copy()->subMinute(),
                    $qrStartTime->copy()->addMinute()
                )) {
                    Log::info("⏰ Waktunya generate QR untuk shift {$shift->nama_shift}");

                    // Cek apakah sudah ada QR aktif untuk shift ini
                    $existingQR = Barcode::where('id_shift', $shift->id)
                        ->active()
                        ->first();

                    if (!$existingQR) {
                        // Generate QR baru
                        $kode = Str::uuid();
                        $waktuMulai = $now->copy();
                        $waktuAkhir = $shiftStart->copy(); // Berakhir tepat saat shift mulai

                        // Pastikan QR tidak lebih dari 1 jam
                        if ($waktuMulai->diffInMinutes($waktuAkhir) > 60) {
                            $waktuAkhir = $waktuMulai->copy()->addMinutes(60);
                        }

                        Barcode::create([
                            'id_shift' => $shift->id,
                            'kode_barcode' => $kode,
                            'waktu_mulai' => $waktuMulai,
                            'waktu_akhir' => $waktuAkhir,
                            'status' => 'aktif'
                        ]);

                        $generatedCount++;
                        Log::info("✅ QR berhasil digenerate untuk shift {$shift->nama_shift}");
                        Log::info("   Kode: {$kode}");
                        Log::info("   Berlaku: {$waktuMulai->format('H:i:s')} - {$waktuAkhir->format('H:i:s')}");
                    } else {
                        Log::info("⏭️ QR sudah ada untuk shift {$shift->nama_shift}, skip");
                    }
                }
            }

            Log::info("🎯 Selesai. Total QR digenerate: {$generatedCount}");

        })->everyMinute()->name('generate-qr-codes')->withoutOverlapping();

        // ==================== TASK 2: NONAKTIFKAN QR KADALUARSA ====================
        $schedule->call(function () {
            $expiredCount = Barcode::expiring()
                ->update(['status' => 'expired']);

            if ($expiredCount > 0) {
                Log::info("🗑️ {$expiredCount} QR code telah dinonaktifkan (kadaluarsa)");
            }

        })->everyMinute()->name('expire-qr-codes');

        // ==================== TASK 3: CLEANUP QR LAMA (24 jam) ====================
        $schedule->call(function () {
            $deletedCount = Barcode::where('created_at', '<', now()->subDay())
                ->delete();

            if ($deletedCount > 0) {
                Log::info("🧹 {$deletedCount} QR code lama telah dihapus");
            }

        })->dailyAt('03:00')->name('cleanup-old-qr');
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
    }
}
