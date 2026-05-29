<?php

namespace App\Http\Controllers;

use App\Models\JadwalMasuk;
use Carbon\Carbon;
use Illuminate\Http\Request;

class JadwalMasukController extends Controller
{
    public function updateDashboard(Request $request, JadwalMasuk $jadwal)
    {
        $validated = $request->validate([
            'jam_masuk'       => 'required|date_format:H:i',
            'jam_pulang'      => 'required|date_format:H:i',
            'batas_toleransi' => 'required|integer|min:0|max:120',
        ]);

        $jamMasuk = Carbon::createFromFormat('H:i', $validated['jam_masuk']);
        $batasWaktu = $jamMasuk->copy()->addMinutes((int) $validated['batas_toleransi'])->format('H:i:s');

        $jadwal->update([
            'jam_masuk'       => $validated['jam_masuk'] . ':00',
            'jam_pulang'      => $validated['jam_pulang'] . ':00',
            'batas_toleransi' => $batasWaktu,
        ]);

        return back()->with('success', 'Jadwal berhasil diperbarui.');
    }
}
