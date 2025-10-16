<?php
// Cek apakah sudah login, jika ya redirect ke dashboard
session_start();
if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true) {
    header('Location: admin-dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Admin Panel</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
  <link rel="stylesheet" href="assets/css/main.css">
  <style>
    :root {
      --primary-color: #4e73df;
      --secondary-color: #f8f9fc;
      --accent-color: #2e59d9;
      --text-color: #5a5c69;
      --light-text: #858796;
      --dark-text: #3a3b45;
      --success-color: #1cc88a;
      --error-color: #e74a3b;
      --sidebar-bg: #e8f4f8;
      --sidebar-blue: #4e73df;
    }
    
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    
    body {
      font-family: 'Nunito', 'Segoe UI', Roboto, sans-serif;
      background: #fff;
      min-height: 100vh;
      display: flex;
      margin: 0;
    }
    
    .login-container {
      display: flex;
      width: 100%;
      min-height: 100vh;
    }
    
    .login-sidebar {
      width: 420px;
      background-color: var(--sidebar-bg);
      padding: 40px;
      display: flex;
      flex-direction: column;
      position: relative;
    }
    
    .sidebar-header {
      margin-bottom: 40px;
    }
    
    .sidebar-header h2 {
      font-size: 1.5rem;
      font-weight: 700;
      color: var(--dark-text);
      margin-bottom: 15px;
    }
    
    .sidebar-description {
      color: var(--text-color);
      font-size: 0.95rem;
      line-height: 1.6;
      margin-bottom: 30px;
    }
    
    .sidebar-features {
      flex: 1;
    }
    
    .feature-item {
      display: flex;
      align-items: flex-start;
      margin-bottom: 25px;
      padding: 15px;
      background: white;
      border-radius: 10px;
      transition: all 0.3s;
    }
    
    .feature-item:hover {
      transform: translateX(5px);
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .feature-icon {
      width: 45px;
      height: 45px;
      background: var(--sidebar-blue);
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-right: 15px;
      flex-shrink: 0;
    }
    
    .feature-icon i {
      color: white;
      font-size: 1.2rem;
    }
    
    .feature-content h4 {
      font-size: 1rem;
      font-weight: 700;
      color: var(--dark-text);
      margin-bottom: 5px;
    }
    
    .feature-content p {
      font-size: 0.85rem;
      color: var(--light-text);
      margin: 0;
    }
    
    .sidebar-footer {
      margin-top: auto;
      padding-top: 20px;
      border-top: 1px solid #d1e7f0;
    }
    
    .sidebar-footer p {
      font-size: 0.85rem;
      color: var(--light-text);
      margin: 0;
    }
    
    .login-form {
      flex: 1;
      padding: 60px 80px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      background: white;
    }
    
    .form-header {
      margin-bottom: 40px;
    }
    
    .back-link {
      display: inline-flex;
      align-items: center;
      color: var(--primary-color);
      text-decoration: none;
      font-size: 0.9rem;
      margin-bottom: 20px;
      font-weight: 600;
    }
    
    .back-link i {
      margin-right: 5px;
    }
    
    .back-link:hover {
      text-decoration: underline;
    }
    
    .logo-section {
      text-align: center;
      margin-bottom: 30px;
    }
    
    .logo-section img {
      max-width: 120px;
      height: auto;
      margin-bottom: 15px;
    }
    
    .login-form h2 {
      color: var(--dark-text);
      font-weight: 700;
      margin-bottom: 10px;
      font-size: 1.8rem;
    }
    
    .login-form .welcome-text {
      color: var(--light-text);
      margin-bottom: 10px;
      font-size: 0.95rem;
    }
    
    .login-form .highlight {
      color: var(--primary-color);
      font-weight: 700;
    }
    
    .form-group {
      margin-bottom: 20px;
    }
    
    .form-group label {
      display: block;
      margin-bottom: 8px;
      color: var(--dark-text);
      font-weight: 600;
      font-size: 0.9rem;
    }
    
    .form-control {
      width: 100%;
      padding: 15px;
      border: 1px solid #e3e6f0;
      border-radius: 10px;
      font-size: 1rem;
      transition: all 0.3s;
    }
    
    .form-control:focus {
      border-color: var(--primary-color);
      box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.25);
      outline: none;
    }
    
    .input-group {
      position: relative;
    }
    
    .input-group .form-control {
      padding-right: 45px;
    }
    
    .input-group .toggle-password {
      position: absolute;
      right: 15px;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      color: var(--light-text);
      cursor: pointer;
      z-index: 5;
    }
    
    .btn-login {
      width: 100%;
      padding: 15px;
      background-color: var(--primary-color);
      border: none;
      border-radius: 10px;
      color: white;
      font-weight: 700;
      font-size: 1rem;
      cursor: pointer;
      transition: all 0.3s;
      margin-top: 10px;
    }
    
    .btn-login:hover {
      background-color: var(--accent-color);
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(78, 115, 223, 0.4);
    }
    
    .login-footer {
      margin-top: 30px;
      text-align: center;
      color: var(--light-text);
      font-size: 0.9rem;
    }
    
    .login-footer a {
      color: var(--primary-color);
      text-decoration: none;
      font-weight: 600;
    }
    
    .login-footer a:hover {
      text-decoration: underline;
    }
    
    #loginError {
      background-color: #f8d7da;
      color: #721c24;
      padding: 10px;
      border-radius: 5px;
      margin-top: 20px;
      display: none;
      text-align: center;
      font-size: 0.9rem;
    }
    
    .loading-spinner {
      display: inline-block;
      width: 20px;
      height: 20px;
      border: 3px solid rgba(255, 255, 255, 0.3);
      border-radius: 50%;
      border-top-color: white;
      animation: spin 1s ease-in-out infinite;
      margin-right: 10px;
      display: none;
    }
    
    @keyframes spin {
      to { transform: rotate(360deg); }
    }
    
    .checkbox-wrapper {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 20px;
    }
    
    .checkbox-wrapper label {
      display: flex;
      align-items: center;
      font-size: 0.9rem;
      color: var(--text-color);
      cursor: pointer;
    }
    
    .checkbox-wrapper input[type="checkbox"] {
      margin-right: 8px;
    }
    
    .forgot-password {
      color: var(--primary-color);
      text-decoration: none;
      font-size: 0.9rem;
      font-weight: 600;
    }
    
    .forgot-password:hover {
      text-decoration: underline;
    }
    
    @media (max-width: 992px) {
      .login-sidebar {
        display: none;
      }
      
      .login-form {
        padding: 40px 30px;
      }
    }
    
    @media (max-width: 576px) {
      .login-form {
        padding: 30px 20px;
      }
      
      .login-form h2 {
        font-size: 1.5rem;
      }
    }
  </style>
</head>
<body>
  <div class="login-container">
    <!-- Sidebar Kiri -->
    <div class="login-sidebar">
      <div class="sidebar-header">
        <h2>Single Sign On</h2>
        <p class="sidebar-description">
          Hi Digiers, Selarang Akun-Mu Dapat Digunakan Untuk Berbagai Platform. 
          Nikmati Berbagai Kemudahan Akses Platform Dalam Satu Akun.
        </p>
      </div>
      
      <div class="sidebar-features">
        <div class="feature-item">
          <div class="feature-icon">
            <i class="bi bi-newspaper"></i>
          </div>
          <div class="feature-content">
            <h4>Manajemen Artikel</h4>
            <p>Platform untuk mengelola artikel dan berita organisasi</p>
          </div>
        </div>
        
        <div class="feature-item">
          <div class="feature-icon">
            <i class="bi bi-images"></i>
          </div>
          <div class="feature-content">
            <h4>Galeri Media</h4>
            <p>Kelola galeri foto dan dokumentasi kegiatan</p>
          </div>
        </div>
        
        <div class="feature-item">
          <div class="feature-icon">
            <i class="bi bi-calendar-check"></i>
          </div>
          <div class="feature-content">
            <h4>Program Kerja</h4>
            <p>Atur dan pantau program kerja organisasi</p>
          </div>
        </div>
        
        <div class="feature-item">
          <div class="feature-icon">
            <i class="bi bi-gear"></i>
          </div>
          <div class="feature-content">
            <h4>Pengaturan Website</h4>
            <p>Konfigurasi dan kelola pengaturan website</p>
          </div>
        </div>
      </div>
      
      <div class="sidebar-footer">
        <p>&copy; 2025 MAHKOTA Manado. All rights reserved.</p>
      </div>
    </div>
    
    <!-- Form Login Kanan -->
    <div class="login-form">
      <div class="form-header">
        <a href="index.html" class="back-link">
          <i class="bi bi-arrow-left"></i> Beranda
        </a>
        
        <div class="logo-section">
          <img src="assets/img/about/LOGO MAHKOTA (1) (1).png" alt="Logo MAHKOTA">
        </div>
        
        <h2>Log In Akun</h2>
        <p class="welcome-text">Hi, Selamat Datang <span class="highlight">#AdminMAHKOTA</span></p>
      </div>
      
      <form id="loginForm" action="#" method="post">
        <div class="form-group">
          <label for="username">Email / No Handphone <span style="color: red;">*</span></label>
          <input type="text" class="form-control" id="username" name="username" required placeholder="Email">
        </div>
        
        <div class="form-group">
          <label for="password">Password <span style="color: red;">*</span></label>
          <div class="input-group">
            <input type="password" class="form-control" id="password" name="password" required placeholder="**********">
            <button type="button" class="toggle-password" id="togglePassword">
              <i class="bi bi-eye"></i>
            </button>
          </div>
        </div>
        
        <div class="checkbox-wrapper">
          <label>
            <input type="checkbox" name="remember" id="remember">
            Ingat Saya
          </label>
          <a href="#" class="forgot-password">Lupa Password</a>
        </div>
        
        <button type="submit" class="btn-login">
          <span class="loading-spinner" id="loginSpinner"></span>
          <span id="loginText">LOGIN</span>
        </button>
        
        <div id="loginError"></div>
      </form>
    </div>
  </div>
  
  <script>
    // Toggle password visibility
    document.getElementById('togglePassword').addEventListener('click', function() {
      const passwordInput = document.getElementById('password');
      const icon = this.querySelector('i');
      
      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
      } else {
        passwordInput.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
      }
    });
    
    // Form submission
    const form = document.getElementById('loginForm');
    const errorDiv = document.getElementById('loginError');
    const loginSpinner = document.getElementById('loginSpinner');
    const loginText = document.getElementById('loginText');
    
    form.addEventListener('submit', async function(e) {
      e.preventDefault();
      errorDiv.style.display = 'none';
      loginSpinner.style.display = 'inline-block';
      loginText.textContent = 'Memproses...';
      
      const formData = new FormData(form);
      
      try {
        const res = await fetch('login.php', {
          method: 'POST',
          body: formData
        });
        
        const data = await res.json();
        
        if (data.success) {
          loginText.textContent = 'Berhasil!';
          window.location.href = 'admin-dashboard.php';
        } else {
          loginSpinner.style.display = 'none';
          loginText.textContent = 'Login';
          errorDiv.textContent = data.error || 'Username atau password salah. Silakan coba lagi.';
          errorDiv.style.display = 'block';
        }
      } catch (err) {
        loginSpinner.style.display = 'none';
        loginText.textContent = 'Login';
        errorDiv.textContent = 'Terjadi kesalahan koneksi. Silakan coba lagi nanti.';
        errorDiv.style.display = 'block';
      }
    });
    
    // Simple animation on load
    document.addEventListener('DOMContentLoaded', function() {
      const loginContainer = document.querySelector('.login-container');
      loginContainer.style.opacity = '0';
      loginContainer.style.transform = 'translateY(20px)';
      loginContainer.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
      
      setTimeout(() => {
        loginContainer.style.opacity = '1';
        loginContainer.style.transform = 'translateY(0)';
      }, 200);
    });
  </script>
</body>
</html>
