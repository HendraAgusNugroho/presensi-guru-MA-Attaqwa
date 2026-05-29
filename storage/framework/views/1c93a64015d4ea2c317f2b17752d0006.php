<?php $__env->startSection('title','Data Guru'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header" style="display:flex;align-items:center;justify-content:space-between">
    <div>
        <h1>Data Guru</h1>
        <p>Kelola data guru dan QR Code presensi</p>
    </div>
    <a href="<?php echo e(route('guru.create')); ?>" class="btn btn-primary">
        <i class="fas fa-plus"></i> Tambah Guru
    </a>
</div>

<div class="card">
    <form method="GET" style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap">
        <div style="flex:1;min-width:200px">
            <input type="text" name="search" class="form-control" placeholder="Cari nama atau NIP..." value="<?php echo e(request('search')); ?>">
        </div>
        <select name="status" class="form-control" style="width:auto">
            <option value="">Semua Status</option>
            <option value="aktif" <?php if(request('status')=='aktif'): echo 'selected'; endif; ?>>Aktif</option>
            <option value="nonaktif" <?php if(request('status')=='nonaktif'): echo 'selected'; endif; ?>>Non-Aktif</option>
        </select>
        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Cari</button>
        <a href="<?php echo e(route('guru.index')); ?>" class="btn btn-secondary"><i class="fas fa-refresh"></i></a>
    </form>
</div>

<div class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr><th>#</th><th>Nama</th><th>ID</th><th>Jabatan</th><th>Mapel</th>
                <th>ID Fingerprint</th><th>Status</th><th>Aksi</th></tr>
            </thead>
            <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $gurus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td><?php echo e($gurus->firstItem() + $i); ?></td>
                <td>
                    <div style="display:flex;align-items:center;gap:10px">
                        <div style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,#ede9fe,#dbeafe);
                            display:flex;align-items:center;justify-content:center;font-weight:700;color:#4f46e5;font-size:.9rem">
                            <?php echo e(strtoupper(substr($g->nama,0,1))); ?>

                        </div>
                        <div>
                            <strong><?php echo e($g->nama); ?></strong>
                            <div style="font-size:.75rem;color:#94a3b8"><?php echo e($g->email ?? '-'); ?></div>
                        </div>
                    </div>
                </td>
                <td><code style="background:#f1f5f9;padding:2px 6px;border-radius:4px;font-size:.8rem"><?php echo e($g->id_pengguna); ?></code></td>
                <td><?php echo e($g->jabatan ?? '-'); ?></td>
                <td><?php echo e($g->mata_pelajaran ?? '-'); ?></td>
                <td>
                    <?php if($g->id_fingerprint): ?>
                        <span style="background:#dbeafe;color:#1d4ed8;padding:2px 8px;border-radius:99px;font-size:.75rem;font-weight:700">
                            <?php echo e($g->id_fingerprint); ?>

                        </span>
                    <?php else: ?>
                        <span style="color:#94a3b8;font-size:.8rem">Belum set</span>
                    <?php endif; ?>
                </td>
                <td>
                    <span class="badge <?php echo e($g->status == 'aktif' ? 'badge-hadir' : 'badge-tidak-hadir'); ?>">
                        <?php echo e(strtoupper($g->status)); ?>

                    </span>
                </td>
                <td>
                    <div style="display:flex;gap:4px">
                        <a href="<?php echo e(route('guru.show', $g)); ?>" class="btn btn-secondary btn-sm" title="Detail">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="<?php echo e(route('guru.edit', $g)); ?>" class="btn btn-primary btn-sm" title="Edit">
                            <i class="fas fa-pen"></i>
                        </a>
                        <form method="POST" action="<?php echo e(route('guru.destroy', $g)); ?>"
                            onsubmit="return confirm('Hapus guru <?php echo e($g->nama); ?>?')">
                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                            <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="8" style="text-align:center;color:#94a3b8;padding:32px">Belum ada data guru</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div style="margin-top:16px"><?php echo e($gurus->links()); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\laravel-presensi-guru\resources\views\guru\index.blade.php ENDPATH**/ ?>