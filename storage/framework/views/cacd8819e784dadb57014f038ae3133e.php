<?php $__env->startSection('title','QR Code Saya'); ?>

<?php $__env->startPush('styles'); ?>
<style>
body { background: #f8fafc; }

.barcode-page {
    max-width: 420px;
    margin: 0 auto;
    text-align: center;
}

/* Kartu identitas guru */
.id-card {
    background: linear-gradient(135deg, #4f46e5, #7c3aed);
    border-radius: 20px;
    padding: 24px 20px 20px;
    color: #fff;
    margin-bottom: 16px;
    position: relative;
    overflow: hidden;
}
.id-card::before {
    content: '';
    position: absolute;
    top: -30px; right: -30px;
    width: 120px; height: 120px;
    border-radius: 50%;
    background: rgba(255,255,255,.08);
}
.id-card::after {
    content: '';
    position: absolute;
    bottom: -20px; left: -20px;
    width: 90px; height: 90px;
    border-radius: 50%;
    background: rgba(255,255,255,.06);
}
.id-avatar {
    width: 64px; height: 64px;
    background: rgba(255,255,255,.15);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.8rem;
    margin: 0 auto 14px;
    border: 3px solid rgba(255,255,255,.3);
}
.id-nama {
    font-size: 1.05rem;
    font-weight: 800;
    margin-bottom: 4px;
    position: relative; z-index: 1;
}
.id-nip {
    font-size: .8rem;
    opacity: .8;
    margin-bottom: 6px;
    position: relative; z-index: 1;
}
.id-jabatan {
    font-size: .75rem;
    background: rgba(255,255,255,.15);
    padding: 3px 10px;
    border-radius: 6px;
    display: inline-block;
    position: relative; z-index: 1;
}

/* Kotak QR Code */
.barcode-box {
    background: #fff;
    border-radius: 20px;
    padding: 28px 20px 20px;
    box-shadow: 0 4px 24px rgba(0,0,0,.08);
    margin-bottom: 16px;
}
.barcode-label {
    font-size: .78rem;
    color: #94a3b8;
    font-weight: 600;
    letter-spacing: .05em;
    text-transform: uppercase;
    margin-bottom: 16px;
}
#qrCanvas {
    display: inline-block;
}
#qrCanvas canvas,
#qrCanvas img {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
}
.barcode-text {
    font-family: 'Courier New', monospace;
    font-size: .85rem;
    color: #64748b;
    margin-top: 12px;
    letter-spacing: .1em;
    font-weight: 700;
}

/* Tombol layar penuh */
.btn-fullscreen {
    width: 100%;
    padding: 14px;
    background: linear-gradient(135deg, #4f46e5, #7c3aed);
    color: #fff;
    border: none;
    border-radius: 14px;
    font-size: 1rem;
    font-weight: 700;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    font-family: 'Inter', sans-serif;
    margin-bottom: 10px;
    transition: opacity .2s;
}
.btn-fullscreen:hover { opacity: .9; }

.btn-brightness {
    width: 100%;
    padding: 12px;
    background: #fef9c3;
    color: #a16207;
    border: 1.5px solid #fde68a;
    border-radius: 12px;
    font-size: .875rem;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    font-family: 'Inter', sans-serif;
    transition: all .2s;
    margin-bottom: 10px;
}

/* Tombol Download QR Code */
.btn-print {
    width: 100%;
    padding: 12px;
    background: #f0fdf4;
    color: #15803d;
    border: 1.5px solid #bbf7d0;
    border-radius: 12px;
    font-size: .875rem;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    font-family: 'Inter', sans-serif;
    transition: all .2s;
    margin-bottom: 10px;
}
.btn-print:hover { background: #dcfce7; border-color: #86efac; }
.btn-print:disabled { opacity: .6; cursor: wait; }

.tip-box {
    background: #f0fdf4;
    border: 1.5px solid #bbf7d0;
    border-radius: 12px;
    padding: 14px 16px;
    font-size: .82rem;
    color: #15803d;
    text-align: left;
    margin-top: 4px;
}
.tip-box ol { margin: 8px 0 0 16px; line-height: 1.8; }

/* ===== FULLSCREEN MODE ===== */
.fullscreen-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: #fff;
    z-index: 9999;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 24px;
}
.fullscreen-overlay.show { display: flex; }
.fullscreen-overlay #qrFull canvas,
.fullscreen-overlay #qrFull img {
    width: 300px !important;
    height: 300px !important;
    max-width: 90vw;
    max-height: 90vw;
}
.btn-close-fs {
    position: fixed;
    top: 16px; right: 16px;
    width: 44px; height: 44px;
    background: #f1f5f9;
    border: none;
    border-radius: 50%;
    font-size: 1.1rem;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    z-index: 10000;
}
.fs-nama {
    margin-top: 20px;
    font-size: 1rem;
    font-weight: 700;
    color: #1e293b;
    text-align: center;
}
.fs-nip {
    font-size: .82rem;
    color: #64748b;
    margin-top: 4px;
}
.fs-hint {
    font-size: .78rem;
    color: #94a3b8;
    margin-top: 12px;
    text-align: center;
}

