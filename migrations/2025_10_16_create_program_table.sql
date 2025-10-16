-- Create program table aligned with API filters (bidang, status)
CREATE TABLE IF NOT EXISTS `program` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `judul` VARCHAR(255) NOT NULL,
  `deskripsi` TEXT NOT NULL,
  `bidang` VARCHAR(100) NULL,
  `status` VARCHAR(50) NULL,
  PRIMARY KEY (`id`),
  KEY `idx_bidang` (`bidang`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;