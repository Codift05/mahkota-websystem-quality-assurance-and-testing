<?php
// api/artikel/read.php
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

$sql = 'SELECT * FROM artikel ORDER BY tanggal DESC, id DESC';
$result = $conn->query($sql);
$artikels = [];
while ($row = $result->fetch_assoc()) {
    $artikels[] = $row;
}
echo json_encode($artikels);
// Do not close $conn to allow multiple API calls in same process
