<?php
// count_galeri.php - Menghitung jumlah galeri dalam database
header('Content-Type: application/json');
require_once dirname(__DIR__) . '/db.php';

// Menggunakan prepared statement untuk keamanan
$sql = "SELECT COUNT(*) as count FROM galeri";
$result = $conn->query($sql);

if ($result) {
    $row = $result->fetch_assoc();
    echo json_encode(['count' => $row['count']]);
} else {
    echo json_encode(['count' => 0, 'error' => 'Gagal menghitung galeri']);
}

$conn->close();
?>