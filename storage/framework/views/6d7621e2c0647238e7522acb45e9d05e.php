<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta name="robots" content="noindex, nofollow">
    <meta name="description" content="<?php echo $__env->yieldContent('meta_description', 'Sistem Presensi Guru MA Attaqwa — kelola kehadiran, laporan, dan rekapitulasi guru secara digital.'); ?>">
    <meta name="theme-color" content="#2d6a4f">
    <meta property="og:title" content="<?php echo $__env->yieldContent('title', 'Dashboard'); ?> — Presensi Guru MA Attaqwa">
    <meta property="og:description" content="<?php echo $__env->yieldContent('meta_description', 'Sistem Presensi Guru MA Attaqwa — kelola kehadiran, laporan, dan rekapitulasi guru secara digital.'); ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo e(config('app.url')); ?>">
    <title><?php echo $__env->yieldContent('title', 'Dashboard'); ?> — Presensi Guru MA Attaqwa</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" as="style"
          href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
          onload="this.onload=null;this.rel='stylesheet'">
    <noscript>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    </noscript>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo e(asset('css/app.css')); ?>">
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body>
<a href="#main-content" class="skip-link">Langsung ke konten utama</a>

<!-- Preloader -->
<div class="preloader" id="preloader">
    <div class="spinner"></div>
    <span style="font-size:.88rem;color:#5a7a5a;font-weight:600">Memuat data...</span>
</div>

<!-- Overlay (mobile) -->
<div class="overlay" id="overlay" onclick="closeSidebar()"></div>