/* ===== PRINT STYLES ===== */
@media print {
    body * { visibility: hidden; }
    #printArea, #printArea * { visibility: visible; }
    #printArea {
        position: absolute;
        top: 0; left: 0;
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 40px 20px;
        text-align: center;
    }
    #printArea .print-school-name {
        font-size: 13pt;
        font-weight: 800;
        color: #1e293b;
        margin-bottom: 4px;
        font-family: 'Inter', Arial, sans-serif;
    }
    #printArea .print-school-sub {
        font-size: 10pt;
        color: #64748b;
        margin-bottom: 16px;
        font-family: 'Inter', Arial, sans-serif;
    }
    #printArea .print-divider {
        width: 200px; height: 2px;
        background: #e2e8f0; margin: 0 auto 16px;
    }
    #printArea .print-nama {
        font-size: 14pt;
        font-weight: 800;
        color: #1e293b;
        margin-bottom: 4px;
        font-family: 'Inter', Arial, sans-serif;
    }
    #printArea .print-nip {
        font-size: 10pt;
        color: #64748b;
        margin-bottom: 4px;
        font-family: 'Inter', Arial, sans-serif;
    }
    #printArea .print-jabatan {
        font-size: 10pt;
        color: #64748b;
        margin-bottom: 20px;
        font-family: 'Inter', Arial, sans-serif;
    }
    #printArea #qrPrint canvas,
    #printArea #qrPrint img {
        width: 200px !important;
        height: 200px !important;
    }
    #printArea .print-barcode-text {
        font-family: 'Courier New', monospace;
        font-size: 10pt;
        color: #64748b;
        margin-top: 12px;
        letter-spacing: 2px;
        font-weight: 700;
    }
    #printArea .print-footer {
        font-size: 9pt;
        color: #94a3b8;
        margin-top: 20px;
        font-family: 'Inter', Arial, sans-serif;
    }
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="barcode-page">

    
    <div class="id-card">
        <div class="id-avatar"><i class="fas fa-chalkboard-user"></i></div>
        <div class="id-nama"><?php echo e($guru->nama); ?></div>
        <div class="id-nip">ID: <?php echo e($guru->id_pengguna); ?></div>
        <div class="id-jabatan"><?php echo e($guru->jabatan); ?> &nbsp;·&nbsp; <?php echo e($guru->mata_pelajaran ?? '-'); ?></div>
    </div>

    
    <div class="barcode-box">
        <div class="barcode-label"><i class="fas fa-qrcode"></i> &nbsp;QR Code Presensi</div>
        <div id="qrCanvas"></div>
        <div class="barcode-text"><?php echo e($guru->barcode); ?></div>
    </div>

    
    <button class="btn-fullscreen" onclick="tampilFullscreen()">
        <i class="fas fa-expand"></i> Tampilkan Layar Penuh
    </button>
    <button class="btn-brightness" onclick="maxBrightness()">
        <i class="fas fa-sun"></i> Tips: Naikkan Kecerahan Layar
    </button>
    <button class="btn-print" id="btnDownload" onclick="unduhQR(this)">
        <i class="fas fa-download"></i> Unduh QR Code (PNG)
    </button>

    
    <div class="tip-box" style="margin-top:14px;">
        <strong><i class="fas fa-circle-info"></i> Cara Presensi:</strong>
        <ol>
            <li>Tekan <strong>Tampilkan Layar Penuh</strong></li>
            <li>Naikkan kecerahan HP ke maksimal</li>
            <li>Arahkan layar ke <strong>kamera scanner QR Code</strong> di meja piket</li>
            <li>Presensi otomatis tercatat ✓</li>
        </ol>
    </div>

