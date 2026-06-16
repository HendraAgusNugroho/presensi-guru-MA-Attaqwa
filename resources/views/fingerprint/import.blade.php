@extends('layouts.app')
@section('title','Import Fingerprint')

@push('styles')
<style>
.step-list { counter-reset: step; }
.step-item { display:flex; gap:12px; margin-bottom:12px; align-items:flex-start; }
.step-num {
    width:28px; height:28px; border-radius:50%; flex-shrink:0;
    background:hsl(145,60%,28%); color:#fff; font-weight:800; font-size:.82rem;
    display:flex; align-items:center; justify-content:center;
}
.step-text { font-size:.88rem; color:#5a7a5a; padding-top:4px; line-height:1.5; }
.step-text strong { color:#1a2e1a; }

#drop-area {
    border:2px dashed #a0c4a0; border-radius:12px; padding:36px 24px;
    text-align:center; cursor:pointer; transition:all .2s; background:#f8fdf8;
    position:relative;
}
#drop-area:hover, #drop-area.dragover {
    border-color:hsl(145,60%,28%); background:hsl(145,60%,97%);
}
#drop-area i { font-size:2.5rem; color:hsl(145,60%,35%); margin-bottom:12px; display:block; }
#drop-area p { color:#5a7a5a; margin-bottom:6px; }
#drop-area strong { color:hsl(145,60%,28%); }
#file-input { display:none; }
#file-chosen {
    display:none; margin-top:12px; padding:10px 16px;
    background:#d4e8d4; border-radius:8px; font-size:.88rem; color:#1a2e1a;
    display:none; align-items:center; gap:8px;
}

