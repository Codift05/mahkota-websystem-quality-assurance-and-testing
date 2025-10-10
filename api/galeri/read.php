<?php
// api/galeri/read.php
session_start();
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}
header('Content-Type: application/json');
require_once '../../db.php';

$kategori = $_GET['kategori'] ?? '';

if ($kategori) {
    $stmt = $conn->prepare('SELECT * FROM galeri WHERE kategori = ? ORDER BY tanggal DESC');
    $stmt->bind_param('s', $kategori);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query('SELECT * FROM galeri ORDER BY tanggal DESC');
}

$galeri = [];
while ($row = $result->fetch_assoc()) {
    $galeri[] = $row;
}

echo json_encode($galeri);
$conn->close();
