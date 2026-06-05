@extends('layouts.app')
@section('title','Jadwal Saya')

@push('styles')
<style>
.jadwal-table-wrap { overflow-x: auto; }
.jadwal-table {
    width: 100%;
    border-collapse: collapse;
    font-size: .875rem;
    min-width: 700px;
}
.jadwal-table th {
    background: hsl(145,60%,18%);
    color: #fff;
    padding: 11px 14px;
    font-weight: 700;
    text-align: center;
    white-space: nowrap;
}
.jadwal-table th:first-child { text-align: left; min-width: 200px; }
.jadwal-table td {
    padding: 7px 10px;
    border-bottom: 1px solid #e8f5e9;
    vertical-align: middle;
}
.jadwal-table tbody tr:nth-child(even) { background: #f9fdf9; }
.jadwal-table tbody tr:hover { background: #f0fdf0; }

.guru-name {
    font-weight: 600;
    color: #1a2e1a;
    font-size: .85rem;
}
.guru-no {
    font-size: .72rem;
    color: #94a3b8;
    font-weight: 400;
}

.time-display {
    font-size: .9rem;
    font-weight: 600;
    color: hsl(145,60%,28%);
    padding: 6px 12px;
    background: #f0fdf4;
    border-radius: 8px;
    display: inline-block;
}
.time-empty {
    font-size: .75rem;
    color: #94a3b8;
    font-style: italic;
}

/* Hari header colored */
.hari-senin    { background: hsl(145,60%,22%) !important; }
.hari-selasa   { background: hsl(145,50%,26%) !important; }
.hari-rabu     { background: hsl(145,45%,30%) !important; }
.hari-kamis    { background: hsl(145,40%,34%) !important; }
.hari-jumat    { background: hsl(145,35%,38%) !important; }

@media(max-width:600px) {
    .jadwal-table th, .jadwal-table td { padding: 6px 6px; }
}
</style>
@endpush

@section('content')

<div class="page-header">
    <h1><i class="fas fa-calendar-days" style="color:hsl(145,60%,28%)"></i> Jadwal Mengajar Saya</h1>
    <p>Jadwal kehadiran Anda per hari. Harap scan QR Code sesuai jam masuk yang tertera.</p>
</div>

<!-- Keterangan -->
<div class="card" style="padding:14px 18px;margin-bottom:16px;background:#f0fdf4;border:1.5px solid #bbf7d0;">
    <div style="display:flex;gap:16px;flex-wrap:wrap;align-items:center;font-size:.8rem;color:#5a7a5a;">
        <span><i class="fas fa-clock" style="color:hsl(145,60%,28%);font-size:.8rem;margin-right:4px"></i>Jam pulang: <strong>14:30</strong></span>
        <span><i class="fas fa-qrcode" style="color:hsl(145,60%,28%);font-size:.8rem;margin-right:4px"></i>Scan QR sesuai jam masuk</span>
    </div>
</div>

<div class="card">
    <div class="jadwal-table-wrap">
        <table class="jadwal-table">
            <thead>
                <tr>
                    <th>Hari</th>
                    @foreach($namaHari as $kode => $nama)
                        <th class="hari-{{ strtolower($nama) }}">
                            <i class="fas fa-calendar-day" style="font-size:.7rem;opacity:.8;margin-right:4px"></i>
                            {{ $nama }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div class="guru-name">
                            {{ $guru->nama }}
                        </div>
                        <div style="font-size:.72rem;color:#94a3b8;margin-top:2px">
                            ID: {{ $guru->id_pengguna }} &nbsp;|&nbsp; {{ $guru->barcode }}
                        </div>
                    </td>

                    @php
                        $jadwalMap = $guru->jadwalHarian->keyBy('hari');
                    @endphp

                    @foreach($namaHari as $kode => $namaH)
                        @php
                            $j = $jadwalMap->get($kode);
                            $jamVal = $j?->jam_masuk
                                ? \Carbon\Carbon::parse($j->jam_masuk)->format('H:i')
                                : null;
                        @endphp
                        <td style="text-align:center">
                            @if($jamVal)
                                <span class="time-display">{{ $jamVal }}</span>
                            @else
                                <span class="time-empty">-</span>
                            @endif
                        </td>
                    @endforeach
                </tr>
            </tbody>
        </table>
    </div>
</div>

@endsection
