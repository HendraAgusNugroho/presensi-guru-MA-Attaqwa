<?php

namespace App\Exports;

use App\Models\Guru;
use App\Models\Presensi;
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
use Carbon\Carbon;

class LaporanRekapSheet implements FromCollection, WithTitle, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithEvents
{
    protected int $no = 0;

    public function __construct(
        protected string $dari,
        protected string $sampai,
        protected ?int   $guruId = null,
    ) {}

    public function title(): string
    {
        return 'Rekap Keseluruhan Presensi';
    }

    public function collection()
    {
        $query = Guru::with('jadwalHarian')->where('status', 'aktif')->orderBy('nama');
        if ($this->guruId) {
            $query->where('id', $this->guruId);
        }

        // Hanya guru yang punya minimal 1 hari dengan jam_masuk tidak null
        return $query->get()->filter(function ($guru) {
            return $guru->jadwalHarian->whereNotNull('jam_masuk')->isNotEmpty();
        })->values();
    }

    public function headings(): array
    {
        return [
            'No', 'Nama Guru', 'NIP', 'Jabatan',
            'Hadir', 'Terlambat', 'Izin', 'Sakit',
            'Tidak Hadir', 'Alpha', 'Total Hari Jadwal', 'Persentase Kehadiran',
        ];
    }

    private function n(int $val): string
    {
        return (string) $val;
    }

    public function map($guru): array
    {
        // Ambil HANYA hari yang jam_masuknya tidak null (ada jadwal)
        $hariJadwal = $guru->jadwalHarian
            ->whereNotNull('jam_masuk')
            ->pluck('hari')
            ->unique()
            ->toArray();

        if (empty($hariJadwal)) return [];

        // Hitung total hari dalam periode yang sesuai jadwal guru
        $totalHariJadwal = 0;
        $tgl      = Carbon::parse($this->dari);
        $tglAkhir = Carbon::parse($this->sampai);
        while ($tgl->lte($tglAkhir)) {
            if (in_array($tgl->dayOfWeek, $hariJadwal)) {
                $totalHariJadwal++;
            }
            $tgl->addDay();
        }

        if ($totalHariJadwal === 0) return [];

        $this->no++;

        $presensiGuru = Presensi::where('guru_id', $guru->id)
            ->whereBetween('tanggal', [$this->dari, $this->sampai])
            ->get();

        $hadir      = (int) $presensiGuru->where('status', 'hadir')->count();
        $telat      = (int) $presensiGuru->where('status', 'telat')->count();
        $izin       = (int) $presensiGuru->where('status', 'izin')->count();
        $sakit      = (int) $presensiGuru->where('status', 'sakit')->count();
        $tidakHadir = (int) $presensiGuru->where('status', 'tidak_hadir')->count();
        $alpha      = (int) $presensiGuru->where('status', 'alpha')->count();

        // Hari jadwal (jam_masuk tidak null) yang tidak ada record presensi = tidak hadir
        // Hitung presensi hanya di hari yang ada jadwal
        $presensiDiHariJadwal = $presensiGuru->filter(function ($p) use ($hariJadwal) {
            return in_array(Carbon::parse($p->tanggal)->dayOfWeek, $hariJadwal);
        })->count();

        $tidakHadir += max(0, $totalHariJadwal - $presensiDiHariJadwal);

        $totalHadirEfektif = $hadir + $telat;
        $persen = $totalHariJadwal > 0
            ? round(($totalHadirEfektif / $totalHariJadwal) * 100, 1) . '%'
            : '0%';

        return [
            $this->no,
            $guru->nama,
            $guru->id_pengguna,
            $guru->jabatan ?? '-',
            $this->n($hadir),
            $this->n($telat),
            $this->n($izin),
            $this->n($sakit),
            $this->n($tidakHadir),
            $this->n($alpha),
            $this->n($totalHariJadwal),
            $persen,
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,  'B' => 28, 'C' => 22, 'D' => 20,
            'E' => 10, 'F' => 12, 'G' => 8,  'H' => 8,
            'I' => 14, 'J' => 8,  'K' => 18, 'L' => 22,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
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
                $lastCol = 'L';

                $sheet->insertNewRowBefore(1, 3);

                $sheet->setCellValue('A1', 'REKAP KESELURUHAN PRESENSI GURU');
                $sheet->setCellValue('A2', "MADRASAH ALIYAH ATTAQWA BENDA TANGERANG \u{2014} YPIA DAARUL MU'MIN");
                $sheet->setCellValue('A3', 'Periode: ' . Carbon::parse($this->dari)->isoFormat('D MMMM Y') . ' s/d ' . Carbon::parse($this->sampai)->isoFormat('D MMMM Y') . '   |   Tanggal Cetak: ' . now()->isoFormat('D MMMM Y'));

                foreach ([1, 2, 3] as $row) {
                    $sheet->mergeCells("A{$row}:{$lastCol}{$row}");
                    $sheet->getStyle("A{$row}")->applyFromArray([
                        'font'      => ['bold' => true, 'color' => ['rgb' => $row <= 2 ? 'FFFFFF' : '1B5E20'], 'size' => $row === 1 ? 14 : ($row === 2 ? 11 : 10)],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                        'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $row === 1 ? '1B5E20' : ($row === 2 ? '2E7D32' : 'F9A825')]],
                    ]);
                }

                $sheet->getStyle("A4:{$lastCol}4")->applyFromArray([
                    'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1B5E20']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension(4)->setRowHeight(24);

                for ($row = 5; $row <= $lastRow + 3; $row++) {
                    if ($row % 2 === 0) {
                        $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray([
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F1F8E9']],
                        ]);
                    }
                    $persenVal = (string) $sheet->getCell("L{$row}")->getValue();
                    $persen    = (float) str_replace('%', '', $persenVal);
                    $bg        = $persen >= 90 ? '43A047' : ($persen >= 75 ? 'F9A825' : 'E53935');
                    $sheet->getStyle("L{$row}")->applyFromArray([
                        'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                        'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ]);
                    $sheet->getStyle("E{$row}:K{$row}")->applyFromArray([
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ]);
                }

                $sheet->getStyle("A4:{$lastCol}" . ($lastRow + 3))->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'C8E6C9']],
                    ],
                ]);

                $sheet->freezePane('A5');

                $noteRow = $lastRow + 4;
                $sheet->setCellValue("A{$noteRow}", 'Catatan: Tidak Hadir = hari ada jadwal (jam masuk diisi) tapi tidak ada presensi. Hari kosong (--:--) tidak dihitung.');
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
