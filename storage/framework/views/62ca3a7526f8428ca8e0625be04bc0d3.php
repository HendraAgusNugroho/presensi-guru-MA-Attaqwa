<?php $__env->startSection('title', 'Presensi Saya'); ?>
<?php $__env->startSection('content'); ?>
<div class="page-header" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
    <h1 class="page-title"><i class="fas fa-calendar-check"></i> Presensi Saya</h1>
    <form method="GET" style="display:flex;gap:8px;align-items:center;">
        <select name="bulan" class="form-control" style="width:auto;" onchange="this.form.submit()">
            <?php $__currentLoopData = range(1,12); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($m); ?>" <?php if($m==$bulan): echo 'selected'; endif; ?>><?php echo e(\Carbon\Carbon::create()->month($m)->isoFormat('MMMM')); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <select name="tahun" class="form-control" style="width:auto;" onchange="this.form.submit()">
            <?php $__currentLoopData = range(date('Y')-2, date('Y')); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $y): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($y); ?>" <?php if($y==$tahun): echo 'selected'; endif; ?>><?php echo e($y); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </form>
</div>


<?php if($guru): ?>
<div class="card" style="margin-bottom:20px;">
    <div class="card-body" style="padding:16px 20px;display:flex;align-items:center;gap:16px;">
        <div style="width:48px;height:48px;background:#ede9fe;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;color:#4f46e5;">
            <i class="fas fa-chalkboard-user"></i>
        </div>
        <div>
            <div style="font-weight:700;font-size:.95rem;color:#1e293b;"><?php echo e($guru->nama); ?></div>
            <div style="font-size:.8rem;color:#64748b;"><?php echo e($guru->jabatan); ?> &bull; <?php echo e($guru->mata_pelajaran ?? '-'); ?> &bull; ID: <?php echo e($guru->id_pengguna); ?></div>
        </div>
    </div>
</div>
<?php endif; ?>


<div style="display:grid;grid-template-columns:repeat(5,1fr);gap:12px;margin-bottom:20px;">
    <?php $__currentLoopData = [
        ['hadir','Hadir','#16a34a','fa-circle-check'],
        ['telat','Telat','#d97706','fa-clock'],
        ['izin','Izin','#0284c7','fa-calendar-xmark'],
        ['sakit','Sakit','#7c3aed','fa-heart-pulse'],
        ['tidak_hadir','Tidak Hadir','#dc2626','fa-circle-xmark'],
    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$key,$label,$color,$icon]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="card" style="text-align:center;padding:16px;">
        <div style="font-size:1.5rem;color:<?php echo e($color); ?>;margin-bottom:6px;"><i class="fas <?php echo e($icon); ?>"></i></div>
        <div style="font-size:1.6rem;font-weight:800;color:#1e293b;"><?php echo e($rekap[$key]); ?></div>
        <div style="font-size:.75rem;color:#64748b;"><?php echo e($label); ?></div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>


<div class="card">
    <div class="card-header"><i class="fas fa-list"></i> Riwayat Presensi — <?php echo e(\Carbon\Carbon::create()->month($bulan)->isoFormat('MMMM')); ?> <?php echo e($tahun); ?></div>
    <div class="card-body" style="padding:0;">
        <?php if($presensis->isEmpty()): ?>
        <div style="text-align:center;padding:40px;color:#94a3b8;">
            <i class="fas fa-calendar-xmark" style="font-size:2rem;margin-bottom:12px;display:block;"></i>
            Belum ada data presensi untuk periode ini.
        </div>
        <?php else: ?>
        <div style="overflow-x:auto;">
        <table class="table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Hari</th>
                    <th>Jam Masuk</th>
                    <th>Jam Pulang</th>
                    <th>Terlambat</th>
                    <th>Status</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $presensis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td style="font-weight:600;"><?php echo e(\Carbon\Carbon::parse($p->tanggal)->format('d/m/Y')); ?></td>
                    <td style="color:#64748b;"><?php echo e(\Carbon\Carbon::parse($p->tanggal)->isoFormat('dddd')); ?></td>
                    <td><?php echo e($p->jam_masuk ? \Carbon\Carbon::parse($p->jam_masuk)->format('H:i') : '-'); ?></td>
                    <td><?php echo e($p->jam_pulang ? \Carbon\Carbon::parse($p->jam_pulang)->format('H:i') : '-'); ?></td>
                    <td>
                        <?php if($p->menit_telat > 0): ?>
                            <span style="color:#d97706;font-weight:600;"><?php echo e($p->menit_telat); ?> menit</span>
                        <?php else: ?>
                            <span style="color:#94a3b8;">—</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php $st = match($p->status){
                            'hadir'=>['#dcfce7','#15803d','Hadir'],
                            'telat'=>['#fef9c3','#a16207','Terlambat'],
                            'izin'=>['#dbeafe','#1d4ed8','Izin'],
                            'sakit'=>['#f3e8ff','#7e22ce','Sakit'],
                            default=>['#fee2e2','#dc2626','Tidak Hadir'],
                        }; ?>
                        <span style="background:<?php echo e($st[0]); ?>;color:<?php echo e($st[1]); ?>;padding:3px 10px;border-radius:6px;font-size:.78rem;font-weight:700;">
                            <?php echo e($st[2]); ?>

                        </span>
                    </td>
                    <td style="color:#64748b;font-size:.85rem;"><?php echo e($p->keterangan ?? '-'); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\laravel-presensi-guru\resources\views\presensi\saya.blade.php ENDPATH**/ ?>