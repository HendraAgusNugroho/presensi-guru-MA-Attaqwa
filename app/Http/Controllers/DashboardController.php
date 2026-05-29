<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Presensi;
use App\Models\JadwalMasuk;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $totalGuru = Guru::where('status', 'aktif')->count();

        $hadirHariIni   = Presensi::whereDate('tanggal', $today)->where('status', 'hadir')->count();
        $telatHariIni   = Presensi::whereDate('tanggal', $today)->where('status', 'telat')->count();
        $tidakHadir     = $totalGuru - Presensi::whereDate('tanggal', $today)->whereIn('status', ['hadir','telat','izin','sakit'])->count();
        $izinHariIni    = Presensi::whereDate('tanggal', $today)->where('status', 'izin')->count();
        $sakitHariIni   = Presensi::whereDate('tanggal', $today)->where('status', 'sakit')->count();

        // Data grafik 7 hari terakhir — satu query saja (bukan N+1)
        $start7     = Carbon::today()->subDays(6)->toDateString();
        $end7       = Carbon::today()->toDateString();
        $rawGrafik  = Presensi::whereBetween('tanggal', [$start7, $end7])
            ->whereIn('status', ['hadir', 'telat', 'tidak_hadir'])
            ->selectRaw('tanggal, status, COUNT(*) as total')
            ->groupBy('tanggal', 'status')
            ->get()
            ->groupBy(fn ($r) => $r->tanggal);

        $grafikData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date    = Carbon::today()->subDays($i);
            $key     = $date->toDateString();
            $dayRows = $rawGrafik->get($key, collect());
            $grafikData[] = [
                'tanggal'     => $date->format('d/m'),
                'hadir'       => (int) $dayRows->where('status', 'hadir')->sum('total'),
                'telat'       => (int) $dayRows->where('status', 'telat')->sum('total'),
                'tidak_hadir' => (int) $dayRows->where('status', 'tidak_hadir')->sum('total'),
            ];
        }

        $riwayatScan = Presensi::with('guru')
            ->whereDate('tanggal', $today)
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        $guruTelat = Presensi::with('guru')
            ->whereDate('tanggal', $today)
            ->where('status', 'telat')
            ->orderBy('menit_telat', 'desc')
            ->get();

        $jadwal = JadwalMasuk::getAktif();

        return view('dashboard.index', compact(
            'totalGuru','hadirHariIni','telatHariIni','tidakHadir',
            'izinHariIni','sakitHariIni','grafikData','riwayatScan',
            'guruTelat','jadwal','today'
        ));
    }
}
