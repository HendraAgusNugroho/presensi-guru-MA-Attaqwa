<?php

namespace App\Exports;

use App\Models\Guru;
use App\Models\Presensi;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class LaporanDetailSheet implements FromCollection, WithTitle, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithEvents
{
    protected int $no = 0;

    public function __construct(
        protected string  $dari,
        protected string  $sampai,
        protected ?int    $guruId = null,
        protected ?string $status = null,
    ) {}

    public function title(): string
    {
        return 'Detail Jam Masuk & Pulang';
    }

    public function collection()
    {
        // Presensi yang tersimpan di DB
        $existing = Presensi::with('guru')
            ->whereBetween('tanggal', [$this->dari, $this->sampai])
            ->when($this->guruId, fn($q) => $q->where('guru_id', $this->guruId))
            ->when($this->status && $this->status !== 'tidak_hadir',
                   fn($q) => $q->where('status', $this->status))
            ->when($this->status === 'tidak_hadir',
                   fn($q) => $q->where('status', 'tidak_hadir'))
            ->orderBy('tanggal')
            ->orderBy('jam_masuk')
            ->get();

        // Virtual "Tidak Hadir" hanya untuk guru yang PUNYA JADWAL di hari tersebut
        if (!$this->status || $this->status === 'tidak_hadir') {

            // Guru yang sudah punya presensi di DB dalam periode ini
            $existingGuruIds = $existing->pluck('guru_id')->unique();

            // Guru aktif yang tidak punya record sama sekali
            $guruTanpaPresensi = Guru::with('jadwalHarian')
                ->where('status', 'aktif')
                ->whereNotIn('id', $existingGuruIds)
                ->when($this->guruId, fn($q) => $q->where('id', $this->guruId))
                ->orderBy('nama')
                ->get();

            $virtualRows = collect();

            foreach ($guruTanpaPresensi as $g) {
                $hariJadwal = $g->jadwalHarian->pluck('hari')->unique()->toArray();

                // Skip guru yang tidak punya jadwal sama sekali
                if (empty($hariJadwal)) continue;

                // Cek apakah guru ini punya jadwal di minimal satu hari dalam periode
                $tgl      = Carbon::parse($this->dari);
                $tglAkhir = Carbon::parse($this->sampai);
                $adaJadwal = false;

                while ($tgl->lte($tglAkhir)) {
                    if (in_array($tgl->dayOfWeek, $hariJadwal)) {
                        $adaJadwal = true;
                        break;
                    }
                    $tgl->addDay();
                }

                if (!$adaJadwal) continue;

                $virtualRows->push((object) [
                    'id'          => null,
                    'guru_id'     => $g->id,
                    'guru'        => $g,
                    'tanggal'     => Carbon::parse($this->dari),
                    'jam_masuk'   => null,
                    'jam_pulang'  => null,
                    'status'      => 'tidak_hadir',
                    'metode'      => 'sistem',
                    'menit_telat' => 0,
                    'keterangan'  => 'Tidak ada presensi dalam periode ini',
                    'virtual'     => true,
                ]);
            }

            return $existing->concat($virtualRows);
        }

        return $existing;
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Guru',
            'NIP',
            'Tanggal',
            'Hari',
            'Jam Masuk',
            'Jam Pulang',
            'Status Kehadiran',
            'Keterangan Terlambat',
            'Total Jam Kerja',
            'Metode',
        ];
    }

    public function map($row): array
    {
        $this->no++;

        $totalJam = '-';
        if ($row->jam_masuk && $row->jam_pulang) {
            try {
                $masuk  = Carbon::parse($row->tanggal->format('Y-m-d') . ' ' . $row->jam_masuk);
                $pulang = Carbon::parse($row->tanggal->format('Y-m-d') . ' ' . $row->jam_pulang);
                if ($pulang->gt($masuk)) {
                    $diff     = $masuk->diffInMinutes($pulang);
                    $totalJam = floor($diff / 60) . ' jam ' . ($diff % 60) . ' mnt';
                }
            } catch (\Exception $e) {}
        }

        $keteranganTelat = '-';
        if ($row->status === 'telat' && ($row->menit_telat ?? 0) > 0) {
            $keteranganTelat = 'Terlambat ' . $row->menit_telat . ' menit';
        }

        $statusLabel = match($row->status) {
            'hadir'       => 'HADIR',
            'telat'       => 'TERLAMBAT',
            'tidak_hadir' => 'TIDAK HADIR',
            'izin'        => 'IZIN',
            'sakit'       => 'SAKIT',
            'alpha'       => 'ALPHA',
            default       => strtoupper($row->status),
        };

        return [
            $this->no,
            $row->guru->nama ?? '-',
            $row->guru->id_pengguna  ?? '-',
            $row->tanggal->format('d/m/Y'),
            $row->tanggal->isoFormat('dddd'),
            $row->jam_masuk  ?? '-',
            $row->jam_pulang ?? '-',
            $statusLabel,
            $keteranganTelat,
            $totalJam,
            ucfirst($row->metode ?? '-'),
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 28,
            'C' => 22,
            'D' => 14,
            'E' => 14,
            'F' => 12,
            'G' => 12,
            'H' => 16,
            'I' => 24,
            'J' => 16,
            'K' => 12,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1B5E20']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet   = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();

                $sheet->insertNewRowBefore(1, 3);

                $sheet->setCellValue('A1', 'LAPORAN DETAIL JAM MASUK & PULANG GURU');
                $sheet->setCellValue('A2', 'MADRASAH ALIYAH ATTAQWA BENDA TANGERANG — YPIA DAARUL MU\'MIN');
                $sheet->setCellValue('A3', 'Periode: ' . Carbon::parse($this->dari)->isoFormat('D MMMM Y') . ' s/d ' . Carbon::parse($this->sampai)->isoFormat('D MMMM Y'));

                $lastCol = 'K';
                foreach ([1, 2, 3] as $row) {
                    $sheet->mergeCells("A{$row}:{$lastCol}{$row}");
                    $sheet->getStyle("A{$row}")->applyFromArray([
                        'font'      => ['bold' => true, 'color' => ['rgb' => $row <= 2 ? 'FFFFFF' : '1B5E20'], 'size' => $row === 1 ? 14 : ($row === 2 ? 11 : 10)],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                        'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $row === 1 ? '1B5E20' : ($row === 2 ? '2E7D32' : 'F9A825')]],
                    ]);
                }

                $sheet->getStyle("A4:{$lastCol}4")->applyFromArray([
                    'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1B5E20']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension(4)->setRowHeight(22);

                for ($row = 5; $row <= $lastRow + 3; $row++) {
                    $statusCell = $sheet->getCell('H' . $row)->getValue();
                    $bg = match($statusCell) {
                        'HADIR'       => 'E8F5E9',
                        'TERLAMBAT'   => 'FFFDE7',
                        'TIDAK HADIR' => 'FFEBEE',
                        'IZIN'        => 'E3F2FD',
                        'SAKIT'       => 'FFF3E0',
                        'ALPHA'       => 'FCE4EC',
                        default       => null,
                    };
                    if ($bg) {
                        $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray([
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
                        ]);
                    } elseif ($row % 2 === 0) {
                        $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray([
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F1F8E9']],
                        ]);
                    }
                }

                $sheet->getStyle("A4:{$lastCol}" . ($lastRow + 3))->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'C8E6C9']],
                    ],
                ]);

                $sheet->freezePane('A5');

                $noteRow = $lastRow + 4;
                $sheet->setCellValue("A{$noteRow}", 'Catatan: Status "TIDAK HADIR" otomatis = guru punya jadwal di hari tersebut namun tidak ada data presensi. Guru tanpa jadwal tidak ditampilkan.');
                $sheet->mergeCells("A{$noteRow}:{$lastCol}{$noteRow}");
                $sheet->getStyle("A{$noteRow}")->applyFromArray([
                    'font'      => ['italic' => true, 'size' => 9, 'color' => ['rgb' => '5D4037']],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFF9C4']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                ]);
            },
        ];
    }
}
