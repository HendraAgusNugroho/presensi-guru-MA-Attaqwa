@extends('layouts.app')
@section('title','Izin / Sakit')

@push('styles')
<style>
.izin-page { max-width: 720px; margin: 0 auto; }

.status-today-card {
    border-radius: 14px;
    padding: 16px 20px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 14px;
}
.status-today-card.hadir   { background:#dcfce7; border:1.5px solid #86efac; }
.status-today-card.telat   { background:#fef9c3; border:1.5px solid #fde047; }
.status-today-card.izin    { background:#dbeafe; border:1.5px solid #93c5fd; }
.status-today-card.sakit   { background:#f3e8ff; border:1.5px solid #d8b4fe; }
.status-today-card.kosong  { background:#f8fafc; border:1.5px solid #e2e8f0; }

.form-card {
    background: #fff;
    border-radius: 16px;
    padding: 28px 24px;
    box-shadow: 0 2px 16px rgba(0,0,0,.07);
    margin-bottom: 24px;
}
.form-card h3 {
    font-size: 1rem;
    font-weight: 800;
    color: #1e293b;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}
.form-card h3 .icon-wrap {
    width: 34px; height: 34px;
    border-radius: 9px;
    background: linear-gradient(135deg, #4f46e5, #7c3aed);
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: .85rem;
}

.radio-group {
    display: flex;
    gap: 12px;
    margin-bottom: 18px;
}
.radio-option {
    flex: 1;
    cursor: pointer;
}
.radio-option input { display: none; }
.radio-label {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 14px 16px;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    font-weight: 600;
    font-size: .9rem;
    color: #64748b;
    transition: all .2s;
    background: #f8fafc;
}
.radio-label:hover {
    border-color: #c7d2fe;
    background: #eef2ff;
    color: #4f46e5;
}
.radio-option input:checked + .radio-label {
    border-color: #4f46e5;
    background: linear-gradient(135deg, #eef2ff, #f5f3ff);
    color: #4338ca;
}
.radio-option input[value="sakit"]:checked + .radio-label {
    border-color: #7c3aed;
    background: linear-gradient(135deg, #f5f3ff, #fdf4ff);
    color: #6d28d9;
}
.radio-label .ri-icon {
    width: 32px; height: 32px;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: .9rem;
}
.radio-option input[value="izin"]:checked + .radio-label .ri-icon  { background:#c7d2fe; color:#4338ca; }
.radio-option input[value="sakit"]:checked + .radio-label .ri-icon { background:#ddd6fe; color:#6d28d9; }
.radio-label .ri-icon { background:#e2e8f0; color:#94a3b8; }

.form-group { margin-bottom: 16px; }
.form-label {
    display: block;
    font-size: .82rem;
    font-weight: 700;
    color: #475569;
    margin-bottom: 6px;
}
.form-label .optional {
    font-size: .72rem;
    font-weight: 500;
    color: #94a3b8;
    margin-left: 4px;
}
.form-control-styled {
    width: 100%;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    padding: 10px 14px;
    font-size: .9rem;
    font-family: 'Inter', sans-serif;
    color: #1e293b;
    background: #fff;
    outline: none;
    transition: border-color .2s, box-shadow .2s;
    resize: vertical;
    box-sizing: border-box;
}
.form-control-styled:focus {
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99,102,241,.1);
}

.upload-area {
    border: 2px dashed #e2e8f0;
    border-radius: 12px;
    padding: 20px;
    text-align: center;
    cursor: pointer;
    transition: all .2s;
    background: #f8fafc;
}
.upload-area:hover { border-color: #a5b4fc; background: #eef2ff; }
.upload-area input[type=file] { display: none; }
.upload-area .upload-icon { font-size: 1.8rem; color: #94a3b8; margin-bottom: 8px; }
.upload-area p { font-size: .82rem; color: #64748b; margin: 0; }
.upload-area small { font-size: .72rem; color: #94a3b8; }
#file-name { font-size:.82rem; color:#4f46e5; font-weight:600; margin-top:8px; display:none; }

.btn-submit {
    width: 100%;
    padding: 14px;
    background: linear-gradient(135deg, #4f46e5, #7c3aed);
    color: #fff;
    border: none;
    border-radius: 12px;
    font-size: .95rem;
    font-weight: 700;
    cursor: pointer;
    font-family: 'Inter', sans-serif;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    transition: opacity .2s;
    margin-top: 20px;
}
.btn-submit:hover { opacity: .9; }

.riwayat-card {
    background: #fff;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 2px 16px rgba(0,0,0,.07);
}
.riwayat-card h3 {
    font-size: 1rem;
    font-weight: 800;
    color: #1e293b;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.riwayat-item {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 12px 0;
    border-bottom: 1px solid #f1f5f9;
}
.riwayat-item:last-child { border-bottom: none; }
.riwayat-icon {
    width: 40px; height: 40px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: .9rem;
    flex-shrink: 0;
}
.riwayat-icon.izin  { background: #dbeafe; color: #1d4ed8; }
.riwayat-icon.sakit { background: #f3e8ff; color: #7e22ce; }
.riwayat-info { flex: 1; min-width: 0; }
.riwayat-tanggal { font-weight: 700; font-size: .88rem; color: #1e293b; }
.riwayat-ket     { font-size: .78rem; color: #94a3b8; margin-top: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.approval-badge {
    font-size: .7rem; font-weight: 700; padding: 3px 10px; border-radius: 6px;
    flex-shrink: 0;
}
.approval-menunggu  { background: #fef9c3; color: #a16207; }
.approval-disetujui { background: #dcfce7; color: #15803d; }
.approval-ditolak   { background: #fee2e2; color: #dc2626; }
.approval-null      { background: #f1f5f9; color: #94a3b8; }
</style>
@endpush

@section('content')
<div class="izin-page">

    {{-- Status Hari Ini --}}
    @php
        $todayStatus = $presensiHariIni?->status;
        $cardClass = match($todayStatus) {
            'hadir','telat' => $todayStatus,
            'izin','sakit'  => $todayStatus,
            default         => 'kosong',
        };
        $statusInfo = match($todayStatus) {
            'hadir'  => ['#16a34a','fa-circle-check','Anda sudah hadir hari ini. Izin/sakit tidak dapat diajukan.'],
            'telat'  => ['#d97706','fa-clock','Anda sudah hadir (terlambat) hari ini. Izin/sakit tidak dapat diajukan.'],
            'izin'   => ['#1d4ed8','fa-file-circle-check','Pengajuan Izin untuk hari ini sudah tercatat.'],
            'sakit'  => ['#7e22ce','fa-heart-pulse','Pengajuan Sakit untuk hari ini sudah tercatat.'],
            default  => ['#64748b','fa-calendar-day','Belum ada presensi hari ini. Anda dapat mengajukan izin atau sakit.'],
        };
    @endphp
    <div class="status-today-card {{ $cardClass }}">
        <div style="width:40px;height:40px;border-radius:10px;background:{{ $statusInfo[0] }}20;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <i class="fas {{ $statusInfo[1] }}" style="color:{{ $statusInfo[0] }};font-size:1rem"></i>
        </div>
        <div>
            <div style="font-weight:700;font-size:.88rem;color:{{ $statusInfo[0] }};">Status Hari Ini</div>
            <div style="font-size:.82rem;color:#475569;margin-top:2px;">{{ $statusInfo[2] }}</div>
        </div>
    </div>

    {{-- Form Pengajuan --}}
    @if(!in_array($todayStatus, ['hadir','telat','izin','sakit']))
    <div class="form-card">
        <h3>
            <div class="icon-wrap"><i class="fas fa-file-medical"></i></div>
            Form Pengajuan Izin / Sakit
        </h3>

        <form method="POST" action="{{ route('presensi.ajukan_izin_sakit') }}" enctype="multipart/form-data">
            @csrf

            {{-- Pilih Status --}}
            <div class="form-group">
                <label class="form-label">Jenis Pengajuan <span style="color:#ef4444">*</span></label>
                <div class="radio-group">
                    <label class="radio-option">
                        <input type="radio" name="status" value="izin" {{ old('status') == 'izin' ? 'checked' : '' }} required>
                        <div class="radio-label">
                            <div class="ri-icon"><i class="fas fa-calendar-xmark"></i></div>
                            <span>Izin</span>
                        </div>
                    </label>
                    <label class="radio-option">
                        <input type="radio" name="status" value="sakit" {{ old('status') == 'sakit' ? 'checked' : '' }}>
                        <div class="radio-label">
                            <div class="ri-icon"><i class="fas fa-heart-pulse"></i></div>
                            <span>Sakit</span>
                        </div>
                    </label>
                </div>
                @error('status')
                    <div style="color:#dc2626;font-size:.78rem;margin-top:4px;"><i class="fas fa-circle-exclamation"></i> {{ $message }}</div>
                @enderror
            </div>

            {{-- Keterangan --}}
            <div class="form-group">
                <label class="form-label">
                    Alasan / Keterangan
                    <span class="optional">(opsional)</span>
                </label>
                <textarea name="keterangan" class="form-control-styled"
                    rows="3"
                    placeholder="Tuliskan alasan izin atau keterangan sakit Anda...">{{ old('keterangan') }}</textarea>
                @error('keterangan')
                    <div style="color:#dc2626;font-size:.78rem;margin-top:4px;"><i class="fas fa-circle-exclamation"></i> {{ $message }}</div>
                @enderror
            </div>

            {{-- Upload Bukti --}}
            <div class="form-group">
                <label class="form-label">
                    Upload Bukti
                    <span class="optional">(opsional — JPG, PNG, PDF, maks. 2MB)</span>
                </label>
                <label class="upload-area" for="bukti_file_input">
                    <input type="file" name="bukti_file" id="bukti_file_input"
                           accept=".jpg,.jpeg,.png,.pdf"
                           onchange="showFileName(this)">
                    <div class="upload-icon"><i class="fas fa-cloud-arrow-up"></i></div>
                    <p>Klik untuk pilih file atau seret ke sini</p>
                    <small>Surat dokter, surat izin, foto, dsb.</small>
                    <div id="file-name"></div>
                </label>
                @error('bukti_file')
                    <div style="color:#dc2626;font-size:.78rem;margin-top:4px;"><i class="fas fa-circle-exclamation"></i> {{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn-submit">
                <i class="fas fa-paper-plane"></i> Kirim Pengajuan
            </button>
        </form>
    </div>
    @else
    <div class="form-card" style="text-align:center;padding:32px;">
        <div style="font-size:2.5rem;color:#94a3b8;margin-bottom:12px;">
            <i class="fas fa-ban"></i>
        </div>
        <div style="font-weight:700;color:#475569;margin-bottom:6px;">Pengajuan Tidak Tersedia</div>
        <div style="font-size:.85rem;color:#94a3b8;">{{ $statusInfo[2] }}</div>
    </div>
    @endif

    {{-- Riwayat Pengajuan --}}
    <div class="riwayat-card">
        <h3>
            <i class="fas fa-clock-rotate-left" style="color:hsl(145,60%,28%)"></i>
            Riwayat Pengajuan Izin / Sakit
        </h3>

        @if($riwayat->isEmpty())
        <div style="text-align:center;padding:32px 0;color:#94a3b8;">
            <i class="fas fa-folder-open" style="font-size:2rem;margin-bottom:10px;display:block;"></i>
            Belum ada riwayat pengajuan izin/sakit.
        </div>
        @else
        @foreach($riwayat as $item)
        @php
            $approvalLabel = match($item->approval_status) {
                'menunggu'  => ['approval-menunggu',  'Menunggu'],
                'disetujui' => ['approval-disetujui', 'Disetujui'],
                'ditolak'   => ['approval-ditolak',   'Ditolak'],
                default     => ['approval-null',      '—'],
            };
        @endphp
        <div class="riwayat-item">
            <div class="riwayat-icon {{ $item->status }}">
                <i class="fas {{ $item->status === 'izin' ? 'fa-calendar-xmark' : 'fa-heart-pulse' }}"></i>
            </div>
            <div class="riwayat-info">
                <div class="riwayat-tanggal">
                    {{ \Carbon\Carbon::parse($item->tanggal)->isoFormat('dddd, D MMMM Y') }}
                    &nbsp;·&nbsp;
                    <span style="color:{{ $item->status === 'izin' ? '#1d4ed8' : '#7e22ce' }};font-weight:700;font-size:.8rem;">
                        {{ ucfirst($item->status) }}
                    </span>
                </div>
                <div class="riwayat-ket">{{ $item->keterangan ?: 'Tidak ada keterangan' }}</div>
            </div>
            <span class="approval-badge {{ $approvalLabel[0] }}">{{ $approvalLabel[1] }}</span>
        </div>
        @endforeach
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
function showFileName(input) {
    const el = document.getElementById('file-name');
    if (input.files && input.files[0]) {
        el.textContent = '✓ ' + input.files[0].name;
        el.style.display = 'block';
    } else {
        el.style.display = 'none';
    }
}
</script>
@endpush
