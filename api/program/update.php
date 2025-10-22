<?php
// api/program/update.php
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
$nama_program = $_POST['nama_program'] ?? '';
$bidang = $_POST['bidang'] ?? '';
$deskripsi = $_POST['deskripsi'] ?? '';
$status = $_POST['status'] ?? 'planned';

if (!$id || !$nama_program || !$bidang) {
    echo json_encode(['error' => 'ID, nama program, dan bidang wajib diisi']);
    exit;
}

$stmt = $conn->prepare('UPDATE program SET nama_program=?, bidang=?, deskripsi=?, status=? WHERE id=?');
$stmt->bind_param('ssssi', $nama_program, $bidang, $deskripsi, $status, $id);
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Program berhasil diperbarui']);
} else {
    echo json_encode(['error' => 'Gagal update program']);
}
$stmt->close();
// Do not close $conn to allow multiple API calls in same process