</div>


<div class="fullscreen-overlay" id="fsOverlay">
    <button class="btn-close-fs" onclick="tutupFullscreen()" title="Tutup">
        <i class="fas fa-xmark"></i>
    </button>
    <div id="qrFull"></div>
    <div class="fs-nama"><?php echo e($guru->nama); ?></div>
    <div class="fs-nip">ID: <?php echo e($guru->id_pengguna); ?></div>
    <div class="fs-hint"><i class="fas fa-qrcode"></i> Arahkan ke kamera scanner QR Code</div>
</div>


<div id="printArea" style="display:none;">
    <img src="<?php echo e(asset('images/logo-sekolah.png')); ?>" alt="Logo"
         loading="lazy"
         style="width:72px;height:72px;object-fit:contain;margin-bottom:8px;">
    <div class="print-school-name">MADRASAH ALIYAH ATTAQWA</div>
    <div class="print-school-sub">YPIA Daarul Mu'min — Benda Tangerang</div>
    <div class="print-divider"></div>
    <div class="print-nama"><?php echo e($guru->nama); ?></div>
    <div class="print-nip">ID: <?php echo e($guru->id_pengguna); ?></div>
    <div class="print-jabatan"><?php echo e($guru->jabatan); ?> — <?php echo e($guru->mata_pelajaran ?? '-'); ?></div>
    <div id="qrPrint"></div>
    <div class="print-barcode-text"><?php echo e($guru->barcode); ?></div>
    <div class="print-footer">Kartu Presensi — MA Attaqwa Benda Tangerang</div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
const kodeQR = <?php echo json_encode($guru->barcode, 15, 512) ?>;
const namaGuru = <?php echo json_encode($guru->nama, 15, 512) ?>;
const nipGuru = <?php echo json_encode("ID: " . $guru->id_pengguna, 15, 512) ?>;
const jabatanGuru = <?php echo json_encode(($guru->jabatan ?? "Guru") . " 2014 " . ($guru->mata_pelajaran ?? "-"), 15, 512) ?>;
const barcodeText = <?php echo json_encode($guru->barcode, 15, 512) ?>;
const idPengguna = <?php echo json_encode($guru->id_pengguna, 15, 512) ?>;

document.addEventListener('DOMContentLoaded', function () {
    renderQR('qrCanvas', 220);
    renderQR('qrFull', 300);
    renderQR('qrPrint', 200);
});

function renderQR(containerId, size) {
    const container = document.getElementById(containerId);
    if (!container) return;
    container.innerHTML = '';
    new QRCode(container, {
        text: kodeQR,
        width: size,
        height: size,
        colorDark: '#1e293b',
        colorLight: '#ffffff',
        correctLevel: QRCode.CorrectLevel.H
    });
}

function tampilFullscreen() {
    const overlay = document.getElementById('fsOverlay');
    overlay.classList.add('show');
    if (document.documentElement.requestFullscreen) {
        document.documentElement.requestFullscreen().catch(() => {});
    }
    if ('wakeLock' in navigator) {
        navigator.wakeLock.request('screen').catch(() => {});
    }
}

function tutupFullscreen() {
    document.getElementById('fsOverlay').classList.remove('show');
    if (document.exitFullscreen) document.exitFullscreen().catch(() => {});
}

function maxBrightness() {
    alert('Cara naikkan kecerahan:\n• Android: Geser notifikasi dari atas → geser slider kecerahan ke kanan\n• iOS: Geser dari pojok kanan atas → geser slider kecerahan');
}

