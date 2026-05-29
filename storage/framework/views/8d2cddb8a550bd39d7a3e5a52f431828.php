<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akses Ditolak — Madrasah Aliyah Attaqwa</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body{font-family:'Inter',sans-serif;min-height:100vh;display:flex;align-items:center;justify-content:center;
            background:linear-gradient(135deg,#1e1b4b,#312e81);}
        .box{background:#fff;border-radius:20px;padding:48px 40px;text-align:center;max-width:420px;width:90%;}
        .icon{width:72px;height:72px;background:#fee2e2;border-radius:50%;
            display:flex;align-items:center;justify-content:center;margin:0 auto 20px;font-size:2rem;color:#dc2626;}
        h1{font-size:1.5rem;font-weight:800;color:#1e293b;margin-bottom:8px;}
        p{color:#64748b;margin-bottom:24px;line-height:1.6;}
        a{background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;padding:10px 24px;
            border-radius:10px;text-decoration:none;font-weight:700;font-size:.9rem;}
    </style>
</head>
<body>
<div class="box">
    <div class="icon"><i class="fas fa-ban"></i></div>
    <h1>Akses Ditolak</h1>
    <p>Anda tidak memiliki izin untuk mengakses halaman ini.<br>
    Silakan kembali ke halaman yang sesuai dengan role Anda.</p>
    <a href="<?php echo e(url('/')); ?>"><i class="fas fa-home"></i> Kembali ke Dashboard</a>
</div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\laravel-presensi-guru\resources\views\errors\403.blade.php ENDPATH**/ ?>