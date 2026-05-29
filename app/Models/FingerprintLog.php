<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FingerprintLog extends Model
{
    protected $table = 'fingerprint_logs';

    protected $fillable = [
        'id_fingerprint', 'guru_id', 'waktu_scan', 'tipe', 'diproses',
    ];

    protected $casts = [
        'waktu_scan' => 'datetime',
        'diproses'   => 'boolean',
    ];

    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }
}
