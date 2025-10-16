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

// Pastikan tabel galeri ada
$tableCheck = $conn->query("SHOW TABLES LIKE 'galeri'");
if (!$tableCheck || $tableCheck->num_rows === 0) {
    echo json_encode([]);
    $conn->close();
    exit;
}

// Tentukan kolom tanggal yang tersedia (fallback untuk struktur berbeda)
$orderColumn = 'tanggal';
$colCheck = $conn->query("SHOW COLUMNS FROM galeri LIKE 'tanggal'");
if (!$colCheck || $colCheck->num_rows === 0) {
    // Coba gunakan tanggal_upload jika kolom tanggal tidak ada
    $orderColumn = 'tanggal_upload';
}

$kategori = $_GET['kategori'] ?? '';

if ($kategori) {
    $stmt = $conn->prepare("SELECT * FROM galeri WHERE kategori = ? ORDER BY $orderColumn DESC");
    $stmt->bind_param('s', $kategori);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("SELECT * FROM galeri ORDER BY $orderColumn DESC");
}

$galeri = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $galeri[] = $row;
    }
}

echo json_encode($galeri);
$conn->close();
