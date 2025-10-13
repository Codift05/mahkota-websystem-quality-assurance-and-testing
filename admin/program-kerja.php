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

// Proses tambah program kerja
if (isset($_POST['tambah'])) {
    $nama_program = $conn->real_escape_string($_POST['nama_program']);
    $deskripsi = $conn->real_escape_string($_POST['deskripsi']);
    $divisi = $conn->real_escape_string($_POST['divisi']);
    $tahun = $conn->real_escape_string($_POST['tahun']);
    $icon = $conn->real_escape_string($_POST['icon']);
    $status = $conn->real_escape_string($_POST['status']);
    $urutan = intval($_POST['urutan']);
    $tanggal_dibuat = date('Y-m-d H:i:s');
    
    // Simpan ke database
    $sql = "INSERT INTO program_kerja (nama_program, deskripsi, divisi, tahun, icon, status, urutan, tanggal_dibuat) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssssssis', $nama_program, $deskripsi, $divisi, $tahun, $icon, $status, $urutan, $tanggal_dibuat);
    
    if ($stmt->execute()) {
        $message = "Program kerja berhasil ditambahkan";
        
        // Log aktivitas
        $admin_id = $_SESSION['admin_id'];
        $log_sql = "INSERT INTO log_aktivitas (admin_id, aktivitas, tanggal) 
                   VALUES (?, ?, ?)";
        $log_stmt = $conn->prepare($log_sql);
        $aktivitas = "Menambahkan program kerja: $nama_program";
        $log_stmt->bind_param('iss', $admin_id, $aktivitas, $tanggal_dibuat);
        $log_stmt->execute();
    } else {
        $error = "Error: " . $stmt->error;
    }
}

// Proses hapus program kerja
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    
    // Ambil info program sebelum dihapus
    $info_query = "SELECT nama_program FROM program_kerja WHERE id = ?";
    $info_stmt = $conn->prepare($info_query);
    $info_stmt->bind_param('i', $id);
    $info_stmt->execute();
    $info_result = $info_stmt->get_result();
    
    if ($info_result->num_rows > 0) {
        $info_data = $info_result->fetch_assoc();
        
        // Hapus dari database
        $sql = "DELETE FROM program_kerja WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $id);
        
        if ($stmt->execute()) {
            $message = "Program kerja berhasil dihapus";
            
            // Log aktivitas
            $admin_id = $_SESSION['admin_id'];
            $tanggal = date('Y-m-d H:i:s');
            $log_sql = "INSERT INTO log_aktivitas (admin_id, aktivitas, tanggal) 
                       VALUES (?, ?, ?)";
            $log_stmt = $conn->prepare($log_sql);
            $aktivitas = "Menghapus program kerja: {$info_data['nama_program']}";
            $log_stmt->bind_param('iss', $admin_id, $aktivitas, $tanggal);
            $log_stmt->execute();
        } else {
            $error = "Error: " . $stmt->error;
        }
    }
}

// Ambil data program kerja untuk ditampilkan
$sql = "SELECT * FROM program_kerja ORDER BY tahun DESC, urutan ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Program Kerja - Admin Dashboard</title>
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
                    <h1 class="h2">Manajemen Program Kerja</h1>
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
                
                <!-- Form Tambah Program Kerja -->
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="bi bi-plus-circle me-1"></i> Tambah Program Kerja Baru
                    </div>
                    <div class="card-body">
                        <form action="" method="post">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="nama_program" class="form-label">Nama Program</label>
                                    <input type="text" class="form-control" id="nama_program" name="nama_program" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="divisi" class="form-label">Divisi</label>
                                    <select class="form-select" id="divisi" name="divisi" required>
                                        <option value="">Pilih Divisi</option>
                                        <option value="Pendidikan">Pendidikan</option>
                                        <option value="Sosial">Sosial</option>
                                        <option value="Budaya">Budaya</option>
                                        <option value="Olahraga">Olahraga</option>
                                        <option value="Kewirausahaan">Kewirausahaan</option>
                                        <option value="Media & Informasi">Media & Informasi</option>
                                        <option value="Hubungan Masyarakat">Hubungan Masyarakat</option>
                                        <option value="Lainnya">Lainnya</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="deskripsi" class="form-label">Deskripsi</label>
                                <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" required></textarea>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="tahun" class="form-label">Tahun</label>
                                    <input type="number" class="form-control" id="tahun" name="tahun" value="<?php echo date('Y'); ?>" min="2020" max="2100" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="icon" class="form-label">Icon (Bootstrap Icons)</label>
                                    <input type="text" class="form-control" id="icon" name="icon" value="bi-calendar-check" required>
                                    <small class="text-muted">Contoh: bi-calendar-check, bi-book, bi-people</small>
                                </div>
                                <div class="col-md-4">
                                    <label for="urutan" class="form-label">Urutan</label>
                                    <input type="number" class="form-control" id="urutan" name="urutan" value="1" min="1" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="Aktif">Aktif</option>
                                    <option value="Berjalan">Berjalan</option>
                                    <option value="Selesai">Selesai</option>
                                </select>
                            </div>
                            <button type="submit" name="tambah" class="btn btn-primary">
                                <i class="bi bi-save"></i> Simpan
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- Tabel Program Kerja -->
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-list-ul me-1"></i> Daftar Program Kerja
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Icon</th>
                                        <th>Nama Program</th>
                                        <th>Divisi</th>
                                        <th>Tahun</th>
                                        <th>Status</th>
                                        <th>Urutan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    if ($result && $result->num_rows > 0) {
                                        $no = 1;
                                        while($row = $result->fetch_assoc()) {
                                            // Status badge
                                            $badge_class = 'bg-success';
                                            if ($row['status'] == 'Berjalan') {
                                                $badge_class = 'bg-warning';
                                            } elseif ($row['status'] == 'Selesai') {
                                                $badge_class = 'bg-info';
                                            }
                                    ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td><i class="bi <?php echo htmlspecialchars($row['icon']); ?>" style="font-size: 1.5rem;"></i></td>
                                        <td><?php echo htmlspecialchars($row['nama_program']); ?></td>
                                        <td><span class="badge bg-primary"><?php echo htmlspecialchars($row['divisi']); ?></span></td>
                                        <td><?php echo htmlspecialchars($row['tahun']); ?></td>
                                        <td><span class="badge <?php echo $badge_class; ?>"><?php echo htmlspecialchars($row['status']); ?></span></td>
                                        <td><?php echo $row['urutan']; ?></td>
                                        <td>
                                            <a href="edit_program.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">
                                                <i class="bi bi-pencil"></i> Edit
                                            </a>
                                            <a href="?hapus=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus program kerja ini?')">
                                                <i class="bi bi-trash"></i> Hapus
                                            </a>
                                        </td>
                                    </tr>
                                    <?php 
                                        }
                                    } else {
                                    ?>
                                    <tr>
                                        <td colspan="8" class="text-center">Belum ada data program kerja</td>
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
</body>
</html>
