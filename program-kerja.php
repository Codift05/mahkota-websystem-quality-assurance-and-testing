<?php
// Koneksi database
require_once 'db.php';

// Ambil divisi dari parameter URL jika ada
$divisi_filter = isset($_GET['divisi']) ? $_GET['divisi'] : '';

// Cek apakah kolom divisi ada di tabel program_kerja
$check_column = $conn->query("SHOW COLUMNS FROM program_kerja LIKE 'divisi'");
$has_divisi = ($check_column && $check_column->num_rows > 0);

// Query untuk mengambil data program kerja
if ($has_divisi && $divisi_filter) {
    $sql = "SELECT * FROM program_kerja WHERE divisi = ? ORDER BY tahun DESC, urutan ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $divisi_filter);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $sql = "SELECT * FROM program_kerja ORDER BY tahun DESC, urutan ASC";
    $result = $conn->query($sql);
}

// Ambil semua divisi yang tersedia (jika kolom divisi ada)
if ($has_divisi) {
    $divisi_sql = "SELECT DISTINCT divisi FROM program_kerja WHERE divisi IS NOT NULL AND divisi != '' ORDER BY divisi";
    $divisi_result = $conn->query($divisi_sql);
} else {
    $divisi_result = null;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Program Kerja - Mahkota Insight</title>
  <meta name="description" content="Berbagai program tahunan yang dirancang untuk mendukung pengembangan mahasiswa Ternate di Manado">
  <meta name="keywords" content="program kerja, mahkota, divisi, kegiatan">

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

    /* Program Cards */
    .program-container {
      padding: 60px 0;
    }

    .program-card {
      margin-bottom: 30px;
      border-radius: 10px;
      box-shadow: 0 0 20px rgba(0, 0, 0, 0.08);
      transition: all 0.3s ease-in-out;
      background: white;
      overflow: hidden;
      height: 100%;
      display: flex;
      flex-direction: column;
    }

    .program-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    }

    .program-header {
      background: linear-gradient(135deg, var(--accent-color) 0%, #5a67d8 100%);
      color: white;
      padding: 25px;
      position: relative;
    }

    .program-icon {
      font-size: 2.5rem;
      margin-bottom: 10px;
      opacity: 0.9;
    }

    .program-divisi {
      display: inline-block;
      background: rgba(255, 255, 255, 0.2);
      padding: 4px 12px;
      border-radius: 15px;
      font-size: 0.75rem;
      font-weight: 600;
      margin-bottom: 10px;
    }

    .program-body {
      padding: 25px;
      flex-grow: 1;
      display: flex;
      flex-direction: column;
    }

    .program-title {
      font-size: 1.3rem;
      font-weight: 700;
      color: var(--heading-color);
      margin-bottom: 15px;
    }

    .program-description {
      color: var(--default-color);
      line-height: 1.6;
      margin-bottom: 15px;
      flex-grow: 1;
    }

    .program-meta {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding-top: 15px;
      border-top: 1px solid rgba(0, 0, 0, 0.1);
      font-size: 0.85rem;
      color: var(--default-color);
    }

    .program-tahun {
      font-weight: 600;
      color: var(--accent-color);
    }

    .program-status {
      padding: 4px 12px;
      border-radius: 15px;
      font-size: 0.75rem;
      font-weight: 600;
    }

    .status-aktif {
      background: #d4edda;
      color: #155724;
    }

    .status-selesai {
      background: #d1ecf1;
      color: #0c5460;
    }

    .status-berjalan {
      background: #fff3cd;
      color: #856404;
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
          <li><a href="program-kerja.php" class="active">Program</a></li>
          <li><a href="galeri.php">Galeri</a></li>
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
        <h1>Program Kerja</h1>
        <p>Berbagai program tahunan yang dirancang untuk mendukung pengembangan mahasiswa</p>
      </div>
    </div><!-- End Page Title -->

    <!-- Filter Buttons -->
    <?php if (!$has_divisi): ?>
    <section class="filter-section">
      <div class="container">
        <div class="alert alert-warning" style="border-radius: 10px; margin: 20px 0;">
          <i class="bi bi-exclamation-triangle-fill"></i> 
          <strong>Perhatian:</strong> Tabel program_kerja belum memiliki kolom divisi. 
          Silakan jalankan <a href="update_program_table.php" style="color: #856404; text-decoration: underline;"><strong>update_program_table.php</strong></a> 
          untuk menambahkan fitur filter divisi.
        </div>
      </div>
    </section>
    <?php else: ?>
    <section class="filter-section">
      <div class="container">
        <div class="filter-buttons">
          <a href="program-kerja.php" class="btn filter-btn <?php echo !$divisi_filter ? 'active' : ''; ?>">
            <i class="bi bi-grid-3x3"></i> Semua Divisi
          </a>
          <?php
          if ($divisi_result && $divisi_result->num_rows > 0) {
            while ($div = $divisi_result->fetch_assoc()) {
              $active = ($divisi_filter == $div['divisi']) ? 'active' : '';
              echo '<a href="program-kerja.php?divisi=' . urlencode($div['divisi']) . '" class="btn filter-btn ' . $active . '">';
              echo '<i class="bi bi-briefcase"></i> ' . htmlspecialchars($div['divisi']);
              echo '</a>';
            }
          }
          ?>
        </div>
      </div>
    </section>
    <?php endif; ?>

    <!-- Program Cards -->
    <section id="program" class="program-container section">
      <div class="container">
        <div class="row gy-4">
          <?php
          if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
              // Tentukan status badge
              $status_class = 'status-aktif';
              $status_text = isset($row['status']) ? $row['status'] : 'Aktif';
              
              if (isset($row['status'])) {
                if ($row['status'] == 'Selesai') {
                  $status_class = 'status-selesai';
                } elseif ($row['status'] == 'Berjalan') {
                  $status_class = 'status-berjalan';
                }
              }
              ?>
              <div class="col-lg-4 col-md-6">
                <div class="program-card">
                  <div class="program-header">
                    <div class="program-icon">
                      <i class="bi <?php echo isset($row['icon']) ? htmlspecialchars($row['icon']) : 'bi-calendar-check'; ?>"></i>
                    </div>
                    <?php if ($has_divisi && isset($row['divisi']) && $row['divisi']): ?>
                    <div class="program-divisi"><?php echo htmlspecialchars($row['divisi']); ?></div>
                    <?php endif; ?>
                  </div>
                  <div class="program-body">
                    <h3 class="program-title"><?php echo htmlspecialchars($row['nama_program']); ?></h3>
                    <p class="program-description">
                      <?php echo isset($row['deskripsi']) ? htmlspecialchars($row['deskripsi']) : ''; ?>
                    </p>
                    <div class="program-meta">
                      <span class="program-tahun">
                        <i class="bi bi-calendar3"></i> 
                        <?php echo isset($row['tahun']) ? htmlspecialchars($row['tahun']) : date('Y'); ?>
                      </span>
                      <span class="program-status <?php echo $status_class; ?>">
                        <?php echo htmlspecialchars($status_text); ?>
                      </span>
                    </div>
                  </div>
                </div>
              </div>
              <?php
            }
          } else {
            ?>
            <div class="col-12">
              <div class="no-data">
                <i class="bi bi-calendar-x"></i>
                <h3>Belum Ada Program Kerja</h3>
                <p>Saat ini belum ada program kerja yang tersedia<?php echo $divisi_filter ? ' untuk divisi ini' : ''; ?>.</p>
              </div>
            </div>
            <?php
          }
          ?>
        </div>
      </div>
    </section>

  </main>

  <footer id="footer" class="footer dark-background">
    <div class="container">
      <div class="row gy-3">
        <div class="col-lg-3 col-md-6 d-flex">
          <i class="bi bi-geo-alt icon"></i>
          <div class="address">
            <h4>Alamat</h4>
            <p>Manado, Sulawesi Utara</p>
            <p>Indonesia</p>
          </div>
        </div>

        <div class="col-lg-3 col-md-6 d-flex">
          <i class="bi bi-telephone icon"></i>
          <div>
            <h4>Kontak</h4>
            <p>
              <strong>Email:</strong> <span>info@mahkota.org</span><br>
            </p>
          </div>
        </div>

        <div class="col-lg-3 col-md-6 d-flex">
          <i class="bi bi-clock icon"></i>
          <div>
            <h4>Jam Operasional</h4>
            <p>
              <strong>Senin-Jumat:</strong> <span>09:00 - 17:00</span><br>
            </p>
          </div>
        </div>

        <div class="col-lg-3 col-md-6">
          <h4>Ikuti Kami</h4>
          <div class="social-links d-flex">
            <a href="#" class="twitter"><i class="bi bi-twitter-x"></i></a>
            <a href="#" class="facebook"><i class="bi bi-facebook"></i></a>
            <a href="#" class="instagram"><i class="bi bi-instagram"></i></a>
            <a href="#" class="linkedin"><i class="bi bi-linkedin"></i></a>
          </div>
        </div>
      </div>
    </div>

    <div class="container copyright text-center mt-4">
      <p>© <span>Copyright</span> <strong class="px-1 sitename">Mahkota Insight</strong> <span>All Rights Reserved</span></p>
    </div>
  </footer>

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Preloader -->
  <div id="preloader"></div>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>

  <!-- Main JS File -->
  <script src="assets/js/main.js"></script>

</body>

</html>
