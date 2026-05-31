@extends('layouts.app')
@section('title', 'Profil Saya')
@section('meta_description', 'Kelola profil akun dan ubah password Sistem Presensi Guru MA Attaqwa.')

@section('content')
<div class="page-header">
    <h1><i class="fas fa-user-circle" aria-hidden="true"></i> Profil Saya</h1>
</div>

<div class="profil-grid">
    {{-- Info Profil --}}
    <div class="card card--flush">
        <div class="card-header">
            <h2 class="card-title"><i class="fas fa-id-badge" aria-hidden="true"></i> Informasi Akun</h2>
        </div>
        <div class="card-body">
            <div class="profil-avatar-wrap">
                <div class="profil-avatar" style="--role-color: {{ $user->role_color }};">
                    <i class="fas fa-user" aria-hidden="true"></i>
                </div>
                <h3 class="profil-name">{{ $user->name }}</h3>
                <span class="profil-role-badge" style="--role-color: {{ $user->role_color }};">
                    {{ $user->role_label }}
                </span>
            </div>
            <table class="profil-table">
                <tbody>
                    <tr>
                        <th scope="row">ID</th>
                        <td>{{ $user->id_pengguna }}</td>
                    </tr>
                    <tr>
                        <th scope="row">Nama</th>
                        <td>{{ $user->name }}</td>
                    </tr>
                    <tr>
                        <th scope="row">Role</th>
                        <td><span style="color:{{ $user->role_color }};font-weight:700;">{{ $user->role_label }}</span></td>
                    </tr>
                    @if($user->guru)
                    <tr>
                        <th scope="row">Jabatan</th>
                        <td>{{ $user->guru->jabatan }}</td>
                    </tr>
                    <tr>
                        <th scope="row">Mata Pelajaran</th>
                        <td>{{ $user->guru->mata_pelajaran ?? '-' }}</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    {{-- Ubah Password --}}
    <div class="card card--flush">
        <div class="card-header">
            <h2 class="card-title"><i class="fas fa-lock" aria-hidden="true"></i> Ubah Password</h2>
        </div>
        <div class="card-body">
            @if($errors->any())
            <div class="alert alert-danger" role="alert">
                <i class="fas fa-circle-xmark" aria-hidden="true"></i>
                {{ $errors->first() }}
            </div>
            @endif
            @if(session('success'))
            <div class="alert alert-success" role="status">
                <i class="fas fa-check" aria-hidden="true"></i> {{ session('success') }}
            </div>
            @endif
            <form method="POST" action="{{ route('profil.password') }}">
                @csrf
                <x-password-input
                    name="password_lama"
                    id="password_lama"
                    label="Password Lama"
                    :required="true"
                    placeholder="Password saat ini"
                    autocomplete="current-password"
                />
                <x-password-input
                    name="password_baru"
                    id="password_baru"
                    label="Password Baru"
                    :required="true"
                    placeholder="Minimal 6 karakter"
                    autocomplete="new-password"
                />
                <x-password-input
                    name="password_baru_confirmation"
                    id="password_baru_confirmation"
                    label="Konfirmasi Password Baru"
                    :required="true"
                    placeholder="Ulangi password baru"
                    autocomplete="new-password"
                />
                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-save" aria-hidden="true"></i> Simpan Password Baru
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
