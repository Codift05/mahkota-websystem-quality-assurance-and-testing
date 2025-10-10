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

// Proses tambah galeri
if (isset($_POST['tambah'])) {
    $judul = $conn->real_escape_string($_POST['judul']);
    $deskripsi = $conn->real_escape_string($_POST['deskripsi']);
    $kategori = $conn->real_escape_string($_POST['kategori']);
    $tanggal_upload = date('Y-m-d H:i:s');
    
    // Upload gambar
    $target_dir = "../assets/img/galeri/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $file_name = basename($_FILES["gambar"]["name"]);
    $target_file = $target_dir . time() . '_' . $file_name;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    
    // Validasi file
    $valid_extensions = array("jpg", "jpeg", "png", "gif");
    if (in_array($imageFileType, $valid_extensions)) {
        if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
            $file_path = str_replace('../', '', $target_file);
            
            // Simpan ke database
            $sql = "INSERT INTO galeri (judul, deskripsi, kategori, file_path, tanggal_upload) 
                    VALUES ('$judul', '$deskripsi', '$kategori', '$file_path', '$tanggal_upload')";
            
            if ($conn->query($sql) === TRUE) {
                $message = "Galeri berhasil ditambahkan";
                
                // Log aktivitas
                $admin_id = $_SESSION['admin_id'];
                $log_sql = "INSERT INTO log_aktivitas (admin_id, aktivitas, tanggal) 
                           VALUES ('$admin_id', 'Menambahkan galeri: $judul', '$tanggal_upload')";
                $conn->query($log_sql);
            } else {
                $error = "Error: " . $sql . "<br>" . $conn->error;
            }
        } else {
            $error = "Maaf, terjadi kesalahan saat mengupload file.";
        }
    } else {
        $error = "Maaf, hanya file JPG, JPEG, PNG & GIF yang diperbolehkan.";
    }
}

// Proses hapus galeri
if (isset($_GET['hapus'])) {
    $id = $conn->real_escape_string($_GET['hapus']);
    
    // Ambil info file sebelum dihapus
    $file_query = "SELECT judul, file_path FROM galeri WHERE id = '$id'";
    $file_result = $conn->query($file_query);
    
    if ($file_result->num_rows > 0) {
        $file_data = $file_result->fetch_assoc();
        $file_to_delete = '../' . $file_data['file_path'];
        
        // Hapus file fisik jika ada
        if (file_exists($file_to_delete)) {
            unlink($file_to_delete);
        }
        
        // Hapus dari database
        $sql = "DELETE FROM galeri WHERE id = '$id'";
        if ($conn->query($sql) === TRUE) {
            $message = "Galeri berhasil dihapus";
            
            // Log aktivitas
            $admin_id = $_SESSION['admin_id'];
            $tanggal = date('Y-m-d H:i:s');
            $log_sql = "INSERT INTO log_aktivitas (admin_id, aktivitas, tanggal) 
                       VALUES ('$admin_id', 'Menghapus galeri: {$file_data['judul']}', '$tanggal')";
            $conn->query($log_sql);
        } else {
            $error = "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

// Ambil data galeri untuk ditampilkan
$sql = "SELECT * FROM galeri ORDER BY tanggal_upload DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Galeri - Admin Dashboard</title>
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
                    <h1 class="h2">Manajemen Galeri</h1>
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
                
                <!-- Form Tambah Galeri -->
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="bi bi-plus-circle me-1"></i> Tambah Galeri Baru
                    </div>
                    <div class="card-body">
                        <form action="" method="post" enctype="multipart/form-data">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="judul" class="form-label">Judul</label>
                                    <input type="text" class="form-control" id="judul" name="judul" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="kategori" class="form-label">Kategori</label>
                                    <select class="form-select" id="kategori" name="kategori" required>
                                        <option value="">Pilih Kategori</option>
                                        <option value="Kegiatan">Kegiatan</option>
                                        <option value="Dokumentasi">Dokumentasi</option>
                                        <option value="Acara">Acara</option>
                                        <option value="Lainnya">Lainnya</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="deskripsi" class="form-label">Deskripsi</label>
                                <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="gambar" class="form-label">Upload Gambar</label>
                                <input class="form-control" type="file" id="gambar" name="gambar" accept="image/*" required>
                                <div class="form-text">Format yang diperbolehkan: JPG, JPEG, PNG, GIF. Ukuran maksimal: 2MB.</div>
                            </div>
                            <button type="submit" name="tambah" class="btn btn-primary">Simpan</button>
                        </form>
                    </div>
                </div>
                
                <!-- Tabel Galeri -->
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-images me-1"></i> Daftar Galeri
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Gambar</th>
                                        <th>Judul</th>
                                        <th>Kategori</th>
                                        <th>Tanggal Upload</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    if ($result && $result->num_rows > 0) {
                                        $no = 1;
                                        while($row = $result->fetch_assoc()) {
                                    ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td><img src="../<?php echo $row['file_path']; ?>" alt="<?php echo $row['judul']; ?>" width="100"></td>
                                        <td><?php echo $row['judul']; ?></td>
                                        <td><?php echo $row['kategori']; ?></td>
                                        <td><?php echo date('d-m-Y H:i', strtotime($row['tanggal_upload'])); ?></td>
                                        <td>
                                            <a href="edit_galeri.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">
                                                <i class="bi bi-pencil"></i> Edit
                                            </a>
                                            <a href="?hapus=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus galeri ini?')">
                                                <i class="bi bi-trash"></i> Hapus
                                            </a>
                                        </td>
                                    </tr>
                                    <?php 
                                        }
                                    } else {
                                    ?>
                                    <tr>
                                        <td colspan="6" class="text-center">Belum ada data galeri</td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        // Preview gambar sebelum upload
        document.getElementById('gambar').addEventListener('change', function(e) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.createElement('img');
                preview.src = e.target.result;
                preview.style.maxWidth = '200px';
                preview.style.marginTop = '10px';
                
                const previewContainer = document.getElementById('gambar').parentNode;
                const oldPreview = previewContainer.querySelector('img');
                if (oldPreview) {
                    previewContainer.removeChild(oldPreview);
                }
                previewContainer.appendChild(preview);
            }
            reader.readAsDataURL(this.files[0]);
        });
    </script>
</body>
</html>



