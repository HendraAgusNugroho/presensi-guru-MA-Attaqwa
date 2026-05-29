# Panduan Deploy & Hosting — Sistem Presensi Guru MA Attaqwa

Panduan langkah demi langkah untuk menaikkan aplikasi Laravel ke hosting production (cPanel / shared hosting / VPS).

> **Demo cepat di cloud (gratis/trial):** lihat **[PANDUAN_RAILWAY.md](PANDUAN_RAILWAY.md)**

---

## Ringkasan singkat

| Item | Nilai |
|------|--------|
| PHP | **8.1 atau lebih baru** |
| Database | **MySQL / MariaDB** |
| Document root | Folder **`public/`** |
| Framework | Laravel 10 |

---

## FASE 1 — Persiapan di komputer lokal

### Langkah 1.1: Pastikan project jalan lokal

```bash
cd c:\xampp\htdocs\laravel-presensi-guru
composer install
php artisan migrate
php artisan db:seed
php artisan serve
```

Buka `http://127.0.0.1:8000` dan tes login.

### Langkah 1.2: Buat arsip deploy (tanpa .env)

**Windows PowerShell:**

```powershell
cd c:\xampp\htdocs\laravel-presensi-guru
.\scripts\build-deploy.ps1
```

Hasil: file **`deploy-presensi-guru.zip`** di root project.

> ZIP ini **tidak** berisi `.env`, `.git`, atau `node_modules`.

### Langkah 1.3: Install dependency production (jika hosting tanpa SSH)

```bash
composer install --no-dev --optimize-autoloader
```

Setelah selesai, folder **`vendor/`** ikut di-upload (atau jalankan perintah yang sama lewat SSH di server).

---

## FASE 2 — Siapkan hosting

### Langkah 2.1: Cek spesifikasi hosting

Pastikan panel hosting menyediakan:

