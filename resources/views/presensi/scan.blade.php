@extends('layouts.app')
@section('title','Scan QR Code')

@push('styles')
<style>
/* ================================================================
   HALAMAN SCAN — LAYOUT UTAMA
   ================================================================ */
.scan-wrap { max-width: 540px; margin: 0 auto; }

.scan-card {
    background: #fff;
    border-radius: 22px;
    box-shadow: 0 6px 32px rgba(0,80,0,.1);
    overflow: hidden;
    border: 1.5px solid #e2f0e2;
}

/* ---- Tab toggle ---- */
.scan-tabs {
    display: flex;
    border-bottom: 2px solid #f1f5f9;
    background: #fafcfa;
}
.scan-tab {
    flex: 1; padding: 15px 10px; text-align: center; font-size: .92rem;
    font-weight: 600; cursor: pointer; border: none; background: none;
    color: #94a3b8; transition: all .2s; font-family: 'Inter', sans-serif;
    border-bottom: 3px solid transparent; margin-bottom: -2px;
}
.scan-tab.active {
    color: hsl(145,60%,28%);
    border-bottom-color: hsl(145,60%,28%);
    background: #f0f9f0;
}
.scan-tab i { margin-right: 6px; }

/* ---- Area kamera ---- */
#reader-wrap { padding: 20px 20px 10px; }
#reader {
    width: 100% !important;
    border-radius: 16px;
    overflow: hidden;
    border: 3px dashed hsl(145,60%,70%);
    background: #f0f9f0;
    min-height: 220px;
}
#reader video { border-radius: 13px; }
#reader img { display: none; }

