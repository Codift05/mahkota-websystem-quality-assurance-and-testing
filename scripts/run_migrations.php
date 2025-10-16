<?php
// Simple migration runner to align DB schema with API and tests
// Usage: php scripts/run_migrations.php

require_once __DIR__ . '/../db.php';

function columnExists(mysqli $conn, string $table, string $column): bool {
    $sql = "SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '$table' AND COLUMN_NAME = '$column'";
    $res = $conn->query($sql);
    return $res && $res->num_rows > 0;
}

function tableExists(mysqli $conn, string $table): bool {
    $sql = "SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '$table'";
    $res = $conn->query($sql);
    return $res && $res->num_rows > 0;
}

function runQuery(mysqli $conn, string $sql, string $desc) {
    if ($conn->query($sql) === TRUE) {
        echo "[OK] $desc\n";
    } else {
        echo "[ERR] $desc: " . $conn->error . "\n";
    }
}

echo "Running migrations on DB: " . $conn->query('SELECT DATABASE() as db')->fetch_assoc()['db'] . "\n";

// 1) Ensure program table exists and aligned to API schema
if (!tableExists($conn, 'program')) {
    $createProgram = "CREATE TABLE IF NOT EXISTS `program` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `nama_program` VARCHAR(255) NOT NULL,
        `bidang` VARCHAR(100) NOT NULL,
        `deskripsi` TEXT,
        `tanggal_mulai` DATE,
        `tanggal_selesai` DATE,
        `status` ENUM('planned','ongoing','completed') DEFAULT 'planned',
        `gambar` VARCHAR(255),
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX `idx_bidang` (`bidang`),
        INDEX `idx_status` (`status`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    runQuery($conn, $createProgram, 'Create table program');
} else {
    echo "[INFO] Table program already exists, checking columns...\n";
    $hasNamaProgram   = columnExists($conn, 'program', 'nama_program');
    $hasJudul         = columnExists($conn, 'program', 'judul');
    $hasBidang        = columnExists($conn, 'program', 'bidang');
    $hasDeskripsi     = columnExists($conn, 'program', 'deskripsi');
    $hasMulai         = columnExists($conn, 'program', 'tanggal_mulai');
    $hasSelesai       = columnExists($conn, 'program', 'tanggal_selesai');
    $hasStatus        = columnExists($conn, 'program', 'status');
    $hasGambar        = columnExists($conn, 'program', 'gambar');
    $hasCreatedAt     = columnExists($conn, 'program', 'created_at');

    // Rename legacy 'judul' to 'nama_program' if needed
    if (!$hasNamaProgram && $hasJudul) {
        runQuery($conn, "ALTER TABLE `program` CHANGE COLUMN `judul` `nama_program` VARCHAR(255) NOT NULL", 'Rename judul to nama_program');
        $hasNamaProgram = true;
    }

    // Add missing columns to align with API
    if (!$hasNamaProgram && !$hasJudul) {
        runQuery($conn, "ALTER TABLE `program` ADD COLUMN `nama_program` VARCHAR(255) NOT NULL", 'Add nama_program');
    }
    if (!$hasBidang) {
        runQuery($conn, "ALTER TABLE `program` ADD COLUMN `bidang` VARCHAR(100) NOT NULL", 'Add bidang');
    }
    if (!$hasDeskripsi) {
        runQuery($conn, "ALTER TABLE `program` ADD COLUMN `deskripsi` TEXT NULL", 'Add deskripsi');
    }
    if (!$hasMulai) {
        runQuery($conn, "ALTER TABLE `program` ADD COLUMN `tanggal_mulai` DATE NULL", 'Add tanggal_mulai');
    }
    if (!$hasSelesai) {
        runQuery($conn, "ALTER TABLE `program` ADD COLUMN `tanggal_selesai` DATE NULL", 'Add tanggal_selesai');
    }
    if (!$hasStatus) {
        runQuery($conn, "ALTER TABLE `program` ADD COLUMN `status` ENUM('planned','ongoing','completed') DEFAULT 'planned'", 'Add status');
    }
    if (!$hasGambar) {
        runQuery($conn, "ALTER TABLE `program` ADD COLUMN `gambar` VARCHAR(255) NULL", 'Add gambar');
    }
    if (!$hasCreatedAt) {
        runQuery($conn, "ALTER TABLE `program` ADD COLUMN `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP", 'Add created_at');
    }
}

// 2) Align galeri table columns
if (!tableExists($conn, 'galeri')) {
    $createGaleri = "CREATE TABLE IF NOT EXISTS `galeri` (
        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `judul` VARCHAR(255) NOT NULL,
        `deskripsi` TEXT NOT NULL,
        `kategori` VARCHAR(100) NOT NULL,
        `gambar` VARCHAR(255) NOT NULL,
        `tanggal` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    runQuery($conn, $createGaleri, 'Create table galeri');
} else {
    echo "[INFO] Table galeri exists, checking columns...\n";

    // Rename legacy columns if present
    $hasFilePath = columnExists($conn, 'galeri', 'file_path');
    $hasGambar   = columnExists($conn, 'galeri', 'gambar');
    if ($hasFilePath && !$hasGambar) {
        runQuery($conn, "ALTER TABLE `galeri` CHANGE COLUMN `file_path` `gambar` VARCHAR(255) NOT NULL", 'Rename file_path to gambar');
    } else if ($hasFilePath && $hasGambar) {
        echo "[WARN] Both file_path and gambar exist; consider consolidating to gambar\n";
    } else {
        echo "[SKIP] No legacy file_path column\n";
    }

    $hasTanggalUpload = columnExists($conn, 'galeri', 'tanggal_upload');
    $hasTanggal       = columnExists($conn, 'galeri', 'tanggal');
    if ($hasTanggalUpload && !$hasTanggal) {
        runQuery($conn, "ALTER TABLE `galeri` CHANGE COLUMN `tanggal_upload` `tanggal` DATETIME NOT NULL", 'Rename tanggal_upload to tanggal');
    } else {
        echo "[SKIP] No legacy tanggal_upload column\n";
    }
}

echo "Migrations completed.\n";