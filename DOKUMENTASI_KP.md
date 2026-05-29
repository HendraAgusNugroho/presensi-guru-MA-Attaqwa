# DOKUMENTASI TEKNIS — SISTEM PRESENSI GURU
## Madrasah Aliyah Attaqwa Benda Tangerang | YPIA Daarul Mu'min

---

## CARA MENJALANKAN DI XAMPP / LOCALHOST

1. Copy folder `laravel-presensi-guru` ke `C:\xampp\htdocs\`
2. Buat database MySQL bernama `presensi_guru`
3. Import file `database/presensi_guru.sql`
4. Copy `.env.example` menjadi `.env`, isi:
   ```
   DB_DATABASE=presensi_guru
   DB_USERNAME=root
   DB_PASSWORD=
   ```
5. Jalankan di terminal:
   ```
   composer install
   php artisan key:generate
   php artisan storage:link
   ```
6. Akses: `http://localhost/laravel-presensi-guru/public`

---

## CARA MENGGANTI LOGO SEKOLAH

**Lokasi file logo:**
- `public/images/logo.png` — Logo utama (buat folder images, taruh logo di sini)

**Cara tampilkan logo:**
1. Buka `resources/views/layouts/app.blade.php`
2. Cari `.sidebar-brand-logo` — ganti ikon dengan tag `<img>`
3. Buka `resources/views/auth/login.blade.php`
4. Cari `.school-logo-wrap` — ganti ikon dengan `<img>`

**Contoh penggantian ikon dengan gambar:**
```html
<!-- Sebelum (ikon) -->
<div class="school-logo-wrap">
    <i class="fas fa-school"></i>
</div>

<!-- Sesudah (gambar) -->
<div class="school-logo-wrap" style="background:transparent;border:2px solid #d4e8d4">
    <img src="{{ asset('images/logo.png') }}" style="width:60px;height:60px;object-fit:contain">
</div>
```

---

## CARA MENGUBAH WARNA TEMA

**File utama:** `public/css/app.css`

Cari bagian `:root` di baris paling atas:
```css
:root {
    --primary:      hsl(145, 60%, 28%);   /* Hijau utama — ubah angka ini */
    --primary-dark: hsl(145, 60%, 18%);   /* Hijau gelap sidebar */
    --accent:       hsl(48, 96%, 53%);    /* Kuning emas — ubah untuk aksen */
}
```
- Ubah `145` untuk warna berbeda (0=merah, 220=biru, 280=ungu)
- Ubah `60%` untuk saturasi (0%=abu, 100%=sangat cerah)
- Ubah `28%` untuk kecerahan (lebih kecil=lebih gelap)

---

## CARA MENGUBAH TAMPILAN DASHBOARD

**File:** `resources/views/dashboard/index.blade.php`

- **Stat card:** Tambah/hapus `<div class="stat-card ...">` di dalam `.stats-grid`
- **Chart:** Edit bagian `<script>` — data grafik dari variabel `grafikData`
- **Tabel scan terbaru:** Edit bagian `@forelse($riwayatScan as $p)`

**Untuk mengubah data dashboard:**
File: `app/Http/Controllers/DashboardController.php`
- Ubah query di method `index()`

---

## CARA EDIT MENU / SIDEBAR

**File:** `resources/views/layouts/app.blade.php`

Cari `<nav class="sidebar-nav">` — setiap menu adalah `<a class="nav-link ...">`.

**Menambah menu baru:**
```html
<a href="{{ route('nama-route') }}" class="nav-link @if(request()->routeIs('nama-route')) active @endif">
    <i class="fas fa-icon-name"></i> Nama Menu
</a>
```

Ikon Font Awesome tersedia di: https://fontawesome.com/icons

---

## CARA EDIT LAPORAN

**Controller:** `app/Http/Controllers/LaporanController.php`
- Method `index()` — filter dan rekap
- Method `exportPdf()` — generate PDF
- Method `exportExcel()` — export Excel 2 sheet

**View laporan web:** `resources/views/laporan/index.blade.php`
**Template PDF:** `resources/views/laporan/pdf.blade.php`

**Excel 2 Sheet:**
- Sheet 1 (Detail): `app/Exports/LaporanDetailSheet.php`
- Sheet 2 (Rekap): `app/Exports/LaporanRekapSheet.php`

Untuk menambah kolom Excel, edit method `headings()` dan `map()`.

---

## CARA EDIT IMPORT FINGERPRINT

**Controller:** `app/Http/Controllers/FingerprintController.php`
- Method `prosesImport()` — logika import file

**Format kolom yang didukung:**
| Kolom A | Kolom B |
|---------|---------|
| ID Fingerprint | Waktu Scan |
| FP001 | 2025-01-15 07:05:30 |

**Format tanggal yang didukung secara otomatis:**
- `Y-m-d H:i:s` → 2025-01-15 07:05:30
- `d/m/Y H:i:s` → 15/01/2025 07:05:30
- `d-m-Y H:i`   → 15-01-2025 07:05

**Batas toleransi jadwal:** diatur di tabel `jadwal_masuk` (atur via database atau tambah UI admin).

---

## LOKASI FILE PENTING

| File | Fungsi |
|------|--------|
| `public/css/app.css` | Semua styling / tema warna |
| `resources/views/layouts/app.blade.php` | Sidebar & layout utama |
| `resources/views/auth/login.blade.php` | Halaman login |
| `resources/views/dashboard/index.blade.php` | Dashboard |
| `resources/views/laporan/index.blade.php` | Halaman laporan |
| `resources/views/laporan/pdf.blade.php` | Template PDF cetak |
| `resources/views/fingerprint/import.blade.php` | Halaman import fingerprint |
| `app/Http/Controllers/DashboardController.php` | Logika dashboard |
| `app/Http/Controllers/LaporanController.php` | Logika laporan & export |
| `app/Http/Controllers/FingerprintController.php` | Logika import fingerprint |
| `app/Exports/LaporanDetailSheet.php` | Excel Sheet 1 (Detail) |
| `app/Exports/LaporanRekapSheet.php` | Excel Sheet 2 (Rekap) |
| `app/Models/Guru.php` | Model data guru |
| `app/Models/Presensi.php` | Model data presensi |
| `routes/web.php` | Semua routing aplikasi |
| `database/presensi_guru.sql` | Dump database siap import |

---

## AKUN DEFAULT

| Role | NIP | Password |
|------|-----|----------|
| Super Admin | 196801012000011001 | superadmin123 |
| Admin | 000000000 | admin123 |
| Guru (contoh) | 198203202005011003 | guru123 |

---

## FITUR YANG TERSEDIA

- ✅ Login multi-role (Super Admin, Admin, Guru)
- ✅ Dashboard dengan statistik & chart
- ✅ CRUD Data Guru
- ✅ Presensi via scan QR Code
- ✅ Import fingerprint dari Excel/CSV
- ✅ Laporan presensi dengan filter lengkap
- ✅ Export PDF dengan kop sekolah
- ✅ Export Excel 2 Sheet (Detail + Rekap)
- ✅ Manajemen pengguna (Super Admin only)
- ✅ Responsive mobile (hamburger menu)
- ✅ Tema hijau & kuning emas MA Attaqwa

---

*Dibuat untuk keperluan Sidang Kerja Praktik (KP) — MA Attaqwa Benda Tangerang*
