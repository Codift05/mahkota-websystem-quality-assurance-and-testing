<?php
// Koneksi database
require_once 'db.php';

// Cek apakah kolom kategori ada di tabel galeri
$check_column = $conn->query("SHOW COLUMNS FROM galeri LIKE 'kategori'");
$has_kategori = ($check_column && $check_column->num_rows > 0);

// Tentukan nama kolom tanggal yang tersedia (fallback antara tanggal_upload vs tanggal)
$orderColumn = 'tanggal_upload';
$colCheckTanggalUpload = $conn->query("SHOW COLUMNS FROM galeri LIKE 'tanggal_upload'");
if (!$colCheckTanggalUpload || $colCheckTanggalUpload->num_rows === 0) {
  $orderColumn = 'tanggal';
}

// Tentukan nama kolom file path yang tersedia (fallback antara file_path vs gambar)
$fileColumn = 'file_path';
$colCheckFilePath = $conn->query("SHOW COLUMNS FROM galeri LIKE 'file_path'");
if (!$colCheckFilePath || $colCheckFilePath->num_rows === 0) {
  $fileColumn = 'gambar';
}

// Ambil kategori dari parameter URL jika ada
$kategori_filter = isset($_GET['kategori']) ? $_GET['kategori'] : '';

// Query untuk mengambil data galeri
if ($has_kategori && $kategori_filter) {
    $sql = "SELECT * FROM galeri WHERE kategori = ? ORDER BY $orderColumn DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $kategori_filter);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $sql = "SELECT * FROM galeri ORDER BY $orderColumn DESC";
    $result = $conn->query($sql);
}

// Ambil semua kategori yang tersedia (jika kolom kategori ada)
if ($has_kategori) {
    $kategori_sql = "SELECT DISTINCT kategori FROM galeri WHERE kategori IS NOT NULL AND kategori != '' ORDER BY kategori";
    $kategori_result = $conn->query($kategori_sql);
} else {
    $kategori_result = null;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Galeri Kegiatan - Mahkota Insight</title>
  <meta name="description" content="Dokumentasi visual dari seluruh aktivitas dan momen penting organisasi Mahkota">
  <meta name="keywords" content="galeri, kegiatan, mahkota, dokumentasi">

  <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600;1,700;1,800&family=Questrial:wght@400&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

  <!-- Main CSS File -->
  <link href="assets/css/main.css" rel="stylesheet">

  <style>
    /* Filter Buttons */
    .filter-section {
      padding: 40px 0 20px;
      background-color: var(--surface-color);
    }

    .filter-buttons {
      text-align: center;
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 10px;
    }

    .filter-btn {
      padding: 10px 25px;
      border: 2px solid var(--accent-color);
      background: var(--surface-color);
      color: var(--accent-color);
      border-radius: 50px;
      transition: all 0.3s ease-in-out;
      font-weight: 600;
      text-decoration: none;
      display: inline-block;
      font-size: 14px;
    }

    .filter-btn:hover,
    .filter-btn.active {
      background: var(--accent-color);
      color: var(--contrast-color);
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(var(--accent-color-rgb), 0.4);
    }

    /* Gallery Grid */
    .gallery-container {
      padding: 60px 0;
    }

    .gallery-item {
      margin-bottom: 30px;
      position: relative;
      overflow: hidden;
      border-radius: 10px;
      box-shadow: 0 0 20px rgba(0, 0, 0, 0.08);
      transition: all 0.3s ease-in-out;
      background: white;
    }

    .gallery-item:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    }

    .gallery-item img {
      width: 100%;
      height: 300px;
      object-fit: cover;
      transition: transform 0.3s;
    }

    .gallery-item:hover img {
      transform: scale(1.05);
    }

    .gallery-overlay {
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      background: linear-gradient(to top, rgba(0, 0, 0, 0.85), transparent);
      padding: 20px;
      transform: translateY(100%);
      transition: transform 0.3s ease-in-out;
    }

    .gallery-item:hover .gallery-overlay {
      transform: translateY(0);
    }

    .gallery-overlay h4 {
      color: white;
      font-size: 1.1rem;
      font-weight: 600;
      margin-bottom: 8px;
    }

    .gallery-overlay p {
      color: rgba(255, 255, 255, 0.9);
      font-size: 0.85rem;
      margin-bottom: 8px;
      line-height: 1.4;
    }

    .gallery-badge {
      display: inline-block;
      padding: 4px 12px;
      background: var(--accent-color);
      color: white;
      border-radius: 15px;
      font-size: 0.75rem;
      margin-top: 5px;
      font-weight: 500;
    }

    .no-data {
      text-align: center;
      padding: 80px 20px;
      color: var(--default-color);
    }

    .no-data i {
      font-size: 4rem;
      color: rgba(var(--default-color-rgb), 0.2);
      margin-bottom: 20px;
      display: block;
    }

    .no-data h3 {
      color: var(--heading-color);
      margin-bottom: 10px;
    }

    .no-data p {
      color: var(--default-color);
      opacity: 0.7;
    }
  </style>
</head>

