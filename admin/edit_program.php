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
    header('Location: program-kerja.php');
    exit;
}

// Ambil data program kerja berdasarkan ID
$sql = "SELECT * FROM program_kerja WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header('Location: program-kerja.php');
    exit;
}

$program = $result->fetch_assoc();

// Proses update program kerja
if (isset($_POST['update'])) {
    $nama_program = $conn->real_escape_string($_POST['nama_program']);
    $deskripsi = $conn->real_escape_string($_POST['deskripsi']);
    $divisi = $conn->real_escape_string($_POST['divisi']);
    $tahun = $conn->real_escape_string($_POST['tahun']);
    $icon = $conn->real_escape_string($_POST['icon']);
    $status = $conn->real_escape_string($_POST['status']);
    $urutan = intval($_POST['urutan']);
    
    // Update database
    $update_sql = "UPDATE program_kerja SET nama_program = ?, deskripsi = ?, divisi = ?, tahun = ?, icon = ?, status = ?, urutan = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param('ssssssii', $nama_program, $deskripsi, $divisi, $tahun, $icon, $status, $urutan, $id);
    
    if ($update_stmt->execute()) {
        $message = "Program kerja berhasil diupdate";
        
        // Log aktivitas
        $admin_id = $_SESSION['admin_id'];
        $tanggal = date('Y-m-d H:i:s');
        $log_sql = "INSERT INTO log_aktivitas (admin_id, aktivitas, tanggal) 
                   VALUES (?, ?, ?)";
        $log_stmt = $conn->prepare($log_sql);
        $aktivitas = "Mengupdate program kerja: $nama_program";
        $log_stmt->bind_param('iss', $admin_id, $aktivitas, $tanggal);
        $log_stmt->execute();
        
        // Refresh data program
        $stmt->execute();
        $result = $stmt->get_result();
        $program = $result->fetch_assoc();
    } else {
        $error = "Error: " . $update_stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Program Kerja - Admin Dashboard</title>
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
                    <h1 class="h2">Edit Program Kerja</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <a href="program-kerja.php" class="btn btn-sm btn-outline-secondary">
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
                
                <!-- Form Edit Program Kerja -->
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-pencil-square me-1"></i> Form Edit Program Kerja
                    </div>
                    <div class="card-body">
                        <form action="" method="post">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="nama_program" class="form-label">Nama Program</label>
                                    <input type="text" class="form-control" id="nama_program" name="nama_program" value="<?php echo htmlspecialchars($program['nama_program']); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="divisi" class="form-label">Divisi</label>
                                    <select class="form-select" id="divisi" name="divisi" required>
                                        <option value="">Pilih Divisi</option>
                                        <option value="Pendidikan" <?php echo $program['divisi'] == 'Pendidikan' ? 'selected' : ''; ?>>Pendidikan</option>
                                        <option value="Sosial" <?php echo $program['divisi'] == 'Sosial' ? 'selected' : ''; ?>>Sosial</option>
                                        <option value="Budaya" <?php echo $program['divisi'] == 'Budaya' ? 'selected' : ''; ?>>Budaya</option>
                                        <option value="Olahraga" <?php echo $program['divisi'] == 'Olahraga' ? 'selected' : ''; ?>>Olahraga</option>
                                        <option value="Kewirausahaan" <?php echo $program['divisi'] == 'Kewirausahaan' ? 'selected' : ''; ?>>Kewirausahaan</option>
                                        <option value="Media & Informasi" <?php echo $program['divisi'] == 'Media & Informasi' ? 'selected' : ''; ?>>Media & Informasi</option>
                                        <option value="Hubungan Masyarakat" <?php echo $program['divisi'] == 'Hubungan Masyarakat' ? 'selected' : ''; ?>>Hubungan Masyarakat</option>
                                        <option value="Lainnya" <?php echo $program['divisi'] == 'Lainnya' ? 'selected' : ''; ?>>Lainnya</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="deskripsi" class="form-label">Deskripsi</label>
                                <textarea class="form-control" id="deskripsi" name="deskripsi" rows="4" required><?php echo htmlspecialchars($program['deskripsi']); ?></textarea>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="tahun" class="form-label">Tahun</label>
                                    <input type="number" class="form-control" id="tahun" name="tahun" value="<?php echo htmlspecialchars($program['tahun']); ?>" min="2020" max="2100" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="icon" class="form-label">Icon (Bootstrap Icons)</label>
                                    <input type="text" class="form-control" id="icon" name="icon" value="<?php echo htmlspecialchars($program['icon']); ?>" required>
                                    <small class="text-muted">Contoh: bi-calendar-check, bi-book, bi-people</small>
                                </div>
                                <div class="col-md-4">
                                    <label for="urutan" class="form-label">Urutan</label>
                                    <input type="number" class="form-control" id="urutan" name="urutan" value="<?php echo $program['urutan']; ?>" min="1" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="Aktif" <?php echo $program['status'] == 'Aktif' ? 'selected' : ''; ?>>Aktif</option>
                                    <option value="Berjalan" <?php echo $program['status'] == 'Berjalan' ? 'selected' : ''; ?>>Berjalan</option>
                                    <option value="Selesai" <?php echo $program['status'] == 'Selesai' ? 'selected' : ''; ?>>Selesai</option>
                                </select>
                            </div>
                            
                            <!-- Preview Icon -->
                            <div class="mb-3">
                                <label class="form-label">Preview Icon</label>
                                <div class="p-3 border rounded" style="background: linear-gradient(135deg, #667eea 0%, #5a67d8 100%);">
                                    <i class="bi <?php echo htmlspecialchars($program['icon']); ?>" style="font-size: 3rem; color: white;"></i>
                                </div>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" name="update" class="btn btn-primary">
                                    <i class="bi bi-save"></i> Update Program
                                </button>
                                <a href="program-kerja.php" class="btn btn-secondary">
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
        // Live preview icon
        document.getElementById('icon').addEventListener('input', function(e) {
            const iconPreview = document.querySelector('.p-3.border i');
            iconPreview.className = 'bi ' + e.target.value;
        });
    </script>
</body>
</html>
