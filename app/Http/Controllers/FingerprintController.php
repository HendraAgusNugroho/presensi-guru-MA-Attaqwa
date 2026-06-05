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

        if (empty($rows)) {
            return back()->with('import_result', [
                'berhasil' => 0, 'gagal' => 0, 'tidak_ditemukan' => 0,
                'errors'   => ['File Excel kosong atau tidak bisa dibaca.'],
            ]);
        }

        // Auto-detect column positions from header
        $header = $rows[0] ?? [];
        $namaColIndex = null;
        $tanggalColIndex = null;
        $waktuColIndex = null;

        foreach ($header as $colIndex => $value) {
            $value = strtolower(trim($value ?? ''));
            if (preg_match('/(nama|name|karyawan|pegawai)/i', $value)) {
                $namaColIndex = $colIndex;
            }
            if (preg_match('/(tanggal|date|tgl)/i', $value)) {
                $tanggalColIndex = $colIndex;
            }
            if (preg_match('/(waktu|time|jam|scan|timestamp)/i', $value)) {
                $waktuColIndex = $colIndex;
            }
        }

        // If auto-detection still fails, try to find by data pattern
        if ($namaColIndex === null || $tanggalColIndex === null || $waktuColIndex === null) {
            foreach ($rows as $rowIndex => $row) {
                if ($rowIndex === 0) continue;
                if (empty(array_filter($row, fn($v) => $v !== '' && $v !== null))) continue;

                foreach ($row as $colIndex => $cellValue) {
                    $cellValue = trim((string) $cellValue);
                    if ($namaColIndex === null && preg_match('/^[a-zA-Z\s\.]+$/', $cellValue) && strlen($cellValue) > 3) {
                        $namaColIndex = $colIndex;
                    }
                    if ($tanggalColIndex === null && preg_match('/\d{4}[-\/]\d{1,2}[-\/]\d{1,2}/', $cellValue)) {
                        $tanggalColIndex = $colIndex;
                    }
                    if ($waktuColIndex === null && preg_match('/\d{1,2}:\d{2}/', $cellValue)) {
                        $waktuColIndex = $colIndex;
                    }
                }
                if ($namaColIndex !== null && $tanggalColIndex !== null && $waktuColIndex !== null) break;
            }
        }

        // If still not found, default to column 0, 1, 2
        if ($namaColIndex === null) $namaColIndex = 0;
        if ($tanggalColIndex === null) $tanggalColIndex = 1;
        if ($waktuColIndex === null) $waktuColIndex = 2;

        // Debug: Show detected columns and raw data
        $debugErrors = [];
        $debugErrors[] = "DEBUG: Kolom terdeteksi - Nama: kolom $namaColIndex ('" . ($header[$namaColIndex] ?? 'N/A') . "'), Tanggal: kolom $tanggalColIndex ('" . ($header[$tanggalColIndex] ?? 'N/A') . "'), Waktu: kolom $waktuColIndex ('" . ($header[$waktuColIndex] ?? 'N/A') . "')";
        $debugErrors[] = "DEBUG: Header lengkap: " . implode(' | ', array_slice($header, 0, 10));
        $debugErrors[] = "DEBUG: Contoh data baris 2: " . implode(' | ', array_slice($rows[1] ?? [], 0, 10));
        $debugErrors[] = "DEBUG: Total baris: " . count($rows);

        // Add debug errors to the beginning of errors array
        $errors = array_merge($debugErrors, $errors);

        $jadwal = JadwalMasuk::getAktif();

        if (!$jadwal) {
            return back()->withErrors(['file' => 'Belum ada jadwal masuk aktif. Hubungi Super Admin untuk mengaktifkan jadwal.']);
        }

        // Group scans by guru and date
        $scansByGuruDate = [];

        foreach ($rows as $i => $row) {
            if ($i === 0) continue; // Skip header
            if (empty(array_filter($row, fn($v) => $v !== '' && $v !== null))) continue; // Skip empty rows

            $nama     = trim((string) ($row[$namaColIndex] ?? ''));
            $tanggalRaw = trim((string) ($row[$tanggalColIndex] ?? ''));
            $waktuRaw = trim((string) ($row[$waktuColIndex] ?? ''));

            if (!$nama || !$tanggalRaw || !$waktuRaw) {
                $gagal++;
                $errors[] = "Baris " . ($i + 1) . ": Nama, tanggal, atau waktu kosong (Nama: '$nama', Tanggal: '$tanggalRaw', Waktu: '$waktuRaw')";
                continue;
            }

            // Parse tanggal
            $tanggal = null;
            $dateFormats = ['Y-m-d', 'd/m/Y', 'd-m-Y', 'Y/m/d', 'm/d/Y'];
            foreach ($dateFormats as $fmt) {
                try {
                    $parsed = Carbon::createFromFormat($fmt, $tanggalRaw);
                    if ($parsed !== false) { $tanggal = $parsed; break; }
                } catch (\Exception $e) {}
            }
            if (!$tanggal) {
                try { $tanggal = Carbon::parse($tanggalRaw); } catch (\Exception $e) {}
            }

            // Parse waktu
            $waktu = null;
            $timeFormats = ['H:i:s', 'H:i', 'h:i:s A', 'h:i A'];
            foreach ($timeFormats as $fmt) {
                try {
                    $parsed = Carbon::createFromFormat($fmt, $waktuRaw);
                    if ($parsed !== false) { $waktu = $parsed; break; }
                } catch (\Exception $e) {}
            }
            if (!$waktu) {
                try { $waktu = Carbon::parse($waktuRaw); } catch (\Exception $e) {}
            }

            if (!$tanggal) {
                $gagal++;
                $errors[] = "Baris " . ($i + 1) . ": Format tanggal tidak dikenali — '$tanggalRaw'";
                continue;
            }

            if (!$waktu) {
                $gagal++;
                $errors[] = "Baris " . ($i + 1) . ": Format waktu tidak dikenali — '$waktuRaw'";
                continue;
            }

            // Combine tanggal and waktu
            $fullDateTime = Carbon::parse($tanggal->toDateString() . ' ' . $waktu->toTimeString());

            // Group by nama (case-insensitive) and date
            $namaKey = strtolower($nama);
            $dateKey = $tanggal->toDateString();

            if (!isset($scansByGuruDate[$namaKey])) {
                $scansByGuruDate[$namaKey] = [];
            }
            if (!isset($scansByGuruDate[$namaKey][$dateKey])) {
                $scansByGuruDate[$namaKey][$dateKey] = [];
            }

            $scansByGuruDate[$namaKey][$dateKey][] = $fullDateTime;
        }

        // Process grouped scans
        foreach ($scansByGuruDate as $namaKey => $datesData) {
            // Find guru by name (case-insensitive)
            $guru = Guru::where('status', 'aktif')
                ->whereRaw('LOWER(nama) = ?', [$namaKey])
                ->first();

            if (!$guru) {
                $tidakDitemukan++;
                $errors[] = "Nama guru '$namaKey' tidak ditemukan di database";
                continue;
            }

            foreach ($datesData as $dateKey => $scans) {
                // Sort scans by time
                sort($scans);

                $jamMasuk = $scans[0]->toTimeString();
                $jamKeluar = count($scans) > 1 ? end($scans)->toTimeString() : null;

                $today          = $dateKey;
                $jamMasukCarbon = Carbon::parse($jadwal->jam_masuk)->setDateFrom($scans[0]);
                $batasAkhir     = $jadwal->batasAkhirPada($scans[0]);

                $status     = $scans[0]->lte($batasAkhir) ? 'hadir' : 'telat';
                $menitTelat = $status === 'telat'
                    ? (int) abs($scans[0]->diffInMinutes($jamMasukCarbon))
                    : 0;

                // Check if presensi already exists
                $presensi = Presensi::where('guru_id', $guru->id)->whereDate('tanggal', $today)->first();

                if (!$presensi) {
                    Presensi::create([
                        'guru_id'    => $guru->id,
                        'tanggal'    => $today,
                        'jam_masuk'  => $jamMasuk,
                        'jam_pulang' => $jamKeluar,
                        'status'     => $status,
                        'metode'     => 'fingerprint',
                        'menit_telat'=> $menitTelat,
                    ]);
                    $berhasil++;
                } else {
                    // Update if new scan is earlier for masuk or later for keluar
                    if ($scans[0]->lt(Carbon::parse($today . ' ' . $presensi->jam_masuk))) {
                        $presensi->update([
                            'jam_masuk' => $jamMasuk,
                            'status' => $status,
                            'menit_telat' => $menitTelat,
                        ]);
                    }
                    if ($jamKeluar && (!$presensi->jam_pulang || Carbon::parse($today . ' ' . $jamKeluar)->gt(Carbon::parse($today . ' ' . $presensi->jam_pulang)))) {
                        $presensi->update(['jam_pulang' => $jamKeluar]);
                    }
                    $berhasil++;
                }

                // Log fingerprint
                FingerprintLog::create([
                    'id_fingerprint' => $guru->id_pengguna,
                    'guru_id'        => $guru->id,
                    'waktu_scan'     => $scans[0],
                    'tipe'           => 'masuk',
                    'diproses'       => true,
                ]);
            }
        }

        return back()->with('import_result', [
            'berhasil'       => $berhasil,
            'gagal'          => $gagal,
            'tidak_ditemukan'=> $tidakDitemukan,
            'errors'         => array_slice($errors, 0, 50),
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
