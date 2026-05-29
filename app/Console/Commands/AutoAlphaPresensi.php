<?php

namespace App\Console\Commands;

use App\Models\Guru;
use App\Models\JadwalGuruHarian;
use App\Models\JadwalMasuk;
use App\Models\Presensi;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AutoAlphaPresensi extends Command
{
    protected $signature   = 'presensi:auto-alpha';
    protected $description = 'Otomatis tandai guru yang tidak hadir (alpha) setelah jam pulang jadwal aktif';

    public function handle(): int
    {
        $now   = Carbon::now();
        $today = $now->toDateString();

        // Skip Sabtu (6) dan Minggu (0 di Carbon, tapi kita pakai isoWeekday: 7=Minggu, 6=Sabtu)
        $hariIso = $now->isoWeekday(); // 1=Senin, 2=Selasa, ..., 6=Sabtu, 7=Minggu
        if ($hariIso >= 6) {
            $namaHari = $hariIso === 6 ? 'Sabtu' : 'Minggu';
            $this->info("[AutoAlpha] Hari {$namaHari}, proses dilewati.");
            Log::info("[AutoAlpha] Hari {$namaHari}, proses dilewati.", ['waktu' => $now->toDateTimeString()]);
            return self::SUCCESS;
        }

        $jadwal = JadwalMasuk::getAktif();

        if (!$jadwal) {
            $this->warn('[AutoAlpha] Tidak ada jadwal aktif, proses dilewati.');
            Log::info('[AutoAlpha] Tidak ada jadwal aktif, proses dilewati.', ['waktu' => $now->toDateTimeString()]);
            return self::SUCCESS;
        }

        $jamPulang = Carbon::parse($jadwal->jam_pulang);

        if ($now->lt($jamPulang)) {
            $this->info('[AutoAlpha] Jam pulang belum terlewati, proses dilewati.');
            return self::SUCCESS;
        }

        $gurusAktif   = Guru::where('status', 'aktif')->get();
        $jumlahDibuat = 0;

        foreach ($gurusAktif as $guru) {
            // Cek apakah guru punya jadwal hari ini (jam_masuk NOT NULL)
            $jadwalHarian = JadwalGuruHarian::where('guru_id', $guru->id)
                ->where('hari', $hariIso)
                ->whereNotNull('jam_masuk')
                ->first();

            if (!$jadwalHarian) {
                // Guru tidak mengajar hari ini, skip
                continue;
            }

            $sudahAbsen = Presensi::where('guru_id', $guru->id)
                ->whereDate('tanggal', $today)
                ->exists();

            if (!$sudahAbsen) {
                Presensi::create([
                    'guru_id'    => $guru->id,
                    'tanggal'    => $today,
                    'jam_masuk'  => null,
                    'jam_pulang' => null,
                    'status'     => 'tidak_hadir',
                    'metode'     => 'sistem',
                    'keterangan' => 'Auto-alpha oleh sistem',
                ]);
                $jumlahDibuat++;
            }
        }

        $message = "[AutoAlpha] Selesai — {$jumlahDibuat} guru ditandai tidak hadir (alpha) pada {$today}.";
        $this->info($message);
        Log::info($message, [
            'jadwal'       => $jadwal->nama_jadwal,
            'jam_pulang'   => $jadwal->jam_pulang,
            'total_dibuat' => $jumlahDibuat,
            'waktu_proses' => $now->toDateTimeString(),
        ]);

        return self::SUCCESS;
    }
}
