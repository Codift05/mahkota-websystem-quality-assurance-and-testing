<?php
// api/artikel/delete.php
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

$id = $_POST['id'] ?? '';
if (!$id) {
    echo json_encode(['error' => 'ID artikel wajib diisi']);
    exit;
}

$stmt = $conn->prepare('DELETE FROM artikel WHERE id=?');
$stmt->bind_param('i', $id);
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Artikel berhasil dihapus']);
} else {
    echo json_encode(['error' => 'Gagal hapus artikel']);
}
$stmt->close();
$conn->close();
