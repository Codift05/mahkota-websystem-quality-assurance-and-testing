<?php
// api/program/create.php
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

$nama_program = $_POST['nama_program'] ?? '';
$bidang = $_POST['bidang'] ?? '';
$deskripsi = $_POST['deskripsi'] ?? '';
$status = $_POST['status'] ?? 'planned';

if (!$nama_program || !$bidang) {
    echo json_encode(['error' => 'Nama program dan bidang wajib diisi']);
    exit;
}

$stmt = $conn->prepare('INSERT INTO program (nama_program, bidang, deskripsi, status) VALUES (?, ?, ?, ?)');
$stmt->bind_param('ssss', $nama_program, $bidang, $deskripsi, $status);
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'id' => $stmt->insert_id, 'message' => 'Program berhasil ditambahkan']);
} else {
    echo json_encode(['error' => 'Gagal menambah program']);
}
$stmt->close();
// Do not close $conn to allow multiple API calls in same process
