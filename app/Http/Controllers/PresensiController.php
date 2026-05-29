<?php

namespace App\Http\Controllers;

use App\Mail\IzinSakitApproval;
use App\Rules\SafeBuktiIzinFile;
use App\Models\Guru;
use App\Models\Presensi;
use App\Models\JadwalMasuk;
use App\Models\JadwalGuruHarian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class PresensiController extends Controller
{
    // ================================================================
    // DATA PRESENSI — tampilkan SEMUA guru aktif, termasuk yang belum hadir
    // ================================================================
    public function index(Request $request)
    {
        $tanggal = $request->tanggal ? Carbon::parse($request->tanggal) : Carbon::today();
        $gurus   = Guru::where('status', 'aktif')->orderBy('nama')->get();
        $jadwal  = JadwalMasuk::getAktif();

        // 1. Ambil data presensi yang sudah ada di DB
        $presensisDb = Presensi::with('guru')
            ->whereDate('tanggal', $tanggal)
            ->when($request->guru_id, fn($q) => $q->where('guru_id', $request->guru_id))
            ->orderBy('jam_masuk')
            ->get();

        // 2. Guru yang belum ada presensi hari ini → tampilkan sebagai "Tidak Hadir" virtual
        $existingGuruIds = $presensisDb->pluck('guru_id');

        $guruBelumHadir = Guru::where('status', 'aktif')
            ->whereNotIn('id', $existingGuruIds)
            ->when($request->guru_id, fn($q) => $q->where('id', $request->guru_id))
            ->orderBy('nama')
            ->get();

        $virtualRows = $guruBelumHadir->map(function ($g) use ($tanggal) {
            return (object) [
                'id'              => null,
                'guru_id'         => $g->id,
                'guru'            => $g,
                'tanggal'         => $tanggal,
                'jam_masuk'       => null,
                'jam_pulang'      => null,
                'status'          => 'tidak_hadir',
                'metode'          => '-',
                'menit_telat'     => 0,
                'keterangan'      => null,
                'bukti_file'      => null,
                'approval_status' => null,
                'virtual'         => true,
            ];
        });

        // 3. Gabungkan: presensi nyata dulu, lalu virtual tidak hadir
        $presensis = $presensisDb->concat($virtualRows);

        // 4. Terapkan filter status setelah merge
        if ($request->status) {
            $presensis = $presensis->filter(fn($p) => $p->status === $request->status)->values();
        }

        return view('presensi.index', compact('presensis', 'gurus', 'tanggal', 'jadwal'));
    }

    // ================================================================
    // SCAN QR
    // ================================================================
    public function scan()
    {
        return view('presensi.scan');
    }

    public function prosesBarcode(Request $request)
    {
        $request->validate(['barcode' => 'required|string']);
        $barcode = strtoupper(trim($request->barcode));

        $guru = Guru::where('barcode', $barcode)->where('status', 'aktif')->first();

        if (!$guru) {
            return response()->json(['success' => false, 'message' => 'Barcode tidak ditemukan atau guru tidak aktif.'], 404);
        }

        $result = $this->prosesPresensi($guru, 'barcode');
        return response()->json($result);
    }

    // ================================================================
    // PROSES PRESENSI — inti logika scan QR
    // Prioritas: jadwal per guru (JadwalGuruHarian) → fallback global (JadwalMasuk)
    // ================================================================
    private function prosesPresensi(Guru $guru, string $metode): array
    {
        $now   = Carbon::now();
        $today = $now->toDateString();
        $hari  = $now->dayOfWeek; // 1=Senin, 2=Selasa, ..., 5=Jumat (Carbon)

        // ── 1. Cek jadwal per guru hari ini ──────────────────────────
        $jadwalGuru = JadwalGuruHarian::where('guru_id', $guru->id)
            ->where('hari', $hari)
            ->first();

        if ($jadwalGuru) {
            // Guru punya jadwal hari ini, tapi jam_masuk null → tidak mengajar
            if ($jadwalGuru->jam_masuk === null) {
                return [
                    'success' => false,
                    'message' => $guru->nama . ' tidak memiliki jadwal mengajar hari ini.',
                ];
            }

            $jadwalGlobal   = JadwalMasuk::getAktif();
            $menitToleransi = $jadwalGlobal ? $jadwalGlobal->toleransiMenit() : 10;

            $jamMasuk  = Carbon::parse($jadwalGuru->jam_masuk)->setDateFrom($now);
            $jamPulang = Carbon::parse($jadwalGuru->jam_pulang)->setDateFrom($now);
            $batas10   = $jamMasuk->copy()->addMinutes($menitToleransi);

        } else {
            // ── 2. Fallback ke jadwal global ─────────────────────────
            $jadwalGlobal = JadwalMasuk::getAktif();

            if (!$jadwalGlobal) {
                return [
                    'success' => false,
                    'message' => 'Belum ada jadwal aktif. Hubungi administrator.',
                ];
            }

            $jamMasuk  = Carbon::parse($jadwalGlobal->jam_masuk)->setDateFrom($now);
            $jamPulang = Carbon::parse($jadwalGlobal->jam_pulang)->setDateFrom($now);
            $batas10   = Carbon::parse($jadwalGlobal->batas_toleransi ?? $jadwalGlobal->jam_masuk)->setDateFrom($now);
        }

        $presensi = Presensi::where('guru_id', $guru->id)->whereDate('tanggal', $today)->first();

        // ──────────────────────────────────────────────────────────
        // KASUS: Guru belum scan sama sekali hari ini
        // ──────────────────────────────────────────────────────────
        if (!$presensi) {

            // Sudah melewati jam pulang → otomatis TIDAK HADIR
            if ($now->gt($jamPulang)) {
                Presensi::create([
                    'guru_id'     => $guru->id,
                    'tanggal'     => $today,
                    'jam_masuk'   => null,
                    'jam_pulang'  => null,
                    'status'      => 'tidak_hadir',
                    'metode'      => 'sistem',
                    'keterangan'  => 'Scan setelah jam pulang (' . $now->format('H:i') . ') — otomatis tidak hadir',
                    'menit_telat' => 0,
                ]);

                return [
                    'success' => false,
                    'message' => 'Jam kerja sudah berakhir pukul ' . $jamPulang->format('H:i') . '. '
                               . $guru->nama . ' dicatat TIDAK HADIR hari ini.',
                ];
            }

            // Tentukan status: hadir atau telat
            $status     = $now->gt($batas10) ? 'telat' : 'hadir';
            $menitTelat = $status === 'telat' ? (int) $now->diffInMinutes($jamMasuk) : 0;

            Presensi::create([
                'guru_id'     => $guru->id,
                'tanggal'     => $today,
                'jam_masuk'   => $now->toTimeString(),
                'status'      => $status,
                'metode'      => $metode,
                'menit_telat' => $menitTelat,
            ]);

            return [
                'success'     => true,
                'tipe'        => 'masuk',
                'nama'        => $guru->nama,
                'id'          => $guru->id_pengguna,
                'jam'         => $now->format('H:i:s'),
                'status'      => $status,
                'menit_telat' => $menitTelat,
                'message'     => 'Presensi masuk berhasil — ' . $guru->nama,
            ];
        }

        // ──────────────────────────────────────────────────────────
        // KASUS: Sudah scan masuk, belum scan pulang
        // ──────────────────────────────────────────────────────────
        if (!$presensi->jam_pulang) {

            if ($presensi->status === 'tidak_hadir') {
                return [
                    'success' => false,
                    'message' => $guru->nama . ' telah dicatat TIDAK HADIR hari ini. Hubungi admin untuk koreksi.',
                ];
            }

            $presensi->update(['jam_pulang' => $now->toTimeString()]);

            return [
                'success' => true,
                'tipe'    => 'pulang',
                'nama'    => $guru->nama,
                'id'      => $guru->id_pengguna,
                'jam'     => $now->format('H:i:s'),
                'status'  => $presensi->status,
                'message' => 'Presensi pulang berhasil — ' . $guru->nama,
            ];
        }

        // ──────────────────────────────────────────────────────────
        // KASUS: Sudah lengkap (masuk + pulang)
        // ──────────────────────────────────────────────────────────
        return [
            'success' => false,
            'message' => $guru->nama . ' sudah melakukan presensi lengkap hari ini.',
        ];
    }

    // ================================================================
    // UPDATE STATUS MANUAL
    // ================================================================
    public function updateStatus(Request $request, Presensi $presensi)
    {
        $request->validate([
            'status'     => 'required|in:hadir,telat,tidak_hadir,izin,sakit',
            'keterangan' => 'nullable|string|max:255',
        ]);
        $presensi->update($request->only('status', 'keterangan'));
        return back()->with('success', 'Status presensi diperbarui.');
    }

    public function inputManual(Request $request)
    {
        $request->validate([
            'guru_id'    => 'required|exists:gurus,id',
            'tanggal'    => 'required|date',
            'status'     => 'required|in:hadir,telat,tidak_hadir,izin,sakit',
            'jam_masuk'  => 'nullable|date_format:H:i',
            'jam_pulang' => 'nullable|date_format:H:i',
            'keterangan' => 'nullable|string',
        ]);

        Presensi::updateOrCreate(
            ['guru_id' => $request->guru_id, 'tanggal' => $request->tanggal],
            [
                'jam_masuk'  => $request->jam_masuk,
                'jam_pulang' => $request->jam_pulang,
                'status'     => $request->status,
                'metode'     => 'manual',
                'keterangan' => $request->keterangan,
            ]
        );

        return back()->with('success', 'Presensi manual berhasil disimpan.');
    }

    /**
     * Kepala Sekolah: ubah jam masuk & pulang pada baris presensi nyata.
     */
    public function updateJamManual(Request $request, Presensi $presensi)
    {
        $rules = [
            'jam_masuk'  => ['nullable', 'date_format:H:i'],
            'jam_pulang' => ['nullable', 'date_format:H:i'],
        ];
        if ($request->filled('jam_masuk') && $request->filled('jam_pulang')) {
            $rules['jam_pulang'][] = 'after_or_equal:jam_masuk';
        }

        $request->validate($rules);

        $presensi->update([
            'jam_masuk'  => $request->input('jam_masuk'),
            'jam_pulang' => $request->input('jam_pulang'),
            'metode'     => 'manual',
        ]);

        return back()->with('success', 'Jam masuk & jam pulang berhasil diperbarui.');
    }

    // ================================================================
    // PRESENSI SAYA (untuk role guru)
    // ================================================================
    public function presensiSaya(Request $request)
    {
        $user = auth()->user();

        if (!$user->guru_id) {
            return back()->with('error', 'Akun Anda belum terhubung ke data guru.');
        }

        $bulan = $request->bulan ?? now()->month;
        $tahun = $request->tahun ?? now()->year;

        $presensis = Presensi::where('guru_id', $user->guru_id)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->orderBy('tanggal', 'desc')
            ->get();

        $rekap = [
            'hadir'       => $presensis->where('status', 'hadir')->count(),
            'telat'       => $presensis->where('status', 'telat')->count(),
            'izin'        => $presensis->where('status', 'izin')->count(),
            'sakit'       => $presensis->where('status', 'sakit')->count(),
            'tidak_hadir' => $presensis->where('status', 'tidak_hadir')->count(),
        ];

        $guru = $user->guru;

        return view('presensi.saya', compact('presensis', 'rekap', 'guru', 'bulan', 'tahun'));
    }

    // ================================================================
    // IZIN / SAKIT — form pengajuan oleh guru
    // ================================================================
    public function izinSakit()
    {
        $user = auth()->user();

        if (!$user->guru_id) {
            return back()->with('error', 'Akun Anda belum terhubung ke data guru.');
        }

        $guru = $user->guru;

        $presensiHariIni = Presensi::where('guru_id', $user->guru_id)
            ->whereDate('tanggal', today())
            ->first();

        $riwayat = Presensi::where('guru_id', $user->guru_id)
            ->whereIn('status', ['izin', 'sakit'])
            ->orderBy('tanggal', 'desc')
            ->limit(20)
            ->get();

        return view('izin_sakit.index', compact('guru', 'presensiHariIni', 'riwayat'));
    }

    public function ajukanIzinSakit(Request $request)
    {
        $user = auth()->user();

        if (!$user->guru_id) {
            return back()->with('error', 'Akun Anda belum terhubung ke data guru.');
        }

        $request->validate([
            'status'     => 'required|in:izin,sakit',
            'keterangan' => 'nullable|string|max:500',
            'bukti_file' => ['nullable', 'file', 'max:2048', new SafeBuktiIzinFile],
        ]);

        $today    = today()->toDateString();
        $existing = Presensi::where('guru_id', $user->guru_id)
            ->whereDate('tanggal', $today)
            ->first();

        if ($existing) {
            if (in_array($existing->status, ['hadir', 'telat'])) {
                return back()->with('error', 'Tidak dapat mengajukan izin/sakit karena Anda sudah hadir hari ini.');
            }
            if (in_array($existing->status, ['izin', 'sakit'])) {
                $label = $existing->status === 'izin' ? 'Izin' : 'Sakit';
                return back()->with('error', "Pengajuan {$label} untuk hari ini sudah tercatat.");
            }
        }

        $buktiPath = null;
        if ($request->hasFile('bukti_file')) {
            $buktiPath = $request->file('bukti_file')->store('bukti_izin_sakit', 'public');
        }

        Presensi::updateOrCreate(
            ['guru_id' => $user->guru_id, 'tanggal' => $today],
            [
                'jam_masuk'       => null,
                'jam_pulang'      => null,
                'status'          => $request->status,
                'metode'          => 'manual',
                'keterangan'      => $request->keterangan,
                'bukti_file'      => $buktiPath,
                'approval_status' => 'menunggu',
            ]
        );

        $label = $request->status === 'izin' ? 'Izin' : 'Sakit';
        return back()->with('success', "Pengajuan {$label} berhasil diajukan. Menunggu persetujuan admin.");
    }

    // ================================================================
    // APPROVAL IZIN / SAKIT oleh admin
    // ================================================================
    public function approveIzinSakit(Request $request, Presensi $presensi)
    {
        $request->validate([
            'approval_status' => 'required|in:disetujui,ditolak',
        ]);

        $presensi->update(['approval_status' => $request->approval_status]);

        $this->kirimEmailNotifikasi($presensi, $request->approval_status);

        $statusLabel = $request->approval_status === 'disetujui' ? 'disetujui' : 'ditolak';
        return back()->with('success', "Pengajuan berhasil {$statusLabel}.");
    }

    private function kirimEmailNotifikasi(Presensi $presensi, string $approvalStatus): void
    {
        $emailGuru = $presensi->guru?->email ?? null;

        if (!$emailGuru) {
            Log::info('[IzinSakitApproval] Email tidak dikirim — guru tidak memiliki alamat email.', [
                'presensi_id' => $presensi->id,
                'guru_id'     => $presensi->guru_id,
            ]);
            return;
        }

        try {
            Mail::to($emailGuru)->send(new IzinSakitApproval($presensi, $approvalStatus));
        } catch (\Exception $e) {
            Log::error('[IzinSakitApproval] Gagal mengirim email.', [
                'guru'  => $presensi->guru->nama ?? '-',
                'email' => $emailGuru,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
