<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_shift',
        'jam_mulai',
        'jam_akhir',
        'batas_telat'
    ];

    public function barcodes()
    {
        return $this->hasMany(Barcode::class, 'id_shift');
    }

    public function absensi()
    {
        return $this->hasMany(Absensi::class, 'id_shift');
    }

    public function getJamMulaiHourAttribute()
    {
        return (int) substr($this->jam_mulai, 0, 2);
    }

    // Accessor untuk jam akhir dalam format jam saja
    public function getJamAkhirHourAttribute()
    {
        $hour = (int) substr($this->jam_akhir, 0, 2);
        // Handle midnight (00:00) as 24
        return $hour == 0 ? 24 : $hour;
    }

    // Method untuk mendapatkan durasi shift
    public function getDurasiAttribute()
    {
        $mulai = \Carbon\Carbon::createFromTimeString($this->jam_mulai);
        $akhir = \Carbon\Carbon::createFromTimeString($this->jam_akhir);

        if ($akhir->lessThan($mulai)) {
            $akhir->addDay(); // Untuk shift yang melewati midnight
        }

        return $mulai->diffInHours($akhir);
    }
}

