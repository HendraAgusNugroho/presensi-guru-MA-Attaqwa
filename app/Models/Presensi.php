<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    use HasFactory;

    protected $fillable = [
        'guru_id', 'tanggal', 'jam_masuk', 'jam_pulang',
        'status', 'metode', 'menit_telat', 'keterangan',
        'bukti_file', 'approval_status',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'hadir'       => '<span class="badge-hadir">Hadir</span>',
            'telat'       => '<span class="badge-telat">Telat</span>',
            'tidak_hadir' => '<span class="badge-tidak-hadir">Tidak Hadir</span>',
            'izin'        => '<span class="badge-izin">Izin</span>',
            'sakit'       => '<span class="badge-sakit">Sakit</span>',
            default       => '<span class="badge-default">-</span>',
        };
    }
}
