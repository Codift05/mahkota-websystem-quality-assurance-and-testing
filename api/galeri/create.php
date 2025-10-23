<?php
// api/galeri/create.php
if (session_status() === PHP_SESSION_NONE && PHP_SAPI !== 'cli' && !headers_sent()) { session_start(); }
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    return;
}
if (PHP_SAPI !== 'cli' && !headers_sent()) { header('Content-Type: application/json'); }
require_once dirname(__DIR__, 2) . '/db.php';
// Import global connection when included inside function scope
global $conn;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Invalid request method']);
    return;
}

$judul = $_POST['judul'] ?? '';
$kategori = $_POST['kategori'] ?? '';
$gambar = '';
$tanggal = date('Y-m-d H:i:s');

if (!$judul || !$kategori) {
    echo json_encode(['error' => 'Judul dan kategori wajib diisi']);
    return;
}

if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
    $uploads_dir = dirname(dirname(__DIR__)) . '/uploads/galeri';
    if (!is_dir($uploads_dir)) { mkdir($uploads_dir, 0777, true); }
    $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
    $filename = uniqid('galeri_', true) . '.' . $ext;
    $target = $uploads_dir . '/' . $filename;
    if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target)) {
        $gambar = 'uploads/galeri/' . $filename;
    }
}

// Require image upload
if ($gambar === '') {
    echo json_encode(['error' => 'Gambar wajib diupload']);
    return;
}

$stmt = $conn->prepare('INSERT INTO galeri (judul, kategori, gambar, tanggal) VALUES (?, ?, ?, ?)');
$stmt->bind_param('ssss', $judul, $kategori, $gambar, $tanggal);
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'id' => $stmt->insert_id, 'message' => 'Galeri berhasil ditambahkan']);
} else {
    echo json_encode(['error' => 'Gagal menambah item galeri']);
}
$stmt->close();
// Do not close $conn to allow multiple API calls in same process