<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
    <!-- Brand / Logo -->
    <div class="sidebar-brand" style="padding:14px 14px 10px;">
        <div style="display:flex;align-items:center;gap:10px;">
            <div style="width:44px;height:44px;border-radius:10px;overflow:hidden;background:#fff;flex-shrink:0;box-shadow:0 2px 8px rgba(0,0,0,.25);">
                <img src="<?php echo e(asset('images/logo-sekolah.png')); ?>" alt="Logo MA Attaqwa"
                     loading="lazy"
                     style="width:100%;height:100%;object-fit:contain;">
            </div>
            <div style="min-width:0;">
                <div style="color:#fff;font-size:.95rem;font-weight:800;line-height:1.25;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">MA ATTAQWA</div>
                <div style="color:rgba(255,255,255,.65);font-size:.76rem;white-space:nowrap;font-weight:500;">YPIA Daarul Mu'min</div>
            </div>
        </div>
    </div>

    <!-- Nama Sekolah -->
    <div class="sidebar-school">
        <strong>YPIA DAARUL MU'MIN</strong>
        Madrasah Aliyah Attaqwa<br>Benda Tangerang
    </div>

    <!-- User Info -->
    <?php
        $roleIcon = match(auth()->user()->role) {
            'super_admin' => 'fa-shield-halved',
            'admin'       => 'fa-user-gear',
            'guru'        => 'fa-chalkboard-user',
            default       => 'fa-user',
        };
        $badgeClass = match(auth()->user()->role) {
            'super_admin' => 'super-admin',
            'admin'       => 'admin',
            default       => 'guru',
        };
    ?>
    <div style="margin:0 12px 10px;padding:10px 12px;border-radius:10px;background:rgba(255,255,255,.08);display:flex;align-items:center;gap:10px;flex-shrink:0;">
        <div style="width:34px;height:34px;border-radius:10px;background:rgba(255,255,255,.12);
            display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <i class="fas <?php echo e($roleIcon); ?>" style="color:hsl(48,96%,70%);font-size:.85rem"></i>
        </div>
        <div style="overflow:hidden;min-width:0;">
            <div style="color:#fff;font-size:.82rem;font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                <?php echo e(auth()->user()->name); ?>

            </div>
            <span class="role-badge <?php echo e($badgeClass); ?>" style="margin-top:3px">
                <i class="fas <?php echo e($roleIcon); ?>"></i>
                <?php echo e(auth()->user()->role_label); ?>

            </span>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="sidebar-nav">

        <?php if(auth()->user()->isGuru()): ?>
            <div class="nav-section">Presensi Saya</div>
            <a href="<?php echo e(route('guru.barcode_saya')); ?>"
               class="nav-link <?php if(request()->routeIs('guru.barcode_saya')): ?> active <?php endif; ?>">
                <i class="fas fa-qrcode"></i> QR Code Saya
            </a>
            <a href="<?php echo e(route('presensi.saya')); ?>"
               class="nav-link <?php if(request()->routeIs('presensi.saya')): ?> active <?php endif; ?>">
                <i class="fas fa-calendar-check"></i> Riwayat Presensi
            </a>
            <a href="<?php echo e(route('presensi.izin_sakit')); ?>"
               class="nav-link <?php if(request()->routeIs('presensi.izin_sakit')): ?> active <?php endif; ?>">
                <i class="fas fa-file-medical"></i> Izin / Sakit
            </a>
        <?php endif; ?>

        <?php if(auth()->user()->isStaff()): ?>
            <div class="nav-section">Utama</div>
            <a href="<?php echo e(route('dashboard')); ?>"
               class="nav-link <?php if(request()->routeIs('dashboard')): ?> active <?php endif; ?>">
                <i class="fas fa-chart-line"></i> Dashboard
            </a>

            <div class="nav-divider"></div>
            <div class="nav-section">Presensi</div>
            <a href="<?php echo e(route('presensi.scan')); ?>"
               class="nav-link <?php if(request()->routeIs('presensi.scan')): ?> active <?php endif; ?>">
                <i class="fas fa-qrcode"></i> Scan QR Code
            </a>
            <a href="<?php echo e(route('presensi.index')); ?>"
               class="nav-link <?php if(request()->routeIs('presensi.index')): ?> active <?php endif; ?>">
                <i class="fas fa-list-check"></i> Data Presensi
            </a>

            <div class="nav-divider"></div>
            <div class="nav-section">Data</div>
            <a href="<?php echo e(route('guru.index')); ?>"
               class="nav-link <?php if(request()->routeIs('guru.*') && !request()->routeIs('guru.barcode_saya')): ?> active <?php endif; ?>">
                <i class="fas fa-chalkboard-teacher"></i> Data Guru
            </a>
            <a href="<?php echo e(route('fingerprint.import')); ?>"
               class="nav-link <?php if(request()->routeIs('fingerprint.*')): ?> active <?php endif; ?>">
                <i class="fas fa-fingerprint"></i> Import Fingerprint
            </a>
            <a href="<?php echo e(route('jadwal_guru.index')); ?>"
               class="nav-link <?php if(request()->routeIs('jadwal_guru.*')): ?> active <?php endif; ?>">
                <i class="fas fa-calendar-days"></i> Jadwal Guru
            </a>

            <div class="nav-divider"></div>
            <div class="nav-section">Laporan</div>
            <a href="<?php echo e(route('laporan.index')); ?>"
               class="nav-link <?php if(request()->routeIs('laporan.*')): ?> active <?php endif; ?>">
                <i class="fas fa-chart-bar"></i> Laporan Presensi
            </a>
        <?php endif; ?>

        <?php if(auth()->user()->isSuperAdmin()): ?>
            <div class="nav-divider"></div>
            <div class="nav-section" style="color:#fca5a5;">
                <i class="fas fa-shield-halved" style="margin-right:4px;font-size:.65rem"></i>
                Super Admin
            </div>
            <a href="<?php echo e(route('pengguna.index')); ?>"
               class="nav-link <?php if(request()->routeIs('pengguna.*')): ?> active <?php endif; ?>">
                <i class="fas fa-users-gear"></i> Manajemen Pengguna
                <span style="margin-left:auto;font-size:.6rem;background:#dc2626;color:#fff;padding:2px 6px;border-radius:4px;font-weight:700">SA</span>
            </a>
        <?php endif; ?>
    </nav>

    <!-- Bottom actions -->
    <div style="padding:12px;border-top:1px solid rgba(255,255,255,.08);flex-shrink:0;">
        <a href="<?php echo e(route('profil.index')); ?>"
           style="display:flex;align-items:center;gap:8px;background:rgba(255,255,255,.07);color:rgba(255,255,255,.8);
                  padding:9px 12px;border-radius:8px;font-size:.82rem;margin-bottom:8px;text-decoration:none;
                  transition:background .2s;"
           onmouseover="this.style.background='rgba(255,255,255,.12)'"
           onmouseout="this.style.background='rgba(255,255,255,.07)'">
            <i class="fas fa-gear"></i> Profil &amp; Password
        </a>
        <form method="POST" action="<?php echo e(route('logout')); ?>">
            <?php echo csrf_field(); ?>
            <button type="submit" class="btn-logout" style="width:100%;text-align:center;justify-content:center;">
                <i class="fas fa-right-from-bracket"></i> Keluar
            </button>
        </form>
    </div>
