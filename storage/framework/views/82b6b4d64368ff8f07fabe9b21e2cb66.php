<?php $__env->startSection('title','Laporan Presensi'); ?>

<?php $__env->startPush('styles'); ?>
<style>
.filter-form { display:flex; flex-wrap:wrap; gap:12px; align-items:flex-end; }
.filter-form .form-group { margin:0; flex:1; min-width:140px; }
.header-row { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; margin-bottom:20px; }
.export-btns { display:flex; gap:8px; flex-wrap:wrap; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>

<div class="header-row">
    <div class="page-header" style="margin-bottom:0">
        <h1><i class="fas fa-chart-bar" style="color:hsl(145,60%,28%)"></i> Laporan Presensi</h1>
        <p>Rekap, filter, dan ekspor data presensi guru</p>
    </div>
    <div class="export-btns">
        <a href="<?php echo e(route('laporan.pdf', request()->query())); ?>" class="btn btn-danger" target="_blank">
            <i class="fas fa-file-pdf"></i> Export PDF
        </a>
        <a href="<?php echo e(route('laporan.excel', request()->query())); ?>" class="btn btn-success">
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
            <input type="date" name="dari" class="form-control" value="<?php echo e($dari); ?>">
        </div>
        <div class="form-group" style="min-width:140px">
            <label>Sampai Tanggal</label>
            <input type="date" name="sampai" class="form-control" value="<?php echo e($sampai); ?>">
        </div>
        <div class="form-group" style="min-width:180px">
            <label>Nama Guru</label>
            <select name="guru_id" class="form-control">
                <option value="">— Semua Guru —</option>
                <?php $__currentLoopData = $gurus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($g->id); ?>" <?php if($guruId == $g->id): echo 'selected'; endif; ?>><?php echo e($g->nama); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="form-group" style="min-width:130px">
            <label>Status</label>
            <select name="status" class="form-control">
                <option value="">— Semua —</option>
                <option value="hadir"        <?php if($status=='hadir'): echo 'selected'; endif; ?>>Hadir</option>
                <option value="telat"        <?php if($status=='telat'): echo 'selected'; endif; ?>>Terlambat</option>
                <option value="tidak_hadir"  <?php if($status=='tidak_hadir'): echo 'selected'; endif; ?>>Tidak Hadir</option>
                <option value="izin"         <?php if($status=='izin'): echo 'selected'; endif; ?>>Izin</option>
                <option value="sakit"        <?php if($status=='sakit'): echo 'selected'; endif; ?>>Sakit</option>
                <option value="alpha"        <?php if($status=='alpha'): echo 'selected'; endif; ?>>Alpha</option>
            </select>
        </div>
        <div style="display:flex;gap:8px;align-items:flex-end;padding-bottom:0">
            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Tampilkan</button>
            <a href="<?php echo e(route('laporan.index')); ?>" class="btn btn-secondary" title="Reset filter">
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
        <div class="stat-value"><?php echo e($rekap['total']); ?></div>
    </div>
    <div class="stat-card hadir">
        <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
        <div class="stat-label">Hadir</div>
        <div class="stat-value"><?php echo e($rekap['hadir']); ?></div>
    </div>
    <div class="stat-card telat">
        <div class="stat-icon"><i class="fas fa-clock"></i></div>
        <div class="stat-label">Terlambat</div>
        <div class="stat-value"><?php echo e($rekap['telat']); ?></div>
    </div>
    <div class="stat-card absen">
        <div class="stat-icon"><i class="fas fa-user-xmark"></i></div>
        <div class="stat-label">Tidak Hadir</div>
        <div class="stat-value"><?php echo e($rekap['tidak_hadir']); ?></div>
    </div>
    <div class="stat-card izin">
        <div class="stat-icon"><i class="fas fa-file-circle-check"></i></div>
        <div class="stat-label">Izin</div>
        <div class="stat-value"><?php echo e($rekap['izin']); ?></div>
    </div>
    <div class="stat-card sakit">
        <div class="stat-icon"><i class="fas fa-kit-medical"></i></div>
        <div class="stat-label">Sakit</div>
        <div class="stat-value"><?php echo e($rekap['sakit']); ?></div>
    </div>
    <?php if($rekap['alpha'] > 0): ?>
    <div class="stat-card" style="border-color:#db2777">
        <div class="stat-icon" style="background:#fce7f3;color:#db2777"><i class="fas fa-ban"></i></div>
        <div class="stat-label">Alpha</div>
        <div class="stat-value" style="color:#db2777"><?php echo e($rekap['alpha']); ?></div>
    </div>
    <?php endif; ?>
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
                <?php echo e(\Carbon\Carbon::parse($dari)->isoFormat('D MMM Y')); ?> s/d <?php echo e(\Carbon\Carbon::parse($sampai)->isoFormat('D MMM Y')); ?>

            </span>
        </h3>
        <span style="font-size:.82rem;color:#5a7a5a"><?php echo e($presensis->total()); ?> data</span>
    </div>
    
    <?php if($rekap['tidak_hadir'] > 0): ?>
    <div style="padding:10px 0 4px;">
        <div class="alert alert-warning" style="margin:0;font-size:.82rem;padding:9px 14px;">
            <i class="fas fa-triangle-exclamation"></i>
            <span>Guru yang <strong>tidak memiliki data presensi sama sekali</strong> dalam periode ini ditampilkan otomatis sebagai <strong>Tidak Hadir</strong>.</span>
        </div>
    </div>
    <?php endif; ?>
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
            <?php $__empty_1 = true; $__currentLoopData = $presensis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td style="text-align:center;color:#94a3b8"><?php echo e($presensis->firstItem() + $i); ?></td>
                <td style="white-space:nowrap">
                    <span style="font-weight:600"><?php echo e($p->tanggal->format('d/m/Y')); ?></span><br>
                    <small style="color:#5a7a5a"><?php echo e($p->tanggal->isoFormat('ddd')); ?></small>
                </td>
                <td><strong><?php echo e($p->guru->nama ?? '-'); ?></strong></td>
                <td style="font-size:.8rem;color:#5a7a5a;white-space:nowrap"><?php echo e($p->guru->id_pengguna ?? '-'); ?></td>
                <td style="font-weight:600;color:hsl(145,60%,28%)"><?php echo e($p->jam_masuk ?? '-'); ?></td>
                <td style="color:#5a7a5a"><?php echo e($p->jam_pulang ?? '-'); ?></td>
                <td>
                    <?php $sc = str_replace('_','-',$p->status); ?>
                    <span class="badge badge-<?php echo e($sc); ?>">
                        <?php echo e(strtoupper(str_replace('_',' ',$p->status))); ?>

                    </span>
                </td>
                <td>
                    <?php if($p->menit_telat > 0): ?>
                        <span style="color:#f59e0b;font-weight:700;font-size:.88rem">+<?php echo e($p->menit_telat); ?> mnt</span>
                    <?php else: ?>
                        <span style="color:#94a3b8">—</span>
                    <?php endif; ?>
                </td>
                <td style="font-size:.8rem;color:#5a7a5a"><?php echo e(ucfirst($p->metode ?? '-')); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="9">
                    <div class="empty-state">
                        <i class="fas fa-calendar-xmark"></i>
                        Tidak ada data presensi pada rentang tanggal ini
                    </div>
                </td>
            </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if($presensis->hasPages()): ?>
    <div style="margin-top:16px;padding-top:14px;border-top:1px solid #d4e8d4">
        <?php echo e($presensis->links()); ?>

    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\laravel-presensi-guru\resources\views\laporan\index.blade.php ENDPATH**/ ?>