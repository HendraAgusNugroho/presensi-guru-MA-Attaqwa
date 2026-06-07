@extends('layouts.app')
@section('title','Edit Pengguna')

@push('styles')
<style>
.role-option {
    display: flex; align-items: flex-start; gap: 14px;
    padding: 16px; border: 2px solid #e2e8f0;
    border-radius: 12px; cursor: pointer; transition: all .2s; margin-bottom: 10px;
}
.role-option:hover { border-color: #c4b5fd; background: #faf8ff; }
.role-option.selected-super { border-color: #dc2626; background: #fff5f5; }
.role-option.selected-admin { border-color: #4f46e5; background: #faf8ff; }
.role-option.selected-guru  { border-color: #16a34a; background: #f0fdf4; }
.role-option input[type=radio] { display: none; }
.role-icon {
    width: 44px; height: 44px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.2rem; flex-shrink: 0;
}
#guruSection { display: none; margin-top: 8px; }

.danger-zone {
    border: 1.5px solid #fecaca;
    border-radius: 14px;
    padding: 20px;
    background: #fff;
    margin-top: 16px;
}
.danger-zone h4 {
    color: #dc2626; font-size: .9rem; font-weight: 700;
    margin-bottom: 14px; display: flex; align-items: center; gap: 8px;
}
</style>
@endpush

@section('content')
<div class="page-header" style="display:flex;align-items:center;justify-content:space-between">
    <div>
        <h1 style="display:flex;align-items:center;gap:10px">
            <i class="fas fa-user-pen" style="color:#4f46e5"></i> Edit Pengguna
        </h1>
        <p style="color:#64748b;font-size:.875rem">{{ $pengguna->name }}</p>
    </div>
    <a href="{{ route('pengguna.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

<div style="max-width:680px">

{{-- Form Edit --}}
<div class="card">
    <form method="POST" action="{{ route('pengguna.update', $pengguna) }}" id="formEdit">
        @csrf @method('PUT')

        {{-- Info Akun --}}
        <div style="margin-bottom:20px">
            <div style="font-weight:700;color:#1e293b;margin-bottom:14px;font-size:.9rem;
                display:flex;align-items:center;gap:8px;padding-bottom:10px;border-bottom:1px solid #f1f5f9">
                <i class="fas fa-id-card" style="color:#4f46e5"></i> Informasi Akun
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Nama Lengkap <span style="color:red">*</span></label>
                    <input type="text" name="name" class="form-control"
                           value="{{ old('name', $pengguna->name) }}" required>
                    @error('name')<div style="color:#dc2626;font-size:.8rem;margin-top:4px">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label>ID <span style="color:red">*</span></label>
                    <input type="text" name="id_pengguna" class="form-control"
                           value="{{ old('id_pengguna', $pengguna->id_pengguna) }}" required>
                    @error('id_pengguna')<div style="color:#dc2626;font-size:.8rem;margin-top:4px">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        {{-- Pilih Role --}}
        <div style="margin-bottom:20px">
            <div style="font-weight:700;color:#1e293b;margin-bottom:14px;font-size:.9rem;
                display:flex;align-items:center;gap:8px;padding-bottom:10px;border-bottom:1px solid #f1f5f9">
                <i class="fas fa-shield-halved" style="color:#dc2626"></i> Hak Akses (Role)
            </div>
            @error('role')<div style="color:#dc2626;font-size:.82rem;margin-bottom:10px;padding:8px 12px;background:#fee2e2;border-radius:8px"><i class="fas fa-times-circle"></i> {{ $message }}</div>@enderror

            @php $currentRole = old('role', $pengguna->role); @endphp

            <label class="role-option {{ $currentRole === 'super_admin' ? 'selected-super' : '' }}" onclick="pilihRole('super_admin', this)">
                <input type="radio" name="role" value="super_admin" {{ $currentRole === 'super_admin' ? 'checked' : '' }}>
                <div class="role-icon" style="background:#fee2e2;color:#dc2626">
                    <i class="fas fa-shield-halved"></i>
                </div>
                <div>
                    <div style="font-weight:700;color:#1e293b">Super Admin</div>
                    <div style="font-size:.78rem;color:#64748b;margin-top:2px">
                        Akses penuh — kelola pengguna, data guru, presensi, laporan, dan seluruh sistem
                    </div>
                </div>
            </label>

            <label class="role-option {{ $currentRole === 'admin' ? 'selected-admin' : '' }}" onclick="pilihRole('admin', this)">
                <input type="radio" name="role" value="admin" {{ $currentRole === 'admin' ? 'checked' : '' }}>
                <div class="role-icon" style="background:#ede9fe;color:#4f46e5">
                    <i class="fas fa-user-gear"></i>
                </div>
                <div>
                    <div style="font-weight:700;color:#1e293b">Admin</div>
                    <div style="font-size:.78rem;color:#64748b;margin-top:2px">
                        Kelola data guru, presensi, laporan, dan fingerprint — tanpa akses manajemen pengguna
                    </div>
                </div>
            </label>

            <label class="role-option {{ $currentRole === 'guru' ? 'selected-guru' : '' }}" onclick="pilihRole('guru', this)">
                <input type="radio" name="role" value="guru" {{ $currentRole === 'guru' ? 'checked' : '' }}>
                <div class="role-icon" style="background:#dcfce7;color:#16a34a">
                    <i class="fas fa-chalkboard-user"></i>
                </div>
                <div>
                    <div style="font-weight:700;color:#1e293b">Guru</div>
                    <div style="font-size:.78rem;color:#64748b;margin-top:2px">
                        Akses QR Code presensi sendiri dan riwayat kehadiran pribadi
                    </div>
                </div>
            </label>
        </div>

        {{-- Hubungkan Data Guru --}}
        <div id="guruSection">
            <div style="font-weight:700;color:#1e293b;margin-bottom:14px;font-size:.9rem;
                display:flex;align-items:center;gap:8px;padding-bottom:10px;border-bottom:1px solid #f1f5f9">
                <i class="fas fa-link" style="color:#16a34a"></i> Hubungkan ke Data Guru
            </div>
            <div class="form-group">
                <label>Pilih Data Guru</label>
                <select name="guru_id" class="form-control">
                    <option value="">— Tidak dihubungkan —</option>
                    @foreach($gurus as $g)
                    <option value="{{ $g->id }}" {{ old('guru_id', $pengguna->guru_id) == $g->id ? 'selected' : '' }}>
                        {{ $g->nama }} (ID: {{ $g->id_pengguna }}) — {{ $g->jabatan ?? 'Guru' }}
                    </option>
                    @endforeach
                </select>
                @error('guru_id')<div style="color:#dc2626;font-size:.8rem;margin-top:4px">{{ $message }}</div>@enderror
            </div>
        </div>

        <div style="display:flex;gap:12px;margin-top:8px;padding-top:16px;border-top:1px solid #f1f5f9">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Simpan Perubahan
            </button>
            <a href="{{ route('pengguna.index') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Batal
            </a>
        </div>
    </form>
</div>

{{-- Reset Password --}}
<div class="danger-zone">
    <h4><i class="fas fa-key"></i> Reset Password</h4>
    <form method="POST" action="{{ route('pengguna.reset_password', $pengguna) }}">
        @csrf
        <div class="form-row" style="margin-bottom:12px">
            <x-password-input name="password" id="reset_password" label="Password Baru" :required="true" placeholder="Min. 6 karakter" autocomplete="new-password" />
            @error('password')<div role="alert" style="color:#dc2626;font-size:.8rem;margin-top:4px">{{ $message }}</div>@enderror
            <x-password-input name="password_confirmation" id="reset_password_confirm" label="Konfirmasi Password" :required="true" placeholder="Ulangi password" autocomplete="new-password" />
        </div>
        <button type="submit" class="btn btn-danger btn-sm"
                data-confirm="Reset password pengguna {{ $pengguna->name }}?">
            <i class="fas fa-key"></i> Reset Password
        </button>
    </form>
</div>

{{-- Hapus Pengguna --}}
@if($pengguna->id !== auth()->id())
<div class="danger-zone" style="margin-top:12px">
    <h4><i class="fas fa-trash"></i> Hapus Pengguna</h4>
    <p style="font-size:.82rem;color:#64748b;margin-bottom:14px">
        Tindakan ini tidak dapat dibatalkan. Akun pengguna akan dihapus permanen dari sistem.
    </p>
    <form method="POST" action="{{ route('pengguna.destroy', $pengguna) }}">
        @csrf @method('DELETE')
        <button type="submit" class="btn btn-danger btn-sm"
                onclick="return confirm('HAPUS PERMANEN pengguna {{ $pengguna->name }}? Tindakan ini tidak dapat dibatalkan!')">
            <i class="fas fa-trash"></i> Hapus Pengguna Ini
        </button>
    </form>
</div>
@endif

</div>
@endsection

@push('scripts')
<script>
function pilihRole(role, el) {
    document.querySelectorAll('.role-option').forEach(o => {
        o.classList.remove('selected-super','selected-admin','selected-guru');
    });
    const cls = role === 'super_admin' ? 'selected-super' : role === 'admin' ? 'selected-admin' : 'selected-guru';
    el.classList.add(cls);
    el.querySelector('input[type=radio]').checked = true;
    document.getElementById('guruSection').style.display = role === 'guru' ? 'block' : 'none';
}

document.addEventListener('DOMContentLoaded', function() {
    const checked = document.querySelector('input[name=role]:checked');
    if (checked && checked.value === 'guru') {
        document.getElementById('guruSection').style.display = 'block';
    }
});
</script>
@endpush
