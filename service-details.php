<?php
// service-details.php
require_once 'koneksi.php';
$sql = 'SELECT * FROM artikel ORDER BY tanggal DESC';
$result = $conn->query($sql);
$artikels = [];
while ($row = $result->fetch_assoc()) {
    $artikels[] = $row;
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Service Details - Devin Bootstrap Template</title>
  <meta name="description" content="">
  <meta name="keywords" content="">
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
  <link href="assets/css/main.css" rel="stylesheet">
</head>
<body class="index-page">
  <header id="header" class="header d-flex align-items-center fixed-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center justify-content-between">
      <a href="index.html" class="logo d-flex align-items-center">
        <img src="assets/img/illustration/LOGO MAHKOTA.png" alt="Logo Mahkota" style="height:40px;width:auto;margin-right:10px;">
        <h1 class="sitename" style="margin-bottom:0;">Insight Mahkota</h1>
      </a>
      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="#hero" class="active">Utama</a></li>
          <li><a href="#about">Tentang</a></li>
          <li><a href="#services">Program</a></li>
          <li><a href="#portfolio">Galeri</a></li>
          <li><a href="#team">Pengurus</a></li>
          <li><a href="login.html">Login</a></li>
          <li><a href="#contact">Kontak</a></li>
        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>
    </div>
  </header>
  <main class="main">
    <div class="page-title light-background">
      <div class="container">
        <h1>Artikel Kegiatan Organisasi</h1>
        <nav class="breadcrumbs">
          <ol>
            <li><a href="index.html">Home</a></li>
            <li class="current">Artikel</li>
          </ol>
        </nav>
      </div>
    </div>
    <section id="single-article" class="service-details section">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-lg-9">
            <?php if (empty($artikels)): ?>
              <div class="alert alert-info">Belum ada artikel.</div>
            <?php else: ?>
              <?php foreach ($artikels as $artikel): ?>
                <article class="card border-0 shadow-sm p-4 mb-5">
                  <?php if ($artikel['gambar']): ?>
                  <div class="mb-4 text-center">
                    <img src="<?php echo htmlspecialchars($artikel['gambar']); ?>" alt="<?php echo htmlspecialchars($artikel['judul']); ?>" class="img-fluid rounded" style="max-width: 500px; width: 100%; height: auto;">
                  </div>
                  <?php endif; ?>
                  <h2 class="mb-2"><?php echo htmlspecialchars($artikel['judul']); ?></h2>
                  <div class="mb-3 text-muted small">
                    Kategori: <span class="badge bg-info text-dark"><?php echo htmlspecialchars($artikel['kategori']); ?></span><br>
                    Dipublikasikan: <?php echo date('d M Y', strtotime($artikel['tanggal'])); ?> &bull; Oleh: Admin Organisasi
                  </div>
                  <div class="article-content" style="text-align:justify;">
                    <?php echo nl2br(htmlspecialchars($artikel['isi'])); ?>
                  </div>
                </article>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
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
