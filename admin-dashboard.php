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
                        <div class="card border-0 shadow-sm" style="border-radius: 15px; overflow: hidden; background: linear-gradient(135deg, #5c99ee 0%, #4a7bc8 100%); transition: transform 0.3s, box-shadow 0.3s;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 10px 30px rgba(92, 153, 238, 0.3)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow=''">
                            <div class="card-body text-white p-4">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <p class="text-white-50 mb-1 text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; font-weight: 600;">Total Artikel</p>
                                        <h2 class="mb-0 fw-bold" style="font-size: 2.5rem;"><?php echo $count_artikel; ?></h2>
                                    </div>
                                    <div class="bg-white bg-opacity-25 rounded-3 p-3">
                                        <i class="bi bi-file-text" style="font-size: 2rem;"></i>
                                    </div>
                                </div>
                                <a href="admin/artikel.php" class="text-white text-decoration-none d-flex align-items-center" style="font-size: 0.875rem; font-weight: 500;">
                                    Lihat Detail <i class="bi bi-arrow-right ms-2"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <div class="card border-0 shadow-sm" style="border-radius: 15px; overflow: hidden; background: linear-gradient(135deg, #1cc88a 0%, #17a673 100%); transition: transform 0.3s, box-shadow 0.3s;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 10px 30px rgba(28, 200, 138, 0.3)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow=''">
                            <div class="card-body text-white p-4">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <p class="text-white-50 mb-1 text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; font-weight: 600;">Total Galeri</p>
                                        <h2 class="mb-0 fw-bold" style="font-size: 2.5rem;"><?php echo $count_galeri; ?></h2>
                                    </div>
                                    <div class="bg-white bg-opacity-25 rounded-3 p-3">
                                        <i class="bi bi-images" style="font-size: 2rem;"></i>
                                    </div>
                                </div>
                                <a href="admin/galeri.php" class="text-white text-decoration-none d-flex align-items-center" style="font-size: 0.875rem; font-weight: 500;">
                                    Lihat Detail <i class="bi bi-arrow-right ms-2"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <div class="card border-0 shadow-sm" style="border-radius: 15px; overflow: hidden; background: linear-gradient(135deg, #36b9cc 0%, #2a9aab 100%); transition: transform 0.3s, box-shadow 0.3s;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 10px 30px rgba(54, 185, 204, 0.3)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow=''">
                            <div class="card-body text-white p-4">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <p class="text-white-50 mb-1 text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; font-weight: 600;">Program Kerja</p>
                                        <h2 class="mb-0 fw-bold" style="font-size: 2.5rem;"><?php echo $count_program; ?></h2>
                                    </div>
                                    <div class="bg-white bg-opacity-25 rounded-3 p-3">
                                        <i class="bi bi-calendar-check" style="font-size: 2rem;"></i>
                                    </div>
                                </div>
                                <a href="admin/program-kerja.php" class="text-white text-decoration-none d-flex align-items-center" style="font-size: 0.875rem; font-weight: 500;">
                                    Lihat Detail <i class="bi bi-arrow-right ms-2"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
      
                <!-- Quick Actions -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                            <div class="card-header bg-white border-0" style="padding: 1.5rem;">
                                <h5 class="mb-0" style="color: #344761; font-weight: 600;">
                                    <i class="bi bi-lightning-fill me-2" style="color: #5c99ee;"></i>Quick Actions
                                </h5>
                            </div>
                            <div class="card-body" style="padding: 1.5rem;">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <a href="admin/artikel.php" class="btn w-100 d-flex align-items-center justify-content-center" style="padding: 1rem; border-radius: 12px; background: linear-gradient(135deg, #5c99ee 0%, #4a7bc8 100%); color: white; border: none; font-weight: 500; transition: all 0.3s;" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 8px 20px rgba(92, 153, 238, 0.3)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                                            <i class="bi bi-plus-circle me-2"></i>Tambah Artikel
                                        </a>
                                    </div>
                                    <div class="col-md-4">
                                        <a href="admin/galeri.php" class="btn w-100 d-flex align-items-center justify-content-center" style="padding: 1rem; border-radius: 12px; background: linear-gradient(135deg, #1cc88a 0%, #17a673 100%); color: white; border: none; font-weight: 500; transition: all 0.3s;" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 8px 20px rgba(28, 200, 138, 0.3)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                                            <i class="bi bi-plus-circle me-2"></i>Tambah Galeri
                                        </a>
                                    </div>
                                    <div class="col-md-4">
                                        <a href="admin/program-kerja.php" class="btn w-100 d-flex align-items-center justify-content-center" style="padding: 1rem; border-radius: 12px; background: linear-gradient(135deg, #36b9cc 0%, #2a9aab 100%); color: white; border: none; font-weight: 500; transition: all 0.3s;" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 8px 20px rgba(54, 185, 204, 0.3)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
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
