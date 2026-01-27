<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_user',
        'id_shift',
        'id_barcode',
        'tanggal_absen',
        'waktu_absen',
        'status'
    ];

    protected $casts = [
        'tanggal_absen' => 'date',
        'waktu_absen' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class, 'id_shift');
    }

    public function barcode()
    {
        return $this->belongsTo(Barcode::class, 'id_barcode');
    }
}
