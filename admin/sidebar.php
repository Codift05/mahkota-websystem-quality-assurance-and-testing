<?php
// sidebar.php - File untuk navigasi sidebar admin
?>
<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
    <div class="position-sticky pt-3">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" href="index.php">
                    <i class="bi bi-speedometer2 me-2"></i>
                    Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'artikel.php' ? 'active' : ''; ?>" href="artikel.php">
                    <i class="bi bi-file-text me-2"></i>
                    Manajemen Artikel
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'galeri.php' ? 'active' : ''; ?>" href="galeri.php">
                    <i class="bi bi-images me-2"></i>
                    Manajemen Galeri
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'media-desain.php' ? 'active' : ''; ?>" href="media-desain.php">
                    <i class="bi bi-brush me-2"></i>
                    Media Desain
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'program-bidang.php' ? 'active' : ''; ?>" href="program-bidang.php">
                    <i class="bi bi-diagram-3 me-2"></i>
                    Program Bidang
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'program-kerja.php' || basename($_SERVER['PHP_SELF']) == 'edit_program.php' ? 'active' : ''; ?>" href="program-kerja.php">
                    <i class="bi bi-calendar-check me-2"></i>
                    Program Kerja
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'profil.php' ? 'active' : ''; ?>" href="profil.php">
                    <i class="bi bi-building me-2"></i>
                    Profil Organisasi
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'kontak.php' ? 'active' : ''; ?>" href="kontak.php">
                    <i class="bi bi-envelope me-2"></i>
                    Informasi Kontak
                </a>
            </li>
        </ul>

        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
            <span>Pengaturan</span>
        </h6>
        <ul class="nav flex-column mb-2">
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'pengaturan.php' ? 'active' : ''; ?>" href="pengaturan.php">
                    <i class="bi bi-gear me-2"></i>
                    Pengaturan Website
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../logout.php">
                    <i class="bi bi-box-arrow-right me-2"></i>
                    Keluar
                </a>
            </li>
        </ul>
    </div>
</nav>