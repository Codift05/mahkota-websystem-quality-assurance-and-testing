<?php
// sidebar.php - File untuk navigasi sidebar admin
// Deteksi apakah dipanggil dari folder admin atau root
$is_in_admin_folder = (basename(dirname($_SERVER['PHP_SELF'])) == 'admin');
$base_path = $is_in_admin_folder ? '' : 'admin/';
$dashboard_path = $is_in_admin_folder ? '../admin-dashboard.php' : 'admin-dashboard.php';
$logout_path = $is_in_admin_folder ? '../logout.php' : 'logout.php';
$logo_path = $is_in_admin_folder ? '../assets/img/about/LOGO MAHKOTA (1) (1).png' : 'assets/img/about/LOGO MAHKOTA (1) (1).png';
?>
<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block sidebar collapse" style="background: linear-gradient(180deg, #4e73df 0%, #2e59d9 100%); position: fixed; top: 0; left: 0; height: 100vh; z-index: 1000;">
    <div class="position-sticky pt-3">
        <!-- Logo dan Brand -->
        <a href="<?php echo $dashboard_path; ?>" class="sidebar-brand text-decoration-none d-flex flex-column align-items-center justify-content-center mb-3" style="padding: 1.5rem 1rem; border-bottom: 1px solid rgba(255,255,255,0.1);">
            <img src="<?php echo $logo_path; ?>" alt="Logo MAHKOTA" style="max-width: 80px; height: auto; margin-bottom: 0.75rem;">
            <span class="brand-text text-white" style="font-size: 1.1rem; font-weight: 700; text-align: center;">MAHKOTA Admin</span>
        </a>
        
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link text-white <?php echo basename($_SERVER['PHP_SELF']) == 'admin-dashboard.php' ? 'active' : ''; ?>" href="<?php echo $dashboard_path; ?>" style="padding: 0.875rem 1.5rem; transition: all 0.3s; <?php echo basename($_SERVER['PHP_SELF']) == 'admin-dashboard.php' ? 'background-color: rgba(255,255,255,0.15); font-weight: 600;' : ''; ?>">
                    <i class="bi bi-speedometer2 me-2"></i>
                    Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white <?php echo basename($_SERVER['PHP_SELF']) == 'artikel.php' ? 'active' : ''; ?>" href="<?php echo $base_path; ?>artikel.php" style="padding: 0.875rem 1.5rem; transition: all 0.3s; <?php echo basename($_SERVER['PHP_SELF']) == 'artikel.php' ? 'background-color: rgba(255,255,255,0.15); font-weight: 600;' : ''; ?>">
                    <i class="bi bi-file-text me-2"></i>
                    Manajemen Artikel
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white <?php echo basename($_SERVER['PHP_SELF']) == 'galeri.php' ? 'active' : ''; ?>" href="<?php echo $base_path; ?>galeri.php" style="padding: 0.875rem 1.5rem; transition: all 0.3s; <?php echo basename($_SERVER['PHP_SELF']) == 'galeri.php' ? 'background-color: rgba(255,255,255,0.15); font-weight: 600;' : ''; ?>">
                    <i class="bi bi-images me-2"></i>
                    Manajemen Galeri
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white <?php echo basename($_SERVER['PHP_SELF']) == 'program-kerja.php' || basename($_SERVER['PHP_SELF']) == 'edit_program.php' ? 'active' : ''; ?>" href="<?php echo $base_path; ?>program-kerja.php" style="padding: 0.875rem 1.5rem; transition: all 0.3s; <?php echo basename($_SERVER['PHP_SELF']) == 'program-kerja.php' || basename($_SERVER['PHP_SELF']) == 'edit_program.php' ? 'background-color: rgba(255,255,255,0.15); font-weight: 600;' : ''; ?>">
                    <i class="bi bi-calendar-check me-2"></i>
                    Program Kerja
                </a>
            </li>
        </ul>

        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1" style="color: rgba(255,255,255,0.5); font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05rem;">
            <span>AKUN</span>
        </h6>
        <ul class="nav flex-column mb-2">
            <li class="nav-item">
                <a class="nav-link text-white" href="<?php echo $logout_path; ?>" style="padding: 0.875rem 1.5rem; transition: all 0.3s;">
                    <i class="bi bi-box-arrow-right me-2"></i>
                    Keluar
                </a>
            </li>
        </ul>
    </div>
</nav>