<body>

  <header id="header" class="header d-flex align-items-center fixed-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center justify-content-between">

      <a href="index.html" class="logo d-flex align-items-center">
        <img src="assets/img/illustration/LOGO MAHKOTA.png" alt="Logo Mahkota" style="height:40px;width:auto;margin-right:10px;">
        <h1 class="sitename" style="margin-bottom:0;">Insight Mahkota</h1>
      </a>

      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="index.html#hero">Utama</a></li>
          <li><a href="index.html#about">Tentang</a></li>
          <li><a href="index.html#services">Program</a></li>
          <li><a href="galeri.php" class="active">Galeri</a></li>
          <li><a href="index.html#team">Pengurus</a></li>
          <li><a href="login-page.php">Login</a></li>
          <li><a href="index.html#contact">Kontak</a></li>
        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>

    </div>
  </header>

  <main class="main">

    <!-- Page Title -->
    <div class="page-title">
      <div class="container">
        <h1>Galeri Kegiatan</h1>
        <p>Dokumentasi visual dari seluruh aktivitas dan momen penting organisasi</p>
      </div>
    </div><!-- End Page Title -->

    <!-- Filter Buttons -->
    <?php if (!$has_kategori): ?>
    <section class="filter-section">
      <div class="container">
        <div class="alert alert-warning" style="border-radius: 10px; margin: 20px 0;">
          <i class="bi bi-exclamation-triangle-fill"></i> 
          <strong>Perhatian:</strong> Tabel galeri belum memiliki kolom kategori. 
          Silakan jalankan <a href="update_galeri_table.php" style="color: #856404; text-decoration: underline;"><strong>update_galeri_table.php</strong></a> 
          untuk menambahkan fitur filter kategori.
        </div>
      </div>
    </section>
    <?php else: ?>
    <section class="filter-section">
      <div class="container">
        <div class="filter-buttons">
          <a href="galeri.php" class="btn filter-btn <?php echo !$kategori_filter ? 'active' : ''; ?>">
            <i class="bi bi-grid-3x3"></i> Semua
          </a>
          <?php
          if ($kategori_result && $kategori_result->num_rows > 0) {
            while ($kat = $kategori_result->fetch_assoc()) {
              $active = ($kategori_filter == $kat['kategori']) ? 'active' : '';
              echo '<a href="galeri.php?kategori=' . urlencode($kat['kategori']) . '" class="btn filter-btn ' . $active . '">';
              echo '<i class="bi bi-tag"></i> ' . htmlspecialchars($kat['kategori']);
              echo '</a>';
            }
          }
          ?>
        </div>
      </div>
    </section>
    <?php endif; ?>

    <!-- Gallery Grid -->
    <section id="galeri" class="gallery-container section">
      <div class="container">
        <div class="row gy-4">
          <?php
          if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
              ?>
              <div class="col-lg-4 col-md-6">
                <div class="gallery-item">
                  <a href="<?php echo htmlspecialchars($row[$fileColumn]); ?>" class="glightbox">
                    <img src="<?php echo htmlspecialchars($row[$fileColumn]); ?>" alt="<?php echo htmlspecialchars($row['judul']); ?>">
                  </a>
                  <div class="gallery-overlay">
                    <h4><?php echo htmlspecialchars($row['judul']); ?></h4>
                    <p><?php echo isset($row['deskripsi']) ? htmlspecialchars(substr($row['deskripsi'], 0, 80)) . (strlen($row['deskripsi']) > 80 ? '...' : '') : ''; ?></p>
                    <?php if ($has_kategori && isset($row['kategori']) && $row['kategori']): ?>
                    <span class="gallery-badge"><?php echo htmlspecialchars($row['kategori']); ?></span>
                    <?php endif; ?>
                    <p style="font-size: 0.8rem; margin-top: 10px;">
                      <i class="bi bi-calendar"></i> <?php echo date('d M Y', strtotime($row[$orderColumn])); ?>
                    </p>
                  </div>
                </div>
              </div>
              <?php
            }
          } else {
            ?>
            <div class="col-12">
              <div class="no-data">
                <i class="bi bi-images"></i>
                <h3>Belum Ada Galeri</h3>
                <p>Saat ini belum ada dokumentasi yang tersedia<?php echo $kategori_filter ? ' untuk kategori ini' : ''; ?>.</p>
              </div>
            </div>
            <?php
          }
          ?>
        </div>
      </div>
    </section>

  </main>

<footer id="footer" class="footer position-relative light-background">
    <div class="container">
      <div class="row gy-5">
        <!-- Contact Section -->
        <section id="contact" class="contact section">
          <div class="container section-title">
            <h2>Contact</h2>
          </div>
          <div class="col-lg-15 order-lg-2 order-1">
            <div class="contact-sidebar">
              <div class="contact-header">
                <h3>Silahkan Hubungi<h3>
                <p></p>
              </div>
              <div class="contact-methods">
                <div class="contact-method">
                  <div class="contact-icon">
                    <i class="bi bi-geo-alt"></i>
                  </div>
                  <div class="contact-details">
                    <span class="method-label">Alamat</span>
                    <p>Asrama Babullah I<br>Jln Samrat 18, Kota Manado</p>
                  </div>
                </div>
                <div class="contact-method">
                  <div class="contact-icon">
                    <i class="bi bi-envelope"></i>
                  </div>
                <div class="contact-method">
                  <div class="contact-icon">
                    <i class="bi bi-telephone"></i>
                  </div>
                  <div class="contact-details">
                    <span class="method-label">Phone</span>
                    <p>+62 85298524163</p>
                  </div>
                </div>
                <div class="contact-method">
                  <div class="contact-icon">
                    <i class="bi bi-clock"></i>
                  </div>
                  <div class="contact-details">
                    <span class="method-label">Waktu</span>
                    <p>Senin - Minggu</p>
                  </div>
                </div>
              </div>
              <div class="connect-section">
                <span class="connect-label">Connect with us</span>
                <div class="social-links">
                  <a href="#" class="social-link">
                    <i class="bi bi-linkedin"></i>
                  </a>
                  <a href="#" class="social-link">
                    <i class="bi bi-instagram"></i>
                  </a>
                </div>
              </div>
            </div>
          </div>
        </section>
      </div>
    </div>
  </footer>
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
  <div id="preloader"></div>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="assets/vendor/imagesloaded/imagesloaded.pkgd.min.js"></script>
  <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
  <script src="assets/js/main.js"></script>
</body>
</html>
