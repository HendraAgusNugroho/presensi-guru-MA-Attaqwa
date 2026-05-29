@extends('layouts.app')
@section('title','Data Guru')

@section('content')
<div class="page-header" style="display:flex;align-items:center;justify-content:space-between">
    <div>
        <h1>Data Guru</h1>
        <p>Kelola data guru dan QR Code presensi</p>
    </div>
    <a href="{{ route('guru.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Tambah Guru
    </a>
</div>

<div class="card">
    <form method="GET" style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap">
        <div style="flex:1;min-width:200px">
            <input type="text" name="search" class="form-control" placeholder="Cari nama atau NIP..." value="{{ request('search') }}">
        </div>
        <select name="status" class="form-control" style="width:auto">
            <option value="">Semua Status</option>
            <option value="aktif" @selected(request('status')=='aktif')>Aktif</option>
            <option value="nonaktif" @selected(request('status')=='nonaktif')>Non-Aktif</option>
        </select>
        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Cari</button>
        <a href="{{ route('guru.index') }}" class="btn btn-secondary"><i class="fas fa-refresh"></i></a>
    </form>
</div>

<div class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr><th>#</th><th>Nama</th><th>ID</th><th>Jabatan</th><th>Mapel</th>
                <th>ID Fingerprint</th><th>Status</th><th>Aksi</th></tr>
            </thead>
            <tbody>
            @forelse($gurus as $i => $g)
            <tr>
                <td>{{ $gurus->firstItem() + $i }}</td>
                <td>
                    <div style="display:flex;align-items:center;gap:10px">
                        <div style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,#ede9fe,#dbeafe);
                            display:flex;align-items:center;justify-content:center;font-weight:700;color:#4f46e5;font-size:.9rem">
                            {{ strtoupper(substr($g->nama,0,1)) }}
                        </div>
                        <div>
                            <strong>{{ $g->nama }}</strong>
                            <div style="font-size:.75rem;color:#94a3b8">{{ $g->email ?? '-' }}</div>
                        </div>
                    </div>
                </td>
                <td><code style="background:#f1f5f9;padding:2px 6px;border-radius:4px;font-size:.8rem">{{ $g->id_pengguna }}</code></td>
                <td>{{ $g->jabatan ?? '-' }}</td>
                <td>{{ $g->mata_pelajaran ?? '-' }}</td>
                <td>
                    @if($g->id_fingerprint)
                        <span style="background:#dbeafe;color:#1d4ed8;padding:2px 8px;border-radius:99px;font-size:.75rem;font-weight:700">
                            {{ $g->id_fingerprint }}
                        </span>
                    @else
                        <span style="color:#94a3b8;font-size:.8rem">Belum set</span>
                    @endif
                </td>
                <td>
                    <span class="badge {{ $g->status == 'aktif' ? 'badge-hadir' : 'badge-tidak-hadir' }}">
                        {{ strtoupper($g->status) }}
                    </span>
                </td>
                <td>
                    <div style="display:flex;gap:4px">
                        <a href="{{ route('guru.show', $g) }}" class="btn btn-secondary btn-sm" title="Detail">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('guru.edit', $g) }}" class="btn btn-primary btn-sm" title="Edit">
                            <i class="fas fa-pen"></i>
                        </a>
                        <form method="POST" action="{{ route('guru.destroy', $g) }}"
                            onsubmit="return confirm('Hapus guru {{ $g->nama }}?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" style="text-align:center;color:#94a3b8;padding:32px">Belum ada data guru</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top:16px">{{ $gurus->links() }}</div>
</div>
@endsection
