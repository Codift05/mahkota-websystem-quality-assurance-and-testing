<?php
/**
 * Update Tabel Program Kerja - Menambahkan tabel program_kerja
 * Jalankan file ini sekali untuk membuat tabel program_kerja
 * Akses: http://localhost/Devin/update_program_table.php
 */

require_once 'db.php';

$errors = [];
$success = [];

// Cek apakah tabel program_kerja ada
$check_table = $conn->query("SHOW TABLES LIKE 'program_kerja'");
if ($check_table->num_rows == 0) {
    // Tabel belum ada, buat tabel baru
    $sql_create = "CREATE TABLE `program_kerja` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `nama_program` varchar(255) NOT NULL,
      `deskripsi` text,
      `divisi` varchar(100) NOT NULL,
      `tahun` varchar(4) NOT NULL,
      `icon` varchar(50) DEFAULT 'bi-calendar-check',
      `status` varchar(50) DEFAULT 'Aktif',
      `urutan` int(11) DEFAULT 1,
      `tanggal_dibuat` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      KEY `divisi` (`divisi`),
      KEY `tahun` (`tahun`),
      KEY `status` (`status`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    if ($conn->query($sql_create)) {
        $success[] = "✅ Tabel 'program_kerja' berhasil dibuat dengan struktur lengkap";
        
        // Insert data contoh
        $sample_data = [
            [
                'nama' => 'Bimbingan Belajar Gratis',
                'desc' => 'Program bimbingan belajar gratis untuk mahasiswa baru yang membutuhkan bantuan dalam mata kuliah dasar.',
                'divisi' => 'Pendidikan',
                'tahun' => date('Y'),
                'icon' => 'bi-book',
                'status' => 'Berjalan'
            ],
            [
                'nama' => 'Bakti Sosial Ramadan',
                'desc' => 'Kegiatan berbagi takjil dan santunan kepada masyarakat kurang mampu di sekitar kampus.',
                'divisi' => 'Sosial',
                'tahun' => date('Y'),
                'icon' => 'bi-heart',
                'status' => 'Aktif'
            ],
            [
                'nama' => 'Festival Budaya Ternate',
                'desc' => 'Pentas seni dan budaya untuk memperkenalkan kebudayaan Ternate kepada mahasiswa di Manado.',
                'divisi' => 'Budaya',
                'tahun' => date('Y'),
                'icon' => 'bi-music-note-beamed',
                'status' => 'Aktif'
            ]
        ];
        
        $insert_sql = "INSERT INTO program_kerja (nama_program, deskripsi, divisi, tahun, icon, status, urutan) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_sql);
        
        $urutan = 1;
        foreach ($sample_data as $data) {
            $stmt->bind_param('ssssssi', $data['nama'], $data['desc'], $data['divisi'], $data['tahun'], $data['icon'], $data['status'], $urutan);
            $stmt->execute();
            $urutan++;
        }
        
        $success[] = "✅ Data contoh program kerja berhasil ditambahkan";
    } else {
        $errors[] = "❌ Error membuat tabel program_kerja: " . $conn->error;
    }
} else {
    $success[] = "ℹ️ Tabel 'program_kerja' sudah ada di database";
    
    // Cek kolom-kolom yang diperlukan
    $required_columns = [
        'id' => 'int(11)',
        'nama_program' => 'varchar(255)',
        'deskripsi' => 'text',
        'divisi' => 'varchar(100)',
        'tahun' => 'varchar(4)',
        'icon' => 'varchar(50)',
        'status' => 'varchar(50)',
        'urutan' => 'int(11)',
        'tanggal_dibuat' => 'timestamp'
    ];
    
    foreach ($required_columns as $column => $type) {
        $check = $conn->query("SHOW COLUMNS FROM program_kerja LIKE '$column'");
        if ($check->num_rows == 0) {
            $errors[] = "⚠️ Kolom '$column' tidak ditemukan. Struktur tabel mungkin tidak lengkap.";
        }
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Tabel Program Kerja - Mahkota</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .update-container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
            max-width: 700px;
            width: 100%;
        }
        .update-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .update-header h1 {
            color: #3a3b45;
            font-weight: 700;
            margin-bottom: 10px;
        }
        .update-header p {
            color: #858796;
        }
        .alert {
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
        }
        .btn-action {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 10px;
            font-weight: 700;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            transition: all 0.3s;
            margin-top: 10px;
        }
        .btn-program {
            background: #667eea;
            color: white;
        }
        .btn-program:hover {
            background: #5568d3;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .btn-admin {
            background: #28a745;
            color: white;
        }
        .btn-admin:hover {
            background: #218838;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
        }
    </style>
</head>
<body>
    <div class="update-container">
        <div class="update-header">
            <h1><i class="bi bi-calendar-check"></i> Update Tabel Program Kerja</h1>
            <p>Memperbarui struktur database untuk fitur program kerja</p>
        </div>

        <?php if (!empty($success)): ?>
            <?php foreach ($success as $msg): ?>
                <div class="alert alert-success">
                    <?php echo $msg; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <?php foreach ($errors as $msg): ?>
                <div class="alert alert-danger">
                    <?php echo $msg; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if (empty($errors)): ?>
            <div class="alert alert-info">
                <i class="bi bi-info-circle-fill"></i> 
                <strong>Update Selesai!</strong> Struktur database sudah siap digunakan.
            </div>
            
            <a href="program-kerja.php" class="btn btn-action btn-program">
                <i class="bi bi-calendar-check"></i> Lihat Halaman Program Kerja
            </a>
            
            <a href="admin/program-kerja.php" class="btn btn-action btn-admin">
                <i class="bi bi-gear-fill"></i> Kelola Program Kerja (Admin)
            </a>
        <?php else: ?>
            <div class="alert alert-warning mt-3">
                <i class="bi bi-exclamation-triangle-fill"></i> 
                <strong>Perhatian:</strong> Ada error yang perlu diperbaiki. Silakan cek pesan error di atas.
            </div>
            <a href="javascript:location.reload()" class="btn btn-action btn-program">
                <i class="bi bi-arrow-clockwise"></i> Coba Lagi
            </a>
        <?php endif; ?>

        <div class="text-center mt-3">
            <small class="text-muted">
                <i class="bi bi-shield-check"></i> 
                File ini aman untuk dijalankan berkali-kali
            </small>
        </div>
    </div>
</body>
</html>
