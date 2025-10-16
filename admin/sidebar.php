<?php
// sidebar.php - File untuk navigasi sidebar admin
// Deteksi apakah dipanggil dari folder admin atau root
$is_in_admin_folder = (basename(dirname($_SERVER['PHP_SELF'])) == 'admin');
$base_path = $is_in_admin_folder ? '' : 'admin/';
$dashboard_path = $is_in_admin_folder ? '../admin-dashboard.php' : 'admin-dashboard.php';
$logout_path = $is_in_admin_folder ? '../logout.php' : 'logout.php';
?>
<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
    <div class="position-sticky pt-3">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'admin-dashboard.php' ? 'active' : ''; ?>" href="<?php echo $dashboard_path; ?>">
                    <i class="bi bi-speedometer2 me-2"></i>
                    Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'artikel.php' ? 'active' : ''; ?>" href="<?php echo $base_path; ?>artikel.php">
                    <i class="bi bi-file-text me-2"></i>
                    Manajemen Artikel
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'galeri.php' ? 'active' : ''; ?>" href="<?php echo $base_path; ?>galeri.php">
                    <i class="bi bi-images me-2"></i>
                    Manajemen Galeri
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'program-kerja.php' || basename($_SERVER['PHP_SELF']) == 'edit_program.php' ? 'active' : ''; ?>" href="<?php echo $base_path; ?>program-kerja.php">
                    <i class="bi bi-calendar-check me-2"></i>
                    Program Kerja
                </a>
            </li>
        </ul>

        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
            <span>Akun</span>
        </h6>
        <ul class="nav flex-column mb-2">
            <li class="nav-item">
                <a class="nav-link" href="<?php echo $logout_path; ?>">
                    <i class="bi bi-box-arrow-right me-2"></i>
                    Keluar
                </a>
            </li>
        </ul>
    </div>
</nav>