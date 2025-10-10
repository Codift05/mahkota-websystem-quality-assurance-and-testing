<?php
// Proteksi halaman dengan session
session_start();
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header('Location: ../login-page.php');
    exit;
}

// Koneksi database
require_once '../db.php';

// Proses form jika ada
$message = '';
$error = '';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Artikel - Admin Dashboard</title>
    <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="../assets/css/admin.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include 'sidebar.php'; ?>
            
            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Manajemen Artikel</h1>
                </div>
                
                <?php if($message): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>
                
                <?php if($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>
                
                <!-- Form Tambah Artikel -->
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="bi bi-file-text me-1"></i> Tambah Artikel Baru
                    </div>
                    <div class="card-body">
                        <form id="formTambah" enctype="multipart/form-data" class="row g-3">
                            <div class="col-md-6">
                                <label for="judul" class="form-label">Judul Artikel</label>
                                <input type="text" name="judul" id="judul" class="form-control" placeholder="Judul artikel" required>
                            </div>
                            <div class="col-md-6">
                                <label for="kategori" class="form-label">Kategori</label>
                                <select name="kategori" id="kategori" class="form-select" required>
                                    <option value="">Pilih Kategori</option>
                                    <option value="Inspirasi">Inspirasi</option>
                                    <option value="Berita">Berita</option>
                                    <option value="Kegiatan">Kegiatan</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label for="gambar" class="form-label">Gambar Artikel</label>
                                <input type="file" name="gambar" id="gambar" class="form-control">
                                <div class="form-text">Format yang diperbolehkan: JPG, JPEG, PNG. Ukuran maksimal: 2MB.</div>
                            </div>
                            <div class="col-12">
                                <label for="isi" class="form-label">Isi Artikel</label>
                                <textarea name="isi" id="isi" class="form-control" placeholder="Isi artikel" rows="6" required></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-success">Tambah Artikel</button>
                            </div>
                            <div id="tambahMsg" class="text-danger"></div>
                        </form>
                    </div>
                </div>
                
                <!-- Tabel Artikel -->
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-list-ul me-1"></i> Daftar Artikel
                    </div>
                    <div class="card-body">
                        <div id="artikelList" class="table-responsive"></div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        // Tampilkan daftar artikel
        function loadArtikel() {
            fetch('../api/artikel/read.php')
                .then(res => res.json())
                .then(data => {
                    let html = '<table class="table table-bordered table-striped"><thead><tr><th>Judul</th><th>Kategori</th><th>Gambar</th><th>Tanggal</th><th>Aksi</th></tr></thead><tbody>';
                    data.forEach(a => {
                        html += `<tr>
                            <td>${a.judul}</td>
                            <td>${a.kategori || ''}</td>
                            <td>${a.gambar ? `<img src=\"../${a.gambar}\" width=\"80\">` : ''}</td>
                            <td>${a.tanggal}</td>
                            <td>
                                <button class="btn btn-warning btn-sm" onclick="editArtikel(${a.id}, '${encodeURIComponent(a.judul)}', '${encodeURIComponent(a.kategori)}', '${encodeURIComponent(a.isi)}')">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                                <button class="btn btn-danger btn-sm" onclick="hapusArtikel(${a.id})">
                                    <i class="bi bi-trash"></i> Hapus
                                </button>
                            </td>
                        </tr>`;
                    });
                    html += '</tbody></table>';
                    document.getElementById('artikelList').innerHTML = html;
                });
        }
        loadArtikel();

        // Tambah artikel
        document.getElementById('formTambah').onsubmit = function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            fetch('../api/artikel/create.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    this.reset();
                    document.getElementById('tambahMsg').textContent = '';
                    loadArtikel();
                    // Tampilkan pesan sukses
                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'alert alert-success alert-dismissible fade show';
                    alertDiv.innerHTML = `
                        Artikel berhasil ditambahkan
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    `;
                    document.querySelector('.card-body').prepend(alertDiv);
                } else {
                    document.getElementById('tambahMsg').textContent = data.error || 'Gagal menambah artikel';
                }
            });
        };

        // Edit artikel
        function editArtikel(id, judul, kategori, isi) {
            // Isi form dengan data artikel
            document.getElementById('judul').value = decodeURIComponent(judul);
            document.getElementById('kategori').value = decodeURIComponent(kategori);
            document.getElementById('isi').value = decodeURIComponent(isi);
            
            // Ubah tombol submit
            const submitBtn = document.querySelector('button[type="submit"]');
            submitBtn.textContent = 'Update Artikel';
            submitBtn.classList.replace('btn-success', 'btn-primary');
            
            // Ubah action form
            const form = document.getElementById('formTambah');
            form.onsubmit = function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                formData.append('id', id);
                
                fetch('../api/artikel/update.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        // Reset form dan tombol
                        this.reset();
                        submitBtn.textContent = 'Tambah Artikel';
                        submitBtn.classList.replace('btn-primary', 'btn-success');
                        
                        // Reset action form
                        form.onsubmit = function(e) {
                            e.preventDefault();
                            const formData = new FormData(this);
                            fetch('../artikel_create.php', {
                                method: 'POST',
                                body: formData
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    this.reset();
                                    document.getElementById('tambahMsg').textContent = '';
                                    loadArtikel();
                                } else {
                                    document.getElementById('tambahMsg').textContent = data.error || 'Gagal menambah artikel';
                                }
                            });
                        };
                        
                        document.getElementById('tambahMsg').textContent = '';
                        loadArtikel();
                        
                        // Tampilkan pesan sukses
                        const alertDiv = document.createElement('div');
                        alertDiv.className = 'alert alert-success alert-dismissible fade show';
                        alertDiv.innerHTML = `
                            Artikel berhasil diupdate
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        `;
                        document.querySelector('.card-body').prepend(alertDiv);
                    } else {
                        document.getElementById('tambahMsg').textContent = data.error || 'Gagal update artikel';
                    }
                });
            };
        }

        // Hapus artikel
        function hapusArtikel(id) {
            if (confirm('Apakah Anda yakin ingin menghapus artikel ini?')) {
                const formData = new FormData();
                formData.append('id', id);
                fetch('../api/artikel/delete.php', {
                    method: 'POST',
                    body: formData
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            loadArtikel();
                            
                            // Tampilkan pesan sukses
                            const alertDiv = document.createElement('div');
                            alertDiv.className = 'alert alert-success alert-dismissible fade show';
                            alertDiv.innerHTML = `
                                Artikel berhasil dihapus
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            `;
                            document.querySelector('.card-body').prepend(alertDiv);
                        } else {
                            alert(data.error || 'Gagal menghapus artikel');
                        }
                    });
            }
        }
    </script>
</body>
</html>