.camera-switch {
    display: flex; justify-content: center; gap: 8px; margin-top: 12px;
}
.cam-btn {
    padding: 8px 18px; border-radius: 9px; border: 1.5px solid #e2e8f0;
    background: #fff; font-size: .82rem; font-weight: 600; cursor: pointer;
    color: #64748b; font-family: 'Inter', sans-serif; transition: all .2s;
}
.cam-btn.active { background: #dcfce7; color: hsl(145,60%,28%); border-color: #86efac; }

/* ---- Area manual ---- */
#manual-wrap { padding: 24px 20px 10px; }
.manual-group { display: flex; gap: 10px; }
.manual-group input {
    flex: 1; padding: 13px 16px; border: 1.5px solid #e2e8f0; border-radius: 12px;
    font-size: 1rem; font-family: 'Inter', sans-serif; outline: none; color: #1e293b;
}
.manual-group input:focus { border-color: hsl(145,60%,28%); box-shadow: 0 0 0 3px hsl(145,60%,93%); }
.manual-group button {
    padding: 13px 20px; background: linear-gradient(135deg, hsl(145,60%,28%), hsl(145,60%,20%));
    color: #fff; border: none; border-radius: 12px; font-weight: 700;
    font-size: .9rem; cursor: pointer; font-family: 'Inter', sans-serif; white-space: nowrap;
}

/* ---- Hint ---- */
.scan-hint {
    background: #f0fdf4; border-radius: 10px; padding: 10px 14px;
    font-size: .82rem; color: hsl(145,60%,28%); margin: 12px 20px 16px;
    display: flex; align-items: center; gap: 8px;
    border: 1px solid #bbf7d0;
}

/* ---- Footer link ---- */
.scan-footer { padding: 0 20px 20px; }
.scan-footer a {
    display: flex; align-items: center; justify-content: center; gap: 8px;
    padding: 12px; border-radius: 11px; border: 1.5px solid #e2e8f0;
    color: #64748b; font-size: .875rem; font-weight: 600; text-decoration: none;
    transition: all .2s; background: #fafcfa;
}
.scan-footer a:hover { border-color: hsl(145,60%,60%); color: hsl(145,60%,28%); background: #f0fdf4; }

/* ---- Status bar mini ---- */
#scanStatus {
    display: none; margin: 0 20px 16px; padding: 11px 16px; border-radius: 10px;
    font-size: .88rem; font-weight: 600; align-items: center; gap: 10px;
}
#scanStatus.show { display: flex; }

/* ================================================================
   POPUP / MODAL OVERLAY
   ================================================================ */
.scan-modal-overlay {
    position: fixed; inset: 0;
    background: rgba(0, 0, 0, 0.55);
    backdrop-filter: blur(6px);
    -webkit-backdrop-filter: blur(6px);
    z-index: 9000;
    display: flex; align-items: center; justify-content: center;
    padding: 20px;
    opacity: 0;
    pointer-events: none;
    transition: opacity .3s ease;
}
.scan-modal-overlay.show {
    opacity: 1;
    pointer-events: auto;
}

.scan-modal {
    background: #fff;
    border-radius: 28px;
    padding: 36px 32px 28px;
    width: 100%;
    max-width: 380px;
    box-shadow: 0 24px 80px rgba(0,0,0,.22);
    transform: scale(.88) translateY(20px);
    transition: transform .35s cubic-bezier(.34,1.56,.64,1), opacity .3s ease;
    opacity: 0;
    position: relative;
    text-align: center;
}
.scan-modal-overlay.show .scan-modal {
    transform: scale(1) translateY(0);
    opacity: 1;
}

/* ---- Icon lingkaran ---- */
.modal-icon-ring {
    width: 80px; height: 80px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 2rem;
    margin: 0 auto 20px;
    position: relative;
}
.modal-icon-ring.success {
    background: linear-gradient(135deg, #dcfce7, #bbf7d0);
    color: hsl(145,60%,28%);
    box-shadow: 0 0 0 8px rgba(34,197,94,.12), 0 0 0 16px rgba(34,197,94,.06);
    animation: pulse-success 2s infinite;
}
.modal-icon-ring.warn {
    background: linear-gradient(135deg, #fef9c3, #fde68a);
    color: #b45309;
    box-shadow: 0 0 0 8px rgba(245,158,11,.12), 0 0 0 16px rgba(245,158,11,.06);
}
.modal-icon-ring.error {
    background: linear-gradient(135deg, #fee2e2, #fecaca);
    color: #dc2626;
    box-shadow: 0 0 0 8px rgba(239,68,68,.12), 0 0 0 16px rgba(239,68,68,.06);
}
@keyframes pulse-success {
    0%, 100% { box-shadow: 0 0 0 8px rgba(34,197,94,.12), 0 0 0 16px rgba(34,197,94,.06); }
    50%       { box-shadow: 0 0 0 12px rgba(34,197,94,.16), 0 0 0 22px rgba(34,197,94,.08); }
}

/* ---- Nama & info ---- */
.modal-guru-name {
    font-size: 1.25rem; font-weight: 800; color: #1e293b;
    margin-bottom: 4px; line-height: 1.3;
}
.modal-guru-nip {
    font-size: .85rem; color: #64748b; font-weight: 500; margin-bottom: 18px;
}

/* ---- Badge status ---- */
.modal-status-badge {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 8px 22px; border-radius: 99px;
    font-size: .95rem; font-weight: 800; letter-spacing: .04em;
    text-transform: uppercase; margin-bottom: 20px;
}
.modal-status-badge.hadir  { background: #dcfce7; color: #15803d; }
.modal-status-badge.telat  { background: #fef9c3; color: #a16207; }
.modal-status-badge.pulang { background: #dbeafe; color: #1d4ed8; }
.modal-status-badge.error  { background: #fee2e2; color: #dc2626; }

/* ---- Detail baris ---- */
.modal-details {
    background: #f8fafc; border-radius: 14px;
    padding: 14px 16px; margin-bottom: 20px; text-align: left;
}
.modal-detail-row {
    display: flex; justify-content: space-between; align-items: center;
    padding: 7px 0; border-bottom: 1px solid #f1f5f9; font-size: .88rem;
}
.modal-detail-row:last-child { border-bottom: none; }
.modal-detail-label { color: #64748b; font-weight: 500; }
.modal-detail-value { color: #1e293b; font-weight: 700; }
.modal-detail-value.jam { font-size: 1.05rem; color: hsl(145,60%,28%); }

/* ---- Progress bar auto-close ---- */
.modal-progress-bar {
    height: 4px; border-radius: 2px;
    background: #e2e8f0; margin-bottom: 16px; overflow: hidden;
}
.modal-progress-fill {
    height: 100%; border-radius: 2px;
    background: linear-gradient(90deg, hsl(145,60%,28%), hsl(145,60%,50%));
    width: 100%;
    transition: width linear;
}
.modal-progress-fill.error-fill {
    background: linear-gradient(90deg, #dc2626, #ef4444);
}
.modal-progress-fill.warn-fill {
    background: linear-gradient(90deg, #d97706, #f59e0b);
}

/* ---- Tombol tutup ---- */
.modal-close-btn {
    width: 100%; padding: 12px; border-radius: 12px; border: none;
    background: #f1f5f9; color: #475569; font-size: .9rem; font-weight: 600;
    cursor: pointer; font-family: 'Inter', sans-serif; transition: all .2s;
}
.modal-close-btn:hover { background: #e2e8f0; color: #1e293b; }

/* ---- Tipe masuk/pulang label ---- */
.modal-tipe-chip {
    display: inline-block; padding: 4px 12px; border-radius: 6px;
    font-size: .78rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: .05em; margin-bottom: 14px;
}
.modal-tipe-chip.masuk  { background: #dcfce7; color: hsl(145,60%,25%); }
.modal-tipe-chip.pulang { background: #dbeafe; color: #1d4ed8; }

/* ================================================================
   MOBILE
   ================================================================ */
@media (max-width: 640px) {
    .scan-wrap { max-width: 100%; }
    .scan-card { border-radius: 18px; }
    .scan-modal { padding: 28px 22px 22px; border-radius: 24px; max-width: 360px; }
    .modal-guru-name { font-size: 1.1rem; }
    .modal-icon-ring { width: 68px; height: 68px; font-size: 1.7rem; }
}
</style>
@endpush

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fas fa-qrcode"></i> Scan QR Code Presensi</h1>
    <p style="color:#64748b;font-size:.875rem;margin-top:4px;">Gunakan kamera HP atau scanner untuk absensi guru</p>
</div>

<div class="scan-wrap">
<div class="scan-card">

    <!-- Tab -->
    <div class="scan-tabs">
        <button class="scan-tab active" onclick="switchTab('kamera',this)">
            <i class="fas fa-camera"></i> Kamera HP
        </button>
        <button class="scan-tab" onclick="switchTab('manual',this)">
            <i class="fas fa-keyboard"></i> Manual / Scanner
        </button>
    </div>

    <!-- Mode Kamera -->
    <div id="kamera-section">
        <div id="reader-wrap">
            <div id="reader"></div>
            <div class="camera-switch">
                <button class="cam-btn active" id="btnBelakang" onclick="gantiKamera('belakang')">
                    <i class="fas fa-camera-rotate"></i> Kamera Belakang
                </button>
                <button class="cam-btn" id="btnDepan" onclick="gantiKamera('depan')">
                    <i class="fas fa-user"></i> Kamera Depan
                </button>
            </div>
        </div>
        <div class="scan-hint">
            <i class="fas fa-circle-info"></i>
            Arahkan kamera ke QR Code guru hingga terbaca otomatis
        </div>
    </div>

    <!-- Mode Manual -->
    <div id="manual-section" style="display:none;">
        <div id="manual-wrap">
            <div class="manual-group">
                <input type="text" id="barcodeInput" placeholder="Scan atau ketik QR Code..." autofocus>
                <button onclick="prosesBarcode(document.getElementById('barcodeInput').value)">
                    <i class="fas fa-check"></i> Proses
                </button>
            </div>
        </div>
        <div class="scan-hint">
            <i class="fas fa-lightbulb"></i>
            Hubungkan QR Code scanner USB → klik kolom input → scan
        </div>
    </div>

    <!-- Status bar kecil -->
    <div id="scanStatus" class="show" style="background:#f0f9f0;color:hsl(145,60%,28%);display:flex;margin-top:0;">
        <i class="fas fa-satellite-dish fa-beat-fade" style="color:hsl(145,60%,45%)"></i>
        Siap memindai QR Code...
    </div>

    <!-- Footer -->
    <div class="scan-footer" style="margin-top:4px;">
        <a href="{{ route('presensi.index') }}">
            <i class="fas fa-list-check"></i> Lihat Data Presensi Hari Ini
        </a>
    </div>

</div>
</div>

{{-- ================================================================
     POPUP MODAL HASIL SCAN
     ================================================================ --}}
<div class="scan-modal-overlay" id="scanModal" onclick="handleOverlayClick(event)">
    <div class="scan-modal" id="scanModalBox">

        <!-- Icon -->
        <div class="modal-icon-ring" id="modalIconRing">
            <i class="fas fa-check" id="modalIcon"></i>
        </div>

        <!-- Error message (hanya saat error) -->
        <div id="modalErrorMsg" style="display:none;">
            <div class="modal-guru-name" id="modalErrorText" style="color:#dc2626;font-size:1rem;margin-bottom:24px;"></div>
        </div>

        <!-- Data guru (hanya saat sukses) -->
        <div id="modalSuccessContent" style="display:none;">
            <div class="modal-tipe-chip" id="modalTipeChip"></div>
            <div class="modal-guru-name" id="modalNama"></div>
            <div class="modal-guru-nip" id="modalNip"></div>
            <div class="modal-status-badge" id="modalStatusBadge">
                <i class="fas fa-circle-check"></i>
                <span id="modalStatusText">HADIR</span>
            </div>
            <div class="modal-details">
                <div class="modal-detail-row">
                    <span class="modal-detail-label"><i class="fas fa-calendar-day" style="margin-right:6px;color:#94a3b8;"></i>Tanggal</span>
                    <span class="modal-detail-value" id="modalTanggal"></span>
                </div>
                <div class="modal-detail-row">
                    <span class="modal-detail-label"><i class="fas fa-clock" style="margin-right:6px;color:#94a3b8;"></i>Jam Scan</span>
                    <span class="modal-detail-value jam" id="modalJam"></span>
                </div>
                <div class="modal-detail-row" id="modalKetRow" style="display:none;">
                    <span class="modal-detail-label"><i class="fas fa-note-sticky" style="margin-right:6px;color:#94a3b8;"></i>Keterangan</span>
                    <span class="modal-detail-value" id="modalKet"></span>
                </div>
            </div>
        </div>

        <!-- Progress auto-close -->
        <div class="modal-progress-bar">
            <div class="modal-progress-fill" id="modalProgressFill"></div>
        </div>

        <!-- Tombol tutup -->
        <button class="modal-close-btn" onclick="closeModal()">
            <i class="fas fa-xmark"></i> Tutup
        </button>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
let html5QrCode   = null;
let scannerAktif  = false;
let modeFacingUser= false;
let lastScan      = '';
let lastScanTime  = 0;
let autoCloseTimer= null;
let progressTimer = null;

// ================================================================
// TAB SWITCH
// ================================================================
function switchTab(tab, el) {
    document.querySelectorAll('.scan-tab').forEach(t => t.classList.remove('active'));
    el.classList.add('active');
    document.getElementById('kamera-section').style.display  = tab === 'kamera' ? '' : 'none';
    document.getElementById('manual-section').style.display  = tab === 'manual' ? '' : 'none';
    if (tab === 'kamera') {
        startScanner();
        document.getElementById('barcodeInput').value = '';
    } else {
        stopScanner();
        setTimeout(() => document.getElementById('barcodeInput').focus(), 100);
    }
}

// ================================================================
// KAMERA
// ================================================================
async function startScanner() {
    if (scannerAktif) return;
    if (!html5QrCode) html5QrCode = new Html5Qrcode("reader", { verbose: false });
    const facingMode = modeFacingUser ? "user" : "environment";
    const config = { fps: 10, qrbox: { width: 250, height: 160 }, aspectRatio: 1.3 };
    try {
        await html5QrCode.start({ facingMode }, config, onScanSuccess, () => {});
        scannerAktif = true;
    } catch(err) {
        setStatusBar('warning', 'Kamera tidak tersedia. Gunakan mode Manual.');
    }
}

async function stopScanner() {
    if (html5QrCode && scannerAktif) {
        try { await html5QrCode.stop(); } catch(e) {}
        scannerAktif = false;
    }
}

async function gantiKamera(mode) {
    modeFacingUser = (mode === 'depan');
    document.getElementById('btnDepan').classList.toggle('active', modeFacingUser);
    document.getElementById('btnBelakang').classList.toggle('active', !modeFacingUser);
    await stopScanner();
    html5QrCode = null;
    document.getElementById('reader').innerHTML = '';
    setTimeout(startScanner, 300);
}

function onScanSuccess(decodedText) {
    const now = Date.now();
    if (decodedText === lastScan && now - lastScanTime < 3000) return;
    lastScan      = decodedText;
    lastScanTime  = now;
    prosesBarcode(decodedText);
}

// ================================================================
// INPUT MANUAL
// ================================================================
document.addEventListener('DOMContentLoaded', () => {
    const inp = document.getElementById('barcodeInput');
    if (inp) inp.addEventListener('keydown', e => { if (e.key === 'Enter') prosesBarcode(inp.value); });
    startScanner();
});

// ================================================================
// PROSES BARCODE → KIRIM KE SERVER
// ================================================================
async function prosesBarcode(barcode) {
    barcode = (barcode || '').trim();
    if (!barcode) return;

    setStatusBar('loading', 'Memproses...');

    try {
        const res = await fetch('{{ route("presensi.barcode") }}', {
            method : 'POST',
            headers: {
                'Content-Type' : 'application/json',
                'X-CSRF-TOKEN' : document.querySelector('meta[name=csrf-token]').content
            },
            body: JSON.stringify({ barcode })
        });
        const data = await res.json();

        if (data.success) {
            const isTelat = data.status === 'telat';
            setStatusBar('success', `<i class="fas fa-circle-check"></i> ${data.message}`);
            showSuccessModal(data, isTelat);
            vibrateDevice([80]);
            playBeep(isTelat ? 'warn' : 'ok');
        } else {
            setStatusBar('error', `<i class="fas fa-times-circle"></i> ${data.message}`);
            showErrorModal(data.message);
            vibrateDevice([80, 40, 80]);
            playBeep('err');
        }
    } catch(e) {
        setStatusBar('error', '<i class="fas fa-times-circle"></i> Gagal terhubung ke server.');
        showErrorModal('Gagal terhubung ke server. Periksa koneksi Anda.');
        playBeep('err');
    }

    const inp = document.getElementById('barcodeInput');
    if (inp) { inp.value = ''; inp.focus(); }
}

// ================================================================
// MODAL — SUKSES
// ================================================================
function showSuccessModal(data, isTelat) {
    const isHadir  = !isTelat;
    const isPulang = data.tipe === 'pulang';

    // Icon ring
    const ring = document.getElementById('modalIconRing');
    ring.className = 'modal-icon-ring ' + (isTelat ? 'warn' : 'success');
    const icon = document.getElementById('modalIcon');
    icon.className = isPulang ? 'fas fa-sign-out-alt' : 'fas fa-check';

    // Tipe chip
    const chip = document.getElementById('modalTipeChip');
    chip.className  = 'modal-tipe-chip ' + (isPulang ? 'pulang' : 'masuk');
    chip.innerHTML  = isPulang
        ? '<i class="fas fa-sign-out-alt"></i> Scan Pulang'
        : '<i class="fas fa-sign-in-alt"></i>  Scan Masuk';

    // Nama & NIP
    document.getElementById('modalNama').textContent = data.nama;
    document.getElementById('modalNip').textContent  = 'ID: ' + (data.id || '-');

    // Badge status
    const badge = document.getElementById('modalStatusBadge');
    if (isPulang) {
        badge.className = 'modal-status-badge pulang';
        badge.innerHTML = '<i class="fas fa-door-open"></i> <span>PULANG</span>';
    } else if (isTelat) {
        badge.className = 'modal-status-badge telat';
        badge.innerHTML = '<i class="fas fa-clock"></i> <span>TERLAMBAT '+(data.menit_telat||0)+' MENIT</span>';
    } else {
        badge.className = 'modal-status-badge hadir';
        badge.innerHTML = '<i class="fas fa-circle-check"></i> <span>HADIR</span>';
    }

    // Detail: tanggal & jam
    const now = new Date();
    const tgl = now.toLocaleDateString('id-ID', { weekday:'long', day:'numeric', month:'long', year:'numeric' });
    document.getElementById('modalTanggal').textContent = tgl;
    document.getElementById('modalJam').textContent     = data.jam;

    // Keterangan (sembunyikan jika kosong)
    const ketRow = document.getElementById('modalKetRow');
    if (data.keterangan) {
        document.getElementById('modalKet').textContent = data.keterangan;
        ketRow.style.display = 'flex';
    } else {
        ketRow.style.display = 'none';
    }

    document.getElementById('modalErrorMsg').style.display      = 'none';
    document.getElementById('modalSuccessContent').style.display = 'block';

    // Progress bar fill type
    const fill = document.getElementById('modalProgressFill');
    fill.className = 'modal-progress-fill' + (isTelat ? ' warn-fill' : '');

    openModal(5000);
}

// ================================================================
// MODAL — ERROR
// ================================================================
function showErrorModal(msg) {
    const ring = document.getElementById('modalIconRing');
    ring.className = 'modal-icon-ring error';
    document.getElementById('modalIcon').className = 'fas fa-times';

    document.getElementById('modalErrorText').textContent      = msg;
    document.getElementById('modalErrorMsg').style.display     = 'block';
    document.getElementById('modalSuccessContent').style.display = 'none';

    const fill = document.getElementById('modalProgressFill');
    fill.className = 'modal-progress-fill error-fill';

    openModal(4000);
}

// ================================================================
// MODAL — OPEN / CLOSE
// ================================================================
function openModal(autoCloseMs) {
    clearTimeout(autoCloseTimer);
    clearInterval(progressTimer);

    const overlay = document.getElementById('scanModal');
    const fill    = document.getElementById('modalProgressFill');

    // Reset progress
    fill.style.transition = 'none';
    fill.style.width = '100%';

    overlay.classList.add('show');

    // Mulai progress bar
    requestAnimationFrame(() => {
        requestAnimationFrame(() => {
            fill.style.transition = `width ${autoCloseMs}ms linear`;
            fill.style.width = '0%';
        });
    });

    // Auto close
    autoCloseTimer = setTimeout(closeModal, autoCloseMs);
}

function closeModal() {
    clearTimeout(autoCloseTimer);
    document.getElementById('scanModal').classList.remove('show');
}

function handleOverlayClick(e) {
    if (e.target === document.getElementById('scanModal')) closeModal();
}

// ================================================================
// STATUS BAR KECIL
// ================================================================
function setStatusBar(type, msg) {
    const el = document.getElementById('scanStatus');
    el.style.display = 'flex';
    el.className = 'show';
    if (type === 'loading') {
        el.style.background = '#dbeafe'; el.style.color = '#1d4ed8';
        el.innerHTML = '<i class="fas fa-spinner fa-spin"></i> ' + msg;
    } else if (type === 'error') {
        el.style.background = '#fee2e2'; el.style.color = '#dc2626';
        el.innerHTML = msg;
    } else if (type === 'success') {
        el.style.background = '#dcfce7'; el.style.color = '#15803d';
        el.innerHTML = msg;
    } else if (type === 'warning') {
        el.style.background = '#fef9c3'; el.style.color = '#92400e';
        el.innerHTML = '<i class="fas fa-triangle-exclamation"></i> ' + msg;
    }
}

// ================================================================
// HELPERS
// ================================================================
function vibrateDevice(pattern) {
    if (navigator.vibrate) navigator.vibrate(pattern);
}

function playBeep(type) {
    try {
        const ctx  = new (window.AudioContext || window.webkitAudioContext)();
        const osc  = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.connect(gain);
        gain.connect(ctx.destination);

        if (type === 'ok') {
            // Dua nada naik — sukses
            osc.type = 'sine';
            osc.frequency.setValueAtTime(660, ctx.currentTime);
            osc.frequency.setValueAtTime(880, ctx.currentTime + 0.12);
            gain.gain.setValueAtTime(0.35, ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.5);
            osc.start(ctx.currentTime);
            osc.stop(ctx.currentTime + 0.5);
        } else if (type === 'warn') {
            // Satu nada sedang — telat
            osc.type = 'sine';
            osc.frequency.value = 580;
            gain.gain.setValueAtTime(0.3, ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.45);
            osc.start(ctx.currentTime);
            osc.stop(ctx.currentTime + 0.45);
        } else {
            // Dua nada turun — error
            osc.type = 'square';
            osc.frequency.setValueAtTime(330, ctx.currentTime);
            osc.frequency.setValueAtTime(200, ctx.currentTime + 0.15);
            gain.gain.setValueAtTime(0.2, ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.45);
            osc.start(ctx.currentTime);
            osc.stop(ctx.currentTime + 0.45);
        }
    } catch(e) {}
}
</script>
@endpush
