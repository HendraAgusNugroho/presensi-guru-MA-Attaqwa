<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Presensi;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanPresensiExport;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $gurus  = Guru::where('status', 'aktif')->orderBy('nama')->get();
        $dari   = $request->dari   ?? Carbon::now()->startOfMonth()->format('Y-m-d');
        $sampai = $request->sampai ?? Carbon::now()->format('Y-m-d');
        $guruId = $request->guru_id;
        $status = $request->status;

        // ── Query dasar presensi yang ADA di DB ──────────────────────
        $baseQuery = Presensi::with('guru')
            ->whereBetween('tanggal', [$dari, $sampai])
            ->when($guruId, fn($q) => $q->where('guru_id', $guruId));

        // ── Guru yang tidak memiliki SATU PUN presensi dalam rentang ─
        $existingGuruIds = (clone $baseQuery)->pluck('guru_id')->unique();

        $guruTanpaPresensi = Guru::where('status', 'aktif')
            ->whereNotIn('id', $existingGuruIds)
            ->when($guruId, fn($q) => $q->where('id', $guruId))
            ->orderBy('nama')
            ->get();

        // Buat virtual baris "tidak_hadir" untuk guru tanpa presensi sama sekali
        $virtualRows = $guruTanpaPresensi->map(function ($g) use ($dari) {
            return (object) [
                'id'              => null,
                'guru_id'         => $g->id,
                'guru'            => $g,
                'tanggal'         => Carbon::parse($dari),
                'jam_masuk'       => null,
                'jam_pulang'      => null,
                'status'          => 'tidak_hadir',
                'metode'          => 'sistem',
                'menit_telat'     => 0,
                'keterangan'      => 'Tidak ada presensi dalam periode ini',
                'bukti_file'      => null,
                'approval_status' => null,
                'virtual'         => true,
            ];
        });

        // ── Rekap statistik (virtual tidak_hadir dimasukkan) ─────────
        $rekap = [
            'total'       => (clone $baseQuery)->count() + $virtualRows->count(),
            'hadir'       => (clone $baseQuery)->where('status', 'hadir')->count(),
            'telat'       => (clone $baseQuery)->where('status', 'telat')->count(),
            'tidak_hadir' => (clone $baseQuery)->where('status', 'tidak_hadir')->count() + $virtualRows->count(),
            'izin'        => (clone $baseQuery)->where('status', 'izin')->count(),
            'sakit'       => (clone $baseQuery)->where('status', 'sakit')->count(),
            'alpha'       => (clone $baseQuery)->where('status', 'alpha')->count(),
        ];

        // ── Ambil presensi dari DB dengan filter status ───────────────
        $dbQuery = (clone $baseQuery)
            ->when($status && $status !== 'tidak_hadir', fn($q) => $q->where('status', $status))
            ->when($status === 'tidak_hadir', fn($q) => $q->where('status', 'tidak_hadir'))
            ->orderBy('tanggal', 'desc')
            ->orderBy('jam_masuk');

        $existingPresensis = $dbQuery->get();

        // ── Gabungkan dengan virtual rows ─────────────────────────────
        if (!$status || $status === 'tidak_hadir') {
            $allPresensis = $existingPresensis->concat($virtualRows);
        } else {
            $allPresensis = $existingPresensis;
        }

        // ── Paginate collection yang sudah digabung ───────────────────
        $perPage  = 25;
        $page     = $request->get('page', 1);
        $items    = $allPresensis->forPage($page, $perPage)->values();

        $presensis = new LengthAwarePaginator(
            $items,
            $allPresensis->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('laporan.index', compact('gurus', 'dari', 'sampai', 'guruId', 'status', 'presensis', 'rekap'));
    }

    public function exportPdf(Request $request)
    {
        $dari   = $request->dari   ?? Carbon::now()->startOfMonth()->format('Y-m-d');
        $sampai = $request->sampai ?? Carbon::now()->format('Y-m-d');
        $guruId = $request->guru_id;
        $status = $request->status;

        $presensis = Presensi::with('guru')
            ->whereBetween('tanggal', [$dari, $sampai])
            ->when($guruId, fn($q) => $q->where('guru_id', $guruId))
            ->when($status,  fn($q) => $q->where('status', $status))
            ->orderBy('tanggal')
            ->orderBy('jam_masuk')
            ->get();

        // Tambahkan virtual tidak_hadir ke PDF juga
        if (!$status || $status === 'tidak_hadir') {
            $existingGuruIds    = $presensis->pluck('guru_id')->unique();
            $guruTanpaPresensi  = Guru::where('status', 'aktif')
                ->whereNotIn('id', $existingGuruIds)
                ->when($guruId, fn($q) => $q->where('id', $guruId))
                ->orderBy('nama')
                ->get();

            $virtualRows = $guruTanpaPresensi->map(fn($g) => (object) [
                'id'          => null,
                'guru'        => $g,
                'tanggal'     => Carbon::parse($dari),
                'jam_masuk'   => null,
                'jam_pulang'  => null,
                'status'      => 'tidak_hadir',
                'metode'      => 'sistem',
                'menit_telat' => 0,
                'keterangan'  => 'Tidak ada presensi dalam periode ini',
                'virtual'     => true,
            ]);

            $presensis = $presensis->concat($virtualRows);
        }

        $rekap = [
            'hadir'       => $presensis->where('status', 'hadir')->count(),
            'telat'       => $presensis->where('status', 'telat')->count(),
            'tidak_hadir' => $presensis->where('status', 'tidak_hadir')->count(),
            'izin'        => $presensis->where('status', 'izin')->count(),
            'sakit'       => $presensis->where('status', 'sakit')->count(),
            'alpha'       => $presensis->where('status', 'alpha')->count(),
        ];

        $pdf = Pdf::loadView('laporan.pdf', compact('presensis', 'dari', 'sampai', 'rekap'))
                  ->setPaper('a4', 'landscape');

        return $pdf->download("laporan-presensi-{$dari}-{$sampai}.pdf");
    }

    public function exportExcel(Request $request)
    {
        $dari   = $request->dari   ?? Carbon::now()->startOfMonth()->format('Y-m-d');
        $sampai = $request->sampai ?? Carbon::now()->format('Y-m-d');

        return Excel::download(
            new LaporanPresensiExport($dari, $sampai, $request->guru_id, $request->status),
            "laporan-presensi-{$dari}-{$sampai}.xlsx"
        );
    }
}
