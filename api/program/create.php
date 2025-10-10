<?php
// api/program/create.php
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

$nama_program = $_POST['nama_program'] ?? '';
$bidang = $_POST['bidang'] ?? '';
$deskripsi = $_POST['deskripsi'] ?? '';
$tanggal_mulai = $_POST['tanggal_mulai'] ?? '';
$tanggal_selesai = $_POST['tanggal_selesai'] ?? '';
$status = $_POST['status'] ?? 'planned';
$gambar = '';

if (!$nama_program || !$bidang) {
    echo json_encode(['error' => 'Nama program dan bidang wajib diisi']);
    exit;
}

// Upload gambar (optional)
if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
    $uploads_dir = dirname(dirname(__DIR__)) . '/uploads/program';
    if (!is_dir($uploads_dir)) {
        mkdir($uploads_dir, 0777, true);
    }
    
    $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
    $filename = uniqid('program_', true) . '.' . $ext;
    $target = $uploads_dir . '/' . $filename;
    
    if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target)) {
        $gambar = 'uploads/program/' . $filename;
    }
}

$stmt = $conn->prepare('INSERT INTO program (nama_program, bidang, deskripsi, tanggal_mulai, tanggal_selesai, status, gambar, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())');
$stmt->bind_param('sssssss', $nama_program, $bidang, $deskripsi, $tanggal_mulai, $tanggal_selesai, $status, $gambar);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Program berhasil ditambahkan']);
} else {
    echo json_encode(['error' => 'Gagal menambah program: ' . $conn->error]);
}

$stmt->close();
$conn->close();
