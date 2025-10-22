<?php
// api/galeri/read.php
if (session_status() === PHP_SESSION_NONE && PHP_SAPI !== 'cli' && !headers_sent()) { session_start(); }
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}
if (PHP_SAPI !== 'cli' && !headers_sent()) { header('Content-Type: application/json'); }
require_once dirname(__DIR__, 2) . '/db.php';

$sql = 'SELECT * FROM galeri ORDER BY tanggal DESC';
$result = $conn->query($sql);
$items = [];
while ($row = $result->fetch_assoc()) {
    $items[] = $row;
}
echo json_encode($items);
$conn->close();
