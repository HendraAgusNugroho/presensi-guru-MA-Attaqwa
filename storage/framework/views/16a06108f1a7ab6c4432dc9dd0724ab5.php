<?php $__env->startSection('title','Detail Guru'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header" style="display:flex;align-items:center;justify-content:space-between">
    <div>
        <h1>Detail Guru</h1>
        <p><?php echo e($guru->nama); ?></p>
    </div>
    <div style="display:flex;gap:8px">
        <a href="<?php echo e(route('guru.edit', $guru)); ?>" class="btn btn-primary">
            <i class="fas fa-pen"></i> Edit
        </a>
        <a href="<?php echo e(route('guru.index')); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="card-grid-2">
    <div class="card">
        <div style="display:flex;align-items:center;gap:16px;margin-bottom:20px">
            <div style="width:64px;height:64px;border-radius:16px;background:linear-gradient(135deg,#4f46e5,#7c3aed);
                display:flex;align-items:center;justify-content:center;color:#fff;font-size:1.8rem;font-weight:800">
                <?php echo e(strtoupper(substr($guru->nama,0,1))); ?>

            </div>
            <div>
                <h2 style="font-size:1.1rem;font-weight:700"><?php echo e($guru->nama); ?></h2>
                <div style="color:#64748b;font-size:.85rem"><?php echo e($guru->jabatan ?? 'Guru'); ?></div>
                <span class="badge <?php echo e($guru->status == 'aktif' ? 'badge-hadir' : 'badge-tidak-hadir'); ?>" style="margin-top:4px">
                    <?php echo e(strtoupper($guru->status)); ?>

                </span>
            </div>
        </div>
        <table style="font-size:.875rem">
            <tr><td style="color:#64748b;padding:6px 0;width:130px">ID</td><td><strong><?php echo e($guru->id_pengguna); ?></strong></td></tr>
            <tr><td style="color:#64748b;padding:6px 0">Email</td><td><?php echo e($guru->email ?? '-'); ?></td></tr>
            <tr><td style="color:#64748b;padding:6px 0">No. HP</td><td><?php echo e($guru->no_hp ?? '-'); ?></td></tr>
            <tr><td style="color:#64748b;padding:6px 0">Mata Pelajaran</td><td><?php echo e($guru->mata_pelajaran ?? '-'); ?></td></tr>
            <tr><td style="color:#64748b;padding:6px 0">ID Fingerprint</td>
                <td>
                    <?php if($guru->id_fingerprint): ?>
                        <span style="background:#dbeafe;color:#1d4ed8;padding:2px 8px;border-radius:99px;font-size:.75rem;font-weight:700">
                            <?php echo e($guru->id_fingerprint); ?>

                        </span>
                    <?php else: ?> - <?php endif; ?>
                </td>
            </tr>
            <tr><td style="color:#64748b;padding:6px 0">QR Code</td>
                <td><code style="background:#f1f5f9;padding:2px 8px;border-radius:4px;font-size:.8rem"><?php echo e($guru->barcode); ?></code></td>
            </tr>
        </table>
    </div>

    <div class="card">
        <div class="card-header"><h3><i class="fas fa-history" style="color:#4f46e5"></i> Riwayat Presensi</h3></div>
        <div class="table-wrapper">
            <table>
                <thead><tr><th>Tanggal</th><th>Masuk</th><th>Pulang</th><th>Status</th></tr></thead>
                <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $presensi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e($p->tanggal->format('d/m/Y')); ?></td>
                    <td><?php echo e($p->jam_masuk ?? '-'); ?></td>
                    <td><?php echo e($p->jam_pulang ?? '-'); ?></td>
                    <td>
                        <span class="badge badge-<?php echo e(str_replace('_','-',$p->status)); ?>">
                            <?php echo e(strtoupper(str_replace('_',' ',$p->status))); ?>

                        </span>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="4" style="text-align:center;color:#94a3b8">Belum ada riwayat presensi</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div style="margin-top:12px"><?php echo e($presensi->links()); ?></div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\laravel-presensi-guru\resources\views\guru\show.blade.php ENDPATH**/ ?>