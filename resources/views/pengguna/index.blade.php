@extends('layouts.app')
@section('title','Manajemen Pengguna')

@push('styles')
<style>
.role-pill {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: .72rem; font-weight: 700; padding: 3px 10px;
    border-radius: 20px; letter-spacing: .02em;
}
.role-pill.super-admin { background: #fee2e2; color: #dc2626; }
.role-pill.admin       { background: #ede9fe; color: #4f46e5; }
.role-pill.guru        { background: #dcfce7; color: #16a34a; }

.stat-role {
    background: #fff;
    border-radius: 16px;
    padding: 18px 20px;
    box-shadow: 0 2px 12px rgba(0,0,0,.06);
    display: flex; align-items: center; gap: 14px;
    flex: 1;
}
.stat-role .icon {
    width: 48px; height: 48px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.3rem; flex-shrink: 0;
}
.stat-role .count { font-size: 1.6rem; font-weight: 800; line-height: 1; }
.stat-role .label { font-size: .78rem; color: #64748b; margin-top: 2px; }
</style>
@endpush

@section('content')
<div class="page-header" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
    <div>
        <h1 style="display:flex;align-items:center;gap:10px">
            <i class="fas fa-users-gear" style="color:#dc2626"></i>
            Manajemen Pengguna
        </h1>
        <p style="color:#64748b;font-size:.875rem">Kelola akun dan hak akses seluruh pengguna sistem</p>
    </div>
    <a href="{{ route('pengguna.create') }}" class="btn btn-primary">
        <i class="fas fa-user-plus"></i> Tambah Pengguna
    </a>
</div>

{{-- Statistik Role --}}
<div style="display:flex;gap:14px;flex-wrap:wrap;margin-bottom:20px">
    <div class="stat-role">
        <div class="icon" style="background:#fee2e2;color:#dc2626">
            <i class="fas fa-shield-halved"></i>
        </div>
        <div>
            <div class="count" style="color:#dc2626">{{ $stats['super_admin'] }}</div>
            <div class="label">Super Admin</div>
        </div>
    </div>
    <div class="stat-role">
        <div class="icon" style="background:#ede9fe;color:#4f46e5">
            <i class="fas fa-user-gear"></i>
        </div>
        <div>
            <div class="count" style="color:#4f46e5">{{ $stats['admin'] }}</div>
            <div class="label">Admin</div>
        </div>
    </div>
    <div class="stat-role">
        <div class="icon" style="background:#dcfce7;color:#16a34a">
            <i class="fas fa-chalkboard-user"></i>
        </div>
        <div>
            <div class="count" style="color:#16a34a">{{ $stats['guru'] }}</div>
            <div class="label">Guru</div>
        </div>
    </div>
</div>

{{-- Filter --}}
<div class="card" style="margin-bottom:16px">
    <form method="GET" style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap">
        <div style="flex:1;min-width:200px">
            <input type="text" name="search" class="form-control"
                   placeholder="Cari nama atau NIP..."
                   value="{{ request('search') }}">
        </div>
        <select name="role" class="form-control" style="width:auto">
            <option value="">Semua Role</option>
            <option value="super_admin" @selected(request('role')=='super_admin')>Super Admin</option>
            <option value="admin"       @selected(request('role')=='admin')>Admin</option>
            <option value="guru"        @selected(request('role')=='guru')>Guru</option>
        </select>
        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Cari</button>
        <a href="{{ route('pengguna.index') }}" class="btn btn-secondary"><i class="fas fa-rotate-right"></i></a>
    </form>
</div>

{{-- Tabel --}}
<div class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama</th>
                    <th>ID</th>
                    <th>Role</th>
                    <th>Data Guru</th>
                    <th>Dibuat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse($users as $i => $u)
            <tr>
                <td>{{ $users->firstItem() + $i }}</td>
                <td>
                    <div style="display:flex;align-items:center;gap:10px">
                        @php
                            $avatarBg = match($u->role) {
                                'super_admin' => 'background:linear-gradient(135deg,#dc2626,#b91c1c)',
                                'admin'       => 'background:linear-gradient(135deg,#4f46e5,#7c3aed)',
                                default       => 'background:linear-gradient(135deg,#16a34a,#15803d)',
                            };
                        @endphp
                        <div style="width:38px;height:38px;border-radius:10px;{{ $avatarBg }};
                            display:flex;align-items:center;justify-content:center;color:#fff;font-size:.95rem;font-weight:700;flex-shrink:0">
                            {{ strtoupper(substr($u->name, 0, 1)) }}
                        </div>
                        <div>
                            <strong style="font-size:.9rem">{{ $u->name }}</strong>
                            @if($u->id === auth()->id())
                                <span style="font-size:.65rem;background:#fef9c3;color:#a16207;padding:1px 6px;border-radius:4px;margin-left:4px;font-weight:700">Anda</span>
                            @endif
                        </div>
                    </div>
                </td>
                <td>
                    <code style="background:#f1f5f9;padding:2px 8px;border-radius:5px;font-size:.82rem">
                        {{ $u->id_pengguna }}
                    </code>
                </td>
                <td>
                    @php $pillClass = match($u->role) { 'super_admin'=>'super-admin', 'admin'=>'admin', default=>'guru' }; @endphp
                    <span class="role-pill {{ $pillClass }}">
                        <i class="fas {{ match($u->role) { 'super_admin'=>'fa-shield-halved','admin'=>'fa-user-gear',default=>'fa-chalkboard-user' } }}"></i>
                        {{ $u->role_label }}
                    </span>
                </td>
                <td>
                    @if($u->guru)
                        <div style="font-size:.82rem;font-weight:600">{{ $u->guru->nama }}</div>
                        <div style="font-size:.72rem;color:#94a3b8">{{ $u->guru->jabatan ?? '-' }}</div>
                    @else
                        <span style="color:#94a3b8;font-size:.82rem">—</span>
                    @endif
                </td>
                <td style="font-size:.8rem;color:#64748b">
                    {{ $u->created_at?->format('d/m/Y') ?? '-' }}
                </td>
                <td>
                    <div style="display:flex;gap:4px;flex-wrap:wrap">
                        <a href="{{ route('pengguna.edit', $u) }}"
                           class="btn btn-primary btn-sm" title="Edit pengguna">
                            <i class="fas fa-pen"></i>
                        </a>
                        @if($u->id !== auth()->id())
                        <form method="POST" action="{{ route('pengguna.destroy', $u) }}"
                              onsubmit="return confirm('Hapus pengguna {{ $u->name }}?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align:center;color:#94a3b8;padding:40px">
                    <i class="fas fa-users" style="font-size:2rem;margin-bottom:10px;display:block"></i>
                    Belum ada data pengguna
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top:16px">{{ $users->links() }}</div>
</div>

{{-- Info Role --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:14px;margin-top:4px">
    <div style="background:#fff;border-radius:14px;padding:18px;border-left:4px solid #dc2626;box-shadow:0 2px 8px rgba(0,0,0,.05)">
        <div style="font-weight:700;color:#dc2626;margin-bottom:8px;display:flex;align-items:center;gap:6px">
            <i class="fas fa-shield-halved"></i> Super Admin
        </div>
        <ul style="font-size:.8rem;color:#475569;margin:0 0 0 16px;line-height:1.9">
            <li>Akses penuh ke seluruh sistem</li>
            <li>Menambah &amp; menghapus pengguna</li>
            <li>Mengubah role pengguna</li>
            <li>Semua fitur Admin</li>
        </ul>
    </div>
    <div style="background:#fff;border-radius:14px;padding:18px;border-left:4px solid #4f46e5;box-shadow:0 2px 8px rgba(0,0,0,.05)">
        <div style="font-weight:700;color:#4f46e5;margin-bottom:8px;display:flex;align-items:center;gap:6px">
            <i class="fas fa-user-gear"></i> Admin
        </div>
        <ul style="font-size:.8rem;color:#475569;margin:0 0 0 16px;line-height:1.9">
            <li>Mengelola data guru</li>
            <li>Mengelola presensi &amp; laporan</li>
            <li>Import &amp; kelola fingerprint</li>
            <li>Tidak bisa kelola pengguna</li>
        </ul>
    </div>
    <div style="background:#fff;border-radius:14px;padding:18px;border-left:4px solid #16a34a;box-shadow:0 2px 8px rgba(0,0,0,.05)">
        <div style="font-weight:700;color:#16a34a;margin-bottom:8px;display:flex;align-items:center;gap:6px">
            <i class="fas fa-chalkboard-user"></i> Guru
        </div>
        <ul style="font-size:.8rem;color:#475569;margin:0 0 0 16px;line-height:1.9">
            <li>Lihat QR Code presensi sendiri</li>
            <li>Melihat riwayat presensi sendiri</li>
            <li>Tidak dapat akses data global</li>
        </ul>
    </div>
</div>
@endsection
