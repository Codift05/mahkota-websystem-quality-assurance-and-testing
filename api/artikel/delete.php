<?php
// api/artikel/delete.php
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
if (!$id) {
    echo json_encode(['error' => 'ID wajib diisi']);
    return;
}

$stmt = $conn->prepare('DELETE FROM artikel WHERE id=?');
$stmt->bind_param('i', $id);
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Artikel berhasil dihapus']);
} else {
    echo json_encode(['error' => 'Gagal delete artikel']);
}
$stmt->close();
// Do not close $conn to allow multiple API calls in same process
