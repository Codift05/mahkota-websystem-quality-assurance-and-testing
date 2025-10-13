<?php
/**
 * Setup Database dan Admin User
 * Jalankan file ini untuk setup awal database
 * Akses: http://localhost/Devin/setup.php
 */

require_once 'db.php';

$errors = [];
$success = [];

// 1. Buat tabel admin jika belum ada
$sql_admin = "CREATE TABLE IF NOT EXISTS `admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if ($conn->query($sql_admin)) {
    $success[] = "✅ Tabel 'admin' berhasil dibuat/sudah ada";
} else {
    $errors[] = "❌ Error membuat tabel admin: " . $conn->error;
}

// 2. Buat tabel admin_logs jika belum ada
$sql_logs = "CREATE TABLE IF NOT EXISTS `admin_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `activity` text NOT NULL,
  `log_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if ($conn->query($sql_logs)) {
    $success[] = "✅ Tabel 'admin_logs' berhasil dibuat/sudah ada";
} else {
    $errors[] = "❌ Error membuat tabel admin_logs: " . $conn->error;
}

// 3. Buat tabel log_aktivitas jika belum ada
$sql_log_aktivitas = "CREATE TABLE IF NOT EXISTS `log_aktivitas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL,
  `aktivitas` text NOT NULL,
  `tanggal` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `admin_id` (`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if ($conn->query($sql_log_aktivitas)) {
    $success[] = "✅ Tabel 'log_aktivitas' berhasil dibuat/sudah ada";
} else {
    $errors[] = "❌ Error membuat tabel log_aktivitas: " . $conn->error;
}

// 4. Buat tabel galeri jika belum ada
$sql_galeri = "CREATE TABLE IF NOT EXISTS `galeri` (
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

if ($conn->query($sql_galeri)) {
    $success[] = "✅ Tabel 'galeri' berhasil dibuat/sudah ada";
} else {
    $errors[] = "❌ Error membuat tabel galeri: " . $conn->error;
}

// 5. Buat tabel program_kerja jika belum ada
$sql_program = "CREATE TABLE IF NOT EXISTS `program_kerja` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_program` varchar(255) NOT NULL,
  `deskripsi` text,
  `divisi` varchar(100) NOT NULL,
  `tahun` varchar(4) NOT NULL,
  `icon` varchar(50) DEFAULT 'bi-calendar-check',
  `status` varchar(50) DEFAULT 'Aktif',
  `urutan` int(11) DEFAULT 1,
  `tanggal_dibuat` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `divisi` (`divisi`),
  KEY `tahun` (`tahun`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if ($conn->query($sql_program)) {
    $success[] = "✅ Tabel 'program_kerja' berhasil dibuat/sudah ada";
} else {
    $errors[] = "❌ Error membuat tabel program_kerja: " . $conn->error;
}

// 6. Cek apakah sudah ada admin
$check = $conn->query("SELECT COUNT(*) as total FROM admin");
$row = $check->fetch_assoc();

if ($row['total'] == 0) {
    // Buat admin default
    $username = 'admin';
    $email = 'admin@mahkota.com';
    $password = 'admin123';
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = $conn->prepare("INSERT INTO admin (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param('sss', $username, $email, $hashed_password);
    
    if ($stmt->execute()) {
        $success[] = "✅ Admin default berhasil dibuat";
        $admin_created = true;
    } else {
        $errors[] = "❌ Error membuat admin: " . $conn->error;
    }
    $stmt->close();
} else {
    $success[] = "ℹ️ Admin sudah ada di database";
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Database - Mahkota</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .setup-container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
            max-width: 700px;
            width: 100%;
        }
        .setup-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .setup-header h1 {
            color: #3a3b45;
            font-weight: 700;
            margin-bottom: 10px;
        }
        .setup-header p {
            color: #858796;
        }
        .alert {
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
        }
        .credentials-box {
            background: #d4edda;
            border: 2px solid #28a745;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }
        .credentials-box h4 {
            color: #155724;
            margin-bottom: 15px;
        }
        .credential-item {
            display: flex;
            justify-content: space-between;
            padding: 10px;
            background: white;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        .credential-item strong {
            color: #3a3b45;
        }
        .credential-item span {
            color: #4e73df;
            font-weight: 600;
        }
        .btn-login {
            width: 100%;
            padding: 15px;
            background: #4e73df;
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 700;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            transition: all 0.3s;
        }
        .btn-login:hover {
            background: #2e59d9;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(78, 115, 223, 0.4);
        }
    </style>
</head>
<body>
    <div class="setup-container">
        <div class="setup-header">
            <h1><i class="bi bi-gear-fill"></i> Setup Database</h1>
            <p>Konfigurasi awal database untuk sistem admin Mahkota</p>
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

        <?php if (isset($admin_created) && $admin_created): ?>
            <div class="credentials-box">
                <h4><i class="bi bi-key-fill"></i> Kredensial Admin Default</h4>
                <div class="credential-item">
                    <strong>Username:</strong>
                    <span>admin</span>
                </div>
                <div class="credential-item">
                    <strong>Email:</strong>
                    <span>admin@mahkota.com</span>
                </div>
                <div class="credential-item">
                    <strong>Password:</strong>
                    <span>admin123</span>
                </div>
                <div class="alert alert-warning mt-3 mb-0">
                    <i class="bi bi-exclamation-triangle-fill"></i> 
                    <strong>PENTING:</strong> Simpan kredensial ini dengan aman! Ganti password setelah login pertama kali.
                </div>
            </div>
        <?php endif; ?>

        <?php if (empty($errors)): ?>
            <a href="login.html" class="btn-login">
                <i class="bi bi-box-arrow-in-right"></i> Login Sekarang
            </a>
        <?php else: ?>
            <div class="alert alert-info mt-3">
                <i class="bi bi-info-circle-fill"></i> 
                Perbaiki error di atas, kemudian refresh halaman ini.
            </div>
        <?php endif; ?>

        <div class="text-center mt-3">
            <small class="text-muted">
                <i class="bi bi-shield-check"></i> 
                Setup ini hanya perlu dijalankan sekali
            </small>
        </div>
    </div>
</body>
</html>
