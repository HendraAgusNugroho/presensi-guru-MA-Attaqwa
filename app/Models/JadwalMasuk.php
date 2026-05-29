<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class JadwalMasuk extends Model
{
    protected $table = 'jadwal_masuk';

    protected $fillable = [
        'nama_jadwal', 'jam_masuk', 'batas_toleransi', 'jam_pulang', 'aktif',
    ];

    public static function getAktif(): ?self
    {
        return static::where('aktif', true)->first();
    }

    /** Menit toleransi keterlambatan (selisih jam_masuk → batas_toleransi). */
    public function toleransiMenit(): int
    {
        if (! $this->jam_masuk || ! $this->batas_toleransi) {
            return 10;
        }

        return max(0, (int) Carbon::parse($this->jam_masuk)->diffInMinutes(Carbon::parse($this->batas_toleransi)));
    }

    /** Batas akhir masuk masih dianggap hadir (bukan telat) pada tanggal tertentu. */
    public function batasAkhirPada(Carbon $hari): Carbon
    {
        return Carbon::parse($this->batas_toleransi ?? $this->jam_masuk)->setDateFrom($hari);
    }
}
