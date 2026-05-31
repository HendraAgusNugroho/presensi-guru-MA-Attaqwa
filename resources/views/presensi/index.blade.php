@extends('layouts.app')
@section('title','Data Presensi')
@section('meta_description', 'Rekap dan kelola presensi guru MA Attaqwa — filter berdasarkan tanggal, guru, dan status kehadiran.')

@section('content')
<div class="page-header" style="display:flex;align-items:center;justify-content:space-between">
    <div>
        <h1>Data Presensi</h1>
        <p>Rekap presensi berdasarkan tanggal — guru tanpa presensi otomatis ditampilkan sebagai Tidak Hadir</p>
    </div>
    <a href="{{ route('presensi.scan') }}" class="btn btn-primary">
        <i class="fas fa-qrcode"></i> Scan QR Code
    </a>
</div>

<!-- Filter -->
<div class="card">
    <form method="GET" class="filter-form" style="display:flex;flex-wrap:wrap;gap:12px;align-items:flex-end" aria-label="Filter data presensi">
        <div class="form-group" style="margin:0;flex:1;min-width:140px">
            <label for="filter-tanggal">Tanggal</label>
            <input type="date" name="tanggal" id="filter-tanggal" class="form-control"
                   value="{{ request('tanggal', $tanggal->format('Y-m-d')) }}">
        </div>
        <div class="form-group" style="margin:0;flex:1;min-width:140px">
            <label for="filter-guru">Guru</label>
            <select name="guru_id" id="filter-guru" class="form-control">
                <option value="">Semua Guru</option>
                @foreach($gurus as $g)
                    <option value="{{ $g->id }}" @selected(request('guru_id') == $g->id)>{{ $g->nama }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group" style="margin:0;flex:1;min-width:120px">
            <label for="filter-status">Status</label>
            <select name="status" id="filter-status" class="form-control">
                <option value="">Semua</option>
                <option value="hadir"        @selected(request('status')=='hadir')>Hadir</option>
                <option value="telat"        @selected(request('status')=='telat')>Telat</option>
                <option value="tidak_hadir"  @selected(request('status')=='tidak_hadir')>Tidak Hadir</option>
                <option value="izin"         @selected(request('status')=='izin')>Izin</option>
                <option value="sakit"        @selected(request('status')=='sakit')>Sakit</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary" style="height:42px">
            <i class="fas fa-search" aria-hidden="true"></i> Filter
        </button>
        <a href="{{ route('presensi.index') }}" class="btn btn-secondary" style="height:42px">
            <i class="fas fa-refresh" aria-hidden="true"></i> Reset
        </a>
    </form>
</div>

<!-- Rekap cepat hari ini -->
@php
    $totalGuru   = $presensis->count();
    $jmlHadir    = $presensis->where('status','hadir')->count();
    $jmlTelat    = $presensis->where('status','telat')->count();
    $jmlTidakHadir = $presensis->where('status','tidak_hadir')->count();
    $jmlIzin     = $presensis->where('status','izin')->count();
    $jmlSakit    = $presensis->where('status','sakit')->count();
@endphp
<div class="stats-grid stats-grid--presensi" style="margin-bottom:18px">
    <div class="stat-card total">
        <div class="stat-icon"><i class="fas fa-users"></i></div>
        <div class="stat-label">Total Guru</div>
        <div class="stat-value">{{ $totalGuru }}</div>
    </div>
    <div class="stat-card hadir">
        <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
        <div class="stat-label">Hadir</div>
        <div class="stat-value">{{ $jmlHadir }}</div>
    </div>
    <div class="stat-card telat">
        <div class="stat-icon"><i class="fas fa-clock"></i></div>
        <div class="stat-label">Terlambat</div>
        <div class="stat-value">{{ $jmlTelat }}</div>
    </div>
    <div class="stat-card absen">
        <div class="stat-icon"><i class="fas fa-user-xmark"></i></div>
        <div class="stat-label">Tidak Hadir</div>
        <div class="stat-value">{{ $jmlTidakHadir }}</div>
    </div>
    <div class="stat-card izin">
        <div class="stat-icon"><i class="fas fa-file-circle-check"></i></div>
        <div class="stat-label">Izin</div>
        <div class="stat-value">{{ $jmlIzin }}</div>
    </div>
    <div class="stat-card sakit">
        <div class="stat-icon"><i class="fas fa-kit-medical"></i></div>
        <div class="stat-label">Sakit</div>
        <div class="stat-value">{{ $jmlSakit }}</div>
    </div>
</div>

<!-- Input Manual -->
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-pen" style="color:#4f46e5"></i> Input Presensi Manual</h3>
        <button onclick="toggleManual()" class="btn btn-secondary btn-sm">
            <i class="fas fa-plus"></i> Tambah
        </button>
    </div>
    <form method="POST" action="{{ route('presensi.manual') }}" id="formManual" style="display:none">
        @csrf
        <div class="form-row" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:12px;margin-bottom:12px">
            <div class="form-group" style="margin:0">
                <label>Guru</label>
                <select name="guru_id" class="form-control" id="manualGuruId" required>
                    <option value="">-- Pilih Guru --</option>
                    @foreach($gurus as $g)
                        <option value="{{ $g->id }}">{{ $g->nama }} ({{ $g->id_pengguna }})</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group" style="margin:0">
                <label>Tanggal</label>
                <input type="date" name="tanggal" id="manualTanggal" class="form-control"
                       value="{{ $tanggal->format('Y-m-d') }}" required>
            </div>
            <div class="form-group" style="margin:0">
                <label>Status</label>
                <select name="status" class="form-control" required>
                    <option value="hadir">Hadir</option>
                    <option value="telat">Telat</option>
                    <option value="tidak_hadir">Tidak Hadir</option>
                    <option value="izin">Izin</option>
                    <option value="sakit">Sakit</option>
                </select>
            </div>
            <div class="form-group" style="margin:0">
                <label>Jam Masuk</label>
                <input type="time" name="jam_masuk" class="form-control">
            </div>
            <div class="form-group" style="margin:0">
                <label>Jam Pulang</label>
                <input type="time" name="jam_pulang" class="form-control">
            </div>
        </div>
        <div class="form-group">
            <label>Keterangan</label>
            <input type="text" name="keterangan" class="form-control" placeholder="Opsional...">
        </div>
        <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Simpan</button>
    </form>
</div>

<!-- Pengajuan Izin/Sakit Menunggu Persetujuan -->
@php
    $menunggu = $presensis->filter(fn($p) => in_array($p->status, ['izin','sakit']) && ($p->approval_status ?? null) === 'menunggu');
@endphp
@if($menunggu->count() > 0)
<div class="card" style="border:1.5px solid #fde68a;">
    <div class="card-header" style="background:linear-gradient(135deg,#fffbeb,#fef9c3);">
        <h3 style="color:#92400e;">
            <i class="fas fa-clock" style="color:#d97706"></i>
            Pengajuan Izin/Sakit Menunggu Persetujuan
        </h3>
        <span style="background:#fde68a;color:#92400e;font-size:.75rem;font-weight:700;padding:3px 10px;border-radius:6px;">
            {{ $menunggu->count() }} pengajuan
        </span>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th scope="col">Guru</th>
                    <th scope="col">Tanggal</th>
                    <th scope="col">Status</th>
                    <th scope="col">Keterangan</th>
                    <th scope="col">Bukti</th>
                    <th scope="col">Aksi Persetujuan</th>
                </tr>
            </thead>
            <tbody>
            @foreach($menunggu as $p)
            @if($p->id)
            <tr>
                <td>
                    <strong>{{ $p->guru->nama ?? '-' }}</strong><br>
                    <small style="color:#94a3b8;font-size:.75rem">{{ $p->guru->id_pengguna ?? '' }}</small>
                </td>
                <td style="font-weight:600">{{ \Carbon\Carbon::parse($p->tanggal)->isoFormat('D MMM Y') }}</td>
                <td>
                    <span class="badge badge-{{ $p->status }}" style="font-size:.78rem">
                        {{ ucfirst($p->status) }}
                    </span>
                </td>
                <td style="font-size:.83rem;color:#475569;max-width:200px;">{{ $p->keterangan ?: '-' }}</td>
                <td>
                    @if($p->bukti_file ?? null)
                        <a href="{{ asset('storage/' . $p->bukti_file) }}" target="_blank"
                           style="color:#4f46e5;font-size:.8rem;display:flex;align-items:center;gap:4px;text-decoration:none;">
                            <i class="fas fa-file-arrow-down"></i> Lihat Bukti
                        </a>
                    @else
                        <span style="color:#94a3b8;font-size:.8rem">—</span>
                    @endif
                </td>
                <td>
                    <div style="display:flex;gap:6px;">
                        <form method="POST" action="{{ route('presensi.approval', $p) }}" style="display:inline">
                            @csrf @method('PATCH')
                            <input type="hidden" name="approval_status" value="disetujui">
                            <button type="submit" class="btn btn-sm btn-confirm"
                                style="background:#dcfce7;color:#15803d;border:1.5px solid #86efac;font-size:.75rem;font-weight:700;padding:5px 10px;border-radius:7px;cursor:pointer;font-family:'Inter',sans-serif;"
                                data-confirm="Setujui pengajuan {{ $p->status }} {{ $p->guru->nama ?? '-' }}?">
                                <i class="fas fa-check"></i> Setujui
                            </button>
                        </form>
                        <form method="POST" action="{{ route('presensi.approval', $p) }}" style="display:inline">
                            @csrf @method('PATCH')
                            <input type="hidden" name="approval_status" value="ditolak">
                            <button type="submit" class="btn btn-sm btn-confirm"
                                style="background:#fee2e2;color:#dc2626;border:1.5px solid #fca5a5;font-size:.75rem;font-weight:700;padding:5px 10px;border-radius:7px;cursor:pointer;font-family:'Inter',sans-serif;"
                                data-confirm="Tolak pengajuan {{ $p->status }} {{ $p->guru->nama ?? '-' }}?">
                                <i class="fas fa-xmark"></i> Tolak
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @endif
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<!-- Tabel Presensi -->
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-table" style="color:#4f46e5"></i>
            Presensi — {{ $tanggal->isoFormat('D MMMM Y') }}
        </h3>
        <span style="font-size:.82rem;color:#64748b">{{ $presensis->count() }} guru</span>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th scope="col">No</th><th scope="col">Guru</th><th scope="col">ID</th>
                    <th scope="col">Jam Masuk</th><th scope="col">Jam Pulang</th>
                    <th scope="col">Status</th><th scope="col">Metode</th><th scope="col">Telat</th>
                    <th scope="col">Approval</th>
                    @if(auth()->user()->isSuperAdmin())
                        <th scope="col">Edit Jam</th>
                    @endif
                    <th scope="col">Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse($presensis as $i => $p)
            @php $isVirtual = !($p->id ?? false); @endphp
            <tr @if($isVirtual) class="tr-virtual" @endif>
                <td>{{ $i + 1 }}</td>
                <td>
                    <strong>{{ $p->guru->nama ?? '-' }}</strong>
                    @if($isVirtual)
                        <span style="font-size:.68rem;background:#fee2e2;color:#dc2626;padding:2px 6px;border-radius:4px;margin-left:4px;font-weight:700;">
                            Belum Scan
                        </span>
                    @endif
                </td>
                <td style="font-size:.8rem;color:#94a3b8">{{ $p->guru->id_pengguna ?? '-' }}</td>
                <td>{{ $p->jam_masuk ?? '—' }}</td>
                <td>{{ $p->jam_pulang ?? '—' }}</td>
                <td>
                    <span class="badge badge-{{ str_replace('_','-',$p->status) }}">
                        {{ strtoupper(str_replace('_',' ',$p->status)) }}
                    </span>
                </td>
                <td style="font-size:.8rem">{{ ucfirst($p->metode ?? '-') }}</td>
                <td>
                    @if(($p->menit_telat ?? 0) > 0)
                        <span style="color:#f59e0b;font-weight:700">+{{ $p->menit_telat }} mnt</span>
                    @else
                        —
                    @endif
                </td>
                <td>
                    @if(in_array($p->status, ['izin','sakit']))
                        @php
                            $apClass = match($p->approval_status ?? null) {
                                'menunggu'  => 'approval-menunggu',
                                'disetujui' => 'approval-disetujui',
                                'ditolak'   => 'approval-ditolak',
                                default     => 'approval-none',
                            };
                            $apText = match($p->approval_status ?? null) {
                                'menunggu'  => 'Menunggu',
                                'disetujui' => 'Disetujui',
                                'ditolak'   => 'Ditolak',
                                default     => '—',
                            };
                        @endphp
                        <span class="approval-badge {{ $apClass }}">{{ $apText }}</span>
                    @else
                        <span style="color:#94a3b8;font-size:.8rem">—</span>
                    @endif
                </td>
                @if(auth()->user()->isSuperAdmin())
                <td>
                    @if(!$isVirtual)
                        @php
                            $jmEdit = $p->jam_masuk ? \Carbon\Carbon::parse($p->jam_masuk)->format('H:i') : '';
                            $jpEdit = $p->jam_pulang ? \Carbon\Carbon::parse($p->jam_pulang)->format('H:i') : '';
                        @endphp
                        <button type="button"
                            class="btn btn-secondary btn-sm btn-edit-jam"
                            aria-label="Edit jam masuk dan pulang {{ $p->guru->nama ?? '-' }}"
                            data-id="{{ $p->id }}"
                            data-jm="{{ $jmEdit }}"
                            data-jp="{{ $jpEdit }}"
                            data-nama="{{ $p->guru->nama ?? '-' }}">
                            <i class="fas fa-clock" aria-hidden="true"></i> Jam
                        </button>
                    @else
                        <span style="color:#94a3b8;font-size:.8rem">—</span>
                    @endif
                </td>
                @endif
                <td>
                    @if($isVirtual)
                        {{-- Baris virtual: tombol input manual cepat --}}
                        <button type="button"
                            class="btn btn-secondary btn-sm btn-input-manual"
                            data-guru-id="{{ $p->guru_id }}"
                            data-tanggal="{{ $tanggal->format('Y-m-d') }}"
                            aria-label="Input presensi manual untuk {{ $p->guru->nama ?? 'guru ini' }}">
                            <i class="fas fa-pen" aria-hidden="true"></i> Input
                        </button>
                    @else
                        {{-- Baris nyata: form update status --}}
                        <form method="POST" action="{{ route('presensi.status', $p) }}" style="display:inline-flex;gap:4px">
                            @csrf @method('PATCH')
                            <select name="status" class="form-control"
                                    style="width:auto;padding:4px 8px;font-size:.78rem">
                                @foreach(['hadir','telat','tidak_hadir','izin','sakit'] as $s)
                                    <option value="{{ $s }}" @selected($p->status == $s)>
                                        {{ ucfirst(str_replace('_',' ',$s)) }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-check"></i>
                            </button>
                        </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="{{ auth()->user()->isSuperAdmin() ? 11 : 10 }}" style="text-align:center;color:#94a3b8;padding:32px">
                    Belum ada data presensi untuk tanggal ini
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

@if(auth()->user()->isSuperAdmin())
<input type="hidden" id="presensi-base-url" value="{{ url('/') }}">
<div id="modalJamBackdrop" class="modal-jam-backdrop" style="display:none;" onclick="if(event.target===this) tutupModalJam()"></div>
<div id="modalJam" class="modal-jam" role="dialog" aria-modal="true" aria-labelledby="modalJamTitle" style="display:none;">
    <div class="modal-jam-inner">
        <h3 id="modalJamTitle" style="margin:0 0 12px;font-size:1.05rem;color:#1a2e1a;">
            <i class="fas fa-clock" style="color:hsl(145,60%,28%)"></i> Edit Jam Presensi
        </h3>
        <p id="modalJamGuru" style="margin:0 0 16px;font-size:.88rem;color:#64748b;"></p>
        <form id="formJamManual" method="POST" action="">
            @csrf
            @method('PATCH')
            <div class="form-group" style="margin-bottom:12px">
                <label for="modal_jam_masuk">Jam Masuk</label>
                <input type="time" name="jam_masuk" id="modal_jam_masuk" class="form-control" required>
            </div>
            <div class="form-group" style="margin-bottom:16px">
                <label for="modal_jam_pulang">Jam Pulang</label>
                <input type="time" name="jam_pulang" id="modal_jam_pulang" class="form-control" required>
            </div>
            <div style="display:flex;gap:10px;justify-content:flex-end">
                <button type="button" class="btn btn-secondary" onclick="tutupModalJam()">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>
<style>
.modal-jam-backdrop{position:fixed;inset:0;background:rgba(15,23,42,.45);z-index:1040;}
.modal-jam{position:fixed;inset:0;z-index:1050;display:flex;align-items:center;justify-content:center;padding:20px;pointer-events:none;}
.modal-jam-inner{pointer-events:auto;background:#fff;border-radius:16px;padding:24px 28px;max-width:400px;width:100%;box-shadow:0 24px 48px rgba(0,0,0,.2);}
</style>
@endif
@endsection

@push('scripts')
@if(auth()->user()->isSuperAdmin())
<script>
function jamManualActionUrl(id) {
    var baseEl = document.getElementById('presensi-base-url');
    var base = baseEl ? baseEl.value : '';
    return base + '/presensi/' + id + '/jam-manual';
}
function bukaModalJam(id, jamMasuk, jamPulang, namaGuru) {
    var form = document.getElementById('formJamManual');
    var bd = document.getElementById('modalJamBackdrop');
    var md = document.getElementById('modalJam');
    if (!form || !bd || !md) return;
    form.action = jamManualActionUrl(id);
    document.getElementById('modal_jam_masuk').value = jamMasuk || '';
    document.getElementById('modal_jam_pulang').value = jamPulang || '';
    document.getElementById('modalJamGuru').textContent = namaGuru || '';
    bd.style.display = 'block';
    md.style.display = 'flex';
    document.body.style.overflow = 'hidden';
    document.getElementById('modal_jam_masuk').focus();
}
function tutupModalJam() {
    var bd = document.getElementById('modalJamBackdrop');
    var md = document.getElementById('modalJam');
    if (bd) bd.style.display = 'none';
    if (md) md.style.display = 'none';
    document.body.style.overflow = '';
}
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && document.getElementById('modalJam') && document.getElementById('modalJam').style.display === 'flex') {
        tutupModalJam();
    }
});
document.querySelectorAll('.btn-edit-jam').forEach(function(btn) {
    btn.addEventListener('click', function() {
        bukaModalJam(
            btn.dataset.id,
            btn.dataset.jm || '',
            btn.dataset.jp || '',
            btn.dataset.nama || ''
        );
    });
});
</script>
@endif
<script>
document.querySelectorAll('.btn-confirm').forEach(function(btn) {
    btn.addEventListener('click', function(e) {
        var msg = btn.getAttribute('data-confirm');
        if (msg && !confirm(msg)) {
            e.preventDefault();
        }
    });
});

function toggleManual() {
    const f = document.getElementById('formManual');
    f.style.display = f.style.display === 'none' ? 'block' : 'none';
}

function inputManualCepat(guruId, tanggal) {
    const form = document.getElementById('formManual');
    form.style.display = 'block';
    document.getElementById('manualGuruId').value    = guruId;
    document.getElementById('manualTanggal').value   = tanggal;
    form.scrollIntoView({ behavior: 'smooth', block: 'center' });
    document.getElementById('manualGuruId').focus();
}

document.querySelectorAll('.btn-input-manual').forEach(function(btn) {
    btn.addEventListener('click', function() {
        inputManualCepat(btn.dataset.guruId, btn.dataset.tanggal);
    });
});
</script>
@endpush
