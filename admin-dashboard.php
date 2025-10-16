<?php
// Proteksi session admin
session_start();
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header('Location: login-page.php');
    exit;
}

// Koneksi database
require_once 'db.php';

$admin_name = $_SESSION['admin_username'] ?? 'Admin';

// Hitung statistik
$count_artikel = $conn->query("SELECT COUNT(*) as total FROM artikel")->fetch_assoc()['total'] ?? 0;
$count_galeri = $conn->query("SELECT COUNT(*) as total FROM galeri")->fetch_assoc()['total'] ?? 0;

// Cek apakah tabel program ada
$table_check = $conn->query("SHOW TABLES LIKE 'program'");
if ($table_check && $table_check->num_rows > 0) {
    $count_program = $conn->query("SELECT COUNT(*) as total FROM program")->fetch_assoc()['total'] ?? 0;
} else {
    $count_program = 0;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Admin Panel</title>
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/css/admin.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include 'admin/sidebar.php'; ?>
            
            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Dashboard</h1>
                    <div class="text-muted">
                        <i class="bi bi-person-circle"></i> Selamat datang, <strong><?php echo htmlspecialchars($admin_name); ?></strong>
                    </div>
                </div>
      
                <!-- Statistik Cards -->
                <div class="row mb-4">
                    <div class="col-md-4 mb-3">
                        <div class="card text-white bg-primary">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title text-uppercase mb-0">Total Artikel</h6>
                                        <h2 class="mb-0"><?php echo $count_artikel; ?></h2>
                                    </div>
                                    <div class="fs-1">
                                        <i class="bi bi-file-text"></i>
                                    </div>
                                </div>
                                <a href="admin/artikel.php" class="text-white text-decoration-none small">
                                    Lihat Detail <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <div class="card text-white bg-success">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title text-uppercase mb-0">Total Galeri</h6>
                                        <h2 class="mb-0"><?php echo $count_galeri; ?></h2>
                                    </div>
                                    <div class="fs-1">
                                        <i class="bi bi-images"></i>
                                    </div>
                                </div>
                                <a href="admin/galeri.php" class="text-white text-decoration-none small">
                                    Lihat Detail <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <div class="card text-white bg-info">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title text-uppercase mb-0">Program Kerja</h6>
                                        <h2 class="mb-0"><?php echo $count_program; ?></h2>
                                    </div>
                                    <div class="fs-1">
                                        <i class="bi bi-calendar-check"></i>
                                    </div>
                                </div>
                                <a href="admin/program-kerja.php" class="text-white text-decoration-none small">
                                    Lihat Detail <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
      
                <!-- Quick Actions -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <i class="bi bi-lightning-fill me-1"></i> Quick Actions
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 mb-2">
                                        <a href="admin/artikel.php" class="btn btn-outline-primary w-100">
                                            <i class="bi bi-plus-circle me-2"></i>Tambah Artikel
                                        </a>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <a href="admin/galeri.php" class="btn btn-outline-success w-100">
                                            <i class="bi bi-plus-circle me-2"></i>Tambah Galeri
                                        </a>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <a href="admin/program-kerja.php" class="btn btn-outline-info w-100">
                                            <i class="bi bi-plus-circle me-2"></i>Tambah Program
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
            </main>
        </div>
    </div>
  
    
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
