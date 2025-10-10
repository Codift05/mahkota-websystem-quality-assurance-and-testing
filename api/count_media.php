<?php
// count_media.php - Menghitung jumlah media desain dalam database
header('Content-Type: application/json');
require_once '../db.php';

// Menggunakan prepared statement untuk keamanan
$sql = "SELECT COUNT(*) as count FROM media_desain";
$result = $conn->query($sql);

if ($result) {
    $row = $result->fetch_assoc();
    echo json_encode(['count' => $row['count']]);
} else {
    echo json_encode(['count' => 0, 'error' => 'Gagal menghitung media desain']);
}

$conn->close();
?>