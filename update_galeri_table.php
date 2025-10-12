<?php
/**
 * Update Tabel Galeri - Menambahkan kolom kategori
 * Jalankan file ini sekali untuk update struktur tabel
 * Akses: http://localhost/Devin/update_galeri_table.php
 */

require_once 'db.php';

$errors = [];
$success = [];

// Cek apakah tabel galeri ada
$check_table = $conn->query("SHOW TABLES LIKE 'galeri'");
if ($check_table->num_rows == 0) {
    // Tabel belum ada, buat tabel baru
    $sql_create = "CREATE TABLE `galeri` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `judul` varchar(255) NOT NULL,
      `deskripsi` text,
      `kategori` varchar(100) DEFAULT 'Lainnya',
      `file_path` varchar(500) NOT NULL,
      `tanggal_upload` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      KEY `kategori` (`kategori`),
      KEY `tanggal_upload` (`tanggal_upload`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    if ($conn->query($sql_create)) {
        $success[] = "✅ Tabel 'galeri' berhasil dibuat dengan struktur lengkap";
    } else {
        $errors[] = "❌ Error membuat tabel galeri: " . $conn->error;
    }
} else {
    // Tabel sudah ada, cek apakah kolom kategori ada
    $check_column = $conn->query("SHOW COLUMNS FROM galeri LIKE 'kategori'");
    
    if ($check_column->num_rows == 0) {
        // Kolom kategori belum ada, tambahkan
        $sql_add_column = "ALTER TABLE `galeri` ADD COLUMN `kategori` varchar(100) DEFAULT 'Lainnya' AFTER `deskripsi`";
        
        if ($conn->query($sql_add_column)) {
            $success[] = "✅ Kolom 'kategori' berhasil ditambahkan ke tabel galeri";
        } else {
            $errors[] = "❌ Error menambahkan kolom kategori: " . $conn->error;
        }
        
        // Tambahkan index untuk kategori
        $sql_add_index = "ALTER TABLE `galeri` ADD INDEX `kategori` (`kategori`)";
        if ($conn->query($sql_add_index)) {
            $success[] = "✅ Index untuk kolom 'kategori' berhasil ditambahkan";
        } else {
            // Index mungkin sudah ada, abaikan error
            $success[] = "ℹ️ Index kategori sudah ada atau tidak perlu ditambahkan";
        }
    } else {
        $success[] = "ℹ️ Kolom 'kategori' sudah ada di tabel galeri";
    }
    
    // Cek kolom lain yang mungkin kurang
    $columns_to_check = [
        'judul' => "varchar(255) NOT NULL",
        'deskripsi' => "text",
        'file_path' => "varchar(500) NOT NULL",
        'tanggal_upload' => "timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP"
    ];
    
    foreach ($columns_to_check as $column => $definition) {
        $check = $conn->query("SHOW COLUMNS FROM galeri LIKE '$column'");
        if ($check->num_rows == 0) {
            $errors[] = "⚠️ Kolom '$column' tidak ditemukan. Struktur tabel mungkin tidak lengkap.";
        }
    }
}

// Cek apakah tabel log_aktivitas ada (untuk fitur log admin)
$check_log = $conn->query("SHOW TABLES LIKE 'log_aktivitas'");
if ($check_log->num_rows == 0) {
    $sql_log = "CREATE TABLE IF NOT EXISTS `log_aktivitas` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `admin_id` int(11) NOT NULL,
      `aktivitas` text NOT NULL,
      `tanggal` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      KEY `admin_id` (`admin_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    if ($conn->query($sql_log)) {
        $success[] = "✅ Tabel 'log_aktivitas' berhasil dibuat";
    } else {
        $errors[] = "❌ Error membuat tabel log_aktivitas: " . $conn->error;
    }
} else {
    $success[] = "ℹ️ Tabel 'log_aktivitas' sudah ada";
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Tabel Galeri - Mahkota</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .update-container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
            max-width: 700px;
            width: 100%;
        }
        .update-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .update-header h1 {
            color: #3a3b45;
            font-weight: 700;
            margin-bottom: 10px;
        }
        .update-header p {
            color: #858796;
        }
        .alert {
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
        }
        .btn-action {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 10px;
            font-weight: 700;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            transition: all 0.3s;
            margin-top: 10px;
        }
        .btn-galeri {
            background: #667eea;
            color: white;
        }
        .btn-galeri:hover {
            background: #5568d3;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .btn-admin {
            background: #28a745;
            color: white;
        }
        .btn-admin:hover {
            background: #218838;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
        }
    </style>
</head>
<body>
    <div class="update-container">
        <div class="update-header">
            <h1><i class="bi bi-database-fill-gear"></i> Update Tabel Galeri</h1>
            <p>Memperbarui struktur database untuk fitur galeri</p>
        </div>

        <?php if (!empty($success)): ?>
            <?php foreach ($success as $msg): ?>
                <div class="alert alert-success">
                    <?php echo $msg; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <?php foreach ($errors as $msg): ?>
                <div class="alert alert-danger">
                    <?php echo $msg; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if (empty($errors)): ?>
            <div class="alert alert-info">
                <i class="bi bi-info-circle-fill"></i> 
                <strong>Update Selesai!</strong> Struktur database sudah siap digunakan.
            </div>
            
            <a href="galeri.php" class="btn btn-action btn-galeri">
                <i class="bi bi-images"></i> Lihat Halaman Galeri
            </a>
            
            <a href="admin/galeri.php" class="btn btn-action btn-admin">
                <i class="bi bi-gear-fill"></i> Kelola Galeri (Admin)
            </a>
        <?php else: ?>
            <div class="alert alert-warning mt-3">
                <i class="bi bi-exclamation-triangle-fill"></i> 
                <strong>Perhatian:</strong> Ada error yang perlu diperbaiki. Silakan cek pesan error di atas.
            </div>
            <a href="javascript:location.reload()" class="btn btn-action btn-galeri">
                <i class="bi bi-arrow-clockwise"></i> Coba Lagi
            </a>
        <?php endif; ?>

        <div class="text-center mt-3">
            <small class="text-muted">
                <i class="bi bi-shield-check"></i> 
                File ini aman untuk dijalankan berkali-kali
            </small>
        </div>
    </div>
</body>
</html>