</aside>

<!-- Main Content -->
<main class="main-content">
    <!-- Topbar -->
    <div class="topbar">
        <div style="display:flex;align-items:center;gap:12px;">
            <button class="hamburger-btn" id="hamburger-btn" onclick="toggleSidebar()" aria-label="Menu">
                <span></span><span></span><span></span>
            </button>
            <span class="topbar-title"><?php echo $__env->yieldContent('title', 'Dashboard'); ?></span>
        </div>
        <div class="topbar-right">
            <span class="hide-xs" style="font-size:.8rem;color:#5a7a5a;display:flex;align-items:center;gap:6px;">
                <i class="fas fa-calendar" style="color:hsl(145,60%,35%)"></i>
                <?php echo e(now()->isoFormat('dddd, D MMMM Y')); ?>

            </span>
            <span class="topbar-role-badge topbar-role-badge--<?php echo e(auth()->user()->role); ?>">
                <i class="fas <?php echo e($roleIcon); ?>"></i>
                <?php echo e(auth()->user()->role_label); ?>

            </span>
        </div>
    </div>

    <!-- Import Errors List -->
    <?php if(session('errors_import') && count(session('errors_import')) > 0): ?>
    <div style="margin:12px 24px 0;">
        <div class="alert alert-warning" style="flex-direction:column;align-items:flex-start;gap:6px;">
            <div style="display:flex;align-items:center;gap:8px;font-weight:700;">
                <i class="fas fa-triangle-exclamation"></i> Detail Error Import:
            </div>
            <ul style="margin-left:20px;font-size:.85rem;">
                <?php $__currentLoopData = session('errors_import'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($err); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    </div>
    <?php endif; ?>

    
    <div id="toast-container" style="position:fixed;top:20px;right:20px;z-index:9999;display:flex;flex-direction:column;gap:10px;pointer-events:none"></div>
    <style>
    .toast-item {
        pointer-events: auto;
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 280px;
        max-width: 400px;
        padding: 14px 16px;
        border-radius: 10px;
        color: #fff;
        font-size: .88rem;
        font-weight: 600;
        box-shadow: 0 8px 24px rgba(0,0,0,.18);
        opacity: 1;
        transform: translateX(0);
        transition: opacity .4s ease, transform .4s ease;
    }
    .toast-item.toast-fade-out {
        opacity: 0;
        transform: translateX(40px);
    }
    .toast-item i.toast-icon { font-size: 1.15rem; flex-shrink: 0; }
    .toast-item span { flex: 1; line-height: 1.4; }
    .toast-close {
        background: none;
        border: none;
        color: rgba(255,255,255,.75);
        font-size: 1.2rem;
        cursor: pointer;
        padding: 0 0 0 4px;
        line-height: 1;
        flex-shrink: 0;
    }
    .toast-close:hover { color: #fff; }
    </style>
    <script>
    function showToast(message, type) {
        var colors = {
            success: 'hsl(145,60%,35%)',
            error:   '#ef4444',
            warning: '#f59e0b',
            info:    '#3b82f6'
        };
        var icons = {
            success: 'fa-circle-check',
            error:   'fa-circle-xmark',
            warning: 'fa-triangle-exclamation',
            info:    'fa-circle-info'
        };
        type = type || 'info';
        var container = document.getElementById('toast-container');
        if (!container) return;

        var toast = document.createElement('div');
        toast.className = 'toast-item';
        toast.style.background = colors[type] || colors.info;

        var icon = document.createElement('i');
        icon.className = 'fas ' + (icons[type] || icons.info) + ' toast-icon';

        var text = document.createElement('span');
        text.textContent = message;

        var closeBtn = document.createElement('button');
        closeBtn.type = 'button';
        closeBtn.className = 'toast-close';
        closeBtn.setAttribute('aria-label', 'Tutup');
        closeBtn.textContent = '\u00d7';

        toast.appendChild(icon);
        toast.appendChild(text);
        toast.appendChild(closeBtn);
        container.appendChild(toast);

        var dismissed = false;
        function dismiss() {
            if (dismissed) return;
            dismissed = true;
            toast.classList.add('toast-fade-out');
            setTimeout(function() { toast.remove(); }, 400);
        }

        closeBtn.addEventListener('click', dismiss);
        setTimeout(dismiss, 3000);
    }
    </script>
    <?php
        $appFlash = array_filter([
            'success' => session('success'),
            'error'   => session('error'),
            'warning' => session('warning'),
            'info'    => session('info'),
        ], fn ($v) => filled($v));
    ?>
    <?php if(!empty($appFlash)): ?>
    <script type="application/json" id="app-flash-data"><?php echo json_encode($appFlash, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?></script>
    <?php endif; ?>

    <div class="content-area" id="main-content"><?php echo $__env->yieldContent('content'); ?></div>
</main>

<script>
window.addEventListener('load', function(){
    document.getElementById('preloader').classList.remove('show');
});
document.addEventListener('DOMContentLoaded', function(){
    document.querySelectorAll('form[data-loading]').forEach(function(f){
        f.addEventListener('submit', function(){
            document.getElementById('preloader').classList.add('show');
        });
    });

    var flashNode = document.getElementById('app-flash-data');
    if (flashNode && typeof showToast === 'function') {
        try {
            var flash = JSON.parse(flashNode.textContent);
            Object.keys(flash).forEach(function(type) {
                showToast(flash[type], type);
            });
        } catch (e) {}
    }
});

function toggleSidebar() {
    var sidebar  = document.getElementById('sidebar');
    var overlay  = document.getElementById('overlay');
    var btn      = document.getElementById('hamburger-btn');
    var isOpen   = sidebar.classList.contains('open');
    if (isOpen) {
        sidebar.classList.remove('open');
        overlay.classList.remove('show');
        btn.classList.remove('active');
        document.body.style.overflow = '';
    } else {
        sidebar.classList.add('open');
        overlay.classList.add('show');
        btn.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

function closeSidebar() {
    var sidebar = document.getElementById('sidebar');
    var overlay = document.getElementById('overlay');
    var btn     = document.getElementById('hamburger-btn');
    sidebar.classList.remove('open');
    overlay.classList.remove('show');
    btn.classList.remove('active');
    document.body.style.overflow = '';
}

// Close sidebar on resize to desktop
window.addEventListener('resize', function(){
    if (window.innerWidth > 768) { closeSidebar(); }
});
</script>
<?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\laravel-presensi-guru\resources\views\layouts\app.blade.php ENDPATH**/ ?>