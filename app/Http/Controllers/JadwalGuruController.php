<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\JadwalGuruHarian;
use Illuminate\Http\Request;

class JadwalGuruController extends Controller
{
    private const HARI = [
        1 => 'Senin',
        2 => 'Selasa',
        3 => 'Rabu',
        4 => 'Kamis',
        5 => 'Jumat',
    ];

    public function index()
    {
        $gurus = Guru::where('status', 'aktif')
            ->with(['jadwalHarian' => fn($q) => $q->orderBy('hari')])
            ->orderBy('nama')
            ->get();

        $namaHari = self::HARI;

        return view('jadwal_guru.index', compact('gurus', 'namaHari'));
    }

    public function jadwalSaya()
    {
        $guru = auth()->user()->guru;
        if (!$guru) {
            return back()->with('error', 'Data guru tidak ditemukan.');
        }

        $guru->load(['jadwalHarian' => fn($q) => $q->orderBy('hari')]);

        $namaHari = self::HARI;

        return view('jadwal_guru.saya', compact('guru', 'namaHari'));
    }

    public function simpan(Request $request)
    {
        $request->validate([
            'jadwal'              => 'required|array',
            'jadwal.*.*.masuk'   => 'nullable|date_format:H:i',
        ], [
            'jadwal.*.*.masuk.date_format' => 'Format jam harus HH:MM (contoh: 07:30)',
        ]);

        $jadwal = $request->input('jadwal'); // jadwal[guru_id][hari]['masuk']

        foreach ($jadwal as $guruId => $hariData) {
            foreach ($hariData as $hari => $data) {
                $jamMasuk = isset($data['masuk']) && $data['masuk'] !== '' ? $data['masuk'] . ':00' : null;

                JadwalGuruHarian::updateOrCreate(
                    ['guru_id' => $guruId, 'hari' => $hari],
                    ['jam_masuk' => $jamMasuk, 'jam_pulang' => '14:30:00']
                );
            }
        }

        return back()->with('success', 'Jadwal guru berhasil disimpan.');
    }
}
