@extends('layouts.app')
@section('title','Detail Guru')

@section('content')
<div class="page-header" style="display:flex;align-items:center;justify-content:space-between">
    <div>
        <h1>Detail Guru</h1>
        <p>{{ $guru->nama }}</p>
    </div>
    <div style="display:flex;gap:8px">
        <a href="{{ route('guru.edit', $guru) }}" class="btn btn-primary">
            <i class="fas fa-pen"></i> Edit
        </a>
        <a href="{{ route('guru.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="card-grid-2">
    <div class="card">
        <div style="display:flex;align-items:center;gap:16px;margin-bottom:20px">
            <div style="width:64px;height:64px;border-radius:16px;background:linear-gradient(135deg,#4f46e5,#7c3aed);
                display:flex;align-items:center;justify-content:center;color:#fff;font-size:1.8rem;font-weight:800">
                {{ strtoupper(substr($guru->nama,0,1)) }}
            </div>
            <div>
                <h2 style="font-size:1.1rem;font-weight:700">{{ $guru->nama }}</h2>
                <div style="color:#64748b;font-size:.85rem">{{ $guru->jabatan ?? 'Guru' }}</div>
                <span class="badge {{ $guru->status == 'aktif' ? 'badge-hadir' : 'badge-tidak-hadir' }}" style="margin-top:4px">
                    {{ strtoupper($guru->status) }}
                </span>
            </div>
        </div>
        <table style="font-size:.875rem">
            <tr><td style="color:#64748b;padding:6px 0;width:130px">ID</td><td><strong>{{ $guru->id_pengguna }}</strong></td></tr>
            <tr><td style="color:#64748b;padding:6px 0">Email</td><td>{{ $guru->email ?? '-' }}</td></tr>
            <tr><td style="color:#64748b;padding:6px 0">No. HP</td><td>{{ $guru->no_hp ?? '-' }}</td></tr>
            <tr><td style="color:#64748b;padding:6px 0">Mata Pelajaran</td><td>{{ $guru->mata_pelajaran ?? '-' }}</td></tr>
            <tr><td style="color:#64748b;padding:6px 0">ID Fingerprint</td>
                <td>
                    @if($guru->id_fingerprint)
                        <span style="background:#dbeafe;color:#1d4ed8;padding:2px 8px;border-radius:99px;font-size:.75rem;font-weight:700">
                            {{ $guru->id_fingerprint }}
                        </span>
                    @else - @endif
                </td>
            </tr>
            <tr><td style="color:#64748b;padding:6px 0">QR Code</td>
                <td><code style="background:#f1f5f9;padding:2px 8px;border-radius:4px;font-size:.8rem">{{ $guru->barcode }}</code></td>
            </tr>
        </table>
    </div>

    <div class="card">
        <div class="card-header"><h3><i class="fas fa-history" style="color:#4f46e5"></i> Riwayat Presensi</h3></div>
        <div class="table-wrapper">
            <table>
                <thead><tr><th>Tanggal</th><th>Masuk</th><th>Pulang</th><th>Status</th></tr></thead>
                <tbody>
                @forelse($presensi as $p)
                <tr>
                    <td>{{ $p->tanggal->format('d/m/Y') }}</td>
                    <td>{{ $p->jam_masuk ?? '-' }}</td>
                    <td>{{ $p->jam_pulang ?? '-' }}</td>
                    <td>
                        <span class="badge badge-{{ str_replace('_','-',$p->status) }}">
                            {{ strtoupper(str_replace('_',' ',$p->status)) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" style="text-align:center;color:#94a3b8">Belum ada riwayat presensi</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top:12px">{{ $presensi->links() }}</div>
    </div>
</div>
@endsection
