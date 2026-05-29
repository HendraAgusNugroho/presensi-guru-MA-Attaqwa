<?php $__env->startSection('title','Data Presensi'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header" style="display:flex;align-items:center;justify-content:space-between">
    <div>
        <h1>Data Presensi</h1>
        <p>Rekap presensi berdasarkan tanggal — guru tanpa presensi otomatis ditampilkan sebagai Tidak Hadir</p>
    </div>
    <a href="<?php echo e(route('presensi.scan')); ?>" class="btn btn-primary">
        <i class="fas fa-qrcode"></i> Scan QR Code
    </a>
</div>

<!-- Filter -->
<div class="card">
    <form method="GET" style="display:flex;flex-wrap:wrap;gap:12px;align-items:flex-end">
        <div class="form-group" style="margin:0;flex:1;min-width:140px">
            <label>Tanggal</label>
            <input type="date" name="tanggal" class="form-control"
                   value="<?php echo e(request('tanggal', $tanggal->format('Y-m-d'))); ?>">
        </div>
        <div class="form-group" style="margin:0;flex:1;min-width:140px">
            <label>Guru</label>
            <select name="guru_id" class="form-control">
                <option value="">Semua Guru</option>
                <?php $__currentLoopData = $gurus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($g->id); ?>" <?php if(request('guru_id') == $g->id): echo 'selected'; endif; ?>><?php echo e($g->nama); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="form-group" style="margin:0;flex:1;min-width:120px">
            <label>Status</label>
            <select name="status" class="form-control">
                <option value="">Semua</option>
                <option value="hadir"        <?php if(request('status')=='hadir'): echo 'selected'; endif; ?>>Hadir</option>
                <option value="telat"        <?php if(request('status')=='telat'): echo 'selected'; endif; ?>>Telat</option>
                <option value="tidak_hadir"  <?php if(request('status')=='tidak_hadir'): echo 'selected'; endif; ?>>Tidak Hadir</option>
                <option value="izin"         <?php if(request('status')=='izin'): echo 'selected'; endif; ?>>Izin</option>
                <option value="sakit"        <?php if(request('status')=='sakit'): echo 'selected'; endif; ?>>Sakit</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary" style="height:42px">
            <i class="fas fa-search"></i> Filter
        </button>
        <a href="<?php echo e(route('presensi.index')); ?>" class="btn btn-secondary" style="height:42px">
            <i class="fas fa-refresh"></i> Reset
        </a>
    </form>
</div>

<!-- Rekap cepat hari ini -->
<?php
    $totalGuru   = $presensis->count();
    $jmlHadir    = $presensis->where('status','hadir')->count();
    $jmlTelat    = $presensis->where('status','telat')->count();
    $jmlTidakHadir = $presensis->where('status','tidak_hadir')->count();
    $jmlIzin     = $presensis->where('status','izin')->count();
    $jmlSakit    = $presensis->where('status','sakit')->count();
?>
<div class="stats-grid" style="grid-template-columns:repeat(auto-fit,minmax(110px,1fr));margin-bottom:18px">
    <div class="stat-card total">
        <div class="stat-icon"><i class="fas fa-users"></i></div>
        <div class="stat-label">Total Guru</div>
        <div class="stat-value"><?php echo e($totalGuru); ?></div>
    </div>
    <div class="stat-card hadir">
        <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
        <div class="stat-label">Hadir</div>
        <div class="stat-value"><?php echo e($jmlHadir); ?></div>
    </div>
    <div class="stat-card telat">
        <div class="stat-icon"><i class="fas fa-clock"></i></div>
        <div class="stat-label">Terlambat</div>
        <div class="stat-value"><?php echo e($jmlTelat); ?></div>
    </div>
    <div class="stat-card absen">
        <div class="stat-icon"><i class="fas fa-user-xmark"></i></div>
        <div class="stat-label">Tidak Hadir</div>
        <div class="stat-value"><?php echo e($jmlTidakHadir); ?></div>
    </div>
    <div class="stat-card izin">
        <div class="stat-icon"><i class="fas fa-file-circle-check"></i></div>
        <div class="stat-label">Izin</div>
        <div class="stat-value"><?php echo e($jmlIzin); ?></div>
    </div>
    <div class="stat-card sakit">
        <div class="stat-icon"><i class="fas fa-kit-medical"></i></div>
        <div class="stat-label">Sakit</div>
        <div class="stat-value"><?php echo e($jmlSakit); ?></div>
    </div>
</div>

<!-- Input Manual -->
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-pen" style="color:#4f46e5"></i> Input Presensi Manual</h3>
        <button onclick="toggleManual()" class="btn btn-secondary btn-sm">
            <i class="fas fa-plus"></i> Tambah
        </button>
    </div>
    <form method="POST" action="<?php echo e(route('presensi.manual')); ?>" id="formManual" style="display:none">
        <?php echo csrf_field(); ?>
        <div class="form-row" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:12px;margin-bottom:12px">
            <div class="form-group" style="margin:0">
                <label>Guru</label>
                <select name="guru_id" class="form-control" id="manualGuruId" required>
                    <option value="">-- Pilih Guru --</option>
                    <?php $__currentLoopData = $gurus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($g->id); ?>"><?php echo e($g->nama); ?> (<?php echo e($g->id_pengguna); ?>)</option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="form-group" style="margin:0">
                <label>Tanggal</label>
                <input type="date" name="tanggal" id="manualTanggal" class="form-control"
                       value="<?php echo e($tanggal->format('Y-m-d')); ?>" required>
            </div>
            <div class="form-group" style="margin:0">
                <label>Status</label>
                <select name="status" class="form-control" required>
                    <option value="hadir">Hadir</option>
                    <option value="telat">Telat</option>
                    <option value="tidak_hadir">Tidak Hadir</option>
                    <option value="izin">Izin</option>
                    <option value="sakit">Sakit</option>
                </select>
            </div>
            <div class="form-group" style="margin:0">
                <label>Jam Masuk</label>
                <input type="time" name="jam_masuk" class="form-control">
            </div>
            <div class="form-group" style="margin:0">
                <label>Jam Pulang</label>
                <input type="time" name="jam_pulang" class="form-control">
            </div>
        </div>
        <div class="form-group">
            <label>Keterangan</label>
            <input type="text" name="keterangan" class="form-control" placeholder="Opsional...">
        </div>
        <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Simpan</button>
    </form>
</div>

<!-- Pengajuan Izin/Sakit Menunggu Persetujuan -->
<?php
    $menunggu = $presensis->filter(fn($p) => in_array($p->status, ['izin','sakit']) && ($p->approval_status ?? null) === 'menunggu');
?>
<?php if($menunggu->count() > 0): ?>
<div class="card" style="border:1.5px solid #fde68a;">
    <div class="card-header" style="background:linear-gradient(135deg,#fffbeb,#fef9c3);">
        <h3 style="color:#92400e;">
            <i class="fas fa-clock" style="color:#d97706"></i>
            Pengajuan Izin/Sakit Menunggu Persetujuan
        </h3>
        <span style="background:#fde68a;color:#92400e;font-size:.75rem;font-weight:700;padding:3px 10px;border-radius:6px;">
            <?php echo e($menunggu->count()); ?> pengajuan
        </span>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Guru</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th>Keterangan</th>
                    <th>Bukti</th>
                    <th>Aksi Persetujuan</th>
                </tr>
            </thead>
            <tbody>
            <?php $__currentLoopData = $menunggu; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if($p->id): ?>
            <tr>
                <td>
                    <strong><?php echo e($p->guru->nama ?? '-'); ?></strong><br>
                    <small style="color:#94a3b8;font-size:.75rem"><?php echo e($p->guru->id_pengguna ?? ''); ?></small>
                </td>
                <td style="font-weight:600"><?php echo e(\Carbon\Carbon::parse($p->tanggal)->isoFormat('D MMM Y')); ?></td>
                <td>
                    <span class="badge badge-<?php echo e($p->status); ?>" style="font-size:.78rem">
                        <?php echo e(ucfirst($p->status)); ?>

                    </span>
                </td>
                <td style="font-size:.83rem;color:#475569;max-width:200px;"><?php echo e($p->keterangan ?: '-'); ?></td>
                <td>
                    <?php if($p->bukti_file ?? null): ?>
                        <a href="<?php echo e(asset('storage/' . $p->bukti_file)); ?>" target="_blank"
                           style="color:#4f46e5;font-size:.8rem;display:flex;align-items:center;gap:4px;text-decoration:none;">
                            <i class="fas fa-file-arrow-down"></i> Lihat Bukti
                        </a>
                    <?php else: ?>
                        <span style="color:#94a3b8;font-size:.8rem">—</span>
                    <?php endif; ?>
                </td>
                <td>
                    <div style="display:flex;gap:6px;">
                        <form method="POST" action="<?php echo e(route('presensi.approval', $p)); ?>" style="display:inline">
                            <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                            <input type="hidden" name="approval_status" value="disetujui">
                            <button type="submit" class="btn btn-sm"
                                style="background:#dcfce7;color:#15803d;border:1.5px solid #86efac;font-size:.75rem;font-weight:700;padding:5px 10px;border-radius:7px;cursor:pointer;font-family:'Inter',sans-serif;"
                                onclick="return confirm('Setujui pengajuan <?php echo e($p->status); ?> <?php echo e($p->guru->nama ?? ""); ?>?')">
                                <i class="fas fa-check"></i> Setujui
                            </button>
                        </form>
                        <form method="POST" action="<?php echo e(route('presensi.approval', $p)); ?>" style="display:inline">
                            <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                            <input type="hidden" name="approval_status" value="ditolak">
                            <button type="submit" class="btn btn-sm"
                                style="background:#fee2e2;color:#dc2626;border:1.5px solid #fca5a5;font-size:.75rem;font-weight:700;padding:5px 10px;border-radius:7px;cursor:pointer;font-family:'Inter',sans-serif;"
                                onclick="return confirm('Tolak pengajuan <?php echo e($p->status); ?> <?php echo e($p->guru->nama ?? ""); ?>?')">
                                <i class="fas fa-xmark"></i> Tolak
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<!-- Tabel Presensi -->
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-table" style="color:#4f46e5"></i>
            Presensi — <?php echo e($tanggal->isoFormat('D MMMM Y')); ?>

        </h3>
        <span style="font-size:.82rem;color:#64748b"><?php echo e($presensis->count()); ?> guru</span>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>No</th><th>Guru</th><th>ID</th>
                    <th>Jam Masuk</th><th>Jam Pulang</th>
                    <th>Status</th><th>Metode</th><th>Telat</th>
                    <th>Approval</th>
                    <?php if(auth()->user()->isSuperAdmin()): ?>
                        <th>Edit Jam</th>
                    <?php endif; ?>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $presensis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php $isVirtual = !($p->id ?? false); ?>
            <tr style="<?php echo e($isVirtual ? 'background:#fafafa;opacity:.88;' : ''); ?>">
                <td><?php echo e($i + 1); ?></td>
                <td>
                    <strong><?php echo e($p->guru->nama ?? '-'); ?></strong>
                    <?php if($isVirtual): ?>
                        <span style="font-size:.68rem;background:#fee2e2;color:#dc2626;padding:2px 6px;border-radius:4px;margin-left:4px;font-weight:700;">
                            Belum Scan
                        </span>
                    <?php endif; ?>
                </td>
                <td style="font-size:.8rem;color:#94a3b8"><?php echo e($p->guru->id_pengguna ?? '-'); ?></td>
                <td><?php echo e($p->jam_masuk ?? '—'); ?></td>
                <td><?php echo e($p->jam_pulang ?? '—'); ?></td>
                <td>
                    <span class="badge badge-<?php echo e(str_replace('_','-',$p->status)); ?>">
                        <?php echo e(strtoupper(str_replace('_',' ',$p->status))); ?>

                    </span>
                </td>
                <td style="font-size:.8rem"><?php echo e(ucfirst($p->metode ?? '-')); ?></td>
                <td>
                    <?php if(($p->menit_telat ?? 0) > 0): ?>
                        <span style="color:#f59e0b;font-weight:700">+<?php echo e($p->menit_telat); ?> mnt</span>
                    <?php else: ?>
                        —
                    <?php endif; ?>
                </td>
                <td>
                    <?php if(in_array($p->status, ['izin','sakit'])): ?>
                        <?php
                            $apLabel = match($p->approval_status ?? null) {
                                'menunggu'  => ['#fef9c3','#a16207','Menunggu'],
                                'disetujui' => ['#dcfce7','#15803d','Disetujui'],
                                'ditolak'   => ['#fee2e2','#dc2626','Ditolak'],
                                default     => ['#f1f5f9','#94a3b8','—'],
                            };
                        ?>
                        <span style="background:<?php echo e($apLabel[0]); ?>;color:<?php echo e($apLabel[1]); ?>;font-size:.7rem;font-weight:700;padding:3px 8px;border-radius:5px;">
                            <?php echo e($apLabel[2]); ?>

                        </span>
                    <?php else: ?>
                        <span style="color:#94a3b8;font-size:.8rem">—</span>
                    <?php endif; ?>
                </td>
                <?php if(auth()->user()->isSuperAdmin()): ?>
                <td>
                    <?php if(!$isVirtual): ?>
                        <?php
                            $jmEdit = $p->jam_masuk ? \Carbon\Carbon::parse($p->jam_masuk)->format('H:i') : '';
                            $jpEdit = $p->jam_pulang ? \Carbon\Carbon::parse($p->jam_pulang)->format('H:i') : '';
                        ?>
                        <button type="button"
                            class="btn btn-secondary btn-sm"
                            title="Edit jam masuk & pulang"
                            onclick="bukaModalJam(<?php echo e($p->id); ?>, <?php echo json_encode($jmEdit, 15, 512) ?>, <?php echo json_encode($jpEdit, 15, 512) ?>, <?php echo json_encode($p->guru->nama ?? '-', 15, 512) ?>)">
                            <i class="fas fa-clock"></i> Jam
                        </button>
                    <?php else: ?>
                        <span style="color:#94a3b8;font-size:.8rem">—</span>
                    <?php endif; ?>
                </td>
                <?php endif; ?>
                <td>
                    <?php if($isVirtual): ?>
                        
                        <button type="button"
                            onclick="inputManualCepat(<?php echo e($p->guru_id); ?>, '<?php echo e($tanggal->format('Y-m-d')); ?>')"
                            class="btn btn-secondary btn-sm"
                            title="Input presensi manual untuk guru ini">
                            <i class="fas fa-pen"></i> Input
                        </button>
                    <?php else: ?>
                        
                        <form method="POST" action="<?php echo e(route('presensi.status', $p)); ?>" style="display:inline-flex;gap:4px">
                            <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                            <select name="status" class="form-control"
                                    style="width:auto;padding:4px 8px;font-size:.78rem">
                                <?php $__currentLoopData = ['hadir','telat','tidak_hadir','izin','sakit']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($s); ?>" <?php if($p->status == $s): echo 'selected'; endif; ?>>
                                        <?php echo e(ucfirst(str_replace('_',' ',$s))); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-check"></i>
                            </button>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="<?php echo e(auth()->user()->isSuperAdmin() ? 11 : 10); ?>" style="text-align:center;color:#94a3b8;padding:32px">
                    Belum ada data presensi untuk tanggal ini
                </td>
            </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if(auth()->user()->isSuperAdmin()): ?>
<div id="modalJamBackdrop" class="modal-jam-backdrop" style="display:none;" onclick="if(event.target===this) tutupModalJam()"></div>
<div id="modalJam" class="modal-jam" role="dialog" aria-modal="true" aria-labelledby="modalJamTitle" style="display:none;">
    <div class="modal-jam-inner">
        <h3 id="modalJamTitle" style="margin:0 0 12px;font-size:1.05rem;color:#1a2e1a;">
            <i class="fas fa-clock" style="color:hsl(145,60%,28%)"></i> Edit Jam Presensi
        </h3>
        <p id="modalJamGuru" style="margin:0 0 16px;font-size:.88rem;color:#64748b;"></p>
        <form id="formJamManual" method="POST" action="">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PATCH'); ?>
            <div class="form-group" style="margin-bottom:12px">
                <label for="modal_jam_masuk">Jam Masuk</label>
                <input type="time" name="jam_masuk" id="modal_jam_masuk" class="form-control" required>
            </div>
            <div class="form-group" style="margin-bottom:16px">
                <label for="modal_jam_pulang">Jam Pulang</label>
                <input type="time" name="jam_pulang" id="modal_jam_pulang" class="form-control" required>
            </div>
            <div style="display:flex;gap:10px;justify-content:flex-end">
                <button type="button" class="btn btn-secondary" onclick="tutupModalJam()">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>
<style>
.modal-jam-backdrop{position:fixed;inset:0;background:rgba(15,23,42,.45);z-index:1040;}
.modal-jam{position:fixed;inset:0;z-index:1050;display:flex;align-items:center;justify-content:center;padding:20px;pointer-events:none;}
.modal-jam-inner{pointer-events:auto;background:#fff;border-radius:16px;padding:24px 28px;max-width:400px;width:100%;box-shadow:0 24px 48px rgba(0,0,0,.2);}
</style>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
<?php if(auth()->user()->isSuperAdmin()): ?>
function jamManualActionUrl(id) {
    return <?php echo json_encode(url('/'), 15, 512) ?> + '/presensi/' + id + '/jam-manual';
}
function bukaModalJam(id, jamMasuk, jamPulang, namaGuru) {
    var form = document.getElementById('formJamManual');
    var bd = document.getElementById('modalJamBackdrop');
    var md = document.getElementById('modalJam');
    if (!form || !bd || !md) return;
    form.action = jamManualActionUrl(id);
    document.getElementById('modal_jam_masuk').value = jamMasuk || '';
    document.getElementById('modal_jam_pulang').value = jamPulang || '';
    document.getElementById('modalJamGuru').textContent = namaGuru || '';
    bd.style.display = 'block';
    md.style.display = 'flex';
    document.body.style.overflow = 'hidden';
    document.getElementById('modal_jam_masuk').focus();
}
function tutupModalJam() {
    var bd = document.getElementById('modalJamBackdrop');
    var md = document.getElementById('modalJam');
    if (bd) bd.style.display = 'none';
    if (md) md.style.display = 'none';
    document.body.style.overflow = '';
}
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && document.getElementById('modalJam') && document.getElementById('modalJam').style.display === 'flex') {
        tutupModalJam();
    }
});
<?php endif; ?>
function toggleManual() {
    const f = document.getElementById('formManual');
    f.style.display = f.style.display === 'none' ? 'block' : 'none';
}

// Pre-fill form manual dan scroll ke sana
function inputManualCepat(guruId, tanggal) {
    const form = document.getElementById('formManual');
    form.style.display = 'block';
    document.getElementById('manualGuruId').value    = guruId;
    document.getElementById('manualTanggal').value   = tanggal;
    form.scrollIntoView({ behavior: 'smooth', block: 'center' });
    document.getElementById('manualGuruId').focus();
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\laravel-presensi-guru\resources\views\presensi\index.blade.php ENDPATH**/ ?>