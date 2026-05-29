<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\PresensiController;
use App\Http\Controllers\FingerprintController;
use App\Http\Controllers\JadwalGuruController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\JadwalMasukController;

// ============================================================
// AUTH
// ============================================================
Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware(['guest', 'throttle:10,1']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ============================================================
// AUTHENTICATED ROUTES
// ============================================================
Route::middleware('auth')->group(function () {

    // Dashboard — super_admin & admin
    Route::get('/', [DashboardController::class, 'index'])
        ->name('dashboard')
        ->middleware('role:admin');

    // Profil — semua role
    Route::get('/profil', [ProfilController::class, 'index'])->name('profil.index');
    Route::post('/profil/password', [ProfilController::class, 'ubahPassword'])->name('profil.password');

    // ============================================================
    // KHUSUS GURU
    // ============================================================
    Route::middleware('role:guru')->group(function () {
        Route::get('/presensi-saya', [PresensiController::class, 'presensiSaya'])->name('presensi.saya');
        Route::get('/barcode-saya', [GuruController::class, 'barcodeSaya'])->name('guru.barcode_saya');
        Route::get('/izin-sakit', [PresensiController::class, 'izinSakit'])->name('presensi.izin_sakit');
        Route::post('/izin-sakit', [PresensiController::class, 'ajukanIzinSakit'])->name('presensi.ajukan_izin_sakit');
    });

    // ============================================================
    // SUPER ADMIN & ADMIN — Presensi
    // ============================================================
    Route::middleware('role:admin')->group(function () {
        Route::get('presensi', [PresensiController::class, 'index'])->name('presensi.index');
        Route::get('presensi/scan', [PresensiController::class, 'scan'])->name('presensi.scan');
        Route::post('presensi/barcode', [PresensiController::class, 'prosesBarcode'])
            ->name('presensi.barcode')
            ->middleware('throttle:30,1');
        Route::post('presensi/manual', [PresensiController::class, 'inputManual'])->name('presensi.manual');
        Route::patch('presensi/{presensi}/status', [PresensiController::class, 'updateStatus'])->name('presensi.status');
        Route::patch('presensi/{presensi}/approval', [PresensiController::class, 'approveIzinSakit'])->name('presensi.approval');
    });

    // ============================================================
    // SUPER ADMIN & ADMIN — Fingerprint
    // ============================================================
    Route::middleware('role:admin')->group(function () {
        Route::get('fingerprint', [FingerprintController::class, 'index'])->name('fingerprint.index');
        Route::get('fingerprint/import', [FingerprintController::class, 'import'])->name('fingerprint.import');
        Route::post('fingerprint/import', [FingerprintController::class, 'prosesImport'])->name('fingerprint.proses');
    });

    // ============================================================
    // SUPER ADMIN & ADMIN — Data Guru (CRUD)
    // ============================================================
    Route::middleware('role:admin')->group(function () {
        Route::get('guru', [GuruController::class, 'index'])->name('guru.index');
        Route::get('guru/create', [GuruController::class, 'create'])->name('guru.create');
        Route::post('guru', [GuruController::class, 'store'])->name('guru.store');
        Route::get('guru/{guru}', [GuruController::class, 'show'])->name('guru.show');
        Route::get('guru/{guru}/edit', [GuruController::class, 'edit'])->name('guru.edit');
        Route::put('guru/{guru}', [GuruController::class, 'update'])->name('guru.update');
        Route::delete('guru/{guru}', [GuruController::class, 'destroy'])->name('guru.destroy');
        Route::post('guru/{guru}/barcode', [GuruController::class, 'generateBarcode'])->name('guru.barcode');
    });

    // ============================================================
    // SUPER ADMIN & ADMIN — Jadwal Guru Per Hari
    // ============================================================
    Route::middleware('role:admin')->group(function () {
        Route::get('jadwal-guru', [JadwalGuruController::class, 'index'])->name('jadwal_guru.index');
        Route::post('jadwal-guru/simpan', [JadwalGuruController::class, 'simpan'])->name('jadwal_guru.simpan');
    });

    // ============================================================
    // SUPER ADMIN & ADMIN — Laporan
    // ============================================================
    Route::middleware('role:admin')->group(function () {
        Route::get('laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('laporan/pdf', [LaporanController::class, 'exportPdf'])->name('laporan.pdf');
        Route::get('laporan/excel', [LaporanController::class, 'exportExcel'])->name('laporan.excel');
    });

    // ============================================================
    // SUPER ADMIN ONLY — Manajemen Pengguna
    // ============================================================
    Route::middleware('role:super_admin')->group(function () {
        Route::patch('jadwal-masuk/{jadwal}/update-dashboard',
            [JadwalMasukController::class, 'updateDashboard'])
            ->name('jadwal_masuk.update_dashboard');

        Route::patch('presensi/{presensi}/jam-manual', [PresensiController::class, 'updateJamManual'])
            ->name('presensi.jam_manual');

        Route::get('pengguna', [PenggunaController::class, 'index'])->name('pengguna.index');
        Route::get('pengguna/create', [PenggunaController::class, 'create'])->name('pengguna.create');
        Route::post('pengguna', [PenggunaController::class, 'store'])->name('pengguna.store');
        Route::get('pengguna/{pengguna}/edit', [PenggunaController::class, 'edit'])->name('pengguna.edit');
        Route::put('pengguna/{pengguna}', [PenggunaController::class, 'update'])->name('pengguna.update');
        Route::post('pengguna/{pengguna}/reset-password', [PenggunaController::class, 'resetPassword'])->name('pengguna.reset_password');
        Route::delete('pengguna/{pengguna}', [PenggunaController::class, 'destroy'])->name('pengguna.destroy');
    });
});

// ============================================================
// API Fingerprint (tanpa auth)
// ============================================================
Route::post('/api/fingerprint/sync', [FingerprintController::class, 'sinkronisasiApi'])
    ->name('fingerprint.api')
    ->middleware('throttle:60,1');
