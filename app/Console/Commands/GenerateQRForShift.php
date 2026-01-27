<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Shift;
use App\Models\Barcode;
use Illuminate\Support\Str;
use Carbon\Carbon;

class GenerateQRForShift extends Command
{
    protected $signature = 'qr:generate {--force} {--shift=} {--test}';
    protected $description = 'Generate QR code untuk shift (testing & maintenance)';

    public function handle()
    {
        $force = $this->option('force');
        $shiftId = $this->option('shift');
        $testMode = $this->option('test');

        $now = Carbon::now();
        $this->info("🕒 Waktu sistem: {$now->format('Y-m-d H:i:s')}");

        if ($testMode) {
            $this->info("🧪 MODE TESTING: Simulasi generate QR");
        }

        // Get shifts
        if ($shiftId) {
            $shifts = Shift::where('id', $shiftId)->get();
            if ($shifts->isEmpty()) {
                $this->error("Shift ID {$shiftId} tidak ditemukan!");
                return;
            }
        } else {
            $shifts = Shift::all();
        }

        $generated = 0;
        $skipped = 0;

        foreach ($shifts as $shift) {
            $shiftStart = Carbon::parse($shift->jam_mulai);
            $qrStartTime = $shiftStart->copy()->subHour();

            $this->line("");
            $this->info("🔍 Shift: {$shift->nama_shift} ({$shift->jam_mulai} - {$shift->jam_akhir})");
            $this->line("   QR seharusnya muncul: {$qrStartTime->format('H:i:s')}");

            // Cek waktu
            if (!$force && !$testMode) {
                // Cek apakah sudah waktunya generate QR (±2 menit toleransi)
                $timeDiff = $now->diffInMinutes($qrStartTime, false);

                if (abs($timeDiff) > 2) {
                    $this->warn("   ⏳ Belum waktunya generate (selisih: {$timeDiff} menit)");
                    $skipped++;
                    continue;
                }
            }

            // Cek QR aktif yang sudah ada
            $existingQR = Barcode::where('id_shift', $shift->id)
                ->active()
                ->first();

            if ($existingQR && !$force) {
                $this->warn("   ⚠️ QR sudah aktif untuk shift ini");
                $this->line("     Kode: " . substr($existingQR->kode_barcode, 0, 20) . "...");
                $this->line("     Berlaku: {$existingQR->waktu_mulai->format('H:i')} - {$existingQR->waktu_akhir->format('H:i')}");
                $skipped++;
                continue;
            }

            // Generate QR
            $kode = Str::uuid();
            $waktuMulai = $testMode ? $now->copy() : $now->copy();
            $waktuAkhir = $shiftStart->copy();

            // Jika force mode atau test mode, buat untuk testing
            if ($force || $testMode) {
                $waktuAkhir = $waktuMulai->copy()->addMinutes(60); // 1 jam untuk testing
            }

            if ($testMode) {
                $this->info("   🧪 [TEST] Akan generate QR:");
                $this->line("     Kode: {$kode}");
                $this->line("     Waktu: {$waktuMulai->format('H:i:s')} - {$waktuAkhir->format('H:i:s')}");
            } else {
                Barcode::create([
                    'id_shift' => $shift->id,
                    'kode_barcode' => $kode,
                    'waktu_mulai' => $waktuMulai,
                    'waktu_akhir' => $waktuAkhir,
                    'status' => 'aktif'
                ]);

                $this->info("   ✅ QR berhasil digenerate!");
                $this->line("     Kode: {$kode}");
                $this->line("     Berlaku: {$waktuMulai->format('H:i:s')} - {$waktuAkhir->format('H:i:s')}");
                $generated++;
            }
        }

        $this->line("");
        $this->line("📊 SUMMARY:");
        $this->line("   Total shift: " . $shifts->count());
        $this->line("   Berhasil digenerate: {$generated}");
        $this->line("   Skipped: {$skipped}");

        if ($testMode) {
            $this->line("");
            $this->warn("⚠️ MODE TESTING - Tidak ada data yang disimpan");
        }

        return 0;
    }
}
