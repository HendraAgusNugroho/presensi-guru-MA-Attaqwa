<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Presensi;
use App\Models\FingerprintLog;
use App\Models\JadwalMasuk;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class FingerprintController extends Controller
{
    public function index()
    {
        $logs = FingerprintLog::with('guru')->orderBy('waktu_scan', 'desc')->paginate(25);
        return view('fingerprint.index', compact('logs'));
    }

    public function import()
    {
        return view('fingerprint.import');
    }

    public function prosesImport(Request $request)
    {
        $request->validate([
            'file' => [
                'required',
                'file',
                'max:10240',
                function ($attribute, $value, $fail) {
                    $ext = strtolower($value->getClientOriginalExtension());
                    if (!in_array($ext, ['xlsx', 'xls', 'csv'])) {
                        $fail('Format file harus .xlsx, .xls, atau .csv');
                    }
                },
            ],
        ], [
            'file.required' => 'File harus dipilih',
            'file.max'      => 'Ukuran file maksimal 10MB',
        ]);

        $file     = $request->file('file');
        $ext      = strtolower($file->getClientOriginalExtension());
        $berhasil = 0;
        $gagal    = 0;
        $tidakDitemukan = 0;
        $errors   = [];

        try {
            if ($ext === 'csv') {
                $rows = array_map('str_getcsv', file($file->getRealPath()));
            } else {
                $rows = Excel::toArray([], $file)[0];
            }
        } catch (\Exception $e) {
            return back()->with('import_result', [
                'berhasil' => 0, 'gagal' => 0, 'tidak_ditemukan' => 0,
                'errors'   => ['Gagal membaca file: ' . $e->getMessage()],
            ]);
        }

        $jadwal = JadwalMasuk::getAktif();

        if (!$jadwal) {
            return back()->withErrors(['file' => 'Belum ada jadwal masuk aktif. Hubungi Super Admin untuk mengaktifkan jadwal.']);
        }

        foreach ($rows as $i => $row) {
            if ($i === 0) continue;
            if (empty(array_filter($row, fn($v) => $v !== '' && $v !== null))) continue;

            $idFp     = trim($row[0] ?? '');
            $waktuRaw = trim($row[1] ?? '');

            if (!$idFp || !$waktuRaw) {
                $gagal++;
                $errors[] = "Baris " . ($i + 1) . ": ID fingerprint atau waktu kosong";
                continue;
            }

            $waktu = null;
            $formats = [
                'Y-m-d H:i:s', 'Y-m-d H:i', 'd/m/Y H:i:s', 'd/m/Y H:i',
                'd-m-Y H:i:s', 'd-m-Y H:i', 'Y/m/d H:i:s', 'Y/m/d H:i',
                'n/j/Y H:i:s', 'n/j/Y H:i',
            ];
            foreach ($formats as $fmt) {
                try {
                    $parsed = Carbon::createFromFormat($fmt, $waktuRaw);
                    if ($parsed !== false) { $waktu = $parsed; break; }
                } catch (\Exception $e) {}
            }
            if (!$waktu) {
                try { $waktu = Carbon::parse($waktuRaw); } catch (\Exception $e) {}
            }

            if (!$waktu) {
                $gagal++;
                $errors[] = "Baris " . ($i + 1) . ": Format waktu tidak dikenali — '$waktuRaw'";
                continue;
            }

            $guru = Guru::where('id_fingerprint', $idFp)->first();

            $log = FingerprintLog::create([
                'id_fingerprint' => $idFp,
                'guru_id'        => $guru?->id,
                'waktu_scan'     => $waktu,
                'tipe'           => 'masuk',
                'diproses'       => false,
            ]);

            if (!$guru) {
                $tidakDitemukan++;
                $errors[] = "Baris " . ($i + 1) . ": ID fingerprint '$idFp' tidak terdaftar pada data guru";
                continue;
            }

            $today          = $waktu->toDateString();
            $jamMasukCarbon = Carbon::parse($jadwal->jam_masuk)->setDateFrom($waktu);
            $batasAkhir     = $jadwal->batasAkhirPada($waktu);

            $presensi = Presensi::where('guru_id', $guru->id)->whereDate('tanggal', $today)->first();

            if (!$presensi) {
                $status     = $waktu->lte($batasAkhir) ? 'hadir' : 'telat';
                $menitTelat = $status === 'telat'
                    ? (int) abs($waktu->diffInMinutes($jamMasukCarbon))
                    : 0;

                Presensi::create([
                    'guru_id'    => $guru->id,
                    'tanggal'    => $today,
                    'jam_masuk'  => $waktu->toTimeString(),
                    'status'     => $status,
                    'metode'     => 'fingerprint',
                    'menit_telat'=> $menitTelat,
                ]);
                $log->update(['diproses' => true]);
                $berhasil++;
            } else {
                if ($waktu->gt(Carbon::parse($today . ' ' . ($presensi->jam_masuk ?? '12:00')))) {
                    $presensi->update(['jam_pulang' => $waktu->toTimeString()]);
                }
                $log->update(['diproses' => true]);
                $berhasil++;
            }
        }

        return back()->with('import_result', [
            'berhasil'       => $berhasil,
            'gagal'          => $gagal,
            'tidak_ditemukan'=> $tidakDitemukan,
            'errors'         => $errors,
        ]);
    }

    public function sinkronisasiApi(Request $request)
    {
        $configuredKey = config('app.fingerprint_api_key');
        if (! is_string($configuredKey) || strlen($configuredKey) < 32) {
            return response()->json([
                'success' => false,
                'message' => 'API fingerprint belum dikonfigurasi di server.',
            ], 503);
        }

        $apiKey = $request->header('X-Api-Key') ?? $request->input('api_key');
        if (! is_string($apiKey) || ! hash_equals($configuredKey, $apiKey)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. API key tidak valid atau tidak ada.',
            ], 401);
        }

        $request->validate([
            'id_fingerprint' => 'required|string',
            'waktu_scan'     => 'required|date',
        ]);

        $guru   = Guru::where('id_fingerprint', $request->id_fingerprint)->first();
        $jadwal = JadwalMasuk::getAktif();

        FingerprintLog::create([
            'id_fingerprint' => $request->id_fingerprint,
            'guru_id'        => $guru?->id,
            'waktu_scan'     => $request->waktu_scan,
            'tipe'           => 'masuk',
            'diproses'       => (bool) $guru && (bool) $jadwal,
        ]);

        if (!$guru) {
            return response()->json(['success' => false, 'message' => 'ID fingerprint tidak ditemukan.'], 404);
        }
        if (!$jadwal) {
            return response()->json(['success' => false, 'message' => 'Belum ada jadwal masuk aktif.'], 422);
        }

        $waktu          = Carbon::parse($request->waktu_scan);
        $today          = $waktu->toDateString();
        $jamMasukCarbon = Carbon::parse($jadwal->jam_masuk)->setDateFrom($waktu);
        $batasAkhir     = $jadwal->batasAkhirPada($waktu);
        $status         = $waktu->lte($batasAkhir) ? 'hadir' : 'telat';
        $menit         = $status === 'telat'
            ? (int) abs($waktu->diffInMinutes($jamMasukCarbon))
            : 0;

        Presensi::updateOrCreate(
            ['guru_id' => $guru->id, 'tanggal' => $today],
            ['jam_masuk' => $waktu->toTimeString(), 'status' => $status, 'metode' => 'fingerprint', 'menit_telat' => $menit]
        );

        return response()->json([
            'success' => true,
            'message' => "Presensi {$guru->nama} berhasil dicatat sebagai " . strtoupper($status) . ".",
            'data'    => ['guru' => $guru->nama, 'status' => $status, 'jam' => $waktu->toTimeString()],
        ]);
    }
}