- [ ] PHP **≥ 8.1**
- [ ] Ekstensi: `openssl`, `pdo_mysql`, `mbstring`, `tokenizer`, `xml`, `ctype`, `json`, `fileinfo`, `gd` atau `imagick`
- [ ] MySQL / MariaDB
- [ ] SSL/HTTPS (Let's Encrypt — biasanya gratis di cPanel)
- [ ] `mod_rewrite` aktif (Apache) atau konfigurasi Nginx untuk Laravel

### Langkah 2.2: Buat database MySQL

Di **cPanel → MySQL Databases** (atau phpMyAdmin):

1. Buat database, contoh: `u123_presensi`
2. Buat user MySQL + password kuat
3. Beri user **ALL PRIVILEGES** ke database tersebut
4. Catat: **nama DB**, **user**, **password**, **host** (biasanya `localhost`)

---

## FASE 3 — Upload file ke server

### Opsi A — Shared hosting / cPanel (tanpa SSH)

1. Upload **`deploy-presensi-guru.zip`** ke folder di luar `public_html` (mis. `/home/username/presensi-guru/`)
2. Extract ZIP di File Manager
3. Atur **document root** domain/subdomain ke:
   ```
   /home/username/presensi-guru/public
   ```
   Bukan ke root folder Laravel.

### Opsi B — VPS dengan SSH

```bash
# Di server
cd /var/www
unzip deploy-presensi-guru.zip -d presensi-guru
cd presensi-guru
```

---

## FASE 4 — Konfigurasi `.env` di server

### Langkah 4.1: Salin environment file

```bash
cp .env.example .env
```

Atau di cPanel File Manager: salin `.env.example` → rename jadi `.env`

### Langkah 4.2: Isi `.env` production

```env
APP_NAME="Presensi Guru At-Taqwa"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://presensi.sekolahanda.sch.id

APP_KEY=
# Isi dengan: php artisan key:generate

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=u123_presensi
DB_USERNAME=u123_user
DB_PASSWORD=password_kuat_anda

SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_SECURE_COOKIE=true

CACHE_DRIVER=file
QUEUE_CONNECTION=sync

MAIL_MAILER=smtp
MAIL_HOST=mail.sekolahanda.sch.id
MAIL_PORT=587
MAIL_USERNAME=noreply@sekolahanda.sch.id
MAIL_PASSWORD=password_email
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@sekolahanda.sch.id
MAIL_FROM_NAME="Sistem Presensi MA Attaqwa"

FINGERPRINT_API_KEY=
SEED_SUPER_ADMIN_PASSWORD=
SEED_ADMIN_PASSWORD=
```

### Langkah 4.3: Generate APP_KEY

```bash
php artisan key:generate
```

### Langkah 4.4: Generate API key fingerprint (min. 32 karakter)

```bash
php -r "echo bin2hex(random_bytes(16));"
```

Salin hasilnya ke `FINGERPRINT_API_KEY=` di `.env`

### Langkah 4.5: SSL belum aktif?

Jika domain **belum** pakai HTTPS, sementara set:

```env
SESSION_SECURE_COOKIE=false
```

Setelah SSL aktif, ubah kembali ke `true`.

---

## FASE 5 — Perintah artisan di server

Jalankan di folder root project (via **SSH** atau **Terminal cPanel**):

```bash
# 1. Dependency (jika belum)
composer install --no-dev --optimize-autoloader

# 2. Database
php artisan migrate --force

# 3. Data awal (isi password di .env dulu!)
php artisan db:seed --force

# 4. Storage link (upload foto guru)
php artisan storage:link

# 5. Permission (Linux)
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# 6. Cache production
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

**Atau pakai script otomatis (Linux/VPS):**

```bash
chmod +x scripts/deploy-server.sh
./scripts/deploy-server.sh
```

---

## FASE 6 — Verifikasi setelah deploy

### Checklist fungsional

- [ ] `https://domainanda.com/login` — halaman login tampil
- [ ] Login super admin / admin berhasil
- [ ] Dashboard — grafik Chart.js tampil (tidak kosong)
- [ ] Menu Scan QR Code — kamera/scanner jalan
- [ ] Edit jadwal (super admin) → toast sukses
- [ ] Upload foto guru berhasil

### Checklist keamanan

- [ ] `APP_DEBUG=false` — error tidak menampilkan stack trace
- [ ] Akses `https://domainanda.com/.env` → **403/404**
- [ ] `POST /api/fingerprint/sync` tanpa header `X-Api-Key` → **401**
- [ ] Ganti password semua akun setelah login pertama

### Tes API fingerprint

```bash
curl -X POST https://domainanda.com/api/fingerprint/sync \
  -H "Content-Type: application/json" \
  -H "X-Api-Key: KUNCI_ANDA_DARI_ENV" \
  -d "{\"id_fingerprint\":\"FP001\",\"waktu_scan\":\"2026-05-28 07:05:00\"}"
```

---

## FASE 7 — Update versi berikutnya

```bash
php artisan down
# Upload file baru (tanpa timpa .env)
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
php artisan up
```

---

## Konfigurasi khusus per jenis hosting

### cPanel — Document root

1. **Domains** → pilih domain → **Document Root**
2. Arahkan ke: `/presensi-guru/public`

### Nginx (VPS)

```nginx
server {
    listen 80;
    server_name presensi.sekolahanda.sch.id;
    root /var/www/presensi-guru/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### Cron job (opsional — auto alpha)

```cron
0 18 * * 1-5 cd /var/www/presensi-guru && php artisan presensi:auto-alpha >> /dev/null 2>&1
```

*(Sesuaikan jika ada command scheduler di project.)*

---

## Troubleshooting

| Masalah | Solusi |
|---------|--------|
| **500 Internal Server Error** | Cek `storage/logs/laravel.log`; pastikan `storage/` & `bootstrap/cache/` writable |
| **Login gagal terus** | Cek `SESSION_SECURE_COOKIE` vs SSL; cek `APP_URL` sesuai domain |
| **Grafik dashboard kosong** | Buka DevTools → Console; pastikan Chart.js tidak diblokir CSP/hosting |
| **Scan QR tidak jalan** | Izinkan akses kamera (HTTPS wajib); cek CSP di `.htaccess` |
| **CSS/JS tidak load** | Pastikan document root = `public/`; jalankan `php artisan storage:link` |
| **Permission denied** | `chmod -R 775 storage bootstrap/cache` |

---

## File pendukung

| File | Fungsi |
|------|--------|
| `PANDUAN_RAILWAY.md` | Demo deploy di Railway (cloud) |
| `DEPLOY_KEAMANAN.md` | Checklist keamanan singkat |
| `CARA_MENJALANKAN.txt` | Panduan lokal & update migration |
| `scripts/build-deploy.ps1` | Buat ZIP deploy (Windows) |
| `scripts/deploy-server.sh` | Optimasi cache di server (Linux) |

---

## Kontak & maintenance

Setelah go-live:

1. Backup database mingguan (cPanel → Backup)
2. Jangan commit/upload file `.env`
3. Pantau `storage/logs/laravel.log` jika ada keluhan user
