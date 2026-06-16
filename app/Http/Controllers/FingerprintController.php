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

        // Parse horizontal format: User ID blocks with date columns (1-31)
        // Structure per employee block:
        // Row 1: User ID info (User ID.： in col 4, ID in col 5, Nama： in col 10, Name in col 11)
        // Row 2: Date headers (1, 2, 3, ... 26)
        // Row 3: Attendance data (format: "07:00\n07:00")

        $processedData = [];
        $year = date('Y');
        $month = date('m');

        // Extract year and month from file info or metadata
        foreach ($rows as $row) {
            foreach ($row as $cellValue) {
                if (preg_match('/(\d{4})/', $cellValue, $matches)) {
                    $year = $matches[1];
                }
                if (preg_match('/(\d{1,2})\/(\d{4})/', $cellValue, $matches)) {
                    $month = $matches[1];
                    $year = $matches[2];
                }
            }
        }

        // Process each row to find User ID blocks
        $i = 0;
        while ($i < count($rows)) {
            $row = $rows[$i];

            // Skip empty rows
            if (empty(array_filter($row, fn($v) => $v !== '' && $v !== null))) {
                $i++;
                continue;
            }

            // Look for "User ID.：" in the row to find employee blocks
            $userIdColIndex = null;
            $userId = null;
            $namaGuru = null;

            foreach ($row as $colIndex => $cellValue) {
                $cellValue = trim((string)$cellValue);
                if (strpos($cellValue, 'User ID.：') !== false || strpos($cellValue, 'User ID.:') !== false) {
                    $userIdColIndex = $colIndex;
                    // User ID is in the next column (we ignore this, but use it to find the block)
                    if (isset($row[$colIndex + 1])) {
                        $userId = trim((string)$row[$colIndex + 1]);
                    }
                    break;
                }
            }

            // If we found User ID, also look for name (this is our key)
            if ($userId) {
                foreach ($row as $colIndex => $cellValue) {
                    $cellValue = trim((string)$cellValue);
                    if (strpos($cellValue, 'Nama：') !== false || strpos($cellValue, 'Nama.:') !== false) {
                        // Name is in the next column - THIS IS OUR KEY
                        if (isset($row[$colIndex + 1])) {
                            $namaGuru = trim((string)$row[$colIndex + 1]);
                        }
                        break;
                    }
                }
            }

            // If we found Guru Name, process the next rows
            if ($namaGuru) {
                // Next row should be date headers
                $i++;
                if ($i >= count($rows)) break;

                $dateHeaderRow = $rows[$i];
                $dateHeaders = [];

                foreach ($dateHeaderRow as $colIndex => $cellValue) {
                    if (is_numeric($cellValue) && $cellValue >= 1 && $cellValue <= 31) {
                        $dateHeaders[$colIndex] = (int)$cellValue;
                    }
                }

                // Next row should be attendance data
                $i++;
                if ($i >= count($rows)) break;

                $dataRow = $rows[$i];

                // Process attendance data
                foreach ($dateHeaders as $colIndex => $day) {
                    $cellValue = trim((string)($dataRow[$colIndex] ?? ''));
                    if ($cellValue && strpos($cellValue, ':') !== false) {
                        // Parse cell format: "07:00\n07:00"
                        $times = explode("\n", str_replace(["\r\n", "\r"], "\n", $cellValue));
                        $times = array_filter($times, fn($t) => !empty(trim($t)));

                        if (count($times) >= 1) {
                            $jamMasuk = trim($times[0]);
                            $jamPulang = count($times) > 1 ? trim($times[1]) : null;

                            // Create date from year, month, day
                            try {
                                $tanggal = Carbon::createFromDate($year, $month, $day);
                                $dateKey = $tanggal->toDateString();

                                // Use nama_guru as key (normalized for flexible matching)
                                $namaGuruKey = $this->normalizeName($namaGuru);

                                if (!isset($processedData[$namaGuruKey])) {
                                    $processedData[$namaGuruKey] = [];
                                }
                                if (!isset($processedData[$namaGuruKey][$dateKey])) {
                                    $processedData[$namaGuruKey][$dateKey] = [];
                                }

                                $processedData[$namaGuruKey][$dateKey] = [
                                    'jam_masuk'  => $jamMasuk,
                                    'jam_pulang' => $jamPulang,
                                    'tanggal'    => $tanggal,
                                    'nama_guru'  => $namaGuru,
                                ];
                            } catch (\Exception $e) {
                                $errors[] = "Baris " . ($i + 1) . ": Tanggal tidak valid - $year-$month-$day";
                            }
                        }
                    }
                }
            }

            $i++;
        }

        // Debug: Show parsing info
        $debugErrors = [];
        $debugErrors[] = "DEBUG: Format blok karyawan - Total Nama Guru: " . count($processedData);
        $debugErrors[] = "DEBUG: Periode: $year-$month";
        $debugErrors[] = "DEBUG: Total baris file: " . count($rows);

        // Show sample guru names found
        $sampleGuruNames = array_slice(array_keys($processedData), 0, 5);
        $debugErrors[] = "DEBUG: Sample Nama Guru dari Excel: " . implode(', ', $sampleGuruNames);

        // Show first 10 guru names from database for debugging
        $gurusFromDB = Guru::select('id', 'nama')->where('status', 'aktif')->limit(10)->get();
        $dbGuruNames = $gurusFromDB->pluck('nama')->toArray();
        $debugErrors[] = "DEBUG: 10 Nama Guru pertama dari database: " . implode(', ', $dbGuruNames);

        // Add debug errors to the beginning of errors array
        $errors = array_merge($debugErrors, $errors);

        $jadwal = JadwalMasuk::getAktif();

        if (!$jadwal) {
            return back()->withErrors(['file' => 'Belum ada jadwal masuk aktif. Hubungi Super Admin untuk mengaktifkan jadwal.']);
        }

        // Process grouped data by Nama Guru
        foreach ($processedData as $namaGuruKey => $datesData) {
            // Get original guru name from first date entry
            $namaGuruDariExcel = $datesData[array_key_first($datesData)]['nama_guru'] ?? $namaGuruKey;

            // Find guru by nama (flexible matching)
            $guru = $this->findGuruByName($namaGuruDariExcel);

            if (!$guru) {
                $tidakDitemukan++;
                $errors[] = "Nama guru '$namaGuruDariExcel' tidak ditemukan di database";
                continue;
            }

            foreach ($datesData as $dateKey => $data) {
                $jamMasuk  = $data['jam_masuk'];
                $jamPulang = $data['jam_pulang'];
                $tanggal   = $data['tanggal'];
                $today     = $tanggal->toDateString();

                // Calculate status based on jam masuk
                $jamMasukCarbon = Carbon::parse($jadwal->jam_masuk)->setDateFrom($tanggal);
                $batasAkhir     = $jadwal->batasAkhirPada($tanggal);
                $waktuMasuk     = Carbon::parse($today . ' ' . $jamMasuk);

                $status     = $waktuMasuk->lte($batasAkhir) ? 'hadir' : 'telat';
                $menitTelat = $status === 'telat'
                    ? (int) abs($waktuMasuk->diffInMinutes($jamMasukCarbon))
                    : 0;

                // Check if presensi already exists
                $presensi = Presensi::where('guru_id', $guru->id)->whereDate('tanggal', $today)->first();

                if (!$presensi) {
                    Presensi::create([
                        'guru_id'    => $guru->id,
                        'tanggal'    => $today,
                        'jam_masuk'  => $jamMasuk,
                        'jam_pulang' => $jamPulang,
                        'status'     => $status,
                        'metode'     => 'fingerprint',
                        'menit_telat'=> $menitTelat,
                    ]);
                    $berhasil++;
                } else {
                    // Update jam masuk if earlier
                    if ($waktuMasuk->lt(Carbon::parse($today . ' ' . $presensi->jam_masuk))) {
                        $presensi->update([
                            'jam_masuk' => $jamMasuk,
                            'status' => $status,
                            'menit_telat' => $menitTelat,
                        ]);
                    }
                    // Update jam pulang if later
                    if ($jamPulang && (!$presensi->jam_pulang || Carbon::parse($today . ' ' . $jamPulang)->gt(Carbon::parse($today . ' ' . $presensi->jam_pulang)))) {
                        $presensi->update(['jam_pulang' => $jamPulang]);
                    }
                    $berhasil++;
                }

                // Log fingerprint
                FingerprintLog::create([
                    'id_fingerprint' => $guru->id_pengguna,
                    'guru_id'        => $guru->id,
                    'waktu_scan'     => $waktuMasuk,
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

    /**
     * Normalize name for flexible matching
     * 
     * BUG FIX: Removed abbreviation expansion that was causing "S.Ag" → "syamsulag"
     * The abbreviation mapping 's\.' => 'syamsul' was interfering with academic titles.
     * 
     * Process:
     * 1. Lowercase and trim
     * 2. Remove academic titles (with dots first)
     * 3. Remove punctuation (dots, commas, apostrophes)
     * 4. Clean up spaces
     */
    private function normalizeName($name)
    {
        // Trim, lowercase
        $normalized = trim(strtolower($name));
        
        // Remove academic titles WITH dots first (before removing dots)
        $titlesWithDots = [
            's\.pd\.', 's\.pd',
            's\.ag\.', 's\.ag',
            's\.t\.', 's\.t',
            'm\.si\.', 'm\.si',
            's\.kom\.', 's\.kom',
            's\.th\.i\.', 's\.th\.i',
            'm\.pd\.', 'm\.pd',
            'm\.p\.', 'm\.p',
            's\.h\.', 's\.h',
            's\.p\.', 's\.p',
            's\.k\.', 's\.k',
            's\.m\.', 's\.m',
            'm\.a\.', 'm\.a',
            'm\.hum\.', 'm\.hum',
            'm\.kes\.', 'm\.kes',
            'm\.farm\.', 'm\.farm',
            'm\.si\.kom\.', 'm\.si\.kom',
            's\.pd\.i\.', 's\.pd\.i',
            'm\.pd\.i\.', 'm\.pd\.i',
            's\.th\.', 's\.th',
            's\.fi\.', 's\.fi',
            's\.farm\.', 's\.farm',
            's\.keperawatan\.', 's\.keperawatan',
            's\.kebidanan\.', 's\.kebidanan',
            's\.psi\.', 's\.psi',
            'm\.ti\.', 'm\.ti',
            'm\.ca\.', 'm\.ca',
            'm\.ak\.', 'm\.ak',
            'm\.kom\.', 'm\.kom',
            'dr\.', 'dra\.', 'drs\.',
        ];
        
        foreach ($titlesWithDots as $title) {
            $normalized = preg_replace('/\b' . $title . '\b/i', '', $normalized);
        }
        
        // Remove academic titles WITHOUT dots
        $titlesWithoutDots = [
            's pd', 's ag', 's t', 'm si', 's kom', 's th i',
            'm pd', 'm p', 's h', 's p', 's k', 's m', 'm a',
            'm hum', 'm kes', 'm farm', 'm si kom',
            's pd i', 'm pd i', 's th', 's fi', 's farm',
            's keperawatan', 's kebidanan', 's psi',
            'm ti', 'm ca', 'm ak', 'm kom',
            'dra', 'drs', 'dr',
        ];
        
        foreach ($titlesWithoutDots as $title) {
            $normalized = preg_replace('/\b' . preg_quote($title, '/') . '\b/i', '', $normalized);
        }
        
        // Remove dots, commas, apostrophes
        $normalized = str_replace(['.', ',', "'"], '', $normalized);
        
        // Convert multiple spaces to single space
        $normalized = preg_replace('/\s+/', ' ', $normalized);
        
        // Trim again
        $normalized = trim($normalized);
        
        return $normalized;
    }

    /**
     * Calculate similarity percentage between two strings using Levenshtein distance
     */
    private function calculateSimilarity($str1, $str2)
    {
        $len1 = strlen($str1);
        $len2 = strlen($str2);
        
        if ($len1 === 0 || $len2 === 0) {
            return 0;
        }
        
        $distance = levenshtein($str1, $str2);
        $maxLen = max($len1, $len2);
        
        // Calculate similarity percentage
        $similarity = (1 - ($distance / $maxLen)) * 100;
        
        return $similarity;
    }

    /**
     * Find best match using fuzzy matching with Levenshtein distance
     */
    private function findBestFuzzyMatch($input, $candidates, $threshold = 80)
    {
        $bestMatch = null;
        $bestSimilarity = 0;
        $bestGuruId = null;
        
        foreach ($candidates as $guruId => $candidate) {
            $similarity = $this->calculateSimilarity($input, $candidate);
            
            if ($similarity > $bestSimilarity && $similarity >= $threshold) {
                $bestSimilarity = $similarity;
                $bestMatch = $candidate;
                $bestGuruId = $guruId;
            }
        }
        
        return [
            'match' => $bestMatch,
            'guru_id' => $bestGuruId,
            'similarity' => $bestSimilarity,
        ];
    }

    /**
     * Find guru by name with flexible matching
     * 
     * Matching strategy (in order):
     * 1. Exact match (case-insensitive, trimmed)
     * 2. Normalized match (after removing academic titles and punctuation)
     * 3. LIKE query (partial match)
     * 4. Fuzzy matching (Levenshtein distance, 80% threshold)
     */
    private function findGuruByName($name)
    {
        $normalizedInput = $this->normalizeName($name);
        $matchingMethod = null;
        $matchedGuru = null;
        $similarityPercent = 0;
        $normalizedDBName = '';

        // Try exact match first
        $guru = Guru::where('status', 'aktif')
            ->whereRaw('LOWER(TRIM(nama)) = ?', [$normalizedInput])
            ->first();

        if ($guru) {
            $matchingMethod = 'exact';
            $matchedGuru = $guru;
            $normalizedDBName = $this->normalizeName($guru->nama);
            $similarityPercent = 100;
        }

        // Try normalized match
        if (!$matchedGuru) {
            $allGurus = Guru::where('status', 'aktif')->get();

            foreach ($allGurus as $guru) {
                $normalizedDBName = $this->normalizeName($guru->nama);

                // Check if normalized names match exactly
                if ($normalizedInput === $normalizedDBName) {
                    $matchingMethod = 'normalized';
                    $matchedGuru = $guru;
                    $similarityPercent = 100;
                    break;
                }

                // Check if input is contained in DB name or vice versa
                if (strpos($normalizedInput, $normalizedDBName) !== false ||
                    strpos($normalizedDBName, $normalizedInput) !== false) {
                    $matchingMethod = 'normalized';
                    $matchedGuru = $guru;
                    $similarityPercent = $this->calculateSimilarity($normalizedInput, $normalizedDBName);
                    break;
                }
            }
        }

        // Try LIKE query as fallback
        if (!$matchedGuru) {
            $guru = Guru::where('status', 'aktif')
                ->whereRaw('LOWER(nama) LIKE ?', ['%' . $normalizedInput . '%'])
                ->first();

            if ($guru) {
                $matchingMethod = 'like';
                $matchedGuru = $guru;
                $normalizedDBName = $this->normalizeName($guru->nama);
                $similarityPercent = $this->calculateSimilarity($normalizedInput, $normalizedDBName);
            }
        }

        // Try fuzzy matching as final fallback (80% threshold)
        if (!$matchedGuru) {
            $allGurus = Guru::where('status', 'aktif')->get();
            $candidates = [];
            
            foreach ($allGurus as $guru) {
                $candidates[$guru->id] = $this->normalizeName($guru->nama);
            }
            
            $fuzzyResult = $this->findBestFuzzyMatch($normalizedInput, $candidates, 80);
            
            if ($fuzzyResult['match'] !== null) {
                $matchedGuru = $allGurus->find($fuzzyResult['guru_id']);
                $matchingMethod = 'fuzzy';
                $normalizedDBName = $fuzzyResult['match'];
                $similarityPercent = $fuzzyResult['similarity'];
            }
        }

        // Log matching result with comprehensive debug info
        if ($matchedGuru) {
            \Log::info('Fingerprint Import - Guru Matched', [
                'excel_name_original' => $name,
                'excel_name_normalized' => $normalizedInput,
                'db_name_original' => $matchedGuru->nama,
                'db_name_normalized' => $normalizedDBName,
                'matching_method' => $matchingMethod,
                'similarity_percent' => round($similarityPercent, 2),
            ]);
        } else {
            \Log::warning('Fingerprint Import - Guru Not Found', [
                'excel_name_original' => $name,
                'excel_name_normalized' => $normalizedInput,
                'matching_method' => 'none',
                'similarity_percent' => 0,
            ]);
        }

        return $matchedGuru;
    }
}
