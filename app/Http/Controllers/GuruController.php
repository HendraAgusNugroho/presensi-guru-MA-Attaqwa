<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GuruController extends Controller
{
    public function index(Request $request)
    {
        $query = Guru::query();
        if ($request->search) {
            $query->where('nama', 'like', "%{$request->search}%")
                  ->orWhere('id_pengguna', 'like', "%{$request->search}%");
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }
        $gurus = $query->orderBy('nama')->paginate(15)->withQueryString();
        return view('guru.index', compact('gurus'));
    }

    public function create()
    {
        return view('guru.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_pengguna' => 'required|string|max:30|unique:gurus',
            'nama'           => 'required|string|max:255',
            'email'          => 'nullable|email|unique:gurus',
            'no_hp'          => 'nullable|string|max:20',
            'jabatan'        => 'nullable|string|max:100',
            'mata_pelajaran' => 'nullable|string|max:100',
            'id_fingerprint' => 'nullable|string|max:20|unique:gurus',
            'jenis_kelamin'  => 'required|in:L,P',
            'status'         => 'required|in:aktif,nonaktif',
            'foto'           => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        $validated['barcode'] = strtoupper('GR-' . $validated['id_pengguna']);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('guru/foto', 'public');
        }

        Guru::create($validated);
        return redirect()->route('guru.index')->with('success', 'Data guru berhasil ditambahkan.');
    }

    public function edit(Guru $guru)
    {
        return view('guru.edit', compact('guru'));
    }

    public function update(Request $request, Guru $guru)
    {
        $validated = $request->validate([
            'id_pengguna' => 'required|string|max:30|unique:gurus,id_pengguna,' . $guru->id,
            'nama'           => 'required|string|max:255',
            'email'          => 'nullable|email|unique:gurus,email,' . $guru->id,
            'no_hp'          => 'nullable|string|max:20',
            'jabatan'        => 'nullable|string|max:100',
            'mata_pelajaran' => 'nullable|string|max:100',
            'id_fingerprint' => 'nullable|string|max:20|unique:gurus,id_fingerprint,' . $guru->id,
            'jenis_kelamin'  => 'required|in:L,P',
            'status'         => 'required|in:aktif,nonaktif',
            'foto'           => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('guru/foto', 'public');
        }

        $guru->update($validated);
        return redirect()->route('guru.index')->with('success', 'Data guru berhasil diperbarui.');
    }

    public function destroy(Guru $guru)
    {
        $guru->delete();
        return redirect()->route('guru.index')->with('success', 'Data guru berhasil dihapus.');
    }

    public function show(Guru $guru)
    {
        $presensi = $guru->presensis()->orderBy('tanggal', 'desc')->paginate(20);
        return view('guru.show', compact('guru', 'presensi'));
    }

    public function generateBarcode(Guru $guru)
    {
        $guru->update(['barcode' => strtoupper('GR-' . $guru->id_pengguna)]);
        return back()->with('success', 'Barcode berhasil diregenerasi.');
    }

    public function barcodeSaya()
    {
        $user = auth()->user();

        if (!$user->guru_id) {
            return redirect()->route('dashboard')
                ->with('error', 'Akun Anda belum terhubung ke data guru. Hubungi administrator.');
        }

        $guru = $user->guru;

        if (!$guru->barcode) {
            $guru->update(['barcode' => strtoupper('GR-' . $guru->id_pengguna)]);
            $guru->refresh();
        }

        return view('guru.barcode_saya', compact('guru'));
    }
}
