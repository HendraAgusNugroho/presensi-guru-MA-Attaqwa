# Checklist Keamanan Sebelum Deploy

> **Panduan lengkap step-by-step:** lihat [`PANDUAN_DEPLOY_HOSTING.md`](PANDUAN_DEPLOY_HOSTING.md)

## Di server hosting (wajib)

1. **Jangan upload file `.env` dari ZIP/lokal.** Salin `.env.example` → `.env` di server, lalu isi:
   - `APP_ENV=production`
   - `APP_DEBUG=false`
   - `APP_KEY=` → jalankan `php artisan key:generate`
   - `FINGERPRINT_API_KEY=` → string acak min. 32 karakter
   - `SEED_SUPER_ADMIN_PASSWORD=` dan `SEED_ADMIN_PASSWORD=` → password kuat (min. 12 karakter) sebelum `db:seed`

2. **Document root** harus mengarah ke folder `public/` (bukan root proyek).

3. **Folder `vendor/`** — jalankan di komputer lokal:
   ```bash
   composer install --no-dev --optimize-autoloader
   ```
   Lalu upload folder `vendor/` jika hosting tidak punya SSH/Composer.

4. **Setelah SSL aktif**, HSTS otomatis aktif pada request HTTPS (sudah dikonfigurasi di `public/.htaccess`).

5. **Ganti password** semua akun (terutama KS001, ADMIN01, dan guru) setelah login pertama.

## Membuat arsip deploy (tanpa .env)

Windows PowerShell dari root proyek:

```powershell
.\scripts\build-deploy.ps1
```

Arsip `deploy-presensi-guru.zip` **tidak** menyertakan `.env`, `.git`, `node_modules`, atau log.

## Verifikasi cepat

- [ ] Halaman login tidak menampilkan password default
- [ ] `POST /api/fingerprint/sync` tanpa API key → 401 atau 503
- [ ] Akses `https://domainanda.com/.env` → 403/404
- [ ] Error aplikasi tidak menampilkan stack trace (APP_DEBUG=false)
