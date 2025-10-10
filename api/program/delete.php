<?php
// api/program/delete.php
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
    echo json_encode(['error' => 'ID program wajib diisi']);
    exit;
}

// Ambil info gambar untuk dihapus
$result = $conn->query("SELECT gambar FROM program WHERE id = $id");
if ($row = $result->fetch_assoc()) {
    if ($row['gambar']) {
        $file_path = dirname(dirname(__DIR__)) . '/' . $row['gambar'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }
}

$stmt = $conn->prepare('DELETE FROM program WHERE id=?');
$stmt->bind_param('i', $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Program berhasil dihapus']);
} else {
    echo json_encode(['error' => 'Gagal hapus program']);
}

$stmt->close();
$conn->close();
