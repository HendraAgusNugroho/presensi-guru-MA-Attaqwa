# Demo / Deploy di Railway — Step by Step

Railway cocok untuk **demo online** (gratis tier terbatas). Aplikasi ini **bisa** dijalankan di Railway dengan MySQL.

> **Catatan demo:** Upload foto guru disimpan di disk server Railway — **bisa hilang** saat redeploy. Untuk production sekolah, pakai hosting VPS/cPanel (`PANDUAN_DEPLOY_HOSTING.md`).

---

## Yang kamu butuhkan

- Akun [railway.app](https://railway.app) (login GitHub)
- Project sudah di folder ini
- **Opsional:** akun GitHub untuk deploy otomatis dari repo

---

## METODE A — Deploy dari GitHub (disarankan)

### Langkah 1: Push project ke GitHub

1. Buat repository baru di GitHub (mis. `presensi-guru`)
2. Di folder project:

```powershell
cd c:\xampp\htdocs\laravel-presensi-guru
git init
git add .
git commit -m "Initial commit - presensi guru"
git branch -M main
git remote add origin https://github.com/USERNAME/presensi-guru.git
git push -u origin main
```

> Jangan commit file `.env` (sudah di `.gitignore`).

---

### Langkah 2: Buat project di Railway

1. Buka [railway.app](https://railway.app) → **Login**
2. Klik **New Project**
3. Pilih **Deploy from GitHub repo**
4. Pilih repository `presensi-guru`
5. Railway mulai build otomatis

---

### Langkah 3: Tambah database MySQL

1. Di project Railway, klik **+ New**
2. Pilih **Database** → **MySQL**
3. Tunggu status MySQL **Active**

---

### Langkah 4: Hubungkan MySQL ke service Laravel

1. Klik service **web** (nama repo kamu)
2. Tab **Variables**
3. Klik **+ New Variable** → **Add Variable Reference** (atau **Connect** dari MySQL)
4. Pilih service **MySQL** dan tambahkan referensi:

| Variable di service Web | Referensi             |
| ----------------------- | --------------------- |
| `MYSQLHOST`             | MySQL → MYSQLHOST     |
| `MYSQLPORT`             | MySQL → MYSQLPORT     |
| `MYSQLUSER`             | MySQL → MYSQLUSER     |
| `MYSQLPASSWORD`         | MySQL → MYSQLPASSWORD |
| `MYSQLDATABASE`         | MySQL → MYSQLDATABASE |

_(Railway bisa menambahkan ini otomatis saat "Connect" database ke service.)_

---

### Langkah 5: Set environment variables (Web service)

Di tab **Variables** service web, tambahkan:

| Variable                    | Nilai                             |
| --------------------------- | --------------------------------- |
| `APP_NAME`                  | `Presensi Guru At-Taqwa`          |
| `APP_ENV`                   | `production`                      |
| `APP_DEBUG`                 | `false`                           |
| `APP_KEY`                   | _(lihat langkah 5b)_              |
| `APP_URL`                   | _(lihat langkah 6 — URL Railway)_ |
| `DB_CONNECTION`             | `mysql`                           |
| `SESSION_DRIVER`            | `file`                            |
| `SESSION_SECURE_COOKIE`     | `true`                            |
| `CACHE_DRIVER`              | `file`                            |
| `MAIL_MAILER`               | `log`                             |
| `FINGERPRINT_API_KEY`       | _(32+ karakter acak)_             |
| `SEED_SUPER_ADMIN_PASSWORD` | `DemoKS2026!`                     |
| `SEED_ADMIN_PASSWORD`       | `DemoAdmin2026!`                  |
| `RUN_DB_SEED`               | `true` _(hanya deploy pertama)_   |

#### Langkah 5b: Generate APP_KEY

Di komputer lokal:

```powershell
cd c:\xampp\htdocs\laravel-presensi-guru
php artisan key:generate --show
```

Salin output `base64:...` ke variable **`APP_KEY`** di Railway.

#### Generate FINGERPRINT_API_KEY

```powershell
php -r "echo bin2hex(random_bytes(16));"
```

Salin ke **`FINGERPRINT_API_KEY`**.

---

### Langkah 6: Dapatkan URL publik

1. Klik service **web** → tab **Settings**
2. Bagian **Networking** → **Generate Domain**
3. Railway memberi URL seperti: `https://presensi-guru-production.up.railway.app`
4. Copy URL itu ke variable **`APP_URL`** (tanpa slash di akhir)
5. Klik **Redeploy** (atau push commit baru) agar `APP_URL` terbaca

---

### Langkah 7: Tunggu deploy sukses

1. Tab **Deployments** → status **SUCCESS**
2. Tab **Logs** — pastikan ada:
   - `Migrating database`
   - `Server on http://0.0.0.0:xxxx`

---

### Langkah 8: Login demo

Buka `APP_URL` di browser → `/login`

| Role        | ID               | Password (jika pakai seed di atas) |
| ----------- | ---------------- | ---------------------------------- |
| Super Admin | `KS001`          | `DemoKS2026!`                      |
| Admin       | `ADMIN01`        | `DemoAdmin2026!`                   |
| Guru        | `A`, `B`, `C`, … | Sama dengan ID guru                |

> Setelah login pertama, ganti password lewat menu **Profil**.

---

### Langkah 9: Matikan seed otomatis (penting)

Setelah data awal masuk, ubah di Railway Variables:

```
RUN_DB_SEED=false
```

Lalu **Redeploy** — agar tidak seed ulang setiap restart.

---

## METODE B — Deploy pakai Railway CLI (tanpa GitHub)

### Langkah 1: Install CLI

```powershell
npm install -g @railway/cli
railway login
```

### Langkah 2: Init & deploy

```powershell
cd c:\xampp\htdocs\laravel-presensi-guru
railway init
railway add --database mysql
railway up
```

### Langkah 3: Set variables

```powershell
railway variables set APP_ENV=production APP_DEBUG=false
railway variables set APP_KEY="base64:XXXX"
railway variables set RUN_DB_SEED=true
# ... set lainnya seperti tabel di Metode A
```

```powershell
railway domain
```

---

## File khusus Railway di project ini

| File                       | Fungsi                               |
| -------------------------- | ------------------------------------ |
| `railway.toml`             | Perintah build & start               |
| `nixpacks.toml`            | PHP 8.2 + ekstensi MySQL             |
| `scripts/railway-start.sh` | Migrate + serve saat container jalan |
| `AppServiceProvider`       | Map `MYSQLHOST` dll. ke Laravel      |

---

## Tes fitur setelah deploy

- [ ] Login KS001 / ADMIN01
- [ ] Dashboard — grafik tampil
- [ ] Data Guru — list muncul
- [ ] Scan QR — **perlu izin kamera** (HTTPS Railway sudah ada)
- [ ] Edit jadwal (super admin)

---

## Troubleshooting Railway

| Masalah                                         | Solusi                                                                                                                                                        |
| ----------------------------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| **Build gagal**                                 | Cek log build; pastikan `composer.lock` ada di repo                                                                                                           |
| **Build gagal: `attribute 'composer' missing`** | Di `nixpacks.toml` pakai `php82Packages.composer`, bukan `php82Extensions.composer`                                                                           |
| **Build gagal: `ext-gd` missing**               | Pastikan `ext-gd` ada di `composer.json`; jangan override `nixPkgs` manual di `nixpacks.toml`                                                                 |
| **Status CRASHED** (bukan gagal build)          | Build sudah OK; container mati saat jalan. Buka tab **Logs** (bukan Build Logs). Biasanya: `APP_KEY` kosong, MySQL belum di-**Connect**, atau `migrate` gagal |
| **500 / APP_KEY**                               | Set `APP_KEY` di Variables                                                                                                                                    |
| **SQL connection refused**                      | Pastikan MySQL sudah di-**connect** ke service web; cek `MYSQLHOST`                                                                                           |
| **Login redirect loop**                         | `APP_URL` harus sama persis dengan domain Railway (https)                                                                                                     |
| **CSS/JS tidak load**                           | Pastikan `APP_URL` benar; clear cache: Redeploy                                                                                                               |
| **Grafik kosong**                               | Buka DevTools → Console; cek CSP (harusnya OK)                                                                                                                |
| **Foto guru hilang**                            | Normal di Railway demo — disk ephemeral                                                                                                                       |

### Lihat log

```powershell
railway logs
```

Atau di dashboard → service → **Logs**.

---

## Biaya & limit

- Railway punya **free trial / credit** — cek [railway.app/pricing](https://railway.app/pricing)
- MySQL + Web service menghabiskan credit
- Untuk demo sekolah jangka panjang → pertimbangkan VPS/cPanel

---

## Perbandingan singkat

|             | Railway              | cPanel/VPS            |
| ----------- | -------------------- | --------------------- |
| Cocok untuk | Demo, uji coba cepat | Production sekolah    |
| Setup       | Mudah (klik)         | Manual, lebih kontrol |
| Upload foto | Tidak permanen       | Permanen              |
| MySQL       | Plugin Railway       | Sendiri               |

---

## Link terkait

- [PANDUAN_DEPLOY_HOSTING.md](PANDUAN_DEPLOY_HOSTING.md) — production cPanel/VPS
- [DEPLOY_KEAMANAN.md](DEPLOY_KEAMANAN.md) — checklist keamanan
