<?php $__env->startSection('title', 'Profil Saya'); ?>
<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h1 class="page-title"><i class="fas fa-user-circle"></i> Profil Saya</h1>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;max-width:900px;">
    
    <div class="card">
        <div class="card-header"><i class="fas fa-id-badge"></i> Informasi Akun</div>
        <div class="card-body" style="padding:24px;">
            <div style="text-align:center;margin-bottom:24px;">
                <div style="width:72px;height:72px;background:<?php echo e($user->role_color); ?>22;border-radius:50%;
                    display:flex;align-items:center;justify-content:center;margin:0 auto 12px;font-size:2rem;color:<?php echo e($user->role_color); ?>;">
                    <i class="fas fa-user"></i>
                </div>
                <h3 style="font-size:1rem;font-weight:700;color:#1e293b;"><?php echo e($user->name); ?></h3>
                <span style="background:<?php echo e($user->role_color); ?>22;color:<?php echo e($user->role_color); ?>;
                    padding:3px 10px;border-radius:6px;font-size:.78rem;font-weight:700;">
                    <?php echo e($user->role_label); ?>

                </span>
            </div>
            <table style="width:100%;font-size:.87rem;border-collapse:collapse;">
                <tr><td style="padding:8px 0;color:#64748b;width:40%">ID</td>
                    <td style="font-weight:600;color:#1e293b;"><?php echo e($user->id_pengguna); ?></td></tr>
                <tr><td style="padding:8px 0;color:#64748b;">Nama</td>
                    <td style="font-weight:600;color:#1e293b;"><?php echo e($user->name); ?></td></tr>
                <tr><td style="padding:8px 0;color:#64748b;">Role</td>
                    <td><span style="color:<?php echo e($user->role_color); ?>;font-weight:700;"><?php echo e($user->role_label); ?></span></td></tr>
                <?php if($user->guru): ?>
                <tr><td style="padding:8px 0;color:#64748b;">Jabatan</td>
                    <td style="font-weight:600;"><?php echo e($user->guru->jabatan); ?></td></tr>
                <tr><td style="padding:8px 0;color:#64748b;">Mata Pelajaran</td>
                    <td style="font-weight:600;"><?php echo e($user->guru->mata_pelajaran ?? '-'); ?></td></tr>
                <?php endif; ?>
            </table>
        </div>
    </div>

    
    <div class="card">
        <div class="card-header"><i class="fas fa-lock"></i> Ubah Password</div>
        <div class="card-body" style="padding:24px;">
            <?php if($errors->any()): ?>
            <div style="background:#fee2e2;color:#dc2626;padding:10px 14px;border-radius:8px;margin-bottom:16px;font-size:.85rem;">
                <?php echo e($errors->first()); ?>

            </div>
            <?php endif; ?>
            <?php if(session('success')): ?>
            <div style="background:#dcfce7;color:#15803d;padding:10px 14px;border-radius:8px;margin-bottom:16px;font-size:.85rem;">
                <i class="fas fa-check"></i> <?php echo e(session('success')); ?>

            </div>
            <?php endif; ?>
            <form method="POST" action="<?php echo e(route('profil.password')); ?>">
                <?php echo csrf_field(); ?>
                <div style="margin-bottom:16px;">
                    <label class="form-label">Password Lama</label>
                    <input type="password" name="password_lama" class="form-control" required placeholder="••••••••">
                </div>
                <div style="margin-bottom:16px;">
                    <label class="form-label">Password Baru</label>
                    <input type="password" name="password_baru" class="form-control" required placeholder="Minimal 6 karakter">
                </div>
                <div style="margin-bottom:20px;">
                    <label class="form-label">Konfirmasi Password Baru</label>
                    <input type="password" name="password_baru_confirmation" class="form-control" required placeholder="Ulangi password baru">
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;">
                    <i class="fas fa-save"></i> Simpan Password Baru
                </button>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\laravel-presensi-guru\resources\views\profil\index.blade.php ENDPATH**/ ?>