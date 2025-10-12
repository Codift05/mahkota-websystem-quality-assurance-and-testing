<?php
// Proteksi halaman dengan session
session_start();
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header('Location: ../login-page.php');
    exit;
}

// Koneksi database
require_once '../db.php';

$message = '';
$error = '';

// Ambil ID dari URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    header('Location: galeri.php');
    exit;
}

// Ambil data galeri berdasarkan ID
$sql = "SELECT * FROM galeri WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header('Location: galeri.php');
    exit;
}

$galeri = $result->fetch_assoc();

// Proses update galeri
if (isset($_POST['update'])) {
    $judul = $conn->real_escape_string($_POST['judul']);
    $deskripsi = $conn->real_escape_string($_POST['deskripsi']);
    $kategori = $conn->real_escape_string($_POST['kategori']);
    
    // Cek apakah ada upload gambar baru
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
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
                // Hapus gambar lama
                $old_file = '../' . $galeri['file_path'];
                if (file_exists($old_file)) {
                    unlink($old_file);
                }
                
                $file_path = str_replace('../', '', $target_file);
                
                // Update dengan gambar baru
                $update_sql = "UPDATE galeri SET judul = ?, deskripsi = ?, kategori = ?, file_path = ? WHERE id = ?";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param('ssssi', $judul, $deskripsi, $kategori, $file_path, $id);
            } else {
                $error = "Maaf, terjadi kesalahan saat mengupload file.";
            }
        } else {
            $error = "Maaf, hanya file JPG, JPEG, PNG & GIF yang diperbolehkan.";
        }
    } else {
        // Update tanpa mengubah gambar
        $update_sql = "UPDATE galeri SET judul = ?, deskripsi = ?, kategori = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param('sssi', $judul, $deskripsi, $kategori, $id);
    }
    
    if (!$error) {
        if ($update_stmt->execute()) {
            $message = "Galeri berhasil diupdate";
            
            // Log aktivitas
            $admin_id = $_SESSION['admin_id'];
            $tanggal = date('Y-m-d H:i:s');
            $log_sql = "INSERT INTO log_aktivitas (admin_id, aktivitas, tanggal) 
                       VALUES (?, ?, ?)";
            $log_stmt = $conn->prepare($log_sql);
            $aktivitas = "Mengupdate galeri: $judul";
            $log_stmt->bind_param('iss', $admin_id, $aktivitas, $tanggal);
            $log_stmt->execute();
            
            // Refresh data galeri
            $stmt->execute();
            $result = $stmt->get_result();
            $galeri = $result->fetch_assoc();
        } else {
            $error = "Error: " . $update_stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Galeri - Admin Dashboard</title>
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
                    <h1 class="h2">Edit Galeri</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <a href="galeri.php" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                    </div>
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
                
                <!-- Form Edit Galeri -->
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-pencil-square me-1"></i> Form Edit Galeri
                    </div>
                    <div class="card-body">
                        <form action="" method="post" enctype="multipart/form-data">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="judul" class="form-label">Judul</label>
                                    <input type="text" class="form-control" id="judul" name="judul" value="<?php echo htmlspecialchars($galeri['judul']); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="kategori" class="form-label">Kategori</label>
                                    <select class="form-select" id="kategori" name="kategori" required>
                                        <option value="">Pilih Kategori</option>
                                        <option value="Kegiatan" <?php echo $galeri['kategori'] == 'Kegiatan' ? 'selected' : ''; ?>>Kegiatan</option>
                                        <option value="Dokumentasi" <?php echo $galeri['kategori'] == 'Dokumentasi' ? 'selected' : ''; ?>>Dokumentasi</option>
                                        <option value="Acara" <?php echo $galeri['kategori'] == 'Acara' ? 'selected' : ''; ?>>Acara</option>
                                        <option value="Lainnya" <?php echo $galeri['kategori'] == 'Lainnya' ? 'selected' : ''; ?>>Lainnya</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="deskripsi" class="form-label">Deskripsi</label>
                                <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" required><?php echo htmlspecialchars($galeri['deskripsi']); ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Gambar Saat Ini</label>
                                <div class="mb-2">
                                    <img src="../<?php echo htmlspecialchars($galeri['file_path']); ?>" alt="<?php echo htmlspecialchars($galeri['judul']); ?>" style="max-width: 300px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="gambar" class="form-label">Upload Gambar Baru (Opsional)</label>
                                <input class="form-control" type="file" id="gambar" name="gambar" accept="image/*">
                                <div class="form-text">Kosongkan jika tidak ingin mengubah gambar. Format yang diperbolehkan: JPG, JPEG, PNG, GIF. Ukuran maksimal: 2MB.</div>
                                <div id="preview-container"></div>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" name="update" class="btn btn-primary">
                                    <i class="bi bi-save"></i> Update Galeri
                                </button>
                                <a href="galeri.php" class="btn btn-secondary">
                                    <i class="bi bi-x-circle"></i> Batal
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        // Preview gambar sebelum upload
        document.getElementById('gambar').addEventListener('change', function(e) {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const previewContainer = document.getElementById('preview-container');
                    previewContainer.innerHTML = `
                        <div class="mt-2">
                            <label class="form-label">Preview Gambar Baru:</label>
                            <div>
                                <img src="${e.target.result}" style="max-width: 300px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                            </div>
                        </div>
                    `;
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>
