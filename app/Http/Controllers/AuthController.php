<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'id_pengguna' => 'required|string',
            'password'    => 'required',
        ], [
            'id_pengguna.required' => 'ID wajib diisi.',
            'password.required'    => 'Password wajib diisi.',
        ]);

        $user = User::where('id_pengguna', $request->id_pengguna)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()
                ->withErrors(['id_pengguna' => 'ID atau password salah.'])
                ->onlyInput('id_pengguna');
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return match ($user->role) {
            'guru'  => redirect()->route('guru.barcode_saya'),
            default => redirect()->route('dashboard'),
        };
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
