<?php
// api/artikel/update.php
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

$id = $_POST['id'] ?? '';
$judul = $_POST['judul'] ?? '';
$kategori = $_POST['kategori'] ?? '';
$isi = $_POST['isi'] ?? '';
$gambar = '';

if (!$id || !$judul || !$kategori || !$isi) {
    echo json_encode(['error' => 'ID, judul, kategori, dan isi wajib diisi']);
    return;
}

if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
    $uploads_dir = dirname(dirname(__DIR__)) . '/uploads';
    if (!is_dir($uploads_dir)) {
        mkdir($uploads_dir, 0777, true);
    }
    $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
    $filename = uniqid('artikel_', true) . '.' . $ext;
    $target = $uploads_dir . '/' . $filename;
    if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target)) {
        $gambar = 'uploads/' . $filename;
    }
}

if ($gambar) {
    $stmt = $conn->prepare('UPDATE artikel SET judul=?, kategori=?, isi=?, gambar=? WHERE id=?');
    $stmt->bind_param('ssssi', $judul, $kategori, $isi, $gambar, $id);
} else {
    $stmt = $conn->prepare('UPDATE artikel SET judul=?, kategori=?, isi=? WHERE id=?');
    $stmt->bind_param('sssi', $judul, $kategori, $isi, $id);
}

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Artikel berhasil diperbarui']);
} else {
    echo json_encode(['error' => 'Gagal update artikel']);
}
$stmt->close();
// Do not close $conn to allow multiple API calls in same process