.preview-table-wrap { max-height:280px; overflow-y:auto; border:1px solid #d4e8d4; border-radius:8px; margin-top:12px; }
</style>
@endpush

@section('content')

<div class="page-header">
    <h1><i class="fas fa-fingerprint" style="color:hsl(145,60%,28%)"></i> Import Data Fingerprint</h1>
    <p>Upload file Excel/CSV dari mesin fingerprint untuk sinkronisasi otomatis ke database presensi</p>
</div>

@if(session('import_result'))
    @php $result = session('import_result'); @endphp
    <div class="alert {{ $result['berhasil'] > 0 ? 'alert-success' : 'alert-danger' }}" style="margin-bottom:18px">
        <i class="fas {{ $result['berhasil'] > 0 ? 'fa-circle-check' : 'fa-triangle-exclamation' }} fa-lg"></i>
        <div>
            <strong>Hasil Import Fingerprint:</strong><br>
            <span style="font-size:.9rem">
                ✅ Berhasil diproses: <strong>{{ $result['berhasil'] }}</strong> data &nbsp;|&nbsp;
                ❌ Gagal: <strong>{{ $result['gagal'] }}</strong> data &nbsp;|&nbsp;
                ⚠️ Tidak ditemukan guru: <strong>{{ $result['tidak_ditemukan'] }}</strong>
            </span>
        </div>
    </div>
    @if(!empty($result['errors']))
    <div class="card" style="border-left:4px solid #f59e0b;margin-bottom:18px">
        <div class="card-header" style="margin-bottom:10px;padding-bottom:8px">
            <h3 style="color:#92400e"><i class="fas fa-exclamation-triangle"></i> Log Error Detail</h3>
            <span style="font-size:.8rem;color:#5a7a5a">{{ count($result['errors']) }} error</span>
        </div>
        <div style="max-height:200px;overflow-y:auto;">
            @foreach($result['errors'] as $err)
                <div style="font-size:.82rem;color:#92400e;padding:4px 0;border-bottom:1px solid #fef9c3;display:flex;gap:8px;">
                    <i class="fas fa-circle-xmark" style="color:#f59e0b;margin-top:2px;flex-shrink:0"></i>
                    <span>{{ $err }}</span>
                </div>
            @endforeach
        </div>
    </div>
    @endif
@endif

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">

    <!-- Upload Card -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-file-import" style="color:hsl(145,60%,28%)"></i> Upload File</h3>
        </div>

        <form method="POST" action="{{ route('fingerprint.proses') }}" enctype="multipart/form-data"
              id="importForm" data-loading>
            @csrf

            <!-- Dropzone -->
            <div id="drop-area" onclick="document.getElementById('file-input').click()">
                <input type="file" name="file" id="file-input" accept=".xlsx,.xls,.csv"
                       onchange="handleFileSelect(this)">
                <i class="fas fa-cloud-arrow-up"></i>
                <p>Klik atau <strong>drag & drop</strong> file ke sini</p>
                <p style="font-size:.8rem;color:#94a3b8">Format: .xlsx, .xls, .csv &nbsp;|&nbsp; Maks. 5MB</p>
            </div>

            <div id="file-chosen" style="display:none;margin-top:12px;padding:10px 16px;
                background:#d4e8d4;border-radius:8px;font-size:.88rem;color:#1a2e1a;
                align-items:center;gap:8px;">
                <i class="fas fa-file-excel" style="color:#22c55e"></i>
                <span id="file-name">—</span>
                <button type="button" onclick="clearFile()"
                    style="margin-left:auto;background:none;border:none;color:#dc2626;cursor:pointer;font-size:.85rem;">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            @error('file')
                <div class="alert alert-danger" style="margin-top:10px;padding:8px 12px;font-size:.85rem">
                    <i class="fas fa-triangle-exclamation"></i> {{ $message }}
                </div>
            @enderror

            <!-- Preview area (filled by JS) -->
            <div id="preview-wrap" style="display:none;margin-top:12px">
                <div style="font-size:.82rem;font-weight:700;color:#1a2e1a;margin-bottom:6px;">
                    <i class="fas fa-eye" style="color:hsl(145,60%,28%)"></i> Preview Data (5 baris pertama):
                </div>
                <div class="preview-table-wrap">
                    <table id="preview-table" style="font-size:.8rem"></table>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" id="submitBtn"
                    style="width:100%;justify-content:center;margin-top:16px" disabled>
                <i class="fas fa-upload"></i> Upload & Proses ke Database
            </button>
        </form>

        <div style="margin-top:14px">
            <a href="{{ route('fingerprint.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-list"></i> Lihat Log Fingerprint
            </a>
        </div>
    </div>

    <!-- Info & API Card -->
    <div style="display:flex;flex-direction:column;gap:16px">

        <!-- Panduan -->
        <div class="card">
            <div class="card-header" style="margin-bottom:12px;padding-bottom:10px">
                <h3><i class="fas fa-circle-info" style="color:hsl(145,60%,28%)"></i> Format & Panduan</h3>
            </div>
            <div class="step-list">
                <div class="step-item">
                    <div class="step-num">1</div>
                    <div class="step-text">Pastikan file berformat <strong>.xlsx, .xls, atau .csv</strong> dari mesin fingerprint</div>
                </div>
                <div class="step-item">
                    <div class="step-num">2</div>
                    <div class="step-text">File Excel harus dalam format <strong>horizontal per blok karyawan</strong> dengan kolom tanggal 1-31</div>
                </div>
                <div class="step-item">
                    <div class="step-num">3</div>
                    <div class="step-text">Sistem akan <strong>otomatis mendeteksi Nama Guru</strong> dan mencocockkannya dengan database guru (case-insensitive)</div>
                </div>
                <div class="step-item">
                    <div class="step-num">4</div>
                    <div class="step-text">Format jam dalam sel: <strong>Jam Masuk</strong> dan <strong>Jam Pulang</strong> dipisahkan dengan baris baru (Enter)</div>
                </div>
                <div class="step-item">
                    <div class="step-num">5</div>
                    <div class="step-text">Waktu masuk sebelum <strong>batas toleransi jadwal</strong> = HADIR. Setelah batas = TERLAMBAT</div>
                </div>
            </div>

            <!-- Contoh format -->
            <div style="margin-top:12px">
                <div style="font-size:.8rem;font-weight:700;margin-bottom:6px;color:#1a2e1a">Contoh format file Excel dari mesin fingerprint:</div>
                <div class="code-block">
                    <span class="comment">User ID.： | Nama： | 1 | 2 | 3 | 4 | 5 | ... | 31</span><br>
                    <span class="val">1 | FITHRIYANI SHOFWA | | 07:00 | | 06:45 | 07:10 |</span><br>
                    <span class="val">2 | HASBULLAH,S.Ag | | 06:58 | | 06:40 | 07:05 | 07:47</span>
                </div>
                <div style="margin-top:8px;font-size:.78rem;color:#5a7a5a">
                    <i class="fas fa-lightbulb" style="color:#f59e0b"></i> <strong>Format jam dalam sel:</strong><br>
                    - Satu jam: <code>07:00</code> (hanya jam masuk)<br>
                    - Dua jam: <code>07:00</code> (baris baru) <code>15:00</code> (jam masuk dan pulang)<br>
                    - Nama Guru harus SAMA dengan nama di database guru (case-insensitive)
                </div>
            </div>
        </div>

        <!-- API Card -->
        <div class="card">
            <div class="card-header" style="margin-bottom:12px;padding-bottom:10px">
                <h3><i class="fas fa-plug" style="color:#22c55e"></i> Sinkronisasi via API</h3>
            </div>
            <p style="font-size:.85rem;color:#5a7a5a;margin-bottom:12px">
                Jika mesin fingerprint mendukung HTTP, gunakan endpoint berikut untuk sinkronisasi real-time:
            </p>
            <div class="code-block">
                <div><span class="comment">// POST Request</span></div>
                <div><span class="key">POST</span> <span class="val">/api/fingerprint/sync</span></div>
                <div style="margin-top:8px"><span class="comment">// Body JSON:</span></div>
                <div>{</div>
                <div>&nbsp;&nbsp;<span class="key">"id_fingerprint"</span>: <span class="val">"FP001"</span>,</div>
                <div>&nbsp;&nbsp;<span class="key">"waktu_scan"</span>: <span class="val">"2025-01-15 07:05:30"</span></div>
                <div>}</div>
            </div>
            <div class="alert alert-success" style="margin-top:12px;padding:8px 12px;font-size:.82rem">
                <i class="fas fa-check-circle"></i>
                Sistem otomatis mencocokkan ID fingerprint dengan NIP guru dan mencatat presensi.
            </div>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
// Drag & drop
const dropArea = document.getElementById('drop-area');
['dragenter','dragover'].forEach(e => dropArea.addEventListener(e, ev => {
    ev.preventDefault(); dropArea.classList.add('dragover');
}));
['dragleave','drop'].forEach(e => dropArea.addEventListener(e, ev => {
    ev.preventDefault(); dropArea.classList.remove('dragover');
}));
dropArea.addEventListener('drop', ev => {
    const file = ev.dataTransfer.files[0];
    if (file) {
        document.getElementById('file-input').files = ev.dataTransfer.files;
        showFile(file);
    }
});

function handleFileSelect(input) {
    if (input.files && input.files[0]) showFile(input.files[0]);
}

function showFile(file) {
    const allowed = ['xlsx','xls','csv'];
    const ext = file.name.split('.').pop().toLowerCase();
    if (!allowed.includes(ext)) {
        alert('Format file tidak didukung. Gunakan .xlsx, .xls, atau .csv');
        clearFile(); return;
    }
    document.getElementById('file-name').textContent = file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)';
    document.getElementById('file-chosen').style.display = 'flex';
    document.getElementById('submitBtn').disabled = false;

    // CSV preview
    if (ext === 'csv') {
        const reader = new FileReader();
        reader.onload = function(e) {
            const lines = e.target.result.split('\n').slice(0, 6).filter(Boolean);
            let html = '<thead><tr>' + lines[0].split(',').map(h => '<th style="padding:6px 10px;background:hsl(145,60%,18%);color:#fff;font-size:.78rem">' + h.trim() + '</th>').join('') + '</tr></thead><tbody>';
            lines.slice(1).forEach((line, i) => {
                html += '<tr>' + line.split(',').map(c => '<td style="padding:5px 10px;font-size:.8rem;border-bottom:1px solid #d4e8d4">' + c.trim() + '</td>').join('') + '</tr>';
            });
            html += '</tbody>';
            document.getElementById('preview-table').innerHTML = html;
            document.getElementById('preview-wrap').style.display = 'block';
        };
        reader.readAsText(file);
    }
}

function clearFile() {
    document.getElementById('file-input').value = '';
    document.getElementById('file-chosen').style.display = 'none';
    document.getElementById('submitBtn').disabled = true;
    document.getElementById('preview-wrap').style.display = 'none';
}
</script>
@endpush
