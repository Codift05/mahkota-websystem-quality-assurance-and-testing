-- ============================================
-- Update Galeri Table - Tambah Kolom Tanpa Hapus Data
-- Mahkota Web System
-- ============================================
-- Script ini untuk menambahkan kolom yang hilang TANPA menghapus data yang sudah ada
-- Gunakan script ini jika tabel galeri sudah ada dan berisi data

-- ============================================
-- Cek dan tambahkan kolom file_path jika belum ada
-- ============================================
-- Jalankan query ini satu per satu di phpMyAdmin
-- Jika ada error "Duplicate column name", berarti kolom sudah ada (skip saja)

ALTER TABLE `galeri` 
ADD COLUMN `file_path` varchar(500) NOT NULL DEFAULT '' AFTER `kategori`;

-- ============================================
-- Cek dan tambahkan kolom kategori jika belum ada
-- ============================================
ALTER TABLE `galeri` 
ADD COLUMN `kategori` varchar(100) DEFAULT 'Lainnya' AFTER `deskripsi`;

-- ============================================
-- Cek dan tambahkan kolom deskripsi jika belum ada
-- ============================================
ALTER TABLE `galeri` 
ADD COLUMN `deskripsi` text AFTER `judul`;

-- ============================================
-- Cek dan tambahkan kolom tanggal_upload jika belum ada
-- ============================================
ALTER TABLE `galeri` 
ADD COLUMN `tanggal_upload` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP;

-- ============================================
-- Tambahkan index untuk performa
-- ============================================
ALTER TABLE `galeri` 
ADD INDEX `kategori` (`kategori`);

ALTER TABLE `galeri` 
ADD INDEX `tanggal_upload` (`tanggal_upload`);

-- ============================================
-- CATATAN:
-- ============================================
-- Jika ada error "Duplicate column name 'nama_kolom'", itu normal
-- Artinya kolom tersebut sudah ada, lanjutkan ke query berikutnya
-- ============================================
