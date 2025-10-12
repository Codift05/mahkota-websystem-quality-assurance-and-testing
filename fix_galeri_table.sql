-- ============================================
-- Fix Galeri Table Structure
-- Mahkota Web System
-- ============================================
-- Jalankan script ini di phpMyAdmin untuk memperbaiki struktur tabel galeri
-- Cara: Buka phpMyAdmin > Pilih database > Tab SQL > Copy-paste script ini > Klik Go

-- ============================================
-- 1. DROP tabel galeri yang lama (jika ada)
-- ============================================
-- PERINGATAN: Ini akan menghapus semua data galeri yang ada!
-- Jika Anda ingin mempertahankan data, jangan jalankan bagian DROP ini
-- DROP TABLE IF EXISTS `galeri`;

-- ============================================
-- 2. Buat tabel galeri dengan struktur lengkap
-- ============================================
CREATE TABLE IF NOT EXISTS `galeri` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `judul` varchar(255) NOT NULL,
  `deskripsi` text,
  `kategori` varchar(100) DEFAULT 'Lainnya',
  `file_path` varchar(500) NOT NULL,
  `tanggal_upload` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `kategori` (`kategori`),
  KEY `tanggal_upload` (`tanggal_upload`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- 3. Jika tabel sudah ada tapi kolom kurang, tambahkan kolom yang hilang
-- ============================================
-- Cek apakah kolom file_path ada, jika tidak tambahkan
-- ALTER TABLE `galeri` ADD COLUMN `file_path` varchar(500) NOT NULL AFTER `kategori`;

-- Cek apakah kolom kategori ada, jika tidak tambahkan
-- ALTER TABLE `galeri` ADD COLUMN `kategori` varchar(100) DEFAULT 'Lainnya' AFTER `deskripsi`;

-- ============================================
-- 4. Buat tabel log_aktivitas (jika belum ada)
-- ============================================
CREATE TABLE IF NOT EXISTS `log_aktivitas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL,
  `aktivitas` text NOT NULL,
  `tanggal` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `admin_id` (`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- 5. Insert data sample (opsional - untuk testing)
-- ============================================
-- INSERT INTO `galeri` (`judul`, `deskripsi`, `kategori`, `file_path`, `tanggal_upload`) VALUES
-- ('Sample Galeri 1', 'Deskripsi sample galeri pertama', 'Kegiatan', 'assets/img/galeri/sample1.jpg', NOW()),
-- ('Sample Galeri 2', 'Deskripsi sample galeri kedua', 'Dokumentasi', 'assets/img/galeri/sample2.jpg', NOW());

-- ============================================
-- SELESAI!
-- ============================================
-- Setelah menjalankan script ini, coba akses:
-- http://localhost/Devin/admin/galeri.php
-- ============================================
