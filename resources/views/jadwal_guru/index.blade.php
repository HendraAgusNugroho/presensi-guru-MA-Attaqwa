@extends('layouts.app')
@section('title','Jadwal Guru Per Hari')

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

.time-input {
    width: 90px;
    padding: 5px 8px;
    border: 1.5px solid #d4e8d4;
    border-radius: 7px;
    font-size: .8rem;
    font-family: 'Inter', sans-serif;
    color: #1a2e1a;
    background: #f8fdf8;
    outline: none;
    transition: border-color .2s, box-shadow .2s;
    text-align: center;
}
.time-input:focus {
    border-color: hsl(145,60%,40%);
    background: #fff;
    box-shadow: 0 0 0 3px hsl(145,60%,90%);
}
.time-input:placeholder-shown { color: #94a3b8; }

/* Indikator: kosong = tidak mengajar */
.time-input-wrap { display: flex; align-items: center; justify-content: center; gap: 4px; }
.no-class-badge {
    font-size: .65rem;
    color: #94a3b8;
    font-style: italic;
}

.btn-save-row {
    padding: 5px 11px;
    background: hsl(145,60%,28%);
    color: #fff;
    border: none;
    border-radius: 7px;
    font-size: .75rem;
    font-weight: 600;
    cursor: pointer;
    font-family: 'Inter', sans-serif;
    transition: opacity .2s;
    white-space: nowrap;
}
.btn-save-row:hover { opacity: .85; }

.legend-box {
    display: flex;
    gap: 16px;
    flex-wrap: wrap;
    align-items: center;
    font-size: .8rem;
    color: #5a7a5a;
    padding: 12px 0 4px;
}
.legend-dot {
    width: 10px; height: 10px; border-radius: 50%;
    display: inline-block; margin-right: 4px;
}

/* Hari header colored */
.hari-senin    { background: hsl(145,60%,22%) !important; }
.hari-selasa   { background: hsl(145,50%,26%) !important; }
.hari-rabu     { background: hsl(145,45%,30%) !important; }
.hari-kamis    { background: hsl(145,40%,34%) !important; }
.hari-jumat    { background: hsl(145,35%,38%) !important; }

@media(max-width:600px) {
    .time-input { width: 72px; font-size: .72rem; padding: 4px 6px; }
    .jadwal-table th, .jadwal-table td { padding: 6px 6px; }
}
</style>
@endpush

@section('content')

<div class="page-header" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px">
    <div>
        <h1><i class="fas fa-calendar-days" style="color:hsl(145,60%,28%)"></i> Jadwal Guru Per Hari</h1>
        <p>Atur jam masuk tiap guru sesuai hari mengajar. Kosongkan jika guru tidak mengajar hari tersebut.</p>
    </div>
    <button type="submit" form="formJadwal" class="btn btn-primary">
        <i class="fas fa-save"></i> Simpan Semua
    </button>
</div>

@if(session('success'))
<div class="alert alert-success" style="margin-bottom:16px">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
</div>
@endif

@if($errors->any())
<div class="alert alert-danger" style="margin-bottom:16px">
    <i class="fas fa-exclamation-circle"></i> {{ $errors->first() }}
</div>
@endif

<!-- Keterangan -->
<div class="card" style="padding:14px 18px;margin-bottom:16px;background:#f0fdf4;border:1.5px solid #bbf7d0;">
    <div class="legend-box">
        <span><span class="legend-dot" style="background:#4ade80"></span><strong>Diisi</strong> = Guru wajib hadir & scan QR jam tersebut</span>
        <span><span class="legend-dot" style="background:#94a3b8"></span><strong>Kosong</strong> = Guru tidak mengajar, tidak perlu absen</span>
        <span><i class="fas fa-clock" style="color:hsl(145,60%,28%);font-size:.8rem;margin-right:4px"></i>Jam pulang semua guru: <strong>14:30</strong> (otomatis)</span>
    </div>
</div>

<div class="card">
    <form method="POST" action="{{ route('jadwal_guru.simpan') }}" id="formJadwal">
        @csrf
        <div class="jadwal-table-wrap">
            <table class="jadwal-table">
                <thead>
                    <tr>
                        <th>Nama Guru</th>
                        @foreach($namaHari as $kode => $nama)
                            <th class="hari-{{ strtolower($nama) }}">
                                <i class="fas fa-calendar-day" style="font-size:.7rem;opacity:.8;margin-right:4px"></i>
                                {{ $nama }}
                            </th>
                        @endforeach
                        <th style="background:hsl(145,60%,14%)">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($gurus as $i => $guru)
                    @php
                        $jadwalMap = $guru->jadwalHarian->keyBy('hari');
                    @endphp
                    <tr>
                        <td>
                            <div class="guru-name">
                                <span class="guru-no">{{ $i + 1 }}.</span>
                                {{ $guru->nama }}
                            </div>
                            <div style="font-size:.72rem;color:#94a3b8;margin-top:2px">
                                ID: {{ $guru->id_pengguna }} &nbsp;|&nbsp; {{ $guru->barcode }}
                            </div>
                        </td>

                        @foreach($namaHari as $kode => $namaH)
                            @php
                                $j = $jadwalMap->get($kode);
                                $jamVal = $j?->jam_masuk
                                    ? \Carbon\Carbon::parse($j->jam_masuk)->format('H:i')
                                    : '';
                            @endphp
                            <td style="text-align:center">
                                <div class="time-input-wrap">
                                    <input type="time"
                                           name="jadwal[{{ $guru->id }}][{{ $kode }}][masuk]"
                                           class="time-input"
                                           value="{{ $jamVal }}"
                                           placeholder="--:--"
                                           title="{{ $namaH }} — {{ $guru->nama }}">
                                </div>
                            </td>
                        @endforeach

                        <td style="text-align:center">
                            <button type="submit"
                                    class="btn-save-row"
                                    formaction="{{ route('jadwal_guru.simpan') }}"
                                    title="Simpan jadwal {{ $guru->nama }}">
                                <i class="fas fa-check"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <!-- Tombol simpan semua di bawah juga -->
        <div style="display:flex;justify-content:flex-end;gap:10px;padding:16px 0 4px;border-top:1px solid #e8f5e9;margin-top:8px">
            <a href="{{ route('jadwal_guru.index') }}" class="btn btn-secondary">
                <i class="fas fa-rotate"></i> Reset
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Simpan Semua Jadwal
            </button>
        </div>
    </form>
</div>

@endsection

@push('scripts')
<script>
// Highlight input yang diisi
document.querySelectorAll('.time-input').forEach(function(input) {
    function updateStyle() {
        if (input.value) {
            input.style.borderColor = 'hsl(145,60%,40%)';
            input.style.background  = '#f0fdf4';
            input.style.fontWeight  = '700';
            input.style.color       = 'hsl(145,60%,22%)';
        } else {
            input.style.borderColor = '#d4e8d4';
            input.style.background  = '#f8fdf8';
            input.style.fontWeight  = '400';
            input.style.color       = '#94a3b8';
        }
    }
    updateStyle();
    input.addEventListener('input', updateStyle);
    input.addEventListener('change', updateStyle);
});
</script>
@endpush
