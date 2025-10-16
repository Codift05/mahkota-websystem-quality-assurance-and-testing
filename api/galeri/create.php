<?php
// api/galeri/create.php
session_start();
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}
header('Content-Type: application/json');
require_once '../../db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Invalid request method']);
    exit;
}

$judul = $_POST['judul'] ?? '';
$deskripsi = $_POST['deskripsi'] ?? '';
$kategori = $_POST['kategori'] ?? '';
$gambar = '';

if (!$judul) {
    echo json_encode(['error' => 'Judul wajib diisi']);
    exit;
}

// Upload gambar
if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
    $uploads_dir = dirname(dirname(__DIR__)) . '/uploads/galeri';
    if (!is_dir($uploads_dir)) {
        mkdir($uploads_dir, 0777, true);
    }
    
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $file_type = $_FILES['gambar']['type'];
    
    if (!in_array($file_type, $allowed_types)) {
        echo json_encode(['error' => 'Format file tidak didukung. Gunakan JPG, PNG, GIF, atau WEBP']);
        exit;
    }
    
    $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
    $filename = uniqid('galeri_', true) . '.' . $ext;
    $target = $uploads_dir . '/' . $filename;
    
    $moved = move_uploaded_file($_FILES['gambar']['tmp_name'], $target);
    if (!$moved && is_file($_FILES['gambar']['tmp_name'])) {
        $moved = copy($_FILES['gambar']['tmp_name'], $target);
    }
    if ($moved) {
        $gambar = 'uploads/galeri/' . $filename;
    } else {
        echo json_encode(['error' => 'Gagal upload gambar']);
        exit;
    }
} else {
    echo json_encode(['error' => 'Gambar wajib diupload']);
    exit;
}

// Tentukan nama kolom sesuai struktur tabel yang tersedia (fallback)
$colGambar = 'gambar';
$colTanggal = 'tanggal';

$checkGambar = $conn->query("SHOW COLUMNS FROM galeri LIKE 'gambar'");
if (!$checkGambar || $checkGambar->num_rows === 0) {
    $colGambar = 'file_path';
}

$checkTanggal = $conn->query("SHOW COLUMNS FROM galeri LIKE 'tanggal'");
if (!$checkTanggal || $checkTanggal->num_rows === 0) {
    $colTanggal = 'tanggal_upload';
}

$sql = "INSERT INTO galeri (judul, deskripsi, kategori, $colGambar, $colTanggal) VALUES (?, ?, ?, ?, NOW())";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ssss', $judul, $deskripsi, $kategori, $gambar);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Galeri berhasil ditambahkan']);
} else {
    echo json_encode(['error' => 'Gagal menambah galeri: ' . $conn->error]);
}

$stmt->close();
$conn->close();
