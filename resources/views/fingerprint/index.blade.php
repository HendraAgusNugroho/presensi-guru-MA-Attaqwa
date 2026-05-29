@extends('layouts.app')
@section('title','Log Fingerprint')

@section('content')
<div class="page-header" style="display:flex;align-items:center;justify-content:space-between">
    <div>
        <h1>Log Fingerprint</h1>
        <p>Riwayat seluruh data scan dari mesin fingerprint</p>
    </div>
    <a href="{{ route('fingerprint.import') }}" class="btn btn-primary">
        <i class="fas fa-file-import"></i> Import Data
    </a>
</div>

<div class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr><th>#</th><th>ID Fingerprint</th><th>Nama Guru</th><th>Waktu Scan</th><th>Tipe</th><th>Status Proses</th></tr>
            </thead>
            <tbody>
            @forelse($logs as $i => $log)
            <tr>
                <td>{{ $logs->firstItem() + $i }}</td>
                <td><code style="background:#f1f5f9;padding:2px 6px;border-radius:4px">{{ $log->id_fingerprint }}</code></td>
                <td>
                    @if($log->guru)
                        <strong>{{ $log->guru->nama }}</strong>
                        <div style="font-size:.75rem;color:#94a3b8">{{ $log->guru->id_pengguna }}</div>
                    @else
                        <span style="color:#ef4444;font-size:.82rem"><i class="fas fa-exclamation-circle"></i> Tidak ditemukan</span>
                    @endif
                </td>
                <td>{{ $log->waktu_scan->format('d/m/Y H:i:s') }}</td>
                <td>
                    <span class="badge {{ $log->tipe=='masuk' ? 'badge-hadir' : 'badge-izin' }}">
                        {{ strtoupper($log->tipe) }}
                    </span>
                </td>
                <td>
                    @if($log->diproses)
                        <span class="badge badge-hadir"><i class="fas fa-check"></i> Diproses</span>
                    @else
                        <span class="badge badge-tidak-hadir"><i class="fas fa-times"></i> Gagal</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="6" style="text-align:center;color:#94a3b8;padding:32px">Belum ada log fingerprint</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top:16px">{{ $logs->links() }}</div>
</div>
@endsection
