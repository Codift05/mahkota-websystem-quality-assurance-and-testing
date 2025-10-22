<?php
// recent_activities.php - Menampilkan aktivitas terbaru dari database
header('Content-Type: application/json');
require_once dirname(__DIR__) . '/db.php';

// Mengambil aktivitas terbaru dari berbagai tabel
$activities = [];

// Artikel terbaru
$sql_artikel = "SELECT 'artikel' as type, judul as title, tanggal_publikasi as date, 
                CONCAT('Artikel baru: ', judul) as description 
                FROM artikel ORDER BY tanggal_publikasi DESC LIMIT 5";
$result_artikel = $conn->query($sql_artikel);

if ($result_artikel && $result_artikel->num_rows > 0) {
    while ($row = $result_artikel->fetch_assoc()) {
        $activities[] = $row;
    }
}

// Galeri terbaru
$sql_galeri = "SELECT 'galeri' as type, judul as title, tanggal_upload as date, 
               CONCAT('Galeri baru: ', judul) as description 
               FROM galeri ORDER BY tanggal_upload DESC LIMIT 5";
$result_galeri = $conn->query($sql_galeri);

if ($result_galeri && $result_galeri->num_rows > 0) {
    while ($row = $result_galeri->fetch_assoc()) {
        $activities[] = $row;
    }
}

// Media desain terbaru
$sql_media = "SELECT 'media' as type, judul as title, tanggal_upload as date, 
              CONCAT('Media desain baru: ', judul) as description 
              FROM media_desain ORDER BY tanggal_upload DESC LIMIT 5";
$result_media = $conn->query($sql_media);

if ($result_media && $result_media->num_rows > 0) {
    while ($row = $result_media->fetch_assoc()) {
        $activities[] = $row;
    }
}

// Gabungkan dan kirim sebagai JSON
echo json_encode($activities);
$conn->close();
?>