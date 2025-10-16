-- Align galeri table to API schema (use columns: gambar, tanggal)
-- If table doesn't exist, create with the expected structure.
CREATE TABLE IF NOT EXISTS `galeri` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `judul` VARCHAR(255) NOT NULL,
  `deskripsi` TEXT NOT NULL,
  `kategori` VARCHAR(100) NOT NULL,
  `gambar` VARCHAR(255) NOT NULL,
  `tanggal` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- If existing columns use legacy names, rename to new schema.
-- Warning: These ALTER statements assume legacy columns exist.
-- Prefer running via scripts/run_migrations.php to check before applying.
ALTER TABLE `galeri` CHANGE COLUMN `file_path` `gambar` VARCHAR(255) NOT NULL;
ALTER TABLE `galeri` CHANGE COLUMN `tanggal_upload` `tanggal` DATETIME NOT NULL;