<?php
// Proteksi session admin
session_start();
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header('Location: login-page.php');
    exit;
}

$admin_name = $_SESSION['admin_username'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard - Admin Panel Mahkota</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/css/admin.css" rel="stylesheet">
</head>
<body>
  <!-- Sidebar -->
  <aside class="sidebar">
    <a href="admin-dashboard.php" class="sidebar-brand">
      <i class="bi bi-shield-check"></i>
      <span>MAHKOTA Admin</span>
    </a>
    
    <div class="sidebar-divider"></div>
    
    <div class="sidebar-heading">Dashboard</div>
    <ul class="nav">
      <li class="nav-item">
        <a href="admin-dashboard.php" class="nav-link active">
          <i class="bi bi-speedometer2"></i>
          <span>Dashboard</span>
        </a>
      </li>
    </ul>
    
    <div class="sidebar-divider"></div>
    
    <div class="sidebar-heading">Konten</div>
    <ul class="nav">
      <li class="nav-item">
        <a href="admin/artikel.php" class="nav-link">
          <i class="bi bi-newspaper"></i>
          <span>Artikel</span>
          <span class="badge badge-primary" id="badge-artikel">0</span>
        </a>
      </li>
      <li class="nav-item">
        <a href="admin/galeri.php" class="nav-link">
          <i class="bi bi-images"></i>
          <span>Galeri</span>
          <span class="badge badge-primary" id="badge-galeri">0</span>
        </a>
      </li>
      <li class="nav-item">
        <a href="admin/media-desain.php" class="nav-link">
          <i class="bi bi-palette"></i>
          <span>Media Desain</span>
        </a>
      </li>
      <li class="nav-item">
        <a href="admin/program-bidang.php" class="nav-link">
          <i class="bi bi-diagram-3"></i>
          <span>Program & Bidang</span>
        </a>
      </li>
    </ul>
    
    <div class="sidebar-divider"></div>
    
    <div class="sidebar-heading">Pengaturan</div>
    <ul class="nav">
      <li class="nav-item">
        <a href="admin/profil.php" class="nav-link">
          <i class="bi bi-building"></i>
          <span>Profil Organisasi</span>
        </a>
      </li>
      <li class="nav-item">
        <a href="admin/kontak.php" class="nav-link">
          <i class="bi bi-telephone"></i>
          <span>Informasi Kontak</span>
        </a>
      </li>
    </ul>
    
    <div class="sidebar-divider"></div>
    
    <ul class="nav">
      <li class="nav-item">
        <a href="logout.php" class="nav-link">
          <i class="bi bi-box-arrow-right"></i>
          <span>Logout</span>
        </a>
      </li>
    </ul>
  </aside>
  
  <!-- Main Content -->
  <div class="main-content">
    <!-- Topbar -->
    <header class="topbar">
      <div class="topbar-left">
        <button class="sidebar-toggle" id="sidebarToggle">
          <i class="bi bi-list"></i>
        </button>
        <div class="topbar-search">
          <i class="bi bi-search"></i>
          <input type="text" placeholder="Cari sesuatu...">
        </div>
      </div>
      
      <div class="topbar-right">
        <button class="topbar-icon">
          <i class="bi bi-bell"></i>
          <span class="badge badge-danger">3</span>
        </button>
        
        <div class="user-dropdown">
          <div class="user-avatar"><?php echo strtoupper(substr($admin_name, 0, 1)); ?></div>
          <div class="user-info">
            <div class="user-name"><?php echo htmlspecialchars($admin_name); ?></div>
            <div class="user-role">Administrator</div>
          </div>
          <i class="bi bi-chevron-down"></i>
        </div>
      </div>
    </header>
    
    <!-- Page Content -->
    <div class="page-content">
      <div class="page-header">
        <h1 class="page-title">Dashboard</h1>
        <p class="page-subtitle">Selamat datang kembali! Berikut ringkasan aktivitas website Mahkota.</p>
      </div>
      
      <!-- Stats Cards -->
      <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
          <div class="card stats-card">
            <div class="card-body">
              <div class="stats-icon primary">
                <i class="bi bi-newspaper"></i>
              </div>
              <div class="stats-label">Total Artikel</div>
              <div class="stats-value" id="count-artikel">0</div>
              <div class="stats-change positive">
                <i class="bi bi-arrow-up"></i>
                <span>12% dari bulan lalu</span>
              </div>
            </div>
          </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
          <div class="card stats-card">
            <div class="card-body">
              <div class="stats-icon success">
                <i class="bi bi-images"></i>
              </div>
              <div class="stats-label">Total Galeri</div>
              <div class="stats-value" id="count-galeri">0</div>
              <div class="stats-change positive">
                <i class="bi bi-arrow-up"></i>
                <span>8% dari bulan lalu</span>
              </div>
            </div>
          </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
          <div class="card stats-card">
            <div class="card-body">
              <div class="stats-icon info">
                <i class="bi bi-palette"></i>
              </div>
              <div class="stats-label">Media Desain</div>
              <div class="stats-value" id="count-media">0</div>
              <div class="stats-change positive">
                <i class="bi bi-arrow-up"></i>
                <span>5% dari bulan lalu</span>
              </div>
            </div>
          </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
          <div class="card stats-card">
            <div class="card-body">
              <div class="stats-icon warning">
                <i class="bi bi-eye"></i>
              </div>
              <div class="stats-label">Total Pengunjung</div>
              <div class="stats-value">1,234</div>
              <div class="stats-change positive">
                <i class="bi bi-arrow-up"></i>
                <span>15% dari bulan lalu</span>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Content Row -->
      <div class="row">
        <div class="col-lg-8 mb-4">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Aktivitas Terbaru</h3>
            </div>
            <div class="card-body">
              <div id="recent-activities">
                <div class="text-center py-4">
                  <div class="spinner"></div>
                  <p class="mt-2 text-muted">Memuat aktivitas...</p>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="col-lg-4 mb-4">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Akses Cepat</h3>
            </div>
            <div class="card-body">
              <div class="d-grid gap-2">
                <a href="admin/artikel.php" class="btn btn-primary">
                  <i class="bi bi-plus-circle"></i>
                  Tambah Artikel
                </a>
                <a href="admin/galeri.php" class="btn btn-success">
                  <i class="bi bi-image"></i>
                  Upload Galeri
                </a>
                <a href="admin/program-bidang.php" class="btn btn-info">
                  <i class="bi bi-diagram-3"></i>
                  Kelola Program
                </a>
                <a href="admin/profil.php" class="btn btn-warning">
                  <i class="bi bi-gear"></i>
                  Pengaturan
                </a>
              </div>
            </div>
          </div>
          
          <div class="card mt-4">
            <div class="card-header">
              <h3 class="card-title">Tips</h3>
            </div>
            <div class="card-body">
              <div class="alert alert-info">
                <i class="bi bi-lightbulb"></i>
                <strong>Tips:</strong> Pastikan untuk selalu backup data secara berkala untuk keamanan website.
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <script>
  // Fungsi untuk memuat statistik konten
  function loadStatistics() {
    // Memuat jumlah artikel
    fetch('api/count_artikel.php')
      .then(res => res.json())
      .then(data => {
        document.getElementById('count-artikel').textContent = data.count;
      })
      .catch(() => {
        document.getElementById('count-artikel').textContent = '?';
      });
      
    // Memuat jumlah galeri
    fetch('api/count_galeri.php')
      .then(res => res.json())
      .then(data => {
        document.getElementById('count-galeri').textContent = data.count;
      })
      .catch(() => {
        document.getElementById('count-galeri').textContent = '?';
      });
      
    // Memuat jumlah media desain
    fetch('api/count_media.php')
      .then(res => res.json())
      .then(data => {
        document.getElementById('count-media').textContent = data.count;
      })
      .catch(() => {
        document.getElementById('count-media').textContent = '?';
      });
  }
  
  // Fungsi untuk memuat aktivitas terbaru
  function loadRecentActivities() {
    fetch('api/recent_activities.php')
      .then(res => res.json())
      .then(data => {
        let html = '<ul class="list-group list-group-flush">';
        if (data.length === 0) {
          html = '<p class="text-center">Tidak ada aktivitas terbaru</p>';
        } else {
          data.forEach(activity => {
            html += `<li class="list-group-item small">
              <strong>${activity.action}</strong>: ${activity.detail}<br>
              <span class="text-muted">${activity.created_at}</span>
            </li>`;
          });
          html += '</ul>';
        }
        document.getElementById('recent-activities').innerHTML = html;
      })
      .catch(() => {
        document.getElementById('recent-activities').innerHTML = '<p class="text-center text-danger">Gagal memuat aktivitas</p>';
      });
  }
  
  // Sidebar toggle untuk mobile
  const sidebarToggle = document.getElementById('sidebarToggle');
  const sidebar = document.querySelector('.sidebar');
  
  sidebarToggle.addEventListener('click', function() {
    sidebar.classList.toggle('active');
  });
  
  // Memuat data saat halaman dimuat
  document.addEventListener('DOMContentLoaded', function() {
    loadStatistics();
    loadRecentActivities();
  });
  </script>
  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
