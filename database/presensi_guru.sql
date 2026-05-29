-- =====================================================
-- PRESENSI GURU AT-TAQWA — SQL DUMP (Role Baru)
-- Alternatif jika artisan migrate tidak bisa digunakan
-- Import via phpMyAdmin: Database > Import > pilih file ini
-- =====================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+07:00";

-- Database
CREATE DATABASE IF NOT EXISTS `presensi_guru` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `presensi_guru`;

-- Tabel gurus (dibuat duluan karena users referencing gurus)
CREATE TABLE IF NOT EXISTS `gurus` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `nip` varchar(30) NOT NULL UNIQUE,
  `nama` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL UNIQUE,
  `no_hp` varchar(20) DEFAULT NULL,
  `jabatan` varchar(100) DEFAULT NULL,
  `mata_pelajaran` varchar(100) DEFAULT NULL,
  `id_fingerprint` varchar(20) DEFAULT NULL UNIQUE,
  `barcode` varchar(255) NOT NULL UNIQUE,
  `foto` varchar(255) DEFAULT NULL,
  `jenis_kelamin` enum('L','P') NOT NULL DEFAULT 'L',
  `status` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel users (role baru: super_admin, admin, guru)
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `nip` varchar(30) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `role` enum('super_admin','admin','guru') NOT NULL DEFAULT 'guru',
  `guru_id` bigint UNSIGNED DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`guru_id`) REFERENCES `gurus`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel jadwal_masuk
CREATE TABLE IF NOT EXISTS `jadwal_masuk` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama_jadwal` varchar(255) NOT NULL DEFAULT 'Jadwal Utama',
  `jam_masuk` time NOT NULL DEFAULT '07:00:00',
  `batas_toleransi` time NOT NULL DEFAULT '07:15:00',
  `jam_pulang` time NOT NULL DEFAULT '15:00:00',
  `aktif` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel presensis
CREATE TABLE IF NOT EXISTS `presensis` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `guru_id` bigint UNSIGNED NOT NULL,
  `tanggal` date NOT NULL,
  `jam_masuk` time DEFAULT NULL,
  `jam_pulang` time DEFAULT NULL,
  `status` enum('hadir','telat','tidak_hadir','izin','sakit') NOT NULL DEFAULT 'tidak_hadir',
  `metode` enum('barcode','fingerprint','manual') NOT NULL DEFAULT 'manual',
  `menit_telat` int NOT NULL DEFAULT '0',
  `keterangan` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `presensis_guru_id_tanggal_unique` (`guru_id`,`tanggal`),
  CONSTRAINT `presensis_guru_id_foreign` FOREIGN KEY (`guru_id`) REFERENCES `gurus` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel fingerprint_logs
CREATE TABLE IF NOT EXISTS `fingerprint_logs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_fingerprint` varchar(20) NOT NULL,
  `guru_id` bigint UNSIGNED DEFAULT NULL,
  `waktu_scan` datetime NOT NULL,
  `tipe` enum('masuk','pulang') NOT NULL DEFAULT 'masuk',
  `diproses` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fingerprint_logs_guru_id_foreign` FOREIGN KEY (`guru_id`) REFERENCES `gurus` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- migrations table (required by Laravel)
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Data contoh guru
INSERT INTO `gurus` (`nip`,`nama`,`jabatan`,`mata_pelajaran`,`id_fingerprint`,`barcode`,`jenis_kelamin`,`status`,`created_at`,`updated_at`) VALUES
('196801012000011001','Drs. Ahmad Fauzi, M.Pd','Kepala Sekolah','-','FP001','GR-196801012000011001','L','aktif',NOW(),NOW()),
('197505152003012002','Siti Rahmawati, S.Pd','Wakil Kepala Sekolah','Matematika','FP002','GR-197505152003012002','P','aktif',NOW(),NOW()),
('198203202005011003','Budi Santoso, S.Pd','Guru','Bahasa Indonesia','FP003','GR-198203202005011003','L','aktif',NOW(),NOW()),
('199001012010011004','Dewi Lestari, S.Pd','Guru','IPA','FP004','GR-199001012010011004','P','aktif',NOW(),NOW()),
('198507112007012005','Rudi Hartono, S.Kom','Guru','TIK','FP005','GR-198507112007012005','L','aktif',NOW(),NOW());

-- =====================================================
-- AKUN PENGGUNA — PASSWORD DEFAULT
-- =====================================================
-- super_admin (NIP: 196801012000011001, password: superadmin123)
-- admin       (NIP: 000000000,           password: admin123)
-- admin       (NIP: 197505152003012002,  password: admin123)
-- guru        (NIP: 198203202005011003,  password: guru123)
-- guru        (NIP: 199001012010011004,  password: guru123)
-- guru        (NIP: 198507112007012005,  password: guru123)
-- Gunakan: php artisan db:seed  untuk mengisi data akun secara otomatis
-- =====================================================

-- Data akun (hash = "password" — GANTI via seeder atau tinker)
INSERT INTO `users` (`name`,`nip`,`password`,`role`,`guru_id`,`created_at`,`updated_at`) VALUES
('Drs. Ahmad Fauzi, M.Pd','196801012000011001','$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','super_admin',1,NOW(),NOW()),
('Operator TU','000000000','$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','admin',NULL,NOW(),NOW()),
('Siti Rahmawati, S.Pd','197505152003012002','$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','admin',2,NOW(),NOW()),
('Budi Santoso, S.Pd','198203202005011003','$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','guru',3,NOW(),NOW()),
('Dewi Lestari, S.Pd','199001012010011004','$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','guru',4,NOW(),NOW()),
('Rudi Hartono, S.Kom','198507112007012005','$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','guru',5,NOW(),NOW());

-- Data default: Jadwal Masuk
INSERT INTO `jadwal_masuk` (`nama_jadwal`,`jam_masuk`,`batas_toleransi`,`jam_pulang`,`aktif`,`created_at`,`updated_at`) VALUES
('Jadwal Utama','07:00:00','07:15:00','15:00:00',1,NOW(),NOW());

COMMIT;
