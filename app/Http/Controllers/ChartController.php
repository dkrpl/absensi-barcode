<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ChartController extends Controller
{
    public function adminAttendanceChart()
    {
        $data = [];
        $labels = [];

        // Get last 7 days
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $labels[] = $date->translatedFormat('D, d M');

            // Count attendance for this day
            $hadir = Absensi::whereDate('tanggal_absen', $date)
                ->where('status', 'hadir')
                ->count();

            $terlambat = Absensi::whereDate('tanggal_absen', $date)
                ->where('status', 'terlambat')
                ->count();

            $data['hadir'][] = $hadir;
            $data['terlambat'][] = $terlambat;
        }

        return response()->json([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Hadir',
                    'data' => $data['hadir'],
                    'backgroundColor' => 'rgba(28, 200, 138, 0.1)',
                    'borderColor' => 'rgba(28, 200, 138, 1)',
                    'borderWidth' => 2
                ],
                [
                    'label' => 'Terlambat',
                    'data' => $data['terlambat'],
                    'backgroundColor' => 'rgba(246, 194, 62, 0.1)',
                    'borderColor' => 'rgba(246, 194, 62, 1)',
                    'borderWidth' => 2
                ]
            ]
        ]);
    }

    public function karyawanAttendanceChart()
    {
        $user = Auth::user();
        $data = [];
        $labels = [];

        // Get last 7 days
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $labels[] = $date->translatedFormat('D, d');

            // Check if user attended this day
            $attended = Absensi::where('id_user', $user->id)
                ->whereDate('tanggal_absen', $date)
                ->exists();

            $data[] = $attended ? 1 : 0;
        }

        return response()->json([
            'labels' => $labels,
            'data' => $data
        ]);
    }
}
