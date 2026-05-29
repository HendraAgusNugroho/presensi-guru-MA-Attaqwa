<?php $__env->startSection('title','Edit Guru'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h1>Edit Data Guru</h1>
    <p><?php echo e($guru->nama); ?></p>
</div>

<div class="card" style="max-width:700px">
    <form method="POST" action="<?php echo e(route('guru.update', $guru)); ?>" enctype="multipart/form-data">
        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
        <div class="form-row">
            <div class="form-group">
                <label>ID <span style="color:red">*</span></label>
                <input type="text" name="id_pengguna" class="form-control" value="<?php echo e(old('id_pengguna', $guru->id_pengguna)); ?>" required>
            </div>
            <div class="form-group">
                <label>Nama Lengkap <span style="color:red">*</span></label>
                <input type="text" name="nama" class="form-control" value="<?php echo e(old('nama', $guru->nama)); ?>" required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="<?php echo e(old('email', $guru->email)); ?>">
            </div>
            <div class="form-group">
                <label>No. HP</label>
                <input type="text" name="no_hp" class="form-control" value="<?php echo e(old('no_hp', $guru->no_hp)); ?>">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Jabatan</label>
                <input type="text" name="jabatan" class="form-control" value="<?php echo e(old('jabatan', $guru->jabatan)); ?>">
            </div>
            <div class="form-group">
                <label>Mata Pelajaran</label>
                <input type="text" name="mata_pelajaran" class="form-control" value="<?php echo e(old('mata_pelajaran', $guru->mata_pelajaran)); ?>">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>ID Fingerprint</label>
                <input type="text" name="id_fingerprint" class="form-control" value="<?php echo e(old('id_fingerprint', $guru->id_fingerprint)); ?>" placeholder="FP001">
            </div>
            <div class="form-group">
                <label>Jenis Kelamin</label>
                <select name="jenis_kelamin" class="form-control">
                    <option value="L" <?php if(old('jenis_kelamin',$guru->jenis_kelamin)=='L'): echo 'selected'; endif; ?>>Laki-laki</option>
                    <option value="P" <?php if(old('jenis_kelamin',$guru->jenis_kelamin)=='P'): echo 'selected'; endif; ?>>Perempuan</option>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="aktif" <?php if($guru->status=='aktif'): echo 'selected'; endif; ?>>Aktif</option>
                    <option value="nonaktif" <?php if($guru->status=='nonaktif'): echo 'selected'; endif; ?>>Non-Aktif</option>
                </select>
            </div>
            <div class="form-group">
                <label>Foto Baru (opsional)</label>
                <input type="file" name="foto" class="form-control" accept="image/*">
            </div>
        </div>
        <div style="display:flex;gap:12px;margin-top:8px">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update</button>
            <a href="<?php echo e(route('guru.index')); ?>" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\laravel-presensi-guru\resources\views\guru\edit.blade.php ENDPATH**/ ?>