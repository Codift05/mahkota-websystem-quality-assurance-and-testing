<?php
// api/galeri/update.php
if (session_status() === PHP_SESSION_NONE && PHP_SAPI !== 'cli' && !headers_sent()) { session_start(); }
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}
if (PHP_SAPI !== 'cli' && !headers_sent()) { header('Content-Type: application/json'); }
require_once dirname(__DIR__, 2) . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Invalid request method']);
    exit;
}

$id = $_POST['id'] ?? '';
$judul = $_POST['judul'] ?? '';
$kategori = $_POST['kategori'] ?? '';
$gambar = '';

if (!$id || !$judul || !$kategori) {
    echo json_encode(['error' => 'ID, judul, dan kategori wajib diisi']);
    exit;
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

if ($gambar) {
    $stmt = $conn->prepare('UPDATE galeri SET judul=?, kategori=?, gambar=? WHERE id=?');
    $stmt->bind_param('sssi', $judul, $kategori, $gambar, $id);
} else {
    $stmt = $conn->prepare('UPDATE galeri SET judul=?, kategori=? WHERE id=?');
    $stmt->bind_param('ssi', $judul, $kategori, $id);
}

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Galeri berhasil diperbarui']);
} else {
    echo json_encode(['error' => 'Gagal update item galeri']);
}
$stmt->close();
$conn->close();
