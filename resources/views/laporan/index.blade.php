@extends('layouts.app')
@section('title','Laporan Presensi')

@push('styles')
<style>
.filter-form { display:flex; flex-wrap:wrap; gap:12px; align-items:flex-end; }
.filter-form .form-group { margin:0; flex:1; min-width:140px; }
.header-row { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; margin-bottom:20px; }
.export-btns { display:flex; gap:8px; flex-wrap:wrap; }
</style>
@endpush

@section('content')

<div class="header-row">
    <div class="page-header" style="margin-bottom:0">
        <h1><i class="fas fa-chart-bar" style="color:hsl(145,60%,28%)"></i> Laporan Presensi</h1>
        <p>Rekap, filter, dan ekspor data presensi guru</p>
    </div>
    <div class="export-btns">
        <a href="{{ route('laporan.pdf', request()->query()) }}" class="btn btn-danger" target="_blank">
            <i class="fas fa-file-pdf"></i> Export PDF
        </a>
        <a href="{{ route('laporan.excel', request()->query()) }}" class="btn btn-success">
            <i class="fas fa-file-excel"></i> Export Excel (2 Sheet)
        </a>
        <button onclick="window.print()" class="btn btn-secondary">
            <i class="fas fa-print"></i> Print
        </button>
    </div>
</div>

