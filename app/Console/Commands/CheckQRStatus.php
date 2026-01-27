<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Barcode;
use App\Models\Shift;
use Carbon\Carbon;

class CheckQRStatus extends Command
{
    protected $signature = 'qr:status';
    protected $description = 'Cek status QR code saat ini';

    public function handle()
    {
        $now = Carbon::now();

        $this->info("🕒 Status Sistem QR Code");
        $this->line("Waktu server: {$now->format('Y-m-d H:i:s')}");
        $this->line("");

        // Check active QR
        $activeQR = Barcode::active()->with('shift')->get();

        if ($activeQR->count() > 0) {
            $this->info("✅ QR Code Aktif ({$activeQR->count()}):");

            foreach ($activeQR as $qr) {
                $remaining = $now->diffInMinutes($qr->waktu_akhir, false);
                $status = $remaining > 0 ? "🟢" : "🔴";

                $this->line("   {$status} Shift: {$qr->shift->nama_shift}");
                $this->line("      Kode: " . substr($qr->kode_barcode, 0, 20) . "...");
                $this->line("      Berlaku: {$qr->waktu_mulai->format('H:i:s')} - {$qr->waktu_akhir->format('H:i:s')}");
                $this->line("      Sisa waktu: " . max(0, $remaining) . " menit");
                $this->line("");
            }
        } else {
            $this->warn("⚠️ Tidak ada QR code aktif");
        }

        // Check upcoming shifts
        $shifts = Shift::orderBy('jam_mulai')->get();
        $this->info("📅 Jadwal Shift Hari Ini:");

        foreach ($shifts as $shift) {
            $shiftStart = Carbon::parse($shift->jam_mulai);
            $qrStartTime = $shiftStart->copy()->subHour();
            $timeToQR = $now->diffInMinutes($qrStartTime, false);

            $icon = $timeToQR <= 0 ? "🔵" : "⚪";
            $status = $timeToQR <= 0 ? "Sudah lewat" : "Akan datang";

            $this->line("   {$icon} {$shift->nama_shift} ({$shift->jam_mulai} - {$shift->jam_akhir})");
            $this->line("      QR muncul: {$qrStartTime->format('H:i')} ({$status})");

            if ($timeToQR > 0) {
                $this->line("      QR akan muncul dalam: {$timeToQR} menit");
            }

            $this->line("");
        }

        // Check expired QR (should be cleaned by scheduler)
        $expiredCount = Barcode::where('status', 'aktif')
            ->where('waktu_akhir', '<', $now)
            ->count();

        if ($expiredCount > 0) {
            $this->error("❌ Ada {$expiredCount} QR yang seharusnya sudah expired!");
            $this->line("   Jalankan: php artisan schedule:run");
        }

        return 0;
    }
}
