<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barcode extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_shift',
        'kode_barcode',
        'waktu_mulai',
        'waktu_akhir',
        'status'
    ];

    protected $casts = [
        'waktu_mulai' => 'datetime',
        'waktu_akhir' => 'datetime',
    ];

    public function shift()
    {
        return $this->belongsTo(Shift::class, 'id_shift');
    }

    public function absensi()
    {
        return $this->hasMany(Absensi::class, 'id_barcode');
    }

    // Method untuk cek apakah QR aktif
    public function isActive()
    {
        return $this->status === 'aktif' &&
               now()->between($this->waktu_mulai, $this->waktu_akhir);
    }

    // Scope untuk QR aktif
    public function scopeActive($query)
    {
        return $query->where('status', 'aktif')
                     ->where('waktu_mulai', '<=', now())
                     ->where('waktu_akhir', '>=', now());
    }

    // Scope untuk shift tertentu
    public function scopeForShift($query, $shiftId)
    {
        return $query->where('id_shift', $shiftId);
    }

    // Scope untuk QR yang akan kadaluarsa
    public function scopeExpiring($query)
    {
        return $query->where('waktu_akhir', '<', now())
                     ->where('status', 'aktif');
    }
}
