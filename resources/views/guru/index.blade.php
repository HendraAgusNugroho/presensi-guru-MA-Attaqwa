@extends('layouts.app')
@section('title', 'Data Guru')
@section('meta_description', 'Kelola data guru MA Attaqwa — daftar, tambah, edit, hapus, dan QR code presensi guru.')

@section('content')
<div class="page-header header-row" style="display:flex;align-items:center;justify-content:space-between">
    <div>
        <h1>Data Guru</h1>
        <p>Kelola data guru dan QR Code presensi</p>
    </div>
    <a href="{{ route('guru.create') }}" class="btn btn-primary">
        <i class="fas fa-plus" aria-hidden="true"></i> Tambah Guru
    </a>
</div>

<div class="card">
    <form method="GET" style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap" aria-label="Filter data guru">
        <div style="flex:1;min-width:200px">
            <label for="guru-search" class="sr-only">Cari nama atau NIP</label>
            <input type="search" name="search" id="guru-search" class="form-control"
                   placeholder="Cari nama atau NIP..." value="{{ request('search') }}"
                   autocomplete="off">
        </div>
        <div>
            <label for="guru-status" class="sr-only">Filter status guru</label>
            <select name="status" id="guru-status" class="form-control" style="width:auto" aria-label="Filter status guru">
                <option value="">Semua Status</option>
                <option value="aktif" @selected(request('status')=='aktif')>Aktif</option>
                <option value="nonaktif" @selected(request('status')=='nonaktif')>Non-Aktif</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-search" aria-hidden="true"></i> Cari
        </button>
        <a href="{{ route('guru.index') }}" class="btn btn-secondary btn-icon" aria-label="Reset filter pencarian">
            <i class="fas fa-refresh" aria-hidden="true"></i>
            <span class="sr-only">Reset filter</span>
        </a>
    </form>
</div>

<div class="card">
    <div class="table-wrapper">
        <table>
            <caption class="sr-only">Daftar guru MA Attaqwa</caption>
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Nama</th>
                    <th scope="col">ID</th>
                    <th scope="col">Jabatan</th>
                    <th scope="col">Mapel</th>
                    <th scope="col">ID Fingerprint</th>
                    <th scope="col">Status</th>
                    <th scope="col">Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse($gurus as $i => $g)
            <tr>
                <td>{{ $gurus->firstItem() + $i }}</td>
                <td>
                    <div style="display:flex;align-items:center;gap:10px">
                        <div style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,#ede9fe,#dbeafe);
                            display:flex;align-items:center;justify-content:center;font-weight:700;color:#4f46e5;font-size:.9rem"
                            aria-hidden="true">
                            {{ strtoupper(substr($g->nama,0,1)) }}
                        </div>
                        <div>
                            <strong>{{ $g->nama }}</strong>
                            <div style="font-size:.75rem;color:#64748b">{{ $g->email ?? '-' }}</div>
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
                        <span style="color:#64748b;font-size:.8rem">Belum set</span>
                    @endif
                </td>
                <td>
                    <span class="badge {{ $g->status == 'aktif' ? 'badge-hadir' : 'badge-tidak-hadir' }}">
                        {{ strtoupper($g->status) }}
                    </span>
                </td>
                <td>
                    <div style="display:flex;gap:4px" role="group" aria-label="Aksi untuk {{ $g->nama }}">
                        <a href="{{ route('guru.show', $g) }}" class="btn btn-secondary btn-sm btn-icon"
                           aria-label="Lihat detail {{ $g->nama }}">
                            <i class="fas fa-eye" aria-hidden="true"></i>
                        </a>
                        <a href="{{ route('guru.edit', $g) }}" class="btn btn-primary btn-sm btn-icon"
                           aria-label="Edit {{ $g->nama }}">
                            <i class="fas fa-pen" aria-hidden="true"></i>
                        </a>
                        <form method="POST" action="{{ route('guru.destroy', $g) }}"
                              data-confirm="Hapus guru {{ $g->nama }}?">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm btn-icon"
                                    aria-label="Hapus {{ $g->nama }}">
                                <i class="fas fa-trash" aria-hidden="true"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" style="text-align:center;color:#64748b;padding:32px">Belum ada data guru</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <nav aria-label="Navigasi halaman data guru" style="margin-top:16px">{{ $gurus->links() }}</nav>
</div>
@endsection
