@extends('layouts.app')
@section('title', 'Profil Saya')
@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fas fa-user-circle"></i> Profil Saya</h1>
</div>

<div class="profil-grid">
    {{-- Info Profil --}}
    <div class="card">
        <div class="card-header"><i class="fas fa-id-badge"></i> Informasi Akun</div>
        <div class="card-body" style="padding:24px;">
            <div style="text-align:center;margin-bottom:24px;">
                <div style="width:72px;height:72px;background:{{ $user->role_color }}22;border-radius:50%;
                    display:flex;align-items:center;justify-content:center;margin:0 auto 12px;font-size:2rem;color:{{ $user->role_color }};">
                    <i class="fas fa-user"></i>
                </div>
                <h3 style="font-size:1rem;font-weight:700;color:#1e293b;">{{ $user->name }}</h3>
                <span style="background:{{ $user->role_color }}22;color:{{ $user->role_color }};
                    padding:3px 10px;border-radius:6px;font-size:.78rem;font-weight:700;">
                    {{ $user->role_label }}
                </span>
            </div>
            <table style="width:100%;font-size:.87rem;border-collapse:collapse;">
                <tr><td style="padding:8px 0;color:#64748b;width:40%">ID</td>
                    <td style="font-weight:600;color:#1e293b;">{{ $user->id_pengguna }}</td></tr>
                <tr><td style="padding:8px 0;color:#64748b;">Nama</td>
                    <td style="font-weight:600;color:#1e293b;">{{ $user->name }}</td></tr>
                <tr><td style="padding:8px 0;color:#64748b;">Role</td>
                    <td><span style="color:{{ $user->role_color }};font-weight:700;">{{ $user->role_label }}</span></td></tr>
                @if($user->guru)
                <tr><td style="padding:8px 0;color:#64748b;">Jabatan</td>
                    <td style="font-weight:600;">{{ $user->guru->jabatan }}</td></tr>
                <tr><td style="padding:8px 0;color:#64748b;">Mata Pelajaran</td>
                    <td style="font-weight:600;">{{ $user->guru->mata_pelajaran ?? '-' }}</td></tr>
                @endif
            </table>
        </div>
    </div>

    {{-- Ubah Password --}}
    <div class="card">
        <div class="card-header"><i class="fas fa-lock"></i> Ubah Password</div>
        <div class="card-body" style="padding:24px;">
            @if($errors->any())
            <div role="alert" style="background:#fee2e2;color:#dc2626;padding:10px 14px;border-radius:8px;margin-bottom:16px;font-size:.85rem;">
                {{ $errors->first() }}
            </div>
            @endif
            @if(session('success'))
            <div role="status" style="background:#dcfce7;color:#15803d;padding:10px 14px;border-radius:8px;margin-bottom:16px;font-size:.85rem;">
                <i class="fas fa-check" aria-hidden="true"></i> {{ session('success') }}
            </div>
            @endif
            <form method="POST" action="{{ route('profil.password') }}">
                @csrf
                <x-password-input name="password_lama" id="password_lama" label="Password Lama" required placeholder="Password saat ini" autocomplete="current-password" />
                <x-password-input name="password_baru" id="password_baru" label="Password Baru" required placeholder="Minimal 6 karakter" autocomplete="new-password" />
                <x-password-input name="password_baru_confirmation" id="password_baru_confirmation" label="Konfirmasi Password Baru" required placeholder="Ulangi password baru" autocomplete="new-password" />
                <button type="submit" class="btn btn-primary" style="width:100%;">
                    <i class="fas fa-save" aria-hidden="true"></i> Simpan Password Baru
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
