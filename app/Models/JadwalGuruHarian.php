<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalGuruHarian extends Model
{
    protected $table = 'jadwal_guru_harian';

    protected $fillable = [
        'guru_id', 'hari', 'jam_masuk', 'jam_pulang',
    ];

    public static array $namaHari = [
        1 => 'Senin',
        2 => 'Selasa',
        3 => 'Rabu',
        4 => 'Kamis',
        5 => 'Jumat',
    ];

    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }
}