function unduhQR(btn) {
    // Tunggu canvas dari QRCode.js siap
    const qrEl = document.querySelector('#qrCanvas canvas');
    if (!qrEl) {
        alert('QR Code belum siap, tunggu sebentar lalu coba lagi.');
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Membuat gambar...';

    // Ukuran kartu
    const W = 440, H = 560;
    const cv = document.createElement('canvas');
    cv.width = W; cv.height = H;
    const ctx = cv.getContext('2d');

    // ─── Background putih ───────────────────────────────────
    ctx.fillStyle = '#ffffff';
    ctx.fillRect(0, 0, W, H);

    // ─── Header hijau tua ────────────────────────────────────
    ctx.fillStyle = '#1B5E20';
    ctx.fillRect(0, 0, W, 90);

    // Garis aksen emas di bawah header
    ctx.fillStyle = '#F9A825';
    ctx.fillRect(0, 90, W, 4);

    // Teks sekolah (putih)
    ctx.textAlign = 'center';
    ctx.fillStyle = '#ffffff';
    ctx.font = 'bold 15px Arial, sans-serif';
    ctx.fillText('MADRASAH ALIYAH ATTAQWA', W / 2, 34);
    ctx.font = '12px Arial, sans-serif';
    ctx.fillText("YPIA Daarul Mu'min \u2014 Benda, Tangerang", W / 2, 56);
    ctx.font = '10px Arial, sans-serif';
    ctx.fillStyle = 'rgba(255,255,255,0.75)';
    ctx.fillText('KARTU PRESENSI GURU', W / 2, 76);

    // ─── Nama, NIP, Jabatan ──────────────────────────────────
    ctx.fillStyle = '#1e293b';
    ctx.font = 'bold 18px Arial, sans-serif';
    ctx.fillText(namaGuru, W / 2, 132);

    ctx.fillStyle = '#64748b';
    ctx.font = '12px Arial, sans-serif';
    ctx.fillText(nipGuru, W / 2, 154);
    ctx.fillText(jabatanGuru, W / 2, 172);

    // Garis pemisah
    ctx.strokeStyle = '#e2e8f0';
    ctx.lineWidth = 1;
    ctx.beginPath();
    ctx.moveTo(40, 185); ctx.lineTo(W - 40, 185);
    ctx.stroke();

    // ─── Gambar QR Code dari canvas ──────────────────────────
    const qrSize = 220;
    const qrX = (W - qrSize) / 2;
    const qrY = 196;

    // Kotak putih di belakang QR
    ctx.fillStyle = '#f8fafc';
    ctx.beginPath();
    ctx.roundRect(qrX - 12, qrY - 12, qrSize + 24, qrSize + 24, 12);
    ctx.fill();
    ctx.strokeStyle = '#e2e8f0';
    ctx.lineWidth = 1.5;
    ctx.stroke();

    ctx.drawImage(qrEl, qrX, qrY, qrSize, qrSize);

    // ─── Kode barcode ────────────────────────────────────────
    ctx.fillStyle = '#475569';
    ctx.font = 'bold 13px Courier New, monospace';
    ctx.fillText(barcodeText, W / 2, qrY + qrSize + 38);

    // ─── Garis pemisah bawah ─────────────────────────────────
    ctx.strokeStyle = '#e2e8f0';
    ctx.lineWidth = 1;
    ctx.beginPath();
    ctx.moveTo(40, qrY + qrSize + 54); ctx.lineTo(W - 40, qrY + qrSize + 54);
    ctx.stroke();

    // ─── Footer ───────────────────────────────────────────────
    ctx.fillStyle = '#94a3b8';
    ctx.font = '10px Arial, sans-serif';
    ctx.fillText('Kartu Presensi \u2014 MA Attaqwa Benda Tangerang', W / 2, qrY + qrSize + 72);

    // ─── Border seluruh kartu ─────────────────────────────────
    ctx.strokeStyle = '#cbd5e1';
    ctx.lineWidth = 2;
    ctx.strokeRect(1, 1, W - 2, H - 2);

    // ─── Trigger download ─────────────────────────────────────
    setTimeout(function () {
        const link = document.createElement('a');
        link.download = 'QR-Presensi-' + idPengguna + '.png';
        link.href = cv.toDataURL('image/png');
        link.click();

        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-download"></i> Unduh QR Code (PNG)';
    }, 100);
}

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') tutupFullscreen();
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\laravel-presensi-guru\resources\views\guru\barcode_saya.blade.php ENDPATH**/ ?>