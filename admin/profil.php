<?php
// Proteksi halaman dengan session
session_start();
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header('Location: ../login-page.php');
    exit();
}

// Koneksi ke database
require_once('../db.php');
require_once('../lib_log.php');

// Fungsi untuk mencatat aktivitas admin
function logActivity($conn, $user_id, $activity) {
    $sql = "INSERT INTO admin_logs (user_id, activity, log_time) VALUES (?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $user_id, $activity);
    $stmt->execute();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Organisasi - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include('sidebar.php'); ?>
            
            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <h1 class="h2 mb-4">Profil Organisasi</h1>
                
                <div class="row">
                    <div class="col-md-8">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title">Informasi Dasar</h5>
                            </div>
                            <div class="card-body">
                                <form id="profilForm">
                                    <div class="mb-3">
                                        <label for="nama_organisasi" class="form-label">Nama Organisasi</label>
                                        <input type="text" class="form-control" id="nama_organisasi" name="nama_organisasi" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="slogan" class="form-label">Slogan/Tagline</label>
                                        <input type="text" class="form-control" id="slogan" name="slogan">
                                    </div>
                                    <div class="mb-3">
                                        <label for="deskripsi_singkat" class="form-label">Deskripsi Singkat</label>
                                        <textarea class="form-control" id="deskripsi_singkat" name="deskripsi_singkat" rows="2"></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="visi" class="form-label">Visi</label>
                                        <textarea class="form-control" id="visi" name="visi" rows="3"></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="misi" class="form-label">Misi</label>
                                        <textarea class="form-control" id="misi" name="misi" rows="5"></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="sejarah" class="form-label">Sejarah Organisasi</label>
                                        <textarea class="form-control" id="sejarah" name="sejarah" rows="5"></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Simpan Informasi</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title">Logo & Gambar</h5>
                            </div>
                            <div class="card-body">
                                <form id="logoForm" enctype="multipart/form-data">
                                    <div class="mb-3">
                                        <label for="logo" class="form-label">Logo Organisasi</label>
                                        <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                                        <div id="logoPreview" class="mt-2 text-center">
                                            <!-- Logo preview will be shown here -->
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="favicon" class="form-label">Favicon</label>
                                        <input type="file" class="form-control" id="favicon" name="favicon" accept="image/*">
                                        <small class="text-muted">Ukuran yang disarankan: 32x32 pixel</small>
                                        <div id="faviconPreview" class="mt-2 text-center">
                                            <!-- Favicon preview will be shown here -->
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="gambar_header" class="form-label">Gambar Header</label>
                                        <input type="file" class="form-control" id="gambar_header" name="gambar_header" accept="image/*">
                                        <div id="headerPreview" class="mt-2 text-center">
                                            <!-- Header image preview will be shown here -->
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Simpan Gambar</button>
                                </form>
                            </div>
                        </div>
                        
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Pengaturan Tampilan</h5>
                            </div>
                            <div class="card-body">
                                <form id="tampilanForm">
                                    <div class="mb-3">
                                        <label for="warna_utama" class="form-label">Warna Utama</label>
                                        <input type="color" class="form-control form-control-color" id="warna_utama" name="warna_utama" value="#4154f1">
                                    </div>
                                    <div class="mb-3">
                                        <label for="warna_sekunder" class="form-label">Warna Sekunder</label>
                                        <input type="color" class="form-control form-control-color" id="warna_sekunder" name="warna_sekunder" value="#2db6fa">
                                    </div>
                                    <div class="mb-3">
                                        <label for="font_utama" class="form-label">Font Utama</label>
                                        <select class="form-select" id="font_utama" name="font_utama">
                                            <option value="Nunito">Nunito</option>
                                            <option value="Open Sans">Open Sans</option>
                                            <option value="Roboto">Roboto</option>
                                            <option value="Poppins">Poppins</option>
                                            <option value="Montserrat">Montserrat</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Simpan Pengaturan</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title">Struktur Organisasi</h5>
                    </div>
                    <div class="card-body">
                        <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addAnggotaModal">
                            <i class="bi bi-plus-circle"></i> Tambah Anggota
                        </button>
                        
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <th>Jabatan</th>
                                        <th>Periode</th>
                                        <th>Foto</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="anggotaTableBody">
                                    <!-- Data will be loaded here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <!-- Add Anggota Modal -->
    <div class="modal fade" id="addAnggotaModal" tabindex="-1" aria-labelledby="addAnggotaModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addAnggotaModalLabel">Tambah Anggota Organisasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="anggotaForm" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" id="nama" name="nama" required>
                        </div>
                        <div class="mb-3">
                            <label for="jabatan" class="form-label">Jabatan</label>
                            <input type="text" class="form-control" id="jabatan" name="jabatan" required>
                        </div>
                        <div class="mb-3">
                            <label for="periode" class="form-label">Periode</label>
                            <input type="text" class="form-control" id="periode" name="periode" placeholder="2023-2024" required>
                        </div>
                        <div class="mb-3">
                            <label for="foto" class="form-label">Foto</label>
                            <input type="file" class="form-control" id="foto" name="foto" accept="image/*">
                        </div>
                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="urutan" class="form-label">Urutan</label>
                            <input type="number" class="form-control" id="urutan" name="urutan" min="1" value="1">
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Nonaktif</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="btnSaveAnggota">Simpan</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Edit Anggota Modal -->
    <div class="modal fade" id="editAnggotaModal" tabindex="-1" aria-labelledby="editAnggotaModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editAnggotaModalLabel">Edit Anggota Organisasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editAnggotaForm" enctype="multipart/form-data">
                        <input type="hidden" id="edit_id" name="id">
                        <div class="mb-3">
                            <label for="edit_nama" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" id="edit_nama" name="nama" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_jabatan" class="form-label">Jabatan</label>
                            <input type="text" class="form-control" id="edit_jabatan" name="jabatan" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_periode" class="form-label">Periode</label>
                            <input type="text" class="form-control" id="edit_periode" name="periode" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_foto" class="form-label">Foto Baru (Opsional)</label>
                            <input type="file" class="form-control" id="edit_foto" name="foto" accept="image/*">
                            <div id="current_foto_container" class="mt-2 text-center"></div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_deskripsi" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="edit_deskripsi" name="deskripsi" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="edit_urutan" class="form-label">Urutan</label>
                            <input type="number" class="form-control" id="edit_urutan" name="urutan" min="1">
                        </div>
                        <div class="mb-3">
                            <label for="edit_status" class="form-label">Status</label>
                            <select class="form-select" id="edit_status" name="status">
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Nonaktif</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="btnSaveEditAnggota">Simpan Perubahan</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Load profil data
            loadProfilData();
            
            // Load anggota data
            loadAnggotaData();
            
            // Preview logo when selected
            document.getElementById('logo').addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        document.getElementById('logoPreview').innerHTML = `
                            <img src="${e.target.result}" alt="Logo Preview" style="max-width: 150px; max-height: 150px;">
                        `;
                    }
                    reader.readAsDataURL(file);
                }
            });
            
            // Preview favicon when selected
            document.getElementById('favicon').addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        document.getElementById('faviconPreview').innerHTML = `
                            <img src="${e.target.result}" alt="Favicon Preview" style="max-width: 32px; max-height: 32px;">
                        `;
                    }
                    reader.readAsDataURL(file);
                }
            });
            
            // Preview header image when selected
            document.getElementById('gambar_header').addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        document.getElementById('headerPreview').innerHTML = `
                            <img src="${e.target.result}" alt="Header Preview" style="max-width: 100%; max-height: 150px;">
                        `;
                    }
                    reader.readAsDataURL(file);
                }
            });
            
            // Submit profil form
            document.getElementById('profilForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                
                fetch('../profil_update.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert('Informasi profil berhasil disimpan!');
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menyimpan informasi profil.');
                });
            });
            
            // Submit logo form
            document.getElementById('logoForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                
                fetch('../profil_upload_images.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert('Gambar berhasil disimpan!');
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menyimpan gambar.');
                });
            });
            
            // Submit tampilan form
            document.getElementById('tampilanForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                
                fetch('../profil_update_tampilan.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert('Pengaturan tampilan berhasil disimpan!');
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menyimpan pengaturan tampilan.');
                });
            });
            
            // Save anggota
            document.getElementById('btnSaveAnggota').addEventListener('click', function() {
                const formData = new FormData(document.getElementById('anggotaForm'));
                
                fetch('../anggota_create.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert('Anggota berhasil ditambahkan!');
                        document.getElementById('anggotaForm').reset();
                        document.getElementById('addAnggotaModal').querySelector('.btn-close').click();
                        loadAnggotaData();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menambahkan anggota.');
                });
            });
            
            // Save edit anggota
            document.getElementById('btnSaveEditAnggota').addEventListener('click', function() {
                const formData = new FormData(document.getElementById('editAnggotaForm'));
                
                fetch('../anggota_update.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert('Anggota berhasil diperbarui!');
                        document.getElementById('editAnggotaModal').querySelector('.btn-close').click();
                        loadAnggotaData();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat memperbarui anggota.');
                });
            });
        });
        
        // Function to load profil data
        function loadProfilData() {
            fetch('../profil_get.php')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        const profil = data.profil;
                        
                        // Fill profil form
                        document.getElementById('nama_organisasi').value = profil.nama_organisasi || '';
                        document.getElementById('slogan').value = profil.slogan || '';
                        document.getElementById('deskripsi_singkat').value = profil.deskripsi_singkat || '';
                        document.getElementById('visi').value = profil.visi || '';
                        document.getElementById('misi').value = profil.misi || '';
                        document.getElementById('sejarah').value = profil.sejarah || '';
                        
                        // Show logo preview if exists
                        if (profil.logo) {
                            document.getElementById('logoPreview').innerHTML = `
                                <img src="../uploads/${profil.logo}" alt="Logo" style="max-width: 150px; max-height: 150px;">
                            `;
                        }
                        
                        // Show favicon preview if exists
                        if (profil.favicon) {
                            document.getElementById('faviconPreview').innerHTML = `
                                <img src="../uploads/${profil.favicon}" alt="Favicon" style="max-width: 32px; max-height: 32px;">
                            `;
                        }
                        
                        // Show header image preview if exists
                        if (profil.gambar_header) {
                            document.getElementById('headerPreview').innerHTML = `
                                <img src="../uploads/${profil.gambar_header}" alt="Header" style="max-width: 100%; max-height: 150px;">
                            `;
                        }
                        
                        // Fill tampilan form
                        document.getElementById('warna_utama').value = profil.warna_utama || '#4154f1';
                        document.getElementById('warna_sekunder').value = profil.warna_sekunder || '#2db6fa';
                        document.getElementById('font_utama').value = profil.font_utama || 'Nunito';
                    } else {
                        console.error('Error:', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }
        
        // Function to load anggota data
        function loadAnggotaData() {
            fetch('../anggota_read.php')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        renderAnggotaTable(data.anggota);
                    } else {
                        console.error('Error:', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }
        
        // Function to render anggota table
        function renderAnggotaTable(anggota) {
            const tableBody = document.getElementById('anggotaTableBody');
            tableBody.innerHTML = '';
            
            anggota.forEach(item => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${item.nama}</td>
                    <td>${item.jabatan}</td>
                    <td>${item.periode}</td>
                    <td>
                        ${item.foto ? 
                            `<img src="../uploads/${item.foto}" alt="${item.nama}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%;">` : 
                            '<span class="badge bg-secondary">Tidak ada foto</span>'}
                    </td>
                    <td>
                        <span class="badge ${item.status === 'aktif' ? 'bg-success' : 'bg-secondary'}">
                            ${item.status}
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-primary" onclick="editAnggota(${item.id})">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deleteAnggota(${item.id})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        }
        
        // Function to edit anggota
        function editAnggota(id) {
            fetch(`../anggota_get.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        const anggota = data.anggota;
                        document.getElementById('edit_id').value = anggota.id;
                        document.getElementById('edit_nama').value = anggota.nama;
                        document.getElementById('edit_jabatan').value = anggota.jabatan;
                        document.getElementById('edit_periode').value = anggota.periode;
                        document.getElementById('edit_deskripsi').value = anggota.deskripsi || '';
                        document.getElementById('edit_urutan').value = anggota.urutan;
                        document.getElementById('edit_status').value = anggota.status;
                        
                        // Show current foto if exists
                        const fotoContainer = document.getElementById('current_foto_container');
                        if (anggota.foto) {
                            fotoContainer.innerHTML = `
                                <p>Foto saat ini:</p>
                                <img src="../uploads/${anggota.foto}" alt="Current Foto" style="max-width: 100px; max-height: 100px; border-radius: 50%;">
                                <input type="hidden" name="current_foto" value="${anggota.foto}">
                            `;
                        } else {
                            fotoContainer.innerHTML = '<p>Tidak ada foto saat ini</p>';
                        }
                        
                        // Show modal
                        new bootstrap.Modal(document.getElementById('editAnggotaModal')).show();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat mengambil data anggota.');
                });
        }
        
        // Function to delete anggota
        function deleteAnggota(id) {
            if (confirm('Apakah Anda yakin ingin menghapus anggota ini?')) {
                fetch('../anggota_delete.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `id=${id}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert('Anggota berhasil dihapus!');
                        loadAnggotaData();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menghapus anggota.');
                });
            }
        }
    </script>
</body>
</html>