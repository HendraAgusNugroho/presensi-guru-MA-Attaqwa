<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Guru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class PenggunaController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('guru');

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('id_pengguna', 'like', "%{$request->search}%");
            });
        }

        if ($request->role) {
            $query->where('role', $request->role);
        }

        $users = $query->orderByRaw("FIELD(role, 'super_admin', 'admin', 'guru')")
                       ->orderBy('name')
                       ->paginate(15)
                       ->withQueryString();

        $stats = [
            'super_admin' => User::where('role', 'super_admin')->count(),
            'admin'       => User::where('role', 'admin')->count(),
            'guru'        => User::where('role', 'guru')->count(),
        ];

        return view('pengguna.index', compact('users', 'stats'));
    }

    public function create()
    {
        $gurus = Guru::whereDoesntHave('user')->where('status', 'aktif')->orderBy('nama')->get();
        return view('pengguna.create', compact('gurus'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'id_pengguna' => 'required|string|max:30|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role'     => 'required|in:super_admin,admin,guru',
            'guru_id'  => 'nullable|exists:gurus,id',
        ]);

        if ($validated['role'] === 'guru' && empty($validated['guru_id'])) {
            return back()->withErrors(['guru_id' => 'Pilih data guru yang akan dihubungkan.'])->withInput();
        }

        User::create([
            'name'     => $validated['name'],
            'id_pengguna' => $validated['id_pengguna'],
            'password' => Hash::make($validated['password']),
            'role'     => $validated['role'],
            'guru_id'  => $validated['guru_id'] ?? null,
        ]);

        return redirect()->route('pengguna.index')
            ->with('success', "Pengguna {$validated['name']} berhasil ditambahkan.");
    }

    public function edit(User $pengguna)
    {
        $gurus = Guru::where('status', 'aktif')
            ->where(function ($q) use ($pengguna) {
                $q->whereDoesntHave('user')
                  ->orWhere('id', $pengguna->guru_id);
            })
            ->orderBy('nama')
            ->get();

        return view('pengguna.edit', compact('pengguna', 'gurus'));
    }

    public function update(Request $request, User $pengguna)
    {
        // Tidak boleh ubah role super_admin terakhir
        if ($pengguna->role === 'super_admin' && $request->role !== 'super_admin') {
            $superAdminCount = User::where('role', 'super_admin')->count();
            if ($superAdminCount <= 1) {
                return back()->withErrors(['role' => 'Minimal harus ada satu Super Admin aktif.'])->withInput();
            }
        }

        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'id_pengguna' => ['required', 'string', 'max:30', Rule::unique('users')->ignore($pengguna->id)],
            'role'    => 'required|in:super_admin,admin,guru',
            'guru_id' => 'nullable|exists:gurus,id',
        ]);

        if ($validated['role'] === 'guru' && empty($validated['guru_id'])) {
            return back()->withErrors(['guru_id' => 'Pilih data guru yang akan dihubungkan.'])->withInput();
        }

        $pengguna->update([
            'name'    => $validated['name'],
            'id_pengguna' => $validated['id_pengguna'],
            'role'    => $validated['role'],
            'guru_id' => $validated['guru_id'] ?? null,
        ]);

        return redirect()->route('pengguna.index')
            ->with('success', "Data pengguna {$pengguna->name} berhasil diperbarui.");
    }

    public function resetPassword(Request $request, User $pengguna)
    {
        $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        $pengguna->update(['password' => Hash::make($request->password)]);

        return back()->with('success', "Password pengguna {$pengguna->name} berhasil direset.");
    }

    public function destroy(User $pengguna)
    {
        // Tidak boleh hapus diri sendiri
        if ($pengguna->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        // Tidak boleh hapus super_admin terakhir
        if ($pengguna->role === 'super_admin') {
            $superAdminCount = User::where('role', 'super_admin')->count();
            if ($superAdminCount <= 1) {
                return back()->with('error', 'Tidak dapat menghapus Super Admin terakhir.');
            }
        }

        $nama = $pengguna->name;
        $pengguna->delete();

        return redirect()->route('pengguna.index')
            ->with('success', "Pengguna {$nama} berhasil dihapus.");
    }
}
