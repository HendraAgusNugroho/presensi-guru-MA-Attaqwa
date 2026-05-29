<?php

namespace App\Exports;

use App\Models\Presensi;
use App\Models\Guru;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class LaporanPresensiExport implements WithMultipleSheets
{
    public function __construct(
        protected string $dari,
        protected string $sampai,
        protected ?int   $guruId = null,
        protected ?string $status = null,
    ) {}

    public function sheets(): array
    {
        return [
            new LaporanDetailSheet($this->dari, $this->sampai, $this->guruId, $this->status),
            new LaporanRekapSheet($this->dari, $this->sampai, $this->guruId),
        ];
    }
}
