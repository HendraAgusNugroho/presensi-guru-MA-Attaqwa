<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfilController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        return view('profil.index', compact('user'));
    }

    public function ubahPassword(Request $request)
    {
        $request->validate([
            'password_lama' => 'required',
            'password_baru' => ['required', 'confirmed', Password::min(6)],
        ]);

        $user = auth()->user();

        if (!Hash::check($request->password_lama, $user->password)) {
            return back()->withErrors(['password_lama' => 'Password lama tidak sesuai.']);
        }

        $user->update(['password' => Hash::make($request->password_baru)]);

        return back()->with('success', 'Password berhasil diubah.');
    }
}
