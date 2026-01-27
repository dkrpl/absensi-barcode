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

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Create Admin User (with complete data)
        $admin = User::create([
            'uuid_user' => Str::uuid(),
            'nama' => 'Administrator',
            'nip' => 'ADM001',
            'username' => 'admin',
            'email' => 'admin@absensi.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'jabatan' => 'System Administrator',
            'departemen' => 'IT',
            'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Jakarta',
            'tanggal_lahir' => '1985-05-15',
            'alamat' => 'Jl. Admin No. 1, Jakarta Pusat',
            'no_telepon' => '081234567890',
            'status_karyawan' => 'tetap',
            'tanggal_masuk' => '2020-01-01',
            'foto' => null
        ]);

        // Create sample karyawan data
        $karyawanData = [
            [
                'nama' => 'Budi Santoso',
                'nip' => 'KRY001',
                'username' => 'budi',
                'email' => 'budi.santoso@company.com',
                'password' => Hash::make('karyawan123'),
                'role' => 'karyawan',
                'jabatan' => 'Staff IT',
                'departemen' => 'IT',
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Bandung',
                'tanggal_lahir' => '1990-08-20',
                'alamat' => 'Jl. Merdeka No. 45, Bandung',
                'no_telepon' => '081234567891',
                'status_karyawan' => 'tetap',
                'tanggal_masuk' => '2021-03-15',
                'foto' => null
            ],
            [
                'nama' => 'Siti Aminah',
                'nip' => 'KRY002',
                'username' => 'siti',
                'email' => 'siti.aminah@company.com',
                'password' => Hash::make('karyawan123'),
                'role' => 'karyawan',
                'jabatan' => 'HR Staff',
                'departemen' => 'Human Resources',
                'jenis_kelamin' => 'P',
                'tempat_lahir' => 'Surabaya',
                'tanggal_lahir' => '1992-11-10',
                'alamat' => 'Jl. Diponegoro No. 12, Surabaya',
                'no_telepon' => '081234567892',
                'status_karyawan' => 'tetap',
                'tanggal_masuk' => '2021-06-01',
                'foto' => null
            ],
            [
                'nama' => 'Ahmad Fauzi',
                'nip' => 'KRY003',
                'username' => 'ahmad',
                'email' => 'ahmad.fauzi@company.com',
                'password' => Hash::make('karyawan123'),
                'role' => 'karyawan',
                'jabatan' => 'Marketing Executive',
                'departemen' => 'Marketing',
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Semarang',
                'tanggal_lahir' => '1988-03-25',
                'alamat' => 'Jl. Gajah Mada No. 78, Semarang',
                'no_telepon' => '081234567893',
                'status_karyawan' => 'kontrak',
                'tanggal_masuk' => '2023-01-10',
                'tanggal_keluar' => '2024-01-09',
                'foto' => null
            ],
            [
                'nama' => 'Rina Melati',
                'nip' => 'KRY004',
                'username' => 'rina',
                'email' => 'rina.melati@company.com',
                'password' => Hash::make('karyawan123'),
                'role' => 'karyawan',
                'jabatan' => 'Finance Officer',
                'departemen' => 'Finance',
                'jenis_kelamin' => 'P',
                'tempat_lahir' => 'Yogyakarta',
                'tanggal_lahir' => '1995-07-14',
                'alamat' => 'Jl. Malioboro No. 56, Yogyakarta',
                'no_telepon' => '081234567894',
                'status_karyawan' => 'tetap',
                'tanggal_masuk' => '2022-08-20',
                'foto' => null
            ],
            [
                'nama' => 'Dewi Anggraini',
                'nip' => 'KRY005',
                'username' => 'dewi',
                'email' => 'dewi.anggraini@company.com',
                'password' => Hash::make('karyawan123'),
                'role' => 'karyawan',
                'jabatan' => 'Customer Service',
                'departemen' => 'Customer Service',
                'jenis_kelamin' => 'P',
                'tempat_lahir' => 'Malang',
                'tanggal_lahir' => '1993-09-30',
                'alamat' => 'Jl. Ijen No. 23, Malang',
                'no_telepon' => '081234567895',
                'status_karyawan' => 'probation',
                'tanggal_masuk' => now()->subMonths(2)->format('Y-m-d'),
                'foto' => null
            ],
            [
                'nama' => 'Joko Susilo',
                'nip' => 'KRY006',
                'username' => 'joko',
                'email' => 'joko.susilo@company.com',
                'password' => Hash::make('karyawan123'),
                'role' => 'karyawan',
                'jabatan' => 'Production Supervisor',
                'departemen' => 'Production',
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Solo',
                'tanggal_lahir' => '1987-12-05',
                'alamat' => 'Jl. Slamet Riyadi No. 34, Solo',
                'no_telepon' => '081234567896',
                'status_karyawan' => 'tetap',
                'tanggal_masuk' => '2020-11-15',
                'foto' => null
            ],
            [
                'nama' => 'Maya Sari',
                'nip' => 'KRY007',
                'username' => 'maya',
                'email' => 'maya.sari@company.com',
                'password' => Hash::make('karyawan123'),
                'role' => 'karyawan',
                'jabatan' => 'Quality Control',
                'departemen' => 'Quality Assurance',
                'jenis_kelamin' => 'P',
                'tempat_lahir' => 'Medan',
                'tanggal_lahir' => '1991-04-18',
                'alamat' => 'Jl. Gatot Subroto No. 67, Medan',
                'no_telepon' => '081234567897',
                'status_karyawan' => 'tetap',
                'tanggal_masuk' => '2021-09-10',
                'foto' => null
            ],
            [
                'nama' => 'Rizki Pratama',
                'nip' => 'KRY008',
                'username' => 'rizki',
                'email' => 'rizki.pratama@company.com',
                'password' => Hash::make('karyawan123'),
                'role' => 'karyawan',
                'jabatan' => 'Warehouse Staff',
                'departemen' => 'Logistics',
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Bekasi',
                'tanggal_lahir' => '1994-02-22',
                'alamat' => 'Jl. Juanda No. 89, Bekasi',
                'no_telepon' => '081234567898',
                'status_karyawan' => 'kontrak',
                'tanggal_masuk' => '2023-03-01',
                'tanggal_keluar' => '2024-03-01',
                'foto' => null
            ],
            [
                'nama' => 'Linda Wati',
                'nip' => 'KRY009',
                'username' => 'linda',
                'email' => 'linda.wati@company.com',
                'password' => Hash::make('karyawan123'),
                'role' => 'karyawan',
                'jabatan' => 'Sales Representative',
                'departemen' => 'Sales',
                'jenis_kelamin' => 'P',
                'tempat_lahir' => 'Tangerang',
                'tanggal_lahir' => '1989-06-12',
                'alamat' => 'Jl. Sudirman No. 45, Tangerang',
                'no_telepon' => '081234567899',
                'status_karyawan' => 'tetap',
                'tanggal_masuk' => '2019-05-05',
                'foto' => null
            ],
            [
                'nama' => 'Hendra Gunawan',
                'nip' => 'KRY010',
                'username' => 'hendra',
                'email' => 'hendra.gunawan@company.com',
                'password' => Hash::make('karyawan123'),
                'role' => 'karyawan',
                'jabatan' => 'Maintenance Technician',
                'departemen' => 'Maintenance',
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Bogor',
                'tanggal_lahir' => '1986-10-08',
                'alamat' => 'Jl. Pajajaran No. 11, Bogor',
                'no_telepon' => '081234567810',
                'status_karyawan' => 'tetap',
                'tanggal_masuk' => '2018-12-20',
                'tanggal_keluar' => '2023-12-19', // Resigned
                'foto' => null
            ]
        ];

        $karyawanUsers = [];
        foreach ($karyawanData as $data) {
            $data['uuid_user'] = Str::uuid();
            $karyawan = User::create($data);
            $karyawanUsers[] = $karyawan;
        }

        // Create Shifts (more variations) - TANPA KETERANGAN
        $shifts = [
            [
                'nama_shift' => 'Pagi',
                'jam_mulai' => '08:00:00',
                'jam_akhir' => '16:00:00',
                'batas_telat' => 15,
            ],
            [
                'nama_shift' => 'Sore',
                'jam_mulai' => '16:00:00',
                'jam_akhir' => '00:00:00',
                'batas_telat' => 15,
            ],
            [
                'nama_shift' => 'Malam',
                'jam_mulai' => '00:00:00',
                'jam_akhir' => '08:00:00',
                'batas_telat' => 10,
            ],
            [
                'nama_shift' => 'Flexi',
                'jam_mulai' => '09:00:00',
                'jam_akhir' => '17:00:00',
                'batas_telat' => 30,
            ]
        ];

        $shiftObjects = [];
        foreach ($shifts as $shiftData) {
            $shift = Shift::create($shiftData);
            $shiftObjects[] = $shift;
        }

        // Create Barcodes for current and future shifts
        $barcodes = [];
        $now = now();

        // Active barcode for morning shift
        $barcode1 = Barcode::create([
            'id_shift' => $shiftObjects[0]->id,
            'kode_barcode' => Str::uuid(),
            'waktu_mulai' => $now->copy()->setTime(7, 0, 0),
            'waktu_akhir' => $now->copy()->setTime(9, 0, 0),
            'status' => 'aktif'
        ]);
        $barcodes[] = $barcode1;

        // Active barcode for afternoon shift
        $barcode2 = Barcode::create([
            'id_shift' => $shiftObjects[1]->id,
            'kode_barcode' => Str::uuid(),
            'waktu_mulai' => $now->copy()->setTime(15, 0, 0),
            'waktu_akhir' => $now->copy()->setTime(17, 0, 0),
            'status' => 'aktif'
        ]);
        $barcodes[] = $barcode2;

        // Expired barcode
        $barcode3 = Barcode::create([
            'id_shift' => $shiftObjects[0]->id,
            'kode_barcode' => Str::uuid(),
            'waktu_mulai' => $now->copy()->subDays(1)->setTime(7, 0, 0),
            'waktu_akhir' => $now->copy()->subDays(1)->setTime(9, 0, 0),
            'status' => 'nonaktif'
        ]);
        $barcodes[] = $barcode3;

        // Create sample attendance records for all karyawan
        $this->createAttendanceRecords($karyawanUsers, $shiftObjects, $barcodes);

        $this->command->info('✅ Database seeded successfully!');
        $this->command->info('👤 Admin Login:');
        $this->command->info('   Username: admin');
        $this->command->info('   Password: admin123');
        $this->command->info('   Email: admin@absensi.com');

        $this->command->info("\n👥 Sample Karyawan Logins:");
        $this->command->info('   1. Budi Santoso - budi / karyawan123 (IT Department)');
        $this->command->info('   2. Siti Aminah - siti / karyawan123 (HR Department)');
        $this->command->info('   3. Rina Melati - rina / karyawan123 (Finance Department)');
        $this->command->info('   4. Joko Susilo - joko / karyawan123 (Production Department)');

        $this->command->info("\n📊 Data Statistics:");
        $this->command->info('   Total Users: ' . User::count());
        $this->command->info('   - Admin: ' . User::where('role', 'admin')->count());
        $this->command->info('   - Karyawan Aktif: ' . User::where('role', 'karyawan')->whereNull('tanggal_keluar')->count());
        $this->command->info('   - Karyawan Nonaktif: ' . User::where('role', 'karyawan')->whereNotNull('tanggal_keluar')->count());
        $this->command->info('   Total Shifts: ' . Shift::count());
        $this->command->info('   Total Barcodes: ' . Barcode::count());
        $this->command->info('   Total Attendance Records: ' . Absensi::count());

        $this->command->info("\n🔑 Active QR Codes:");
        $activeBarcodes = Barcode::where('status', 'aktif')->get();
        foreach ($activeBarcodes as $barcode) {
            $shift = Shift::find($barcode->id_shift);
            $this->command->info("   - Shift {$shift->nama_shift}: {$barcode->kode_barcode}");
            $this->command->info("     Valid: {$barcode->waktu_mulai->format('H:i')} - {$barcode->waktu_akhir->format('H:i')}");
        }
    }

    private function createAttendanceRecords($karyawanUsers, $shifts, $barcodes)
    {
        $today = now();

        // Define department shifts
        $departmentShifts = [
            'IT' => $shifts[0], // Pagi
            'Human Resources' => $shifts[0], // Pagi
            'Marketing' => $shifts[3], // Flexi
            'Finance' => $shifts[0], // Pagi
            'Customer Service' => $shifts[0], // Pagi
            'Production' => $shifts[1], // Sore
            'Quality Assurance' => $shifts[0], // Pagi
            'Logistics' => $shifts[1], // Sore
            'Sales' => $shifts[3], // Flexi
            'Maintenance' => $shifts[2], // Malam
        ];

        // Create attendance for last 30 days
        for ($i = 0; $i < 30; $i++) {
            $date = $today->copy()->subDays($i);
            $dayOfWeek = $date->dayOfWeek; // 0 = Sunday, 6 = Saturday

            // Skip weekends for most employees
            $isWeekend = $dayOfWeek == 0 || $dayOfWeek == 6;

            // Find appropriate barcode for the day (use morning barcode for most shifts)
            $barcode = $barcodes[0]; // Default to morning barcode

            foreach ($karyawanUsers as $karyawan) {
                // Skip if employee has left before this date
                if ($karyawan->tanggal_keluar && $date->gt($karyawan->tanggal_keluar)) {
                    continue;
                }

                // Skip weekends for non-production/non-maintenance staff
                if ($isWeekend && !in_array($karyawan->departemen, ['Production', 'Maintenance', 'Logistics'])) {
                    continue;
                }

                // Determine shift based on department
                $shift = $departmentShifts[$karyawan->departemen] ?? $shifts[0];

                // Determine barcode based on shift
                foreach ($barcodes as $bc) {
                    if ($bc->id_shift == $shift->id && $bc->status == 'aktif') {
                        $barcode = $bc;
                        break;
                    }
                }

                // 90% chance of attendance on work days
                if (!$isWeekend && rand(0, 100) > 90) {
                    continue; // 10% chance of absent
                }

                // Weekend work probability for production/maintenance
                if ($isWeekend && in_array($karyawan->departemen, ['Production', 'Maintenance', 'Logistics'])) {
                    if (rand(0, 100) > 50) { // 50% chance of working weekends
                        continue;
                    }
                }

                // Determine status
                $rand = rand(0, 100);
                if ($rand < 80) {
                    $status = 'hadir';
                    // On time (75%) or slightly early/late (25%)
                    if (rand(0, 100) < 75) {
                        $minute = rand(-10, 5); // -10 to +5 minutes from shift start
                    } else {
                        $minute = rand(6, 45); // 6-45 minutes late
                        $status = 'terlambat';
                    }
                } elseif ($rand < 90) {
                    $status = 'terlambat';
                    $minute = rand(16, 120); // 16-120 minutes late
                } else {
                    $status = 'izin';
                    $minute = null;
                }

                if ($status != 'izin') {
                    // Get hour from shift start time
                    $hour = (int) substr($shift->jam_mulai, 0, 2);

                    // Handle negative minutes (early arrival)
                    if ($minute < 0) {
                        $minute = 60 + $minute;
                    }

                    Absensi::create([
                        'id_user' => $karyawan->id,
                        'id_shift' => $shift->id,
                        'id_barcode' => $barcode->id,
                        'tanggal_absen' => $date->toDateString(),
                        'waktu_absen' => $date->copy()->setTime($hour, $minute, rand(0, 59)),
                        'status' => $status,
                        // 'keterangan' => $status == 'terlambat' ? 'Terlambat ' . abs($minute) . ' menit' : null
                    ]);
                }
            }
        }

        // Create some special attendance records
        // Today's attendance for some employees
        $todayDate = $today->toDateString();

        // Budi (IT) - on time today
        Absensi::create([
            'id_user' => $karyawanUsers[0]->id,
            'id_shift' => $shifts[0]->id,
            'id_barcode' => $barcodes[0]->id,
            'tanggal_absen' => $todayDate,
            'waktu_absen' => $today->copy()->setTime(7, 58, 0),
            'status' => 'hadir'
        ]);

        // Siti (HR) - late today
        Absensi::create([
            'id_user' => $karyawanUsers[1]->id,
            'id_shift' => $shifts[0]->id,
            'id_barcode' => $barcodes[0]->id,
            'tanggal_absen' => $todayDate,
            'waktu_absen' => $today->copy()->setTime(8, 25, 0),
            'status' => 'terlambat',
            // 'keterangan' => 'Macet di jalan'
        ]);

        // Joko (Production) - on time for afternoon shift
        Absensi::create([
            'id_user' => $karyawanUsers[5]->id,
            'id_shift' => $shifts[1]->id,
            'id_barcode' => $barcodes[1]->id,
            'tanggal_absen' => $todayDate,
            'waktu_absen' => $today->copy()->setTime(15, 55, 0),
            'status' => 'hadir'
        ]);
    }
}
