<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guru extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_pengguna', 'nama', 'email', 'no_hp', 'jabatan', 'mata_pelajaran',
        'id_fingerprint', 'barcode', 'foto', 'jenis_kelamin', 'status',
    ];

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function presensis()
    {
        return $this->hasMany(Presensi::class);
    }

    public function fingerprintLogs()
    {
        return $this->hasMany(FingerprintLog::class);
    }

    public function presensiHariIni()
    {
        return $this->hasOne(Presensi::class)->whereDate('tanggal', today());
    }

    public function jadwalHarian()
    {
        return $this->hasMany(JadwalGuruHarian::class);
    }

    public function jadwalHariIni(): ?JadwalGuruHarian
    {
        $hari = now()->dayOfWeek; // Carbon: 1=Senin, 2=Selasa, ..., 5=Jumat
        return $this->jadwalHarian()->where('hari', $hari)->first();
    }
}
