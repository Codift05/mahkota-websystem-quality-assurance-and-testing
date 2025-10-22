<?php
// count_artikel.php - Menghitung jumlah artikel dalam database
header('Content-Type: application/json');
require_once dirname(__DIR__) . '/db.php';

// Menggunakan prepared statement untuk keamanan
$sql = "SELECT COUNT(*) as count FROM artikel";
$result = $conn->query($sql);

if ($result) {
    $row = $result->fetch_assoc();
    echo json_encode(['count' => $row['count']]);
} else {
    echo json_encode(['count' => 0, 'error' => 'Gagal menghitung artikel']);
}

$conn->close();
?>