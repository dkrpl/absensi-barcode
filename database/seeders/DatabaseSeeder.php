<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Shift;
use App\Models\Barcode;
use App\Models\Absensi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Truncate tables dengan aman
        User::truncate();
        Shift::truncate();
        Barcode::truncate();
        Absensi::truncate();

        $this->command->info('✅ Tables truncated.');

        // Load data dari SQL dump
        $sqlFilePath = database_path('seeders/data.sql');
        if (file_exists($sqlFilePath)) {
            $sql = file_get_contents($sqlFilePath);
            preg_match_all('/INSERT INTO `([^`]+)` \((.*?)\) VALUES\s*(.*?);/is', $sql, $matches, PREG_SET_ORDER);

            // HANYA MENGIMPORT SHIFTS DAN USERS
            $allowed_tables = ['shifts', 'users'];
            $tables_inserted = [];

            foreach ($matches as $match) {
                $table = $match[1];
                if (in_array($table, $allowed_tables)) {
                    // Eksekusi insert statement
                    DB::unprepared($match[0]);
                    $tables_inserted[] = $table;
                }
            }
            $this->command->info('✅ Imported data from SQL dump untuk tabel: ' . implode(', ', array_unique($tables_inserted)));
        } else {
            $this->command->error('❌ File data.sql tidak ditemukan di database/seeders/');
            return;
        }

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // List nama user yang ingin dipertahankan
        $allowedNames = ['briyan', 'alvi', 'dana', 'kepin', 'nana'];

        // Hapus semua user yang bukan admin dan namanya tidak ada di daftar yang diizinkan
        $usersToDelete = User::where('role', '!=', 'admin')
            ->get()
            ->filter(function ($user) use ($allowedNames) {
                $nameLower = strtolower($user->nama);
                foreach ($allowedNames as $allowedName) {
                    // Pengecekan substring (contoh "briyan" masuk ke "BRIYAN NUGROHO")
                    if (str_contains($nameLower, strtolower($allowedName))) {
                        return false; // Jangan dihapus
                    }
                }
                return true; // Hapus
            });

        foreach ($usersToDelete as $user) {
            $user->delete();
        }

        $this->command->info('✅ Menghapus user selain yang diminta (Briyan, Alvi, Dana, Kepin, Nana) dan Admin.');

        // Cek dan buat user dummy untuk Alvi, Dana, Kepin jika belum ada
        $dummyUsers = ['alvi', 'dana', 'kepin'];
        foreach ($dummyUsers as $dummyName) {
            $exists = User::whereRaw('LOWER(nama) LIKE ?', ["%{$dummyName}%"])->exists();
            
            if (!$exists) {
                User::create([
                    'uuid_user' => Str::uuid(),
                    'nama' => ucfirst($dummyName),
                    'nip' => 'DUMMY-' . strtoupper($dummyName),
                    'username' => strtolower($dummyName),
                    'email' => strtolower($dummyName) . '@cafe.com',
                    'password' => Hash::make('password123'),
                    'role' => 'karyawan',
                    'status_karyawan' => 'probation',
                    'tanggal_masuk' => now()->format('Y-m-d'),
                ]);
                $this->command->info("✅ Dibuat user dummy baru untuk: {$dummyName}");
            }
        }

        // Generate Active Barcodes untuk setiap shift
        $shifts = Shift::all();
        $barcodes = [];
        $now = now();
        foreach ($shifts as $shift) {
            $barcode = Barcode::create([
                'id_shift' => $shift->id,
                'kode_barcode' => Str::uuid(),
                // Buat waktu mulai dari kemarin sampai sebulan ke depan supaya tetap aktif
                'waktu_mulai' => $now->copy()->subDays(1),
                'waktu_akhir' => $now->copy()->addMonths(2),
                'status' => 'aktif'
            ]);
            $barcodes[$shift->id] = $barcode;
        }

        $this->command->info('✅ Generated Active Barcodes untuk masing-masing shift.');

        // Generate data absensi untuk user-user spesifik ini
        $usersToAbsen = User::where('role', 'karyawan')
            ->get()
            ->filter(function ($user) use ($allowedNames) {
                $nameLower = strtolower($user->nama);
                foreach ($allowedNames as $allowedName) {
                    if (str_contains($nameLower, strtolower($allowedName))) {
                        return true;
                    }
                }
                return false;
            });

        $monthsToGenerate = [
            // 3 => maret (random)
            ['month' => 3, 'year' => 2026, 'type' => 'random'],
            // 5 => mei (random)
            ['month' => 5, 'year' => 2026, 'type' => 'random'],
            // 6 => juni (full)
            ['month' => 6, 'year' => 2026, 'type' => 'full']
        ];

        foreach ($usersToAbsen as $user) {
            // Assign shift ID secara berurutan atau default (contoh pakai shift ID 1, atau random)
            $shiftId = $shifts->first()->id; 
            if (str_contains(strtolower($user->nama), 'nana')) $shiftId = $shifts->get(1)->id ?? $shifts->first()->id;
            
            $shift = Shift::find($shiftId);
            $barcode = $barcodes[$shift->id];

            foreach ($monthsToGenerate as $config) {
                $month = $config['month'];
                $year = $config['year'];
                $type = $config['type'];

                $daysInMonth = Carbon::create($year, $month, 1)->daysInMonth;

                for ($day = 1; $day <= $daysInMonth; $day++) {
                    $date = Carbon::create($year, $month, $day);
                    $dayOfWeek = $date->dayOfWeek; // 0 (Sunday) to 6 (Saturday)

                    // Lewati hari Minggu
                    if ($dayOfWeek == 0) {
                        continue;
                    }

                    if ($type == 'full') {
                        // Full attendance: always present and on time
                        $status = 'hadir';
                        // Datang 10-30 menit sebelum shift
                        $minuteOffset = rand(-30, -10);
                    } else {
                        // Random attendance
                        $rand = rand(1, 100);
                        if ($rand <= 15) {
                            // 15% tidak absen
                            continue;
                        } elseif ($rand <= 70) {
                            // 55% hadir tepat waktu
                            $status = 'hadir';
                            $minuteOffset = rand(-30, 0);
                        } else {
                            // 30% terlambat
                            $status = 'terlambat';
                            $minuteOffset = rand(5, 45);
                        }
                    }

                    // Menghitung waktu absen
                    $shiftStartParts = explode(':', $shift->jam_mulai);
                    $absenTime = $date->copy()
                        ->setTime((int)$shiftStartParts[0], (int)$shiftStartParts[1], 0)
                        ->addMinutes($minuteOffset);

                    Absensi::create([
                        'id_user' => $user->id,
                        'id_shift' => $shift->id,
                        'id_barcode' => $barcode->id,
                        'tanggal_absen' => $date->toDateString(),
                        'waktu_absen' => $absenTime,
                        'status' => $status,
                        'created_at' => $absenTime,
                        'updated_at' => $absenTime
                    ]);
                }
            }
        }

        $this->command->info('✅ Generated absensi (Maret Random, Mei Random, Juni Full) untuk user target.');
        $this->command->info('   Total Attendance Records sekarang: ' . Absensi::count());
        $this->command->info('✅ Seeder selesai dieksekusi!');
    }
}
