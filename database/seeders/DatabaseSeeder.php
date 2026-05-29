<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Guru;
use App\Models\JadwalMasuk;
use App\Models\JadwalGuruHarian;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Jadwal masuk global default (fallback) ────────────────────
        if (!JadwalMasuk::exists()) {
            JadwalMasuk::create([
                'nama_jadwal'     => 'Jadwal Utama',
                'jam_masuk'       => '07:00:00',
                'batas_toleransi' => '07:10:00',
                'jam_pulang'      => '14:30:00',
                'aktif'           => true,
            ]);
        }

        $ksPassword    = $this->resolveSeedPassword('SEED_SUPER_ADMIN_PASSWORD', 'KS001');
        $adminPassword = $this->resolveSeedPassword('SEED_ADMIN_PASSWORD', 'ADMIN01');

        // ── Akun Kepala Sekolah (Super Admin) ────────────────────────
        User::updateOrCreate(
            ['id_pengguna' => 'KS001'],
            [
                'name'     => 'Kepala Sekolah',
                'password' => $ksPassword,
                'role'     => 'super_admin',
            ]
        );

        // ── Akun TU / Admin ───────────────────────────────────────────
        User::updateOrCreate(
            ['id_pengguna' => 'ADMIN01'],
            [
                'name'     => 'Operator TU',
                'password' => $adminPassword,
                'role'     => 'admin',
            ]
        );

        // ── Data Guru dari Excel ──────────────────────────────────────
        // Format: [ID, Nama, Jabatan, [Senin, Selasa, Rabu, Kamis, Jumat]]
        // null = tidak mengajar hari itu
        $guruData = [
            ['A',  'Fitriani Shofwa, S.Pd.',        'Guru',          ['07:40', null,   '07:00', null,   '07:20']],
            ['B',  'Sopandi, S.T.',                  'Guru',          ['07:40', '07:00', null,  null,   null   ]],
            ['C',  "Qurrata A'ayuni, M.Si.",         'Guru',          [null,   null,   '08:15', '08:15', '08:20']],
            ['D',  'Shofian, S.Kom.',                'Guru',          ['08:50', '08:15', null,  '10:00', null  ]],
            ['E',  'Muhammad Idris, S.Ag.',          'Guru',          ['07:40', null,   null,   '07:00', '07:20']],
            ['F',  'Hasbullah, S.Ag.',               'Guru',          ['08:50', null,   '07:00', '08:50', '07:20']],
            ['G',  'A. Nurul Fahmi, S.Th.I.',        'Guru',          ['07:40', null,   '08:15', '07:00', null  ]],
            ['H',  'Dra. Evi Eritawati Umar',        'Guru',          [null,   '08:15', null,  '10:00', '08:20']],
            ['I',  'Husniati, S.Pd.',                'Guru',          [null,   null,   null,   null,   '07:20']],
            ['J',  'Nurfajriyani, S.Pd.',            'Guru',          ['11:45', '07:00', '07:00', '07:00', '09:50']],
            ['K',  'Dati Hidayati, M.Pd.',           'Guru',          [null,   null,   '11:10', null,  '07:20']],
            ['L',  'Laila Kholida, M.Ag.',           'Guru',          ['07:40', '08:15', '07:00', '07:00', null]],
            ['M',  'Fathiyatul Makiyah, S.Th.I.',    'Guru',          ['10:35', '10:00', '11:10', '07:00', '07:20']],
            ['N',  'Siti Hasanah, S.Pd.',            'Guru',          ['07:40', '07:00', null,  null,   '13:30']],
            ['O',  'Uswatun Aulia, S.Pd.',           'Guru',          ['08:50', '07:00', '08:15', '08:50', null]],
            ['P',  'Muzayyin Lidnillah Mara, S.Pd.', 'Guru',          [null,   '07:00', '07:00', '07:00', null]],
            ['Q',  'M. Nurfuadi, S.Pd.',             'Guru',          ['07:40', null,   null,   '07:00', '08:20']],
            ['R',  'Wahdiatu Syamsiah, M.Pd.',       'Guru',          [null,   '07:00', null,  '07:00', '07:20']],
            ['S',  'Hafiz Liridho, S.E.',            'Guru',          [null,   '08:15', '07:00', null,  '10:50']],
            ['T',  'Farhatul Qolbaini, S.Pd.',       'Guru',          [null,   null,   '07:00', '08:50', null  ]],
            ['U',  'Ali Syaroni',                    'Guru',          ['07:40', '07:00', '08:50', null,  '07:50']],
            ['V',  'Nurul Huda, S.Ag.',              'Guru',          ['13:30', '12:55', '11:10', '10:00', null]],
            ['W',  "Ahmad Mun'im Sidik",             'Guru',          [null,   '12:55', null,  '11:45', '13:30']],
            ['X',  'Wildan',                         'Guru',          ['07:40', '07:00', '07:00', '07:00', '07:20']],
            ['Y',  'Babay Suhaeni',                  'Guru',          ['11:45', null,   '07:40', null,  null   ]],
            // Guru Literasi
            ['L1', 'Munasik Nasal',                  'Guru Literasi', ['07:00', '07:00', '07:00', '07:00', '07:00']],
            ['L2', 'Badru Tamam',                    'Guru Literasi', ['07:00', '07:00', '07:00', '07:00', '07:00']],
            ['L3', 'Fatma Aini',                     'Guru Literasi', ['07:00', '07:00', '07:00', '07:00', '07:00']],
            ['L4', 'Fauzan',                         'Guru Literasi', ['07:00', '07:00', '07:00', '07:00', '07:00']],
            ['L5', 'Ahmad Luthfi',                   'Guru Literasi', ['07:00', '07:00', '07:00', '07:00', '07:00']],
        ];

        foreach ($guruData as $row) {
            [$idPengguna, $nama, $jabatan, $jadwal] = $row;

            $barcode = 'GR-' . strtoupper($idPengguna);

            $guru = Guru::updateOrCreate(
                ['id_pengguna' => $idPengguna],
                [
                    'nama'    => $nama,
                    'jabatan' => $jabatan,
                    'barcode' => $barcode,
                    'status'  => 'aktif',
                ]
            );

            // Password awal guru = ID; wajib diganti setelah login pertama (menu Profil)
            User::updateOrCreate(
                ['id_pengguna' => $idPengguna],
                [
                    'name'     => $nama,
                    'password' => $idPengguna,
                    'role'     => 'guru',
                    'guru_id'  => $guru->id,
                ]
            );

            // Jadwal harian per guru (1=Senin ... 5=Jumat)
            foreach ([1, 2, 3, 4, 5] as $idx => $hari) {
                $jamMasuk = $jadwal[$idx] ?? null;
                if ($jamMasuk !== null) {
                    $jamMasuk .= ':00';
                }

                JadwalGuruHarian::updateOrCreate(
                    ['guru_id' => $guru->id, 'hari' => $hari],
                    [
                        'jam_masuk'  => $jamMasuk,
                        'jam_pulang' => '14:30:00',
                    ]
                );
            }
        }
    }

    /**
     * Password akun staff: wajib kuat di production via .env.
     */
    private function resolveSeedPassword(string $envKey, string $localFallback): string
    {
        $password = env($envKey);

        if (app()->environment('production')) {
            if (! is_string($password) || strlen($password) < 12) {
                throw new \RuntimeException(
                    "Production seed membutuhkan {$envKey} di .env (min. 12 karakter)."
                );
            }

            return $password;
        }

        return $password ?: $localFallback;
    }
}
