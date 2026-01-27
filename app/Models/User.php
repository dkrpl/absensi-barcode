<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'uuid_user',
        'nama',
        'nip',
        'username',
        'email',
        'password',
        'role',
        'jabatan',
        'departemen',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat',
        'no_telepon',
        'status_karyawan',
        'tanggal_masuk',
        'tanggal_keluar',
        'foto'
    ];

    protected $hidden = [
        'password'
    ];

    public function absensi()
    {
        return $this->hasMany(Absensi::class, 'id_user');
    }

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_masuk' => 'date',
        'tanggal_keluar' => 'date',
        'email_verified_at' => 'datetime',
    ];

    // Accessor untuk nama lengkap dengan gelar
    public function getNamaLengkapAttribute()
    {
        $jk = $this->jenis_kelamin == 'L' ? 'Bapak' : 'Ibu';
        return $jk . ' ' . $this->nama;
    }

    // Accessor untuk usia
    public function getUsiaAttribute()
    {
        if (!$this->tanggal_lahir) return null;
        return now()->diffInYears($this->tanggal_lahir);
    }

    // Accessor untuk masa kerja
    public function getMasaKerjaAttribute()
    {
        if (!$this->tanggal_masuk) return null;

        $diff = now()->diff($this->tanggal_masuk);

        if ($diff->y > 0) {
            return $diff->y . ' tahun ' . $diff->m . ' bulan';
        } elseif ($diff->m > 0) {
            return $diff->m . ' bulan ' . $diff->d . ' hari';
        } else {
            return $diff->d . ' hari';
        }
    }

    // Scope untuk karyawan aktif
    public function scopeAktif($query)
    {
        return $query->whereNull('tanggal_keluar')
                     ->where('role', 'karyawan');
    }

    // Scope untuk karyawan berdasarkan departemen
    public function scopeByDepartemen($query, $departemen)
    {
        return $query->where('departemen', $departemen);
    }
}
