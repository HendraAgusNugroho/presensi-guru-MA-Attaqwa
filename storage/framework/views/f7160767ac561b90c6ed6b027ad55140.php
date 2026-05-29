<?php $__env->startSection('title','Log Fingerprint'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header" style="display:flex;align-items:center;justify-content:space-between">
    <div>
        <h1>Log Fingerprint</h1>
        <p>Riwayat seluruh data scan dari mesin fingerprint</p>
    </div>
    <a href="<?php echo e(route('fingerprint.import')); ?>" class="btn btn-primary">
        <i class="fas fa-file-import"></i> Import Data
    </a>
</div>

<div class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr><th>#</th><th>ID Fingerprint</th><th>Nama Guru</th><th>Waktu Scan</th><th>Tipe</th><th>Status Proses</th></tr>
            </thead>
            <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td><?php echo e($logs->firstItem() + $i); ?></td>
                <td><code style="background:#f1f5f9;padding:2px 6px;border-radius:4px"><?php echo e($log->id_fingerprint); ?></code></td>
                <td>
                    <?php if($log->guru): ?>
                        <strong><?php echo e($log->guru->nama); ?></strong>
                        <div style="font-size:.75rem;color:#94a3b8"><?php echo e($log->guru->id_pengguna); ?></div>
                    <?php else: ?>
                        <span style="color:#ef4444;font-size:.82rem"><i class="fas fa-exclamation-circle"></i> Tidak ditemukan</span>
                    <?php endif; ?>
                </td>
                <td><?php echo e($log->waktu_scan->format('d/m/Y H:i:s')); ?></td>
                <td>
                    <span class="badge <?php echo e($log->tipe=='masuk' ? 'badge-hadir' : 'badge-izin'); ?>">
                        <?php echo e(strtoupper($log->tipe)); ?>

                    </span>
                </td>
                <td>
                    <?php if($log->diproses): ?>
                        <span class="badge badge-hadir"><i class="fas fa-check"></i> Diproses</span>
                    <?php else: ?>
                        <span class="badge badge-tidak-hadir"><i class="fas fa-times"></i> Gagal</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="6" style="text-align:center;color:#94a3b8;padding:32px">Belum ada log fingerprint</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div style="margin-top:16px"><?php echo e($logs->links()); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\laravel-presensi-guru\resources\views\fingerprint\index.blade.php ENDPATH**/ ?>