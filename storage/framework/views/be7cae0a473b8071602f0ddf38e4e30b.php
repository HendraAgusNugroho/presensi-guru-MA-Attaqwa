<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Presensi Guru</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 9.5px; color: #1a2e1a; background: #fff; }

        /* Header */
        .kop { border-bottom: 3px solid #1B5E20; padding-bottom: 10px; margin-bottom: 14px; display: table; width: 100%; }
        .kop-logo { display: table-cell; width: 65px; vertical-align: middle; }
        .kop-logo-icon {
            width: 55px; height: 55px;
            background: #1B5E20; border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            color: #F9A825; font-size: 28px; font-weight: 900;
            text-align: center; line-height: 55px;
        }
        .kop-text { display: table-cell; vertical-align: middle; padding-left: 12px; }
        .kop-text .inst-name { font-size: 15px; font-weight: 900; color: #1B5E20; text-transform: uppercase; letter-spacing: .03em; }
        .kop-text .inst-full { font-size: 9px; color: #2E7D32; margin-top: 2px; }
        .kop-text .inst-addr { font-size: 8px; color: #666; margin-top: 2px; }
        .kop-stamp { display: table-cell; width: 120px; text-align: right; vertical-align: middle; }
        .kop-stamp .stamp-box {
            border: 2px solid #F9A825; border-radius: 6px;
            padding: 6px 10px; display: inline-block; text-align: center;
        }
        .kop-stamp .stamp-title { font-size: 7px; font-weight: 700; color: #1B5E20; text-transform: uppercase; letter-spacing: .05em; }
        .kop-stamp .stamp-date { font-size: 9px; font-weight: 700; color: #1B5E20; margin-top: 3px; }

        /* Title */
        .report-title { text-align: center; margin-bottom: 12px; }
        .report-title h2 { font-size: 13px; font-weight: 900; color: #1B5E20; text-transform: uppercase; letter-spacing: .04em; }
        .report-title p { font-size: 9px; color: #555; margin-top: 3px; }

        /* Rekap cards */
        .rekap-row { display: table; width: 100%; margin-bottom: 14px; border-collapse: separate; border-spacing: 6px 0; }
        .rekap-cell { display: table-cell; text-align: center; }
        .rekap-box { border-radius: 6px; padding: 7px 4px; }
        .rekap-box .num { font-size: 18px; font-weight: 900; }
        .rekap-box .lbl { font-size: 7.5px; font-weight: 700; text-transform: uppercase; margin-top: 1px; }
        .r-hadir { background: #E8F5E9; }   .r-hadir .num { color: #1B5E20; }   .r-hadir .lbl { color: #2E7D32; }
        .r-telat  { background: #FFFDE7; }  .r-telat .num  { color: #F57F17; }  .r-telat .lbl  { color: #F9A825; }
        .r-absen  { background: #FFEBEE; }  .r-absen .num  { color: #B71C1C; }  .r-absen .lbl  { color: #C62828; }
        .r-izin   { background: #E3F2FD; }  .r-izin .num   { color: #0D47A1; }  .r-izin .lbl   { color: #1565C0; }
        .r-sakit  { background: #FFF3E0; }  .r-sakit .num  { color: #E65100; }  .r-sakit .lbl  { color: #F57C00; }
        .r-alpha  { background: #FCE4EC; }  .r-alpha .num  { color: #880E4F; }  .r-alpha .lbl  { color: #AD1457; }

        /* Table */
        table { width: 100%; border-collapse: collapse; font-size: 8.5px; }
        thead tr { background: #1B5E20; color: #fff; }
        th { padding: 7px 8px; text-align: center; font-weight: 700; font-size: 8px; text-transform: uppercase; letter-spacing: .04em; border: 1px solid #388E3C; }
        td { padding: 6px 8px; border: 1px solid #C8E6C9; vertical-align: middle; }
        tr:nth-child(even) td { background: #F1F8E9; }

        /* Status badges */
        .s { display: inline-block; padding: 2px 8px; border-radius: 10px; font-weight: 800; font-size: 8px; }
        .s-hadir       { background: #E8F5E9; color: #1B5E20; }
        .s-telat        { background: #FFFDE7; color: #F57F17; }
        .s-tidak-hadir  { background: #FFEBEE; color: #C62828; }
        .s-izin         { background: #E3F2FD; color: #1565C0; }
        .s-sakit        { background: #FFF3E0; color: #E65100; }
        .s-alpha        { background: #FCE4EC; color: #AD1457; }

        /* Footer */
        .pdf-footer { margin-top: 16px; display: table; width: 100%; }
        .footer-left { display: table-cell; font-size: 7.5px; color: #777; vertical-align: bottom; }
        .footer-right { display: table-cell; text-align: right; vertical-align: bottom; }
        .ttd-box { display: inline-block; text-align: center; font-size: 8px; }
        .ttd-line { width: 140px; border-top: 1px solid #333; margin-top: 40px; }
        .ttd-label { font-weight: 700; font-size: 8px; color: #1B5E20; }
    </style>
</head>
<body>

<!-- Kop Surat -->
<div class="kop">
    <div class="kop-logo">
        <div class="kop-logo-icon">MA</div>
    </div>
    <div class="kop-text">
        <div class="inst-name">Madrasah Aliyah Attaqwa</div>
        <div class="inst-full">Yayasan Perguruan Islam Attaqwa (YPIA) Daarul Mu'min</div>
        <div class="inst-addr">Jl. Raya Benda, Benda Tangerang &nbsp;|&nbsp; presensi.ma-attaqwa.sch.id</div>
    </div>
    <div class="kop-stamp">
        <div class="stamp-box">
            <div class="stamp-title">Laporan Presensi</div>
            <div class="stamp-date">Dicetak: <?php echo e(now()->isoFormat('D MMM Y')); ?></div>
        </div>
    </div>
</div>

<!-- Judul Laporan -->
<div class="report-title">
    <h2>Laporan Presensi Guru</h2>
    <p>Periode: <?php echo e(\Carbon\Carbon::parse($dari)->isoFormat('D MMMM Y')); ?> — <?php echo e(\Carbon\Carbon::parse($sampai)->isoFormat('D MMMM Y')); ?></p>
</div>

<!-- Rekap Stats -->
<div class="rekap-row">
    <div class="rekap-cell"><div class="rekap-box r-hadir"><div class="num"><?php echo e($rekap['hadir']); ?></div><div class="lbl">Hadir</div></div></div>
    <div class="rekap-cell"><div class="rekap-box r-telat"><div class="num"><?php echo e($rekap['telat']); ?></div><div class="lbl">Terlambat</div></div></div>
    <div class="rekap-cell"><div class="rekap-box r-absen"><div class="num"><?php echo e($rekap['tidak_hadir']); ?></div><div class="lbl">Tidak Hadir</div></div></div>
    <div class="rekap-cell"><div class="rekap-box r-izin"><div class="num"><?php echo e($rekap['izin']); ?></div><div class="lbl">Izin</div></div></div>
    <div class="rekap-cell"><div class="rekap-box r-sakit"><div class="num"><?php echo e($rekap['sakit']); ?></div><div class="lbl">Sakit</div></div></div>
    <div class="rekap-cell"><div class="rekap-box r-alpha"><div class="num"><?php echo e($rekap['alpha']); ?></div><div class="lbl">Alpha</div></div></div>
</div>

<!-- Tabel -->
<table>
    <thead>
        <tr>
            <th style="width:30px">#</th>
            <th>Tanggal</th>
            <th style="width:120px">Nama Guru</th>
            <th>ID</th>
            <th>Jam Masuk</th>
            <th>Jam Pulang</th>
            <th>Status</th>
            <th>Telat (mnt)</th>
            <th>Metode</th>
        </tr>
    </thead>
    <tbody>
    <?php $__empty_1 = true; $__currentLoopData = $presensis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <tr>
        <td style="text-align:center"><?php echo e($i + 1); ?></td>
        <td style="text-align:center"><?php echo e($p->tanggal->isoFormat('ddd, D/M/Y')); ?></td>
        <td><strong><?php echo e($p->guru->nama ?? '-'); ?></strong></td>
        <td style="font-size:7.5px;color:#666"><?php echo e($p->guru->id_pengguna ?? '-'); ?></td>
        <td style="text-align:center"><?php echo e($p->jam_masuk ?? '-'); ?></td>
        <td style="text-align:center"><?php echo e($p->jam_pulang ?? '-'); ?></td>
        <td style="text-align:center">
            <?php $sc = str_replace('_','-',$p->status); ?>
            <span class="s s-<?php echo e($sc); ?>"><?php echo e(strtoupper(str_replace('_',' ',$p->status))); ?></span>
        </td>
        <td style="text-align:center">
            <?php if($p->menit_telat > 0): ?>
                <strong style="color:#F57F17">+<?php echo e($p->menit_telat); ?></strong>
            <?php else: ?> -
            <?php endif; ?>
        </td>
        <td style="text-align:center;font-size:7.5px"><?php echo e(ucfirst($p->metode ?? '-')); ?></td>
    </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <tr><td colspan="9" style="text-align:center;color:#999;padding:16px">Tidak ada data presensi pada periode ini</td></tr>
    <?php endif; ?>
    </tbody>
</table>

<!-- Footer -->
<div class="pdf-footer">
    <div class="footer-left">
        Dicetak: <?php echo e(now()->isoFormat('dddd, D MMMM Y, HH:mm')); ?> WIB<br>
        Total Data: <?php echo e($presensis->count()); ?> record<br>
        Dokumen ini dicetak oleh Sistem Presensi Guru MA Attaqwa
    </div>
    <div class="footer-right">
        <div class="ttd-box">
            <div>Tangerang, <?php echo e(now()->isoFormat('D MMMM Y')); ?></div>
            <div style="margin-top:2px;color:#555">Kepala Madrasah,</div>
            <div class="ttd-line"></div>
            <div class="ttd-label">(.................................................)</div>
        </div>
    </div>
</div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\laravel-presensi-guru\resources\views\laporan\pdf.blade.php ENDPATH**/ ?>