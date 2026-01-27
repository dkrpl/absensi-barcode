<?php

namespace App\Helpers;

use Carbon\Carbon;

class TimeHelper
{
    public static function formatJam($time)
    {
        return Carbon::parse($time)->format('H:i');
    }

    public static function formatTanggal($date)
    {
        return Carbon::parse($date)->format('d/m/Y');
    }

    public static function formatDateTime($datetime)
    {
        return Carbon::parse($datetime)->format('d/m/Y H:i:s');
    }

    public static function hitungKeterlambatan($jamMulaiShift, $waktuAbsen)
    {
        $jamMulai = Carbon::parse($jamMulaiShift);
        $absensi = Carbon::parse($waktuAbsen);

        return $absensi->diffInMinutes($jamMulai, false);
    }

    public static function statusAbsensi($selisihMenit, $batasTelat)
    {
        if ($selisihMenit > $batasTelat) {
            return 'terlambat';
        }
        return 'hadir';
    }
}