<!-- Filter Card -->
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-filter" style="color:hsl(145,60%,28%)"></i> Filter Laporan</h3>
    </div>
    <form method="GET" class="filter-form">
        <div class="form-group" style="min-width:140px">
            <label>Dari Tanggal</label>
            <input type="date" name="dari" class="form-control" value="{{ $dari }}">
        </div>
        <div class="form-group" style="min-width:140px">
            <label>Sampai Tanggal</label>
            <input type="date" name="sampai" class="form-control" value="{{ $sampai }}">
        </div>
        <div class="form-group" style="min-width:180px">
            <label>Nama Guru</label>
            <select name="guru_id" class="form-control">
                <option value="">— Semua Guru —</option>
                @foreach($gurus as $g)
                    <option value="{{ $g->id }}" @selected($guruId == $g->id)>{{ $g->nama }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group" style="min-width:130px">
            <label>Status</label>
            <select name="status" class="form-control">
                <option value="">— Semua —</option>
                <option value="hadir"        @selected($status=='hadir')>Hadir</option>
                <option value="telat"        @selected($status=='telat')>Terlambat</option>
                <option value="tidak_hadir"  @selected($status=='tidak_hadir')>Tidak Hadir</option>
                <option value="izin"         @selected($status=='izin')>Izin</option>
                <option value="sakit"        @selected($status=='sakit')>Sakit</option>
                <option value="alpha"        @selected($status=='alpha')>Alpha</option>
            </select>
        </div>
        <div style="display:flex;gap:8px;align-items:flex-end;padding-bottom:0">
            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Tampilkan</button>
            <a href="{{ route('laporan.index') }}" class="btn btn-secondary" title="Reset filter">
                <i class="fas fa-rotate"></i>
            </a>
        </div>
    </form>
</div>

<!-- Rekap Statistik -->
<div class="stats-grid" style="grid-template-columns:repeat(auto-fit,minmax(120px,1fr));margin-bottom:18px">
    <div class="stat-card total">
        <div class="stat-icon"><i class="fas fa-list"></i></div>
        <div class="stat-label">Total</div>
        <div class="stat-value">{{ $rekap['total'] }}</div>
    </div>
    <div class="stat-card hadir">
        <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
        <div class="stat-label">Hadir</div>
        <div class="stat-value">{{ $rekap['hadir'] }}</div>
    </div>
    <div class="stat-card telat">
        <div class="stat-icon"><i class="fas fa-clock"></i></div>
        <div class="stat-label">Terlambat</div>
        <div class="stat-value">{{ $rekap['telat'] }}</div>
    </div>
    <div class="stat-card absen">
        <div class="stat-icon"><i class="fas fa-user-xmark"></i></div>
        <div class="stat-label">Tidak Hadir</div>
        <div class="stat-value">{{ $rekap['tidak_hadir'] }}</div>
    </div>
    <div class="stat-card izin">
        <div class="stat-icon"><i class="fas fa-file-circle-check"></i></div>
        <div class="stat-label">Izin</div>
        <div class="stat-value">{{ $rekap['izin'] }}</div>
    </div>
    <div class="stat-card sakit">
        <div class="stat-icon"><i class="fas fa-kit-medical"></i></div>
        <div class="stat-label">Sakit</div>
        <div class="stat-value">{{ $rekap['sakit'] }}</div>
    </div>
    @if($rekap['alpha'] > 0)
    <div class="stat-card" style="border-color:#db2777">
        <div class="stat-icon" style="background:#fce7f3;color:#db2777"><i class="fas fa-ban"></i></div>
        <div class="stat-label">Alpha</div>
        <div class="stat-value" style="color:#db2777">{{ $rekap['alpha'] }}</div>
    </div>
    @endif
</div>

<!-- Info Export Excel -->
<div class="alert alert-info" style="margin-bottom:18px">
    <i class="fas fa-circle-info"></i>
    <span><strong>Export Excel 2 Sheet:</strong> Sheet 1 berisi detail jam masuk &amp; pulang per guru. Sheet 2 berisi rekap keseluruhan (hadir, terlambat, izin, sakit, alpha) beserta persentase kehadiran masing-masing guru.</span>
</div>

<!-- Tabel Laporan -->
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-table" style="color:hsl(145,60%,28%)"></i>
            Detail Laporan &mdash;
            <span style="font-weight:500;color:#5a7a5a;font-size:.9rem">
                {{ \Carbon\Carbon::parse($dari)->isoFormat('D MMM Y') }} s/d {{ \Carbon\Carbon::parse($sampai)->isoFormat('D MMM Y') }}
            </span>
        </h3>
        <span style="font-size:.82rem;color:#5a7a5a">{{ $presensis->total() }} data</span>
    </div>
    {{-- Info baris virtual --}}
    @if($rekap['tidak_hadir'] > 0)
    <div style="padding:10px 0 4px;">
        <div class="alert alert-warning" style="margin:0;font-size:.82rem;padding:9px 14px;">
            <i class="fas fa-triangle-exclamation"></i>
            <span>Guru yang <strong>tidak memiliki data presensi sama sekali</strong> dalam periode ini ditampilkan otomatis sebagai <strong>Tidak Hadir</strong>.</span>
        </div>
    </div>
    @endif
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th style="width:40px">#</th>
                    <th>Tanggal</th>
                    <th>Nama Guru</th>
                    <th>ID</th>
                    <th>Jam Masuk</th>
                    <th>Jam Pulang</th>
                    <th>Status</th>
                    <th>Telat</th>
                    <th>Metode</th>
                </tr>
            </thead>
            <tbody>
            @forelse($presensis as $i => $p)
            <tr>
                <td style="text-align:center;color:#94a3b8">{{ $presensis->firstItem() + $i }}</td>
                <td style="white-space:nowrap">
                    <span style="font-weight:600">{{ $p->tanggal->format('d/m/Y') }}</span><br>
                    <small style="color:#5a7a5a">{{ $p->tanggal->isoFormat('ddd') }}</small>
                </td>
                <td><strong>{{ $p->guru->nama ?? '-' }}</strong></td>
                <td style="font-size:.8rem;color:#5a7a5a;white-space:nowrap">{{ $p->guru->id_pengguna ?? '-' }}</td>
                <td style="font-weight:600;color:hsl(145,60%,28%)">{{ $p->jam_masuk ?? '-' }}</td>
                <td style="color:#5a7a5a">{{ $p->jam_pulang ?? '-' }}</td>
                <td>
                    @php $sc = str_replace('_','-',$p->status); @endphp
                    <span class="badge badge-{{ $sc }}">
                        {{ strtoupper(str_replace('_',' ',$p->status)) }}
                    </span>
                </td>
                <td>
                    @if($p->menit_telat > 0)
                        <span style="color:#f59e0b;font-weight:700;font-size:.88rem">+{{ $p->menit_telat }} mnt</span>
                    @else
                        <span style="color:#94a3b8">—</span>
                    @endif
                </td>
                <td style="font-size:.8rem;color:#5a7a5a">{{ ucfirst($p->metode ?? '-') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="9">
                    <div class="empty-state">
                        <i class="fas fa-calendar-xmark"></i>
                        Tidak ada data presensi pada rentang tanggal ini
                    </div>
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($presensis->hasPages())
    <div style="margin-top:16px;padding-top:14px;border-top:1px solid #d4e8d4">
        {{ $presensis->links() }}
    </div>
    @endif
</div>
@endsection
