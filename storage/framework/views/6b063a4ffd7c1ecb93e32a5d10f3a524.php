<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifikasi Pengajuan <?php echo e(ucfirst($presensi->status)); ?></title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            background: #f1f5f9;
            color: #1e293b;
            padding: 32px 16px;
            font-size: 15px;
            line-height: 1.6;
        }
        .email-wrapper {
            max-width: 580px;
            margin: 0 auto;
        }
        /* Header */
        .email-header {
            background: linear-gradient(135deg, #1a4a1a, #2d6a2d);
            border-radius: 16px 16px 0 0;
            padding: 32px 36px 28px;
            text-align: center;
        }
        .logo-box {
            width: 72px;
            height: 72px;
            background: #fff;
            border-radius: 14px;
            margin: 0 auto 16px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .logo-box img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        .school-name {
            color: #fff;
            font-size: 13px;
            font-weight: 800;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        .school-sub {
            color: rgba(255,255,255,0.65);
            font-size: 12px;
        }
        /* Status banner */
        .status-banner {
            padding: 20px 36px;
            text-align: center;
            border-radius: 0;
        }
        .status-banner.disetujui {
            background: #dcfce7;
            border-bottom: 3px solid #86efac;
        }
        .status-banner.ditolak {
            background: #fee2e2;
            border-bottom: 3px solid #fca5a5;
        }
        .status-icon {
            font-size: 42px;
            margin-bottom: 8px;
        }
        .status-title {
            font-size: 20px;
            font-weight: 800;
        }
        .status-banner.disetujui .status-title { color: #15803d; }
        .status-banner.ditolak  .status-title { color: #dc2626; }
        .status-subtitle {
            font-size: 13px;
            margin-top: 4px;
        }
        .status-banner.disetujui .status-subtitle { color: #166534; }
        .status-banner.ditolak  .status-subtitle { color: #991b1b; }
        /* Body */
        .email-body {
            background: #fff;
            padding: 32px 36px;
        }
        .greeting {
            font-size: 16px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 12px;
        }
        .message-text {
            color: #475569;
            font-size: 14px;
            margin-bottom: 24px;
        }
        /* Detail card */
        .detail-card {
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 24px;
        }
        .detail-card-header {
            background: #f1f5f9;
            padding: 10px 16px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            border-bottom: 1px solid #e2e8f0;
        }
        .detail-row {
            display: flex;
            padding: 11px 16px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 14px;
        }
        .detail-row:last-child { border-bottom: none; }
        .detail-label {
            width: 140px;
            flex-shrink: 0;
            color: #64748b;
            font-weight: 600;
        }
        .detail-value {
            color: #1e293b;
            font-weight: 500;
        }
        /* Status pill */
        .pill {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 700;
        }
        .pill-izin  { background: #dbeafe; color: #1d4ed8; }
        .pill-sakit { background: #f3e8ff; color: #7e22ce; }
        .pill-disetujui { background: #dcfce7; color: #15803d; }
        .pill-ditolak   { background: #fee2e2; color: #dc2626; }
        /* Info box */
        .info-box {
            border-radius: 10px;
            padding: 14px 16px;
            font-size: 13px;
            margin-bottom: 24px;
            display: flex;
            gap: 12px;
            align-items: flex-start;
        }
        .info-box.success { background: #f0fdf4; border: 1.5px solid #bbf7d0; color: #166534; }
        .info-box.danger  { background: #fff1f2; border: 1.5px solid #fecdd3; color: #9f1239; }
        .info-icon { font-size: 18px; flex-shrink: 0; margin-top: 1px; }
        .info-text { line-height: 1.5; }
        .info-text strong { display: block; margin-bottom: 3px; font-size: 13px; }
        /* Footer */
        .email-footer {
            background: #f8fafc;
            border-radius: 0 0 16px 16px;
            padding: 20px 36px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
        }
        .footer-text {
            font-size: 12px;
            color: #94a3b8;
            line-height: 1.7;
        }
        .footer-text strong { color: #64748b; }
    </style>
</head>
<body>
<div class="email-wrapper">

    
    <div class="email-header">
        <div class="logo-box">
            <img src="<?php echo e($message->embed(public_path('images/logo-sekolah.png'))); ?>" alt="Logo MA Attaqwa">
        </div>
        <div class="school-name">MA Attaqwa — YPIA Daarul Mu'min</div>
        <div class="school-sub">Benda, Tangerang</div>
    </div>

    
    <?php
        $isDisetujui   = $approvalStatus === 'disetujui';
        $statusLabel   = $presensi->status === 'izin' ? 'Izin' : 'Sakit';
        $namaGuru      = $presensi->guru->nama ?? 'Guru';
        $tanggalFormat = \Carbon\Carbon::parse($presensi->tanggal)->isoFormat('dddd, D MMMM Y');
    ?>
    <div class="status-banner <?php echo e($approvalStatus); ?>">
        <div class="status-icon"><?php echo e($isDisetujui ? '✅' : '❌'); ?></div>
        <div class="status-title">
            Pengajuan <?php echo e($statusLabel); ?> <?php echo e($isDisetujui ? 'Disetujui' : 'Ditolak'); ?>

        </div>
        <div class="status-subtitle">
            <?php echo e($isDisetujui
                ? 'Pengajuan Anda telah disetujui oleh admin.'
                : 'Pengajuan Anda tidak dapat disetujui oleh admin.'); ?>

        </div>
    </div>

    
    <div class="email-body">
        <div class="greeting">Assalamu'alaikum, <?php echo e($namaGuru); ?>.</div>
        <p class="message-text">
            Kami ingin memberitahukan bahwa pengajuan <strong><?php echo e($statusLabel); ?></strong> Anda
            untuk tanggal <strong><?php echo e($tanggalFormat); ?></strong> telah diproses oleh admin.
            Berikut detail pengajuan Anda:
        </p>

        
        <div class="detail-card">
            <div class="detail-card-header">Detail Pengajuan</div>
            <div class="detail-row">
                <div class="detail-label">Nama Guru</div>
                <div class="detail-value"><?php echo e($namaGuru); ?></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">ID</div>
                <div class="detail-value"><?php echo e($presensi->guru->id_pengguna ?? '-'); ?></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Tanggal</div>
                <div class="detail-value"><?php echo e($tanggalFormat); ?></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Jenis Pengajuan</div>
                <div class="detail-value">
                    <span class="pill pill-<?php echo e($presensi->status); ?>"><?php echo e($statusLabel); ?></span>
                </div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Keterangan</div>
                <div class="detail-value"><?php echo e($presensi->keterangan ?: 'Tidak ada keterangan'); ?></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Status Keputusan</div>
                <div class="detail-value">
                    <span class="pill pill-<?php echo e($approvalStatus); ?>">
                        <?php echo e($isDisetujui ? 'Disetujui' : 'Ditolak'); ?>

                    </span>
                </div>
            </div>
        </div>

        
        <?php if($isDisetujui): ?>
        <div class="info-box success">
            <div class="info-icon">ℹ️</div>
            <div class="info-text">
                <strong>Pengajuan Disetujui</strong>
                Presensi Anda untuk tanggal tersebut telah dicatat sebagai <strong><?php echo e($statusLabel); ?></strong>.
                Pastikan untuk tetap memberitahu pimpinan atau rekan kerja jika diperlukan.
            </div>
        </div>
        <?php else: ?>
        <div class="info-box danger">
            <div class="info-icon">⚠️</div>
            <div class="info-text">
                <strong>Pengajuan Ditolak</strong>
                Pengajuan Anda tidak disetujui. Silakan hubungi admin atau pimpinan sekolah
                untuk informasi lebih lanjut mengenai alasan penolakan.
            </div>
        </div>
        <?php endif; ?>
    </div>

    
    <div class="email-footer">
        <p class="footer-text">
            Email ini dikirim otomatis oleh sistem presensi.<br>
            <strong>MA Attaqwa — YPIA Daarul Mu'min, Benda Tangerang</strong><br>
            Jika ada pertanyaan, hubungi admin sekolah secara langsung.
        </p>
    </div>

</div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\laravel-presensi-guru\resources\views\emails\izin_sakit_approval.blade.php ENDPATH**/ ?>