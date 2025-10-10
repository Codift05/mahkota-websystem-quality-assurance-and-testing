<?php
// api/artikel/read.php
session_start();
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}
header('Content-Type: application/json');
require_once '../../db.php';

$sql = 'SELECT * FROM artikel ORDER BY tanggal DESC';
$result = $conn->query($sql);
$artikels = [];
while ($row = $result->fetch_assoc()) {
    $artikels[] = $row;
}
echo json_encode($artikels);
$conn->close();
