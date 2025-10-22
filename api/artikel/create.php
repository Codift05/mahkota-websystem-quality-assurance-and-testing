<?php
// api/artikel/create.php
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

$judul = $_POST['judul'] ?? '';
$kategori = $_POST['kategori'] ?? '';
$isi = $_POST['isi'] ?? '';
$gambar = '';

if (!$judul || !$kategori || !$isi) {
    echo json_encode(['error' => 'Judul, kategori, dan isi wajib diisi']);
    exit;
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

$stmt = $conn->prepare('INSERT INTO artikel (judul, kategori, isi, gambar) VALUES (?, ?, ?, ?)');
$stmt->bind_param('ssss', $judul, $kategori, $isi, $gambar);
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'id' => $stmt->insert_id, 'message' => 'Artikel berhasil dibuat']);
} else {
    echo json_encode(['error' => 'Gagal menambah artikel']);
}
$stmt->close();
$conn->close();
